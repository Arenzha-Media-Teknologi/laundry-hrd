@extends('layouts.app')

@section('title', 'Laporan Kehadiran Periode')

@section('content')
    <div id="kt_content_container" class="container-xxl mb-5 pb-5">
        <!--begin::Row-->
        <!--begin::Main column-->
        <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
            <!--begin::Order details-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Laporan Kehadiran Periode (Per Pegawai)</h2>
                    </div>
                    <div class="card-toolbar">
                        <select class="form-select form-select-solid" @change="onSelectAttendancesReportType($event)">
                            <option value="/reports/attendances/all"
                                <?= request()->is('reports/attendances/all') ? 'selected' : '' ?>>Absensi Seluruh Pegawai
                            </option>
                            <option value="/reports/attendances/by-employee"
                                <?= request()->is('reports/attendances/by-employee') ? 'selected' : '' ?>>Absensi Per
                                Pegawai</option>
                            <option value="/reports/attendances/absences"
                                <?= request()->is('reports/attendances/absences') ? 'selected' : '' ?>>Pegawai Tidak Hadir
                            </option>
                            <option value="/reports/leaves/all"
                                <?= request()->is('reports/leaves/all') ? 'selected' : '' ?>>Cuti Seluruh Pegawai</option>
                            <option value="/reports/leaves/by-employee"
                                <?= request()->is('reports/leaves/by-employee') ? 'selected' : '' ?>>Cuti Per Pegawai
                            </option>
                            <option value="/reports/attendances-period"
                                <?= request()->is('reports/attendances-period') ? 'selected' : '' ?>>Laporan Kehadiran
                                Periode</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Pegawai</label>
                                <select class="form-select form-select-sm" required name="employee_id" id="select-employee">
                                    <option value="">Pilih Pegawai</option>
                                    @foreach ($employees as $item)
                                        <option value="{{ $item->id }}"
                                            <?= request('employee_id') == $item->id ? 'selected' : '' ?>>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" required class="form-control form-control-sm" id="start_date"
                                    name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" required class="form-control form-control-sm" id="end_date"
                                    name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i>
                                    Terapkan</button>
                            </div>
                        </div>
                    </form>
                    @isset($employee)
                        <div class="mt-5">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="attendance_table">
                                    <thead class="bg-light-primary">
                                        <!--begin::Table row-->
                                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-150px ps-2">Pegawai</th>
                                            <th>Status</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Lembur (Menit)</th>
                                            <th>Keterlambatan (Menit)</th>
                                            <!-- <th>Foto</th> -->
                                            <th class="text-end min-w-70px pe-2">Action</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <tbody class="fw-bold text-gray-600">
                                        @foreach ($attendances as $key => $attendance)
                                            <tr>
                                                <td class="text-start">
                                                    {{ $attendance->employee->name }}
                                                    <span class="text-muted d-block fs-7">{{ $attendance->date }}</span>
                                                    <span class="text-muted d-block fs-7">
                                                        {{ $attendance->employee->office->division->company->initial }} -
                                                        {{ $attendance->employee->office->division->initial }} -
                                                        {{ $attendance->employee->number }} |
                                                        {{ $attendance->employee->office->division->company->name ?? 'PERUSAHAAN' }}
                                                        -
                                                        {{ $attendance->employee->office->division->name ?? 'DIVISI' }} -
                                                        {{ $attendance->employee->office->name ?? 'KANTOR' }}
                                                    </span>
                                                    <span class="text-muted d-block fs-7">
                                                        {{ $attendance->employee->activeCareer->jobTitle->name ?? 'JABATAN' }}
                                                        -
                                                        {{ $attendance->employee->activeCareer->jobTitle->designation->name ?? 'DEPARTEMEN' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="badge text-uppercase" :class="badgeColor(`{{ $attendance->status }}`)">{{ $attendance->status }}</div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $attendance->clock_in_time ?? '-' }}
                                                    <div class="d-flex">
                                                        @isset($attendance->clock_in_latitude)
                                                            <a href="{{ 'https://www.google.com/maps/place/' . $attendance->clock_in_latitude . ',' . $attendance->clock_in_longitude }}"
                                                                target="_blank" class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <i class="bi bi-geo-alt-fill"></i>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        @endisset
                                                        @isset($attendance->clock_in_attachment)
                                                            <a href="{{ $attendance->clock_in_attachment }}" target="_blank"
                                                                class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <i class="bi bi-card-image"></i>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        @endisset

                                                        @isset($attendance->clock_in_note)
                                                            <button data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-title="{{ $attendance->clock_in_note }}" target="_blank"
                                                                class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <i class="bi bi-sticky-fill"></i>
                                                            </button>
                                                        @endisset
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $attendance->clock_out_time ?? '-' }}
                                                    <div class="d-flex">
                                                        @isset($attendance->clock_out_latitude)
                                                            <a href="{{ 'https://www.google.com/maps/place/' . $attendance->clock_out_latitude . ',' . $attendance->clock_out_longitude }}"
                                                                target="_blank" class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <i class="bi bi-geo-alt-fill"></i>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        @endisset
                                                        @isset($attendance->clock_out_attachment)
                                                            <a href="{{ $attendance->clock_out_attachment }}" target="_blank"
                                                                class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <i class="bi bi-card-image"></i>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        @endisset
                                                        @isset($attendance->clock_out_note)
                                                            <button data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-title="{{ $attendance->clock_out_note }}" target="_blank"
                                                                class="btn btn-sm btn-icon btn-light-warning ms-2">
                                                                <i class="bi bi-sticky-fill"></i>
                                                            </button>
                                                        @endisset
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $attendance->overtime ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $attendance->time_late ?? '-' }}
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex">
                                                        @can('update', App\Models\Attendance::class)
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-light-info ms-2 btn-edit"
                                                                data-bs-toggle="modal" data-bs-target="#modalEditAttendance"
                                                                @click="onOpenEditModal({{ $attendance }})">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <span class="svg-icon svg-icon-5 m-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none">
                                                                        <path opacity="0.3"
                                                                            d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                            fill="black" />
                                                                        <path
                                                                            d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                            fill="black" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </button>
                                                        @endcan
                                                        @can('delete', App\Models\Attendance::class)
                                                            <button type="button"
                                                                class="btn btn-sm btn-icon btn-light-danger ms-2"
                                                                @click="openDeleteConfirmation({{ $attendance->id }})">
                                                                <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                                                <span class="svg-icon svg-icon-5 m-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none">
                                                                        <path
                                                                            d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                            fill="currentColor" />
                                                                        <path opacity="0.5"
                                                                            d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                            fill="currentColor" />
                                                                        <path opacity="0.5"
                                                                            d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                            fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $attendances->withQueryString() }}
                            </div>
                        </div>
                    @endisset
                </div>
            </div>
            <div class="modal fade" tabindex="-1" id="modalEditAttendance">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Kehadiran</h5>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path
                                            d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                    </svg>
                                </span>
                            </div>
                            <!--end::Close-->
                        </div>

                        <div class="modal-body py-10 px-lg-17">
                            <!--begin::Scroll-->
                            <div class="scroll-y me-n7 pe-7" id="kt_modal_add_division_scroll" data-kt-scroll="true"
                                data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                                data-kt-scroll-dependencies="#kt_modal_add_division_header"
                                data-kt-scroll-wrappers="#kt_modal_add_division_scroll" data-kt-scroll-offset="300px">
                                <div v-if="model.edit.employee !== null" class="mb-5">
                                    <h3>@{{ model?.edit?.employee?.name }}</h3>
                                    <span class="text-muted fs-5">@{{ model?.edit?.employee?.number }}</span>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="d-flex flex-column flex-md-row gap-5 mb-7">
                                    <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                                        <!--begin::Label-->
                                        <label class="required form-label">Jam Masuk</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="time" v-model="model.edit.clockInAt" class="form-control"
                                            value="">
                                        <!--end::Input-->
                                    </div>
                                    <div class="fv-row flex-row-fluid">
                                        <!--begin::Label-->
                                        <label class="required form-label">Jam Keluar</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="time" v-model="model.edit.clockOutAt" class="form-control">
                                        <!--end::Input-->
                                    </div>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="row mb-7">
                                    <div class="fv-row col-md-6 fv-plugins-icon-container">
                                        <!--begin::Label-->
                                        <label class="form-label">Jumlah Lembur (Menit)</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="number" v-model="model.edit.overtime" class="form-control"
                                            min="0" placeholder="Masukkan jumlah lembur">
                                        <!--end::Input-->
                                    </div>
                                    <div class="fv-row col-md-6">
                                        <!--begin::Label-->
                                        <label class="form-label">Jumlah Keterlambatan (Menit)</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="number" v-model="model.edit.timeLate" class="form-control"
                                            min="0" placeholder="Masukkan jumlah keterlambatan">
                                        <!--end::Input-->
                                    </div>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Scroll-->
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" :data-kt-indicator="submitLoading ? 'on' : null"
                                class="btn btn-primary" :disabled="editButtonDisabled" @click="updateAttendance">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Mengirim data...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('pagescript')
        <script>
            const closeModal = (el) => {
                if (el) {
                    const modalElement = document.querySelector(el);
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                }
            }

            var app = new Vue({
                el: '#kt_content_container',
                data() {
                    return {
                        model: {
                            edit: {
                                id: null,
                                employee: null,
                                employeeId: null,
                                workingPatternId: null,
                                status: null,
                                clockInAt: null,
                                clockOutAt: null,
                                overtime: null,
                                timeLate: null,
                                date: null,
                            }
                        },
                        submitLoading: false,
                        selectedAttendancesReportType: '<?= request()->path() ?>',
                    }
                },
                computed: {
                    editButtonDisabled() {
                        if (this.submitLoading) {
                            return true;
                        }
                        return false;
                    },
                },
                methods: {
                    badgeColor(status) {
                        switch (status) {
                            case 'hadir':
                                return 'badge-success';
                            case 'sakit':
                                return 'badge-warning';
                            case 'izin':
                                return 'badge-primary';
                            case 'cuti':
                                return 'badge-info';
                            default:
                                return 'badge-light';
                        }
                    },
                    onSelectAttendancesReportType(event) {
                        window.location.href = event.target.value;
                    },
                    async updateAttendance() {
                        let self = this;
                        try {
                            const {
                                id,
                                employeeId,
                                workingPatternId,
                                status,
                                clockInAt,
                                clockOutAt,
                                overtime,
                                timeLate,
                                date,
                            } = self.model.edit;

                            self.submitLoading = true;

                            const response = await axios.post(`/attendances/${id}`, {
                                employee_id: employeeId,
                                date: date,
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
                                let message = response?.data?.message;
                                if (!message) {
                                    message = 'Data berhasil disimpan'
                                }

                                const data = response?.data?.data;
                                // if (data.employees) {
                                //     self.employees = data.employees;
                                // }
                                closeModal('#modalEditAttendance');
                                toastr.success(message);
                                setTimeout(() => {
                                    location.reload();
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
                    },
                    onOpenEditModal(data) {
                        const self = this;
                        if (data) {
                            const employee = data.employee;
                            self.model.edit.id = data.id;
                            self.model.edit.employee = employee;
                            self.model.edit.employeeId = employee.id;
                            self.model.edit.workingPatternId = data.working_pattern_id;
                            self.model.edit.status = data.status;
                            self.model.edit.clockInAt = data.clock_in_time;
                            self.model.edit.clockOutAt = data.clock_out_time;
                            self.model.edit.overtime = data.overtime;
                            self.model.edit.timeLate = data.time_late;
                            self.model.edit.date = data.date;
                        }
                    },
                    openDeleteConfirmation(id) {
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
                                return self.sendDeleteRequest(id);
                            },
                            allowOutsideClick: () => !Swal.isLoading(),
                            backdrop: true,
                        })
                    },
                    sendDeleteRequest(id) {
                        const self = this;
                        return axios.delete('/attendances/' + id)
                            .then(function(response) {
                                let message = response?.data?.message;
                                if (!message) {
                                    message = 'Data berhasil disimpan'
                                }
                                // self.deleteCompany(id);
                                // redrawDatatable();
                                toastr.success(message);
                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            })
                            .catch(function(error) {
                                // console.log(error.data);
                                let message = error?.response?.data?.message;
                                if (!message) {
                                    message = 'Something wrong...'
                                }
                                toastr.error(message);
                            });
                    },
                }
            });
            $('#select-employee').select2();
        </script>
    @endsection
