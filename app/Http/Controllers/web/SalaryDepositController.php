<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SalaryDeposit;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryDepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposits = SalaryDeposit::query()
            ->withSum(['items' => function ($q) {
                $q->where('paid', 1);
            }], 'amount')
            ->withCount(['items as paid_items_count' => function ($q) {
                $q->where('paid', 1);
            }])
            ->with(['employee', 'items'])->get();

        $totalRedeemedDeposit = SalaryDeposit::query()->where('redeemed', 1)->sum('redeemed_amount');
        $totalUnredeemedDeposit = SalaryDeposit::query()
            ->withSum(['items as unpaid_items' => function ($q) {
                $q->where('paid', 0);
            }], 'amount')->where('redeemed', 0)->get()->sum('unpaid_items');
        $totalDeposit = SalaryDeposit::query()->sum('amount');
        // $totalDeposit = $totalRedeemedDeposit + $totalUnredeemedDeposit;

        $statistic = [
            'total' => $totalDeposit,
            'redeemed' => $totalRedeemedDeposit,
            'unredeemed' => $totalUnredeemedDeposit,
        ];

        return view('salary-deposits.index', [
            'deposits' => $deposits,
            'statistic' => $statistic,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $AERPLUS_DIVISION_ID = 12;
        $employees = Employee::whereHas('office', function ($q) use ($AERPLUS_DIVISION_ID) {
            $q->where('division_id', $AERPLUS_DIVISION_ID);
        })->with(['activeCareer.jobTitle'])->where('active', 1)->get();
        $loanNames = [];

        return view('salary-deposits.create', [
            'min_year' => 2020,
            'employees' => $employees,
            'loan_names' => $loanNames,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $effectiveDate = $request->effective_date;
            $amount = $request->amount;
            $installment = $request->installment;
            $employeeId = $request->employee_id;
            $loanNameId = $request->loan_name_id;
            $description = $request->description;
            $items = $request->items;

            $validated = $request->validate([
                'effective_date' => 'required|date',
                'amount' => 'required|numeric',
                'installment' => 'required|integer',
                'employee_id' => 'required|integer',
            ]);

            // $itemsAmount = collect($items)->sum('paymentAmount');

            // return response()->json([
            //     'date' => [$itemsAmount, $amount],
            // ]);
            // if ($itemsAmount !== (int) $amount) {
            //     throw new Error('CODE ERROR: Total item tidak sama dengan jumlah pinjaman');
            // }

            $salaryDeposit = new SalaryDeposit();
            $salaryDeposit->date = $effectiveDate;
            $salaryDeposit->employee_id = $employeeId;
            $salaryDeposit->amount = $amount;
            // $salaryDeposit->installment = $installment;
            $salaryDeposit->description = $description;
            $salaryDeposit->save();

            $dataItems = collect($items)->map(function ($item, $index) use ($salaryDeposit) {
                return [
                    'salary_deposit_id' => $salaryDeposit->id,
                    // 'installment_order' => $index + 1,
                    'amount' => $item['amount'],
                    'paid' => $item['paid'],
                    'paid_date' => $item['paid'] ? $item['paidDate'] : null,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            DB::table('salary_deposit_items')->insert($dataItems);

            DB::commit();

            return response()->json([
                'message' => 'Data telah tersimpan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $AERPLUS_DIVISION_ID = 12;

        $deposit = SalaryDeposit::with(['items', 'employee'])->findOrFail($id);

        $employees = Employee::whereHas('office', function ($q) use ($AERPLUS_DIVISION_ID) {
            $q->where('division_id', $AERPLUS_DIVISION_ID);
        })->with(['activeCareer.jobTitle'])->get();
        $loanNames = [];

        return view('salary-deposits.detail', [
            'deposit' => $deposit,
            'min_year' => 2020,
            'employees' => $employees,
            'loan_names' => $loanNames,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $AERPLUS_DIVISION_ID = 12;
        $employees = Employee::whereHas('office', function ($q) use ($AERPLUS_DIVISION_ID) {
            $q->where('division_id', $AERPLUS_DIVISION_ID);
        })->with(['activeCareer.jobTitle'])->get();
        $loanNames = [];

        return view('salary-deposits.edit', [
            'min_year' => 2020,
            'employees' => $employees,
            'loan_names' => $loanNames,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $loan = SalaryDeposit::findOrFail($id);
            $loan->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Data gagal dihapus - ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Report
     */
    public function report()
    {
        $deposits = SalaryDeposit::with(['items'])->get();
        return response()->json([
            'deposits' => $deposits,
        ]);
    }

    /**
     * Redeem Deposit
     */
    public function redeemDeposit($id)
    {
        try {
            $deposit = SalaryDeposit::with(['employee'])->withSum(['items as item_paid_amount' => function ($q) {
                $q->where('paid', 1);
            }], 'amount')->find($id);

            $deposit->redeemed = 1;
            $deposit->redeemed_amount = $deposit->item_paid_amount;
            $deposit->redeemed_date = date('Y-m-d');
            $deposit->save();

            return response()->json([
                'message' => 'Deposit berhasil dikembalikan',
                'data' => $deposit,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
