@extends('layouts.app')

@section('title', 'Edit Pembukaan Outlet')

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
                <h2>Edit Pembukaan Outlet</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="my-10">Informasi Pembukaan Outlet</h2>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Outlet</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.officeId" class="form-select mb-2">
                            <option value="">Pilih Outlet</option>
                            <option v-for="office in offices" :key="office.id" :value="office.id">@{{ office.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Tanggal</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="date" v-model="model.date" class="form-control mb-2" :max="todayDate">
                        <!--end::Input-->
                    </div>
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Waktu</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="time" v-model="model.time" class="form-control mb-2">
                        <!--end::Input-->
                    </div>
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Jam Input Data</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="datetime-local" v-model="model.createTime" class="form-control mb-2">
                        <!--end::Input-->
                        <div>
                            <small class="text-muted">Jika dikosongkan maka akan menggunakan waktu saat ini</small>
                        </div>
                    </div>
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Lampiran</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="file" ref="fileInput" @change="handleFileChange" class="form-control mb-2" accept="image/*">
                        <!--end::Input-->
                        <div>
                            <small>Dapat melampirkan foto saat pembukaan outlet</small>
                        </div>
                        <!--begin::Current Image-->
                        <div v-if="currentImage && !imagePreview" class="mt-3">
                            <p class="text-muted mb-2">Gambar saat ini:</p>
                            <img :src="currentImage" alt="Current Image" class="img-fluid rounded" style="max-width: 300px; max-height: 200px;">
                            <div class="mt-2">
                                <button type="button" @click="removeCurrentImage" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus Gambar Saat Ini
                                </button>
                            </div>
                        </div>
                        <!--end::Current Image-->
                        <!--begin::Image Preview-->
                        <div v-if="imagePreview" class="mt-3">
                            <p class="text-muted mb-2">Preview gambar baru:</p>
                            <img :src="imagePreview" alt="Preview" class="img-fluid rounded" style="max-width: 300px; max-height: 200px;">
                            <div class="mt-2">
                                <button type="button" @click="removeImage" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus Gambar Baru
                                </button>
                            </div>
                        </div>
                        <!--end::Image Preview-->
                    </div>
                    <div class="separator"></div>
                    <!--end::Input group-->
                    <h2 class="my-10">Informasi Penanggung Jawab</h2>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Penanggung Jawab</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select class="form-select mb-2" id="select-employee">
                            <option value="">Pilih Penanggung Jawab</option>
                            @foreach($employees as $employee)
                            <option value="{{$employee->id}}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <!--end::Input-->
                    </div>
                    <!-- begin::submit -->
                    <div class="d-flex justify-content-end my-10">
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
                            <span class="indicator-label">Perbarui</span>
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
    // var offices = @json('$offices');
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const outletOpening = <?php echo Illuminate\Support\Js::from($outletOpening) ?>;

    // Helper function to format date to datetime-local
    function formatDateTimeLocal(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                offices,
                employees,
                outletOpening,
                model: {
                    officeId: outletOpening.office_id,
                    date: outletOpening.date,
                    time: outletOpening.time,
                    createTime: outletOpening.created_at ? formatDateTimeLocal(outletOpening.created_at) : '',
                    createdBy: outletOpening.created_by,
                },
                file: null,
                imagePreview: null,
                currentImage: outletOpening.image,
                submitLoading: false,
            }
        },
        computed: {
            todayDate() {
                return new Date().toISOString().split('T')[0];
            },
        },
        mounted() {
            // Set select2 value for employee
            this.$nextTick(() => {
                $('#select-employee').val(this.model.createdBy).trigger('change');
            });
        },
        methods: {
            // OUTLET OPENING METHODS
            handleFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.file = file;

                    // Create image preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            removeImage() {
                this.file = null;
                this.imagePreview = null;
                this.$refs.fileInput.value = '';
            },
            removeCurrentImage() {
                this.currentImage = null;
            },
            async onSubmit() {
                let self = this;
                try {
                    const {
                        officeId,
                        date,
                        time,
                        createTime,
                        createdBy
                    } = self.model;

                    self.submitLoading = true;

                    // Jika jam input data dikosongkan, gunakan waktu saat ini
                    let createTimeValue = createTime;
                    if (!createTimeValue || createTimeValue === '') {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        createTimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;
                    }

                    const formData = new FormData();
                    formData.append('office_id', officeId);
                    formData.append('date', date);
                    formData.append('time', time);
                    formData.append('create_time', createTimeValue);
                    formData.append('created_by', createdBy);
                    formData.append('_method', 'PUT');

                    if (self.file) {
                        formData.append('outlet_opening_attachment', self.file);
                    }

                    const response = await axios.post('/outlet-openings/' + this.outletOpening.id, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil diperbarui'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        setTimeout(() => {
                            document.location.href = '/outlet-openings';
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
        },
    })

    $(function() {
        $('#select-employee').select2();
        $('#select-employee').on('change', function(e) {
            const value = $(this).val();
            app.$data.model.createdBy = value;
        });
    })
</script>
@endsection