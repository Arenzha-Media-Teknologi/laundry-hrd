<?php

namespace App\Exports\payrollbca;

use App\Exports\payrollbca\sheets\MultiPayrollTemplateSheet;
use App\Exports\salaries\Sheets\SalaryMonthlySheet;
use App\Exports\salaries\Sheets\SalaryMonthlySheetV2;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheet;
use App\Exports\salaries\Sheets\SummarySalaryMonthlySheetV2;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MultiPayrollTemplateDailyExport implements ShouldAutoSize, FromView, WithEvents
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
    public function view(): View
    {
        $employees = $this->employees;

        return view('payroll-bca.exports.multi-payroll-template-daily', [
            // 'invoices' => Invoice::all()
            'employees' => $employees,
            'start_date' => $this->startDatePeriod,
            'end_date' => $this->endDatePeriod,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:M1';
                $workSheet = $event->sheet->getDelegate();

                // Header color
                $workSheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB(Color::COLOR_BLACK);

                // Header Bold
                // $workSheet->getStyle($cellRange)->getFont()->setBold(true);
                $workSheet->getStyle($cellRange)->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

                // Header height & vertical alignment
                // $workSheet->getRowDimension('1')->setRowHeight(20);
                // $workSheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $workSheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // // Freeze columns
                // $workSheet->freezePane('A2'); // freezing here
                // $workSheet->freezePane('B2'); // freezing here
            },
        ];
    }
}
