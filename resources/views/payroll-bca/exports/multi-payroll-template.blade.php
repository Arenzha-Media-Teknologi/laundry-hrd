<table>
    <thead>
        <tr>
            <td>No</td>
            <td>Transaction ID</td>
            <td>Transfer Type</td>
            <td>Beneficiary ID</td>
            <td>Credited Account</td>
            <td>Receiver Name</td>
            <td>Amount</td>
            <td>NIP</td>
            <td>Remark</td>
            <td>Benefeciary email address</td>
            <td>Receiver Swift Code</td>
            <td>Receiver Cust Type</td>
            <td>Receiver Cust Residence</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $usedRemarkNames = [];
        $secondName = [];
        ?>
        @foreach($employees as $employee)
        <?php
        $tryedNames = [];
        $joinedName = preg_replace('/\PL/u', '', str_replace(" ", "", $employee->credited_account ?? ""));
        $remark = "";
        // ["muly", "muha"]
        if (strlen($joinedName) > 4) {
            $willRemoveCharIndex = 3;
            $tryIteration = 1;
            do {
                $remark = substr($joinedName, 0, 4);
                $joinedName = substr_replace($joinedName, "", $willRemoveCharIndex, 1);
                array_push($tryedNames, $willRemoveCharIndex . ' - ' . $joinedName);

                if ($willRemoveCharIndex > 10) {
                    break;
                }

                $tryIteration++;
                // 3 + 1 = 4
                // ----
                // 4 + 1 = 5
            } while (
                // false
                in_array($remark, $usedRemarkNames)
                // true
                // ----
            );
        } else {
            $remark = substr($joinedName, 0, 4);
        }
        array_push($usedRemarkNames, $remark);

        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
        $remark = "HON" . strtoupper($remark) . ($months[(\Carbon\Carbon::parse($end_date)->month) - 1]) . \Carbon\Carbon::parse($end_date)->format('y');

        $amount = $employee->amount > 0 ? $employee->amount : 0;
        ?>
        <tr>
            <td></td>
            <td>{{ $employee->transaction_id }}{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT); }}</td>
            <td>{{ $employee->transfer_type }}</td>
            <td></td>
            <td>{{ $employee->bank_account_number }}</td>
            <td>{{ strtoupper($employee->credited_account) }}</td>
            <td data-format="#,##0.00">{{ $amount }}</td>
            <td></td>
            <td>{{ $remark }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>