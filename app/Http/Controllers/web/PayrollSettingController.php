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
use Illuminate\Support\Facades\DB;

class PayrollSettingController extends Controller
{
    /**
     * Display salary component setting
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function component()
    {
        $salaryComponents = SalaryComponent::all();
        return view('settings.payrolls.component', [
            'salary_components' => $salaryComponents,
        ]);
    }

    /**
     * Display salary member setting
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function member()
    {
        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $employees = Employee::all();
        return view('settings.payrolls.member', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
        ]);
    }

    /**
     * Display salary member setting
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function member2()
    {
        // $salaryComponents = SalaryComponent::with(['employees'])->get();
        // $employees = Employee::all();
        // return view('settings.payrolls.member2', [
        //     'salary_components' => $salaryComponents,
        //     'employees' => $employees,
        // ]);
        $salaryComponents = SalaryComponent::with(['employees'])->get();
        $employees = Employee::with(['salaryComponents', 'office.division', 'activeCareer.jobTitle'])->get();
        $companies = Company::all();
        $divisions = Division::all();

        return view('settings.payrolls.member2', [
            'salary_components' => $salaryComponents,
            'employees' => $employees,
            'companies' => $companies,
            'divisions' => $divisions,
        ]);
    }

    /**
     * assign salary member
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignEmployees(Request $request)
    {
        DB::beginTransaction();
        try {
            $salaryComponents = $request->salary_components;
            $employeeSalaryComponents = collect($salaryComponents)->filter(function ($component) {
                return count($component['employees']) > 0;
            })->flatMap(function ($component) {
                $employees = $component['employees'];
                $data = collect($employees)->map(function ($employee) use ($component) {
                    $amount = $employee['pivot']['amount'];
                    $coefficient = null;
                    if (isset($employee['pivot']['coefficient'])) {
                        $coefficient = $employee['pivot']['coefficient'];
                    }
                    return [
                        'employee_id' => $employee['id'],
                        'salary_component_id' => $component['id'],
                        'amount' => $amount,
                        'coefficient' => $coefficient,
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
