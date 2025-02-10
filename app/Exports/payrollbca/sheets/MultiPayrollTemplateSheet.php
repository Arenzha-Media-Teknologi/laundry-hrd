<?php

namespace App\Exports\payrollbca\sheets;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MultiPayrollTemplateSheet implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $company;
    protected $employees;
    protected $startDatePeriod;
    protected $endDatePeriod;
    // protected $staffOnly;

    public function __construct($company, $employees, $startDatePeriod, $endDatePeriod)
    {
        $this->company = $company;
        $this->employees = $employees;
        $this->startDatePeriod = $startDatePeriod;
        $this->endDatePeriod = $endDatePeriod;
        // $this->staffOnly = $staffOnly;
    }

    /**
     * @return Builder
     */
    public function view(): View
    {
        $employees = $this->employees;

        return view('payroll-bca.exports.multi-payroll-template', [
            // 'invoices' => Invoice::all()
            'employees' => $employees,
            'start_date' => $this->startDatePeriod,
            'end_date' => $this->endDatePeriod,
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
