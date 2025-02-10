<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Division;
use App\Models\Employee;
use App\Models\SalaryComponent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThrController extends Controller
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
        $companyId = request()->query('company');
        $divisionId = request()->query('division');
        $staffOnly = request()->query('staff_only') ?? 'false';
        $staffOnly = $staffOnly == 'true' ? true : false;

        $year = request()->query('year') ?? date('Y');
        $previousYear = (int) $year - 1;

        $user = Auth::user();
        $haveStaffPermission = ($user->have_staff_permission ?? 0) == 0 ? false : true;

        $salaryComponents = SalaryComponent::with(['employees'])->get();
        // return date('Y-m-t', strtotime($year . '-12-01'));
        // $currentDate = date('Y-m-d');
        // $employees = Employee::with(['salaryComponents' => function ($q) use ($currentDate) {
        //     $q->wherePivot('effective_date', '<=', $currentDate)->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
        // }, 'office.division', 'activeCareer.jobTitle']);
        $employees = Employee::with([
            'salaryComponents' => function ($q) use ($year) {
                $q->whereRaw('employee_salary_component.effective_date <= ' . '"' . date('Y-m-t', strtotime($year . '-12-01')) . '"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                // $q->whereRaw('employee_salary_component.effective_date >= ' . '"' . ($year - 1) . '-01-01" AND employee_salary_component.effective_date <= ' . '"' . $year . '-01-01"')->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                // $q->whereRaw('employee_salary_component.effective_date < ' . '"' . ($year + 1) . '-01-01"')
                //     ->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
                // $q->wherePivot('effective_date', '<', date(($year + 1) . '-01-01'))
                //     ->orderBy('effective_date', 'desc')->orderBy('id', 'desc');
            },
            'office.division',
            'activeCareer.jobTitle'
        ]);

        // return $employees->get();
        // return $employees->toSql();

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

        // return $employees = $employees->get()->take(3);

        $employees = $employees->where('active', 1)->get()
            ->each(function ($employee) use ($year) {
                $lengthOfWorking = str_replace("sebelumnya", "", Carbon::parse($employee->start_work_date)->diffForHumans(date('Y-m-d'), ['parts' => 4]));
                $employee->length_of_working = $lengthOfWorking;

                $diffStartWorkDateToDate = Carbon::parse($employee->start_work_date)->diffInMonths(date('Y-m-d'));
                $employee->diff_start_work_to_date = $diffStartWorkDateToDate;

                // Gaji Pokok, Harian, Tunjangan, Lembur,
                $salaryComponentTypes = ['gaji_pokok', 'tunjangan', 'uang_harian', 'thr'];

                $gajiPokokValue = 0;
                $tunjanganValue = 0;

                $isThrSaved = true;
                foreach ($salaryComponentTypes as $salaryComponentType) {
                    // $salaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->filter(function ($salaryComponent) use ($year) {
                    //     return $salaryComponent->pivot->effective_date == $year . '-01-01';
                    // })->first();
                    $salaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $salaryComponentValue = 0;
                    $salaryComponentNewValue = 0;
                    $salaryComponentCoefficient = 1;

                    if (isset($salaryComponent)) {
                        $salaryComponentValue = $salaryComponent->pivot->amount ?? 0;
                        $salaryComponentNewValue = $salaryComponent->pivot->amount ?? 0;
                        $salaryComponentCoefficient = $salaryComponent->pivot->coefficient ?? 1;
                    }

                    if ($salaryComponentType == 'gaji_pokok') {
                        $gajiPokokValue = $salaryComponentValue;
                    }

                    if ($salaryComponentType == 'tunjangan') {
                        $tunjanganValue = $salaryComponentValue;
                    }

                    if ($salaryComponentType == 'thr') {
                        $previousYearSalaryComponent = collect($employee->salaryComponents)->where('salary_type', $salaryComponentType)->filter(function ($salaryComponent) use ($year) {
                            return $salaryComponent->pivot->effective_date == ($year - 1) . '-01-01';
                        })->first();

                        $salaryComponentValue = $previousYearSalaryComponent->pivot->amount ?? 0;
                        $salaryComponentNewValue = $salaryComponent->pivot->amount ?? 0;

                        if (!isset($salaryComponent)) {
                            if ($diffStartWorkDateToDate >= 3 && $diffStartWorkDateToDate < 12) {
                                $salaryComponentNewValue = round(($gajiPokokValue / 12) * $diffStartWorkDateToDate);
                            } else if ($diffStartWorkDateToDate >= 12) {
                                $salaryComponentNewValue = $gajiPokokValue + $tunjanganValue;
                            }
                            $isThrSaved = false;
                        }
                    }

                    $employee->{$salaryComponentType . '_value'} = $salaryComponentValue;
                    $employee->{$salaryComponentType . '_coefficient'} = $salaryComponentCoefficient;
                    $employee->{$salaryComponentType . '_new_value'} = $salaryComponentNewValue;
                    $employee->{$salaryComponentType . '_new_coefficient'} = $salaryComponentCoefficient;
                }
                $employee->is_thr_saved = $isThrSaved;
            });

        // return $employees;

        $companies = Company::all();
        $divisions = Division::all();

        // return $employees;

        return view('thr.v2.create', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
            'filter' => [
                'company_id' => $companyId,
                'division_id' => $divisionId,
                'staff_only' => $staffOnly,
            ],
            'have_staff_permission' => $haveStaffPermission,
            'year' => $year,
            'previous_year' => $previousYear,
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
            // $validated = $request->validate([
            //     'effective_date' => 'required|date',
            // ]);

            $employees = $request->employees;
            $employeesIds = collect($request->employees)->pluck('id')->all();
            // $effectiveDate = $request->effective_date . '-01';
            // $effectiveDate = date('Y-m-d');
            // $year = request()->query('year') ?? date('Y');
            $year = $request->year;
            $effectiveDate = $year . '-01-01';

            $salaryComponents = SalaryComponent::all();
            $salaryComponentTypes = ['thr'];
            $salaryComponentTypesIds = SalaryComponent::whereIn('salary_type', $salaryComponentTypes)->get()->pluck('id')->all();
            $employeeSalaryComponents = collect($employees)->flatMap(function ($employee) use ($salaryComponents, $effectiveDate, $salaryComponentTypes) {

                $data = collect($salaryComponentTypes)->map(function ($salaryComponentType) use ($salaryComponents, $employee, $effectiveDate) {
                    $salaryComponent = collect($salaryComponents)->where('salary_type', $salaryComponentType)->first();

                    $amount = $employee[$salaryComponentType . '_new_value'] ?? 0;
                    $coefficient = $employee[$salaryComponentType . '_new_coefficient'] ?? 0;

                    $clearAmount = str_replace('.', '', $amount);
                    // $clearAmount = str_replace(',', '.', $clearAmount);
                    // $clearCoefficient = str_replace('.', '', $coefficient);
                    // $clearCoefficient = str_replace(',', '.', $clearCoefficient);

                    if (isset($salaryComponent)) {
                        return [
                            'employee_id' => $employee['id'],
                            'salary_component_id' => $salaryComponent['id'],
                            'amount' => (int) $clearAmount,
                            // 'coefficient' => (int) $clearCoefficient,
                            'coefficient' => 1,
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

            DB::table('employee_salary_component')->where('effective_date', $effectiveDate)->whereIn('employee_id', $employeesIds)->whereIn('salary_component_id', $salaryComponentTypesIds)->delete();

            // return $employeeSalaryComponents;

            // DB::table('employee_salary_component')->delete();

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
}
