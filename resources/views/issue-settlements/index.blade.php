@extends('layouts.app')

@section('title', 'Kantor')

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
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Divisi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Tindakan</h1>
            <p class="text-muted mb-0">Daftar semua tindakan</p>
        </div>
        <!-- <div class="d-flex">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <a href="/offices/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kantor</a>
            </div>
        </div> -->
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Tindakan" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table class=" use-datatable table align-middle table-row-dashed fs-6" id="office_table">
                    <!--begin::Table head-->
                    <thead class="bg-light-primary">
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-150px ps-2">Pegawai</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Waktu Konfirmasi</th>
                            <th class="text-center">Yang Mengkonfirmasi</th>
                            <th class="text-center">Perusahaan</th>
                            <th class="text-center">Divisi</th>
                            <th class="text-center">Kantor</th>
                            <th class="text-center">Alasan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-bold text-gray-600 fs-7">
                        @foreach($issue_settlements as $issue_settlement)
                        <tr>
                            <td class="ps-2">
                                <div>
                                    <a href="/employees/{{ $issue_settlement->issueSettlementable->employee->id ?? '#' }}/detail-v2" class="text-gray-800 text-hover-primary">{{ $issue_settlement->issueSettlementable->employee->name }}</a>
                                </div>
                                <span class="text-muted d-block fs-7">
                                    {{ $issue_settlement->issueSettlementable->employee->office->division->company->initial ?? '#' }}-{{ $issue_settlement->issueSettlementable->employee->office->division->initial ?? '#' }}-{{ $issue_settlement->issueSettlementable->employee->number ?? '#' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $issue_settlement->issueSettlementable->date ?? '#' }}</td>
                            <td class="text-center">{{ $issue_settlement->created_at ?? '#' }}</td>
                            <td class="text-center">{{ $issue_settlement->createdByEmployee->name ?? '#' }}</td>
                            <td class="text-center">{{ $issue_settlement->issueSettlementable->employee->office->division->company->name ?? '#' }}</td>
                            <td class="text-center">{{ $issue_settlement->issueSettlementable->employee->office->division->name ?? '#' }}</td>
                            <td class="text-center">{{ $issue_settlement->issueSettlementable->employee->office->name ?? '#' }}</td>
                            <td class="text-center">
                                @if($issue_settlement->type == "late_attendance")
                                <span class="badge badge-light-danger">Terlambat</span>
                                @elseif($issue_settlement->type == "outside_attendance")
                                <span class="badge badge-light-danger">Di Luar Area</span>
                                @elseif($issue_settlement->type == "running_activity")
                                <span class="badge badge-light-danger">Aktifitas Berjalan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm btn-icon" @click="openDeleteConfirmation({{ $issue_settlement->id }})"><i class="bi bi-trash"></i></button>
                            </td>
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

        datatable = $('.use-datatable').DataTable({
            "columnDefs": [{
                "targets": 3,
                // "searchable": false
                "className": "text-end",
            }],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                offices,
            }
        },
        methods: {
            // COMPANY METHODS
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
                return axios.delete('/issue-settlements/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        // self.deleteOffice(id);
                        // redrawDatatable();
                        toastr.success(message);
                        setTimeout(() => {
                            document.location.reload();
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
            deleteOffice(id) {
                this.offices = this.offices.filter(office => office.id !== id);
            },
        },
    })
</script>
@endsection