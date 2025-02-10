@extends('layouts.app')

@section('title', 'Ubah Time Off')

@section('prehead')
<!-- <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('pagestyle')
<style>
    .datepicker {
        padding: 20px;
    }

    .datepicker .datepicker-days .table-condensed th,
    .datepicker .datepicker-days .table-condensed td {
        padding: 5px;
    }
</style>
@endsection

@section('bodyscript')
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Ubah Time Off</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <form autocomplete="off">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <!-- <h2 class="my-10">General</h2> -->
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Tanggal Pengajuan</label>
                            <!--end::Label-->
                            <input type="date" v-model="model.applicationDate" class="form-control form-control-sm">
                        </div>
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Pegawai</label>
                            <!--end::Label-->
                            <select id="employee-select" class="form-select form-select-sm" data-placeholder="Pilih Pegawai" data-allow-clear="true">

                            </select>
                        </div>
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Jenis Time Off</label>
                            <!--end::Label-->
                            <select v-model="model.type" class="form-select form-select-sm">
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="cuti">Cuti</option>
                            </select>
                        </div>
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <div v-cloak v-if="model.type == 'izin'" class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Jenis Izin</label>
                            <!--end::Label-->
                            <select v-model="model.permissionCategory" class="form-select form-select-sm">
                                <option value="">Pilih jenis izin</option>
                                @foreach($permission_categories as $permission_category)
                                <option value="{{ $permission_category->id }}">{{ $permission_category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- end:Input group -->
                        <div v-cloak v-if="model.type == 'cuti'" class="mb-10">
                            <div class="fs-6 fw-bold mb-3">Sisa Cuti Pegawai</div>
                            <div v-for="employee in employeesRemainingLeaves" :key="employee.id" class="mb-3 p-3 rounded d-flex justify-content-between align-items-center" :class="employee.background">
                                <div>
                                    <div>
                                        <span class="fw-bold">@{{ employee.name }}</span>&nbsp;-&nbsp;<em>@{{ employee.jobTitle }}</em>
                                    </div>
                                    <div>
                                        <small>@{{ employee.office }} - @{{ employee.division }} - @{{ employee.company }}</small>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-bold">@{{ employee.remaining }}</span>
                                </div>
                            </div>
                        </div>
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Tanggal Time Off</label>
                            <!--end::Label-->
                            <div class="input-group">
                                <input type="text" id="time-off-dates" class="form-control form-control-sm">
                                <span class="input-group-text" id="btn-open-time-off-date">
                                    <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                            <path opacity="0.3" d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z" fill="black" />
                                            <path d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z" fill="black" />
                                        </svg></span>
                                </span>
                            </div>
                        </div>
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <!-- <div class="mb-10 fv-row">
                       
                        <label class="required form-label">Lampiran</label>
                      
                        <input type="file" @change="onChangeAttachment($event)" class="form-control form-control-sm">
                    </div> -->
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Catatan</label>
                            <!--end::Label-->
                            <textarea v-model="model.note" class="form-control form-control-sm">
                        </textarea>
                        </div>
                        <!-- end:Input group -->
                        <!--begin::Input group-->
                        <div class="mb-10 fv-row">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" v-model="model.approvalStatus" id="flexCheckDefault" />
                                <label class="form-check-label" for="flexCheckDefault">
                                    Setujui pengajuan
                                </label>
                            </div>
                        </div>
                        <!-- end:Input group -->
                        <!-- begin::submit -->
                        <div class="d-flex justify-content-end my-10">
                            <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="disabledSubmitButton" @click="onSubmit">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Mengirim data...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!-- end::submit -->
                    </div>
                    <!--end::Card header-->
                </div>
            </form>
        </div>
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<!-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    Vue.prototype.moment = moment;

    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const timeOff = <?php echo Illuminate\Support\Js::from($time_off) ?>;
    const timeOffType = '<?= request()->query('type') ?>';
    const timeOffDates = timeOff.application_dates.split(',');
    const timeOffEmployeeId = timeOff.employee_id;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                model: {
                    id: timeOff.id,
                    applicationDate: timeOff?.date,
                    employees: timeOff?.employee_id.toString(),
                    type: timeOffType,
                    permissionCategory: timeOffType == 'izin' ? timeOff.permission_category_id : '',
                    timeOffDates,
                    attachment: null,
                    note: timeOff.note,
                    approvalStatus: false,
                },
                submitLoading: false,
            }
        },
        computed: {
            employeesRemainingLeaves() {
                if (this.model.type == 'cuti') {
                    const {
                        employees
                    } = this;
                    const selectedEmployees = this.model.employees;
                    const takenDatesCount = this.model.timeOffDates.length;

                    return employees
                        .filter(employee => selectedEmployees.includes(employee.id.toString()))
                        .map(employee => {
                            let remaining = 0;
                            let background = 'bg-light'
                            if (employee.leave) {
                                remaining = Number(employee?.leave?.total - employee?.leave?.taken);
                            }

                            if (takenDatesCount > remaining) {
                                background = 'bg-light-danger';
                            }

                            return {
                                id: employee.id || '',
                                name: employee.name || '',
                                jobTitle: employee?.active_career?.job_title?.name || '',
                                company: employee?.office?.division?.company?.name || '',
                                division: employee?.office?.division?.name || '',
                                office: employee?.office?.name || '',
                                remaining,
                                background,
                            };
                        });
                }

                return [];

            },
            isExceedLeave() {
                const takenDatesCount = this.model.timeOffDates.length;
                const exceedLeavesEmployees = this.employeesRemainingLeaves.filter(employee => takenDatesCount > employee.remaining);

                if (exceedLeavesEmployees.length > 0) {
                    return true;
                }

                return false;
            },
            disabledSubmitButton() {
                if (this.isExceedLeave) {
                    return true;
                }

                return false;
            },
            formattedTimeOffDates: function() {
                return this.model.timeOffDates.map(date => moment(date).format("YYYY-MM-DD")).join();
            },
        },
        methods: {
            onChangeAttachment(e) {
                console.log(e.target.files[0]);
                if (e.target.files && e.target.files.length > 0) {
                    this.model.attachment = e.target.files[0];
                } else {
                    if (this.model.attachment) {
                        this.model.attachment = null;
                    }
                }
            },
            async onSubmit() {
                let self = this;

                try {
                    const timeOffId = self.model.id;

                    const {
                        applicationDate,
                        employees,
                        type,
                        timeOffDates,
                        attachment,
                        note,
                        permissionCategory,
                        approvalStatus,
                    } = self.model;

                    if (type == 'cuti' && self.isExceedLeave) {
                        toastr.warning('Sisa cuti karyawan tidak mencukupi');
                        return null;
                    }

                    self.submitLoading = true;

                    const payload = {
                        date: applicationDate,
                        employee_id: employees,
                        application_dates: self.formattedTimeOffDates,
                        // attachment,
                        note,
                        approval_status: approvalStatus ? 'approved' : 'pending',
                    }

                    let response = null;

                    if (type == 'sakit') {
                        response = await axios.post('/sick-applications/' + timeOffId, payload);
                    } else if (type == 'izin') {
                        payload.permission_category_id = permissionCategory;
                        response = await axios.post('/permission-applications/' + timeOffId, payload);
                    } else if (type == 'cuti') {
                        response = await axios.post('/leave-applications/' + timeOffId, payload);
                    }

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        // setTimeout(() => {
                        //     self.gotoUrl('/offices');
                        // }, 500);
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
            }
        }
    })
</script>
<script>
    let select2Data = [];
    if (Array.isArray(employees)) {
        select2Data = employees.map(employee => ({
            id: employee.id || '',
            text: employee.name || '',
            jobTitle: employee?.active_career?.job_title?.name || '',
            company: employee?.office?.division?.company?.name || '',
            division: employee?.office?.division?.name || '',
            office: employee?.office?.name || '',
            selected: employee.id == timeOffEmployeeId ? true : false,
        }));
    }

    $(function() {

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }

            var $state = $(
                `
                <div class="">
                    <div>
                        <span class="fw-bold">${state.text}</span><em>${state.jobTitle && ' - ' + state.jobTitle}</em>
                    </div>
                    <div>
                        <small>${ state.office } - ${ state.division } - ${state.company}</small>
                    </div>
                </div>
                `
            );
            return $state;
        };


        $('#employee-select').select2({
            data: select2Data,
            templateResult: formatState,
        });

        $('#employee-select').on('change', function(e) {
            // console.log($(this).val());
            const values = $(this).val();
            app.$data.model.employees = values;
        });

        $('#time-off-dates').datepicker({
            format: 'yyyy-mm-dd',
            multidate: true,
        }).on('changeDate', function(e) {
            // `e` here contains the extra attributes
            app.$data.model.timeOffDates = e.dates;
        });

        $('#time-off-dates').datepicker('setDates', timeOffDates);

        $('#btn-open-time-off-date').on('click', function() {
            $('#time-off-dates').datepicker('show');
        });
    })
</script>
@endsection