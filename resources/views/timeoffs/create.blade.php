@extends('layouts.app')

@section('title', 'Tambah Time Off')

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
                <h2>
                    Tambah Time Off
                </h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div>
                        <label class="required form-label">Jenis Time Off</label>
                        <div class="row">
                            <div class="col-md-6">
                                <!--begin::Option-->
                                <input type="radio" v-model="model.type" class="btn-check" name="radio_buttons_2" value="sakit" id="kt_radio_buttons_2_option_1" />
                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center mb-5" for="kt_radio_buttons_2_option_1">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod001.svg-->
                                    <span class="svg-icon svg-icon-4x me-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M12.025 4.725C9.725 2.425 6.025 2.425 3.725 4.725C1.425 7.025 1.425 10.725 3.725 13.025L11.325 20.625C11.725 21.025 12.325 21.025 12.725 20.625L20.325 13.025C22.625 10.725 22.625 7.025 20.325 4.725C18.025 2.425 14.325 2.425 12.025 4.725Z" fill="black" />
                                            <path d="M14.025 17.125H13.925C13.525 17.025 13.125 16.725 13.025 16.325L11.925 11.125L11.025 14.325C10.925 14.725 10.625 15.025 10.225 15.025C9.825 15.125 9.425 14.925 9.225 14.625L7.725 12.325L6.525 12.925C6.425 13.025 6.225 13.025 6.125 13.025H3.125C2.525 13.025 2.125 12.625 2.125 12.025C2.125 11.425 2.525 11.025 3.125 11.025H5.925L7.725 10.125C8.225 9.925 8.725 10.025 9.025 10.425L9.825 11.625L11.225 6.72498C11.325 6.32498 11.725 6.02502 12.225 6.02502C12.725 6.02502 13.025 6.32495 13.125 6.82495L14.525 13.025L15.225 11.525C15.425 11.225 15.725 10.925 16.125 10.925H21.125C21.725 10.925 22.125 11.325 22.125 11.925C22.125 12.525 21.725 12.925 21.125 12.925H16.725L15.025 16.325C14.725 16.925 14.425 17.125 14.025 17.125Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->

                                    <span class="d-block fw-bold text-start">
                                        <span class="text-dark fw-bolder d-block fs-3">Sakit</span>
                                    </span>
                                </label>
                                <!--end::Option-->
                            </div>
                            <div class="col-md-6">
                                <!--begin::Option-->
                                <input type="radio" v-model="model.type" class="btn-check" name="radio_buttons_2" value="cuti" id="kt_radio_buttons_2_option_2" />
                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-7 d-flex align-items-center" for="kt_radio_buttons_2_option_2">
                                    <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                                    <span class="svg-icon svg-icon-4x me-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="black" />
                                            <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->

                                    <span class="d-block fw-bold text-start">
                                        <span class="text-dark fw-bolder d-block fs-3">Cuti</span>
                                    </span>
                                </label>
                                <!--end::Option-->
                            </div>
                        </div>
                    </div>

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
                        <select id="employee-select" class="form-select form-select-sm" data-placeholder="Pilih Pegawai" data-allow-clear="true" multiple="multiple">

                        </select>
                    </div>
                    <!-- end:Input group -->
                    <!--begin::Input group-->
                    <!-- <div class="mb-10 fv-row">
               
                        <label class="required form-label">Jenis Time Off</label>
           
                        <select v-model="model.type" class="form-select form-select-sm">
                            <option value="sakit">Sakit</option>
               
                            <option value="cuti">Cuti</option>
                        </select>
                    </div> -->
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
                    <div v-cloak v-if="model.type == 'cuti'" class="mb-10 fv-row">
                        <!--begin::Label-->
                        <label class="required form-label">Jenis Cuti</label>
                        <!--end::Label-->
                        <select v-model="model.leaveCategory" class="form-select form-select-sm">
                            <option value="">Pilih Jenis Cuti</option>
                            @foreach($leave_categories as $leave_category)
                            <option value="{{ $leave_category->id }}">{{ $leave_category->name }} ({{ $leave_category->max_day ?? 0 }} Hari)</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- end:Input group -->
                    <div v-cloak v-if="model.type == 'cuti' && selectedLeaveCategory && selectedLeaveCategory?.type == 'annual_leave'" class="card card-bordered mb-10">
                        <div class="card-body">
                            <div>
                                <div class="fs-6 fw-bold mb-3">Sisa Cuti Tahunan Pegawai</div>
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
                        </div>
                    </div>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row">
                        <!--begin::Label-->
                        <div class="d-flex justify-content-between">
                            <div>
                                <label class="required form-label">Tanggal Time Off</label>
                            </div>
                            <div>
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input h-20px w-30px" type="checkbox" v-model="model.useSingleDate" id="flexSwitch20x30" />
                                    <label class="form-check-label" for="flexSwitch20x30">
                                        Pilih Manual Tanggal
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!--end::Label-->
                        <div :class="model.useSingleDate ? 'd-block' : 'd-none'">
                            <div class="input-group">
                                <input type="text" id="time-off-dates" class="form-control form-control-sm" autocomplete="off">
                                <span class="input-group-text" id="btn-open-time-off-date">
                                    <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                            <path opacity="0.3" d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z" fill="black" />
                                            <path d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z" fill="black" />
                                        </svg></span>
                                </span>
                            </div>
                        </div>
                        <div :class="model.useSingleDate ? 'd-none' : 'd-block'">
                            <div class="input-group">
                                <input type="text" id="time-off-dates-range" class="form-control form-control-sm" autocomplete="off">
                                <span class="input-group-text" id="btn-open-time-off-date-range">
                                    <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                            <path opacity="0.3" d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z" fill="black" />
                                            <path d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z" fill="black" />
                                        </svg></span>
                                </span>
                            </div>
                        </div>
                        <h1></h1>
                        <div v-if="showDayLimitAlert" class="mt-3">
                            <div class="alert alert-danger"><i class="bi bi-exclamation-circle text-danger"></i> Jumlah hari melebihi batas pengambilan</div>
                        </div>
                        <!-- <h1>@{{ takenDaysCount }}</h1> -->
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
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="isDisableSubmitButton" @click="onSubmit">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
<!-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-range/4.0.2/moment-range.js" integrity="sha512-XKgbGNDruQ4Mgxt7026+YZFOqHY6RsLRrnUJ5SVcbWMibG46pPAC97TJBlgs83N/fqPTR0M89SWYOku6fQPgyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const leaveCategories = <?php echo Illuminate\Support\Js::from($leave_categories) ?>;

    window['moment-range'].extendMoment(moment);

    Vue.prototype.moment = moment;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                leaveCategories,
                model: {
                    applicationDate: '',
                    employees: [],
                    type: 'sakit',
                    permissionCategory: '',
                    leaveCategory: '',
                    timeOffDates: [],
                    timeOffDatesRange: {
                        startDate: '{{ date("Y-m-d") }}',
                        endDate: '{{ date("Y-m-d") }}',
                    },
                    attachment: null,
                    note: '',
                    approvalStatus: false,
                    useSingleDate: false,
                },
                submitLoading: false,
            }
        },
        computed: {
            employeesRemainingLeaves() {
                const self = this;

                const {
                    employees
                } = this;
                const selectedEmployees = this.model.employees;
                // const takenDatesCount = this.model.timeOffDates.length;

                let takenDatesCount = 0;
                const useSingleDate = self.model.useSingleDate;
                if (useSingleDate) {
                    takenDatesCount = self.model.timeOffDates.length;
                } else {
                    takenDatesCount = self.rangeDates.length;
                }

                return employees
                    .filter(employee => selectedEmployees.includes(employee.id.toString()))
                    .map(employee => {
                        let remaining = employee?.leave?.remaining;
                        let background = 'bg-light'
                        // if (employee.leave) {
                        //     remaining = Number(employee?.leave?.total - employee?.leave?.taken);
                        // }

                        const selectedLeaveCategory = self.selectedLeaveCategory;

                        if (selectedLeaveCategory && selectedLeaveCategory?.type == 'annual_leave') {
                            if (takenDatesCount > remaining) {
                                background = 'bg-light-danger';
                            }
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

            },
            selectedLeaveCategory() {
                const self = this;
                const leaveCategoryId = self.model.leaveCategory;
                const [selectedLeaveCategory] = self.leaveCategories.filter(leaveCategory => leaveCategory.id == leaveCategoryId);

                return selectedLeaveCategory || null;
            },
            isExceedLeave() {
                const takenDatesCount = this.takenDaysCount;
                const exceedLeavesEmployees = this.employeesRemainingLeaves.filter(employee => takenDatesCount > employee.remaining);

                if (exceedLeavesEmployees.length > 0) {
                    return true;
                }

                return false;
            },
            formattedTimeOffDates: function() {
                const self = this;
                const useSingleDate = self.model.useSingleDate;
                if (useSingleDate) {
                    return self.model.timeOffDates.map(date => moment(date).format("YYYY-MM-DD")).join();
                } else {
                    return self.rangeDates.map(date => moment(date).format("YYYY-MM-DD")).join();
                }

                return "";
            },
            rangeDates() {
                const startDate = moment(this.model.timeOffDatesRange.startDate, 'YYYY-MM-DD');
                const endDate = moment(this.model.timeOffDatesRange.endDate, 'YYYY-MM-DD');
                const range = moment().range(startDate, endDate);
                const dates = Array.from(range.by('days')).map(m => m.format('YYYY-MM-DD'));

                return dates;
            },
            takenDaysCount() {
                const self = this;
                const useSingleDate = self.model.useSingleDate;
                if (useSingleDate) {
                    const timeOffDates = self.model.timeOffDates;
                    return timeOffDates.length;
                } else {
                    return self.rangeDates.length;
                }

                return 0;
            },
            showDayLimitAlert() {
                const self = this;
                if (self.model.type == 'cuti') {
                    const leaveCategoryId = self.model.leaveCategory;
                    const useSingleDate = self.model.useSingleDate;
                    const [leaveCategory] = self.leaveCategories.filter(leaveCategory => leaveCategory.id == leaveCategoryId);
                    if (leaveCategory) {
                        const limitDays = leaveCategory.max_day;
                        if (self.takenDaysCount > limitDays) {
                            return true;
                        }
                    }

                    // if (this.isExceedLeave) {
                    //     return true;
                    // }

                    return false;
                }

                return false;
            },
            isDisableSubmitButton() {
                if (this.model.type == 'cuti') {
                    if (this.showDayLimitAlert) {
                        return true;
                    }
                }

                return false;
            }
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
                    const {
                        applicationDate,
                        employees,
                        type,
                        timeOffDates,
                        attachment,
                        note,
                        permissionCategory,
                        leaveCategory,
                        approvalStatus,
                    } = self.model;

                    // if (type == 'cuti' && self.isExceedLeave) {
                    //     toastr.warning('Sisa cuti karyawan tidak mencukupi');
                    //     return null;
                    // }

                    self.submitLoading = true;

                    const payload = {
                        date: applicationDate,
                        employees,
                        application_dates: self.formattedTimeOffDates,
                        // attachment,
                        note,
                        approval_status: approvalStatus ? 'approved' : 'pending',
                    }

                    let response = null;

                    if (type == 'sakit') {
                        response = await axios.post('/sick-applications', payload);
                    } else if (type == 'izin') {
                        payload.permission_category_id = permissionCategory;
                        response = await axios.post('/permission-applications', payload);
                    } else if (type == 'cuti') {
                        payload.leave_category_id = leaveCategory;
                        response = await axios.post('/leave-applications', payload);
                    }

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        setTimeout(() => {
                            document.location.href = '/time-offs';
                        }, 500);
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
        },
        mounted() {

            // console.log(dates);
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
            format: 'dd-mm-yyyy',
            multidate: true,
        }).on('changeDate', function(e) {
            // `e` here contains the extra attributes
            app.$data.model.timeOffDates = e.dates;
        });

        $('#btn-open-time-off-date').on('click', function() {
            $('#time-off-dates').datepicker('show');
        });

        $('#time-off-dates-range').daterangepicker({}, function(start, end, label) {
            // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            app.model.timeOffDatesRange.startDate = start.format('YYYY-MM-DD');
            app.model.timeOffDatesRange.endDate = end.format('YYYY-MM-DD');
        });
    })
</script>
@endsection