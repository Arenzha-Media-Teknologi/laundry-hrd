<table>
    <thead>
        <tr>
            <th>Laporan Absensi Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</th>
            @for($i = 0; $i < 7; $i++) <th>
                </th>
                @endfor
                <th>{{ $employee->office->division->company->name ?? '' }}</th>
        </tr>
        <tr>
            <td>
                {{ $employee->number ?? '[ID]' }},
                {{ $employee->name ?? '[NAMA]' }},
                {{ $employee->office->division->name ?? '[DIVISI]' }},
                {{ $employee->npwp_number ?? '[NPWP]' }},
                {{ $employee->npwp_status ?? '[STATUS PTKP]' }},
                {{ $employee->start_work_date ?? '[TGL BEKERJA]' }},
            </td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Total Jam Kerja</th>
            <th>Telat (Menit)</th>
            <th>Lembur (Menit)</th>
            <th>Absen</th>
            <th>Sakit</th>
            <th>Cuti</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance['iso_date'] }}</td>
            <td>{{ $attendance['attendance']['clock_in_time'] ?? '' }}</td>
            <td>{{ $attendance['attendance']['clock_out_time'] ?? '' }}</td>
            <td>{{ $attendance['attendance']['work_duration'] ?? '' }}</td>
            <td>{{ $attendance['attendance']['time_late'] ?? '' }}</td>
            <td>{{ $attendance['attendance']['overtime'] ?? '' }}</td>
            @if(!isset($attendance['attendance']))
            <td>
                1
            </td>
            @else
            <td></td>
            @endif
            @if(($attendance['attendance']['status'] ?? '') == 'sakit')
            <td>
                1
            </td>
            @else
            <td></td>
            @endif
            @if(($attendance['attendance']['status'] ?? '') == 'cuti')
            <td>
                1
            </td>
            @else
            <td></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>