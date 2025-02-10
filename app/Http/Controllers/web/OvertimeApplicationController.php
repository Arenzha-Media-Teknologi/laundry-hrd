<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyBusinessType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OvertimeApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = request()->query('date') ?? date('Y-m-d');
        $companyId = request()->query('company_id');
        $divisionId = request()->query('division_id');
        $officeId = request()->query('office_id');
        $status = request()->query('status');

        $employeesWithAttendancesQuery = Employee::with(['attendances' => function ($q) use ($date) {
            $q->with(['leaveApplication.category'])->where('date', $date)->orderBy('id', 'DESC');
        }, 'activeCareer.jobTitle.designation', 'office' => function ($q) {
            $q->with(['division' => function ($q2) {
                $q2->with(['company']);
            }]);
        }])->where('magenta_daily_salary', 1);


        if (isset($companyId) && !empty($companyId)) {
            $employeesWithAttendancesQuery->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($divisionId) && !empty($divisionId)) {
            $employeesWithAttendancesQuery->whereHas('office.division', function ($q) use ($divisionId) {
                $q->where('id', $divisionId);
            });
        }

        if (isset($officeId) && !empty($officeId)) {
            $employeesWithAttendancesQuery->whereHas('office', function ($q) use ($officeId) {
                $q->where('id', $officeId);
            });
        }

        if (isset($status) && !empty($status)) {
            $employeesWithAttendancesQuery->where('active', $status);
        }

        $employeesWithAttendances = $employeesWithAttendancesQuery->get()
            ->filter(function ($employee) {
                $isFirstAttendanceHaveOvertime = false;
                $attendancesCount = count($employee->attendances);
                if ($attendancesCount > 0) {
                    $isFirstAttendanceHaveOvertime = $employee->attendances[0]->overtime > 0;
                }

                return $attendancesCount > 0 && $isFirstAttendanceHaveOvertime;
            })->each(function (Employee $employee) {
                $employee->new_overtime = $employee->attendances[0]->application_overtime ?? 0;
            })->values()->all();

        // return $employeesWithAttendances;
        return view('overtime-applications.index', [
            'date' => $date,
            'employees_with_attendances' => $employeesWithAttendances,
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
        DB::beginTransaction();
        try {
            $employees = $request->employees;
            collect($employees)->each(function ($employee) {
                DB::table('attendances')->where('id', $employee['attendance_id'])->update([
                    'application_overtime' => $employee['application_overtime'],
                ]);
            });

            DB::commit();
            return response()->json([
                'message' => 'Pengajuan lembur berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ]);
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
}
