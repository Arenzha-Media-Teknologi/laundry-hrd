<table>
    <tr>
        <td>Laporan Deposit</td>
    </tr>
</table>
<table>
    <thead>
        <!--begin::Table row-->
        <tr>
            <th>Pegawai</th>
            <th>Tanggal</th>
            <th>Jumlah Cicilan</th>
            <th>Jumlah (Rp)</th>
            <th>Jumlah Dipotong (Rp)</th>
            <th>Status Pengembalian</th>
        </tr>
        <!--end::Table row-->
    </thead>
    <tbody>
        @foreach($deposits as $deposit)
        <tr>
            <td>{{ $deposit->employee->name ?? '' }}</td>
            <td>{{ $deposit->date }}</td>
            <td>{{ count($deposit->items) }}</td>
            <td data-format="#,##0_-">{{ $deposit->amount }}</td>
            <td data-format="#,##0_-">{{ $deposit->items_sum_amount }}</td>
            <td>
                @if($deposit->redeemed == 1)
                <span>Sudah</span>
                @else
                <span>Belum</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>