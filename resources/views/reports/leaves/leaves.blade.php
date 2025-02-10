@extends('layouts.app')

@section('title', 'Laporan Cuti')

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
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <h3>Laporan Sisa Cuti</h3>
            </div>
            <div class="card-toolbar">
                <select class="form-select form-select-solid" @change="onSelectAttendancesReportType($event)">
                    <option value="/reports/attendances/all" <?= request()->is('reports/attendances/all') ? 'selected' : '' ?>>Absensi Seluruh Pegawai</option>
                    <option value="/reports/attendances/by-employee" <?= request()->is('reports/attendances/by-employee') ? 'selected' : '' ?>>Absensi Per Pegawai</option>
                    <option value="/reports/attendances/absences" <?= request()->is('reports/attendances/absences') ? 'selected' : '' ?>>Pegawai Tidak Hadir</option>
                    <option value="/reports/leaves/all" <?= request()->is('reports/leaves/all') ? 'selected' : '' ?>>Cuti Seluruh Pegawai</option>
                    <option value="/reports/leaves/by-employee" <?= request()->is('reports/leaves/by-employee') ? 'selected' : '' ?>>Cuti Per Pegawai</option>
                </select>
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">

            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center position-relative mb-8">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-salary-increase-filter="search" class="form-control w-100 w-lg-50 ps-14" placeholder="Cari Pegawai" />
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/reports/attendances/all-export" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
                </div>
            </div>

            <div class="border rounded p-8 mb-6">
                <div class="row">
                    <div class="col-md-3">
                        <!--begin::Label-->
                        <label class="form-label">Perusahaan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="filter.companyId" class="form-select form-select-sm">
                            <option value="">Pilih Perusahaan</option>
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
                            <option value="">Pilih Divisi</option>
                            <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Periode</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-rounded table-striped border gy-7 gs-7" id="kt_customers_table">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                            <th>No.</th>
                            <th class="min-w-150px">ID</th>
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-150px">Tgl. Masuk</th>
                            <th class="min-w-150px">NPWP</th>
                            <th>Status PTKP</th>
                            <th class="min-w-250px">Jatah Cuti</th>
                            <th>Cuti</th>
                            <th>Absen</th>
                            <th>Sisa</th>
                            <th>Nominal</th>
                            <th>Keterangan (Jenis Cuti)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->number }}</td>
                            <td class="fw-bold">{{ $employee->name }}</td>
                            <td>{{ $employee->start_work_date }}</td>
                            <td>{{ $employee->npwp_number }}</td>
                            <td class="text-uppercase">{{ $employee->npwp_status }}</td>
                            <td>{{ $employee->leave->total ?? 0 }}</td>
                            <td>{{ $employee->leave->taken ?? 0 }}</td>
                            <td>-</td>
                            <td>{{ $employee->leave->remaining ?? 0 }}</td>
                            <td>0</td>
                            <td></td>
                        </tr>
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
    // var companies = @json('$companies');
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisions,
                filter: {
                    companyId: '',
                    divisionId: '',
                }
            }
        },
        methods: {
            onSelectAttendancesReportType(event) {
                const URL = event.target.value;
                document.location.href = URL;
            }
        },
        computed: {
            filteredDivisions() {
                const {
                    companyId
                } = this.filter;
                const {
                    divisions
                } = this;
                if (companyId) {
                    return divisions.filter(division => division.company_id == companyId);
                }

                return [];
            },
        }
    })
</script>
@endsection