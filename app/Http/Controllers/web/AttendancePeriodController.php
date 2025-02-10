<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\WorkingPattern;
use Carbon\Carbon;

class AttendancePeriodController extends Controller
{
    public function index(Request $request)
    {
        $employee_id = $request->employee_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if ($employee_id) {
            $data['employee'] = Employee::find($employee_id);

            $data['attendances'] = Attendance::where('employee_id', $employee_id)
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->orderBy('date', 'asc')
                ->paginate(10);

        }
        $attendanceDayOrder = Carbon::now()->dayOfWeek;
        $data['working_patterns'] = WorkingPattern::with([
            'items' => function ($q) use ($attendanceDayOrder) {
                $q->where('order', $attendanceDayOrder);
            }
        ])->get();
        // dd($data['attendances']);
        $data['employees'] = Employee::all();
        return view('reports.attendance_period.index', $data);
    }
}
