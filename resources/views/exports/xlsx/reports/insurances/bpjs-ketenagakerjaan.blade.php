<table>
    <thead>
        <tr>
            <th>Rekap BPJS Ketenagakerjaan - Periode Thn {{ $year }}</th>
            @for($i = 0; $i < 9; $i++) <th>
                </th>
                @endfor
                <th>{{ $company->name ?? '' }}</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">ID</th>
            <th rowspan="2">Nama Pemegang Polis</th>
            <th rowspan="2">Polis</th>
            <th rowspan="2">Tahun Mulai Polis</th>
            <th colspan="4">Pembayaran Polis Tahun 2022</th>
            <th colspan="2">Akumulasi Nilai Polis Tahun 2022</th>
        </tr>
        <tr>
            <th>JHT</th>
            <th>JKK</th>
            <th>JKM</th>
            <th>JP</th>
            <!-- ----- -->
            <th>JHT</th>
            <th>JP</th>

        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <tr class="bg-light-primary">
            <td colspan="11">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>

        </tr>
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
        <tr>
            <td colspan="5">Sub-Total</td>
            <?php
            $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $division->subtotal[$property] ?? 0 }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">Grand-Total</td>
            <?php
            $properties = ['jht_payment', 'jkk_payment', 'jkm_payment', 'jp_payment', 'jht', 'jp'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>