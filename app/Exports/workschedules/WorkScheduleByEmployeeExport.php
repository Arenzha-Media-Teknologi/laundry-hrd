<?php

namespace App\Exports\workschedules;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkScheduleByEmployeeExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $employees;
    protected $periods;
    protected $startDate;
    protected $endDate;

    function __construct($employees, $periods, $startDate, $endDate)
    {
        $this->employees = $employees;
        $this->periods = $periods;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $employees = $this->employees;
        $periods = $this->periods;
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        return view('work-schedule.export.export-by-employee', [
            'employees' => $employees,
            'periods' => $periods,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
