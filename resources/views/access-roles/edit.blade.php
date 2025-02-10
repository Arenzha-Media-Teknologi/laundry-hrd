@extends('layouts.app')

@section('title', 'Ubah Hak Akses')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Tambah Hak Akses</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="">
                    <h2 class="my-10">General</h2>
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-10 fv-row fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="required form-label">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.name" class="form-control" placeholder="Contoh: Super Admin">
                                <!--end::Input-->
                            </div>
                        </div>
                    </div>
                    <!-- begin::submit -->
                    <!-- begin::permission table -->
                    <h2 class="my-10">Akses</h2>
                    <div v-cloak class="table-responsive">
                        <table class="table table-row-dashed gy-7 gs-7">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th>Nama</th>
                                    <th class="text-center">Lihat</th>
                                    <th class="text-center">Tambah</th>
                                    <th class="text-center">Ubah</th>
                                    <th class="text-center">Hapus</th>
                                    <th class="text-center">Konfirmasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(permission, index) in permissions">
                                    <tr class="bg-light">
                                        <td class="fw-bold text-gray-800 fs-5">@{{ permission.heading }}</td>
                                        <td v-for="n in (maxColumn - 1)"></td>
                                    </tr>
                                    <tr v-for="(item, itemIndex) in permission.items">
                                        <td>@{{ item.name }}</td>
                                        <td v-for="allPermission in allPermissions">
                                            <div v-if="item.permissions.includes(allPermission)" class="form-check form-check-custom justify-content-center">
                                                <input class="form-check-input" type="checkbox" v-model="model.permissions" :value="`${allPermission}_${item.key}`" :id="`${allPermission}_${item.key}`" />
                                                <label class="form-check-label" :for="`${allPermission}_${item.key}`">
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- end::permission table -->
                    <div class=" d-flex justify-content-end my-10">
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!-- end::submit -->
                </div>
                <!--end::Card header-->
            </div>
        </div>
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    let datatable = null;
    $(function() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toastr-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

    })
</script>
<script>
    // var companies = @json('$companies');
    const companies = [];
    const divisions = [];

    // echo Illuminate\Support\Js::from($divisions)
    const permissionsModel = <?php echo Illuminate\Support\Js::from($permissions) ?>;
    const allPermissions = ['add', 'view', 'update', 'delete', 'confirmation'];
    const defaultPermissions = ['add', 'view', 'update', 'delete'];

    function generateItemPermissions(excludes = [], customs = []) {
        let modifiedPermissions = defaultPermissions;
        if (Array.isArray(customs) && customs.length > 0) {
            modifiedPermissions = modifiedPermissions.concat(customs);
        }

        if (Array.isArray(excludes) && excludes.length > 0) {
            modifiedPermissions = modifiedPermissions.filter(permission => excludes.indexOf(permission) < 0);
        }

        return modifiedPermissions;

        // return modifiedPermissions.map(permission => {
        //     return {
        //         type: permission,
        //         value: `${permission}_${key}`,
        //     }
        // })
    }

    const permissions = [{
            heading: 'Pegawai',
            items: [{
                name: 'Pegawai',
                key: 'employee',
                permissions: generateItemPermissions(),
            }]
        },
        {
            heading: 'Perusahaan',
            items: [{
                    name: 'Perusahaan',
                    key: 'company',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Divisi',
                    key: 'division',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Kantor',
                    key: 'office',
                    permissions: generateItemPermissions(),
                },
            ]
        },
        {
            heading: 'Struktur Organisasi',
            items: [{
                    name: 'Departemen',
                    key: 'department',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Bagian',
                    key: 'designation',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Job Title',
                    key: 'job_title',
                    permissions: generateItemPermissions(),
                },
            ]
        },
        {
            heading: 'Kehadiran',
            items: [{
                    name: 'Kehadiran',
                    key: 'attendance',
                    permissions: generateItemPermissions(['delete']),
                },
                {
                    name: 'Pengajuan Sakit',
                    key: 'sick_application',
                    permissions: generateItemPermissions([], ['confirmation']),
                },
                {
                    name: 'Pengajuan Izin',
                    key: 'permission_application',
                    permissions: generateItemPermissions([], ['confirmation']),
                },
                {
                    name: 'Pengajuan Cuti',
                    key: 'leave_application',
                    permissions: generateItemPermissions([], ['confirmation']),
                },
                {
                    name: 'Pola Kerja',
                    key: 'working_pattern',
                    permissions: generateItemPermissions(),
                },
            ]
        },
        {
            heading: 'Penggajian',
            items: [{
                    name: 'Gaji Bulanan',
                    key: 'monthly_salary',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Gaji Harian Magenta',
                    key: 'magenta_daily_salary',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Gaji Harian Aerplus',
                    key: 'aerplus_daily_salary',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Kenaikan Gaji',
                    key: 'salary_increase',
                    permissions: generateItemPermissions(),
                },
                {
                    name: 'Asuransi',
                    key: 'insurance',
                    permissions: generateItemPermissions(),
                },
            ]
        },
        {
            heading: 'Pinjaman',
            items: [{
                name: 'Pinjaman',
                key: 'loan',
                permissions: generateItemPermissions(),
            }]
        },
        {
            heading: 'Pengaturan',
            items: [{
                name: 'Pengaturan Gaji',
                key: 'salary_setting',
                permissions: generateItemPermissions(),
            }]
        },
    ]

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                permissions,
                companies,
                divisions,
                defaultPermissions,
                allPermissions,
                maxColumn: allPermissions.length + 1,
                model: {
                    id: '{{ $access_role->id }}',
                    name: '{{ $access_role->name }}',
                    permissions: permissionsModel,
                },
                submitLoading: false,
            }
        },
        methods: {
            // COMPANY METHODS
            async onSubmit() {
                let self = this;
                try {
                    const {
                        id,
                        name,
                        permissions,
                    } = self.model;

                    self.submitLoading = true;

                    const response = await axios.post('/access-roles/' + id, {
                        name,
                        permissions: JSON.stringify(permissions),
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        document.location.href = '/access-roles';
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitLoading = false;
                }
            },
        },
    })
</script>
@endsection