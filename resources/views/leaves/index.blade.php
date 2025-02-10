@extends('layouts.app')

@section('title', 'Data Cuti')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Card-->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <span>Data Cuti</span>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="card card-bordered mb-5">
                <div class="card-header">
                    <h5 class="card-title">Daftar Cuti Bersama ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($mass_leaves as $mass_leave)
                        <li class="mb-2"><span class="badge badge-light-danger">{{ \Carbon\Carbon::parse($mass_leave->date)->isoFormat('LL') }}</span> <span class="fs-7">{{ $mass_leave->name }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="d-flex align-items-center position-relative mb-4">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-350px ps-15" placeholder="Cari Pegawai" />
                </div>
                <div>
                    <a class="btn btn-secondary" href="/reports/salaries/leaves">
                        <i class="bi bi-cash"></i>
                        <span>Uangkan Cuti</span>
                    </a>
                    <button class="btn btn-info" @click="resetLeave">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z" />
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z" />
                        </svg>
                        <span>Reset Data Cuti</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table align-middle table-row-bordered fs-6" id="attendance_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th rowspan="2" class="text-start min-w-150px align-middle ps-2">Pegawai</th>
                            <th rowspan="2" class="align-middle">Jatah Cuti</th>
                            <th colspan="12" class="align-middle">Cuti Diambil</th>
                            <th rowspan="2" class="align-middle">Total Cuti Diambil</th>
                            <th rowspan="2" class="align-middle">Sisa Cuti</th>
                            <th rowspan="2" class="text-end min-w-70px align-middle pe-2">Action</th>
                        </tr>
                        <tr class="text-center text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <?php $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] ?>
                            @foreach($months as $month)
                            <th>{{ $month }}</th>
                            @endforeach
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600">
                        <!-- <tr v-for="employee in employees" class="text-center">
                            <td class="text-start ps-2">
                                <div>
                                    <a href="#" class="text-gray-800">@{{ employee?.name || '' }}</a>
                                </div>
                                <span class="text-muted fs-7">@{{ employee?.number || '' }} | Manager Finance</span>
                            </td>
                            <td v-if="employee.leave !== null">@{{ employee?.leave?.total || 0 }}</td>
                            <td v-else class="text-center"><span class="badge badge-light-danger">Belum Aktif</span></td>
                            <td v-for="n in 12">
                                <span v-if="employee?.grouped_leave_applications !== null">@{{ employee?.grouped_leave_applications[n - 1] }}</span>
                            </td>
                            <td>@{{ employee?.leave?.taken || 0 }}</td>
                            <td>@{{ (employee?.leave?.total || 0) - (employee?.leave?.taken || 0) }}</td>
                            <td class="pe-2">
                                <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2 btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditLeave" @click="onOpenEditModal(employee)">
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                </button>
                            </td>
                            <td v-else class="pe-2">
                                <button type="button" :data-kt-indicator="employee.activateLoading ? 'on' : null" class="btn btn-sm btn-light-success ms-2" :disabled="employee.activateLoading" @click="activateLeave(employee)">
                                    <span class="indicator-label">Aktifkan</span>
                                    <span class="indicator-progress">Mengaktifkan...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </td>
                        </tr> -->
                        @foreach($employees as $employee)
                        <tr class="text-center">
                            <td class="text-start ps-2">
                                <div>
                                    <a href="#" class="text-gray-800">{{ $employee->name ?? '' }}</a>
                                </div>
                                <span class="text-muted fs-7">{{ $employee->number ?? '' }} | {{ $employee->activeCareer->jobTitle->name ?? '' }}</span>
                                <span class="text-muted fs-7 d-block">{{ str_replace("sebelumnya","",\Carbon\Carbon::parse($employee->start_work_date)->diffForHumans(date('Y-m-d'), ['parts' => 3])) }}</span>
                            </td>
                            <td>{{ $employee->leave['total'] ?? 0 }}</td>
                            @for($i = 0; $i < 12; $i++) <td>
                                <span>{{ $employee->grouped_leave_applications[$i] ?? '' }}</span>
                                </td>
                                @endfor
                                <td>{{ $employee->leave['taken'] ?? 0 }}</td>
                                <?php
                                $remainingLeave = ($employee->leave['total'] ?? 0) - ($employee->leave['taken'] ?? 0);
                                ?>
                                <td>{{ ($remainingLeave < 0) ? 0 : $remainingLeave }}</td>
                                <td class="pe-2">
                                    <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2 btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditLeave" @click="onOpenEditModal({{ json_encode($employee) }})">
                                        <span class=" svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                            </svg>
                                        </span>
                                    </button>
                                </td>
                                <!-- <td v-else class="pe-2">
                                    <button type="button" :data-kt-indicator="employee.activateLoading ? 'on' : null" class="btn btn-sm btn-light-success ms-2" :disabled="employee.activateLoading" @click="activateLeave(employee)">
                                        <span class="indicator-label">Aktifkan</span>
                                        <span class="indicator-progress">Mengaktifkan...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <!-- begin::modal -->
    <div class="modal fade" tabindex="-1" id="modalEditLeave">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Data Cuti</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                            </svg>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_division_header" data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
                        <div v-if="model.edit.employee !== null" class="mb-5">
                            <h3>@{{ model?.edit?.employee?.name }}</h3>
                            <span class="text-muted fs-5">@{{ model?.edit?.employee?.number }}</span>
                        </div>
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Jatah Cuti</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="number" v-model="model.edit.total_leave" class="form-control">
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <span class="text-muted">Total cuti harus lebih besar atau sama dengan total cuti diambil</span>
                    </div>
                    <!--end::Scroll-->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="editButtonDisabled" @click="updateLeave">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Mengirim data...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- end::modal -->
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


        datatable = $('#attendance_table').DataTable({
            order: false,
            columnDefs: [{
                targets: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15],
                orderable: false,
            }, ],
            // fixedColumns: {
            //     left: 1,
            // },
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
        // }

        const deleteTableRow = (el) => {
            const row = $(el).parents('tr');
            datatable
                .row(row)
                .remove()
                .draw();
        }

        $('#kt_customers_table').on('click', '.btn-delete', function() {
            const id = $(this).attr('data-id');
            const self = this;
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data akan dihapus",
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-light"
                },
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.delete('/companies/' + id)
                        .then(function(response) {
                            // console.log(response.data);
                            let message = response?.data?.message;
                            if (!message) {
                                message = 'Data berhasil disimpan'
                            }
                            toastr.success(message);
                            deleteTableRow(self);
                        })
                        .catch(function(error) {
                            console.log(error)
                            // console.log(error.data);
                            let message = error?.response?.data?.message;
                            if (!message) {
                                message = 'Something wrong...'
                            }
                            toastr.error(message);
                            // Swal.fire({
                            //     icon: 'error',
                            //     title: 'Oops',
                            //     text: 'Something wrong',
                            // })
                        });
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        })
        $('#kt_customers_table').on('click', '.btn-edit', function() {
            alert('btn edit clocked');
        })

    })
</script>
<script>
    const closeModal = (el) => {
        if (el) {
            const modalElement = document.querySelector(el);
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        }
    }

    const textFormatter = (text) => `<span class="text-gray-800">${text}</span>`;

    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                submitLoading: false,
                date: '{{ date("Y-m-d") }}',
                model: {
                    edit: {
                        id: null,
                        index: null,
                        employee: null,
                        total_leave: null,
                        leave: null,
                    }
                },
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                // submitLoading: false,
            }
        },
        computed: {
            editButtonDisabled() {
                // if (this.submitLoading) {
                //     return true;
                // }

                // if (!this.model.edit.leave) {
                //     return true;
                // }

                // if (this.model.edit.total_leave < this.model.edit.leave.taken) {
                //     return true;
                // }

                return false;
            }
        },
        methods: {
            async activateLeave(employee) {
                let self = this;
                if (employee) {
                    employee.activateLoading = true;
                    try {
                        const employeeId = employee.id;
                        const response = await axios.post(`/employees/${employeeId}/activate-leave`);

                        if (response) {
                            console.log(response)
                            let message = response?.data?.message;
                            if (!message) {
                                message = 'Data berhasil disimpan'
                            }

                            const data = response?.data?.data;
                            if (data) {
                                employee.leave = data;
                            }
                            toastr.success(message);
                        }
                    } catch (error) {
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    } finally {
                        employee.activateLoading = false;
                    }
                }
                return null;
            },
            async updateLeave() {
                let self = this;
                self.submitLoading = true;
                try {
                    const leaveId = self.model.edit.id;
                    const employeeId = self.model.edit.employeeId;
                    const leave = self.model.edit.leave;
                    const totalLeave = self.model.edit.total_leave;

                    if (totalLeave < leave?.taken) {
                        toastr.warning('Total cuti harus lebih besar atau sama dengan total cuti diambil');
                        return;
                    }

                    const payload = {
                        employee_id: employeeId,
                        total: totalLeave,
                    }

                    const response = await axios.post(`/leaves/${employeeId}`, payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        if (data) {
                            const [employee] = self.employees.filter(employee => employee.id == employeeId);
                            if (employee) {
                                employee.leave = data;
                            }
                        }
                        toastr.success(message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                        closeModal('#modalEditLeave');
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
            async storeAttendance() {
                let self = this;
                try {
                    const {
                        employeeId,
                        workingPatternId,
                        status,
                        clockInAt,
                        clockOutAt,
                        overtime,
                        timeLate,
                    } = self.model.add;

                    const date = self.date;

                    self.submitLoading = true;

                    const response = await axios.post('/attendances', {
                        employee_id: employeeId,
                        date,
                        working_pattern_id: workingPatternId,
                        clock_in_at: `${date} ${clockInAt}`,
                        clock_in_time: `${clockInAt}`,
                        clock_out_at: `${date} ${clockOutAt}`,
                        clock_out_time: `${clockOutAt}`,
                        overtime,
                        time_late: timeLate,
                        status,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        closeModal('#modalAddAttendance');
                        toastr.success(message);
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
            onOpenEditModal(employee) {
                const self = this;
                // console.log(employee);
                // const [employee] = self.employees.filter(employee => employee.id == employeeId);
                if (employee) {
                    // if (attendance) {
                    //     self.model.edit.id = attendanceId;
                    //     self.model.edit.employee = employee;
                    //     self.model.edit.employeeId = employee.id;
                    // }
                    self.model.edit.id = employee?.leave?.id ?? null;
                    self.model.edit.employee = employee;
                    self.model.edit.total_leave = employee?.leave?.total ?? 0;
                    self.model.edit.employeeId = employee.id ?? null;
                    self.model.edit.leave = employee?.leave ?? null;
                    // if (employee.leave) {
                    // }
                }
            },
            async resetLeave() {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data cuti akan diatur ulang. Pegawai yang memiliki masa kerja lebih dari 1 tahun akan mendapatkan 12 hari cuti",
                    icon: 'warning',
                    reverseButtons: true,
                    showCancelButton: true,
                    confirmButtonText: 'Reset',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: "btn btn-info",
                        cancelButton: "btn btn-light"
                    },
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('/leaves/reset')
                            .then(function(response) {
                                let message = response?.data?.message;
                                if (!message) {
                                    message = 'Data berhasil disimpan'
                                }
                                toastr.success(message);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            })
                            .catch(function(error) {
                                console.error(error)
                                // console.log(error.data);
                                let message = error?.response?.data?.message;
                                if (!message) {
                                    message = 'Something wrong...'
                                }
                                toastr.error(message);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
        },
    })
</script>
@endsection