<table>
    <tr>
        <td>1</td>
        <td>Rekap BPJS Ketenaga kerjaan - Periode Thn {{ $year }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th rowspan="2">No. Urut</th>
            <th rowspan="2">ID #</th>
            <th rowspan="2">Nama Pemegang Polis</th>
            <th rowspan="2">Polis #</th>
            <th rowspan="2">Thn Mulai Polis</th>
            <th colspan="4">Pambayaran Polis Thn {{ $year }}</th>
            <th colspan="4">Akumulasi Nilai Polis Thn {{ $year }}</th>
        </tr>
        <tr>
            <th></th>
            <th>JHT</th>
            <th>JKK</th>
            <th>JKM</th>
            <th>JP</th>
            <th>JHT</th>
            <th>JKK</th>
            <th>JKM</th>
            <th>JP</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalJhtPayment = 0;
        $totalJkkPayment = 0;
        $totalJkmPayment = 0;
        $totalJpPayment = 0;
        $totalJht = 0;
        $totalJkk = 0;
        $totalJkm = 0;
        $totalJp = 0;
        ?>
        @foreach($employees_bpjs_ketenagakerjaan as $employee)
        <tr>
            <td></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_number ?? '' }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_start_year ?? '' }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jht_payment'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jkk_payment'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jkm_payment'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jp_payment'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jht'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jkk'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jkm'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['jp'] ?? 0 }}</td>
        </tr>
        <?php
        $totalJhtPayment += $employee->current_year_value['jht_payment'] ?? 0;
        $totalJkkPayment += $employee->current_year_value['jkk_payment'] ?? 0;
        $totalJkmPayment += $employee->current_year_value['jkm_payment'] ?? 0;
        $totalJpPayment += $employee->current_year_value['jp_payment'] ?? 0;
        $totalJht += $employee->current_year_value['jht'] ?? 0;
        $totalJkk += $employee->current_year_value['jkk'] ?? 0;
        $totalJkm += $employee->current_year_value['jkm'] ?? 0;
        $totalJp += $employee->current_year_value['jp'] ?? 0;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total</td>
            <th data-format="#,##0_-">{{ $totalJhtPayment }}</th>
            <th data-format="#,##0_-">{{ $totalJkkPayment }}</th>
            <th data-format="#,##0_-">{{ $totalJkmPayment }}</th>
            <th data-format="#,##0_-">{{ $totalJpPayment }}</th>
            <th data-format="#,##0_-">{{ $totalJht }}</th>
            <th data-format="#,##0_-">{{ $totalJkk }}</th>
            <th data-format="#,##0_-">{{ $totalJkm }}</th>
            <th data-format="#,##0_-">{{ $totalJp }}</th>
        </tr>
    </tfoot>
</table>
<table>
    <tr>
        <td>2</td>
        <td>Rekap BPJS Mandiri - Periode Thn {{ $year }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th>No. Urut</th>
            <th>ID #</th>
            <th>Nama Pemegang Polis</th>
            <th>Polis #</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees_bpjs_mandiri as $employee)
        <tr>
            <td></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->mandiri_number ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@foreach($private_insurances as $private_insurance)
<table>
    <tr>
        <td>{{ $loop->iteration + 2 }}</td>
        <td>Rekap {{ $private_insurance->name }} - Periode Thn {{ $year }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th rowspan="2">No. Urut</th>
            <th rowspan="2">ID #</th>
            <th rowspan="2">Nama Pemegang Polis</th>
            <th rowspan="2">Polis #</th>
            <th rowspan="2">Thn Mulai Polis</th>
            <th colspan="3">Pembayaran Premi Polis Thn {{ $year }}</th>
            <th rowspan="2">Akum. Nilai Tabungan Thn {{ $year }}</th>
            <th rowspan="2">Polis Kematian</th>
        </tr>
        <tr>
            <th></th>
            <th>Total Premi</th>
            <th>Kesehatan</th>
            <th>Tabungan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalPremi = 0;
        $totalKesehatan = 0;
        $totalTabungan = 0;
        $totalNilaiTabungan = 0;
        $totalKematian = 0;
        ?>
        @foreach($private_insurance['members'] as $employee)
        <tr>
            <td></td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->current_private_insurance->pivot->number ?? '' }}</td>
            <td>{{ $employee->current_private_insurance->pivot->start_year ?? '' }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['total_premi'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['kesehatan'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $private_insurance->premi_tabungan ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $employee->current_year_value['nilai_tabungan'] ?? 0 }}</td>
            <td data-format="#,##0_-">{{ $private_insurance->premi_kematian ?? 0 }}</td>
        </tr>
        <?php
        $totalPremi += $employee->current_year_value['total_premi'] ?? 0;
        $totalKesehatan += $employee->current_year_value['kesehatan'] ?? 0;
        $totalTabungan += $private_insurance->premi_tabungan ?? 0;
        $totalNilaiTabungan += $employee->current_year_value['nilai_tabungan'] ?? 0;
        $totalKematian += $private_insurance->premi_kematian ?? 0;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total</td>
            <td data-format="#,##0_-">{{ $totalPremi }}</td>
            <td data-format="#,##0_-">{{ $totalKesehatan }}</td>
            <td data-format="#,##0_-">{{ $totalTabungan }}</td>
            <td data-format="#,##0_-">{{ $totalNilaiTabungan }}</td>
            <td data-format="#,##0_-">{{ $totalKematian }}</td>
        </tr>
    </tfoot>
</table>
@endforeach