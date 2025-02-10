<?php

namespace App\Exports\reports\insurances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PrivateInsurancesExport implements FromView, ShouldAutoSize
{
    protected $divisions;
    protected $year;
    protected $company;
    protected $grandTotals;
    protected $privateInsurance;

    public function __construct($divisions, $year, $company, $grandTotals, $privateInsurance)
    {
        $this->divisions = $divisions;
        $this->year = $year;
        $this->company = $company;
        $this->grandTotals = $grandTotals;
        $this->privateInsurance = $privateInsurance;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $divisions = $this->divisions;
        $year = $this->year;
        $company = $this->company;
        $grandTotals = $this->grandTotals;
        $privateInsurance = $this->privateInsurance;

        return view('exports.xlsx.reports.insurances.v2.privates', [
            'divisions' => $divisions,
            'year' => $year,
            'company' => $company,
            'grand_totals' => $grandTotals,
            'private_insurance' => $privateInsurance,
        ]);
    }
}
