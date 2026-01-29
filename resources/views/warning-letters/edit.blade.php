@extends('layouts.app')

@section('title', 'Ubah Surat Peringatan')

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
                <h2>Ubah Surat Peringatan</h2>
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
                        <label class="required form-label">Pegawai</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select class="form-select mb-2" id="select-employee">
                            <option value="">Pilih Pegawai</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }} - {{ $employee->activeCareer?->jobTitle?->name ?? '-' }} ({{ $employee->office?->division?->company?->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Jenis SP</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.type" class="form-select mb-2">
                            <option value="">Pilih Jenis SP</option>
                            <option value="sp1">SP1</option>
                            <option value="sp2">SP2</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column flex-md-row gap-5 mb-10">
                        <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="form-label">Tanggal Mulai Efektif</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" v-model="model.effectiveStartDate" class="form-control" placeholder="Masukkan tanggal mulai efektif" value="">
                            <!--end::Input-->
                        </div>
                        <div class="flex-row-fluid">
                            <!--begin::Label-->
                            <label class="form-label">Tanggal Akhir Efektif</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" v-model="model.effectiveEndDate" class="form-control" placeholder="Masukkan tanggal akhir efektif">
                            <!--end::Input-->
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Deskripsi</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <textarea v-model="model.description" class="form-control mb-2" rows="5" placeholder="Masukkan deskripsi surat peringatan"></textarea>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Pejabat Penandatangan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select class="form-select mb-2" id="select-signatory">
                            <option value="">Pilih Pejabat Penandatangan</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }} - {{ $employee->activeCareer?->jobTitle?->name ?? '-' }} ({{ $employee->office?->division?->company?->name ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Lampiran</label>
                        <!--end::Label-->
                        <!--begin::Existing Attachments-->
                        <div v-if="existingAttachments && existingAttachments.length > 0" class="mb-3">
                            <div v-for="(attachment, index) in existingAttachments" :key="attachment.id" class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-bold">@{{ attachment.original_name }}</div>
                                        <small class="text-muted">@{{ formatFileSize(attachment.file_size) }}</small>
                                    </div>
                                </div>
                                <div>
                                    <a :href="`/warning-letters/attachments/${attachment.id}/download`" class="btn btn-sm btn-icon btn-light-primary me-2" target="_blank">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" @click="deleteAttachment(attachment.id, index)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!--end::Existing Attachments-->
                        <!--begin::Input-->
                        <input type="file" id="attachments" name="attachments[]" class="form-control mb-2" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Maksimal 10MB per file. Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG</small>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!-- begin::submit -->
                    <div class="d-flex justify-content-end my-10">
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!-- end::submit -->
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

    // Format date for input
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    id: Number(warningLetter?.id),
                    employeeId: Number(warningLetter?.employee_id),
                    type: warningLetter?.type || '',
                    effectiveStartDate: formatDateForInput(warningLetter?.effective_start_date),
                    effectiveEndDate: formatDateForInput(warningLetter?.effective_end_date),
                    description: warningLetter?.description || '',
                    signatory: warningLetter?.signatory ? Number(warningLetter?.signatory) : null,
                },
                existingAttachments: warningLetter?.attachments || [],
                submitLoading: false,
            }
        },
        methods: {
            formatFileSize(bytes) {
                if (!bytes) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },
            async deleteAttachment(attachmentId, index) {
                const self = this;
                try {
                    const response = await axios.delete(`/warning-letters/attachments/${attachmentId}`);
                    if (response) {
                        self.existingAttachments.splice(index, 1);
                        toastr.success('Lampiran berhasil dihapus');
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Gagal menghapus lampiran'
                    }
                    toastr.error(message);
                }
            },
            async onSubmit() {
                let self = this;
                
                // Validasi form
                if (!self.model.employeeId) {
                    toastr.error('Pilih pegawai terlebih dahulu');
                    return;
                }
                if (!self.model.type) {
                    toastr.error('Pilih jenis SP terlebih dahulu');
                    return;
                }

                // Konfirmasi dengan SweetAlert2
                const result = await Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah surat peringatan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    reverseButtons: true
                });

                if (!result.isConfirmed) {
                    return;
                }

                try {
                    const {
                        employeeId,
                        type,
                        effectiveStartDate,
                        effectiveEndDate,
                        description,
                        signatory,
                    } = self.model;

                    self.submitLoading = true;

                    // Create FormData for file upload
                    const formData = new FormData();
                    formData.append('employee_id', employeeId);
                    formData.append('type', type);
                    formData.append('effective_start_date', effectiveStartDate);
                    formData.append('effective_end_date', effectiveEndDate);
                    formData.append('description', description);
                    if (signatory) {
                        formData.append('signatory', signatory);
                    }

                    // Append attachments
                    const attachmentsInput = document.getElementById('attachments');
                    if (attachmentsInput && attachmentsInput.files.length > 0) {
                        for (let i = 0; i < attachmentsInput.files.length; i++) {
                            formData.append('attachments[]', attachmentsInput.files[i]);
                        }
                    }

                    const response = await axios.post(`/warning-letters/${self.model.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        setTimeout(() => {
                            self.gotoUrl('/warning-letters');
                        }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitLoading = false;
                }
            },
            gotoUrl(url = null) {
                if (url) {
                    document.location.href = url
                }
            },
        },
    })

    $(function() {
        $('#select-employee').select2();
        $('#select-signatory').select2();

        $('#select-employee').on('change', function() {
            // console.log('changed');
            const value = $(this).val();
            app.$data.model.employeeId = value;
        });

        $('#select-signatory').on('change', function() {
            const value = $(this).val();
            app.$data.model.signatory = value ? Number(value) : null;
        });

        const employeeId = app.$data.model.employeeId;
        if (employeeId) {
            $('#select-employee').val(employeeId).trigger('change');
        }

        const signatoryId = app.$data.model.signatory;
        if (signatoryId) {
            $('#select-signatory').val(signatoryId).trigger('change');
        }
    })
</script>
@endsection