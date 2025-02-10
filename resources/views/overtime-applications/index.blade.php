@extends('layouts.app')

@section('title', 'Pengajuan Lembur')

@section('prehead')
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
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Perusahaan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Pengajuan Lembur</h1>
            <p class="text-muted mb-0">Buat Pengajuan Lembur Pegawai</p>
        </div>
        <div class="d-flex">
            <!-- <button type="button" class="btn btn-success"><i class="bi bi-check-lg"></i> Simpan</button> -->
            <button v-cloak type="button" :data-kt-indicator="saveLoading ? 'on' : null" class="btn btn-success" @click="save" :disabled="saveLoading">
                <span class="indicator-label"><i class="bi bi-check-lg"></i> Simpan</span>
                <span class="indicator-progress">Meyimpan pengajuan...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0"><i class="bi bi-calendar3 fs-1"></i> <span class="text-gray-600">{{ \Carbon\Carbon::parse($date)->isoFormat('LL') }}</span></h1>
        </div>
        <div>
            <input type="date" class="form-control" value="{{ $date }}" id="filter-date">
        </div>
    </div>
    <div class="pt-5"></div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-3">
            <!-- <div class="d-flex justify-content-end mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" v-model="isHourUnitMode" role="switch" id="switchHourUnitMode">
                    <label class="form-check-label" for="switchHourUnitMode">Gunakan Satuan Jam</label>
                </div>
            </div> -->
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-7 gy-5" id="attendance_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-start ps-2">Pegawai</th>
                            <th class="text-center">Perusahaan</th>
                            <th class="text-center">Lembur Sistem (Menit)</th>
                            <th class="text-center">Lembur Pengajuan (Menit)</th>
                            <th class="text-center">Konversi Lembur (Jam)</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600">
                        <tr v-for="(employee, index) in employeesWithAttendances" class="text-center">
                            <!--begin::Name=-->
                            <td class="text-start ps-2" style="max-width: 200px;">
                                <div v-cloak>
                                    <div>
                                        <a :href="`/employees/${employee.id}/detail-v2`" class="text-gray-800 text-hover-primary" tabindex="-1">@{{ employee.name }}</a>
                                    </div>
                                    <span class="text-muted d-block fs-7">
                                        @{{ employee?.office?.division?.company?.initial ?? 'NA' }}-@{{ employee?.office?.division?.initial ?? 'NA' }}-@{{ employee?.number }}
                                    </span>
                                    <!-- <span class="text-muted d-block fs-7">@{{ employee?.office?.division?.company?.initial || 'NA' }}-@{{ employee?.office?.division?.initial || 'NA' }}-@{{ employee?.number }} | @{{ employee?.office?.division?.company?.name ?? 'PERUSAHAAN' }} - @{{ employee?.office?.division?.name ?? 'DIVISI' }} - @{{ employee?.office?.name ?? 'KANTOR' }}</span>
                                            <span class="text-muted d-block fs-7">@{{ employee?.active_career?.job_title?.name || '' }} - @{{ employee?.active_career?.job_title?.designation?.name || '' }}</span> -->
                                </div>
                            </td>
                            <!--end::Name=-->
                            <td class="text-center"><span v-cloak>@{{ employee?.office?.division?.company?.name ?? 'PERUSAHAAN' }}</span></td>
                            <td class="text-center fw-bolder"><span v-cloak>@{{ employee?.attendances[0]?.overtime ?? 0 }}</span></td>
                            <td>
                                <div class="d-flex justify-content-center" v-cloak>
                                    <input type="number" v-model="employee.new_overtime" class="form-control form-control-sm" style="width: 150px;">
                                </div>
                            </td>
                            <td><span v-cloak>@{{ hourOvertimeConversion[index] }}</span></td>
                        </tr>
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
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

        const datatable = $('#attendance_table').DataTable({
            ordering: false,
        });

        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });

        $('#filter-date').on('change', function(e) {
            document.location.href = `/overtime-applications?date=${$(this).val()}`;
        })
    })
</script>
<script>
    const employeesWithAttendances = <?php echo Illuminate\Support\Js::from($employees_with_attendances) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employeesWithAttendances,
                isHourUnitMode: false,
                saveLoading: false,
            }
        },
        methods: {
            async save() {
                let self = this;
                try {

                    self.saveLoading = true;

                    const response = await axios.post('/overtime-applications', {
                        employees: self.payload
                    });

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
                    self.saveLoading = false;
                }
            },
        },
        computed: {
            payload() {
                return this.employeesWithAttendances.map(employee => {
                    return {
                        attendance_id: employee.attendances[0].id,
                        application_overtime: employee.new_overtime,
                    }
                });
            },
            hourOvertimeConversion() {
                return this.employeesWithAttendances.map(employee => {
                    const newestAttendanceOvertime = employee?.new_overtime || 0;
                    let overtimeHours = 0;

                    const x = newestAttendanceOvertime % 30;
                    const y = (newestAttendanceOvertime - x) / 30;

                    if (y > 0) {
                        const z = (newestAttendanceOvertime - x) - 30;
                        overtimeHours = 1 + Math.floor(z / 60);
                    }

                    overtimeHours = (overtimeHours > 0) ? overtimeHours : 0;

                    return overtimeHours;
                });
            }
        },
        watch: {
            isHourUnitMode: function(newValue, oldValue) {
                if (newValue == true) {

                }
            }
        }
    })
</script>
@endsection