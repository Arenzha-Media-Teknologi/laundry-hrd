<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  *
    //  * @return bool
    //  */
    // public function authorize()
    // {
    //     return true;
    // }

    // /**
    //  * Get the validation rules that apply to the request.
    //  *
    //  * @return array
    //  */
    // public function rules()
    // {
    //     return [
    //         'name' => 'required|string|max:255',
    //         'gender' => [
    //             'required',
    //             Rule::in(['male', 'female'])
    //         ],
    //         'place_of_birth' => 'required|string|max:255',
    //         'date_of_birth' => 'required|date',
    //         'identity_type' => [
    //             'required',
    //             Rule::in(['ktp'])
    //         ],
    //         'identity_number' => 'required|string|max:50',
    //         'driver_license_type' => 'nullable|max:255',
    //         'driver_license_number' => 'nullable|max:255',
    //         'marital_status' => [
    //             'nullable',
    //             // Rule::in(['lajang', 'menikah', 'duda', 'janda'])
    //         ],
    //         'religion' => [
    //             'nullable',
    //             // Rule::in(['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu', 'other'])
    //         ],
    //         'blood_group' => [
    //             'nullable',
    //             // Rule::in(['a', 'b', 'ab', 'o'])
    //         ],
    //         'recent_education' => [
    //             'nullable',
    //             // Rule::in(['sd', 'smp', 'sma', 'smea', 'smk', 'stm', 'd1', 'd2', 'd3', 'd4', 's1', 's2', 's3'])
    //         ],
    //         'education_institution_name' => [
    //             'nullable',
    //             'string',
    //             'max:255'
    //         ],
    //         'study_program' => 'nullable|max:255',
    //         'email' => 'nullable|string|max:255',
    //         'phone' => 'nullable|string|max:50',
    //         'address' => 'nullable|string|max:255',
    //         'emergency_contact_name' => 'nullable|string|max:255',
    //         'emergency_contact_relation' => 'nullable|string|max:255',
    //         'emergency_contact_phone' => 'nullable|string|max:50',
    //         'start_work_date' => 'nullable|date',
    //         'employment_status' => [
    //             'nullable',
    //             Rule::in(['tetap', 'tidak_tetap'])
    //         ],
    //         'number' => 'required|max:50',
    //         'company_id' => 'required|numeric',
    //         'division_id' => 'required|numeric',
    //         'office_id' => 'required|numeric',
    //         'type' => [
    //             'nullable',
    //             Rule::in(['staff', 'non_staff'])
    //         ],
    //         // 'job_title_id' => 'required|numeric',
    //         // 'photo' => 'nullable|string|image|mimes:jpeg,png,jpg|max:2048',
    //     ];
    // }

    // /**
    //  * Get custom attributes for validator errors.
    //  *
    //  * @return array
    //  */
    // public function attributes()
    // {
    //     return [
    //         'name' => 'nama',
    //         'number' => 'nomor pegawai',
    //         'gender' => 'jenis kelamin',
    //         'place_of_birth' => 'tempat lahir',
    //         'date_of_birth' => 'tanggal lahir',
    //         'identity_type' => 'identitas',
    //         'identity_number' => 'nomor identitas',
    //         'driver_license_type' => 'jenis sim',
    //         'driver_license_number' => 'nomor sim',
    //         'marital_status' => 'status perkawinan',
    //         'religion' => 'agama',
    //         'blood_group' => 'golongan darah',
    //         'recent_education' => 'pendidikan terakhir',
    //         'education_institution_name' => 'nama institusi pendidikan',
    //         'study_program' => 'program studi',
    //         'email' => 'email',
    //         'phone' => 'telepon',
    //         'address' => 'alamat',
    //         'emergency_contact_name' => 'nama kontak darurat',
    //         'emergency_contact_relation' => 'hubungan kontak darurat',
    //         'emergency_contact_phone' => 'telepon kontak darurat',
    //         'start_work_date' => 'tanggal mulai bekerja',
    //         'company_id' => 'perusahaan',
    //         'division_id' => 'divisi',
    //         'office_id' => 'kantor',
    //         'job_title_id' => 'job title',
    //     ];
    // }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => [
                'required',
                Rule::in(['male', 'female'])
            ],
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'identity_type' => [
                'required',
                Rule::in(['ktp'])
            ],
            'identity_number' => 'required|string|max:50',
            'driver_license_type' => 'nullable|max:255',
            'driver_license_number' => 'nullable|max:255',
            'marital_status' => [
                'nullable',
                // Rule::in(['lajang', 'menikah', 'duda', 'janda'])
            ],
            'religion' => [
                'nullable',
                // Rule::in(['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu', 'other'])
            ],
            'blood_group' => [
                'nullable',
                // Rule::in(['a', 'b', 'ab', 'o'])
            ],
            'recent_education' => [
                'nullable',
                // Rule::in(['sd', 'smp', 'sma', 'smea', 'smk', 'stm', 'd1', 'd2', 'd3', 'd4', 's1', 's2', 's3', 'universitas'])
            ],
            'education_institution_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'study_program' => 'nullable|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'start_work_date' => 'nullable|date',
            'employment_status' => [
                'required',
                Rule::in(['tetap', 'tidak_tetap'])
            ],
            'number' => 'required|max:50',
            'company_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'office_id' => 'required|numeric',
            'job_title_id' => 'required|numeric',
            'type' => [
                'nullable',
                Rule::in(['staff', 'non_staff'])
            ],
            'npwp_number' => 'nullable|max:255',
            'npwp_effective_date' => 'nullable|date',
            'npwp_status' => 'nullable',
            'magenta_daily_salary' => 'nullable',
            'aerplus_daily_salary' => 'nullable',
            'overtime_approver_id' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'nama',
            'number' => 'nomor pegawai',
            'gender' => 'jenis kelamin',
            'place_of_birth' => 'tempat lahir',
            'date_of_birth' => 'tanggal lahir',
            'identity_type' => 'identitas',
            'identity_number' => 'nomor identitas',
            'driver_license_type' => 'jenis sim',
            'driver_license_number' => 'nomor sim',
            'marital_status' => 'status perkawinan',
            'religion' => 'agama',
            'blood_group' => 'golongan darah',
            'recent_education' => 'pendidikan terakhir',
            'education_institution_name' => 'nama institusi pendidikan',
            'study_program' => 'program studi',
            'email' => 'email',
            'phone' => 'telepon',
            'address' => 'alamat',
            'emergency_contact_name' => 'nama kontak darurat',
            'emergency_contact_relation' => 'hubungan kontak darurat',
            'emergency_contact_phone' => 'telepon kontak darurat',
            'start_work_date' => 'tanggal mulai bekerja',
            'employment_status' => 'status pegawai',
            'company_id' => 'perusahaan',
            'division_id' => 'divisi',
            'office_id' => 'kantor',
            'job_title_id' => 'job title',
        ];
    }
}
