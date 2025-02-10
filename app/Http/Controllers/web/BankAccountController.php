<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $bankName = $request->bank_name;
            $accountNumber = $request->account_number;
            $accountOwner = $request->account_owner;
            $employeeId = $request->employee_id;
            $default = $request->default;

            $validated = $request->validate([
                'bank_name' => 'required|max:255',
                'account_owner' => 'required|max:255',
                'account_number' => 'required|max:255',
                'employee_id' => 'required|integer',
            ]);

            if ($default) {
                DB::table('bank_accounts')->where('employee_id', $employeeId)->update(['default' => false]);
            }

            $bankAccount = new BankAccount();
            $bankAccount->bank_name = ucwords($bankName);
            $bankAccount->account_owner = ucwords($accountOwner);
            $bankAccount->account_number = ucwords($accountNumber);
            $bankAccount->default = $default;
            $bankAccount->employee_id = $employeeId;
            $bankAccount->save();

            DB::commit();


            $bankAccountsAll = BankAccount::query()->where('employee_id', $employeeId)->get();

            return response()->json([
                'message' => 'Rekening bank telah tersimpan',
                'data' => $bankAccountsAll,
            ]);
        } catch (Exception $e) {
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        DB::beginTransaction();
        try {
            $bankName = $request->bank_name;
            $accountNumber = $request->account_number;
            $accountOwner = $request->account_owner;
            $default = $request->default;
            $employeeId = $request->employee_id;

            // return response()->json(['message' => $default]);

            $validated = $request->validate([
                'bank_name' => 'required|max:255',
                'account_owner' => 'required|max:255',
                'account_number' => 'required|max:255',
                'employee_id' => 'required|integer',
            ]);

            if ($default) {
                DB::table('bank_accounts')->where('employee_id', $employeeId)->update(['default' => false]);
            }

            $bankAccount = BankAccount::find($id);
            $bankAccount->bank_name = ucwords($bankName);
            $bankAccount->account_owner = ucwords($accountOwner);
            $bankAccount->account_number = ucwords($accountNumber);
            $bankAccount->default = $default;
            $bankAccount->employee_id = $employeeId;
            $bankAccount->save();

            DB::commit();

            $bankAccountsAll = BankAccount::query()->where('employee_id', $employeeId)->get();

            return response()->json([
                'message' => 'Rekening bank telah tersimpan',
                'data' => $bankAccountsAll,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
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
            $bankAccount = BankAccount::findOrFail($id);
            $bankAccount->delete();
            return response()->json([
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Data gagal dihapus: ' . $e->getMessage(),
            ], 500);
        }
    }
}
