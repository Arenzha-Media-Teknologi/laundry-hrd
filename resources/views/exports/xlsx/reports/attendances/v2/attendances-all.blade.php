<table>
    <thead>
        <tr>
            <th>Laporan Absensi Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</th>
            @for($i = 0; $i < 11; $i++) <th>
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
            @for($i = 0; $i < 12; $i++) <th>
                </th>
                @endfor
        </tr>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama</th>
            <th>NPWP</th>
            <th>Status PTKP</th>
            <th>Tgl Masuk</th>
            <!-- --- -->
            <th>Jumlah Hari</th>
            <th>Hadir</th>
            <th>Telat</th>
            <th>Absen</th>
            <th>Sakit</th>
            <th>Cuti</th>
            <th>Sisa Cuti</th>
        </tr>
    </thead>
    <tbody>
        <?php $columnsCount = 12; ?>
        @if(count($division->employees) > 0)
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name ?? '' }}</td>
            <td>{{ $employee->npwp_number }}</td>
            <td>{{ $employee->npwp_status }}</td>
            <td>{{ $employee->start_work_date }}</td>
            <td>{{ $total_days }}</td>
            <td>{{ $employee->attendance_summary['hadir'] ?? 0 }}</td>
            <td>{{ $employee->attendance_summary['total_late'] ?? 0 }}</td>
            <td>{{ $employee->attendance_summary['absence'] ?? 0 }}</td>
            <td>{{ $employee->attendance_summary['sakit'] ?? 0 }}</td>
            <td>{{ $employee->attendance_summary['cuti'] ?? 0 }}</td>
            <td>{{ ($employee->leave->total ?? 0) - ($employee->leave->taken ?? 0) }}</td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong><em>Tidak Ada Data</em></strong></td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align: right;">Sub-Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endforeach
<table>
    <tr>
        <td colspan="7" style="text-align: right;">Grand-Total</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>