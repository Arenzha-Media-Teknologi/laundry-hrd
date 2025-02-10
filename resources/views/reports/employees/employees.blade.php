@extends('layouts.app')

@section('title', 'Daftar Karyawan')

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
                <h3>Daftar Karyawan</h3>
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
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="form-label">Periode Tanggal Masuk</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="date" v-model="filter.startDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" v-model="filter.endDate" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3 mt-5">
                                <!--begin::Label-->
                                <label class="form-label">Status</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="filter.status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
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
                    <a href="/reports/employees/all-export?company={{ $filter['company_id'] ?? '' }}&division={{ $filter['division_id'] ?? '' }}&start_date={{ $filter['start_date'] ?? '' }}&end_date={{ $filter['end_date'] ?? '' }}&status={{ $filter['status'] ?? '' }}" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-rounded border gy-7 gs-7" id="kt_customers_table">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                            <th>No.</th>
                            <th class="min-w-150px">ID</th>
                            <th>Divisi</th>
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-150px">Tgl. Lahir</th>
                            <th class="min-w-150px">Tgl. Masuk</th>
                            <th class="min-w-150px">NPWP</th>
                            <th>Status PTKP</th>
                            <th class="min-w-250px">Alamat Domisili</th>
                            <th>HP</th>
                            <th>Kontak Darurat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($divisions as $division)
                        <tr class="bg-light-primary">
                            <td colspan="18" class="fw-bold fs-6 text-gray-800">{{ $division->name }} - {{ $division->company->name ?? '' }}</td>
                        </tr>
                        @foreach($division->employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->number }}</td>
                            <td>{{ $employee->office->division->name ?? '' }}</td>
                            <td class="fw-bold">{{ $employee->name }}</td>
                            <td>{{ $employee->date_of_birth }}</td>
                            <td>{{ $employee->start_work_date }}</td>
                            <td>{{ $employee->npwp_number }}</td>
                            <td class="text-uppercase">{{ $employee->npwp_status }}</td>
                            <td>{{ $employee->address }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->emergency_contact_phone }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
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


        datatable = $('#kt_customers_table').DataTable({
            // "columnDefs": [{
            //     "targets": 3,
            //     // "searchable": false
            //     "className": "text-end",
            // }],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            },
            "order": false,
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
    const divisionsOptions = <?php echo Illuminate\Support\Js::from($divisions_options) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisions,
                divisionsOptions,
                filter: {
                    companyId: '{{ $filter["company_id"] }}',
                    divisionId: '{{ $filter["division_id"] }}',
                    startDate: '{{ $filter["start_date"] }}',
                    endDate: '{{ $filter["end_date"] }}',
                    status: '{{ $filter["status"] }}',
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
                    startDate,
                    endDate,
                    status,
                } = this.filter;

                return `/reports/employees/all-export?company=${companyId}&division=${divisionId}&start_date=${startDate}&end_date=${endDate}&status=${status}`;
            }
        },
        methods: {
            applyFilter() {
                const {
                    companyId,
                    divisionId,
                    startDate,
                    endDate,
                    status,
                } = this.filter;

                document.location.href = `/reports/employees/all?company=${companyId}&division=${divisionId}&start_date=${startDate}&end_date=${endDate}&status=${status}`;
            }
        }
    })
</script>
@endsection