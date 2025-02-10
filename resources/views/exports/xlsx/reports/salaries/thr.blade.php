<table>
    <thead>
        <tr>
            <th>Laporan THR - Periode {{ $year }}</th>
            @for($i = 0; $i < 16; $i++) <th>
                </th>
                @endfor
                <th>{{ $company->name ?? '' }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama</th>
            <th>NPWP</th>
            <th>Status PTKP</th>
            <th>Tgl Masuk</th>
            <th>THR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <tr>
            <td colspan="7">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>

        </tr>
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->npwp_number }}</td>
            <td>{{ $employee->npwp_status }}</td>
            <td>{{ $employee->start_work_date }}</td>
            <td data-format="#,##0_-">{{ $employee->thr }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6">Sub-Total</td>
            <td data-format="#,##0_-">{{ $division->subtotal['thr'] ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Grand-Total</td>
            <?php
            $properties = ['thr'];
            ?>
            @foreach($properties as $property)
            <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>