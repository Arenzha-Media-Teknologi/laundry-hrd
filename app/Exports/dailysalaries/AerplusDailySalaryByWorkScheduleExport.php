<?php

namespace App\Exports\dailysalaries;

use App\Exports\dailysalaries\Sheets\AerplusDailySalaryByWorkScheduleSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AerplusDailySalaryByWorkScheduleExport implements WithMultipleSheets, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $startDate;
    protected $endDate;
    protected $dailySalariesByOffice;

    function __construct($startDate, $endDate, $dailySalariesByOffice)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dailySalariesByOffice = $dailySalariesByOffice;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets[] = new AerplusDailySalaryByWorkScheduleSheet($this->startDate, $this->endDate, $this->dailySalariesByOffice);

        return $sheets;
    }
}

