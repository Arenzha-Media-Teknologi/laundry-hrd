<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityItem;
use App\Models\Attendance;
use App\Models\Employee;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActivityApiController extends Controller
{
    /**
     * Do check in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'time' => 'required',
            ]);

            $employeeId = $request->employee_id;
            $date = $request->date;
            $time = date('H:i:s');
            $checkInIpAddress = $request->check_in_ip_address;
            $checkInDeviceDetail = $request->check_in_device_detail;
            $checkInLatitude = $request->check_in_latitude;
            $checkInLongitude = $request->check_in_longitude;
            $checkInLocation = $request->check_in_location;
            $checkInIsInsideOfficeRadius = $request->check_in_is_inside_office_radius;
            $checkInNote = $request->note;

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Error('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Error('Pegawai nonaktif tidak dapat melakukan aktifitas');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('status', 'hadir')
                // ->where('clock_in_time', null)
                // ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance == null) {
                throw new Error('Kamu harus clock in sebelum melakukan aktifitas');
            }

            $timeLate = 0;

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $name = time() . '-checkin-' . implode('-', explode(' ', $employee->name));
                $filePath = 'activities/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $activity = new Activity();
            $activity->employee_id = $employeeId;
            $activity->date = $date;
            $activity->check_in_time = $time;
            $activity->check_in_ip_address = $checkInIpAddress;
            $activity->check_in_device_detail = $checkInDeviceDetail;
            $activity->check_in_latitude = $checkInLatitude;
            $activity->check_in_longitude = $checkInLongitude;
            $activity->check_in_location = $checkInLocation;
            $activity->check_in_is_inside_office_radius = $checkInIsInsideOfficeRadius;
            $activity->check_in_note = $checkInNote;
            $activity->check_in_attachment = $urlPath;
            $activity->save();

            DB::commit();
            return response()->json([
                'message' => 'Check in berhasil tersimpan',
                'data' => $activity,
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Do check out attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        DB::beginTransaction();
        try {
            // return response()->json([
            //     'request' => $request->all(),
            // ]);

            // $hasFiles = [];
            // foreach ($request->items as $index => $item) {
            //     if ($request->hasFile('activity_file_' . $index)) {
            //         $hasFiles[] = 'index ' . $index . ' has file';
            //     } else {
            //         $hasFiles[] = 'index ' . $index . ' doesnt have file';
            //     }
            // }

            // return $hasFiles;

            $validated = $request->validate([
                'employee_id' => 'required',
                'time' => 'required',
            ]);

            $employeeId = $request->employee_id;
            $date = $request->date;
            $time = date('H:i:s');
            $checkOutIpAddress = $request->check_out_ip_address;
            $checkOutDeviceDetail = $request->check_out_device_detail;
            $checkOutLatitude = $request->check_out_latitude;
            $checkOutLongitude = $request->check_out_longitude;
            $checkOutLocation = $request->check_out_location;
            $checkOutIsInsideOfficeRadius = $request->check_out_is_inside_office_radius;
            $checkOutNote = $request->note;
            $activityItems = $request->items;

            $todayActivity = Activity::query()
                ->where('employee_id', $employeeId)
                ->where('date', $date)
                ->where('check_out_time', null)
                ->whereNot('check_in_time', null)
                ->orderBy('id', 'DESC')
                ->first();

            if ($todayActivity == null) {
                throw new Error('Anda belum clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Error('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Error('Pegawai nonaktif tidak dapat melakukan clock out');
            }

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $name = time() . '-checkout-' . implode('-', explode(' ', $employee->name));
                $filePath = 'activities/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $activity = Activity::find($todayActivity->id);
            $activity->employee_id = $employeeId;
            $activity->date = $date;
            $activity->check_out_time = $time;
            $activity->check_out_ip_address = $checkOutIpAddress;
            $activity->check_out_device_detail = $checkOutDeviceDetail;
            $activity->check_out_latitude = $checkOutLatitude;
            $activity->check_out_longitude = $checkOutLongitude;
            $activity->check_out_location = $checkOutLocation;
            $activity->check_out_is_inside_office_radius = $checkOutIsInsideOfficeRadius;
            $activity->check_out_note = $checkOutNote;
            $activity->check_out_attachment = $urlPath;
            $activity->save();

            foreach ($activityItems as $index => $item) {
                $filePath = null;
                $urlPath = null;
                if ($request->hasFile('activity_file_' . $index)) {
                    $file = $request->file('activity_file_' . $index);

                    $name = time() . '-activity-item-' . implode('-', explode(' ', $employee->name));
                    $filePath = 'activity-items/attachments/' . $name . $file->getExtension();
                    $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                    $urlPath = Storage::disk('s3')->url($filePath);
                    // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
                }

                $newActivityItem = new ActivityItem();
                $newActivityItem->note = $item['note'];
                $newActivityItem->attachment = $urlPath;
                $newActivityItem->activity_id = $activity->id;
                $newActivityItem->save();
            }

            DB::commit();

            $activity = Activity::with(['items'])->find($todayActivity->id);

            return response()->json([
                'message' => 'success',
                'data' => $activity,
                'code' => 200,
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function getCurrentState()
    {
        try {
            $currentState = "check_in";

            $employeeId = request()->query('employee_id');
            $date = request()->query('date');

            if (empty($employeeId)) {
                throw new Error('employee_id is required');
            }

            if (empty($date)) {
                throw new Error('date is required');
            }

            $todayActivity = Activity::where('employee_id', $employeeId)->where('date', $date)->orderBy('id', 'DESC')->first();

            if ($todayActivity != null) {
                if ($todayActivity->check_in_time != null && $todayActivity->check_out_time == null) {
                    $currentState = 'check_out';
                }
            }

            return response()->json([
                'message' => 'success',
                'data' => $currentState
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getActiveActivity()
    {
        try {
            $currentState = "check_in";

            $employeeId = request()->query('employee_id');
            $date = request()->query('date');

            if (empty($employeeId)) {
                throw new Error('employee_id is required');
            }

            if (empty($date)) {
                throw new Error('date is required');
            }

            $activeActivity = null;

            $todayActivity = Activity::where('employee_id', $employeeId)->where('date', $date)->orderBy('id', 'DESC')->first();

            if ($todayActivity != null) {
                if ($todayActivity->check_in_time != null && $todayActivity->check_out_time == null) {
                    $currentState = 'check_out';
                    $activeActivity = $todayActivity;
                }
            }

            return response()->json([
                'message' => 'success',
                'data' => $activeActivity
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
