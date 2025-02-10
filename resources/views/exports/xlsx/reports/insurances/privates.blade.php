<table>
    <thead>
        <tr>
            <th>Rekap {{ $private_insurance->name ?? '' }} - Periode Thn {{ $year }}</th>
            @for($i = 0; $i < 8; $i++) <th>
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
            <th colspan="3">Pembayaran Premi Polis Tahun 2022</th>
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
        @foreach($divisions as $division)
        <tr>
            <td colspan="10">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>

        </tr>
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
        <tr>
            <td colspan="5">Sub-Total</td>
            <?php
            $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];
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
            $properties = ['total_premi', 'kesehatan', 'premi_tabungan', 'nilai_tabungan', 'premi_kematian'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>