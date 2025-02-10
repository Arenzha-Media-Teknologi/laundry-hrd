<table>
    <thead>
        <tr>
            <th style="text-align: center;"><strong>PERIODE</strong></th>
            <th style="text-align: center;"><strong>NAMA</strong></th>
            <th style="text-align: center;"><strong>UANG HARIAN</strong></th>
            <th style="text-align: center;"><strong>LEMBUR</strong></th>
            <th style="text-align: center;"><strong>DENDA</strong></th>
            <th style="text-align: center;"><strong>JUMLAH</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($final_payslips as $period => $payslips)
        <?php $totalAllDailyMoney = 0; ?>
        <?php $totalAllOvertimePay = 0; ?>
        <?php $totalAllLateFee = 0; ?>
        <?php $totalAllAmount = 0; ?>
        <?php $explodedPeriod = explode(' - ', $period); ?>
        <?php $startDate = $explodedPeriod[0]; ?>
        <?php $endDate = $explodedPeriod[1]; ?>
        @foreach($payslips as $payslip)
        <tr>
            <td>Uang Mingguan Per {{ \Carbon\Carbon::parse($startDate)->isoFormat('ll') }} - {{ \Carbon\Carbon::parse($endDate)->isoFormat('ll') }}</td>
            <td>{{ $payslip->employee->name }}</td>
            <td data-format="#,##0_-"><strong>{{ $payslip->total_daily_money }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $payslip->total_overtime_pay }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $payslip->total_late_fee }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $payslip->amount }}</strong></td>
        </tr>
        <?php $totalAllDailyMoney += $payslip->total_daily_money ?>
        <?php $totalAllOvertimePay += $payslip->total_overtime_pay ?>
        <?php $totalAllLateFee += $payslip->total_late_fee ?>
        <?php $totalAllAmount += $payslip->amount ?>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: center;"><strong>TOTAL</strong></td>
            <td style="text-align: center;" data-format="#,##0_-"><strong>{{ $totalAllDailyMoney }}</strong></td>
            <td style="text-align: center;" data-format="#,##0_-"><strong>{{ $totalAllOvertimePay }}</strong></td>
            <td style="text-align: center;" data-format="#,##0_-"><strong>{{ $totalAllLateFee }}</strong></td>
            <td style="text-align: center;" data-format="#,##0_-"><strong>{{ $totalAllAmount }}</strong></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: center;">Jakarta, {{ \Carbon\Carbon::now()->isoFormat('L') }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Yang Menerima, </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: center;">Dibuat oleh</td>
        </tr>
        @for($i = 0; $i < 3; $i++) <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            </tr>
            @endfor
            <tr>
                <td style="text-align: center;"><strong>{{ $setting->report_receiver ?? "" }}</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;"><strong>{{ $setting->report_maker ?? "" }}</strong></td>
            </tr>
            @endforeach
            @for($i = 0; $i < 2; $i++) <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>
                @endfor
    </tbody>
</table>