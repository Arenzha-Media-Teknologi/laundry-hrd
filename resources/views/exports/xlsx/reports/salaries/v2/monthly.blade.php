<table>
  <thead>
    <tr>
      <th>Laporan Honor - Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</th>
      @for($i = 0; $i < 18; $i++) <th>
        </th>
        @endfor
        <th>{{ $company->name ?? '' }}</th>
    </tr>
  </thead>
</table>
@foreach($divisions as $division)
<table>
  <thead>
    <tr>
      <th><strong><em>{{ !isset($company) ? $division->company->name . ' - ' . $division->name : $division->name }}</em></strong></th>
    </tr>
    <tr>
      <th rowspan="2">No</th>
      <th rowspan="2">ID</th>
      <th rowspan="2">Nama</th>
      <th rowspan="2">Perusahaan</th>
      <th rowspan="2">Divisi</th>
      <th rowspan="2">NPWP</th>
      <th rowspan="2">Status PTKP</th>
      <th rowspan="2">Tgl Masuk</th>
      <th colspan="7" style="text-align: center;">Honor</th>
      <th colspan="4" style="text-align: center;">Piutang</th>
      <th rowspan="2">Netto</th>
    </tr>
    <tr>
      <th>Pokok</th>
      <th>Harian</th>
      <th>Tunjangan</th>
      <th>Insentif Kehadiran</th>
      <th>Lembur</th>
      <th>Cuti</th>
      <th>Total</th>
      <!-- ----- -->
      <th>Awal</th>
      <th>Pinjaman</th>
      <th>Potongan</th>
      <th>Akhir</th>
    </tr>
  </thead>
  <tbody>
    <?php $columnsCount = 12; ?>
    @if(count($division->employees) > 0)
    @foreach($division->employees as $employee)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $employee->number }}</td>
      <td>{{ $employee->name }}</td>
      <td>{{ $employee->office->division->company->name ?? '' }}</td>
      <td>{{ $employee->office->division->name ?? '' }}</td>
      <td>{{ $employee->npwp_number }}</td>
      <td>{{ $employee->npwp_status }}</td>
      <td>{{ $employee->start_work_date }}</td>
      <td data-format="#,##0_-">{{ $employee->basic_salary }}</td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-">{{ $employee->position_allowance }}</td>
      <td data-format="#,##0_-">{{ $employee->attendance_allowance }}</td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-">{{ $employee->bruto_salary }}</td>
      <td data-format="#,##0_-">{{ $employee->total_loan }}</td>
      <td data-format="#,##0_-">{{ $employee->current_month_loan }}</td>
      <td data-format="#,##0_-">{{ 0 - $employee->loan }}</td>
      <td data-format="#,##0_-">{{ $employee->remaining_loan }}</td>
      <td data-format="#,##0_-">{{ $employee->total }}</td>
    </tr>
    @endforeach
    @else
    <tr>
      <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong><em>Tidak Ada Data</em></strong></td>
    </tr>
    @endif
  </tbody>
  @if(count($division->employees) > 0)
  <tfoot>
    <tr>
      <td colspan="8" style="text-align: right;"><strong>Sub-Total</strong></td>
      <td data-format="#,##0_-">{{ $division->subtotal['basic_salary'] ?? 0 }}</td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-">{{ $division->subtotal['position_allowance'] ?? 0 }}</td>
      <td data-format="#,##0_-">{{ $division->subtotal['attendance_allowance'] ?? 0 }}</td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-"></td>
      <td data-format="#,##0_-">{{ $division->subtotal['bruto_salary'] ?? 0 }}</td>
      <td data-format="#,##0_-">{{ $division->subtotal['total_loan'] ?? 0 }}</td>
      <td data-format="#,##0_-">{{ $division->subtotal['current_month_loan'] ?? 0 }}</td>
      <td data-format="#,##0_-">{{ 0 - ($division->subtotal['loan'] ?? 0) }}</td>
      <td data-format="#,##0_-">{{ $division->subtotal['remaining_loan'] ?? 0 }}</td>
      <td data-format="#,##0_-">{{ $division->subtotal['total'] ?? 0 }}</td>
    </tr>
  </tfoot>
  @endif
</table>
@endforeach
<table>
  <tr>
    <td colspan="8" style="text-align: right;"><strong>Grand-Total</strong></td>
    <?php
    $properties = ['basic_salary', 'none', 'position_allowance', 'attendance_allowance', 'none', 'none', 'bruto_salary', 'total_loan', 'current_month_loan', 'loan', 'remaining_loan', 'total'];
    ?>
    @foreach($properties as $property)
    <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
    @endforeach
  </tr>
</table>