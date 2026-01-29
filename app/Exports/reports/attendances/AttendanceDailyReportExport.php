<?php

namespace App\Exports\reports\attendances;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceDailyReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $employees;
    protected $date;
    protected $attendanceStatus;

    public function __construct($employees, $date, $attendanceStatus = null)
    {
        $this->employees = $employees;
        $this->date = $date;
        $this->attendanceStatus = $attendanceStatus;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $employees = $this->employees;
        $date = $this->date;
        $attendanceStatus = $this->attendanceStatus;

        // Group employees by status
        $groupedByStatus = [];

        foreach ($employees as $employee) {
            $attendance = collect($employee->attendances)->first();

            if ($attendance !== null) {
                $status = $attendance->status;
            } else {
                $status = 'na'; // Tanpa Keterangan
            }

            // If specific status filter is set, only include that status
            if ($attendanceStatus && $attendanceStatus !== '' && $attendanceStatus !== 'na' && $status !== $attendanceStatus) {
                continue;
            }

            if ($attendanceStatus && $attendanceStatus === 'na' && $status !== 'na') {
                continue;
            }

            if (!isset($groupedByStatus[$status])) {
                $groupedByStatus[$status] = [];
            }

            $groupedByStatus[$status][] = [
                'employee' => $employee,
                'attendance' => $attendance,
            ];
        }

        // Define status order
        $statusOrder = ['hadir', 'cuti', 'sakit', 'izin', 'off', 'na'];

        // Reorder grouped data according to status order
        // Only show statuses that have data
        $orderedGrouped = [];
        foreach ($statusOrder as $status) {
            if (isset($groupedByStatus[$status]) && count($groupedByStatus[$status]) > 0) {
                $orderedGrouped[$status] = $groupedByStatus[$status];
            }
        }

        return view('exports.xlsx.reports.attendances.daily-report', [
            'groupedByStatus' => $orderedGrouped,
            'date' => $date,
            'attendanceStatus' => $attendanceStatus,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style for title row (row 1)
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Loop through all rows to find and style headers (rows with "No", "Divisi", etc.)
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();

            // Check if this is a header row (contains "No")
            if ($cellValue === 'No') {
                // Apply header styling
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '70AD47'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Style for status name rows with different colors for each status
            $statusColors = [
                'HADIR' => 'C6EFCE',        // Light green
                'CUTI' => 'FFEB9C',         // Light yellow
                'SAKIT' => 'E6D0EC',        // Light purple
                'IZIN' => 'FFD9B3',         // Light orange
                'OFF' => 'FFC7CE',          // Light red
                'TANPA KETERANGAN' => 'D9D9D9', // Light grey
            ];

            if (array_key_exists($cellValue, $statusColors)) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $statusColors[$cellValue]],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }

            // Check for summary rows (TEPAT WAKTU, TERLAMBAT, TOTAL)
            $cellValueF = $sheet->getCell('F' . $row)->getValue();
            if (in_array($cellValueF, ['TEPAT WAKTU', 'TERLAMBAT', 'TOTAL'])) {
                $sheet->getStyle('F' . $row . ':G' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7E6E6'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        }

        // Apply borders to all data cells
        for ($row = 1; $row <= $highestRow; $row++) {
            $cellValue = $sheet->getCell('A' . $row)->getValue();

            // Check if this is a data row (starts with a number)
            if (is_numeric($cellValue)) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Center align the No column (column A)
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Center align the Jam Masuk column (column E)
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        // Set row height for better visibility
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        return [];
    }
}
