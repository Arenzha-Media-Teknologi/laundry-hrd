@extends('layouts.app')

@section('title', 'Pengajuan Lembur')

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
    <div class="d-flex align-items-center justify-content-between pt-5">
        <div>
            <h1 class="mb-0">Daftar Pengajuan Lembur</h1>
            <p class="text-muted mb-0">Daftar semua pengajuan lembur</p>
        </div>
        <div class="d-flex">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <a href="/overtime-applications-v2/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pengajuan Lembur</a>
            </div>
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Kantor" />
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="use-datatable table align-middle table-row-dashed fs-6" id="office_table">
                <thead class="bg-light-primary">
                    <tr class="text-center text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th class="ps-2">Nomor</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Pegawai</th>
                        <th>Tgl. Kirim</th>
                        <th>Status</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="fw-bold text-gray-800 fs-6 fw-bold text-center">
                    @foreach($overtime_applications as $overtimeApplication)
                    <tr>
                        <td class="ps-3 fw-bolder">{{ $overtimeApplication->number }}</td>
                        <td>{{ $overtimeApplication->date }}</td>
                        <td><span class="badge badge-secondary text-capitalize">{{ $overtimeApplication->type }}</span></td>
                        <td class="text-start">
                            @foreach($overtimeApplication->members as $member)
                            <div>{{ $member->employee->name ?? '-' }}</div>
                            @endforeach
                        </td>
                        <td>
                            {{ $overtimeApplication->delivery_date }}
                        </td>
                        <td>
                            @if($overtimeApplication->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                            @elseif($overtimeApplication->status == 'confirmed')
                            <span class="badge badge-success">Dikonfirmasi</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="/overtime-applications-v2/{{ $overtimeApplication->id }}/print" target="_blank" class="btn btn-sm btn-success btn-icon"><i class="bi bi-printer-fill"></i></a>
                            <a href="/overtime-applications-v2/{{ $overtimeApplication->id }}/confirmation" class="btn btn-sm btn-info">Konfirmasi</a>
                            @if($overtimeApplication->status == "pending")
                            <a href="/overtime-applications-v2/{{ $overtimeApplication->id }}/edit" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
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
                return axios.delete('/offices/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteOffice(id);
                        // redrawDatatable();
                        toastr.success(message);
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