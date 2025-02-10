<?php

namespace App\Exports\insurances;

use App\Models\Employee;
use App\Models\PrivateInsurance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InsuranceExport implements FromView
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $currentYear = $this->year;
        $employeesBpjsKetenagakerjaan = Employee::query()
            ->whereHas('bpjs', function ($q) {
                $field = 'ketenagakerjaan_number';
                // if ($insuranceId == 'mandiri') {
                //     $field = 'mandiri_number';
                // }
                $q->where($field, '!=', null);
            })
            ->with(['bpjs', 'bpjsValues' => function ($q) use ($currentYear) {
                $q->where('year', $currentYear)->orderBy('id', 'desc');
            }])->get()
            ->lazy()
            ->map(function ($employee) use ($currentYear) {
                $currentYearBpjsValue = collect($employee->bpjsValues)->where('year', $currentYear)->first();
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

        $employeesBpjsMandiri = Employee::query()
            ->whereHas('bpjs', function ($q) {
                $field = 'mandiri_number';
                // if ($insuranceId == 'mandiri') {
                //     $field = 'mandiri_number';
                // }
                $q->where($field, '!=', null);
            })
            ->with(['bpjs'])
            ->get();

        $privateInsurances = PrivateInsurance::all()->each(function ($privateInsurance) use ($currentYear) {
            $insuranceId = $privateInsurance->id;
            $employeesPrivateInsurance = Employee::query()
                ->whereHas('privateInsurances', function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                })->with(['privateInsurances' => function ($q) use ($insuranceId) {
                    $q->where('private_insurances.id', $insuranceId);
                }, 'privateInsuranceValues' => function ($q) use ($currentYear) {
                    $q->where('year', $currentYear)->orderBy('id', 'desc');
                }])->get()
                ->lazy()
                ->map(function ($employee) use ($currentYear) {
                    $currentYearValue = collect($employee->privateInsuranceValues)->where('year', $currentYear)->first();

                    $currentPrivateInsurance = collect($employee->privateInsurances)->first();

                    $employee->current_private_insurance = $currentPrivateInsurance;

                    $employee->current_year_value = [
                        'total_premi' => $currentYearValue->total_premi ?? 0,
                        'kesehatan' => $currentYearValue->kesehatan ?? 0,
                        'nilai_tabungan' => $currentYearValue->nilai_tabungan ?? 0,
                    ];
                    return $employee;
                })->all();
            $privateInsurance->members = $employeesPrivateInsurance;
        });


        return view('insurances.exports.xlsx.insurance-report', [
            'employees_bpjs_ketenagakerjaan' => $employeesBpjsKetenagakerjaan,
            'employees_bpjs_mandiri' => $employeesBpjsMandiri,
            'private_insurances' => $privateInsurances,
            'year' => $this->year,
        ]);
    }
}
