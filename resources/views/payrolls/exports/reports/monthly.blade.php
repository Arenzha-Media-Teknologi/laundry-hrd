<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Job Title</th>
            <th>Gaji</th>
            <th>Tunjangan Jabatan</th>
            <th>Insentif Kehadiran</th>
            <th>Pinjaman</th>
            <th>Kelebihan Cuti</th>
            <th>Total</th>
            <th>Awal Piutang</th>
            <th>Sisa Piutang</th>
            <th>Tanggal Mulai Kerja</th>
            <th>Rek Bank</th>
            <th>NPWP</th>
            <th>Status NPWP</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalBasicSalary = 0; ?>
        <?php $totalPositionAllowance = 0; ?>
        <?php $totalAttendanceAllowance = 0; ?>
        <?php $totalAttendanceAllowance = 0; ?>
        @foreach($employees as $employee)
        <tr>
            <td>{{ $employee->name }}</td>
            @if($employee->activeCareer !== null)
            @if($employee->activeCareer->jobTitle !== null)
            <td>{{ $employee->activeCareer->jobTitle->name }}</td>
            @else
            <td></td>
            @endif
            @else
            <td></td>
            @endif
            <td data-format="#,##0_-">{{ $employee->basic_salary }}</td>
            <td data-format="#,##0_-">{{ $employee->position_allowance }}</td>
            <td data-format="#,##0_-">{{ $employee->attendance_allowance }}</td>
            <!--<td>-</td>-->
            <td data-format="#,##0_-">{{ $employee->loan }}</td>
            <td data-format="#,##0_-">{{ $employee->excess_leave }}</td>
            <td data-format="#,##0_-">{{ $employee->total }}</td>
            <!--<td data-format="#,##0_-">{{ $employee->loan_balance + $employee->loan }}</td>-->
            <td data-format="#,##0_-">{{ $employee->total_loan }}</td>
            <td data-format="#,##0_-">{{ $employee->loan_balance }}</td>
            <td>{{ \Carbon\Carbon::parse($employee->start_work_date)->isoFormat('LL') }}</td>
            <td>{{ $employee->bank_account_number !== null ? '(' . $employee->bank_account_number . ')' : '' }}</td>
            @if(isset( $employee->npwp_number ))
            <td>{{ $employee->npwp_number }}</td>
            @else
            <td></td>
            @endif
            @if(isset( $employee->npwp_status ))
            <td>{{ $employee->npwp_status }}</td>
            @else
            <td></td>
            @endif
        </tr>
        @endforeach
        <tr>
            <td colspan="2"><strong>TOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('basic_salary') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('position_allowance') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('attendance_allowance') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('loan') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('excess_leave') }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ collect($employees)->sum('total') }}</strong></td>
            <!-- <td></td>
            <td></td> -->
        </tr>
    </tbody>
</table>