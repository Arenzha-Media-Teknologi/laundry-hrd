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

class LeavePayrollExport implements FromView, ShouldAutoSize
{
    protected $employees;
    protected $year;
    protected $company;

    public function __construct($employees, $year, $company)
    {
        $this->employees = $employees;
        $this->year = $year;
        $this->company = $company;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $employees = $this->employees;
        $year = $this->year;
        $company = $this->company;

        $companies = Company::all()->mapWithKeys(function ($company) {
            return [
                $company->id => $company->name
            ];
        });

        return view('exports.xlsx.reports.salaries.leave', [
            'employees' => $employees,
            'year' => $year,
            'company' => $company,
            'companies' => $companies,
        ]);
    }
}
