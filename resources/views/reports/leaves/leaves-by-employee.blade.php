@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.1.0/css/fixedColumns.dataTables.min.css">
@endsection

@inject('carbon', 'Carbon\Carbon')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::Order details-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Laporan Absensi (Per Pegawai)</h2>
                </div>
                <div class="card-toolbar">
                    <!-- <button v-cloak v-if="model.componentId" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_employee">Tambah</button> -->
                    <select class="form-select form-select-solid" @change="onSelectAttendancesReportType($event)">
                        <option value="/reports/attendances/all" <?= request()->is('reports/attendances/all') ? 'selected' : '' ?>>Absensi Seluruh Pegawai</option>
                        <option value="/reports/attendances/by-employee" <?= request()->is('reports/attendances/by-employee') ? 'selected' : '' ?>>Absensi Per Pegawai</option>
                        <option value="/reports/attendances/absences" <?= request()->is('reports/attendances/absences') ? 'selected' : '' ?>>Pegawai Tidak Hadir</option>
                        <option value="/reports/leaves/all" <?= request()->is('reports/leaves/all') ? 'selected' : '' ?>>Cuti Seluruh Pegawai</option>
                        <option value="/reports/leaves/by-employee" <?= request()->is('reports/leaves/by-employee') ? 'selected' : '' ?>>Cuti Per Pegawai</option>
                    </select>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-10">
                    <div class="border rounded p-8">
                        <div class="row">
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Perusahaan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.companyId" class="form-select form-select-sm">
                                    <option value="">Pilih Perusahaan</option>
                                    <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <!--begin::Label-->
                                <label class="form-label">Pegawai</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.companyId" class="form-select form-select-sm">
                                    <option value="">Pilih Pegawai</option>
                                    <option v-for="(employee, index) in employees" :key="employee.id" :value="employee.id">@{{ employee.name }}</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-6">
                                <!--begin::Label-->
                                <label class="form-label">Periode</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="col-md-6">
                                    <select class="form-select form-select-sm">
                                        <option value="">2021</option>
                                        <option value="">2022</option>
                                    </select>
                                </div>

                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="/reports/attendances/by-employee-export" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-rounded table-striped border gy-7 gs-7">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Divisi</th>
                                    <th>NPWP</th>
                                    <th>Status PTKP</th>
                                    <th>Tgl Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="division-table">
                        <div class="table-responsive">
                            <table class="table table-rounded table-striped border gy-7 gs-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th class="min-w-200px">Bulan</th>
                                        <th class="min-w-200px">Absen</th>
                                        <th class="min-w-200px">Cuti</th>
                                        <th class="min-w-200px">Sisa</th>
                                        <th class="min-w-200px">Nominal</th>
                                        <th class="min-w-200px">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Januari</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card header-->
        </div>
    </div>
    <!--end::Main column-->
    <!-- <div class="d-flex flex-column flex-lg-row">
    </div> -->
    <!--end::Row-->
    <!-- end::card -->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
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
                employees,
                model: {
                    companyId: '',
                },
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