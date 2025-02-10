<?php

namespace App\Exports\dailysalaries;

use App\Exports\dailysalaries\Sheets\MagentaDailySalarySheet;
use App\Models\Company;
use App\Models\DailySalary;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MagentaDailySalaryExport implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $month;
    protected $year;
    protected $setting;

    public function __construct($month, $year, $setting)
    {
        $this->month = $month;
        $this->year = $year;
        $this->setting = $setting;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $companies = Company::all()->push(null)->all();

        // $companyInitials = ['MM', 'UL', 'SRC', 'BIS', 'EO', 'OELLO'];

        $dailySalaries = DailySalary::with(['employee' => function ($employeeQuery) {
            $employeeQuery->with([
                'activeCareer.jobTitle',
                'office.division.company',
                'bankAccounts',
            ]);
        }])
            // ->whereHas('employee', function ($q) {
            //     $q->where('employee_id', 'like', $this->initial . '%');
            // })
            ->where('type', 'regular')
            ->whereMonth('end_date', $this->month)
            ->whereYear('end_date', $this->year)
            ->get()
            // ->each(function ($payslip) {
            //     $payslip['period'] = $payslip->start_date_period . ' - ' . $payslip->end_date_period;
            //     $payslip['income'] = json_decode($payslip->income);

            //     $filteredIncome = collect($payslip->income)->filter(function ($income) {
            //         return $income->attendance !== null;
            //     })->all();

            //     $payslip['total_daily_money'] = collect($filteredIncome)->map(function ($income) {
            //         return $income->attendance->daily_money;
            //     })->sum();

            //     $payslip['total_overtime_pay'] = collect($filteredIncome)->map(function ($income) {
            //         return $income->attendance->overtime_pay;
            //     })->sum();

            //     $payslip['amount'] = $payslip['total_daily_money'] + $payslip['total_overtime_pay'];
            // })
            ->map(function ($payslip) {
                $period = $payslip->start_date . ' - ' . $payslip->end_date;
                $incomes = json_decode($payslip->incomes);
                $deductions = json_decode($payslip->deductions, true);

                $totalDailyMoney = collect($incomes)->sum('daily_wage');
                $totalOvertimePay = collect($incomes)->sum('overtime_pay');
                $totalLateFee = collect($deductions)->filter(function ($deduction) {
                    $type = $deduction['type'] ?? '';
                    return $type != 'deposit';
                })->sum('value');
                $amount = ($totalDailyMoney + $totalOvertimePay) - ($totalLateFee);

                $payslip->period = $period;
                $payslip->total_daily_money = $totalDailyMoney;
                $payslip->total_overtime_pay = $totalOvertimePay;
                $payslip->total_late_fee = $totalLateFee;
                $payslip->amount = $amount;

                return $payslip;
            });

        foreach ($companies as $company) {
            $sheetCompanyId = null;
            if (isset($company->id)) {
                $sheetCompanyId = $company->id;
            }

            $dailySalariesByCompany = $dailySalaries
                ->filter(
                    function ($dailySalary) use ($sheetCompanyId) {
                        $employeeCompanyId = null;
                        $employee = $dailySalary->employee;
                        if (isset($employee?->office?->division?->company?->id)) {
                            $employeeCompanyId = $employee?->office?->division?->company?->id;
                        }

                        return $employeeCompanyId == $sheetCompanyId;
                    }
                )->groupBy('period');

            $finalDailySalariesByCompany = $dailySalariesByCompany->all();

            // $companyTotal = collect($employeesByCompany)->sum('total');

            // array_push($summariesByCompany, [
            //     'company' => $company,
            //     'total' => $companyTotal,
            // ]);

            $sheets[] = new MagentaDailySalarySheet($company, $finalDailySalariesByCompany, $this->setting);
        }

        return $sheets;
    }
}
