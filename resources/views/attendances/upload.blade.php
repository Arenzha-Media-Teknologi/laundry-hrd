@extends('layouts.app')

@section('title', 'Kehadiran')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Upload Absensi</h3>
            </div>
            <!--end::Card title-->
            <div class="card-toolbar">
            </div>
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!-- <input type="file" class="form-control" @change="onChangeFile($event)">
            <button class="btn btn-primary" @click="upload">Upload</button> -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h1 class="text-center"><i class="fas fa-upload fa-2x"></i></h1>
                    <h3 class="card-title text-center">Pilih &amp; Upload File (.csv)</h3>
                    <p class="text-muted text-center">Format file yang diupload harus sesuai dengan template</p>
                    <div class="my-5 text-center">
                        <input type="file" class="form-control" @change="onChangeFile($event)" accept=".csv">
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-primary" :data-kt-indicator="loading ? 'on' : 'off'" @click="upload" :disabled="disabled">
                            <span class=" indicator-label">
                                Upload
                            </span>
                            <span class="indicator-progress">
                                Mengunggah... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <!-- <button type="submit" disabled="disabled" class="btn btn-primary px-5">Upload</button> -->
                    </div>
                </div>
                <!-- <div class="progress mt-5" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">Mengunggah Data ... (25%)</div>
                </div> -->
            </div>
        </div>
        <!--end::Card body-->
    </div>
</div>
@endsection

@section('script')

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
    const app = new Vue({
        el: '#kt_content_container',
        data: {
            message: 'OK',
            file: null,
            loading: false,
            disabled: false,
        },
        methods: {
            onChangeFile(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    const file = files[0];
                    this.file = file;
                }
            },
            async upload() {
                try {
                    this.loading = true;
                    this.disabled = true;
                    const formData = new FormData();
                    formData.append('file', this.file);

                    const response = await axios.post('/attendances/action/do-upload', formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        toastr.success(message);

                        this.disabled = false;
                        // setTimeout(() => {
                        //     window.document.location.href = '/attendances';
                        // }, 500);
                    }
                } catch (error) {
                    this.disabled = false;
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    this.loading = false;
                }

            }
        }
    });
</script>
@endsection