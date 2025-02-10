@extends('layouts.app')

@section('title', $employee->name . ' - Pengaturan')

@section('head')

@endsection

@section('content')
<?php
$permissions = json_decode(auth()->user()->group->permissions ?? "[]", true);
$harianAerplusOnly = false;
if (in_array('edit_employee_salary_harian_aerplus', $permissions)) {
    $harianAerplusOnly = true;
}
?>
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <!-- <div class="card card-flush mb-10">
        <div class="card-body">
            <h3>Daftar isi</h3>
            <ul>
                <li>
                    <a href="#" class="fw-bold fs-6">Penggajian</a>
                    <ul>
                        <li><a href="#">Nominal Gaji</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div> -->
    <div class="card card-flush" id="kt_profile_details_view">
        <!-- <div class="card-header pt-8">
            <div class="card-title">
                <h2>Preferences</h2>
            </div>
        </div> -->
        <!--begin::Card body-->
        <div class="card-body">
            <!-- <h3 class="mb-5" id="credential">Penggajian</h3> -->
            <div class="fv-row row mb-15">
                <div class="col-md-3">
                    <label class="fs-6 fw-bold">Nominal gaji</label>
                    <div class="text-muted fs-7">Masukkan nominal gaji untuk setiap komponen jika ada</div>
                </div>
                <div class="col-md-9">
                    @if(!$harianAerplusOnly)
                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Gaji Pokok</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.gaji_pokok.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.gaji_pokok.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Tunjangan Bulanan</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.tunjangan.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.tunjangan.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row mb-10">
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Uang Harian</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.uang_harian.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.uang_harian.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                        @if($employee->magenta_daily_salary == 1)
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Koefisien Uang Harian</label>
                            <div>
                                @if(in_array('edit_employee_salary_value', $permissions))
                                <input type="number" v-model="salary.uang_harian.coefficient" class="form-control form-control-sm" placeholder="0">
                                @else
                                @if(in_array('view_employee_salary_value', $permissions))
                                <div>
                                    <strong>@{{ salary.uang_harian.coefficient }}</strong>
                                </div>
                                @else
                                <div>
                                    <em>Tidak ada akses</em>
                                </div>
                                @endif
                                @endif
                                <small class="text-muted d-block mt-2">Koefisien digunakan sebagai pengali nominal gaji di hari libur jika hadir</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($employee->magenta_daily_salary == 1 || $employee->aerplus_overtime == 1)
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Lembur</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.lembur.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.lembur.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Koefisien Lembur</label>
                            <div>
                                @if(in_array('edit_employee_salary_value', $permissions))
                                <input type="number" v-model="salary.lembur.coefficient" class="form-control form-control-sm" placeholder="0">
                                @else
                                @if(in_array('view_employee_salary_value', $permissions))
                                <div>
                                    <strong>@{{ salary.lembur.coefficient }}</strong>
                                </div>
                                @else
                                <div>
                                    <em>Tidak ada akses</em>
                                </div>
                                @endif
                                @endif
                                <small class="text-muted d-block mt-2">Koefisien digunakan sebagai pengali nominal gaji di hari libur jika hadir</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($employee->aerplus_daily_salary == 1)
                    <div class="separator my-10"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Uang Makan</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.uang_makan.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.uang_makan.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Tunjangan Harian (Leader)</label>
                            @if(in_array('edit_employee_salary_value', $permissions))
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" v-model="salary.tunjangan_harian.value" class="form-control form-control-sm" placeholder="0">
                            </div>
                            @else
                            @if(in_array('view_employee_salary_value', $permissions))
                            <div>
                                <strong>Rp @{{ salary.tunjangan_harian.value }}</strong>
                            </div>
                            @else
                            <div>
                                <em>Tidak ada akses</em>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <!-- <div class="separator separator-dashed"></div> -->
            <div class="text-end mb-15">
                <button type="button" class="btn btn-primary" :data-kt-indicator="loading ? 'on' : null" :disabled="loading" @click="updateSalary">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Profil Completion-->
</div>
@endsection

@section('pagescript')
<script>
    const closeModal = (selector) => {
        const element = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(element);
        modal.hide();
    }

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
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    const employee = <?php echo Illuminate\Support\Js::from($employee) ?>;
    const workingPatterns = [];
    const credentialGroups = [];

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employee,
                salary: {
                    gaji_pokok: {
                        value: '{{ $salary_values["gaji_pokok"]["value"] ?? 0}}',
                        coefficient: '{{ $salary_values["gaji_pokok"]["coefficient"] ?? 1 }}'
                    },
                    tunjangan: {
                        value: '{{ $salary_values["tunjangan"]["value"] ?? 0 }}',
                    },
                    uang_harian: {
                        value: '{{ $salary_values["uang_harian"]["value"] ?? 0 }}',
                        coefficient: '{{ $salary_values["uang_harian"]["coefficient"] ?? 1 }}',
                    },
                    lembur: {
                        value: '{{ $salary_values["lembur"]["value"] ?? 0 }}',
                        coefficient: '{{ $salary_values["lembur"]["coefficient"] ?? 1 }}',
                    },
                    tunjangan_harian: {
                        value: '{{ $salary_values["tunjangan_harian"]["value"] ?? 0 }}',
                    },
                    uang_makan: {
                        value: '{{ $salary_values["uang_makan"]["value"] ?? 0 }}',
                    }
                },
                loading: false,
            }
        },
        methods: {
            async updateSalary() {
                const self = this;
                self.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        salary_gaji_pokok_value: self.salary.gaji_pokok.value,
                        salary_gaji_pokok_coefficient: self.salary.gaji_pokok.coefficient,
                        salary_tunjangan_value: self.salary.tunjangan.value,
                        salary_tunjangan_coefficient: self.salary.tunjangan.coefficient,
                        salary_uang_harian_value: self.salary.uang_harian.value,
                        salary_uang_harian_coefficient: self.salary.uang_harian.coefficient,
                        salary_lembur_value: self.salary.lembur.value,
                        salary_lembur_coefficient: self.salary.lembur.coefficient,
                        salary_tunjangan_harian_value: self.salary.tunjangan_harian.value,
                        salary_uang_makan_value: self.salary.uang_makan.value,
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-salary', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);

                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.loading = false;
                }
            },
        }
    })
</script>
@endsection