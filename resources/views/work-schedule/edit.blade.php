@extends('layouts.app')

@section('title', 'Ubah Jadwal Kerja')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
        color: #000 !important;
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl" style="margin-top: -30px;">
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
    <div>
        <h1 class="">Ubah Jadwal Kerja</h1>
        <h4>Periode {{ \Carbon\Carbon::parse($work_schedule->start_date)->isoFormat('LL') }} - {{ \Carbon\Carbon::parse($work_schedule->end_date)->isoFormat('LL') }}</h4>
        <p class="text-muted mb-0">Ubah Jadwal Kerja Pegawai</p>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card mb-5 mb-xxl-10">
        <div class="card-header">
            <div class="card-title">
                <h3>Informasi</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row p-0 mb-5">
                <div class="col-sm-4">
                    <div class="border border-dashed border-gray-300 min-w-125px rounded pt-4 pb-2 my-3 px-2">
                        <span class="fs-4 fw-bold text-primary d-block mb-3  text-center">Jam Kerja</span>
                        <ul v-cloak class="list-group">
                            <li v-for="workScheduleWorkingPattern in workScheduleWorkingPatterns" class="list-group-item d-flex justify-content-between align-items-center"><span class="badge badge-sm" :style="`background-color: ${workScheduleWorkingPattern.color};`">@{{ workScheduleWorkingPattern.name }}</span> <span>@{{ workScheduleWorkingPattern.start_time }} - @{{ workScheduleWorkingPattern.end_time }}</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-primary d-block">Total Pegawai</span>
                        <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="36899">@{{ currencyFormat(totalEmployees) }}</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                        <span class="fs-4 fw-bold text-success d-block">Total Pendapatan</span>
                        <span v-cloak class="fs-2hx fw-bolder text-gray-900 counted" data-kt-countup="true" data-kt-countup-value="72">Rp @{{ currencyFormat(totalIncomes) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative me-2">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" v-model="model.searchEmployeeKeyword" employee-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Pegawai" id="input_search_employee" />
                </div>
                <div>
                    <select name="" id="select-office" class="form-select">
                        <option value="">Semua Outlet</option>
                        @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <!--begin::Export-->
                    <button type="button" :data-kt-indicator="saveLoading ? 'on' : null" class="btn btn-success" :disabled="saveLoading" @click="save">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <span class="indicator-label">Simpan </span>
                        <span class="indicator-progress">Mengirim data...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div v-if="generateLoading" class="w-100 d-flex justify-content-center align-items-center" style="height: 200px;">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <!--begin::Table-->
            <div v-else class="table-responsive">
                <table class="table align-middle table-row-dashed fs-7" id="employee_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#employee_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                            <!-- <th class="ps-2">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="checkbox" @change="checkAll($event)" id="checkAll" />
                                    <label class="form-check-label" for="checkAll"></label>
                                </div>
                            </th> -->
                            <th class="min-w-125px ps-2">Nama</th>
                            <th v-for="period in selectedPeriod" class="text-center">
                                <div>
                                    @{{ period.formattedDate }}
                                </div>
                                <div>
                                    <small>@{{ period.dayName }}</small>
                                </div>
                            </th>
                            <th class="text-center">Jumlah <br>Hari</th>
                            <th class="text-center">Upah</th>
                            <th class="text-center">Total <br>Pendapatan</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600">
                        <tr v-cloak v-for="(employee, employeeIndex) in searchedEmployees">
                            <!-- <td class="ps-2">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input" type="checkbox" v-model="model.checkedEmployeesIds" :value="salary.employee.id" :id="'flexCheckDefault' + salary.employee.id" />
                                    <label class="form-check-label" :for="'flexCheckDefault' + salary.employee.id"></label>
                                </div>
                            </td> -->
                            <td>
                                <div>
                                    <div>
                                        <a :href="'/employees/' + employee?.id + '/detail'" target="_blank" class="text-gray-800 text-hover-primary fw-bolder mb-1">@{{ employee?.name }}</a>
                                    </div>
                                    <div>
                                        <div v-if="employee.active_career">
                                            <small class="text-gray-700">@{{ employee?.active_career?.job_title?.name }}</small>
                                        </div>
                                    </div>
                                    <div>@{{ employee?.office?.name }}</div>
                                </div>
                            </td>
                            <td v-for="(period, periodIndex) in selectedPeriod" class="text-center">
                                <div class="d-flex">
                                    <select v-model="employee?.schedules[periodIndex].work_schedule_working_pattern_id" class="form-select form-select-sm me-2" style="width: 200px;">
                                        <option value="">Pilih Shift</option>
                                        @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
                                        <option value="{{ $work_schedule_working_pattern->id }}">{{ $work_schedule_working_pattern->name }}</option>
                                        @endforeach
                                        <!-- <option v-for="workScheduleWorkingPattern in workScheduleWorkingPatterns" :value="workScheduleWorkingPattern.id">@{{ workScheduleWorkingPattern.name }}</option> -->
                                        <option value="off">Off</option>
                                    </select>
                                    <select2 :options="offices" :emptytext="'Pilih Outlet'" v-model="employee?.schedules[periodIndex].office_id" class="form-select form-select-sm" style="width: 200px;">
                                    </select2>
                                </div>
                                <div class="mt-1" :style="`width: 100%; height: 3px; border-radius: 2px; background-color: ${workingPatternColors[employeeIndex][periodIndex]}`"></div>
                            </td>

                            <td class="text-center">@{{ employeeComputedProperties[employeeIndex].totalWorkDays }}</td>
                            <td class="text-center">Rp @{{ employee?.daily_wage?.toLocaleString('De-de') }}</td>
                            <td class="text-center">Rp @{{ employeeComputedProperties[employeeIndex].totalIncomes?.toLocaleString('De-de') }}</td>
                        </tr>
                    </tbody>
                    <!--end::Table body-->
                </table>
            </div>
            <!--end::Table-->
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
    moment.locale('id');
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
    let workScheduleWorkingPatterns = <?php echo \Illuminate\Support\Js::from($work_schedule_working_patterns) ?>;
    let offices = <?php echo \Illuminate\Support\Js::from($offices) ?>;

    const closeCompanyAddModal = () => {
        const addCompanyModal = document.querySelector('#kt_modal_add_company');
        const modal = bootstrap.Modal.getInstance(addCompanyModal);
        modal.hide();
    }

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    checkedAll: false,
                    checkedEmployeesIds: [],
                    startDate: '{{ $work_schedule->start_date }}',
                    endDate: '{{ $work_schedule->end_date }}',
                    // startDate: '',
                    // endDate: '',
                    searchEmployeeKeyword: '{{ request()->query("search") ?? "" }}',

                },
                filter: {
                    officeId: '',
                },
                selectedStartDate: '',
                selectedEndDate: '',
                generateLoading: false,
                saveLoading: false,
                employees: [],
                totalEmployees: 0,
                totalTakeHomePay: 0,
                selectedDetailEmployeeId: null,
                workScheduleWorkingPatterns,
                offices,
            }
        },
        mounted() {
            this.generate();
        },
        computed: {
            workScheduleWorkingPatternsById() {
                const workSchedules = {};
                this.workScheduleWorkingPatterns.forEach(workingPattern => {
                    workSchedules[workingPattern.id] = workingPattern;
                })

                return workSchedules;
            },
            workingPatternColors() {
                const self = this;
                return this.searchedEmployees.map(employee => {
                    return employee.schedules.map(schedule => {
                        if (schedule.work_schedule_working_pattern_id == 'off') {
                            return '#F44336';
                        }

                        if (schedule.work_schedule_working_pattern_id) {
                            return self.workScheduleWorkingPatternsById[schedule.work_schedule_working_pattern_id].color ?? '#2196F3';
                        }

                        return '#B0BEC5';
                    })
                })
            },
            employeeComputedProperties() {
                const self = this;
                return this.searchedEmployees.map(employee => {
                    const totalWorkDays = employee.schedules.filter(schedule => {
                        return schedule.work_schedule_working_pattern_id && schedule.work_schedule_working_pattern_id != 'off'
                    }).length;

                    const totalIncomes = totalWorkDays * employee.daily_wage;

                    return {
                        totalWorkDays: totalWorkDays,
                        totalIncomes: totalIncomes,
                    }
                })
            },
            totalIncomes() {
                return this.employeeComputedProperties.reduce((acc, employee) => acc + employee.totalIncomes, 0);
            },
            visibleAlert() {
                return !this.model.startDate && !this.model.endDate;
            },
            employeeIds() {
                return this.employees.map(employee => employee.id);
            },
            checkedEmployees() {
                const {
                    checkedEmployeesIds
                } = this.model;
                return this.employees.filter(employee => checkedEmployeesIds.includes(employee.id));
            },
            goodToSave() {
                if (this.model.checkedEmployeesIds.length > 0) {
                    return true;
                }

                return false;
            },
            searchedEmployees() {
                const keyword = this.model.searchEmployeeKeyword;
                const filterOfficeId = this.filter.officeId;
                return this.employees.filter(employee => {
                    if (filterOfficeId) {
                        return employee.office_id == filterOfficeId
                    }
                    return employee;
                }).filter(employee => employee.name.toLowerCase().indexOf(keyword) > -1);
            },
            selectedDetailEmployee() {
                const {
                    selectedDetailEmployeeId,
                    employees
                } = this;
                if (selectedDetailEmployeeId && employees.length) {
                    const [employee] = employees.filter(employee => employee.id == selectedDetailEmployeeId);
                    if (employee) {
                        return employee;
                    }
                }
                return null;
            },
            selectedPeriod() {
                let dates = [];
                const startDate = this.selectedStartDate;
                const endDate = this.selectedEndDate;
                if (startDate && endDate) {
                    let currentDate = moment(startDate);
                    let stopDate = moment(endDate);

                    while (currentDate <= stopDate) {
                        dates.push({
                            date: currentDate.format('YYYY-MM-DD'),
                            formattedDate: currentDate.format('DD MMM YYYY'),
                            dayName: currentDate.format('dddd') // Nama hari (Senin, Selasa, dll.)
                        });
                        currentDate.add(1, 'days');
                    }
                }

                return dates;
            }
        },
        methods: {
            moment(date) {
                return moment(date);
            },
            onChangeIncludeDeposit(selectedDetailSalary) {
                this.$nextTick(() => {
                    const includeDeposit = selectedDetailSalary.include_deposit;
                    if (includeDeposit) {
                        selectedDetailSalary.summary.total_deductions += selectedDetailSalary.total_unpaid_deposits;
                    } else {
                        selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_unpaid_deposits;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            onChangeIncludeUnredeemedDeposit(selectedDetailSalary) {
                this.$nextTick(() => {
                    const includeDeposit = selectedDetailSalary.include_unredeemed_deposit;
                    if (includeDeposit) {
                        selectedDetailSalary.summary.total_incomes += selectedDetailSalary.total_unredeemed_deposits;
                    } else {
                        selectedDetailSalary.summary.total_incomes -= selectedDetailSalary.total_unredeemed_deposits;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            onChangeIncludeLoan(selectedDetailSalary, previousSelectedLoanId = null) {
                this.$nextTick(() => {
                    // const includeLoan = selectedDetailSalary.include_loan;
                    // if(includeLoan) {
                    //     selectedDetailSalary.summary.total_deductions += selectedDetailSalary.total_loan;
                    // } else {
                    //     selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_loan;
                    // }

                    // selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;



                    // if (!selectedLoanId) {
                    //     totalLoan = 0;
                    // }

                    const includeLoan = selectedDetailSalary?.include_loan;

                    let previousTotalLoan = 0;
                    if (previousSelectedLoanId) {
                        const [selectedPreviousLoan] = selectedDetailSalary?.loan_items.filter(loanItem => loanItem.id == previousSelectedLoanId);
                        if (selectedPreviousLoan) {
                            previousTotalLoan = selectedPreviousLoan?.basic_payment || 0;
                        }
                    }

                    const selectedLoanId = selectedDetailSalary?.selected_loan_id;
                    const [selectedLoan] = selectedDetailSalary?.loan_items.filter(loanItem => loanItem.id == selectedLoanId);

                    let totalLoan = 0;

                    if (selectedLoan) {
                        totalLoan = selectedLoan?.basic_payment;
                    }
                    totalLoan = totalLoan - previousTotalLoan;

                    if (includeLoan) {

                        console.log('selected loan', selectedLoan);
                        selectedDetailSalary.total_loan = totalLoan;
                        selectedDetailSalary.summary.total_deductions += totalLoan;
                    } else {
                        if (!selectedLoanId) {
                            selectedDetailSalary.total_loan = 0;
                        }
                        selectedDetailSalary.summary.total_deductions -= selectedDetailSalary.total_loan;
                    }

                    selectedDetailSalary.summary.take_home_pay = selectedDetailSalary.summary.total_incomes - selectedDetailSalary.summary.total_deductions;
                    // console.log();
                });
            },
            checkAll(e) {
                const value = e.target.checked;
                // console.log(value)
                // console.log(e)
                if (value) {
                    const ids = this.salaries.filter(salary => salary.employee).map(salary => salary.employee.id);
                    // console.log(ids)
                    this.model.checkedEmployeesIds = ids;
                } else {
                    this.model.checkedEmployeesIds = [];
                }
            },
            async generate() {
                const self = this;
                try {
                    const startDate = this.model.startDate;
                    const endDate = this.model.endDate;

                    if (!startDate || !endDate) {
                        Swal.fire({
                            text: "Pilih tanggal",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });

                        return null;
                    }

                    self.generateLoading = true;

                    const response = await axios.get(`/work-schedules/action/generate-schedule-edit?start_date=${startDate}&end_date=${endDate}&id={{ $work_schedule->id }}`);

                    if (response) {
                        // console.log(response);
                        const employees = response?.data?.data?.employees ?? [];

                        if (employees) {
                            self.employees = employees;
                        }

                        self.totalEmployees = employees.length;
                        self.totalTakeHomePay = 0;
                        self.selectedStartDate = self.model.startDate;
                        self.selectedEndDate = self.model.endDate;
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.generateLoading = false
                }
            },
            async save() {
                const self = this;
                try {
                    const startDate = this.model.startDate;
                    const endDate = this.model.endDate;

                    if (!startDate || !endDate) {
                        Swal.fire({
                            text: "Pilih tanggal",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });

                        return null;
                    }

                    self.saveLoading = true;

                    const body = {
                        employees: self.employees,
                        start_date: startDate,
                        end_date: endDate,
                        selected_period: self.selectedPeriod,
                    }

                    const response = await axios.post(`/work-schedules/{{ $work_schedule->id }}/update`, body);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        // const data = response?.data?.data;
                        toastr.success(message);
                        // setTimeout(() => {
                        //     window.location.reload();
                        // }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.saveLoading = false
                }
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            },
            openDetailModal(employeeId) {
                this.selectedDetailEmployeeId = employeeId;
            },
            badgeColor(status) {
                const prefix = 'badge badge-';
                switch (status) {
                    case 'hadir':
                        return prefix + 'success';
                    case 'izin':
                        return prefix + 'primary';
                    case 'cuti':
                        return prefix + 'info';
                    case 'sakit':
                        return prefix + 'warning';
                    default:
                        return '';
                }
            }
        },
        watch: {
            // 'model.checkedAll': function(newValue) {
            //     if (newValue) {
            //         const ids = this.employeeIds;
            //         this.model.checkedEmployeesIds = ids;
            //     } else {
            //         this.model.checkedEmployeesIds = [];
            //     }
            // },
            'model.checkedEmployeesIds': function(newValue) {
                const {
                    employeeIds
                } = this;
                const diff = employeeIds.filter(id => !newValue.includes(id));

                if (diff.length > 0) {
                    // this.model.checkedAll = false;
                    document.getElementById('checkAll').checked = false;
                } else {
                    document.getElementById('checkAll').checked = true;
                }
            },
            'selectedDetailSalary.selected_loan_id': function(newValue, oldValue) {
                const self = this;
                if (this.selectedDetailSalary) {
                    this.onChangeIncludeLoan(this.selectedDetailSalary, oldValue);
                    // console.log('changed', newValue);
                    // console.log(this.selectedDetailSalary);
                };
            }
        },
        components: {
            select2: {
                props: ['options', 'value', 'disabled', 'emptytext'],
                template: `
        <select @change="$emit('input', $event.target.value)" class="form-select form-select-sm">
            <option value="">@{{ emptytext }}</option>
          <option v-for="option in options" :key="option.id" :value="option.id" :disabled="disabled">
            @{{ option.name }}
          </option>
        </select>
      `,
                mounted() {
                    const selectOptions = {
                        ...this.options,
                        // dropdownParent: this.mode == 'add' ? $('#addGoodModal') : $('#editGoodModal')
                    };

                    $(this.$el)
                        .select2(selectOptions)
                        .val(this.value)
                        .trigger('change')
                        .on('change', (e) => {
                            this.$emit('input', e.target.value);
                        });
                },
                watch: {
                    value(newValue) {
                        $(this.$el).val(newValue).trigger('change');
                    },
                    options() {
                        $(this.$el).select2('destroy').select2(this.options);
                    },
                },
                destroyed() {
                    $(this.$el).off().select2('destroy');
                },
            },
        }
    })
</script>
<script>
    $(function() {
        const usedDates = <?php echo \Illuminate\Support\Js::from($used_work_schedule_dates ?? []); ?>;
        $("#date-range-picker").daterangepicker({
            locale: {
                format: "DD/MM/YYYY"
            },
            autoUpdateInput: false,
            isInvalidDate: function(date) {
                var disabledDates = usedDates; // Format YYYY-MM-DD
                return disabledDates.includes(date.format('YYYY-MM-DD'));
            }
        }, function(start, end, label) {
            // alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            app.$data.model.startDate = start.format('YYYY-MM-DD');
            app.$data.model.endDate = end.format('YYYY-MM-DD');

            $('#date-range-picker').val(`${start.format('DD MMM YYYY')} - ${end.format('DD MMM YYYY')}`)
        }, );

        $('#select-office').select2();
        $('#select-office').on('change', function(e) {
            app.$data.filter.officeId = $(this).val();
        });

        // datatable = $('#employee_table').DataTable({
        //     "drawCallback": function() {
        //         console.log('redraw table...')
        //     },
        //     "language": {
        //         "infoEmpty": " ",
        //         "zeroRecords": " "
        //     }
        // });

        // const filterSearch = document.querySelector('[employee-table-filter="search"]');
        // filterSearch.addEventListener('keyup', function(e) {
        //     datatable.search(e.target.value).draw();
        // });
    })
</script>
@endsection