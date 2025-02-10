<table>
    <thead>
        <tr>
            <th>Rekap {{ $private_insurance->name ?? '' }} - Periode Thn {{ $year }}</th>
            @for($i = 0; $i < 9; $i++) <th>
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
            <th rowspan="2">Nama Pemegang Polis</th>
            <th rowspan="2">Polis</th>
            <th rowspan="2">Tahun Mulai Polis</th>
            <th colspan="3" style="text-align: center;">Pembayaran Premi Polis Tahun 2022</th>
            <th rowspan="2">Akum. Nilai Tabungan Tahun 2022</th>
            <th rowspan="2">Polis Kematian</th>
        </tr>
        <tr>
            <th>Total Premi</th>
            <th>Kesehatan</th>
            <th>Tabungan</th>
        </tr>
    </thead>
    <tbody>
        <?php $columnsCount = 10; ?>
        @if(count($division->employees) > 0)
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_number ?? '' }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_start_year ?? '' }}</td>
            <?php
            $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $employee->current_year_value[$property] ?? 0 }}</td>
            @endforeach
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
            <td colspan="5" style="text-align: right;">Sub-Total</td>
            <?php
            $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $division->subtotal[$property] ?? 0 }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>
@endforeach
<table>
    <tr>
        <td colspan="5" style="text-align: right;">Grand-Total</td>
        <?php
        $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];
        ?>
        @foreach($properties as $property)
        <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
        @endforeach
    </tr>
</table>