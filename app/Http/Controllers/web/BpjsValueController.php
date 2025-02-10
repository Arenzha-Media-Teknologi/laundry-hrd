<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\BpjsValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpjsValueController extends Controller
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
            // $validated = $request->validate([
            //     'name' => 'required|unique:companies|max:255',
            //     'initial' => 'required|max:10',
            //     'address' => 'max:255',
            // ]);

            // $company = new Company();
            // $company->name = ucwords($request->name);
            // $company->initial = strtoupper($request->initial);
            // $company->address = $request->address;
            // $company->npwp_number = $request->npwp_number;
            // $company->npwp_address = $request->npwp_address;
            // $company->president_director = $request->president_director;
            // $company->director = $request->director;
            // $company->save();

            $employees = $request->employees;
            $currentYear = $request->current_year;

            $values = collect($employees)->map(function ($employee) use ($currentYear) {
                return [
                    'year' => $currentYear,
                    'jht_payment' => $employee['current_year_value']['jht_payment'] ?? 0,
                    'jkk_payment' => $employee['current_year_value']['jkk_payment'] ?? 0,
                    'jkm_payment' => $employee['current_year_value']['jkm_payment'] ?? 0,
                    'jp_payment' => $employee['current_year_value']['jp_payment'] ?? 0,
                    'jht' => $employee['current_year_value']['jht'] ?? 0,
                    'jkk' => $employee['current_year_value']['jkk'] ?? 0,
                    'jkm' => $employee['current_year_value']['jkm'] ?? 0,
                    'jp' => $employee['current_year_value']['jp'] ?? 0,
                    'employee_id' => $employee['id'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->values()->all();

            $employeesIds = collect($employees)->pluck('id')->all();

            DB::table('bpjs_values')->where('year', $currentYear)->whereIn('employee_id', $employeesIds)->delete();

            // $newBpjsValues = BpjsValue::create($values);
            DB::table('bpjs_values')->insert($values);

            DB::commit();
            return response()->json([
                'message' => 'Nilai asuransi telah tersimpan',
                // 'data' => $newBpjsValues,
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
        //
    }
}
