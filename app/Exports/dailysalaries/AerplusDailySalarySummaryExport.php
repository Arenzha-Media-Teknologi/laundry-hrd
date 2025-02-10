<?php

namespace App\Exports\dailysalaries;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AerplusDailySalarySummaryExport implements FromView, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $startDate;
    protected $endDate;
    protected $dailySalaries;

    function __construct($startDate, $endDate, $dailySalaries)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dailySalaries = $dailySalaries;
    }

    public function view(): View
    {
        return view('payrolls.daily.exports.reports.aerplus-summary', [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'daily_salaries' => $this->dailySalaries,
        ]);
    }
}
