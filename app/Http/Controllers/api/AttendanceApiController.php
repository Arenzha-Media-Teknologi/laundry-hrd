<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceQuote;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\OutletOpening;
use App\Models\SickApplication;
use App\Models\WorkingPattern;
use App\Models\WorkScheduleItem;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class AttendanceApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Responses
     */
    public function index()
    {
        return response()->json([
            'message' => 'you\'re connected',
        ]);
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
    public function store(Request $request)
    {

        //   if (todayAttendance) {
        //     throw new Error('Anda sudah check in hari ini');
        //   }

        //   /**
        //    * TODO: CHECK IN STEPS
        //    * - Check active working pattern ✅
        //    * - Calculate working pattern✅
        //    * - calculate time late✅
        //    */

        //   // Check active working pattern
        //   const employee = await Employee.findByPk(employee_id, {
        //     include: [
        //       {
        //         model: WorkingPattern,
        //         as: 'workingPatterns',
        //         through: {
        //           where: { active: true },
        //           order: [['effectiveDate', 'DESC']],
        //         },
        //         include: ['items'],
        //       },
        //     ],
        //   });

        //   // ! Check If employee exist
        //   if (!employee) {
        //     throw new Error('Employee does not exist');
        //   }

        //   // ? Assign working pattern
        //   let activeWorkingPattern = null;

        //   // const { workingPatterns } = employee;

        //   // if (Array.isArray(workingPatterns)) {
        //   //   if (workingPatterns.length > 0) {
        //   //     [activeWorkingPattern] = workingPatterns;
        //   //   }
        //   // }

        //   activeWorkingPattern = await WorkingPattern.findByPk(working_pattern_id, {
        //     include: ['items'],
        //   });

        //   // ! Check If active working pattern exist
        //   if (!activeWorkingPattern) {
        //     throw new Error('Employee does not have active working pattern');
        //   }

        //   // Calculate working pattern

        //   // const WPEffectiveDate =
        //   //   activeWorkingPattern.EmployeeWorkingPatterns.effectiveDate;
        //   // const WPDaysTo = activeWorkingPattern.EmployeeWorkingPatterns.daysTo;
        //   // const WPNumberOfDays = activeWorkingPattern.numberOfDays;
        //   // TODO ----------------------------

        //   // const dayOrder = workingPatternHelper.determineWorkingPatternOrder(
        //   //   WPEffectiveDate,
        //   //   WPDaysTo,
        //   //   date,
        //   //   WPNumberOfDays,
        //   // );
        //   const todayOrder = dayjs(date, 'YYYY-MM-DD').day();
        //   const dayOrder = todayOrder < 1 ? 7 : todayOrder;


        //   // return dayOrder;

        //   if (dayOrder === null) {
        //     throw new Error('Failed to read employee working pattern');
        //   }

        //   // const workingPatternDay = activeWorkingPattern.items.filter(
        //   //   (item) => Number(item.order) === Number(dayOrder),
        //   // )[0];

        //   const workingPatternDay = activeWorkingPattern.items.filter(
        //     (item) => Number(item.order) === Number(dayOrder),
        //   )[0];

        //   if (!workingPatternDay) {
        //     throw new Error('Working pattern day not found');
        //   }

        //   // return workingPatternDay;

        //   const timeLate = dayjs(clock_in_at).diff(
        //     dayjs(`${date} ${workingPatternDay.clockIn}`),
        //     'minute',
        //   );

        //   let attachment = null;

        //   if (req.file) {
        //     const params = {
        //       ACL: 'public-read',
        //       Bucket: config.aws.s3.bucket,
        //       Body: req.file.buffer,
        //       ContentType: 'image/jpeg',
        //       Key: `attendances/clockins/${employee_id}-${Date.now().toString()}.jpg`,
        //     };

        //     attachment = await this.awsS3upload(params);
        //   }

        //   const attendance = await Attendance.create({
        //     employeeId: employee_id,
        //     date,
        //     clockIn: clock_in,
        //     clockInAt: clock_in_at,
        //     clockInIpAddress: clock_in_ip_address,
        //     clockInDeviceDetail: clock_in_device_detail,
        //     clockInLatitude: clock_in_latitude,
        //     clockInLongitude: clock_in_longitude,
        //     clockInOfficeLatitude: clock_in_office_latitude,
        //     clockInOfficeLongitude: clock_in_office_longitude,
        //     clockInWorkingPatternTime: workingPatternDay.clockIn,
        //     status: 'hadir_hari_kerja',
        //     timeLate: timeLate > 0 ? timeLate : 0,
        //     clockInNote: note,
        //     clockInAttachment: attachment ? attachment.key : null,
        //     workingPatternId: working_pattern_id,
        //   });
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
            $attendance = Attendance::with(['longShiftConfirmer'])->find($id);

            return response()->json([
                'message' => 'OK',
                'data' => $attendance,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockIn(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                // 'working_pattern_id' => 'nullable',
                'clock_in_time' => 'required',
                'clock_in_at' => 'required',
                // 'attachment' => 'nullable|image|mimes:jpeg,png,jpg',
                'attachment' => 'nullable|string',
            ]);

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            $workingPatternId = $request->working_pattern_id;
            // $clockInTime = $request->clock_in_time;
            // $clockInAt = $request->clock_in_at;
            $clockInTime = date('H:i:s');
            $clockInAt = date('Y-m-d H:i:s');
            $clockInIpAddress = $request->clock_in_ip_address;
            $clockInDeviceDetail = $request->clock_in_device_detail;
            $clockInLatitude = $request->clock_in_latitude;
            $clockInLongitude = $request->clock_in_longitude;
            $clockInOfficeLatitude = $request->clock_in_office_latitude;
            $clockInOfficeLongitude = $request->clock_in_office_longitude;
            $note = $request->note;

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance !== null) {
                throw new Exception('Anda sudah clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock in');
            }

            $timeLate = 0;

            $workingPatternDay = null;
            if (isset($workingPatternId) && $workingPatternId !== "") {
                $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                if ($activeWorkingPattern == null) {
                    throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                }

                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                $workingPatternDay = $workingPatternDays[0] ?? null;


                $calendarHolidays = [];

                if (isset($workingPatternDay->clock_in)) {
                    if (count($calendarHolidays) < 1) {
                        $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_in)->diffInMinutes($attendanceDate . ' ' . $clockInTime, false);
                        // $timeLate = Carbon::parse($clockInAt)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_in);
                        $timeLate = $diffMinutes;
                    }
                }
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'time_late' => $timeLate,
            // ]);

            // Photo
            $filePath = null;
            $urlPath = null;
            if (isset($request->attachment)) {
                // $file = $request->file('attachment');
                $base64File = base64_decode($request->attachment);

                // Compress Image
                // $imageWidth = Image::make($file->getRealPath())->width();
                // $ratio = 50 / 100;
                // $newWidth = floor($imageWidth * $ratio);
                // $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->stream();
                $imageWidth = Image::make($base64File)->width();
                $ratio = 20 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($base64File)->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockin-' . implode('-', explode(' ', $employee->name));
                $filePath = 'attendances/attachments/' . $name . '.jpg';
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_in_time = $clockInTime;
            $attendance->clock_in_at = $clockInAt;
            $attendance->clock_in_ip_address = $clockInIpAddress;
            $attendance->clock_in_device_detail = $clockInDeviceDetail;
            $attendance->clock_in_latitude = $clockInLatitude;
            $attendance->clock_in_longitude = $clockInLongitude;
            $attendance->clock_in_office_latitude = $clockInOfficeLatitude;
            $attendance->clock_in_office_longitude = $clockInOfficeLongitude;
            $attendance->clock_in_working_pattern_time = $workingPatternDay->clock_in ?? null;
            $attendance->status = 'hadir';
            $attendance->time_late = $timeLate;
            $attendance->clock_in_note = $note;
            $attendance->clock_in_attachment = $urlPath;
            $attendance->working_pattern_id = $workingPatternId;
            $attendance->save();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'time_late' => $timeLate,
                // ],
                'data' => $attendance,
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
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockInV2(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required',
                'date' => 'required|date',
                // 'working_pattern_id' => 'nullable',
                'clock_in_time' => 'required',
                'clock_in_at' => 'required',
                // 'attachment' => 'nullable|image|mimes:jpeg,png,jpg',
                // 'clock_in_attachment' => 'nullable|string',
            ]);

            $splittedNote = explode('---', $request->clock_in_note ?? "");

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            $workingPatternId = $request->working_pattern_id;
            // $clockInTime = $request->clock_in_time;
            // $clockInAt = $request->clock_in_at;
            $clockInTime = date('H:i:s');
            $clockInAt = date('Y-m-d H:i:s');
            $clockInIpAddress = $request->clock_in_ip_address;
            $clockInDeviceDetail = $request->clock_in_device_detail;
            $clockInLatitude = $request->clock_in_latitude;
            $clockInLongitude = $request->clock_in_longitude;
            $clockInIsInsideOfficeRadius = $request->clock_in_is_inside_office_radius;
            $clockInOfficeLatitude = $request->clock_in_office_latitude;
            $clockInOfficeLongitude = $request->clock_in_office_longitude;
            $note = $splittedNote[1] ?? null;
            $permissionNote = $splittedNote[0] ?? null;
            $permissionCategoryId = $request->permission_category_id;
            $status = $request->status ?? 'hadir';

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance !== null) {
                throw new Exception('Anda sudah clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock in');
            }

            $todayWorkSchedule = WorkScheduleItem::with(['workScheduleWorkingPattern'])->where('employee_id', $employeeId)->where('date', $attendanceDate)->first();

            $timeLate = 0;
            $workingPatternDay = null;
            $workScheduleWorkingPatternId = null;

            // Skip validasi work schedule is_off jika status = "off" (user memilih OFF secara manual)
            if ($todayWorkSchedule != null && $status !== 'off') {
                $isOff = $todayWorkSchedule->is_off;
                if ($isOff == 1) {
                    throw new Error('Gagal absen, jadwal kamu OFF hari ini');
                }

                $workingPattern = $todayWorkSchedule->workScheduleWorkingPattern;

                if ($workingPattern != null) {
                    $calendarHolidays = [];

                    $workScheduleWorkingPatternId = $workingPattern->id;

                    if (isset($workingPattern->start_time)) {
                        if (count($calendarHolidays) < 1) {
                            $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPattern->start_time)->diffInMinutes($attendanceDate . ' ' . $clockInTime, false);
                            $timeLate = $diffMinutes;
                        }
                    }
                }
            } else {
                // Skip perhitungan time_late jika status = "off"
                if ($status !== 'off' && isset($workingPatternId) && $workingPatternId !== "") {
                    $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                    if ($activeWorkingPattern == null) {
                        throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                    }

                    $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                    $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                    $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                        return $item->order == $dayOrder;
                    })->values()->all();

                    if (count($workingPatternDays) < 1) {
                        throw new Exception('"working pattern day" tidak ditemukan');
                    }

                    $workingPatternDay = $workingPatternDays[0] ?? null;


                    $calendarHolidays = [];

                    if (isset($workingPatternDay->clock_in)) {
                        if (count($calendarHolidays) < 1) {
                            $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_in)->diffInMinutes($attendanceDate . ' ' . $clockInTime, false);
                            // $timeLate = Carbon::parse($clockInAt)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_in);
                            $timeLate = $diffMinutes;
                        }
                    }
                }
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'time_late' => $timeLate,
            // ]);

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('clock_in_attachment')) {
                $file = $request->file('clock_in_attachment');

                $name = time() . '-clockin-' . implode('-', explode(' ', $employee->name));
                $filePath = 'attendances/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_in_time = $clockInTime;
            $attendance->clock_in_at = $clockInAt;
            $attendance->clock_in_ip_address = $clockInIpAddress;
            $attendance->clock_in_device_detail = $clockInDeviceDetail;
            $attendance->clock_in_latitude = $clockInLatitude;
            $attendance->clock_in_longitude = $clockInLongitude;
            $attendance->clock_in_is_inside_office_radius = $clockInIsInsideOfficeRadius;
            $attendance->clock_in_office_latitude = $clockInOfficeLatitude;
            $attendance->clock_in_office_longitude = $clockInOfficeLongitude;
            $attendance->clock_in_working_pattern_time = $workingPatternDay->clock_in ?? null;
            $attendance->status = $status;
            $attendance->time_late = $timeLate;
            $attendance->clock_in_note = $note;
            $attendance->clock_in_attachment = $urlPath;
            $attendance->working_pattern_id = $workingPatternId;
            $attendance->work_schedule_working_pattern_id = $workScheduleWorkingPatternId;

            if (!empty($permissionCategoryId)) {
                $attendance->is_permission = 1;
                $attendance->permission_category_id = $permissionCategoryId;
                $attendance->permission_status = "pending";
                $attendance->permission_note = $permissionNote;
            }

            $attendance->save();

            if ($employee->office_id) {
                $outletOpening = OutletOpening::where('office_id', $employee->office_id)
                    ->where('date', $attendanceDate)
                    ->where('approval_status', 'approved')
                    ->where('timeliness_status', 'late')
                    ->first();

                if ($outletOpening) {
                    $attendance->outlet_opening_late = $outletOpening->late_amount;
                    $attendance->save();
                }
            }

            $quotes = $this->getRandomQuotes();

            return response()->json([
                'message' => 'success',
                'quotes' => $quotes,
                'data' => $attendance,
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockInV3(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required',
                'date' => 'required|date',
                // 'working_pattern_id' => 'nullable',
                'clock_in_time' => 'required',
                'clock_in_at' => 'required',
                // 'attachment' => 'nullable|image|mimes:jpeg,png,jpg',
                // 'clock_in_attachment' => 'nullable|string',
            ]);

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            $workingPatternId = $request->working_pattern_id;
            // $clockInTime = $request->clock_in_time;
            // $clockInAt = $request->clock_in_at;
            $clockInTime = date('H:i:s');
            $clockInAt = date('Y-m-d H:i:s');
            $clockInIpAddress = $request->clock_in_ip_address;
            $clockInDeviceDetail = $request->clock_in_device_detail;
            $clockInLatitude = $request->clock_in_latitude;
            $clockInLongitude = $request->clock_in_longitude;
            $clockInIsInsideOfficeRadius = $request->clock_in_is_inside_office_radius;
            $clockInOfficeLatitude = $request->clock_in_office_latitude;
            $clockInOfficeLongitude = $request->clock_in_office_longitude;
            $note = $request->clock_in_note;
            $status = $request->status ?? 'hadir';
            $permissionCategoryId = $request->permission_category_id;

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_in_time', null)
                ->first();

            if ($todayAttendance !== null) {
                throw new Exception('Kamu sudah clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock in');
            }

            $todayWorkSchedule = WorkScheduleItem::where('employee_id', $employeeId)->where('date', $attendanceDate)->first();

            // Skip validasi work schedule is_off jika status = "off" (user memilih OFF secara manual)
            if ($todayWorkSchedule != null && $status !== 'off') {
                $isOff = $todayWorkSchedule->is_off;
                if ($isOff == 1) {
                    throw new Error('Gagal absen, jadwal kamu OFF hari ini');
                }
            }

            $timeLate = 0;

            // Skip perhitungan time_late jika status = "off"
            $workingPatternDay = null;
            if ($status !== 'off' && isset($workingPatternId) && $workingPatternId !== "") {
                $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                if ($activeWorkingPattern == null) {
                    throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                }

                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                $workingPatternDay = $workingPatternDays[0] ?? null;


                $calendarHolidays = [];

                if (isset($workingPatternDay->clock_in)) {
                    if (count($calendarHolidays) < 1) {
                        $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_in)->diffInMinutes($attendanceDate . ' ' . $clockInTime, false);
                        // $timeLate = Carbon::parse($clockInAt)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_in);
                        $timeLate = $diffMinutes;
                    }
                }
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'time_late' => $timeLate,
            // ]);

            // Photo
            $filePath = null;
            $urlPath = null;
            // if ($request->hasFile('clock_in_attachment')) {
            //     $file = $request->file('clock_in_attachment');

            //     $name = time() . '-clockin-' . implode('-', explode(' ', $employee->name));
            //     $filePath = 'attendances/attachments/' . $name . $file->getExtension();
            //     $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
            //     $urlPath = Storage::disk('s3')->url($filePath);
            // }
            if (!empty($request->clock_in_attachment)) {
                $file = base64_decode($request->clock_in_attachment);
                // $file = $request->file('clock_in_attachment');

                $name = time() . '-clockin-' . implode('-', explode(' ', $employee->name));
                // $filePath = 'attendances/attachments/' . $name . $file->getExtension();
                $filePath = 'attendances/attachments/' . $name . '.png';
                $path = Storage::disk('s3')->put($filePath, $file, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
            }

            $attendance = new Attendance();
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_in_time = $clockInTime;
            $attendance->clock_in_at = $clockInAt;
            $attendance->clock_in_ip_address = $clockInIpAddress;
            $attendance->clock_in_device_detail = $clockInDeviceDetail;
            $attendance->clock_in_latitude = $clockInLatitude;
            $attendance->clock_in_longitude = $clockInLongitude;
            $attendance->clock_in_is_inside_office_radius = $clockInIsInsideOfficeRadius;
            $attendance->clock_in_office_latitude = $clockInOfficeLatitude;
            $attendance->clock_in_office_longitude = $clockInOfficeLongitude;
            $attendance->clock_in_working_pattern_time = $workingPatternDay->clock_in ?? null;
            $attendance->status = $status;
            $attendance->time_late = $timeLate;
            $attendance->clock_in_note = $note;
            $attendance->clock_in_attachment = $urlPath;
            $attendance->working_pattern_id = $workingPatternId;
            
            // Set permission category jika ada
            if (!empty($permissionCategoryId)) {
                $attendance->is_permission = 1;
                $attendance->permission_category_id = $permissionCategoryId;
                $attendance->permission_status = "pending";
            }
            
            $attendance->save();

            if ($employee->office_id) {
                $outletOpening = OutletOpening::where('office_id', $employee->office_id)
                    ->where('date', $attendanceDate)
                    ->where('approval_status', 'approved')
                    ->where('timeliness_status', 'late')
                    ->first();

                if ($outletOpening) {
                    $attendance->outlet_opening_late = $outletOpening->late_amount;
                    $attendance->save();
                }
            }

            $quotes = $this->getRandomQuotes();

            return response()->json([
                'message' => 'success',
                'quotes' => $quotes,
                'data' => $attendance,
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    private function getRandomQuotes()
    {
        $quotes = AttendanceQuote::all();

        if (count($quotes) < 1) {
            $quotes = [
                "Pagi adalah waktu untuk memulai kembali dengan penuh semangat.",
                "Jangan biarkan hari ini berlalu tanpa senyum dan usaha terbaikmu.",
                "Setiap pagi adalah peluang baru untuk menjadi versi terbaik dari dirimu.",
                "Awali hari dengan pikiran positif dan energi yang baik.",
                "Hari ini adalah hadiah, nikmati setiap momennya.",
                "Lakukan sesuatu yang hebat hari ini.",
                "Keberhasilan dimulai dari langkah kecil di pagi hari.",
                "Biarkan sinar matahari pagi membangunkan semangatmu.",
                "Hari baru, peluang baru.",
                "Pagi ini adalah saat yang tepat untuk memulai petualangan baru.",
                "Jangan takut untuk bermimpi besar hari ini.",
                "Setiap hari adalah kesempatan untuk belajar dan tumbuh.",
                "Mulailah hari dengan hati yang penuh rasa syukur.",
                "Jadikan setiap hari luar biasa dengan tekad dan usaha.",
                "Sambut pagi dengan senyuman dan semangat baru.",
                "Berikan yang terbaik hari ini dan lihat keajaiban terjadi.",
                "Hari ini adalah milikmu, manfaatkan sebaik-baiknya.",
                "Kesempatan terbaik datang di pagi hari, jangan lewatkan.",
                "Jadilah inspirasi bagi dirimu sendiri setiap pagi.",
                "Bangun pagi dengan tekad untuk membuat perubahan.",
                "Setiap pagi adalah halaman kosong, tulis kisah yang indah.",
                "Mulailah hari dengan niat baik dan hati yang terbuka.",
                "Bersyukur atas hari baru dan semua peluang yang datang.",
                "Hidup adalah tentang membuat hari ini lebih baik dari kemarin.",
                "Jadilah pahlawan dalam kisah hidupmu sendiri setiap pagi.",
                "Pagi adalah waktu untuk merencanakan kesuksesanmu.",
                "Jangan menunggu kesempatan, ciptakan kesempatanmu sendiri hari ini.",
                "Mulailah hari dengan keyakinan dan keberanian.",
                "Berani bermimpi dan berani bertindak, mulai dari pagi ini.",
                "Setiap hari adalah anugerah, buatlah itu berarti."
            ];
        }

        $min = 0;
        $max = count($quotes) - 1;
        $index = rand($min, $max);

        return $quotes[$index]->quotes ?? 'A Quotes..';
    }

    /**
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockOut(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|integer',
                'date' => 'required|date',
                'clock_out_time' => 'required',
                'clock_out_at' => 'required',
                'attachment' => 'nullable',
                // 'is_long_shift' => [
                //     'required',
                //     Rule::in(['true', 'false'])
                // ],
                // 'long_shift_working_pattern_id' => 'nullable|integer',
            ]);

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            // $clockOutTime = $request->clock_out_time;
            // $clockOutAt = $request->clock_out_at;
            $clockOutTime = date('H:i:s');
            $clockOutAt = date('Y-m-d H:i:s');
            $clockOutIpAddress = $request->clock_out_ip_address;
            $clockOutDeviceDetail = $request->clock_out_device_detail;
            $clockOutLatitude = $request->clock_out_latitude;
            $clockOutLongitude = $request->clock_out_longitude;
            $clockOutOfficeLatitude = $request->clock_out_office_latitude;
            $clockOutOfficeLongitude = $request->clock_out_office_longitude;
            $note = $request->note;
            $isLongShift = false;

            if (strtolower($request->is_long_shift) == "true") {
                $isLongShift = true;
            }

            // return response()->json([
            //     'message' => 'is_long_shift: ' . $request->is_long_shift . ', type: ' . gettype($request->is_long_shift) . ', id: ' . $request->long_shift_working_pattern_id . ', $isLongShift: ' . ($isLongShift == true ? "true" : "false"),
            //     'type' => gettype($request->is_long_shift),
            // ]);
            // return response()->json([
            //     'message' => $isLongShift,
            // ]);

            $longShiftWorkingPatternId = null;

            if (strtolower($request->long_shift_working_pattern_id) !== "null" && isset($request->long_shift_working_pattern_id)) {
                $longShiftWorkingPatternId = $request->long_shift_working_pattern_id;
            }

            $clockOutExist = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_out_time', null)
                ->first();

            if ($clockOutExist !== null) {
                throw new Exception('Anda sudah clock out hari ini');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->where('clock_out_time', null)
                ->whereNot('clock_in_time', null)
                ->first();

            // return 'asdasd';
            // return response()->json([
            //     'message' => $todayAttendance,
            // ]);

            if ($todayAttendance == null) {
                throw new Exception('Anda belum clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock out');
            }

            $calendarHolidays = [];
            $earlyLeaving = 0;
            $overtime = 0;

            $workingPatternId = $todayAttendance->working_pattern_id;
            $workingPatternDay = null;
            if (isset($workingPatternId) && $workingPatternId !== "") {
                if ($workingPatternId == null || !isset($todayAttendance->working_pattern_id)) {
                    throw new Exception('Pola kerja clock in kosong');
                }

                if ($isLongShift) {
                    if ($longShiftWorkingPatternId == null) {
                        throw new Exception('Clock out long shift membutuhkan properti "long_shift_working_pattern_id"');
                    }

                    $workingPatternId = $longShiftWorkingPatternId;
                }

                $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                if ($activeWorkingPattern == null) {
                    throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                }

                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                $workingPatternDay = $workingPatternDays[0] ?? null;


                if ($workingPatternDay->clock_out !== null) {
                    // function below will return negative value
                    // $diffMinutes = Carbon::parse($attendanceDate . ' ' . $clockOutTime)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_out, false);
                    // function below will return positive value
                    $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_out)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                    if (count($calendarHolidays) < 1) {
                        $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                    }
                    $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                }
            }

            // Photo
            $filePath = null;
            $urlPath = null;
            if (isset($request->attachment)) {
                // $file = $request->file('attachment');
                $base64File = $request->attachment;

                // Compress Image
                // $imageWidth = Image::make($file->getRealPath())->width();
                // $ratio = 50 / 100;
                // $newWidth = floor($imageWidth * $ratio);
                // $resizedImage = Image::make($file->getRealPath())->resize($newWidth, null, function ($constraint) {
                //     $constraint->aspectRatio();
                // })->stream();
                $imageWidth = Image::make($base64File)->width();
                $ratio = 20 / 100;
                $newWidth = floor($imageWidth * $ratio);
                $resizedImage = Image::make($base64File)->resize($newWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream();

                // Uploading Image
                $name = time() . '-clockout-' . implode('-', explode(' ', $employee->name));
                $filePath = 'attendances/attachments/' . $name . '.jpg';
                $path = Storage::disk('s3')->put($filePath, $resizedImage, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'early_leaving' => $earlyLeaving,
            //     'overtime' => $overtime,
            // ]);

            $attendance = Attendance::find($todayAttendance->id);
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_out_time = $clockOutTime;
            $attendance->clock_out_at = $clockOutAt;
            $attendance->clock_out_ip_address = $clockOutIpAddress;
            $attendance->clock_out_device_detail = $clockOutDeviceDetail;
            $attendance->clock_out_latitude = $clockOutLatitude;
            $attendance->clock_out_longitude = $clockOutLongitude;
            $attendance->clock_out_office_latitude = $clockOutOfficeLatitude;
            $attendance->clock_out_office_longitude = $clockOutOfficeLongitude;
            $attendance->clock_out_working_pattern_time = $workingPatternDay->clock_out ?? null;
            $attendance->clock_out_note = $note;
            $attendance->clock_out_attachment = $urlPath;
            $attendance->early_leaving = $earlyLeaving;
            $attendance->overtime = $overtime;
            $attendance->is_long_shift = $isLongShift;
            if ($isLongShift) {
                $attendance->long_shift_status = 'pending';
                $attendance->long_shift_working_pattern_id = $longShiftWorkingPatternId;
                $attendance->long_shift_working_pattern_clock_in_time = $workingPatternDay->clock_in ?? null;
                $attendance->long_shift_working_pattern_clock_out_time = $workingPatternDay->clock_out ?? null;
            }
            $attendance->save();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'early_leaving' => $earlyLeaving,
                //     'overtime' => $overtime,
                // ],
                'data' => $attendance,
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
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockOutV2(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required',
                'date' => 'required|date',
                'clock_out_time' => 'required',
                'clock_out_at' => 'required',
                'attachment' => 'nullable',
                // 'is_long_shift' => [
                //     'required',
                //     Rule::in(['true', 'false'])
                // ],
                // 'long_shift_working_pattern_id' => 'nullable|integer',
            ]);

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            // $clockOutTime = $request->clock_out_time;
            // $clockOutAt = $request->clock_out_at;
            $clockOutTime = date('H:i:s');
            $clockOutAt = date('Y-m-d H:i:s');
            $clockOutIpAddress = $request->clock_out_ip_address;
            $clockOutDeviceDetail = $request->clock_out_device_detail;
            $clockOutLatitude = $request->clock_out_latitude;
            $clockOutLongitude = $request->clock_out_longitude;
            $clockOutIsInsideOfficeRadius = $request->clock_out_is_inside_office_radius;
            $clockOutOfficeLatitude = $request->clock_out_office_latitude;
            $clockOutOfficeLongitude = $request->clock_out_office_longitude;
            $note = $request->clock_out_note;
            $isLongShift = false;

            if (strtolower($request->is_long_shift) == "true") {
                $isLongShift = true;
            }

            // return response()->json([
            //     'message' => 'is_long_shift: ' . $request->is_long_shift . ', type: ' . gettype($request->is_long_shift) . ', id: ' . $request->long_shift_working_pattern_id . ', $isLongShift: ' . ($isLongShift == true ? "true" : "false"),
            //     'type' => gettype($request->is_long_shift),
            // ]);
            // return response()->json([
            //     'message' => $isLongShift,
            // ]);

            $longShiftWorkingPatternId = null;

            if (strtolower($request->long_shift_working_pattern_id) !== "null" && isset($request->long_shift_working_pattern_id)) {
                $longShiftWorkingPatternId = $request->long_shift_working_pattern_id;
            }

            $clockOutExist = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_out_time', null)
                ->first();

            if ($clockOutExist !== null) {
                throw new Exception('Anda sudah clock out hari ini');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->where('clock_out_time', null)
                ->whereNot('clock_in_time', null)
                ->first();

            // return 'asdasd';
            // return response()->json([
            //     'message' => $todayAttendance,
            // ]);

            if ($todayAttendance == null) {
                throw new Exception('Anda belum clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock out');
            }

            $todayWorkSchedule = WorkScheduleItem::with(['workScheduleWorkingPattern'])->where('employee_id', $employeeId)->where('date', $attendanceDate)->first();

            $calendarHolidays = [];
            $earlyLeaving = 0;
            $overtime = 0;

            if ($todayWorkSchedule != null) {
                $isOff = $todayWorkSchedule->is_off;
                if ($isOff == 1) {
                    throw new Error('Gagal absen, jadwal kamu OFF hari ini');
                }

                $workingPattern = $todayWorkSchedule->workScheduleWorkingPattern;

                if ($workingPattern != null) {
                    if ($workingPattern->have_overtime == 1) {
                        if ($workingPattern->overtime_start_time !== null) {
                            $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPattern->overtime_start_time)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                            if (count($calendarHolidays) < 1) {
                                $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                            }
                            $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                        }
                    }
                }
            } else {
                $workingPatternId = $todayAttendance->working_pattern_id;
                $workingPatternDay = null;
                if (isset($workingPatternId) && $workingPatternId !== "") {
                    if ($workingPatternId == null || !isset($todayAttendance->working_pattern_id)) {
                        throw new Exception('Pola kerja clock in kosong');
                    }

                    if ($isLongShift) {
                        // if ($longShiftWorkingPatternId == null) {
                        //     throw new Exception('Clock out long shift membutuhkan properti "long_shift_working_pattern_id"');
                        // }

                        // $workingPatternId = $longShiftWorkingPatternId;
                        $workingPatternId = 6;
                    }

                    $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                    if ($activeWorkingPattern == null) {
                        throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                    }

                    $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                    $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                    $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                        return $item->order == $dayOrder;
                    })->values()->all();

                    if (count($workingPatternDays) < 1) {
                        throw new Exception('"working pattern day" tidak ditemukan');
                    }

                    $workingPatternDay = $workingPatternDays[0] ?? null;


                    // if ($workingPatternDay->clock_out !== null) {
                    //     // function below will return negative value
                    //     // $diffMinutes = Carbon::parse($attendanceDate . ' ' . $clockOutTime)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_out, false);
                    //     // function below will return positive value
                    //     $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_out)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                    //     if (count($calendarHolidays) < 1) {
                    //         $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                    //     }
                    //     $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                    // }
                    if ($workingPatternDay->have_overtime == 1) {
                        if ($workingPatternDay->overtime_start_time !== null) {
                            $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->overtime_start_time)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                            if (count($calendarHolidays) < 1) {
                                $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                            }
                            $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                        }
                    }
                }
            }

            // Photo
            $filePath = null;
            $urlPath = null;
            if ($request->hasFile('clock_out_attachment')) {
                $file = $request->file('clock_out_attachment');

                $name = time() . '-clockout-' . implode('-', explode(' ', $employee->name));
                $filePath = 'attendances/attachments/' . $name . $file->getExtension();
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
                // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'early_leaving' => $earlyLeaving,
            //     'overtime' => $overtime,
            // ]);

            $attendance = Attendance::find($todayAttendance->id);
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_out_time = $clockOutTime;
            $attendance->clock_out_at = $clockOutAt;
            $attendance->clock_out_ip_address = $clockOutIpAddress;
            $attendance->clock_out_device_detail = $clockOutDeviceDetail;
            $attendance->clock_out_latitude = $clockOutLatitude;
            $attendance->clock_out_longitude = $clockOutLongitude;
            $attendance->clock_out_is_inside_office_radius = $clockOutIsInsideOfficeRadius;
            $attendance->clock_out_office_latitude = $clockOutOfficeLatitude;
            $attendance->clock_out_office_longitude = $clockOutOfficeLongitude;
            $attendance->clock_out_working_pattern_time = $workingPatternDay->clock_out ?? null;
            $attendance->clock_out_note = $note;
            $attendance->clock_out_attachment = $urlPath;
            $attendance->early_leaving = $earlyLeaving;
            $attendance->overtime = $overtime;
            $attendance->overtime_approval_status = 'pending';
            $attendance->is_long_shift = $isLongShift;
            if ($isLongShift) {
                $attendance->long_shift_status = 'pending';
                $attendance->long_shift_working_pattern_id = $longShiftWorkingPatternId;
                $attendance->long_shift_working_pattern_clock_in_time = $workingPatternDay->clock_in ?? null;
                $attendance->long_shift_working_pattern_clock_out_time = $workingPatternDay->clock_out ?? null;
            }
            $attendance->save();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'early_leaving' => $earlyLeaving,
                //     'overtime' => $overtime,
                // ],
                'data' => $attendance,
                'code' => 200,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    /**
     * Do clock in attendance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function clockOutV3(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required',
                'date' => 'required|date',
                'clock_out_time' => 'required',
                'clock_out_at' => 'required',
                'attachment' => 'nullable',
                // 'is_long_shift' => [
                //     'required',
                //     Rule::in(['true', 'false'])
                // ],
                // 'long_shift_working_pattern_id' => 'nullable|integer',
            ]);

            $employeeId = $request->employee_id;
            $attendanceDate = $request->date;
            // $clockOutTime = $request->clock_out_time;
            // $clockOutAt = $request->clock_out_at;
            $clockOutTime = date('H:i:s');
            $clockOutAt = date('Y-m-d H:i:s');
            $clockOutIpAddress = $request->clock_out_ip_address;
            $clockOutDeviceDetail = $request->clock_out_device_detail;
            $clockOutLatitude = $request->clock_out_latitude;
            $clockOutLongitude = $request->clock_out_longitude;
            $clockOutIsInsideOfficeRadius = $request->clock_out_is_inside_office_radius;
            $clockOutOfficeLatitude = $request->clock_out_office_latitude;
            $clockOutOfficeLongitude = $request->clock_out_office_longitude;
            $note = $request->clock_out_note;
            $isLongShift = false;

            if (strtolower($request->is_long_shift) == "true") {
                $isLongShift = true;
            }

            // return response()->json([
            //     'message' => 'is_long_shift: ' . $request->is_long_shift . ', type: ' . gettype($request->is_long_shift) . ', id: ' . $request->long_shift_working_pattern_id . ', $isLongShift: ' . ($isLongShift == true ? "true" : "false"),
            //     'type' => gettype($request->is_long_shift),
            // ]);
            // return response()->json([
            //     'message' => $isLongShift,
            // ]);

            $longShiftWorkingPatternId = null;

            if (strtolower($request->long_shift_working_pattern_id) !== "null" && isset($request->long_shift_working_pattern_id)) {
                $longShiftWorkingPatternId = $request->long_shift_working_pattern_id;
            }

            $clockOutExist = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->whereNot('clock_out_time', null)
                ->first();

            if ($clockOutExist !== null) {
                throw new Exception('Anda sudah clock out hari ini');
            }

            $todayAttendance = Attendance::query()
                ->where('employee_id', $employeeId)
                ->where('date', $attendanceDate)
                ->where('clock_out_time', null)
                ->whereNot('clock_in_time', null)
                ->first();

            // return 'asdasd';
            // return response()->json([
            //     'message' => $todayAttendance,
            // ]);

            if ($todayAttendance == null) {
                throw new Exception('Anda belum clock in hari ini');
            }

            $employee = Employee::find($employeeId);

            if ($employee == null) {
                throw new Exception('Pegawai dengan user id ' . $employeeId . ' tidak ditemukan');
            }

            if ($employee->active == 0) {
                throw new Exception('Pegawai nonaktif tidak dapat melakukan clock out');
            }

            $todayWorkSchedule = WorkScheduleItem::where('employee_id', $employeeId)->where('date', $attendanceDate)->first();

            if ($todayWorkSchedule != null) {
                $isOff = $todayWorkSchedule->is_off;
                if ($isOff == 1) {
                    throw new Error('Gagal absen, jadwal kamu OFF hari ini');
                }
            }

            $calendarHolidays = [];
            $earlyLeaving = 0;
            $overtime = 0;

            $workingPatternId = $todayAttendance->working_pattern_id;
            $workingPatternDay = null;
            if (isset($workingPatternId) && $workingPatternId !== "") {
                if ($workingPatternId == null || !isset($todayAttendance->working_pattern_id)) {
                    throw new Exception('Pola kerja clock in kosong');
                }

                if ($isLongShift) {
                    // if ($longShiftWorkingPatternId == null) {
                    //     throw new Exception('Clock out long shift membutuhkan properti "long_shift_working_pattern_id"');
                    // }

                    // $workingPatternId = $longShiftWorkingPatternId;
                    $workingPatternId = 6;
                }

                $activeWorkingPattern = WorkingPattern::with(['items'])->find($workingPatternId);

                if ($activeWorkingPattern == null) {
                    throw new Exception('Pola kerja dengan id ' . $workingPatternId . ' tidak ditemukan');
                }

                $attendanceDayOrder = Carbon::parse($attendanceDate)->dayOfWeek;
                $dayOrder = $attendanceDayOrder < 1 ? 7 : $attendanceDayOrder;

                $workingPatternDays = collect($activeWorkingPattern->items)->filter(function ($item) use ($dayOrder) {
                    return $item->order == $dayOrder;
                })->values()->all();

                if (count($workingPatternDays) < 1) {
                    throw new Exception('"working pattern day" tidak ditemukan');
                }

                $workingPatternDay = $workingPatternDays[0] ?? null;


                // if ($workingPatternDay->clock_out !== null) {
                //     // function below will return negative value
                //     // $diffMinutes = Carbon::parse($attendanceDate . ' ' . $clockOutTime)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_out, false);
                //     // function below will return positive value
                //     $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->clock_out)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                //     if (count($calendarHolidays) < 1) {
                //         $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                //     }
                //     $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                // }
                if ($workingPatternDay->have_overtime == 1) {
                    if ($workingPatternDay->overtime_start_time !== null) {
                        // function below will return negative value
                        // $diffMinutes = Carbon::parse($attendanceDate . ' ' . $clockOutTime)->diffInMinutes($attendanceDate . ' ' . $workingPatternDay->clock_out, false);
                        // function below will return positive value
                        $diffMinutes = Carbon::parse($attendanceDate . ' ' . $workingPatternDay->overtime_start_time)->diffInMinutes($attendanceDate . ' ' . $clockOutTime, false);
                        if (count($calendarHolidays) < 1) {
                            $earlyLeaving = $diffMinutes < 0 ? abs($diffMinutes) : 0;
                        }
                        $overtime = $diffMinutes > 0 ? $diffMinutes : 0;
                    }
                }
            }

            // Photo
            $filePath = null;
            $urlPath = null;
            // if ($request->hasFile('clock_out_attachment')) {
            //     $file = $request->file('clock_out_attachment');

            //     $name = time() . '-clockout-' . implode('-', explode(' ', $employee->name));
            //     $filePath = 'attendances/attachments/' . $name . $file->getExtension();
            //     $path = Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
            //     $urlPath = Storage::disk('s3')->url($filePath);
            //     // $path = $file->storePubliclyAs('employees/photos', $name, 's3');
            // }
            if (!empty($request->clock_out_attachment)) {
                $file = base64_decode($request->clock_out_attachment);
                // $file = $request->file('clock_in_attachment');

                $name = time() . '-clockout-' . implode('-', explode(' ', $employee->name));
                // $filePath = 'attendances/attachments/' . $name . $file->getExtension();
                $filePath = 'attendances/attachments/' . $name . '.png';
                $path = Storage::disk('s3')->put($filePath, $file, 'public');
                $urlPath = Storage::disk('s3')->url($filePath);
            }

            // return response()->json([
            //     'today_order' => $attendanceDayOrder,
            //     'day_order' => $dayOrder,
            //     'working_pattern_day' => $workingPatternDay,
            //     'early_leaving' => $earlyLeaving,
            //     'overtime' => $overtime,
            // ]);

            $attendance = Attendance::find($todayAttendance->id);
            $attendance->employee_id = $employeeId;
            $attendance->date = $attendanceDate;
            $attendance->clock_out_time = $clockOutTime;
            $attendance->clock_out_at = $clockOutAt;
            $attendance->clock_out_ip_address = $clockOutIpAddress;
            $attendance->clock_out_device_detail = $clockOutDeviceDetail;
            $attendance->clock_out_latitude = $clockOutLatitude;
            $attendance->clock_out_longitude = $clockOutLongitude;
            $attendance->clock_out_is_inside_office_radius = $clockOutIsInsideOfficeRadius;
            $attendance->clock_out_office_latitude = $clockOutOfficeLatitude;
            $attendance->clock_out_office_longitude = $clockOutOfficeLongitude;
            $attendance->clock_out_working_pattern_time = $workingPatternDay->clock_out ?? null;
            $attendance->clock_out_note = $note;
            $attendance->clock_out_attachment = $urlPath;
            $attendance->early_leaving = $earlyLeaving;
            $attendance->overtime = $overtime;
            $attendance->is_long_shift = $isLongShift;
            if ($isLongShift) {
                $attendance->long_shift_status = 'pending';
                $attendance->long_shift_working_pattern_id = $longShiftWorkingPatternId;
                $attendance->long_shift_working_pattern_clock_in_time = $workingPatternDay->clock_in ?? null;
                $attendance->long_shift_working_pattern_clock_out_time = $workingPatternDay->clock_out ?? null;
            }
            $attendance->save();

            return response()->json([
                'message' => 'success',
                // 'data' => [
                //     'today_order' => $attendanceDayOrder,
                //     'day_order' => $dayOrder,
                //     'working_pattern_day' => $workingPatternDay,
                //     'early_leaving' => $earlyLeaving,
                //     'overtime' => $overtime,
                // ],
                'data' => $attendance,
                'code' => 200,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    public function getLongShifts()
    {
        try {
            $status = request()->query('status');
            $employeeId = request()->query('employee_id');
            $supervisorId = request()->query('supervisor_id');
            $childOnly = request()->query('child_only');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            $longShiftsQuery = Attendance::with(['employee' => function ($q) {
                $q->with(['office']);
            }])->where('is_long_shift', 1);
            if (!empty($supervisorId) && !empty($childOnly) && $childOnly == 'true') {
                $supervisor = Employee::with(['credential'])->find($supervisorId);
                if (isset($supervisor)) {
                    $officesIds = json_decode($supervisor->credential->accessible_offices ?? "[]");
                    $longShiftsQuery->whereHas('employee', function ($q) use ($officesIds) {
                        $q->whereIn('office_id', $officesIds);
                    });
                    // $longShiftsQuery->where('employee_id', $employeeId);
                }
            }

            if (!empty($status)) {
                $longShiftsQuery->where('long_shift_status', $status);
            }

            if (!empty($employeeId)) {
                $longShiftsQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $longShiftsQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $longShifts = $longShiftsQuery->get();

            return response()->json([
                'message' => 'OK',
                'data' => $longShifts,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getLongShiftsV2()
    {
        try {
            $status = request()->query('status');
            $employeeId = request()->query('employee_id');
            $supervisorId = request()->query('supervisor_id');
            $childOnly = request()->query('child_only');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            $longShiftsQuery = Attendance::with(['employee' => function ($q) {
                $q->with(['office']);
            }])->where('is_long_shift', 1);
            if (!empty($supervisorId) && !empty($childOnly) && $childOnly == 'true') {
                $supervisor = Employee::with(['credential'])->find($supervisorId);
                if (isset($supervisor)) {
                    $officesIds = json_decode($supervisor->credential->accessible_offices ?? "[]");
                    $longShiftsQuery->whereHas('employee', function ($q) use ($officesIds) {
                        $q->whereIn('office_id', $officesIds);
                    });
                    // $longShiftsQuery->where('employee_id', $employeeId);
                }
            }

            if (!empty($status)) {
                $longShiftsQuery->where('long_shift_status', $status);
            }

            if (!empty($employeeId)) {
                $longShiftsQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $longShiftsQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $longShifts = $longShiftsQuery->simplePaginate(10)->withQueryString();

            return response()->json([
                'message' => 'OK',
                'data' => $longShifts,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getPendingLongShiftsCount()
    {
        try {
            $status = request()->query('status');
            $employeeId = request()->query('employee_id');
            $supervisorId = request()->query('supervisor_id');
            $childOnly = request()->query('child_only');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            $longShiftsQuery = Attendance::with(['employee' => function ($q) {
                $q->with(['office']);
            }])->where('is_long_shift', 1);
            if (!empty($supervisorId) && !empty($childOnly) && $childOnly == 'true') {
                $supervisor = Employee::with(['credential'])->find($supervisorId);
                if (isset($supervisor)) {
                    $officesIds = json_decode($supervisor->credential->accessible_offices ?? "[]");
                    $longShiftsQuery->whereHas('employee', function ($q) use ($officesIds) {
                        $q->whereIn('office_id', $officesIds);
                    });
                    // $longShiftsQuery->where('employee_id', $employeeId);
                }
            }

            if (!empty($status)) {
                $longShiftsQuery->where('long_shift_status', $status);
            }

            if (!empty($employeeId)) {
                $longShiftsQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $longShiftsQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $longShiftsCount = $longShiftsQuery->count();

            return response()->json([
                'message' => 'OK',
                'data' => $longShiftsCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function approveManyLongShift(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'long_shift_status' => 'approved',
                'long_shift_confirmed_by' => $confirmerId,
                'long_shift_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Long shift berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function rejectManyLongShift(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'long_shift_status' => 'rejected',
                'long_shift_confirmed_by' => $confirmerId,
                'long_shift_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Long shift berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function approveLongShift($id, Request $request)
    {
        try {
            Attendance::query()->where('id', $id)->update([
                'long_shift_status' => 'approved',
            ]);

            return response()->json([
                'message' => 'Long shift berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function rejectLongShift($id, Request $request)
    {
        try {
            Attendance::query()->where('id', $id)->update([
                'long_shift_status' => 'rejected',
            ]);

            return response()->json([
                'message' => 'Long shift berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    private function timeOffsReources($request)
    {
        $date = $request->query('date');
        $companyId = $request->query('company_id');

        if (isset($date)) {
            $date = date('Y-m-d', strtotime($date));
        }

        $sickApplications = SickApplication::whereHas('employee.office.division', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['employee'])->where('application_dates', 'LIKE', '%' . $date . '%')->where('approval_status', 'approved')->get()->each(function ($sickApplication) {
            // collect($sickApplication)->put('type', 'sick')->all();
            $sickApplication->type = 'sakit';
        });

        // return $sickApplications;

        $leaveApplications = LeaveApplication::whereHas('employee.office.division', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['category', 'employee'])->where('application_dates', 'LIKE', '%' . $date . '%')->where('approval_status', 'approved')->get()->each(function ($leaveApplication) {
            $leaveApplication->type = 'cuti';
        });

        $timeoffs = collect($sickApplications)->merge($leaveApplications)->all();

        return $timeoffs;
    }

    public function getTimeOffs()
    {
        try {
            $request = request();
            $timeoffs = $this->timeOffsReources($request);

            return response()->json([
                'message' => 'OK',
                'data' => $timeoffs,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getTimeOffsCount()
    {
        try {
            $request = request();
            $timeoffsCount = collect($this->timeOffsReources($request))->count();

            return response()->json([
                'message' => 'OK',
                'data' => $timeoffsCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Overtime
    public function getOvertimes()
    {
        // return [
        //     'asdsd' => 'asdasd',
        // ];
        try {
            $status = request()->query('status');
            $approverId = request()->query('approver_id');
            $employeeId = request()->query('employee_id');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');
            $officeId = request()->query('office_id');

            $overtimeQuery = Attendance::whereHas('employee', function ($q) use ($approverId, $officeId) {
                $q->where('overtime_approver_id', $approverId);
                if (!empty($officeId)) {
                    $q->where('office_id', $officeId);
                }
            })->with(['employee' => function ($q) {
                $q->with(['office']);
            }])
                ->where('overtime', '>', 0);

            if (!empty($status)) {
                $overtimeQuery->where('overtime_approval_status', $status);
            }

            if (!empty($employeeId)) {
                $overtimeQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $overtimeQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $overtimes = $overtimeQuery->simplePaginate(10)->withQueryString();

            return response()->json([
                'message' => 'OK',
                'data' => $overtimes,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getPendingOvertimesCount()
    {
        try {
            $status = request()->query('status');
            $approverId = request()->query('approver_id');
            $employeeId = request()->query('employee_id');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            // return $status;

            $overtimeQuery = Attendance::whereHas('employee', function ($q) use ($approverId) {
                $q->where('overtime_approver_id', $approverId);
            })
                ->with(['employee' => function ($q) {
                    $q->with(['office']);
                }])->where('overtime', '>', 0);

            if (!empty($status)) {
                $overtimeQuery->where('overtime_approval_status', $status);
            }

            if (!empty($employeeId)) {
                $overtimeQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $overtimeQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $overtimeCount = $overtimeQuery->count();

            return response()->json([
                'message' => 'OK',
                'data' => $overtimeCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function approveManyOvertimes(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'overtime_approval_status' => 'approved',
                'overtime_confirmed_by' => $confirmerId,
                'overtime_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pengajuan lembur berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function rejectManyOvertimes(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'overtime_approval_status' => 'rejected',
                'overtime_confirmed_by' => $confirmerId,
                'overtime_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Perngajuan lembur berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function approveOvertime($id, Request $request)
    {
        $confirmerId = $request->confirmer_id;

        try {
            Attendance::query()->where('id', $id)->update([
                'overtime_approval_status' => 'approved',
                'overtime_confirmed_by' => $confirmerId,
                'overtime_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pengajuan berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function rejectOvertime($id, Request $request)
    {
        $confirmerId = $request->confirmer_id;

        try {
            Attendance::query()->where('id', $id)->update([
                'overtime_approval_status' => 'rejected',
                'overtime_confirmed_by' => $confirmerId,
                'overtime_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pengajuan lembur berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // Permission
    public function getPermissions()
    {
        try {
            $status = request()->query('status');
            $approverId = request()->query('approver_id');
            $employeeId = request()->query('employee_id');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            $permissionQuery = Attendance::whereHas('employee', function ($q) use ($approverId) {
                $q->where('permission_approver_id', $approverId);
            })->with(['permissionCategory', 'employee' => function ($q) {
                $q->with(['office']);
            }])->where('is_permission', 1);

            if (!empty($status)) {
                $permissionQuery->where('permission_status', $status);
            }

            if (!empty($employeeId)) {
                $permissionQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $permissionQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $permissions = $permissionQuery->simplePaginate(10)->withQueryString();

            return response()->json([
                'message' => 'OK',
                'data' => $permissions,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function getPendingPermissionsCount()
    {
        try {
            $status = request()->query('status');
            $approverId = request()->query('approver_id');
            $employeeId = request()->query('employee_id');
            $startDate = request()->query('start_date');
            $endDate = request()->query('end_date');

            // return $status;

            $permissionQuery = Attendance::whereHas('employee', function ($q) use ($approverId) {
                $q->where('permission_approver_id', $approverId);
            })
                ->with(['employee' => function ($q) {
                    $q->with(['office']);
                }])->where('is_permission', 1);

            if (!empty($status)) {
                $permissionQuery->where('permission_status', $status);
            }

            if (!empty($employeeId)) {
                $permissionQuery->where('employee_id', $employeeId);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $permissionQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $permissionCount = $permissionQuery->count();

            return response()->json([
                'message' => 'OK',
                'data' => $permissionCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function approveManyPermission(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'status' => 'hadir',
                'time_late' => 0,
                'permission_status' => 'approved',
                'permission_confirmed_by' => $confirmerId,
                'permission_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pengajuan izin berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function rejectManyPermission(Request $request)
    {
        try {
            $attendanceIds = $request->attendance_ids ?? "[]";
            $confirmerId = $request->confirmer_id;
            $attendanceIds = json_decode($attendanceIds);
            Attendance::query()->whereIn('id', $attendanceIds)->update([
                'status' => 'hadir',
                'permission_status' => 'rejected',
                'permission_confirmed_by' => $confirmerId,
                'permission_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Perngajuan izin berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function approvePermission($id, Request $request)
    {
        $confirmerId = $request->confirmer_id;

        try {
            Attendance::query()->where('id', $id)->update([
                'permission_status' => 'approved',
                'permission_confirmed_by' => $confirmerId,
                'permission_confirmed_at' => Carbon::now()->toDateTimeString(),
                'time_late' => 0,
            ]);

            return response()->json([
                'message' => 'Pengajuan berhasil disetujui',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function rejectPermission($id, Request $request)
    {
        $confirmerId = $request->confirmer_id;

        try {
            Attendance::query()->where('id', $id)->update([
                'permission_status' => 'rejected',
                'permission_confirmed_by' => $confirmerId,
                'permission_confirmed_at' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Pengajuan izin berhasil ditolak',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }
    }
}
