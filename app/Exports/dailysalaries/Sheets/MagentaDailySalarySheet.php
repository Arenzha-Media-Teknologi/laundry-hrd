<?php

namespace App\Exports\dailysalaries\Sheets;

use App\Models\Employee;
use App\Models\FinalPayslip;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MagentaDailySalarySheet implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $company;
    protected $dailySalaries;
    protected $setting;
    public function __construct($company, $dailySalaries, $setting)
    {
        $this->company = $company;
        $this->dailySalaries = $dailySalaries;
        $this->setting = $setting;
    }

    /**
     * @return Builder
     */
    public function view(): View
    {

        return view('payrolls.daily.exports.reports.magenta', [
            'final_payslips' => $this->dailySalaries,
            'setting' => $this->setting,
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
                $cellRange = 'A1:F1';
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
                // $workSheet->freezePane('B2'); // freezing here
            },
        ];
    }
}
