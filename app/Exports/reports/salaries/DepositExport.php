<?php

namespace App\Exports\reports\salaries;

use App\Models\Company;
use App\Models\Division;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepositExport implements FromView, ShouldAutoSize
{
    protected $deposits;
    protected $startDate;
    protected $endDate;
    protected $company;

    public function __construct($deposits, $startDate, $endDate, $company)
    {
        $this->deposits = $deposits;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->company = $company;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $deposits = $this->deposits;
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $company = $this->company;

        return view('exports.xlsx.reports.salaries.deposit', [
            'deposits' => $deposits,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company' => $company,
        ]);
    }
}
