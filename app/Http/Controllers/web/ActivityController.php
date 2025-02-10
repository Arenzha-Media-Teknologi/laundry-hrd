<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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

    public function detail()
    {
        $employeeId = request()->query('employee_id');
        $date = request()->query('date');

        if (empty($employeeId) || empty($date)) {
            abort(404);
        }

        $employee = Employee::with(['activeCareer.jobTitle', 'office.division.company'])->find($employeeId);

        $attendance = Attendance::query()->where('employee_id', $employeeId)->where('date', $date)->orderBy('id', 'DESC')->first();

        $activities = Activity::with(['items'])->where('employee_id', $employeeId)->where('date', $date)->get();

        // return $checkIns;
        return view('activities.detail', [
            'date' => $date,
            'employee' => $employee,
            'activities' => $activities,
            'attendance' => $attendance,
        ]);
    }
}
