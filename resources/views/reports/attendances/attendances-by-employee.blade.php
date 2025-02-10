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
                                <label class="form-label">Pegawai</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-sm" id="select-employee">
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <input v-model="filter.startDate" type="date" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <input v-model="filter.endDate" type="date" class="form-control form-control-sm">
                                    </div>
                                </div>

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
                    <div class="text-end">
                        <a href="/reports/attendances/by-employee-export?employee={{ $filter['employee_id'] }}&start_date={{ $filter['start_date'] }}&end_date={{ $filter['end_date'] }}" class="btn btn-success" target="_blank"><i class="bi bi-download"></i> Download .xlsx</a>
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
                                <tr>
                                    <td>{{ $employee->number ?? '' }}</td>
                                    <td>{{ $employee->name ?? '' }}</td>
                                    <td>{{ $employee->activeCareer->jobTitle->designation->name ?? '' }}</td>
                                    <td>{{ $employee->npwp_number ?? '' }}</td>
                                    <td>{{ $employee->npwp_status ?? '' }}</td>
                                    <td>{{ $employee->start_work_date ?? '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="division-table">
                        <div class="table-responsive">
                            <table class="table table-rounded border align-middle">
                                <thead class="bg-dark">
                                    <tr class="fw-bold fs-7 text-gray-100 border-bottom border-gray-200">
                                        <th class="min-w-200px ps-2">Tanggal</th>
                                        <th class="min-w-200px text-center">Kalender</th>
                                        <th class="min-w-200px text-center">Jam Masuk</th>
                                        <th class="min-w-200px text-center">Jam Pulang</th>
                                        <th class="min-w-200px text-center">Total Jam Kerja</th>
                                        <th class="min-w-200px text-center">Telat (Menit)</th>
                                        <th class="min-w-200px text-center">Lembur (Menit)</th>
                                        <th class="min-w-200px text-center">Absen</th>
                                        <th class="min-w-200px text-center">Sakit</th>
                                        <th class="min-w-200px text-center pe-2">Cuti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                    <?php
                                    $tableRowClass = '';
                                    if ($attendance['day_status'] == 'holiday' || count($attendance['events']) > 0) {
                                        $tableRowClass = 'bg-light-danger text-danger fw-bold';
                                    }
                                    ?>
                                    <tr class="{{ $tableRowClass }}">
                                        <td class="ps-2">
                                            <span>{{ $attendance['iso_date'] }}</span>
                                            <span class="d-block">{{$attendance['day']}}</span>
                                        </td>
                                        <td class="text-center">
                                            @foreach($attendance['events'] as $event)
                                            <span class="badge badge-danger">{{ $event->name ?? '' }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">{{ $attendance['attendance']['clock_in_time'] ?? '' }}</td>
                                        <td class="text-center">{{ $attendance['attendance']['clock_out_time'] ?? '' }}</td>
                                        <td class="text-center">{{ $attendance['attendance']['work_duration'] ?? '' }}</td>
                                        <td class="text-center">{{ $attendance['attendance']['time_late'] ?? '' }}</td>
                                        <td class="text-center">{{ $attendance['attendance']['overtime'] ?? '' }}</td>
                                        <td class="text-center">
                                            @if(!isset($attendance['attendance']))
                                            <i class="bi bi-check text-success fs-1"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(($attendance['attendance']['status'] ?? '') == 'sakit')
                                            <i class="bi bi-check text-success fs-1"></i>
                                            @endif
                                        </td>
                                        <td class="text-center pe-2">
                                            @if(($attendance['attendance']['status'] ?? '') == 'cuti')
                                            <i class="bi bi-check text-success fs-1"></i>
                                            @endif
                                        </td>
                                    </tr>
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
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employees,
                filter: {
                    employeeId: '{{ $filter["employee_id"] }}',
                    startDate: '{{ $filter["start_date"] }}',
                    endDate: '{{ $filter["end_date"] }}',
                }
            }
        },
        methods: {
            applyFilter() {
                const {
                    employeeId,
                    startDate,
                    endDate,
                } = this.filter;

                document.location.href = `/reports/attendances/by-employee?employee=${employeeId}&start_date=${startDate}&end_date=${endDate}`;
            },
            onSelectAttendancesReportType(event) {
                const URL = event.target.value;
                document.location.href = URL;
            }
        }
    })

    $('#select-employee').select2();

    if (app.$data.filter.employeeId) {
        $('#select-employee').val(app.$data.filter.employeeId).trigger('change');
    }

    $('#select-employee').on('change', function() {
        const value = $(this).val();
        app.$data.filter.employeeId = value;
    });


    // var myModalEl = document.getElementById('modal_add_employee')
    // myModalEl.addEventListener('hidden.bs.modal', function(event) {
    //     app.$data.selectedUnassignedEmployeesIds = [];
    // })
</script>
@endsection