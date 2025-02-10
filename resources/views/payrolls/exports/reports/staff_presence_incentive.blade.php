<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Rek Bank</th>
            <th>Job Title</th>
            <th>Denda Keterlambatan</th>
            <th>Insentif Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalBasicSalary = 0; ?>
        <?php $totalPositionAllowance = 0; ?>
        <?php $totalAttendanceAllowance = 0; ?>
        <?php $totalAttendanceAllowance = 0; ?>
        @foreach($employees as $employee)
        @if($employee->type == "staff")
        <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->bank_account_number !== null ? '(' . $employee->bank_account_number . ')' : '' }}</td>
            @if($employee->activeCareer !== null)
            @if($employee->activeCareer->jobTitle !== null)
            <td>{{ $employee->activeCareer->jobTitle->name }}</td>
            @else
            <td></td>
            @endif
            @else
            <td></td>
            @endif
            <td data-format="#,##0_-">{{ $employee->late_fee }}</td>
            <td data-format="#,##0_-">{{ $employee->attendance_allowance }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td colspan="3"><strong>TOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('late_fee') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('attendance_allowance') }}</strong></td>
            <!-- <td></td>
            <td></td> -->
        </tr>
    </tbody>
</table>