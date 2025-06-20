<?php

namespace App\Exports\workschedules;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkScheduleByOfficeExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $offices;
    protected $attendances;
    protected $workScheduleWorkingPatterns;
    protected $startDate;
    protected $endDate;

    function __construct($offices, $attendances, $workScheduleWorkingPatterns, $startDate, $endDate)
    {
        $this->offices = $offices;
        $this->attendances = $attendances;
        $this->workScheduleWorkingPatterns = $workScheduleWorkingPatterns;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $offices = $this->offices;
        $attendances = $this->attendances;
        $workScheduleWorkingPatterns = $this->workScheduleWorkingPatterns;
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        return view('work-schedule.export.export-by-office', [
            'offices' => $offices,
            'attendances' => $attendances,
            'work_schedule_working_patterns' => $workScheduleWorkingPatterns,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
