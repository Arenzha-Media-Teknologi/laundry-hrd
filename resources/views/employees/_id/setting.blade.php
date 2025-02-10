@extends('layouts.app')

@section('title', $employee->name . ' - Pengaturan')

@section('head')

@endsection

@section('content')
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
            <!-- <h3 class="mb-5" id="credential">Penggajian</h3>
            <div class="fv-row row mb-15">
                <div class="col-md-3">
                    <label class="fs-6 fw-bold">Nominal gaji</label>
                    <div class="text-muted fs-7">Masukkan nominal gaji untuk setiap komponen jika ada</div>
                </div>
                <div class="col-md-9">
                    <div class="row mb-10">
                        <div class="col-md-6">
                      
                            <label class="fs-6 fw-bold mb-3">Gaji Pokok</label>
     
                            <div>
                                <input type="number" class="form-control form-control-sm" placeholder="0">
                            </div>
                     
                        </div>
                        <div class="col-md-6">
                          
                            <label class="fs-6 fw-bold mb-3">Tunjangan</label>

                            <div>
                                <input type="number" class="form-control form-control-sm" placeholder="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Uang Harian</label>
                            <div>
                                <input type="number" class="form-control form-control-sm" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-bold mb-3">Lembur</label>
                            <div>
                                <input type="number" class="form-control form-control-sm" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-dashed"></div> -->
            <h3 class="mb-5 mt-15" id="working-pattern">Pola Kerja</h3>
            <!--begin::Input group-->
            <div class="fv-row row mb-15">
                <!--begin::Col-->
                <div class="col-md-3">
                    <!--begin::Label-->
                    <label class="fs-6 fw-bold">Pola Kerja</label>
                    <div class="text-muted fs-7">Pola kerja digunakan sebagai acuan jam masuk & jam keluar pegawai.
                    </div>
                    <!--end::Label-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-9">
                    <!--begin::Switch-->
                    <select v-model="workingPattern.model.workingPatternId" class="form-select">
                        <option value="">Pilih Pola Kerja</option>
                        @foreach($working_patterns as $working_pattern)
                        <option value="{{ $working_pattern->id }}">{{ $working_pattern->name }}</option>
                        @endforeach
                    </select>
                    <!--begin::Switch-->
                    <div class="table-responsive mt-5">
                        <table class="table table-row-bordered">
                            <thead class="fw-bolder fs-6 text-gray-800">
                                <tr>
                                    <td>Hari</td>
                                    <td>Status</td>
                                    <td class="text-center">Jam Masuk</td>
                                    <td class="text-center">Jam Keluar</td>
                                    <td class="text-center">Lembur</td>
                                    <td class="text-center">Jam Mulai Lembur</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in workingPatternItems">
                                    <td :class="item.day_status == 'holiday' ? 'text-danger' : ''">@{{ item.day }}</td>
                                    <td :class="item.day_status == 'holiday' ? 'text-danger' : ''">@{{ item.day_status_locale }}</td>
                                    <td class="text-center">@{{ item.clock_in }}</td>
                                    <td class="text-center">@{{ item.clock_out }}</td>
                                    <td class="text-center">
                                        <span v-if="item.have_overtime == 1"><i class="bi bi-check-circle-fill text-success"></i></span>
                                    </td>
                                    <td class="text-center">@{{ item.overtime_start_time }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            @can('updateSetting', App\Models\Employee::class)
            <div class="text-end mb-15">
                <button type="button" class="btn btn-primary" :data-kt-indicator="workingPattern.loading ? 'on' : null" :disabled="workingPattern.loading" @click="updateWorkingPattern">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
            @endcan
            <!--end::Input group-->
            <div class="separator separator-dashed"></div>
            <h3 class="mb-5 mt-15" id="salary">Penggajian</h3>
            <!--begin::Input group-->
            <!-- <div class="fv-row row mb-15">
                <div class="col-md-3">
                    <label class="fs-6 fw-bold">Gaji Harian Magenta</label>
                    <div class="text-muted fs-7">
                        Aktifkan jika pegawai mendapatkan gaji harian magenta
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-check form-switch form-check-custom form-check-solid me-10">
                        <input v-model="payroll.model.magentaDailySalary" class="form-check-input h-30px w-50px" name="autotimezone" type="checkbox" value="" id="autotimezone">
                        <label class="form-check-label" for="autotimezone">Aktif</label>
                    </div>
                </div>
            </div> -->
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="mb-15" :class="payroll.model.aerplusDailySalary ? 'bg-light p-4' : ''">
                <div class="fv-row row mb-10">
                    <!--begin::Col-->
                    <div class="col-md-3">
                        <!--begin::Label-->
                        <label class="fs-6 fw-bold">Gaji Harian</label>
                        <div class="text-muted fs-7">
                            Aktifkan jika pegawai mendapatkan gaji harian
                        </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-md-9">
                        <!--begin::Switch-->
                        <div class="form-check form-switch form-check-custom form-check-solid me-10">
                            <input v-model="payroll.model.aerplusDailySalary" class="form-check-input h-30px w-50px" name="autotimezone" type="checkbox" value="" id="autotimezone">
                            <label class="form-check-label" for="autotimezone">Aktif</label>
                        </div>
                        <!--begin::Switch-->
                    </div>
                    <!--end::Col-->
                </div>
                <!-- <div v-if="payroll.model.aerplusDailySalary">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="payroll.model.aerplusOvertime" id="checkGetMagentaDailySalary" />
                        <label class="form-check-label" for="checkGetMagentaDailySalary">
                            <div class="fs-6 fw-bold text-gray-800">Hitung Lembur</div>
                            <div class="fs-7 text-muted">Pegawai akan mendapatkan upah lembur</div>
                        </label>
                    </div>
                </div> -->
            </div>
            <!--end::Input group-->
            @can('updateSetting', App\Models\Employee::class)
            <div class="text-end mb-15">
                <button type="button" class="btn btn-primary" :data-kt-indicator="payroll.loading ? 'on' : null" :disabled="payroll.loading" @click="updatePayrollSetting">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
            @endcan
            <div class="separator separator-dashed"></div>
            <h3 class="mb-5 mt-15" id="salary">Tracking (Pelacakan)</h3>
            <div class="fv-row row mb-15">
                <!--begin::Col-->
                <div class="col-md-3">
                    <!--begin::Label-->
                    <label class="fs-6 fw-bold">Lacak Pegawai</label>
                    <div class="text-muted fs-7">
                        Aktifkan untuk melacak pegawai ini
                    </div>
                    <!--end::Label-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-9">
                    <div class="form-check form-switch form-check-custom form-check-solid me-10">
                        <input v-model="tracking.model.isTracked" class="form-check-input h-30px w-50px" type="checkbox" value="" id="istracked">
                        <label class="form-check-label" for="istracked">Aktif</label>
                    </div>
                </div>
                <!--end::Col-->
            </div>
            @can('updateSetting', App\Models\Employee::class)
            <div class="text-end mb-15">
                <button type="button" class="btn btn-primary" :data-kt-indicator="tracking.loading ? 'on' : null" :disabled="tracking.loading" @click="updateIsTracked">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
            @endcan
            <div class="separator separator-dashed"></div>
            <h3 class="mb-5 mt-15" id="credential">Akun</h3>
            <div class="fv-row row">
                <!--begin::Col-->
                <div class="col-md-3">
                    <!--begin::Label-->
                    <label class="fs-6 fw-bold">Kredensial</label>
                    <div class="text-muted fs-7">Kredensial berikut digunakan untuk akses masuk kedalam aplikasi</div>
                    <!--end::Label-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-9">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div>
                                <!--begin::Subtitle-->
                                <label class="fs-6 fw-bold mb-3">Username</label>
                                <!--end::Subtitle-->
                                <!--begin::Options-->
                                <div>
                                    <input type="text" v-model="account.model.username" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <!--end::Options-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <!--begin::Subtitle-->
                                <label class="fs-6 fw-bold mb-3">Password</label>
                                <!--end::Subtitle-->
                                <!--begin::Options-->
                                <div>
                                    <input type="password" v-model="account.model.password" class="form-control form-control-sm" autocomplete="off">
                                </div>
                                <!--end::Options-->
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div>
                                <!--begin::Subtitle-->
                                <label class="fs-6 fw-bold mb-3">Grup Hak Akses</label>
                                <!--end::Subtitle-->
                                <!--begin::Options-->
                                <div>
                                    <select v-model="account.model.credentialGroupId" class="form-select form-select-sm">
                                        <option value="">Pilih Grup</option>
                                        @foreach($credential_groups as $credential_group)
                                        <option value="{{ $credential_group->id }}">{{ $credential_group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Options-->
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border mb-8 rounded">
                        <h4 class="mb-5">Mobile</h4>
                        <div class="row mb-8">
                            <div class="col-md-6">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="account.model.allowMobileAccess" value="" id="flexSwitchDefault" />
                                    <label class="form-check-label" for="flexSwitchDefault">
                                        <span class="fs-6 fw-bold">Akses aplikasi mobile</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div v-if="account.model.allowMobileAccess" class="row">
                            <div class="col-md-6">
                                <div>
                                    <!--begin::Subtitle-->
                                    <label class="fs-6 fw-bold mb-3">Grup Hak Akses</label>
                                    <!--end::Subtitle-->
                                    <!--begin::Options-->
                                    <div>
                                        <div class="d-flex">
                                            <div class="form-check form-check-custom form-check-solid me-5">
                                                <input class="form-check-input" type="radio" v-model="account.model.mobileAccessType" value="regular" name="mobile-access-type" id="flexRadioDefault1" />
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Pegawai
                                                </label>
                                            </div>
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" type="radio" v-model="account.model.mobileAccessType" value="admin" name="mobile-access-type" id="flexRadioDefault2" />
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    Admin
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Options-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="p-5 border rounded mb-8">
                        <h4 class="mb-5">AerPlus</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-8">
                                    <label class="fs-6 fw-bold mb-3">Akses Sebagai</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-custom form-check-solid me-5">
                                            <input class="form-check-input" type="radio" value="supervisor" v-model="account.model.mobileAccessAs" name="mobile-access-as" id="mobileAccessAs1" />
                                            <label class="form-check-label" for="mobileAccessAs1">
                                                Supervisor
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" value="technician" v-model="account.model.mobileAccessAs" name="mobile-access-as" id="mobileAccessAs2" />
                                            <label class="form-check-label" for="mobileAccessAs2">
                                                Teknisi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div :class="account.model.mobileAccessAs == 'supervisor' ? 'd-block' : 'd-none'">
                                    <label class="fs-6 fw-bold mb-3">Depot Supervisor</label>
                                    <div>
                                        <select class="form-select form-select-sm" id="select-aerplus-offices" multiple>
                                            @foreach($aerplus_offices as $aerplus_office)
                                            <option value="{{ $aerplus_office->id }}">{{ $aerplus_office->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!--end::Col-->
            </div>
            @can('updateSetting', App\Models\Employee::class)
            <div class="text-end mb-15">
                <button type="button" class="btn btn-primary" :data-kt-indicator="account.loading ? 'on' : null" :disabled="account.loading" @click="updateAccount">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
        @endcan
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
    const workingPatterns = <?php echo Illuminate\Support\Js::from($working_patterns) ?>;
    const credentialGroups = <?php echo Illuminate\Support\Js::from($credential_groups) ?>;
    const accessibleOffices = <?php echo Illuminate\Support\Js::from(json_decode($employee->credential->accessible_offices ?? '[]')) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employee,
                workingPattern: {
                    data: workingPatterns,
                    model: {
                        workingPatternId: '{{ $active_working_pattern_id }}',
                    },
                    loading: false,
                },
                payroll: {
                    model: {
                        magentaDailySalary: '{{ $employee->magenta_daily_salary }}' == '1' ? true : false,
                        aerplusDailySalary: '{{ $employee->aerplus_daily_salary }}' == '1' ? true : false,
                        aerplusOvertime: '{{ $employee->aerplus_overtime }}' == '1' ? true : false,
                    },
                    loading: false,
                },
                tracking: {
                    model: {
                        isTracked: Number.parseInt('{{ $employee->is_tracked }}'),
                    },
                    loading: false,
                },
                account: {
                    model: {
                        username: '{{ $employee->credential->username ?? "" }}',
                        password: '',
                        credentialGroupId: '{{ $employee->credential->credential_group_id ?? "" }}',
                        allowMobileAccess: Number.parseInt('{{ $employee->credential->mobile_access ?? 0 }}'),
                        mobileAccessType: '{{ $employee->credential->mobile_access_type ?? "regular" }}',
                        isAerplusSupervisor: Number.parseInt('{{ $employee->credential->is_aerplus_supervisor ?? 0 }}'),
                        isAerplusTechnician: Number.parseInt('{{ $employee->credential->is_aerplus_technician ?? 0 }}'),
                        mobileAccessAs: '',
                        accessibleOffices: accessibleOffices,
                    },
                    loading: false,
                },
                // credentialGroups,
            }
        },
        mounted() {
            if (this.account.model.isAerplusTechnician) {
                this.account.model.mobileAccessAs = 'technician';
            }

            if (this.account.model.isAerplusSupervisor) {
                this.account.model.mobileAccessAs = 'supervisor';
            }
        },
        computed: {
            workingPatternItems() {
                const workingPatternId = this.workingPattern.model.workingPatternId;
                const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                if (workingPatternId) {
                    const [workingPattern] = this.workingPattern.data.filter(wp => wp.id == workingPatternId);
                    if (workingPattern) {
                        return workingPattern.items.map((item, index) => ({
                            day: days[item.order - 1],
                            day_status_locale: item.day_status == 'workday' ? 'Hari Kerja' : 'Hari Libur',
                            day_status: item.day_status,
                            clock_in: item.clock_in,
                            clock_out: item.clock_out,
                            have_overtime: item.have_overtime,
                            overtime_start_time: item.overtime_start_time,
                        }))
                    }
                }

                return [];
            }
        },
        methods: {
            async updateWorkingPattern() {
                const self = this;
                self.workingPattern.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        working_pattern_id: self.workingPattern.model.workingPatternId,
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-working-pattern', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.workingPattern.loading = false;
                }
            },
            async updatePayrollSetting() {
                const self = this;
                self.payroll.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        magenta_daily_salary: self.payroll.model.magentaDailySalary,
                        aerplus_daily_salary: self.payroll.model.aerplusDailySalary,
                        aerplus_overtime: self.payroll.model.aerplusOvertime,
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-payroll-setting', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.payroll.loading = false;
                }
            },
            async updateIsTracked() {
                const self = this;
                self.tracking.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        is_tracked: self.tracking.model.isTracked,
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-is-tracked', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.tracking.loading = false;
                }
            },
            async updateAccount() {
                const self = this;
                self.account.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        username: self.account.model.username,
                        password: self.account.model.password,
                        credential_group_id: self.account.model.credentialGroupId,
                        mobile_access: self.account.model.allowMobileAccess,
                        mobile_access_type: self.account.model.mobileAccessType,
                        is_aerplus_supervisor: self.account.model.isAerplusSupervisor,
                        is_aerplus_technician: self.account.model.isAerplusTechnician,
                        accessible_offices: JSON.stringify(self.account.model.accessibleOffices),
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-account', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.account.loading = false;
                }
            },
        },
        watch: {
            'account.model.mobileAccessAs': function(newValue) {
                if (newValue == 'supervisor') {
                    this.account.model.isAerplusTechnician = false;
                    this.account.model.isAerplusSupervisor = true;
                } else if (newValue == 'technician') {
                    this.account.model.isAerplusTechnician = true;
                    this.account.model.isAerplusSupervisor = false;
                }
            }
        }
    })
</script>
<script>
    $(function() {
        $('#select-aerplus-offices').select2();

        if (accessibleOffices && Array.isArray(accessibleOffices)) {
            $('#select-aerplus-offices').val(accessibleOffices).trigger('change');
        }

        $('#select-aerplus-offices').on('change', function() {
            const values = $(this).val();
            app.account.model.accessibleOffices = values;
        });
    })
</script>
@endsection