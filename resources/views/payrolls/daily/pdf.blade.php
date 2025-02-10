<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji ({{ $employee->employee_id }}) {{ $employee->first_name }} Periode {{ \Carbon\Carbon::parse($final_payslip->start_date_period)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date_period)->isoFormat('LL') }}</title>

    <style>
        html {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
        }

        .table-detail,
        .table-employee {
            width: 100%;
            border-collapse: collapse;
        }

        .table-employee thead td {
            background-color: #F48FB1;
            border: 1px solid #F06292;
            padding: 0.4rem 0.2rem;
            text-transform: uppercase;
            font-size: 10px;
            /* font-size: 0.8rem; */
        }

        .table-employee tbody td {
            border: 1px solid #F06292;
            padding: 0.2rem;
        }

        .table-detail thead td {
            background-color: #E0E0E0;
            border-bottom: 1px solid #9E9E9E;
            padding: 0.4rem 0.2rem;
            text-transform: uppercase;
            font-size: 10px;
            /* font-size: 0.8rem; */
        }

        .table-detail tfoot th {
            background-color: #E0E0E0;
            border-bottom: 1px solid #9E9E9E;
            padding: 0.4rem 0.2rem;
        }

        .table-detail tbody td {
            border-bottom: 1px solid #9E9E9E;
            padding: 0.2rem;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bg-gray {
            background-color: #95a5a6;
        }

        .w-50 {
            width: 50%;
        }
    </style>
</head>

<body>
    <div class="header" style="width: 100%;">
        <div style="width: 50%; float: left;">
            <h1 style="margin-bottom: 0; color: #E91E63">PT. Magenta Mediatama</h1>
            <p style="margin-top: 0; width: 80%;">Jl. Raya Kby. Lama No.15, RT.4/RW.3, Grogol Utara, Kec. Kby. Lama, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 11540</p>
        </div>
        <div style="width: 50%; float: right;" class="text-right">
            <!--<img src="https://karir-production.nos.jkt-1.neo.id/logos/11/1029111/unilabel_magenta.png" alt="LOGO PT. MAGENTA MEDIATAMA" width="250" height="100">-->
        </div>
    </div>

    <div class="title text-center" style="clear: both;">
        <h2 style="margin-bottom: 0;">Slip Gaji</h2>
        <h3 style="margin-top: 0;">Periode {{ \Carbon\Carbon::parse($final_payslip->start_date_period)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date_period)->isoFormat('LL') }}</h3>
    </div>
    <div style="margin-bottom: 20px;">
        <table class="table-employee">
            <thead>
                <tr class="text-center">
                    <td>ID Pegawai</td>
                    <td>Nama</td>
                    <td>Departemen</td>
                    <td>Bagian</td>
                    <td>Job Title</td>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->first_name }}</td>
                    @if(count($employee->careers) > 0)
                    <td>{{ $employee->careers[0]->department->name }}</td>
                    <td>{{ $employee->careers[0]->designation->name }}</td>
                    @if(count($employee->careers) > 0)
                    @if($employee->careers[0]->jobTitle !== null)
                    <td>{{$employee->careers[0]->jobTitle->name}}</td>
                    @else
                    <td></td>
                    @endif
                    @endif
                    @else
                    <td></td>
                    <td></td>
                    <td></td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>

    <table class="table-detail">
        <thead>
            <tr class="text-center">
                <td>Tanggal</td>
                <td>Hari</td>
                <td>Kal</td>
                <td>Masuk</td>
                <td>Pulang</td>
                <td>Lembur</td>
                <td>Keterlambatan</td>
                <td>Gaji Harian</td>
                <td>Uang Lembur</td>
                <td>Total</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $weekBahasa = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $totalDailyMoney = 0;
            $totalOvertimePay = 0;
            $takeHomePay = 0;
            $totalMinutesOfDelay = 0;
            ?>
            @foreach($final_payslip->income as $income)
            <tr>
                <td class="text-center">{{ date_format(date_create($income->date), "d/m/Y") }}</td>
                <td class="text-center">{{ $weekBahasa[\Carbon\Carbon::parse($income->date)->dayOfWeek] }}</td>
                <td class="text-center">{{ $income->calendar }}</td>
                @if($income->attendance !== null)
                <td class="text-center">{{ $income->attendance->clock_in }}</td>
                <td class="text-center">{{ $income->attendance->clock_out }}</td>
                <td class="text-center">{{ $income->attendance->overtime }}</td>
                @if($income->attendance->minutes_of_delay > 0)
                <td class="text-center">{{ number_format($income->attendance->minutes_of_delay, 0, ",", ".") }} Menit</td>
                @else
                <td class="text-center"></td>
                @endif
                <td class="text-right">Rp {{ number_format($income->attendance->daily_money, 0, ",", ".") }}</td>
                <td class="text-right">Rp {{ number_format($income->attendance->overtime_pay, 0, ",", ".") }}</td>
                <td class="text-right">Rp {{ number_format($income->attendance->overtime_pay + $income->attendance->daily_money, 0, ",", ".") }}</td>
                <?php
                $totalDailyMoney += $income->attendance->daily_money;
                $totalOvertimePay += $income->attendance->overtime_pay;
                $totalMinutesOfDelay += $income->attendance->minutes_of_delay;
                ?>
                @else
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @endif
            </tr>

            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-left" colspan="6">TOTAL</th>
                <th class="text-center">{{ number_format($totalMinutesOfDelay, 0, ",", ".") }} Menit</th>
                <th class="text-right">Rp {{ number_format($totalDailyMoney, 0, ",", ".") }}</th>
                <th class="text-right">Rp {{ number_format($totalOvertimePay, 0, ",", ".") }}</th>
                <th class="text-right">Rp {{ number_format($totalDailyMoney + $totalOvertimePay, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>
    <h4 style="margin-top: 10px;">Deduction</h4>
    <table class="table-detail" style="margin-top: 30px;">
        <thead>
            <tr>
                <td>Deduction</td>
                <td class="text-right">Jumlah</td>
            </tr>
        </thead>
        <tbody>
            <?php $totalDeduction = 0; ?>
            @if(is_array($final_payslip->deduction))
            @foreach($final_payslip->deduction as $deduction)
            <tr>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">Rp {{ number_format($deduction->value, 0, ",", ".") }}</td>
                <?php $totalDeduction += $deduction->value ?>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

    <table class="table-detail" style="margin-top: 30px;">
        <tfoot>
            <tr>
                <th class="text-left">TAKE HOME PAY</th>
                <th class="text-right">Rp {{ number_format($totalDailyMoney + $totalOvertimePay - $totalDeduction, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>