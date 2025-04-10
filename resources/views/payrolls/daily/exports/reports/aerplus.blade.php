<?php
$columnsCount = 12;
?>
<table>
    <tbody>
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong>Rekapitulasi Gaji Harian AerPlus</strong></td>
        </tr>
        <tr>
            <td colspan="{{ $columnsCount }}" style="text-align: center;"><strong>Periode {{\Carbon\Carbon::parse($start_date)->format('d/m/Y')}} - {{\Carbon\Carbon::parse($end_date)->format('d/m/Y')}}</strong></td>
        </tr>
        <?php
        $total = [
            'allowance' => 0,
            'redeem_deposit' => 0,
            'late_fee' => 0,
            'deposit' => 0,
            'loan' => 0,
            'daily' => 0,
            'take_home_pay' => 0,
        ]
        ?>
        @foreach($daily_salaries as $daily_salary)
        <tr style="background-color: grey;">
            <td colspan="{{ $columnsCount }}">{{ $daily_salary['cv_name'] }} | {{ $daily_salary['office'] }}</td>
        </tr>
        <tr>
            <th>NO</th>
            <th>NAMA</th>
            <th>PEMILIK REKENING</th>
            <th>NO. REKENING</th>
            <th>HARIAN</th>
            <th>TUNJANGAN</th>
            <th>PENGEMBALIAN DEPOSIT</th>
            <th>DEPOSIT</th>
            <th>KASBON</th>
            <th>DENDA</th>
            <th colspan="2">TRANSFER</th>
        </tr>
        <?php $isSameBankAccount = false; ?>
        <?php $transferColSpan = 1; ?>
        <?php
        $subtotal = [
            'allowance' => 0,
            'redeem_deposit' => 0,
            'late_fee' => 0,
            'deposit' => 0,
            'loan' => 0,
            'daily' => 0,
            'take_home_pay' => 0,
        ]
        ?>
        @foreach($daily_salary['employees'] as $index => $employee)
        <?php
        $additionalIncomes = json_decode($employee['additional_incomes'], true);
        $deductions = json_decode($employee['deductions'], true);
        $allowance = collect($additionalIncomes)->where('type', 'tunjangan_harian')->sum('value');
        $redeemDeposit = collect($additionalIncomes)->where('type', 'redeem_deposit')->sum('value');
        $lateFee = collect($deductions)->where('type', 'late_fee')->sum('value');
        $deposit = collect($deductions)->where('type', 'deposit')->sum('value');
        $loan = collect($deductions)->where('type', 'loan')->sum('value');
        $daily = $employee['total_daily'];
        // 4 = 10 - (3 + 3)
        ?>
        <tr>
            <td style="text-align: center">{{ $index + 1 }}</td>
            <td>{{ $employee['name'] }}</td>
            <td>{{ $employee['bank_account_owner'] }}</td>
            <td>{{ $employee['bank_account_number'] }}</td>
            <td data-format="#,##0_-">{{ $daily }}</td>
            <td data-format="#,##0_-">{{ $allowance }}</td>
            <td data-format="#,##0_-">{{ $redeemDeposit }}</td>
            <td data-format="#,##0_-">{{ $deposit }}</td>
            <td data-format="#,##0_-">{{ $loan }}</td>
            <td data-format="#,##0_-">{{ $lateFee }}</td>
            <td data-format="#,##0_-">{{ $employee['take_home_pay'] }}</td>
            <!-- IMPORTANT -->
            <td></td>
        </tr>
        <?php
        $subtotal['allowance'] += $allowance;
        $subtotal['redeem_deposit'] += $redeemDeposit;
        $subtotal['late_fee'] += $lateFee;
        $subtotal['deposit'] += $deposit;
        $subtotal['loan'] += $loan;
        $subtotal['daily'] += $daily;
        $subtotal['take_home_pay'] += $employee['take_home_pay'];
        ?>
        @endforeach
        <tr>
            <td colspan="4"><strong>SUBTOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['daily'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['allowance'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['redeem_deposit'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['deposit'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['loan'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['late_fee'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $subtotal['take_home_pay'] }}</strong></td>
            <!-- IMPORTANT -->
            <td></td>
        </tr>
        <tr>
            @for($i = 0 ; $i < $columnsCount; $i++) <td>
                </td>
                @endfor
        </tr>
        <?php
        $total['allowance'] += $subtotal['allowance'];
        $total['redeem_deposit'] += $subtotal['redeem_deposit'];
        $total['late_fee'] += $subtotal['late_fee'];
        $total['deposit'] += $subtotal['deposit'];
        $total['loan'] += $subtotal['loan'];
        $total['daily'] += $subtotal['daily'];
        $total['take_home_pay'] += $subtotal['take_home_pay'];
        ?>
        @endforeach
        <tr>
            <td colspan="4"><strong>TOTAL</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['daily'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['allowance'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['redeem_deposit'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['deposit'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['loan'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['late_fee'] }}</strong></td>
            <td data-format="#,##0_-"><strong>{{ $total['take_home_pay'] }}</strong></td>
            <!-- IMPORTANT -->
            <td></td>
        </tr>
    </tbody>
</table>