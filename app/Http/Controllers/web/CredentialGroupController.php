<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\CredentialGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CredentialGroupController extends Controller
{
    protected $permissions = [
        [
            'header' => 'Dashboard',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Dashboard',
                    'value' => 'dashboard',
                    'items' => ['view'],
                ],
            ],
        ],
        [
            'header' => 'Pegawai',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Pegawai',
                    'value' => 'employee',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Detail',
                    'value' => 'employee_detail',
                    'items' => ['view'],
                ],
                [
                    'name' => 'Karir',
                    'value' => 'employee_career',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Kehadiran',
                    'value' => 'employee_attendance',
                    'items' => ['view'],
                ],
                [
                    'name' => 'Pinjaman',
                    'value' => 'employee_loan',
                    'items' => ['view'],
                ],
                [
                    'name' => 'Time Off',
                    'value' => 'employee_time_off',
                    'items' => ['view'],
                ],
                [
                    'name' => 'Asuransi',
                    'value' => 'employee_insurance',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'File',
                    'value' => 'employee_file',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Nominal Gaji',
                    'value' => 'employee_salary_value',
                    'items' => ['view', 'edit'],
                ],
                [
                    'name' => 'Pengaturan',
                    'value' => 'employee_setting',
                    'items' => ['view', 'edit'],
                ],
            ],
        ],
        [
            'header' => 'Perusahaan',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Perusahaan',
                    'value' => 'company',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Divisi',
                    'value' => 'division',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Kantor',
                    'value' => 'office',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Departemen',
                    'value' => 'department',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Bagian',
                    'value' => 'designation',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Job Title',
                    'value' => 'job_title',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Kehadiran',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Kehadiran',
                    'value' => 'attendance',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Pengajuan Sakit',
                    'value' => 'sick_application',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Pengajuan Cuti',
                    'value' => 'leave_application',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Data Cuti',
                    'value' => 'leave',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Pola Kerja',
                    'value' => 'working_pattern',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Pengajuan Lembur',
                    'value' => 'overtime_application',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Penggajian',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Gaji Bulanan',
                    'value' => 'monthly_salary',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Gaji Harian Magenta',
                    'value' => 'magenta_daily_salary',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Gaji Harian Aerplus',
                    'value' => 'aerplus_daily_salary',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Komponen Gaji',
                    'value' => 'salary_component',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Nilai Gaji',
                    'value' => 'salary_value',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Kenaikan Gaji',
                    'value' => 'salary_increase',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Tunjangan Hari Raya',
                    'value' => 'thr',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Pinjaman',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Pinjaman',
                    'value' => 'loan',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Deposit',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Deposit',
                    'value' => 'deposit',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Pengumuman',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Pengumuman',
                    'value' => 'announcement',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
        [
            'header' => 'Asuransi',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Jenis Asuransi',
                    'value' => 'private_insurance',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Nilai Asuransi',
                    'value' => 'insurance_value',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                // view_insurance_value, add_insurance_value
            ],
        ],
        [
            'header' => 'Laporan',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Laporan',
                    'value' => 'report',
                    'items' => ['view'],
                    // view_report
                ],
            ],
        ],
        [
            'header' => 'Pengaturan',
            'subheaders' => [ // Pegawai
                [
                    'name' => 'Kehadiran',
                    'value' => 'setting_attendance',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Kalender',
                    'value' => 'calendar',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Jenis Cuti',
                    'value' => 'leave_category',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
                [
                    'name' => 'Grup Hak Akses',
                    'value' => 'credential_group',
                    'items' => ['view', 'add', 'edit', 'delete'],
                ],
            ],
        ],
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $credentialGroups = CredentialGroup::query()->withCount('credentials')->get();
        // return $credentialGroups;
        return view('credential-groups.index', [
            'credential_groups' => $credentialGroups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->permissions;
        return view('credential-groups.create', [
            'permissions' => $permissions,
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
        try {
            $validated = $request->validate([
                'name' => 'required|unique:credential_groups|max:255',
            ]);

            $group = new CredentialGroup();
            $group->name = ucwords($request->name);
            $group->permissions = json_encode(collect(array_unique($request->permissions))->values()->all());
            $group->save();

            return response()->json([
                'message' => 'Data grup telah tersimpan',
                'data' => $group,
            ]);
        } catch (\Throwable $e) {
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
        $permissions = $this->permissions;

        $credentialGroup = CredentialGroup::find($id);

        return view('credential-groups.edit', [
            'credential_group' => $credentialGroup,
            'permissions' => $permissions,
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
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
            ]);

            $group = CredentialGroup::find($id);
            $group->name = ucwords($request->name);
            $group->permissions = json_encode(collect(array_unique($request->permissions))->values()->all());
            $group->save();

            return response()->json([
                'message' => 'Data grup telah tersimpan',
                'data' => $group,
            ]);
        } catch (\Throwable $e) {
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        // return redirect()->route('login');
        return redirect('/login');
    }
}
