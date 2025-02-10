<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\SalaryComponent;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryIncreaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $currentDate = date('Y-m-d');
        $employees = Employee::with(['salaryComponents' => function ($q) use ($currentDate) {
            $q->wherePivot('effective_date', '<=', $currentDate)->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
        }, 'office.division', 'activeCareer.jobTitle'])
            ->get()
            ->each(function ($employee) {
                // Gaji Pokok, Harian, Tunjangan, Lembur,
                $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];

                foreach ($salaryComponentTypes as $salaryComponentType) {
                    $salaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $salaryComponentValue = 0;
                    $salaryComponentCoefficient = 1;

                    if (isset($salaryComponent)) {
                        $salaryComponentValue = $salaryComponent->pivot->amount ?? 0;
                        $salaryComponentCoefficient = $salaryComponent->pivot->coefficient ?? 1;
                    }

                    $employee->{$salaryComponentType . '_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_coefficient'} = $salaryComponentCoefficient;
                    $employee->{$salaryComponentType . '_new_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_new_coefficient'} = $salaryComponentCoefficient;
                }
            });

        // return $employees[0];

        // return $employees;

        $companies = Company::all();
        $divisions = Division::all();

        return view('salary-increases.maintenance', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexV3()
    {
        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $companies = Company::all();
        $divisions = Division::all();
        $currentDate = date('Y-m-d');
        $year = request()->query('year') ?? date('Y');

        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;


        // $employees = Employee::with(['salaryComponents' => function ($q) use ($currentDate, $year) {
        //     $q->whereRaw('employee_salary_component.effective_date = ' . '"' . ($year - 1) . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
        // }, 'office.division', 'activeCareer.jobTitle']);

        $employees = Employee::with(['salaryComponents' => function ($q) use ($currentDate, $year) {
            $q->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
        }, 'office.division', 'activeCareer.jobTitle']);

        if (isset($companyId) && $companyId !== "") {
            $employees->whereHas('office.division.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if (isset($divisionId) && $divisionId !== "") {
            $employees->whereHas('office.division', function ($q) use ($divisionId) {
                $q->where('id', $divisionId);
            });
        }

        if (!$haveStaffPermission) {
            $employees->where('type', '!=', 'staff');
        } else {
            if ($staffOnly) {
                $employees->where('type', 'staff');
            }
        }

        $employees = $employees->get()
            ->each(function ($employee) {
                // Gaji Pokok, Harian, Tunjangan, Lembur,
                $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];

                foreach ($salaryComponentTypes as $salaryComponentType) {
                    $salaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $salaryComponentValue = 0;
                    $salaryComponentCoefficient = 1;

                    if (isset($salaryComponent)) {
                        $salaryComponentValue = $salaryComponent->pivot->amount ?? 0;
                        $salaryComponentCoefficient = $salaryComponent->pivot->coefficient ?? 1;
                    }

                    $employee->{$salaryComponentType . '_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_coefficient'} = $salaryComponentCoefficient;
                    $employee->{$salaryComponentType . '_new_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_new_coefficient'} = $salaryComponentCoefficient;
                }
            });

        // return $employees[0];

        // return $employees;

        return view('salary-increases.v3.index', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'staff_only' => $staffOnly,
                'year' => $year,
            ],
            'have_staff_permission' => $haveStaffPermission,
        ]);
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
            //     'effective_date' => 'required|date',
            // ]);

            $employees = $request->employees;
            $employeesIds = collect($request->employees)->pluck('id')->all();
            // $effectiveDate = $request->year . '-01-01';
            $effectiveDate = date('Y-m-d');

            $salaryComponents = SalaryComponent::all();
            $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'lembur'];
            $salaryComponentTypesIds = SalaryComponent::whereIn('salary_type', $salaryComponentTypes)->get()->pluck('id')->all();
            $employeeSalaryComponents = collect($employees)->flatMap(function ($employee) use ($salaryComponents, $effectiveDate, $salaryComponentTypes) {

                $data = collect($salaryComponentTypes)->map(function ($salaryComponentType) use ($salaryComponents, $employee, $effectiveDate) {
                    $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $amount = $employee[$salaryComponentType . '_new_value'] ?? 0;
                    $coefficient = $employee[$salaryComponentType . '_new_coefficient'] ?? 0;

                    $increaseTypes = [
                        'gaji_pokok' => 'basic_salary',
                        'tunjangan' => 'allowance',
                        'uang_harian' => 'daily_money',
                        'lembur' => 'overtime',
                    ];


                    $increase = $employee[$increaseTypes[$salaryComponentType] . '_increase'] ?? 0;

                    $clearAmount = str_replace('.', '', $amount);
                    $clearAmount = str_replace(',', '.', $clearAmount);

                    $clearIncrease = str_replace('.', '', $increase);
                    // $clearIncrease = str_replace(',', '.', $clearIncrease);/
                    // $clearCoefficient = str_replace('.', '', $coefficient);
                    // $clearCoefficient = str_replace(',', '.', $clearCoefficient);

                    if (isset($salaryComponent)) {
                        return [
                            'employee_id' => $employee['id'],
                            'salary_component_id' => $salaryComponent['id'],
                            'amount' => $clearAmount + $clearIncrease,
                            'coefficient' => $coefficient,
                            'effective_date' => $effectiveDate,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];
                    }

                    return null;
                })->filter(function ($data) {
                    return isset($data);
                })->values()->all();

                return $data;
            })->all();

            // return $employeeSalaryComponents;

            // DB::table('employee_salary_component')->delete();
            DB::table('employee_salary_component')->where('effective_date', $effectiveDate)->whereIn('employee_id', $employeesIds)->whereIn('salary_component_id', $salaryComponentTypesIds)->delete();

            DB::table('employee_salary_component')->insert($employeeSalaryComponents);

            DB::commit();
            return response()->json([
                'message' => 'Data komponen gaji telah tersimpan',
                'data' => $employeeSalaryComponents,
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

    /**
     * Increase employee's salary.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function increase(Request $request)
    {
        DB::beginTransaction();
        try {
            $employees = $request->employees;
            $employeeSalaryComponents = collect($employees)->flatMap(function ($employee) {
                $components = $employee['salary_components'];
                $data = collect($components)->map(function ($component) use ($employee) {
                    $amount = $component['pivot']['amount'];
                    if (isset($component['addition'])) {
                        $amount += $component['addition'];
                    }
                    return [
                        'employee_id' => $employee['id'],
                        'salary_component_id' => $component['id'],
                        'amount' => $amount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                });
                return $data;
            })->all();

            DB::table('employee_salary_component')->delete();

            DB::table('employee_salary_component')->insert($employeeSalaryComponents);

            DB::commit();
            return response()->json([
                'message' => 'Data komponen gaji telah tersimpan',
                'data' => $employeeSalaryComponents,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
