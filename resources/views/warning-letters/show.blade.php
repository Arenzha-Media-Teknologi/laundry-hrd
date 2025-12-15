@extends('layouts.app')

@section('title', 'Detail Surat Peringatan')

@section('head')
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
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Detail Surat Peringatan</h2>
            </div>
            <div class="card-toolbar">
                <a href="/warning-letters" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="my-10">Data Surat Peringatan</h2>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Nomor</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            @{{ warningLetter?.number || '-' }}
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Pegawai</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            @{{ warningLetter?.employee?.name || '-' }}
                            <span class="text-muted" v-if="warningLetter?.employee?.active_career?.job_title">- @{{ warningLetter?.employee?.active_career?.job_title?.name }}</span>
                            <span class="text-muted" v-if="warningLetter?.employee?.office?.division?.company">(@{{ warningLetter?.employee?.office?.division?.company?.name }})</span>
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Tipe</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            @{{ formatType(warningLetter?.type) }}
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column flex-md-row gap-5 mb-10">
                        <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="form-label fw-bold">Tanggal Mulai Efektif</label>
                            <!--end::Label-->
                            <!--begin::Value-->
                            <div class="text-gray-800 fs-6">
                                @{{ formatDate(warningLetter?.effective_start_date) }}
                            </div>
                            <!--end::Value-->
                        </div>
                        <div class="flex-row-fluid">
                            <!--begin::Label-->
                            <label class="form-label fw-bold">Tanggal Akhir Efektif</label>
                            <!--end::Label-->
                            <!--begin::Value-->
                            <div class="text-gray-800 fs-6">
                                @{{ formatDate(warningLetter?.effective_end_date) }}
                            </div>
                            <!--end::Value-->
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Deskripsi</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            @{{ warningLetter?.description || '-' }}
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Pejabat Penandatangan</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            @{{ warningLetter?.signatory_employee?.name || '-' }}
                            <span class="text-muted" v-if="warningLetter?.signatory_employee?.active_career?.job_title">- @{{ warningLetter?.signatory_employee?.active_career?.job_title?.name }}</span>
                            <span class="text-muted" v-if="warningLetter?.signatory_employee?.office?.division?.company">(@{{ warningLetter?.signatory_employee?.office?.division?.company?.name }})</span>
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container" v-if="warningLetter?.attachments && warningLetter?.attachments.length > 0">
                        <!--begin::Label-->
                        <label class="form-label fw-bold">Lampiran</label>
                        <!--end::Label-->
                        <!--begin::Value-->
                        <div class="text-gray-800 fs-6">
                            <div v-for="attachment in warningLetter?.attachments" :key="attachment.id" class="mb-2">
                                <a :href="attachment.file_path" target="_blank" class="d-flex align-items-center text-decoration-none text-gray-800 hover-text-primary p-2 bg-light rounded">
                                    <i class="bi bi-file-earmark fs-3 me-3 text-primary"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">@{{ attachment.original_name }}</div>
                                        <small class="text-muted">@{{ formatFileSize(attachment.file_size) }}</small>
                                    </div>
                                    <i class="bi bi-box-arrow-up-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <!--end::Value-->
                    </div>
                    <!--end::Input group-->
                    <!-- begin::actions -->
                    <div class="d-flex justify-content-end my-10">
                        <a :href="`/warning-letters/${warningLetter?.id}/edit`" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a :href="`/warning-letters/${warningLetter?.id}/print`" target="_blank" class="btn btn-success me-2">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                        <button type="button" class="btn btn-light-danger" @click="openDeleteConfirmation(warningLetter?.id)">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                    <!-- end::actions -->
                </div>
                <!--end::Card header-->
            </div>
        </div>
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
    })
</script>
<script>
    const warningLetter = <?php echo Illuminate\Support\Js::from($warningLetter) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                warningLetter,
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
            formatFileSize(bytes) {
                if (!bytes) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
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
                        toastr.success(message);
                        setTimeout(() => {
                            self.gotoUrl('/warning-letters');
                        }, 500);
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
            gotoUrl(url = null) {
                if (url) {
                    document.location.href = url
                }
            },
        },
    })
</script>
@endsection