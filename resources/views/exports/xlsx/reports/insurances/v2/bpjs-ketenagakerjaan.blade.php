<table>
    <thead>
        <tr>
            <th>Rekap BPJS Ketenagakerjaan - Periode Thn {{ $year }}</th>
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
            <th colspan="4" style="text-align: center;">Pembayaran Polis Tahun 2022</th>
            <th colspan="2" style="text-align: center;">Akumulasi Nilai Polis Tahun 2022</th>
        </tr>
        <tr>
            <th style="text-align: right;">JHT</th>
            <th style="text-align: right;">JKK</th>
            <th style="text-align: right;">JKM</th>
            <th style="text-align: right;">JP</th>
            <!------- -->
            <th style="text-align: right;">JHT</th>
            <th style="text-align: right;">JP</th>
        </tr>
    </thead>
    <tbody>
        <?php $columnsCount = 11; ?>
        @if(count($division->employees) > 0)
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_number ?? '' }}</td>
            <td>{{ $employee->bpjs->ketenagakerjaan_start_year ?? '' }}</td>
            <?php
            $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];
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
            $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];
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
        $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];
        ?>
        @foreach($properties as $property)
        <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
        @endforeach
    </tr>
</table>