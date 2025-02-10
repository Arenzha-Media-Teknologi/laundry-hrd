<?php

namespace App\Exports\reports\attendances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsencesExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $divisions;
    protected $date;
    protected $company;

    public function __construct($divisions, $date, $company)
    {
        $this->divisions = $divisions;
        $this->date = $date;
        $this->company = $company;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $divisions = $this->divisions;
        $date = $this->date;
        $company = $this->company;

        return view('exports.xlsx.reports.attendances.v2.absences', [
            'divisions' => $divisions,
            'date' => $date,
            'company' => $company,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // $sheet->getStyle('A2:L2')->applyFromArray([
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //         ],
        //     ]
        // ]);
        $divisions = $this->divisions ?? [];

        $startRow = 4;
        $startColumn = 'A';
        $endColumn = 'D';
        foreach ($divisions as $division) {
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

            $adder = 3;
            if ($employeesCount == 0) {
                $adder = 4;
            }

            $startRow += ($employeesCount + $adder);
        }
    }
}
