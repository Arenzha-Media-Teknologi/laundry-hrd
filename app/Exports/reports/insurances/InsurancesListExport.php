<?php

namespace App\Exports\reports\insurances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InsurancesListExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $divisions;
    protected $year;
    protected $company;
    protected $grandTotals;

    public function __construct($divisions, $year, $company, $grandTotals)
    {
        $this->divisions = $divisions;
        $this->year = $year;
        $this->company = $company;
        $this->grandTotals = $grandTotals;
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

        return view('exports.xlsx.reports.insurances.v2.insurances-list', [
            'divisions' => $divisions,
            'year' => $year,
            'company' => $company,
            'grand_total' => $grandTotals,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $divisions = $this->divisions ?? [];

        $startRow = 4;
        $startColumn = 'A';
        $endColumn = 'G';

        $subTotalSpec = [
            'start_row' => 0,
            'start_column' => 'G',
            'end_column' => 'G',
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

            $employeeInsurancesCount = collect($division->employees)->sum(function ($employee) {
                return count($employee->insurance_values) ?? 0;
            });

            $startRow += ($employeesCount + $adder);

            // Subtotal ---
        }
    }
}
