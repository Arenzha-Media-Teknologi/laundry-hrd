<table>
    <tr>
        <td><strong>Laporan Cuti Diuangkan Tahun {{ $year }}</strong></td>
    </tr>
</table>
@foreach($employees as $companyId => $employees2)
<table>
    <!--begin::Table head-->
    <thead>
        <tr>
            <th><strong>{{ $companies[$companyId] ?? 'NAMA_PERUSAHAAN' }}</strong></th>
        </tr>
        <!--begin::Table row-->
        <tr>
            <th rowspan="2">Pegawai</th>
            <th rowspan="2">Jatah Cuti</th>
            <th colspan="12" style="text-align: center;">Cuti Diambil</th>
            <th rowspan="2">Total Cuti Diambil</th>
            <th rowspan="2">Sisa Cuti</th>
            <th rowspan="2">Gaji Pokok (Rp)</th>
            <th rowspan="2">Uang Harian (Rp)</th>
            <th rowspan="2">Nominal (Rp)</th>
        </tr>
        <tr>
            <?php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] ?>
            @foreach($months as $month)
            <th>{{ $month }}</th>
            @endforeach
        </tr>
        <!--end::Table row-->
    </thead>
    <!--end::Table head-->
    <!--begin::Table body-->
    <tbody>
        <?php
        $totalGajiPokok = 0;
        $totalUangHarian = 0;
        $totalRedeemedLeaveAmount = 0;
        ?>
        @foreach($employees2 as $employee)
        <tr>
            <td>{{ $employee->name ?? '' }}</td>
            <td>{{ $employee->leave['total'] ?? 0 }}</td>
            @for($i = 0; $i < 12; $i++) <td>
                {{ $employee->grouped_leave_applications[$i] ?? '' }}
                </td>
                @endfor
                <td>{{ $employee->leave['taken'] ?? 0 }}</td>
                <?php
                $remainingLeave = ($employee->leave['total'] ?? 0) - ($employee->leave['taken'] ?? 0);
                ?>
                <td>{{ ($remainingLeave < 0) ? 0 : $remainingLeave }}</td>
                <td data-format="#,##0_-">{{ $employee->gaji_pokok }}</td>
                <td data-format="#,##0_-">{{ $employee->uang_harian }}</td>
                <td data-format="#,##0_-">{{ $employee->redeemed_leave_amount }}</td>
        </tr>
        <?php
        $totalGajiPokok += $employee->gaji_pokok;
        $totalUangHarian += $employee->uang_harian;
        $totalRedeemedLeaveAmount += $employee->redeemed_leave_amount;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="16"><strong>TOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ $totalGajiPokok }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $totalUangHarian }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $totalRedeemedLeaveAmount }}</strong></td>
        </tr>
    </tfoot>
    <!--end::Table body-->
</table>
@endforeach