<?php

namespace App\Exports\reports\attendances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendancesByEmployeeReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $employee;
    protected $attendances;
    protected $startDate;
    protected $endDate;
    // protected $company;

    public function __construct($employee, $attendances, $startDate, $endDate)
    {
        $this->employee = $employee;
        $this->attendances = $attendances;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        // $this->company = $company;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $employee = $this->employee;
        $attendances = $this->attendances;
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        // $company = $this->company;

        return view('exports.xlsx.reports.attendances.v2.by-employee', [
            'employee' => $employee,
            'attendances' => $attendances,
            'start_date' => $startDate,
            'end_date' => $endDate,
            // 'company' => $company,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'I') as $excelColumn) {
            // echo "$v \n";
            $startRow = 3;
            $singleCell = $excelColumn . $startRow;
            $sheet->getStyle($singleCell)->applyFromArray([
                'borders' => [
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
    }
}
