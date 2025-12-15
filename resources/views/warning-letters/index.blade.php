@extends('layouts.app')

@section('title', 'Surat Peringatan')

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
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Surat Peringatan</h1>
            <p class="text-muted mb-0">Daftar semua surat peringatan</p>
        </div>
        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
            <!--begin::Add customer-->
            <a href="/warning-letters/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Surat Peringatan</a>
            <!--end::Add customer-->
        </div>
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
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Surat Peringatan" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Table-->
            <table class="use-datatable table align-middle table-row-dashed fs-6" id="warning_letter_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th class="ps-2">Nomor</th>
                        <th class="ps-2">Pegawai</th>
                        <th>Tipe</th>
                        <th>Tanggal Mulai Efektif</th>
                        <th>Tanggal Akhir Efektif</th>
                        <th>Deskripsi</th>
                        <th>Lampiran</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
                    <tr v-for="(warningLetter, index) in warningLetters" :key="warningLetter.id">
                        <!--begin::Number=-->
                        <td class="ps-2">
                            <span class="text-gray-800" v-cloak>@{{ warningLetter?.number || "-" }}</span>
                        </td>
                        <!--end::Number=-->
                        <!--begin::Employee=-->
                        <td class="ps-2">
                            <span class="text-gray-800" v-cloak>@{{ warningLetter?.employee?.name || "-" }}</span>
                        </td>
                        <!--end::Employee=-->
                        <!--begin::Type=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ formatType(warningLetter?.type) }}</span>
                        </td>
                        <!--end::Type=-->
                        <!--begin::Start Date=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ formatDate(warningLetter?.effective_start_date) }}</span>
                        </td>
                        <!--end::Start Date-->
                        <!--begin::End Date=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ formatDate(warningLetter?.effective_end_date) }}</span>
                        </td>
                        <!--end::End Date-->
                        <!--begin::Description=-->
                        <td>
                            <span class="text-gray-800 text-truncate d-inline-block" style="max-width: 200px;" v-cloak>@{{ warningLetter?.description || "-" }}</span>
                        </td>
                        <!--end::Description=-->
                        <!--begin::Attachments=-->
                        <td>
                            <span v-if="warningLetter?.attachments && warningLetter?.attachments.length > 0" class="badge badge-light-primary">
                                <i class="bi bi-paperclip me-1"></i>
                                @{{ warningLetter?.attachments.length }} file
                            </span>
                            <span v-else class="text-gray-400">
                                -
                            </span>
                        </td>
                        <!--end::Attachments=-->
                        <!--begin::Action=-->
                        <td class="text-end pe-2">
                            <div class="d-flex justify-content-end">
                                <!--begin::Detail link-->
                                <a :href="`/warning-letters/${warningLetter.id}/detail`" class="btn btn-sm btn-icon btn-secondary ms-2" title="Detail">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                <!--end::Detail link-->
                                <!--begin::Edit link-->
                                <a :href="`/warning-letters/${warningLetter.id}/edit`" class="btn btn-sm btn-icon btn-light-info ms-2" title="Edit">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                <!--end::Edit link-->
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openDeleteConfirmation(warningLetter.id)" title="Hapus">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>
                                <a :href="`/warning-letters/${warningLetter.id}/print`" target="_blank" class="btn btn-sm btn-icon btn-light-success ms-2" title="Print">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                            </div>
                        </td>
                        <!--end::Action=-->
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
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
                "targets": 7,
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

        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const warningLetters = <?php echo Illuminate\Support\Js::from($warningLetters) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                warningLetters,
            }
        },
        methods: {
            formatDate(date) {
                if (!date) return '-';
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },
            formatType(type) {
                if (!type) return '-';
                const typeMap = {
                    'sp1': 'SP1',
                    'sp2': 'SP2'
                };
                return typeMap[type.toLowerCase()] || type.toUpperCase();
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
                return axios.delete('/warning-letters/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil dihapus'
                        }
                        self.deleteWarningLetter(id);
                        toastr.success(message);
                    })
                    .catch(function(error) {
                        console.error(error)
                        let message = error?.response?.data?.message;
                        if (!message) {
                            message = 'Something wrong...'
                        }
                        toastr.error(message);
                    });
            },
            deleteWarningLetter(id) {
                this.warningLetters = this.warningLetters.filter(warningLetter => warningLetter.id !== id);
            },
        },
    })
</script>
@endsection