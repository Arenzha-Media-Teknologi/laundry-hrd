<?php

namespace App\Exports\reports\insurances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BpjsMandiriExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $divisions;
    protected $year;
    protected $company;

    public function __construct($divisions, $year, $company)
    {
        $this->divisions = $divisions;
        $this->year = $year;
        $this->company = $company;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $divisions = $this->divisions;
        $year = $this->year;
        $company = $this->company;

        return view('exports.xlsx.reports.insurances.v2.bpjs-mandiri', [
            'divisions' => $divisions,
            'year' => $year,
            'company' => $company,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
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
