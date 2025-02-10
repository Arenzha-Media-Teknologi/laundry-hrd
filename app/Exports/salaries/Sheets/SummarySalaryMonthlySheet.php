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

class SummarySalaryMonthlySheet implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $summaries;

    public function __construct($summaries)
    {
        $this->summaries = $summaries;
    }

    /**
     * @return Builder
     */
    public function view(): View
    {
        $summaries = $this->summaries;

        return view('payrolls.exports.reports.monthly-summary', [
            'summaries' => $summaries,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'SUMMARY';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:B1';
                $workSheet = $event->sheet->getDelegate();

                // Header color
                $workSheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFe3f2fd');

                // Header Bold
                $workSheet->getStyle($cellRange)->getFont()->setBold(true);

                // Header height & vertical alignment
                $workSheet->getRowDimension('1')->setRowHeight(30);
                $workSheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $workSheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Freeze columns & rows
                // $workSheet->freezePane('A2');
                // $workSheet->freezePane('B2');
            },
        ];
    }
}
