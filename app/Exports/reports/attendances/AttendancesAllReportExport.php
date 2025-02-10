<?php

namespace App\Exports\reports\attendances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendancesAllReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $divisions;
    protected $startDate;
    protected $endDate;
    protected $company;
    protected $totalDays;

    public function __construct($divisions, $startDate, $endDate, $company, $totalDays)
    {
        $this->divisions = $divisions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->company = $company;
        $this->totalDays = $totalDays;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $divisions = $this->divisions;
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $company = $this->company;
        $totalDays = $this->totalDays;

        return view('exports.xlsx.reports.attendances.v2.attendances-all', [
            'divisions' => $divisions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company' => $company,
            'total_days' => $totalDays,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $divisions = $this->divisions ?? [];

        $startRow = 4;
        $startColumn = 'A';
        $endColumn = 'M';

        $subTotalSpec = [
            'start_row' => 0,
            'start_column' => 'H',
            'end_column' => 'M',
        ];

        foreach ($divisions as $index => $division) {
            $cells = $startColumn . $startRow . ':' . $endColumn . $startRow;

            // $sheet->getStyle($cells)->getFont()->setBold(true);
            foreach (range($startColumn, $endColumn) as $excelColumn) {
                // echo "$v \n";
                $singleCell = $excelColumn . $startRow;
                $sheet->getStyle($singleCell)->applyFromArray([
                    'borders' => [
                        // 'top' => [
                        //     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        // ],
                        // 'bottom' => [
                        //     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        // ],
                        'outline' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            // 'color' => array('argb' => 'FFFF0000'),
                        ),
                    ],
                    'fill' => array(
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => array('argb' => 'FFbbdefb')
                    )
                ]);
            }



            $employeesCount = count($division->employees);

            $adder = 4;
            if ($employeesCount == 0) {
                $adder += 1;
            }

            $subTotalAdder = 1;
            if ($employeesCount == 0) {
                $subTotalAdder += 1;
            }
            $subTotalSpec['start_row'] = $startRow + $employeesCount + $subTotalAdder;

            $subTotalCells = $subTotalSpec['start_column'] . $subTotalSpec['start_row'] . ':' . $subTotalSpec['end_column'] . $subTotalSpec['start_row'];

            // $sheet->getStyle($subTotalCells)->getFont()->setBold(true);


            $sheet->getStyle($subTotalCells)->applyFromArray([
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            if ($index == (count($divisions) - 1)) {
                $subTotalSpec['start_row'] += 2;
                $totalCells = $subTotalSpec['start_column'] . $subTotalSpec['start_row'] . ':' . $subTotalSpec['end_column'] . $subTotalSpec['start_row'];
                $sheet->getStyle($totalCells)->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                    ],
                ]);
            }

            $startRow += ($employeesCount + $adder);

            // Subtotal ---
        }
    }
}
