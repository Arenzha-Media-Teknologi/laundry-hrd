@extends('layouts.app')

@section('title', 'Laporan BPJS Mandiri')

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
                    <h2>Rekapitulasi BPJS Mandiri</h2>
                </div>
                <!-- <div class="card-toolbar">
                    <button v-cloak v-if="model.componentId" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_employee">Tambah</button>
                </div> -->
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
                                <label class="form-label">Periode</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="filter.year" class="form-select form-select-sm">
                                    @for($i = 2021; $i <= date('Y-m-d'); $i++) <option value="{{ $i }}" <?= $i == $filter["year"] ? 'selected' : '' ?>>{{ $i }}</option>
                                        @endfor
                                </select>
                                <!--end::Input-->
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-primary btn-sm" @click="applyFilter"><i class="bi bi-filter"></i> Terapkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="division-table">
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
                                <a href="/reports/insurances/bpjs-mandiri-export?company={{ $filter['company_id'] ?? '' }}&division={{ $filter['division_id'] ?? '' }}&year={{ $filter['year'] ?? '' }}" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-rounded border gy-7 gs-7">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>No</th>
                                        <th class="min-w-200px">ID</th>
                                        <th class="min-w-200px">Nama Pemegang Polis</th>
                                        <th class="min-w-200px">Polis</th>
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
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->bpjs->mandiri_number ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                                </tbody>
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


        datatable = $('#salary-increases-datatable').DataTable({
            "ordering": false,
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            },
            fixedColumns: {
                left: 1,
            },
            // fixedHeader: {
            // header: true,
            // footer: true
            // }
        });

        const filterSearch = document.querySelector('[data-salary-increase-filter="search"]');
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
                    year: '{{ $filter["year"] }}'
                },

            }
        },
        computed: {
            filteredDivisions() {
                const companyId = this.filter.companyId;
                if (companyId) {
                    return this.divisionsOptions.filter(division => division.company_id == companyId);
                }

                return [];
            }
        },
        methods: {
            applyFilter() {
                const {
                    companyId,
                    divisionId,
                    year
                } = this.filter;

                document.location.href = `/reports/insurances/bpjs-mandiri?company=${companyId}&division=${divisionId}&year=${year}`;
            }
        }
    })
</script>
@endsection