<?php

namespace App\Exports\payrollbca;

use App\Exports\payrollbca\sheets\MultiPayrollTemplateSheet;
use App\Exports\salaries\Sheets\SalaryMonthlySheet;
use App\Exports\salaries\Sheets\SalaryMonthlySheetV2;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheet;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheetV2;
use App\Models\Company;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiPayrollTemplateExport implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $startDatePeriod;
    protected $endDatePeriod;
    protected $staffOnly;
    protected $haveStaffPermission;
    protected $companyId;
    protected $employees;

    public function __construct($startDatePeriod, $endDatePeriod, $staffOnly, $haveStaffPermission, $companyId, $employees)
    {
        $this->startDatePeriod = $startDatePeriod;
        $this->endDatePeriod = $endDatePeriod;
        $this->staffOnly = $staffOnly;
        $this->haveStaffPermission = $haveStaffPermission;
        $this->companyId = $companyId;
        $this->employees = $employees;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // $companyInitials = ['MM', 'UL', 'SRC', 'BIS', 'EO', 'OELLO'];
        $companies = Company::where('id', $this->companyId)->get()->all();

        $summariesByCompany = [];

        $employees = $this->employees;

        foreach ($companies as $company) {
            $sheetCompanyId = null;
            if (isset($company->id)) {
                $sheetCompanyId = $company->id;
            }

            $employeesByCompany = $employees
                ->filter(
                    function ($employee) use ($sheetCompanyId) {
                        $employeeCompanyId = null;
                        if (isset($employee?->office?->division?->company?->id)) {
                            $employeeCompanyId = $employee?->office?->division?->company?->id;
                        }

                        return $employeeCompanyId == $sheetCompanyId;
                    }
                );

            $finalEmployeesByCompany = $employeesByCompany->all();

            $companyTotal = collect($employeesByCompany)->sum('total');

            array_push($summariesByCompany, [
                'company' => $company,
                'total' => $companyTotal,
            ]);

            $sheets[] = new MultiPayrollTemplateSheet($company, $finalEmployeesByCompany, $this->startDatePeriod, $this->endDatePeriod);
        }

        // $sheets[] = new SummarySalaryMonthlySheetV2($summariesByCompany);

        return $sheets;
    }
}
