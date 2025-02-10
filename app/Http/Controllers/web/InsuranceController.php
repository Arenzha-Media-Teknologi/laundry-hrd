<?php

namespace App\Http\Controllers\web;

use App\Exports\insurances\InsuranceExport;
use App\Http\Controllers\Controller;
use App\Models\BpjsValue;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Insurance;
use App\Models\PrivateInsurance;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceController extends Controller
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
        $employees = Employee::with(['insurances', 'activeCareer.jobTitle', 'office.division.company'])->get();
        $companies = Company::all();
        $divisions = Division::all();

        // return $employees;

        // $yearRange = DB::table('insurances')
        //     ->selectRaw('MIN(year) AS min_year, MAX(year) AS max_year')
        //     ->first();
        $minYear = DB::table('insurances')
            ->selectRaw('MIN(year) AS min_year')
            ->first()->min_year;

        return view('insurances.create', [
            'min_year' => $minYear,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
        ]);
    }

    public function createV2(Request $request)
    {
        // $companies = Company::with(['presidentDirector', 'director'])->get();
        // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();
        // return view('companies.index2', [
        //     'companies' => $companies,
        //     'employees' => $employees,
        // ]);

        $typeQuery = $request->query('type');
        $year = $request->query('year');
        if ($typeQuery == null || $year == null) {
            abort(404);
        }

        $explodedTypeQuery = explode('_', $typeQuery);
        $insuranceType = $explodedTypeQuery[0];
        $insuranceId = $explodedTypeQuery[1];

        $privateInsurances = PrivateInsurance::all();

        if ($insuranceType == 'bpjs') {
            // $companies = Company::with(['presidentDirector', 'director'])->get();
            // $employees = Employee::with(['office.division.company', 'activeCareer.jobTitle'])->get();

            if ($insuranceId !== 'ketenagakerjaan' && $insuranceId !== 'mandiri') {
                abort(404);
            }

            $yearRange = DB::table('bpjs_values')
                ->selectRaw('MIN(year) AS min_year, MAX(year) AS max_year')
                ->first();

            $minYear = $yearRange->min_year;
            $maxYear = $yearRange->max_year;

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            $employees = Employee::query()
                ->whereHas('bpjs', function ($q) use ($insuranceId) {
                    $field = 'ketenagakerjaan_number';
                    if ($insuranceId == 'mandiri') {
                        $field = 'mandiri_number';
                    }
                    $q->where($field, '!=', null);
                })->with(['bpjs', 'bpjsValues' => function ($q) use ($previousYear, $currentYear) {
                    // $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                    $q->where('year', $currentYear)->orderBy('id', 'desc');
                }])->get()
                ->lazy()
                ->map(function ($employee) use ($previousYear, $currentYear) {
                    $previousYearBpjsValue = collect($employee->bpjsValues)->where('year', $previousYear)->first();
                    $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();
                    $employee->previous_year_value = [
                        'jht' => $previousYearBpjsValue->jht ?? 0,
                        'jkk' => $previousYearBpjsValue->jkk ?? 0,
                        'jkm' => $previousYearBpjsValue->jkm ?? 0,
                        'jp' => $previousYearBpjsValue->jp ?? 0,
                    ];
                    $employee->current_year_value = [
                        'jht_payment' => $currentYearBpjsValue->jht_payment ?? '',
                        'jkk_payment' => $currentYearBpjsValue->jkk_payment ?? '',
                        'jkm_payment' => $currentYearBpjsValue->jkm_payment ?? '',
                        'jp_payment' => $currentYearBpjsValue->jp_payment ?? '',
                        'jht' => $currentYearBpjsValue->jht ?? '',
                        'jkk' => $currentYearBpjsValue->jkk ?? '',
                        'jkm' => $currentYearBpjsValue->jkm ?? '',
                        'jp' => $currentYearBpjsValue->jp ?? '',
                    ];
                    return $employee;
                })->all();
            // $insurances = BpjsValue::where with(['employee' => function($q) {
            //     $q->with(['bpjs'])
            // }])
            // return $employees;

            return view('insurances.create.bpjs', [
                'previous_year' => $previousYear,
                'current_year' => $currentYear,
                'employees' => $employees,
                'private_insurances' => $privateInsurances,
                'bpjs_id' => $insuranceId,
                'years' => [
                    'min_year' => $minYear,
                    'max_year' => $maxYear,
                ]
                // 'companies' => $companies,
                // 'employees' => $employees,
            ]);
        }

        if ($insuranceType == 'private') {
            if (!isset($insuranceId)) {
                abort(404);
            }

            $privateInsurance = PrivateInsurance::find($insuranceId);

            if ($privateInsurance == null) {
                abort(404);
            }

            $yearRange = DB::table('private_insurance_values')
                ->selectRaw('MIN(year) AS min_year, MAX(year) AS max_year')
                ->first();

            $minYear = $yearRange->min_year;
            $maxYear = $yearRange->max_year;

            $previousYear = ((int) $year) - 1;
            $currentYear = $year;

            $employees = Employee::query()
                ->whereHas('privateInsurances', function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                })->with(['privateInsurances' => function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                }, 'privateInsuranceValues' => function ($q) use ($previousYear, $currentYear) {
                    $q->whereBetween('year', [$previousYear, $currentYear])->orderBy('id', 'desc');
                }])->get()
                ->lazy()
                ->map(function ($employee) use ($previousYear, $currentYear) {
                    $previousYearValue = collect($employee->privateInsuranceValues)->where('year', $previousYear)->first();
                    $currentYearValue = collect($employee->privateInsuranceValues)->where('year', $currentYear)->first();

                    $currentPrivateInsurance = collect($employee->privateInsurances)->first();

                    $employee->current_private_insurance = $currentPrivateInsurance;

                    $employee->previous_year_value = [
                        'total_premi' => $previousYearValue->total_premi ?? 0,
                        'kesehatan' => $previousYearValue->kesehatan ?? 0,
                        'nilai_tabungan' => $previousYearValue->nilai_tabungan ?? 0,
                    ];
                    $employee->current_year_value = [
                        'total_premi' => $currentYearValue->total_premi ?? '',
                        'kesehatan' => $currentYearValue->kesehatan ?? '',
                        'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? '',
                    ];
                    return $employee;
                })->all();
            // $insurances = BpjsValue::where with(['employee' => function($q) {
            //     $q->with(['bpjs'])
            // }])
            // return $employees;

            return view('insurances.create.private', [
                'previous_year' => $previousYear,
                'current_year' => $currentYear,
                'employees' => $employees,
                'current_private_insurance' => $privateInsurance,
                'private_insurances' => $privateInsurances,
                'bpjs_id' => $insuranceId,
                'years' => [
                    'min_year' => $minYear,
                    'max_year' => $maxYear,
                ]
                // 'companies' => $companies,
                // 'employees' => $employees,
            ]);
        }
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
            $employees = $request->employees;
            $year = $request->year;

            $employeesIds = collect($employees)->pluck('id');

            $insurances = collect($employees)->map(function ($employee) use ($year) {
                return [
                    'year' => $year,
                    'health' => $employee['new_health'],
                    'retirement' => $employee['new_retirement'],
                    'employee_id' => $employee['id'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            })->all();

            Insurance::query()->where('year', $year)->whereIn('employee_id', $employeesIds)->delete();

            DB::table('insurances')->insert($insurances);

            DB::commit();
            // return response()->json([
            //     'message' => 'Data komponen gaji telah tersimpan',
            //     'data' => $insurances,
            // ]);
            return response()->json([
                'message' => 'Data komponen gaji telah tersimpan',
                'data' => $insurances,
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

    /**
     * Export report
     */
    public function exportReport(Request $request)
    {
        try {
            $year = $request->query('year');

            if ($year == null) {
                throw new Error('params "year" are required');
            }

            $currentYear = $year;
            // $employeesBpjsKetenagakerjaan = Employee::query()
            //     ->whereHas('bpjs', function ($q) {
            //         $field = 'ketenagakerjaan_number';
            //         // if ($insuranceId == 'mandiri') {
            //         //     $field = 'mandiri_number';
            //         // }
            //         $q->where($field, '!=', null);
            //     })
            //     ->with(['bpjs', 'bpjsValues' => function ($q) use ($currentYear) {
            //         $q->where('year', $currentYear)->orderBy('id', 'desc');
            //     }])->get()
            //     ->lazy()
            //     ->map(function ($employee) use ($currentYear) {
            //         $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();
            //         $employee->current_year_value = [
            //             'jht_payment' => $currentYearBpjsValue->jht_payment ?? '',
            //             'jkk_payment' => $currentYearBpjsValue->jkk_payment ?? '',
            //             'jkm_payment' => $currentYearBpjsValue->jkm_payment ?? '',
            //             'jp_payment' => $currentYearBpjsValue->jp_payment ?? '',
            //             'jht' => $currentYearBpjsValue->jht ?? '',
            //             'jkk' => $currentYearBpjsValue->jkk ?? '',
            //             'jkm' => $currentYearBpjsValue->jkm ?? '',
            //             'jp' => $currentYearBpjsValue->jp ?? '',
            //         ];
            //         return $employee;
            //     })->all();

            // $employeesBpjsMandiri = Employee::query()
            //     ->whereHas('bpjs', function ($q) {
            //         $field = 'mandiri_number';
            //         // if ($insuranceId == 'mandiri') {
            //         //     $field = 'mandiri_number';
            //         // }
            //         $q->where($field, '!=', null);
            //     })
            //     ->with(['bpjs'])
            //     ->get();

            // $privateInsurances = PrivateInsurance::all()->each(function ($privateInsurance) use ($currentYear) {
            //     $insuranceId = $privateInsurance->id;
            //     $employeesPrivateInsurance = Employee::query()
            //         ->whereHas('privateInsurances', function ($q) use ($insuranceId) {
            //             $q->where('private_insurances.id', $insuranceId);
            //         })->with(['privateInsurances' => function ($q) use ($insuranceId) {
            //             $q->where('private_insurances.id', $insuranceId);
            //         }, 'privateInsuranceValues' => function ($q) use ($currentYear) {
            //             $q->where('year', $currentYear)->orderBy('id', 'desc');
            //         }])->get()
            //         ->lazy()
            //         ->map(function ($employee) use ($currentYear) {
            //             $currentYearValue = collect($employee->privateInsuranceValues)->where('year', $currentYear)->first();

            //             $currentPrivateInsurance = collect($employee->privateInsurances)->first();

            //             $employee->current_private_insurance = $currentPrivateInsurance;

            //             $employee->current_year_value = [
            //                 'total_premi' => $currentYearValue->total_premi ?? 0,
            //                 'kesehatan' => $currentYearValue->kesehatan ?? 0,
            //                 'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? 0,
            //             ];
            //             return $employee;
            //         })->all();
            //     $privateInsurance->member = $employeesPrivateInsurance;
            // });

            // return $privateInsurances;

            return Excel::download(new InsuranceExport($year), 'Laporan Asuransi Tahun ' . $year . '.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
