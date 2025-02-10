<table>
    <thead>
        <tr>
            <th>{{ $company->name ?? '' }}</th>
        </tr>
        <tr>
            <th>Daftar Karyawan Yang Tidak Masuk Tanggal {{ $date }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($divisions as $division)
        <tr>
            <td colspan="4">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>
        </tr>
        @foreach($division->employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee->number }}</td>
            <td>{{ $employee->name ?? '' }}</td>
            <td></td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>