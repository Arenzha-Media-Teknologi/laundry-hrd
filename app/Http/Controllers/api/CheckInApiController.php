<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Employee;
use App\Models\WorkingPattern;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CheckInApiController extends Controller
{
    public function getAll()
    {
        return response()->json([
            'message' => 'OK',
        ]);
    }

    /**
     * Do check in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'time' => 'required',
            ]);

            $employeeId = $request->employee_id;
            $date = $request->date;
            $workingPatternId = $request->working_pattern_id;
            // $clockInTime = $request->clock_in_time;
            // $clockInAt = $request->clock_in_at;
            $time = date('H:i:s');
            $ipAddress = $request->ip_address;
            $deviceDetail = $request->device_detail;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $location = $request->location;
            $isInsideOfficeRadius = $request->is_inside_office_radius;
            $officeLatitude = $request->office_latitude;
            $officeLongitude = $request->office_longitude;
            $note = $request->note;

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan check in');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('status', 'hadir')
                // ->where('clock_in_time', null)
                // ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance == null) {
                throw new Exception('Kamu harus clock in sebelum melakukan check in');
            }

            $timeLate = 0;

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $name = time() . '-checkin-' . implode('-', explode(' ', $employee->name));
                $filePath = 'checkins/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $checkin = new CheckIn();
            $checkin->employee_id = $employeeId;
            $checkin->date = $date;
            $checkin->time = $time;
            $checkin->ip_address = $ipAddress;
            $checkin->device_detail = $deviceDetail;
            $checkin->latitude = $latitude;
            $checkin->longitude = $longitude;
            $checkin->location = $location;
            $checkin->is_inside_office_radius = $isInsideOfficeRadius;
            $checkin->office_latitude = $officeLatitude;
            $checkin->office_longitude = $officeLongitude;
            $checkin->status = 'hadir';
            $checkin->time_late = $timeLate;
            $checkin->note = $note;
            $checkin->attachment = $urlPath;
            $checkin->working_pattern_id = $workingPatternId;
            $checkin->save();

            return response()->json([
                'message' => 'success',
                'data' => $checkin,
                'code' => 200,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Do check in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkInV2(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'time' => 'required',
            ]);

            $employeeId = $request->employee_id;
            $date = $request->date;
            $workingPatternId = $request->working_pattern_id;
            // $clockInTime = $request->clock_in_time;
            // $clockInAt = $request->clock_in_at;
            $time = date('H:i:s');
            $ipAddress = $request->ip_address;
            $deviceDetail = $request->device_detail;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $location = $request->location;
            $isInsideOfficeRadius = $request->is_inside_office_radius;
            $officeLatitude = $request->office_latitude;
            $officeLongitude = $request->office_longitude;
            $note = $request->note;

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan check in');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('status', 'hadir')
                // ->where('clock_in_time', null)
                // ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance == null) {
                throw new Exception('Kamu harus clock in sebelum melakukan check in');
            }

            $timeLate = 0;

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $name = time() . '-checkin-' . implode('-', explode(' ', $employee->name));
                $filePath = 'checkins/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $checkin = new CheckIn();
            $checkin->employee_id = $employeeId;
            $checkin->date = $date;
            $checkin->time = $time;
            $checkin->ip_address = $ipAddress;
            $checkin->device_detail = $deviceDetail;
            $checkin->latitude = $latitude;
            $checkin->longitude = $longitude;
            $checkin->location = $location;
            $checkin->is_inside_office_radius = $isInsideOfficeRadius;
            $checkin->office_latitude = $officeLatitude;
            $checkin->office_longitude = $officeLongitude;
            $checkin->status = 'hadir';
            $checkin->time_late = $timeLate;
            $checkin->note = $note;
            $checkin->attachment = $urlPath;
            $checkin->working_pattern_id = $workingPatternId;
            $checkin->save();

            return response()->json([
                'message' => 'success',
                'data' => $checkin,
                'code' => 200,
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
            $checkIn = CheckIn::find($id);

            return response()->json([
                'message' => 'OK',
                'data' => $checkIn,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // public function getCurrentState()
    // {
    //     try {
    //         $employeeId = 
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //     }
    // }
}
