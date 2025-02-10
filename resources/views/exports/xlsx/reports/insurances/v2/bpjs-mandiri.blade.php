<table>
    <thead>
        <tr>
            <th>Rekap BPJS Mandiri - Periode Thn {{ $year }}</th>
            @for($i = 0; $i < 2; $i++) <th>
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
            <th>Nama Pemegang Polis</th>
            <th>Polis</th>
        </tr>
    </thead>
    <tbody>
        <?php $columnsCount = 4; ?>
        @if(count($division->employees) > 0)
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bpjs->mandiri_number ?? '' }}</td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong><em>Tidak Ada Data</em></strong></td>
        </tr>
        @endif
    </tbody>
</table>
@endforeach