@extends('layouts.app')

@section('title', 'Tambah Kantor')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    .dataTables_empty {
        display: none;
    }

    #map {
        width: 100%;
        height: 450px;
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
                <h2>Payroll BCA - Kirim Email</h2>
            </div>
            <div class="card-toolbar">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" width="100">
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label">Pengirim</label>
                <!--end::Label-->
                <!--begin::Input-->

                <div class="input-group mb-3">
                    <input type="text" v-model="model.sender" class="form-control form-control-solid" disabled readonly>
                    <span class="input-group-text" id="basic-addon2"><i class="bi bi-lock"></i></span>
                </div>
                <!--end::Input-->
            </div>
            <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label required">Ke</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="text" v-model="model.to" class="form-control" placeholder="penerima@gmail.com">
                <!--end::Input-->
            </div>
            <div class="mb-10">
                <!--begin::Label-->
                <label class="form-label required">Subjek</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input type="text" v-model="model.subject" class="form-control" placeholder="HONOR MINGGUAN 1217 META">
                <!--end::Input-->
            </div>
            <div class="mb-10 row">
                <div class="col-md-6">
                    <!--begin::Label-->
                    <label class="form-label required">File Transaksi</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="file" accept=".txt" class="form-control" @change="onChangeFile($event, 'transactionFile')">
                    <!--end::Input-->
                </div>
                <div class="col-md-6">
                    <!--begin::Label-->
                    <label class="form-label required">File Checksum</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="file" accept=".txt" class="form-control" @change="onChangeFile($event, 'checksumFile')">
                    <!--end::Input-->
                </div>
            </div>
            <div class="mb-10">
                <label class="form-label">Isi</label>
                <textarea v-model="model.content" cols="30" rows="10" class="form-control"></textarea>
            </div>
            <div class="text-end">
                <!-- <button class="btn btn-primary" style="background-color: royalblue;" @click="sendEmail"><i class="bi bi-send"></i> Kirim</button> -->
                <button type="button" :data-kt-indicator="sendEmailLoading ? 'on' : null" class="btn btn-primary" style="background-color: royalblue;" @click="sendEmail" :disabled="sendEmailLoading">
                    <span class="indicator-label">Kirim</span>
                    <span class="indicator-progress">Mengirim email...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
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
    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    sender: '{{ env("MAIL_FROM_ADDRESS") }}',
                    to: '',
                    subject: '',
                    transactionFile: null,
                    checksumFile: null,
                    content: '',
                },
                sendEmailLoading: false,
            };
        },
        methods: {
            onChangeFile(event, model) {
                const [file] = event.target.files;
                if (file) {
                    this.model[model] = file;
                }
            },
            async sendEmail() {
                try {

                    const self = this;

                    self.sendEmailLoading = true;
                    const data = {
                        to: self.model.to,
                        sender: self.model.sender,
                        subject: self.model.subject,
                        transaction_file: self.model.transactionFile,
                        checksum_file: self.model.checksumFile,
                        content: self.model.content,
                    }

                    console.log(data);

                    const formData = new FormData();
                    for (key in data) {
                        formData.append(key, data[key]);
                    }

                    const response = await axios.post('/payroll-bca-email-log/send-email', formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        // setTimeout(() => {
                        //     document.location.href = '/payroll-bca-email-log/send-email';
                        // }, 500);
                        self.sendEmailLoading = false;
                    }

                } catch (error) {
                    console.log(error)
                    self.sendEmailLoading = false;

                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                }
            }
        }
    })
</script>
@endsection