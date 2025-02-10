<?php

namespace App\Exports\reports\employees;

use App\Models\Company;
use App\Models\Division;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EmployeesReportExport implements FromView, ShouldAutoSize, WithStyles
{
  protected $divisions;
  protected $startDate;
  protected $endDate;
  protected $company;

  public function __construct($divisions, $startDate, $endDate, $company)
  {
    $this->divisions = $divisions;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
    $this->company = $company;
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

    return view('exports.xlsx.reports.employees.v2.employees-all', [
      'divisions' => $divisions,
      'start_date' => $startDate,
      'end_date' => $endDate,
      'company' => $company,
    ]);
  }

  public function styles(Worksheet $sheet)
  {
    $divisions = $this->divisions ?? [];

    $startRow = 4;
    $headerRows = 2; // Number of header rows
    $startColumn = 'A';
    $endColumn = 'AD'; // Adjust as per your need

    // Convert the end column to an index
    $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
    $endColumnIndex = Coordinate::columnIndexFromString($endColumn);

    foreach ($divisions as $division) {
      // Define the range for the header rows
      $headerStartRow = $startRow;
      $headerEndRow = $startRow + $headerRows - 1;

      // Apply styles to the header rows
      foreach (range($headerStartRow, $headerEndRow) as $row) {
        foreach (range($startColumnIndex, $endColumnIndex) as $columnIndex) {
          $excelColumn = Coordinate::stringFromColumnIndex($columnIndex);
          $singleCell = $excelColumn . $row;
          $sheet->getStyle($singleCell)->applyFromArray([
            'borders' => [
              'outline' => [
                'borderStyle' => Border::BORDER_THIN,
              ],
            ],
            'fill' => [
              'fillType' => Fill::FILL_SOLID,
              'startColor' => ['argb' => 'FFbbdefb'],
            ],
          ]);
        }
      }

      // Apply styles to the data rows
      $dataStartRow = $headerEndRow + 1;
      $dataEndRow = $dataStartRow + count($division->employees) - 1;

      foreach (range($dataStartRow, $dataEndRow) as $row) {
        foreach (range($startColumnIndex, $endColumnIndex) as $columnIndex) {
          $excelColumn = Coordinate::stringFromColumnIndex($columnIndex);
          $singleCell = $excelColumn . $row;
          $sheet->getStyle($singleCell)->applyFromArray([
            'borders' => [
              'outline' => [
                'borderStyle' => Border::BORDER_THIN,
              ],
            ],
            'fill' => [
              'fillType' => Fill::FILL_SOLID,
              'startColor' => ['argb' => 'FFffffff'], // White background for data rows
            ],
          ]);
        }
      }

      $employeesCount = count($division->employees);
      $adder = 2;

      if ($employeesCount == 0) {
        $adder = 3;
      }

      // Adjust the start row for the next division
      $startRow = $dataEndRow + $adder + 1;
    }
  }
}
