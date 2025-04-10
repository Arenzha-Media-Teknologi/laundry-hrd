@extends('layouts.app')

@section('title', 'Detail Jadwal Kerja')

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
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="mb-0">Detail Jadwal Kerja</h1>
            <p class="text-muted mb-0">Detail Jadwal Kerja Pegawai</p>
        </div>
        <div class="d-flex">
            <button class="btn btn-dark me-3" data-bs-toggle="collapse" data-bs-target=".collapseSchedule" aria-expanded="false" aria-controls="collapseSchedule"><i class="bi bi-chevron-down"></i> Semua</button>
            <a href="#" class="btn btn-primary"><i class="bi bi-pencil"></i> Ubah</a>
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    @foreach($offices as $office )
    <div class="card mb-5 mb-xxl-10">
        <div class="card-header align-items-center">
            <div class="card-title">
                <h5>{{ $office->name }}</h5>
            </div>
            <div class="card-toolbars">
                <button class="btn btn-light btn-icon" data-bs-toggle="collapse" data-bs-target="#collapseSchedule_{{ $office->id }}" aria-expanded="false" aria-controls="collapseSchedule_{{ $office->id }}"><i class="bi bi-chevron-down"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="collapse collapseSchedule" id="collapseSchedule_{{ $office->id }}">
                <div class="row">
                    @foreach($work_schedule_working_patterns as $work_schedule_working_pattern)
                    <div class="col-md-4">
                        <div class="card card-bordered">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="badge" style="background-color: <?= $work_schedule_working_pattern->color ?>">{{ $work_schedule_working_pattern->name }}</span>&nbsp;<span class="fw-bolder fs-7">{{ $work_schedule_working_pattern->start_time }} - {{ $work_schedule_working_pattern->end_time }}</span>
                                </div>
                            </div>
                            <div class="card-body p-1">
                                <ul class="list-group">
                                    @foreach($period_dates as $period_date)
                                    <li class="list-group-item list-group-item-dark text-center">{{ \Carbon\Carbon::parse($period_date)->isoFormat('LL') }}</li>
                                    <?php
                                    $items = $grouped_schedule_items[$office->id][$work_schedule_working_pattern->id][$period_date] ?? [];
                                    ?>
                                    @if(count($items) > 0)
                                    @foreach($items as $item)
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-10">
                                                {{ $item->employee->name ?? 'NAMA_PEGAWAI' }}
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="/work-schedules/{{ $work_schedule->id }}/edit?search={{ strtolower($item->employee->name) }}" target="_blank"><i class="bi bi-pencil"></i></a>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                    @else
                                    <li class="list-group-item text-center text-danger"><em>Tidak ada</em></li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
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
                    // startDate: '{{ date("Y-m-d") }}',
                    // endDate: '{{ date("Y-m-d") }}',
                    startDate: '',
                    endDate: '',
                    searchEmployeeKeyword: '',

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
            }
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

                    const response = await axios.get(`/work-schedules/action/generate-schedule?start_date=${startDate}&end_date=${endDate}`);

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

                    const response = await axios.post(`/work-schedules/store`, body);

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