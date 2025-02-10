@extends('layouts.app')

@section('title', 'Laporan Cuti Diuangkan')

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
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title">
                <h3>Laporan Cuti Diuangkan</h3>
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row align-items-stretch">
                <div class="col-md-12">
                    <div class="border rounded p-8 mb-6">
                        <!-- <h3>Filter</h3> -->
                        <div class="row">
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Perusahaan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="filter.companyId" class="form-select form-select-sm">
                                    <option value="">Semua Perusahaan</option>
                                    <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Divisi</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="filter.divisionId" class="form-select form-select-sm">
                                    <option value="">Semua Divisi</option>
                                    <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Tahun</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="filter.year" class="form-select form-select-sm">
                                    <option value="2023">2024</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!-- <div class="col-md-3">

                        <label class="form-label">Periode</label>

                        <input type="month" class="form-control form-control-sm">

                    </div> -->
                        </div>
                        <div class="mt-5">
                            <button class="btn btn-primary btn-sm" @click="applyFilter"><i class="bi bi-filter"></i> Terapkan</button>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="border rounded p-8 mb-6">
                        <h3 class="mb-8">Export</h3>
                        <button class="btn btn-success btn-sm"><i class="bi bi-download"></i> .xlsx</button>
                    </div>
                </div> -->
            </div>

            <div class="row justify-content-between my-6">
                <div class="col-md-6">

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
                        <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-100 ps-15" placeholder="Cari Pegawai" />
                    </div>
                    <!--end::Search-->

                </div>
                <div class="col-md-6 text-end">
                    <a href="/reports/salaries/leaves-export?company={{ $filter['company_id'] ?? '' }}&division={{ $filter['division_id'] ?? '' }}&year={{ $filter['year'] ?? '' }}" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-row-bordered fs-7" id="attendance_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th rowspan="2" class="text-start align-middle ps-2" style="min-width: 200px;">Pegawai</th>
                            <th rowspan="2" class="align-middle" style="min-width: 100px;">Jatah Cuti</th>
                            <th colspan="12" class="align-middle" style="min-width: 100px;">Cuti Diambil</th>
                            <th rowspan="2" class="align-middle" style="min-width: 100px;">Total Cuti Diambil</th>
                            <th rowspan="2" class="align-middle" style="min-width: 100px;">Sisa Cuti</th>
                            <th rowspan="2" class="align-middle text-end" style="min-width: 120px;">Gaji Pokok (Rp)</th>
                            <th rowspan="2" class="align-middle text-end" style="min-width: 120px;">Uang Harian (Rp)</th>
                            <th rowspan="2" class="align-middle text-end" style="min-width: 120px;">Nominal (Rp)</th>
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
                                <td class="text-end">{{ number_format($employee->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($employee->uang_harian, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($employee->redeemed_leave_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <!--end::Table body-->
                </table>
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
    })
</script>
<script>
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisionsOptions = <?php echo Illuminate\Support\Js::from($divisions_options) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisionsOptions,
                filter: {
                    companyId: '{{ $filter["company_id"] }}',
                    divisionId: '{{ $filter["division_id"] }}',
                    year: '{{ $filter["year"] }}',
                }
            }
        },
        computed: {
            filteredDivisions() {
                const companyId = this.filter.companyId;
                if (companyId) {
                    return this.divisionsOptions.filter(division => division.company_id == companyId);
                }

                return [];
            },
            exportURL() {
                const {
                    companyId,
                    divisionId,
                    year,
                } = this.filter;

                return `/reports/salaries/leaves-export?company=${companyId}&division=${divisionId}&year=${year}`;
            }
        },
        methods: {
            applyFilter() {
                const {
                    companyId,
                    divisionId,
                    year,
                } = this.filter;

                document.location.href = `/reports/salaries/leaves?company=${companyId}&division=${divisionId}&year=${year}`;
            }
        }
    })
</script>
@endsection