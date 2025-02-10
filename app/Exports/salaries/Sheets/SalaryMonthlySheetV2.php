<?php

namespace App\Exports\salaries\Sheets;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalaryMonthlySheetV2 implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $company;
    protected $employees;
    protected $presenceIncentiveOnly;
    // protected $startDatePeriod;
    // protected $endDatePeriod;
    // protected $staffOnly;

    public function __construct($company, $employees, $presenceIncentiveOnly)
    {
        $this->company = $company;
        $this->employees = $employees;
        $this->presenceIncentiveOnly = $presenceIncentiveOnly;
        // $this->startDatePeriod = $startDatePeriod;
        // $this->endDatePeriod = $endDatePeriod;
        // $this->staffOnly = $staffOnly;
    }

    /**
     * @return Builder
     */
    public function view(): View
    {
        // $startDate = $this->startDatePeriod;
        // $endDate = $this->endDatePeriod;
        // $company = $this->company;

        // $sheetCompanyId = null;
        // if (isset($company->id)) {
        //     $sheetCompanyId = $company->id;
        // }

        // $employees = Employee::query()->whereHas('salaries', function ($query) use ($startDate, $endDate) {
        //     $query->with(['items'])->where('start_date', $startDate)->where('end_date', $endDate);
        // })
        //     ->with([
        //         'activeCareer.jobTitle',
        //         'office.division.company',
        //         'bankAccounts',
        //         'loans' => function ($q) use ($endDate) {
        //             $q->with(['items' => function ($itemQuery) use ($endDate) {
        //                 $itemQuery->withCount(['salaryItem'])->where('payment_date', '<=', $endDate);
        //             }]);
        //         },
        //         'salaries' => function ($query) use ($startDate, $endDate) {
        //             $query->where('start_date', $startDate)->where('end_date', $endDate);
        //         }
        //     ])
        //     ->get()
        //     ->filter(
        //         function ($employee) use ($sheetCompanyId) {
        //             $employeeCompanyId = null;
        //             if (isset($employee?->office?->division?->company?->id)) {
        //                 $employeeCompanyId = $employee?->office?->division?->company?->id;
        //             }

        //             return $employeeCompanyId == $sheetCompanyId;
        //         }
        //     )
        //     ->map(function ($employee) {
        //         $basicSalary = 0;
        //         $positionAllowance = 0;
        //         $attendanceAllowance = 0;
        //         $currentMonthLoans = 0;
        //         $excessLeave = 0;
        //         $total = 0;
        //         $bankAccountNumber = null;

        //         $defaultBankAccount = collect($employee->bankAccounts)->where('default', 1)->first();
        //         if ($defaultBankAccount !== null) {
        //             $bankAccountNumber = $defaultBankAccount['account_number'];
        //         }

        //         $totalLoan = collect($employee->loans)->sum('amount');
        //         // $totalPaidLoan = collect($employee->loans)->pluck('items')->filter(function ($item) {
        //         //     return $item->salary_item_count > 0;
        //         // })->sum('amount');
        //         $totalPaidLoan = collect($employee->loans)->flatMap(function ($loan) {
        //             return $loan->items;
        //         })->filter(function ($item) {
        //             return $item->salary_item_count > 0;
        //         })->sum('basic_payment');

        //         $remainingLoan = $totalLoan - $totalPaidLoan;

        //         $salariesDataCount = collect($employee->salaries)->count();
        //         if ($salariesDataCount > 0) {
        //             $salaryItems = collect($employee->salaries)->flatMap(function ($salary) {
        //                 return $salary->items;
        //             });
        //             $basicSalary = $salaryItems->where('salary_type', 'gaji_pokok')->sum('amount');
        //             $positionAllowance = $salaryItems->where('salary_type', 'tunjangan')->sum('amount');
        //             $attendanceAllowance = $salaryItems->where('salary_type', 'presence_incentive')->sum('amount');
        //             $currentMonthLoans = $salaryItems->where('salary_type', 'loan')->sum('amount');
        //             $excessLeave = $salaryItems->where('salary_type', 'excess_leave')->sum('amount');
        //             $total = ($basicSalary + $positionAllowance + $attendanceAllowance) - ($currentMonthLoans + $excessLeave);
        //         }

        //         $employee->basic_salary = $basicSalary;
        //         $employee->position_allowance = $positionAllowance;
        //         $employee->attendance_allowance = $attendanceAllowance;
        //         $employee->loan = $currentMonthLoans;
        //         $employee->total_loan = $totalLoan;
        //         $employee->total_paid_loan = $totalPaidLoan;
        //         $employee->loan_balance = $remainingLoan;
        //         $employee->excess_leave = $excessLeave;
        //         $employee->total = $total;
        //         $employee->bank_account_number = $bankAccountNumber;

        //         return $employee;
        //     })->all();

        $employees = $this->employees;
        $presenceIncentiveOnly = $this->presenceIncentiveOnly;

        if ($presenceIncentiveOnly == 1) {
            return view('payrolls.exports.reports.staff_presence_incentive', [
                'employees' => $employees,
            ]);
        }

        return view('payrolls.exports.reports.monthly-v2', [
            // 'invoices' => Invoice::all()
            'employees' => $employees,
            // 'start_date' => $startDate,
            // 'end_date' => $endDate,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if (isset($this->company->name)) {
            return $this->company->name;
        }
        return 'Lainnya';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:N1';
                $workSheet = $event->sheet->getDelegate();

                // Header color
                $workSheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFe3f2fd');

                // Header Bold
                $workSheet->getStyle($cellRange)->getFont()->setBold(true);

                // Header height & vertical alignment
                $workSheet->getRowDimension('1')->setRowHeight(20);
                $workSheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $workSheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze columns
                $workSheet->freezePane('A2'); // freezing here
                $workSheet->freezePane('B2'); // freezing here
            },
        ];
    }
}
