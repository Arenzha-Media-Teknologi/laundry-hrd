<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Office;
use App\Models\OutletOpening;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class OutletOpeningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offices = Office::all();
        return view('outlet-openings.index', [
            'offices' => $offices,
        ]);
    }

    public function indexData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $officeId = $request->query('office_id');
        $timelinessStatus = $request->query('timeliness_status');
        $approvalStatus = $request->query('approval_status');

        $outletOpeningQuery = OutletOpening::with(['creator' => function ($q) {
            $q->with(['activeCareer.jobTitle', 'office.division.company']);
        }, 'office']);

        if (isset($startDate) && !empty($startDate) && isset($endDate) && !empty($endDate)) {
            $outletOpeningQuery = $outletOpeningQuery->whereBetween('date', [$startDate, $endDate]);
        } elseif (isset($startDate) && !empty($startDate)) {
            $outletOpeningQuery = $outletOpeningQuery->where('date', '>=', $startDate);
        } elseif (isset($endDate) && !empty($endDate)) {
            $outletOpeningQuery = $outletOpeningQuery->where('date', '<=', $endDate);
        }

        if (isset($officeId) && !empty($officeId)) {
            $outletOpeningQuery = $outletOpeningQuery->where('office_id', $officeId);
        }

        if (isset($approvalStatus) && !empty($approvalStatus)) {
            $outletOpeningQuery = $outletOpeningQuery->where('approval_status', $approvalStatus);
        }

        if (isset($timelinessStatus) && !empty($timelinessStatus)) {
            $outletOpeningQuery = $outletOpeningQuery->where('timeliness_status', $timelinessStatus);
        }

        $outletOpenings = $outletOpeningQuery->orderBy('date', 'DESC')->select('outlet_openings.*');

        return DataTables::eloquent($outletOpenings)
            ->addColumn('formatted_date', function (OutletOpening $outletOpening) {
                return Carbon::parse($outletOpening->date)->format('d/m/Y');
            })
            ->addColumn('formatted_attachment', function (OutletOpening $outletOpening) {
                $paths = explode('/', $outletOpening->image);
                $fileName = $paths[count($paths) - 1] ?? "";
                if (!empty($outletOpening->image)) {
                    return '<a href="' . $outletOpening->image . '" target="_blank"><span class="badge badge-light-info"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="16" height="16" class="bi bi-file-earmark-post-fill align-middle" viewBox="0 0 16 16">
                      <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-5-.5H7a.5.5 0 0 1 0 1H4.5a.5.5 0 0 1 0-1m0 3h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5"/><span class="ms-2 align-middle">' . $fileName . '</span></span></a>';
                }

                return '';
            })
            ->addColumn('image_preview', function (OutletOpening $outletOpening) {
                if (!empty($outletOpening->image)) {
                    $officeName = $outletOpening->office->name ?? 'Outlet';
                    $date = Carbon::parse($outletOpening->date)->format('d/m/Y');
                    $title = $officeName . ' - ' . $date;

                    return '<a href="' . $outletOpening->image . '" data-lightbox="outlet-openings" data-title="' . htmlspecialchars($title) . '">
                        <img src="' . $outletOpening->image . '" alt="' . htmlspecialchars($title) . '" class="outlet-opening-thumbnail" />
                    </a>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('office_name', function (OutletOpening $outletOpening) {
                return $outletOpening->office->name ?? "OFFICE_NAME";
            })
            ->addColumn('person_in_charge', function (OutletOpening $outletOpening) {
                if (isset($outletOpening->creator)) {
                    return $this->employeeColumn($outletOpening->creator);
                }
                return 'NAMA_PEGAWAI';
            })
            ->addColumn('timeliness_status', function (OutletOpening $outletOpening) {
                $color = "secondary";
                $text = "N/A";
                switch ($outletOpening->timeliness_status) {
                    case 'on_time':
                        $color = "success";
                        $text = "TEPAT WAKTU";
                        break;
                    case 'late':
                        $color = "danger";
                        $text = "TELAT";
                        break;
                }

                return ' <span class="badge badge-' . $color . '  text-uppercase">
                        ' . $text . '
                    </span>';
            })
            ->addColumn('approval_status', function (OutletOpening $outletOpening) {
                $color = "secondary";
                $text = "N/A";
                switch ($outletOpening->approval_status) {
                    case 'pending':
                        $color = "warning";
                        $text = "PENDING";
                        break;
                    case 'approved':
                        $color = "success";
                        $text = "DISETUJUI";
                        break;
                    case 'rejected':
                        $color = "danger";
                        $text = "DITOLAK";
                        break;
                }

                return ' <span class="badge badge-' . $color . '  text-uppercase">
                        ' . $text . '
                    </span>';
            })
            ->addColumn('action', function (OutletOpening $outletOpening) {
                $action = '<div class="d-flex justify-content-end">';
                $action = '<div class="btn-group mt-2" role="group" aria-label="Basic example">';
                if (true) {
                    if ($outletOpening->approval_status == 'pending') {
                        $action .= '<a href="/outlet-openings/' . $outletOpening->id . '/edit" class="btn btn-sm btn-icon btn-secondary">
                          <span class="svg-icon svg-icon-5 m-0">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                          <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                          </svg>
                          </span></a>';
                    }

                    // Tombol delete tetap ditampilkan untuk semua status (pending, approved, rejected)
                    $action .= '<button type="button" class="btn btn-sm btn-icon btn-secondary btn-delete"  data-id="' . $outletOpening->id . '" data-status="' . $outletOpening->approval_status . '">
                      <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                      <span class="svg-icon svg-icon-5 m-0">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                              <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                              <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                          </svg>
                      </span>
                      <!--end::Svg Icon-->
                  </button>';
                }
                if (true) {
                }
                $action .= '</div>';

                $action .= '<div class="btn-group ms-2
                 mt-2" role="group" aria-label="Basic example">';
                if (true) {
                    if ($outletOpening->approval_status == 'pending' && $outletOpening->timeliness_status == "late") {
                        $action .= '<button class="btn btn-sm btn-icon btn-light-danger btn-reject" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Tolak" data-id="' . $outletOpening->id . '">
                          <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                          <span class="svg-icon svg-icon-5 m-0">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                  <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                  <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                              </svg>
                          </span>
                          <!--end::Svg Icon-->
                      </button>
                      <button class="btn btn-sm btn-icon btn-light-success btn-approve" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Setujui" data-id="' . $outletOpening->id . '">
                          <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                          <span class="svg-icon svg-icon-5 m-0">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                  <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor" />
                              </svg>
                          </span>
                          <!--end::Svg Icon-->
                      </button>';
                    }
                }
                $action .= '</div>';

                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['person_in_charge', 'timeliness_status', 'approval_status', 'formatted_attachment', 'image_preview', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $offices = Office::all();
        $employees = Employee::all();

        return view('outlet-openings.create', [
            'offices' => $offices,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'office_id' => 'required|exists:offices,id',
                'date' => 'required|date',
                'time' => 'required',
                'created_by' => 'required|exists:employees,id',
            ]);

            $currentDateOutletOpeningCount = OutletOpening::withTrashed()->where('date', $request->date)->count();
            $number = 'OP/' . date('dmy') . '/' . sprintf('%03d', ($currentDateOutletOpeningCount + 1));

            $office = Office::find($request->office_id);

            // Calculate timeliness_status based on office opening_time
            $timelinessStatus = 'on_time'; // default
            $lateAmount = 0;
            if ($office && $office->opening_time) {
                // Ensure time format is correct - convert from H:i to H:i:s if needed
                $requestTime = $request->time;
                if (strlen($requestTime) === 5) { // Format is H:i
                    $requestTime .= ':00'; // Add seconds
                }

                $openingTime = \Carbon\Carbon::createFromFormat('H:i:s', $office->opening_time)->addMinutes(11);
                $actualTime = \Carbon\Carbon::createFromFormat('H:i:s', $requestTime);

                if ($actualTime->gt($openingTime)) {
                    $timelinessStatus = 'late';
                    $lateAmount = $actualTime->diffInMinutes($openingTime);
                }
            }

            $newOutletOpening = new OutletOpening();
            $newOutletOpening->number = $number;
            $newOutletOpening->date = $request->date;
            $newOutletOpening->time = $request->time;
            $newOutletOpening->timeliness_status = $timelinessStatus;
            $newOutletOpening->approval_status = 'pending'; // default status
            $newOutletOpening->office_id = $request->office_id;
            $newOutletOpening->late_amount = $lateAmount;
            $newOutletOpening->created_by = $request->created_by;

            // Set created_at dari field create_time jika ada
            if ($request->has('create_time') && !empty($request->create_time)) {
                $newOutletOpening->created_at = Carbon::parse($request->create_time);
            }

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('outlet_opening_attachment')) {
                $file = $request->file('outlet_opening_attachment');
                $name = time() . '-outlet-opening-' . implode('-', explode(' ', $office->name ?? "OFFICE_NAME"));
                $filePath = 'outlet-opening/attachments/' . $name . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = config('filesystems.disks.s3.url') . '/' . $filePath;
            }

            $newOutletOpening->image = $urlPath;
            $newOutletOpening->save();

            return response()->json([
                'message' => 'Laporan pembukaan outlet berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $outletOpening = OutletOpening::with(['office', 'creator'])->findOrFail($id);
        $offices = Office::all();
        $employees = Employee::all();

        return view('outlet-openings.edit', [
            'outletOpening' => $outletOpening,
            'offices' => $offices,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $outletOpening = OutletOpening::findOrFail($id);
            $office = Office::find($request->office_id);

            // Calculate timeliness_status based on office opening_time
            $timelinessStatus = 'on_time'; // default
            $lateAmount = 0;
            if ($office && $office->opening_time) {
                // Ensure time format is correct - convert from H:i to H:i:s if needed
                $requestTime = $request->time;
                if (strlen($requestTime) === 5) { // Format is H:i
                    $requestTime .= ':00'; // Add seconds
                }

                $openingTime = \Carbon\Carbon::createFromFormat('H:i:s', $office->opening_time)->addMinutes(11);
                $actualTime = \Carbon\Carbon::createFromFormat('H:i:s', $requestTime);

                if ($actualTime->gt($openingTime)) {
                    $timelinessStatus = 'late';
                    $lateAmount = $actualTime->diffInMinutes($openingTime);
                }
            }

            $outletOpening->date = $request->date;
            $outletOpening->time = $request->time;
            $outletOpening->timeliness_status = $timelinessStatus;
            $outletOpening->office_id = $request->office_id;
            $outletOpening->late_amount = $lateAmount;
            $outletOpening->created_by = $request->created_by;
            $outletOpening->updated_by = Auth::id()->employee_id ?? null;

            // Set created_at dari field create_time jika ada
            if ($request->has('create_time') && !empty($request->create_time)) {
                $outletOpening->created_at = Carbon::parse($request->create_time);
            }

            // Handle file upload if new file is provided
            if ($request->hasFile('outlet_opening_attachment')) {
                $file = $request->file('outlet_opening_attachment');
                $name = time() . '-outlet-opening-' . implode('-', explode(' ', $office->name ?? "OFFICE_NAME"));
                $filePath = 'outlet-opening/attachments/' . $name . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = config('filesystems.disks.s3.url') . '/' . $filePath;
                $outletOpening->image = $urlPath;
            }

            $outletOpening->save();

            return response()->json([
                'message' => 'Laporan pembukaan outlet berhasil diperbarui'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $outletOpening = OutletOpening::findOrFail($id);

            // Jika sudah disetujui, reverse aksi dengan mengembalikan outlet_opening_late menjadi 0
            if ($outletOpening->approval_status == 'approved') {
                Attendance::whereHas('employee', function ($q) use ($outletOpening) {
                    $q->where('office_id', $outletOpening->office_id);
                })->where('date', $outletOpening->date)->update([
                    'outlet_opening_late' => 0
                ]);
            }

            $outletOpening->deleted_by = Auth::user()->employee_id ?? null;
            $outletOpening->delete();

            DB::commit();
            return response()->json([
                'message' => 'Laporan pembukaan outlet berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $outletOpening = OutletOpening::findOrFail($id);
            $outletOpening->approval_status = 'approved';
            $outletOpening->confirmed_by = auth()->id();
            $outletOpening->save();

            Attendance::whereHas('employee', function ($q) use ($outletOpening) {
                $q->where('office_id', $outletOpening->office_id);
            })->where('date', $outletOpening->date)->update([
                'outlet_opening_late' => $outletOpening->late_amount
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Laporan pembukaan outlet berhasil disetujui'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            $outletOpening = OutletOpening::findOrFail($id);
            $outletOpening->approval_status = 'rejected';
            $outletOpening->confirmed_by = auth()->id();
            $outletOpening->save();

            return response()->json([
                'message' => 'Laporan pembukaan outlet berhasil ditolak'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function employeeColumn(Employee $employee)
    {
        $employeeColumn = ' <div class="d-flex align-items-center">';

        if (isset($employee->photo) && !empty($employee->photo)) {
            $employeeColumn .= '
            <div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label" style="background-image:url(\'' . $employee->photo . '\')"></div>
            </div>
            ';
        } else {
            $employeeColumn .= '<div class="symbol symbol-40px symbol-circle">
                <div class="symbol-label fs-2 fw-semibold text-primary">' . substr($employee->name, 0, 1) . '</div>
            </div>';
        }

        $employeeColumn .= '<div class="ps-3">
            <p class="mb-1 fs-7 fw-bolder"><a href="/employees/' . $employee->id . '/detail-v2" class="text-gray-700 text-hover-primary">' . $employee->name . '</a></p>
                <span class="fs-7 text-muted">' . ($employee->office->division->company->name ?? '') . '</span>
        </div>';


        $employeeColumn .= ' </div>';

        return $employeeColumn;
    }
}
