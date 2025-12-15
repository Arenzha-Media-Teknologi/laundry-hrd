<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\Employee;
use App\Models\Office;
use App\Models\OutletOpening;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutletOpeningApiController extends Controller
{
    public function getAll()
    {
        try {
            $timelinessStatus = request()->query('timeliness_status');
            $officeId = request()->query('office_id');

            $outletOpeningQuery = OutletOpening::with(['employee' => function ($q) {
                $q->with(['office']);
            }, 'office']);

            if (!empty($timelinessStatus)) {
                $outletOpeningQuery->where('timeliness_status', $timelinessStatus);
            }

            if (!empty($officeId)) {
                $outletOpeningQuery->where('office_id', $officeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $outletOpeningQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $outletOpenings = $outletOpeningQuery->simplePaginate(10)->withQueryString();

            return response()->json([
                'message' => 'OK',
                'data' => $outletOpenings,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $createdBy = $request->created_by;
            // if (!isset($createdBy)) {
            //     throw new Error('Kamu tidak memiliki akses untuk melakukan pembukaan outlet');
            // }

            // $canOpenOutlet = Credential::where('employee_id', $createdBy)->first()->can_open_outlet ?? null;

            // if ($canOpenOutlet != 1) {
            //     throw new Error('Kamu tidak memiliki akses untuk melakukan pembukaan outlet');
            // }

            $currentDateOutletOpeningCount = OutletOpening::withTrashed()->where('date', $request->date)->count();
            $number = 'OP/' . date('dmy') . '/' . sprintf('%03d', ($currentDateOutletOpeningCount + 1));

            $office = Office::find($request->office_id);

            $currentDateOutletOpening = OutletOpening::with(['creator'])->where('office_id', $request->office_id)->where('date', date('Y-m-d'))->first();
            if (isset($currentDateOutletOpening)) {
                throw new Error('Outlet ' . ($office->name ?? "NAMA_OUTLET") . ' sudah melakukan pembukaan di hari ini oleh ' . ($currentDateOutletOpening->creator->name ?? "PENANGGUNG_JAWAB"));
            }

            // Calculate timeliness_status based on office opening_time
            $timelinessStatus = 'on_time'; // default
            $lateAmount = 0;
            if ($office && $office->opening_time) {
                // Ensure time format is correct - convert from H:i to H:i:s if needed
                $requestTime = $request->time;
                if (strlen($requestTime) === 5) { // Format is H:i
                    $requestTime .= ':00'; // Add seconds
                }

                $openingTime = Carbon::createFromFormat('H:i:s', $office->opening_time)->addMinutes(11);
                $actualTime = Carbon::createFromFormat('H:i:s', $requestTime);

                if ($actualTime->gt($openingTime)) {
                    $timelinessStatus = 'late';
                    $lateAmount = $actualTime->diffInMinutes($openingTime);
                }
            }

            $newOutletOpening = new OutletOpening();
            $newOutletOpening->number = $number;
            $newOutletOpening->date = date('Y-m-d');
            $newOutletOpening->time = $request->time;
            $newOutletOpening->timeliness_status = $timelinessStatus;
            $newOutletOpening->late_amount = $lateAmount;
            $newOutletOpening->approval_status = "pending";
            $newOutletOpening->office_id = $request->office_id;
            $newOutletOpening->created_by = $request->created_by;

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('outlet_opening_attachment')) {
                $file = $request->file('outlet_opening_attachment');
                $name = time() . '-outlet-opening-' . implode('-', explode(' ', $office->name ?? "OFFICE_NAME"));
                $filePath = 'outlet-opening/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $newOutletOpening->image = $urlPath;
            $newOutletOpening->save();

            DB::commit();
            return response()->json([
                'message' => 'Laporan buka station berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getMonthly(Request $request)
    {
        try {
            $month = $request->query('month') ?? date('Y-m');
            $officeId = $request->query('office_id');

            // $startDate = date($month . '-01');
            // $endDate = date("Y-m-t", strtotime($startDate));
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $outletOpenings = OutletOpening::with(['office', 'creator'])
                ->where('office_id', $officeId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('id', 'desc')
                ->get();

            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            $statistics = [
                'on_time' => 0,
                'late' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'no_data' => 0,
            ];

            $datesRange = $this->getDatesFromRange($startDate, $endDate);
            $finalOutletOpenings = collect($datesRange)->map(function ($date, $key) use ($outletOpenings, $days, &$statistics) {
                $carbonDate = Carbon::parse($date);
                $dayIndex = $carbonDate->dayOfWeekIso;
                $outletOpening = collect($outletOpenings)->where('date', $date)->first();

                $item = [
                    'date' => $date,
                    'iso_date' => $carbonDate->isoFormat('ll'),
                    'day' => $days[$dayIndex - 1],
                    'outlet_opening' => null,
                    'outlet_opening_id' => null,
                    'time' => null,
                    'timeliness_status' => null,
                    'approval_status' => null,
                    'late_amount' => 0,
                    'image' => null,
                    'creator_name' => null,
                ];

                if ($outletOpening !== null) {
                    $item['outlet_opening'] = $outletOpening->timeliness_status ?? null;
                    $item['outlet_opening_id'] = $outletOpening->id ?? null;
                    $item['time'] = $outletOpening->time ?? null;
                    $item['timeliness_status'] = $outletOpening->timeliness_status ?? null;
                    $item['approval_status'] = $outletOpening->approval_status ?? null;
                    $item['late_amount'] = $outletOpening->late_amount ?? 0;
                    $item['image'] = $outletOpening->image ?? null;
                    $item['creator_name'] = $outletOpening->creator->name ?? null;

                    // Update statistics
                    if ($item['timeliness_status'] == 'on_time') {
                        $statistics['on_time'] += 1;
                    } else if ($item['timeliness_status'] == 'late') {
                        $statistics['late'] += 1;
                    }

                    if ($item['approval_status'] == 'pending') {
                        $statistics['pending'] += 1;
                    } else if ($item['approval_status'] == 'approved') {
                        $statistics['approved'] += 1;
                    } else if ($item['approval_status'] == 'rejected') {
                        $statistics['rejected'] += 1;
                    }
                } else {
                    $statistics['no_data'] += 1;
                }

                return $item;
            })->all();

            return response()->json([
                'data' => $finalOutletOpenings,
                'statistics' => $statistics,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    public function getOne($id)
    {
        try {
            $outletOpening = OutletOpening::find($id);

            return response()->json([
                'message' => 'OK',
                'data' => $outletOpening,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $outletOpening = OutletOpening::find($id);

            if (!$outletOpening) {
                return response()->json([
                    'message' => 'Data outlet opening tidak ditemukan',
                ], 404);
            }

            $office = Office::find($request->office_id ?? $outletOpening->office_id);

            // Calculate timeliness_status based on office opening_time if time is being updated
            if ($request->has('time')) {
                $outletOpening->time = $request->time;

                // Recalculate timeliness_status based on office opening_time
                $timelinessStatus = 'on_time'; // default
                $lateAmount = 0;
                if ($office && $office->opening_time) {
                    // Ensure time format is correct - convert from H:i to H:i:s if needed
                    $requestTime = $request->time;
                    if (strlen($requestTime) === 5) { // Format is H:i
                        $requestTime .= ':00'; // Add seconds
                    }

                    $openingTime = Carbon::createFromFormat('H:i:s', $office->opening_time)->addMinutes(11);
                    $actualTime = Carbon::createFromFormat('H:i:s', $requestTime);

                    if ($actualTime->gt($openingTime)) {
                        $timelinessStatus = 'late';
                        $lateAmount = $actualTime->diffInMinutes($openingTime);
                    }
                }

                $outletOpening->timeliness_status = $timelinessStatus;
                $outletOpening->late_amount = $lateAmount;
            } else if ($request->has('office_id') && $request->office_id != $outletOpening->office_id) {
                // If office_id changes, recalculate timeliness_status with new office
                $newOffice = Office::find($request->office_id);
                $timelinessStatus = 'on_time'; // default
                $lateAmount = 0;
                if ($newOffice && $newOffice->opening_time && $outletOpening->time) {
                    // Ensure time format is correct - convert from H:i to H:i:s if needed
                    $outletTime = $outletOpening->time;
                    if (strlen($outletTime) === 5) { // Format is H:i
                        $outletTime .= ':00'; // Add seconds
                    }

                    $openingTime = Carbon::createFromFormat('H:i:s', $newOffice->opening_time)->addMinutes(11);
                    $actualTime = Carbon::createFromFormat('H:i:s', $outletTime);

                    if ($actualTime->gt($openingTime)) {
                        $timelinessStatus = 'late';
                        $lateAmount = $actualTime->diffInMinutes($openingTime);
                    }
                }

                $outletOpening->timeliness_status = $timelinessStatus;
                $outletOpening->late_amount = $lateAmount;
            }

            if ($request->has('approval_status')) {
                $outletOpening->approval_status = $request->approval_status;
            }

            if ($request->has('office_id')) {
                $outletOpening->office_id = $request->office_id;
            }


            if ($request->has('updated_by')) {
                $outletOpening->updated_by = $request->updated_by;
            }

            // Handle image update
            if ($request->hasFile('outlet_opening_attachment')) {
                // Delete old image if exists
                if ($outletOpening->image) {
                    $oldPath = str_replace(Storage::disk('s3')->url(''), '', $outletOpening->image);
                    if (Storage::disk('s3')->exists($oldPath)) {
                        Storage::disk('s3')->delete($oldPath);
                    }
                }

                // Upload new image
                $file = $request->file('outlet_opening_attachment');
                $name = time() . '-outlet-opening-' . implode('-', explode(' ', $office->name ?? "OFFICE_NAME"));
                $filePath = 'outlet-opening/attachments/' . $name . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);

                $outletOpening->image = $urlPath;
            }

            $outletOpening->save();

            DB::commit();
            return response()->json([
                'message' => 'Laporan buka outlet berhasil diupdate'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
