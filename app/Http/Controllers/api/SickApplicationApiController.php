<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SickApplication;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class SickApplicationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $whereClause = $request->query();
        try {
            $sickApplications = SickApplication::with(['employee'])->where($whereClause)->get();
            return response()->json([
                'message' => 'OK',
                'error' => false,
                'code' => 200,
                'data' => $sickApplications,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Error',
                'error' => true,
                'code' => 500,
                'errors' => $e
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTest(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $note = $request->note;

            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    }),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // return response()->json([
            //     'message' => $request->hasFile('attachment') ? 'has file' : 'no file', 
            // ]);

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . $file->getClientOriginalName();
                $filePath = 'timeoffs/attachments/sicks_' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $application = new SickApplication();
            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->attachment = $urlPath;
            $application->save();

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $application,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'ERROR ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
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
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $note = $request->note;

            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    }),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . $file->getClientOriginalName();
                $filePath = 'timeoffs/attachments/sicks_' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $application = new SickApplication();
            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->attachment = $urlPath;
            $application->save();

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $application,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBase64(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $note = $request->note;

            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    }),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $filePath = null;
            $urlPath = null;
            // if ($request->hasFile('attachment')) {
            //     $file = $request->file('attachment');

            //     // Compress Image
            //     $imageWidth = Image::make($file->getRealPath())->width();
            //     $ratio = 50 / 100;
            //     $newWidth = floor($imageWidth * $ratio);
            //     $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
            //         $constraint->aspectRatio();
            //     })->stream();

            //     // Uploading Image
            //     $name = time() . '-clockout-' . $file->getClientOriginalName();
            //     $filePath = 'timeoffs/attachments/sicks_' . $name;
            //     $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
            //     $urlPath = Storage::disk('s3')->url($filePath);
            //     // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            // }
            if (!empty($request->attachment)) {
                $file = base64_decode($request->attachment);
                // $file = $request->file('clock_in_attachment');

                $name = time();
                // $filePath = 'attendances/attachments/' . $name . $file->getExtension();
                $filePath = 'timeoffs/attachments/sicks_' . $name . '.png';
                $path = Storage::disk('s3')->put($filePath, $file, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
            }

            $application = new SickApplication();
            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->attachment = $urlPath;
            $application->save();

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $application,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
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
        try {
            $sickApplication = SickApplication::with(['employee', 'confirmedByEmployee'])->find($id);

            return response()->json([
                'message' => 'OK',
                'data' => $sickApplication,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            $employeeId = $request->employee_id;
            $date = $request->date;
            $applicationDates = $request->application_dates;
            $approvalStatus = $request->approval_status;
            $note = $request->note;

            $application = SickApplication::find($id);

            if (isset($application->approval_status) && $application->approval_status !== 'pending') {
                throw new Error('Pengajuan sudah dikonfirmasi');
            }

            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'application_dates' => [
                    'required',
                    Rule::unique('sick_applications')->where(function ($query) use ($applicationDates, $employeeId) {
                        return $query->where('application_dates', 'like', '%' . $applicationDates . '%')->whereIn('employee_id', [$employeeId]);
                    })->ignore($id),
                    'max:255'
                ],
                'approval_status' => [
                    Rule::in(['pending', 'approved', 'rejected'])
                ],
                'note' => 'max:255',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $filePath = null;
            $urlPath = $application->attachment;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Compress Image
                $imageWidth = Image::make($file->getRealPath())->width();
                $ratio = 50 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . $file->getClientOriginalName();
                $filePath = 'timeoffs/attachments/sicks_' . $name;
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $application->date = $date;
            $application->application_dates = $applicationDates;
            $application->note = $note;
            $application->approval_status = $approvalStatus;
            $application->employee_id = $employeeId;
            $application->attachment = $urlPath;
            $application->save();

            return response()->json([
                'message' => 'Data pengajuan telah tersimpan',
                'code' => 200,
                'data' => $application,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
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
        try {
            $application = SickApplication::findOrFail($id);
            $application->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Approve application.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $application = SickApplication::find($id);

            $employeeId = $application->employee_id;

            if ($application->approval_status !== 'pending') {
                throw new Exception('pengajuan ini sudah dikonfirmasi');
            }

            $application->approval_status = 'approved';
            $application->confirmed_by = 29;
            $application->confirmed_at = date("Y-m-d H:i:s");
            $application->save();

            $attendances = [];
            $applicationDates = explode(',', $application->application_dates);
            if (is_array($applicationDates)) {
                foreach ($applicationDates as $applicationDate) {
                    $isValidDate = Carbon::canBeCreatedFromFormat($applicationDate, 'Y-m-d');
                    if ($isValidDate) {
                        array_push($attendances, [
                            'employee_id' => $employeeId,
                            'date' => $applicationDate,
                            'status' => 'sakit',
                            'approval_status' => 'approved',
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);
                    }
                }
            }

            DB::table('attendances')->insert($attendances);

            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve application.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        DB::beginTransaction();
        try {
            $application = SickApplication::find($id);

            if ($application->approval_status !== 'pending') {
                throw new Exception('pengajuan ini sudah dikonfirmasi');
            }

            $application->approval_status = 'rejected';
            $application->confirmed_by = 29;
            $application->confirmed_at = date("Y-m-d H:i:s");
            $application->save();

            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
