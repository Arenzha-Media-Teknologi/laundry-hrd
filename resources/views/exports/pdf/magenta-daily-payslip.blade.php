<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji ({{ $employee->number }}) {{ $employee->name }} Periode {{ \Carbon\Carbon::parse($final_payslip->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date)->isoFormat('LL') }}</title>

    <style>
        body {
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
    @if($final_payslip->type == "regular")
    <div class="header" style="width: 100%;">
        <div style="width: 50%; float: left;">
            <h1 style="margin-bottom: 0; color: #E91E63">PT. Magenta Mediatama</h1>
            <p style="margin-top: 0; width: 80%;">Jl. Raya Kby. Lama No.15, RT.4/RW.3, Grogol Utara, Kec. Kby. Lama, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 11540</p>
        </div>
        <div style="width: 50%; float: right;" class="text-right">
            <!--<img src="https://karir-production.nos.jkt-1.neo.id/logos/11/1029111/unilabel_magenta.png" alt="LOGO PT. MAGENTA MEDIATAMA" width="250" height="100">-->
        </div>
    </div>
    @elseif($final_payslip->type == "aerplus")
    <div class="header" style="width: 100%;">
        <div style="width: 50%; float: left;">
            <h1 style="margin-bottom: 0; color: #E91E63">CV. Amerta Ekakarsa Radhika</h1>
            <!--<p style="margin-top: 0; width: 80%;">Jl. Raya Kby. Lama No.15, RT.4/RW.3, Grogol Utara, Kec. Kby. Lama, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 11540</p>-->
        </div>
        <div style="width: 50%; float: right;" class="text-right">
            <!--<img src="https://karir-production.nos.jkt-1.neo.id/logos/11/1029111/unilabel_magenta.png" alt="LOGO PT. MAGENTA MEDIATAMA" width="250" height="100">-->
        </div>
    </div>
    @endif

    <div class="title text-center" style="clear: both;">
        <h2 style="margin-bottom: 0;">Slip Gaji</h2>
        <h3 style="margin-top: 0;">Periode {{ \Carbon\Carbon::parse($final_payslip->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date)->isoFormat('LL') }}</h3>
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
                    <td>{{ $employee->number }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->activeCareer->jobTitle->designation->department->name ?? '' }}</td>
                    <td>{{ $employee->activeCareer->jobTitle->designation->name ?? '' }}</td>
                    <td>{{ $employee->activeCareer->jobTitle->name ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="table-detail">
        <thead>
            <tr class="text-center">
                <td style="width: 10%;">Tanggal</td>
                <td style="width: 10%;">Hari</td>
                <td class="text-center" style="width: 10%;">Status</td>
                <td class="text-center" style="width: 10%;">Masuk</td>
                <td class="text-center" style="width: 10%;">Pulang</td>
                <td class="text-center" style="width: 10%;">Lembur</td>
                <td class="text-center" style="width: 5%;">Keterlambatan (Menit)</td>
                <td class="text-right" style="width: 12%;">Gaji Harian</td>
                <td class="text-right" style="width: 11%;">Uang Lembur</td>
                <td class="text-right" style="width: 12%;">Total</td>
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
            @foreach($final_payslip->incomes as $income)
            <tr>
                <td>{{ date_format(date_create($income->date), "d/m/Y") }}</td>
                <td>
                    <?php $eventCalendars = $income->event_calendars ?? $income->attendance->event_calendars ?? []; ?>
                    @if($income?->attendance?->working_pattern_day == 'holiday' || count($eventCalendars) > 0)
                    <span style="color: red;">
                        {{ $weekBahasa[\Carbon\Carbon::parse($income->date)->dayOfWeek] }}
                    </span>
                    @else
                    <span>
                        {{ $weekBahasa[\Carbon\Carbon::parse($income->date)->dayOfWeek] }}
                    </span>
                    @endif
                </td>
                <td class="text-center" style="text-transform: uppercase;">{{ $income?->attendance?->status }}</td>
                @if($income->attendance !== null)
                <td class="text-center">{{ $income->attendance->clock_in_time }}</td>
                <td class="text-center">{{ $income->attendance->clock_out_time }}</td>
                <td class="text-center">{{ $income->attendance->overtime }}</td>
                @if($income->attendance->time_late > 0)
                <td class="text-center">{{ number_format($income->attendance->time_late, 0, ",", ".") }}</td>
                @else
                <td class="text-center"></td>
                @endif
                <td class="text-right">{{ number_format($income->daily_wage, 0, ",", ".") }}</td>
                <td class="text-right">{{ number_format($income->overtime_pay, 0, ",", ".") }}</td>
                <td class="text-right">{{ number_format($income->total, 0, ",", ".") }}</td>
                <?php
                $totalDailyMoney += $income->daily_wage;
                $totalOvertimePay += $income->overtime_pay;
                $totalMinutesOfDelay += ($income->attendance->time_late > 0 ? $income->attendance->time_late : 0);
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
    @if(count($event_calendars) > 0)
    <!-- <h4 style="margin-top: 30px;">Kalender</h4>
    <ul>
        @foreach($event_calendars as $event)
        <li>{{ $event->date }} - {{ $event->name }}</li>
        @endforeach
    </ul> -->
    @endif

    <h4 style="margin-top: 30px;">Pendapatan Lainnya</h4>
    <table class="table-detail" style="margin-top: 10px;">
        <thead>
            <tr>
                <td>Nama</td>
                <td class="text-right">Jumlah</td>
            </tr>
        </thead>
        <tbody>
            <?php $totalAdditionalIncome = 0; ?>
            @if(is_array($final_payslip->additional_incomes))
            @foreach($final_payslip->additional_incomes as $additional_income)
            <tr>
                <td>{{ $additional_income->name }}</td>
                <td class="text-right">{{ number_format($additional_income->value ?? 0, 0, ",", ".") }}</td>
                <?php $totalAdditionalIncome += $additional_income->value ?>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th class="text-left">TOTAL</th>
                <th class="text-right">Rp {{ number_format($totalAdditionalIncome, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>

    <h4 style="margin-top: 30px;">Potongan</h4>
    <table class="table-detail" style="margin-top: 10px;">
        <thead>
            <tr>
                <td>Nama</td>
                <td class="text-right">Jumlah</td>
            </tr>
        </thead>
        <tbody>
            <?php $totalDeduction = 0; ?>
            @if(is_array($final_payslip->deductions))
            @foreach($final_payslip->deductions as $deduction)
            <tr>
                <td>{{ $deduction->name }}</td>
                <td class="text-right">{{ number_format($deduction->value, 0, ",", ".") }}</td>
                <?php $totalDeduction += $deduction->value ?>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th class="text-left">TOTAL</th>
                <th class="text-right">Rp {{ number_format($final_payslip->total_deductions, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="table-detail" style="margin-top: 30px;">
        <tfoot>
            <tr>
                <th class="text-left">TAKE HOME PAY</th>
                <th class="text-right">Rp {{ number_format($totalDailyMoney + $totalOvertimePay + $totalAdditionalIncome - $totalDeduction, 0, ",", ".") }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>