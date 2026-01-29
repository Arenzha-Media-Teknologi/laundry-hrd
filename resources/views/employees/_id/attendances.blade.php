@extends('layouts.app')

@section('title', $employee->name . ' - Absensi')

@section('head')

@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Ringkasan Absensi</h3>
            </div>
            <!--end::Card title-->
            <div class="card-toolbar">
                <div class="input-group">
                    <input class="form-control" placeholder="Pick date rage" id="kt_daterangepicker_1" />
                    <span class="input-group-text" id="basic-addon2">
                        <!-- <i class="fas fa-calendar fs-4"></i> -->
                        <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path opacity="0.3" d="M19 3.40002C18.4 3.40002 18 3.80002 18 4.40002V8.40002H14V4.40002C14 3.80002 13.6 3.40002 13 3.40002C12.4 3.40002 12 3.80002 12 4.40002V8.40002H8V4.40002C8 3.80002 7.6 3.40002 7 3.40002C6.4 3.40002 6 3.80002 6 4.40002V8.40002H2V4.40002C2 3.80002 1.6 3.40002 1 3.40002C0.4 3.40002 0 3.80002 0 4.40002V19.4C0 20 0.4 20.4 1 20.4H19C19.6 20.4 20 20 20 19.4V4.40002C20 3.80002 19.6 3.40002 19 3.40002ZM18 10.4V13.4H14V10.4H18ZM12 10.4V13.4H8V10.4H12ZM12 15.4V18.4H8V15.4H12ZM6 10.4V13.4H2V10.4H6ZM2 15.4H6V18.4H2V15.4ZM14 18.4V15.4H18V18.4H14Z" fill="black" />
                                <path d="M19 0.400024H1C0.4 0.400024 0 0.800024 0 1.40002V4.40002C0 5.00002 0.4 5.40002 1 5.40002H19C19.6 5.40002 20 5.00002 20 4.40002V1.40002C20 0.800024 19.6 0.400024 19 0.400024Z" fill="black" />
                            </svg></span>
                    </span>
                </div>

            </div>
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!-- begin:Row -->
            <div class="row justify-content-between align-items-center">
                <!--begin::Col-->
                <div class="col-md-5">
                    <div id="attendance_pie_chart"></div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-6">
                    <h3 class="card-title align-items-start flex-column mb-10">
                        <div class="card-label fw-bolder text-dark">Statistik Absensi</div>
                        <div class="text-gray-400 pt-2 fw-bold fs-6">Tanggal @{{ moment(dates.startDate).format("Do MMMM YYYY") }} - @{{ moment(dates.endDate).format("Do MMMM YYYY") }}</div>
                    </h3>
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom border-bottom mb-4 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-success me-5"></span> Hadir</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.hadir }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-warning me-5"></span> Sakit</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.sakit }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-primary me-5"></span> Izin</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.izin }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-info me-5"></span> Cuti</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.cuti }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical bg-secondary me-5"></span> OFF</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.off || 0 }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 border-gray-300 p-1">
                        <div>
                            <span class="fw-bolder fs-5 text-gray-700"><span class="bullet bullet-vertical me-5"></span> Tanpa Keterangan</span>
                        </div>
                        <div>
                            <span class="fw-bolder fs-5">@{{ statistics.na }}</span>
                        </div>
                    </div>
                    <!--end::Completion Item-->
                </div>
                <!--end::Col-->
            </div>
            <!-- end:Row -->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Profil Completion-->
    <div class="alert alert-info d-flex align-items-center p-5 mb-5 mb-xl-10">
        <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
        <span class="svg-icon svg-icon-2hx svg-icon-info me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="black" />
            </svg>
        </span>
        <!--end::Svg Icon-->
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-info">Informasi</h4>
            <span>Tanggal berwarna merah mengacu pada <a href="/working-patterns">pola kerja</a> dengan status <span class="fw-bold">"Hari Libur"</span></span>
        </div>
    </div>
    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view2">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Detail Absensi</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                            <th>Tanggal</th>
                            <th>Kalender</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-center">Long Shift</th>
                            <th class="text-center">Lembur (Menit)</th>
                            <th class="text-center">Keterlambatan (Menit)</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6 text-gray-700">
                        <tr v-for="attendance in attendances">
                            <td class="ps-2" :class="attendance.day_status == 'holiday' ? 'text-danger' : ''">
                                <div class="fw-bold">@{{ attendance.iso_date }}</div>
                                <span class="text-muted">@{{ attendance.day }}</span>
                            </td>
                            <td>
                                <div v-for="event in attendance.events" class="mb-3">
                                    <span class="badge badge-light-danger">@{{ event.name }}</span>
                                </div>
                            </td>
                            <td v-if="attendance.attendance !== null" class="text-center">
                                <div>
                                    <span class="text-uppercase badge" :class="statusBadgeColor(attendance.attendance).color">@{{ statusBadgeColor(attendance.attendance).text }}</span>
                                </div>
                                <div v-cloak v-if="attendance.attendance.is_permission == 1" class="mt-2">
                                    <span v-if="attendance.attendance.permission_status == 'pending'" class="badge badge-light-warning"><span class="align-middle">@{{ attendance.attendance.permission_category.name }} (Menunggu)</span> <i class="bi bi-clock text-warning align-middle"></i></span>
                                    <span v-else-if="attendance.attendance.permission_status == 'approved'" class="badge badge-light-success"><span class="align-middle">@{{ attendance.attendance.permission_category.name }} (Disetujui)</span> <i class="bi bi-check text-success align-middle"></i></span>
                                    <span v-else-if="attendance.attendance.permission_status == 'rejected'" class="badge badge-light-danger"><span class="align-middle">@{{ attendance.attendance.permission_category.name }} (Ditolak)</span> <i class="bi bi-x text-danger align-middle"></i></span>
                                </div>
                            </td>
                            <td v-else></td>
                            <td v-if="attendance.attendance !== null" class="text-center">@{{ attendance.attendance.clock_in_time }}</td>
                            <td v-else></td>
                            <td v-if="attendance.attendance !== null" class="text-center">@{{ attendance.attendance.clock_out_time }}</td>
                            <td v-else></td>
                            <td v-if="attendance.attendance !== null" class="text-center">
                                <div v-cloak v-if="attendance.attendance.is_long_shift == 1">
                                    <!-- @{{ attendance.attendance.long_shift_status }} -->
                                    <span v-if="attendance.attendance.long_shift_status == 'pending'" class="badge badge-light-warning">Pending</span>
                                    <span v-else-if="attendance.attendance.long_shift_status == 'approved'" class="badge badge-light-success">Disetujui (@{{ attendance.attendance?.long_shift_confirmer?.name ?? 'Approver' }} - @{{ attendance.attendance.long_shift_confirmed_at }})</span>
                                    <span v-else-if="attendance.attendance.long_shift_status == 'rejected'" class="badge badge-light-danger">Ditolak (@{{ attendance.attendance?.long_shift_confirmer?.name ?? 'Approver' }} - @{{ attendance.attendance.long_shift_confirmed_at }})</span>
                                </div>
                            </td>
                            <td v-else></td>
                            <td v-if="attendance.attendance !== null" class="text-center">@{{ attendance.attendance.overtime }}</td>
                            <td v-else></td>
                            <td v-if="attendance.attendance !== null" class="text-center">@{{ attendance.attendance.time_late }}</td>
                            <td v-else></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::details View-->
</div>
@endsection
@section('script')
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->
@endsection
@section('pagescript')
<script>
    moment.locale('id');
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
    const bankAccounts = <?php echo Illuminate\Support\Js::from($employee->bankAccounts) ?>;
    const attendances = <?php echo Illuminate\Support\Js::from($attendances) ?>;
    const statistics = <?php echo Illuminate\Support\Js::from($statistics) ?>;

    Vue.prototype.moment = moment

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employee,
                attendances,
                statistics,
                loading: false,
                dates: {
                    startDate: '{{ $dates["start_date"] }}',
                    endDate: '{{ $dates["end_date"] }}',
                }
            }
        },
        methods: {
            // moment() {
            //     return moment();
            // },
            async addBankAccount() {
                const self = this;
                self.bankAccount.loading = true;
                try {
                    const payload = {
                        bank_name: self.bankAccount.model.add.bankName,
                        account_number: self.bankAccount.model.add.accountNumber,
                        account_owner: self.bankAccount.model.add.accountOwner,
                        default: self.bankAccount.model.add.default,
                        employee_id: self.employee?.id,
                    }

                    const response = await axios.post('/bank-accounts', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        self.bankAccount.data = data;

                        closeModal('#bank_account_modal');

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.bankAccount.loading = false;
                }
            },
            statusBadgeColor(attendance) {
                let status = attendance?.status;
                // if (status.is_permission == 1) {
                //     status = "izin";
                // }
                const leaveCategoryName = attendance?.leave_application?.category?.name;
                switch (status) {
                    case 'hadir':
                        return {
                            text: status,
                                color: `badge-success`,
                        };
                        break;
                    case 'sakit':
                        return {
                            text: status,
                                color: `badge-warning`,
                        };
                        break;
                    case 'izin':
                        return {
                            text: status,
                                color: `badge-primary`,
                        };
                        break;
                    case 'cuti':
                        return {
                            text: `${status} (${leaveCategoryName})`,
                                color: `badge-info`,
                        };
                        break;
                    case 'off':
                        return {
                            text: 'OFF',
                                color: `badge-secondary`,
                        };
                        break;
                    default:
                        return {
                            text: 'Tanpa Keterangan',
                                color: `badge-dark`,
                        };
                }
            }
        }
    })
</script>
<script>
    const bgColor = {
        primary: KTUtil.getCssVariableValue('--bs-primary'),
        success: KTUtil.getCssVariableValue('--bs-success'),
        danger: KTUtil.getCssVariableValue('--bs-danger'),
        info: KTUtil.getCssVariableValue('--bs-info'),
        warning: KTUtil.getCssVariableValue('--bs-warning'),
        light: KTUtil.getCssVariableValue('--bs-dark'),
        secondary: KTUtil.getCssVariableValue('--bs-secondary'),
    }
    var options = {
        chart: {
            type: 'donut',
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false,
            },
            width: '100%',
        },
        legend: {
            show: false
        },
        series: [statistics.hadir, statistics.sakit, statistics.izin, statistics.cuti, statistics.off || 0, statistics.na],
        labels: ['Hadir', 'Sakit', 'Izin', 'Cuti', 'OFF', 'Tanpa Keterangan'],
        colors: [bgColor.success, bgColor.warning, bgColor.primary, bgColor.info, bgColor.secondary, bgColor.light],
    }

    var chart = new ApexCharts(document.querySelector("#attendance_pie_chart"), options);

    chart.render();

    $(function() {
        $("#kt_daterangepicker_1").daterangepicker({
            startDate: moment("{{ $dates['start_date'] }}"),
            endDate: moment("{{ $dates['end_date'] }}"),
        }, async function(start, end, label) {
            // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            const vueData = app.$data;

            vueData.loading = true;
            try {
                const startDate = start.format('YYYY-MM-DD');
                const endDate = end.format('YYYY-MM-DD');
                const params = {
                    start_date: startDate,
                    end_date: endDate,
                }

                const response = await axios.get('/employees/{{ $employee->id }}/filtered-attendances', {
                    params,
                });

                if (response) {
                    console.log(response)
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Data berhasil disimpan'
                    }

                    const data = response?.data?.data;

                    vueData.dates.startDate = startDate;
                    vueData.dates.endDate = endDate;
                    vueData.attendances = data.attendances || [];
                    vueData.statistics = data.statistics || {};

                    if (data.statistics) {
                        chart.updateSeries(
                            [data.statistics.hadir, data.statistics.sakit, data.statistics.izin, data.statistics.cuti, data.statistics.off || 0, data.statistics.na]
                        )
                    }
                }
            } catch (error) {
                let message = error?.response?.data?.message;
                if (!message) {
                    message = 'Something wrong...'
                }
                toastr.error(message);
            } finally {
                vueData.loading = false;
            }
        });
    })
</script>
@endsection