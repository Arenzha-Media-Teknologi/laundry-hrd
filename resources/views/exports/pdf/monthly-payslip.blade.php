<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji ({{ $employee->number }}) {{ $employee->name }} Periode {{ \Carbon\Carbon::parse($final_payslip->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date)->isoFormat('LL') }}</title>

    <style>
        html {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
        }

        .table-income,
        .table-deduction,
        .table-thp,
        .table-employee {
            width: 100%;
            border-collapse: collapse;
        }

        .table-deduction,
        .table-thp {
            margin-top: 20px;
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
            font-size: 11px;
        }

        .table-income thead td,
        .table-deduction thead td {
            background-color: #E0E0E0;
            border-bottom: 1px solid #9E9E9E;
            padding: 0.4rem 0.3rem;
            text-transform: uppercase;
            font-size: 11px;
            /* font-size: 0.8rem; */
        }

        .table-income tfoot th,
        .table-deduction tfoot th,
        .table-thp tbody th {
            background-color: #E0E0E0;
            border-bottom: 1px solid #9E9E9E;
            padding: 0.4rem 0.3rem;
            font-size: 11px;
        }

        .table-income tbody td,
        .table-deduction tbody td {
            border-bottom: 1px solid #9E9E9E;
            font-size: 11px;
            padding: 0.6rem 0.3rem;
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
            <h1 style="margin-bottom: 0; color: #E91E63; font-size: 1rem">PT. Magenta Mediatama</h1>
            <p style="margin-top: 0; width: 80%; font-size: 11px">Jl. Raya Kby. Lama No.15, RT.4/RW.3, Grogol Utara, Kec. Kby. Lama, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 11540</p>
        </div>
        <!-- <div style="width: 50%; float: right;" class="text-right">
            <img src="https://karir-production.nos.jkt-1.neo.id/logos/11/1029111/unilabel_magenta.png" alt="LOGO PT. MAGENTA MEDIATAMA" width="250" height="100">
        </div> -->
    </div>

    <div class="title text-center" style="clear: both; ">
        <h2 style="margin-bottom: 0; font-size: 1.2rem">Slip Gaji</h2>
        <h3 style="margin-top: 0; font-size: 1rem">Periode {{ \Carbon\Carbon::parse($final_payslip->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($final_payslip->end_date)->isoFormat('LL') }}</h3>
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

    <?php
    $totalIncome = 0;
    $totalDeduction = 0;
    ?>
    <div class="table-income-container">
        <table class="table-income">
            <thead>
                <tr>
                    <td class="text-left">Komponen Pendapatan</td>
                    <td class="text-right">Jumlah (<span style="text-transform: capitalize;">Rp</span>)</td>
                </tr>
            </thead>
            <tbody>
                <?php $incomes = collect($final_payslip->items)->where('type', 'income')->sortByDesc('amount')->all(); ?>
                @foreach($incomes as $income)
                <tr>
                    <td>{{ $income->name }}</td>
                    <td class="text-right">{{ number_format($income->amount, 0, ",", ".") }}</td>
                </tr>
                <?php $totalIncome += $income->amount ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-left">Total Pendapatan</th>
                    <th class="text-right">{{ number_format($totalIncome, 0, ",", ".") }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="table-deduction-container">
        <table class="table-deduction">
            <thead>
                <tr>
                    <td class="text-left">Komponen Potongan</td>
                    <td class="text-right">Jumlah (<span style="text-transform: capitalize;">Rp</span>)</td>
                </tr>
            </thead>
            <tbody>
                <?php $deductions = collect($final_payslip->items)->where('type', 'deduction')->sortByDesc('amount')->all(); ?>
                @foreach($deductions as $deduction)
                <tr>
                    <td>{{ $deduction->name }}</td>
                    <td class="text-right">{{ number_format($deduction->amount, 0, ",", ".") }}</td>
                </tr>
                <?php $totalDeduction += $deduction->amount ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-left">Total Potongan</th>
                    <th class="text-right">{{ number_format($totalDeduction, 0, ",", ".") }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="table-thp-container">
        <table class="table-thp">
            <tbody>
                <tr>
                    <th class="text-left">Take Home Pay</th>
                    <th class="text-right">Rp {{ number_format(($totalIncome - $totalDeduction), 0, ",", ".") }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>