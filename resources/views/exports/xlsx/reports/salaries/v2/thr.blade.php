<table>
    <thead>
        <tr>
            <th>Laporan THR Periode {{ \Carbon\Carbon::parse($start_date)->isoFormat('LL') }} sd {{ \Carbon\Carbon::parse($end_date)->isoFormat('LL') }}</th>
            @for($i = 0; $i < 11; $i++) <th>
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
        <?php $columnsCount = 7; ?>
        @if(count($division->employees) > 0)
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
        @else
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong><em>Tidak Ada Data</em></strong></td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align: right;">Sub-Total</td>
            <td data-format="#,##0_-">{{ $division->subtotal['thr'] ?? 0 }}</td>
        </tr>
    </tfoot>
</table>
@endforeach
<table>
    <tr>
        <td colspan="6" style="text-align: right;">Grand-Total</td>
        <?php
        $properties = ['thr'];
        ?>
        @foreach($properties as $property)
        <td data-format="#,##0_-">{{ $grand_totals[$property] ?? 0 }}</td>
        @endforeach
    </tr>
</table>