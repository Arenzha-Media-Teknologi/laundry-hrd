<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Office;
use App\Models\OvertimeApplication;
use App\Models\OvertimeApplicationMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class OvertimeApplicationV2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $overtimeApplications = OvertimeApplication::with(['members.employee'])->get();
        $offices = Office::with(['division.company'])->get();

        return view('overtime-applications-v2.index', [
            'overtime_applications' => $overtimeApplications,
            'offices' => $offices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allEmployees = Employee::where('active', 1)->get();
        // $employees = Employee::whereRelation('activeCareer.jobTitle.designation.department', 'id', '=', 5)->where('active', 1)->get();
        $employees = Employee::whereRelation('office.division.company', 'id', '=', 1)->where('active', 1)->get();

        $number = $this->getNumber(date('Y-m-d'));

        return view('overtime-applications-v2.create', [
            'number' => $number,
            'employees' => $employees,
            'all_employees' => $allEmployees,
        ]);
    }

    /**
     * Get Number 
     */
    public function getNumber($date)
    {
        // $number = trim($request->number, '[COUNTER]') . sprintf('%03d', ($datePurchaseOrderCount + 1));
        // $customNumber = false;

        $overtimeApplicationsCount = OvertimeApplication::withTrashed()->where('date', $date)->count();
        $number = Carbon::parse($date)->format('ymd') . sprintf('%02d', $overtimeApplicationsCount + 1);
        return $number;
    }

    /**
     * 
     */
    public function number()
    {
        try {
            $date = request()->query('date');
            $number = $this->getNumber($date);
            return response()->json([
                'data' => $number,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
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
        DB::beginTransaction();
        try {
            $newOvertimeApplication = new OvertimeApplication();
            $newOvertimeApplication->number = $request->number;
            $newOvertimeApplication->date = $request->date;
            $newOvertimeApplication->type = $request->type;
            $newOvertimeApplication->title = $request->title;
            $newOvertimeApplication->job_order_number = $request->job_order_number;
            $newOvertimeApplication->order = $request->order;
            $newOvertimeApplication->delivery_date = $request->delivery_date;
            $newOvertimeApplication->customer = $request->customer;
            $newOvertimeApplication->order_quantity = $request->order_quantity;
            $newOvertimeApplication->difference_note = $request->difference_note;
            $newOvertimeApplication->status = "pending";
            $newOvertimeApplication->prepared_by = $request->prepared_by;
            $newOvertimeApplication->submitted_by = $request->submitted_by;
            $newOvertimeApplication->known_by = $request->known_by;
            $newOvertimeApplication->created_by = Auth::user()->employee->id ?? null;
            $newOvertimeApplication->save();

            $members = $request->members;
            foreach ($members as $member) {
                $newOvertimeApplicationMember = new OvertimeApplicationMember();
                $newOvertimeApplicationMember->type = $member['type'];
                $newOvertimeApplicationMember->description = $member['description'];
                $newOvertimeApplicationMember->clock_in = $member['clockIn'];
                $newOvertimeApplicationMember->clock_out = $member['clockOut'];
                $newOvertimeApplicationMember->overtime = $member['overtime'];
                $newOvertimeApplicationMember->employee_id = $member['employeeId'];
                $newOvertimeApplicationMember->overtime_application_id = $newOvertimeApplication->id;
                $newOvertimeApplicationMember->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
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
        $overtimeApplication = OvertimeApplication::with(['members.employee'])->findOrFail($id);

        $allEmployees = Employee::where('active', 1)->get();
        // $employees = Employee::whereRelation('activeCareer.jobTitle.designation.department', 'id', '=', 5)->where('active', 1)->get();
        $employees = Employee::whereRelation('office.division.company', 'id', '=', 1)->where('active', 1)->get();

        $number = $overtimeApplication->number;

        $memberPic = collect($overtimeApplication->members)->where('type', 'pic')->map(function ($member) {
            return [
                'employeeId' => $member->employee_id,
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                'type' => 'pic',
            ];
        })->values()->all();

        $members = collect($overtimeApplication->members)->where('type', 'member')->map(function ($member) {
            return [
                'employeeId' => $member->employee_id,
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                'type' => 'member',
            ];
        })->values()->all();

        return view('overtime-applications-v2.edit', [
            'overtime_application' => $overtimeApplication,
            'number' => $number,
            'employees' => $employees,
            'all_employees' => $allEmployees,
            'member_pic' => $memberPic,
            'members' => $members,
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
        DB::beginTransaction();
        try {
            $overtimeApplication = OvertimeApplication::find($id);
            $overtimeApplication->number = $request->number;
            $overtimeApplication->date = $request->date;
            $overtimeApplication->type = $request->type;
            $overtimeApplication->title = $request->title;
            $overtimeApplication->job_order_number = $request->job_order_number;
            $overtimeApplication->order = $request->order;
            $overtimeApplication->delivery_date = $request->delivery_date;
            $overtimeApplication->customer = $request->customer;
            $overtimeApplication->order_quantity = $request->order_quantity;
            $overtimeApplication->difference_note = $request->difference_note;
            $overtimeApplication->status = "pending";
            $overtimeApplication->prepared_by = $request->prepared_by;
            $overtimeApplication->submitted_by = $request->submitted_by;
            $overtimeApplication->known_by = $request->known_by;
            $overtimeApplication->created_by = Auth::user()->employee->id ?? null;
            $overtimeApplication->save();

            $overtimeApplication->members()->delete();

            $members = $request->members;
            foreach ($members as $member) {
                $overtimeApplicationMember = new OvertimeApplicationMember();
                $overtimeApplicationMember->type = $member['type'];
                $overtimeApplicationMember->description = $member['description'];
                $overtimeApplicationMember->clock_in = $member['clockIn'];
                $overtimeApplicationMember->clock_out = $member['clockOut'];
                $overtimeApplicationMember->overtime = $member['overtime'];
                $overtimeApplicationMember->employee_id = $member['employeeId'];
                $overtimeApplicationMember->overtime_application_id = $overtimeApplication->id;
                $overtimeApplicationMember->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
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
        //
    }


    /**
     * Confirm the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmation($id)
    {
        $overtimeApplication = OvertimeApplication::with(['members.employee.attendances'])->findOrFail($id);

        $allEmployees = Employee::where('active', 1)->get();
        $employees = Employee::whereRelation('activeCareer.jobTitle.designation.department', 'id', '=', 5)->where('active', 1)->get();

        $number = $overtimeApplication->number;

        $memberPic = collect($overtimeApplication->members)->where('type', 'pic')->map(function ($member) use ($overtimeApplication) {
            $attendance = collect($member->employee->attendances ?? [])->where('date', $overtimeApplication->date)->sortByDesc('id')->first();
            return [
                'employeeId' => $member->employee_id,
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                // 'systemClockIn' => $attendance->clock_in_time ?? '',
                'systemClockIn' => $member->clock_in,
                'systemClockOut' => $attendance->clock_out_time ?? '',
                'systemOvertime' => $attendance->overtime ?? 0,
                'attendanceId' => $attendance->id ?? null,
                'type' => 'pic',
            ];
        })->values()->all();

        // return $memberPic;

        $members = collect($overtimeApplication->members)->where('type', 'member')->map(function ($member) use ($overtimeApplication) {
            $attendance = collect($member->employee->attendances ?? [])->where('date', $overtimeApplication->date)->sortByDesc('id')->first();
            return [
                'employeeId' => $member->employee_id,
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                // 'systemClockIn' => $attendance->clock_in_time ?? '',
                'systemClockIn' => $member->clock_in,
                'systemClockOut' => $attendance->clock_out_time ?? '',
                'systemOvertime' => $attendance->overtime ?? 0,
                'attendanceId' => $attendance->id ?? null,
                'type' => 'member',
            ];
        })->values()->all();

        return view('overtime-applications-v2.confirm', [
            'overtime_application' => $overtimeApplication,
            'number' => $number,
            'employees' => $employees,
            'all_employees' => $allEmployees,
            'member_pic' => $memberPic,
            'members' => $members,
        ]);
    }

    /**
     * Do Confirm
     */
    public function confirm(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $overtimeApplication = OvertimeApplication::find($id);
            $overtimeApplication->status = "confirmed";
            $overtimeApplication->difference_note = $request->difference_note;
            $overtimeApplication->prepared_by = $request->prepared_by;
            $overtimeApplication->submitted_by = $request->submitted_by;
            $overtimeApplication->known_by = $request->known_by;
            $overtimeApplication->confirmed_by = Auth::user()->employee->id ?? null;
            $overtimeApplication->save();

            $members = $request->members;
            foreach ($members as $member) {
                $attendanceId = $member['attendanceId'];
                if (isset($member['attendanceId']) && !empty($member['attendanceId'])) {
                    $attendance = Attendance::find($attendanceId);
                    // $attendance->clock_in_time = $member['systemClockIn'];
                    $attendance->clock_out_time = $member['systemClockOut'];
                    $attendance->overtime = $member['systemOvertime'];
                    $attendance->application_overtime = $member['systemOvertime'];
                    $attendance->save();
                } else {
                    $newAttendance = new Attendance();
                    $newAttendance->employee_id = $member['employeeId'];
                    $newAttendance->date = $overtimeApplication->date;
                    // Clock in
                    $newAttendance->clock_in_time = $member['systemClockIn'];
                    $newAttendance->clock_in_at = Carbon::parse($overtimeApplication->date . ' ' . $member['systemClockIn'])->toDateString();
                    $newAttendance->clock_in_working_pattern_time = null;
                    // --
                    // Clock out
                    $newAttendance->clock_out_time = $member['systemClockOut'];
                    $newAttendance->clock_out_at = Carbon::parse($overtimeApplication->date . ' ' . $member['systemClockOut'])->toDateString();
                    $newAttendance->clock_out_working_pattern_time = null;
                    // --
                    $newAttendance->status = 'hadir';
                    $newAttendance->time_late = 0;
                    $newAttendance->overtime = $member['systemOvertime'];
                    $newAttendance->application_overtime = $member['systemOvertime'];
                    $newAttendance->working_pattern_id = null;
                    $newAttendance->created_by = Auth::user()->employee->id ?? null;
                    $newAttendance->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Perubahan berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Print
     */
    public function print($id)
    {
        $overtimeApplication = OvertimeApplication::with(['members.employee', 'preparedByEmployee', 'submittedByEmployee', 'knownByEmployee'])->findOrFail($id);

        $memberPic = collect($overtimeApplication->members)->where('type', 'pic')->map(function ($member) use ($overtimeApplication) {
            $attendance = collect($member->employee->attendances ?? [])->where('date', $overtimeApplication->date)->sortByDesc('id')->first();
            return [
                'employee' => $member->employee->name ?? '',
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                'systemClockIn' => $attendance->clock_in_time ?? '',
                'systemClockOut' => $attendance->clock_out_time ?? '',
                'systemOvertime' => $attendance->application_overtime ?? 0,
                'attendanceId' => $attendance->id ?? null,
                'type' => 'pic',
            ];
        })->values()->all();

        // return $memberPic;

        $members = collect($overtimeApplication->members)->where('type', 'member')->map(function ($member) use ($overtimeApplication) {
            $attendance = collect($member->employee->attendances ?? [])->where('date', $overtimeApplication->date)->sortByDesc('id')->first();
            return [
                'employee' => $member->employee->name ?? '',
                'description' => $member->description,
                'clockIn' => $member->clock_in,
                'clockOut' => $member->clock_out,
                'overtime' => $member->overtime,
                'systemClockIn' => $attendance->clock_in_time ?? '',
                'systemClockOut' => $attendance->clock_out_time ?? '',
                'systemOvertime' => $attendance->application_overtime ?? 0,
                'attendanceId' => $attendance->id ?? null,
                'type' => 'member',
            ];
        })->values()->all();

        $data = [
            'overtime_application' => $overtimeApplication,
            'member_pic' => $memberPic,
            'members' => $members,
        ];

        $pdf = PDF::loadView('overtime-applications-v2.print', $data, [], [
            'format' => 'A4',
            'orientation' => 'L',
        ]);
        return $pdf->stream();
    }
}
