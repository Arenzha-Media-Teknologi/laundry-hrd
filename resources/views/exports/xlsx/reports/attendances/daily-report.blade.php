<?php

use Carbon\Carbon;

$formattedDate = Carbon::parse($date);
$dayName = $formattedDate->locale('id')->isoFormat('dddd');
$formattedDateString = $formattedDate->locale('id')->isoFormat('D MMMM YYYY');
$titleDate = $formattedDate->locale('id')->isoFormat('D MMMM YYYY');
?>

<table>
    <tr>
        <td colspan="7"><strong>LAPORAN ABSENSI : {{ strtoupper($titleDate) }}</strong></td>
    </tr>
    <tr></tr>
</table>

@foreach($groupedByStatus as $status => $items)
<?php
$statusNames = [
    'hadir' => 'HADIR',
    'cuti' => 'CUTI',
    'sakit' => 'SAKIT',
    'izin' => 'IZIN',
    'off' => 'OFF',
    'na' => 'TANPA KETERANGAN',
];

$statusName = $statusNames[$status] ?? strtoupper($status);

// Count tepat waktu and terlambat for HADIR status
$tepatWaktu = 0;
$terlambat = 0;

if ($status === 'hadir') {
    foreach ($items as $item) {
        if ($item['attendance'] && $item['attendance']->time_late > 0) {
            $terlambat++;
        } else if ($item['attendance']) {
            $tepatWaktu++;
        }
    }
}
?>

<table>
    <tr>
        <td colspan="2"><strong>Laporan Absensi</strong></td>
        <td colspan="5">{{ $dayName }}, {{ $formattedDateString }}</td>
    </tr>
    <tr>
        <td colspan="7"><strong>{{ $statusName }}</strong></td>
    </tr>
    <tr>
        <th>No</th>
        <th>Divisi</th>
        <th>Kantor</th>
        <th>Karyawan</th>
        <th>Jam Masuk</th>
        <th>Status</th>
        <th>Keterangan</th>
    </tr>
    @if(count($items) > 0)
    @foreach($items as $index => $item)
    <?php
    $employee = $item['employee'];
    $attendance = $item['attendance'];

    $divisionName = $employee->office->division->name ?? '';
    $officeName = $employee->office->name ?? '';
    $employeeName = $employee->name ?? '';

    $statusText = '';
    $keterangan = '';
    $jamMasuk = '';

    if ($attendance) {
        if ($status === 'hadir') {
            $statusText = 'Hadir';
            $jamMasuk = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '';
            if ($attendance->time_late > 0) {
                $keterangan = 'Terlambat';
            } else {
                $keterangan = 'Tepat Waktu';
            }
        } else if ($status === 'cuti') {
            $statusText = 'Cuti';
            $keterangan = '';
        } else if ($status === 'sakit') {
            $statusText = 'Sakit';
            $keterangan = '';
        } else if ($status === 'izin') {
            $statusText = 'Izin';
            $keterangan = '';
        } else if ($status === 'off') {
            $statusText = 'OFF';
            $jamMasuk = $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '';
            $keterangan = '';
        } else {
            $statusText = ucfirst($status);
            $keterangan = '';
        }
    } else {
        $statusText = 'Tidak Hadir (Tanpa Keterangan)';
        $keterangan = '';
    }
    ?>
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $divisionName }}</td>
        <td>{{ $officeName }}</td>
        <td>{{ $employeeName }}</td>
        <td>{{ $jamMasuk }}</td>
        <td>{{ $statusText }}</td>
        <td>{{ $keterangan }}</td>
    </tr>
    @endforeach
    @endif

    @if($status === 'hadir')
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>TEPAT WAKTU</strong></td>
        <td><strong>{{ $tepatWaktu }}</strong></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>TERLAMBAT</strong></td>
        <td><strong>{{ $terlambat }}</strong></td>
    </tr>
    @else
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endif

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>TOTAL</strong></td>
        <td><strong>{{ count($items) }}</strong></td>
    </tr>
</table>

<tr></tr>
@endforeach