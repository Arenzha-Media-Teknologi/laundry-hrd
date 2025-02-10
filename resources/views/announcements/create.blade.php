@extends('layouts.app')

@section('title', 'Tambah Pengumuman')

@section('head')

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
                <h2>Tambah Pengumuman</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <h2 class="my-10">General</h2>
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="required form-label">Perusahaan</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                            <input class="form-check-input" type="checkbox" id="flexSwitchDefault" v-model="model.isAllCompanies" />
                            <label class="form-check-label" for="flexSwitchDefault">
                                Semua Perusahaan
                            </label>
                        </div>
                        <div :class="model.isAllCompanies ? 'd-none': 'd-show'">
                            <select class="form-select mb-2" multiple id="select-company">
                                <option value="">Pilih Perusahaan</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input-->
                    </div>
                    <!-- <div class="mb-10 fv-row fv-plugins-icon-container">
                        <label class="required form-label">Divisi</label>
                        <select v-model="model.divisionId" class="form-select mb-2">
                            <option value="">Pilih Divisi</option>
                            <option v-for="division in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                        </select>
                    </div> -->
                    <div class="mb-10">
                        <label class="required form-label">Judul</label>
                        <input type="text" v-model="model.title" class="form-control mb-2" value="">
                    </div>
                    <div class="row mb-10">
                        <div class="col-lg-6">
                            <label class="required form-label">Tanggal Awal Tampil</label>
                            <input type="text" class="form-control mb-2" id="input-date-first-date">
                            <small class="text-muted">Kapan pengumuman akan ditampilkan kepada pegawai</small>
                        </div>
                        <div class="col-lg-6">
                            <label class="required form-label">Tanggal Akhir Tampil</label>
                            <input type="text" class="form-control mb-2" id="input-date-end-date">
                            <small class="text-muted">Kapan pengumuman akan <strong>berhenti</strong> ditampilkan kepada pegawai</small>
                        </div>
                    </div>
                    <div class="mb-10 fv-row">
                        <label class="required form-label">Isi</label>
                        <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target">
                        </textarea>
                    </div>
                    <div class="mb-10">
                        <label class="form-label">Lampiran</label>
                        <input type="file" @change="onChangeAttachment($event)" class="form-control mb-2">
                    </div>
                </div>
                <!--end::Card header-->
            </div>
            <div class="d-flex justify-content-end my-10">
                <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit" :disabled="submitLoading">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Mengirim data...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
    <!--end::Card-->
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
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
                    isAllCompanies: true,
                    companyIds: [],
                    title: '',
                    startDate: '{{ date("Y-m-d 00:00") }}',
                    endDate: '{{ \Carbon\Carbon::parse(date("Y-m-d 00:00"))->addWeek() }}',
                    content: '',
                    attachment: null,
                },
                submitLoading: false,
            }
        },
        mounted() {},
        methods: {
            onChangeAttachment(event) {
                const [file] = event.target.files;
                if (file) {
                    this.model.attachment = file;
                }
            },
            async onSubmit() {
                let self = this;
                try {
                    self.submitLoading = true;
                    const content = tinymce.get("kt_docs_tinymce_hidden").getContent();

                    // return console.log(content);

                    const data = {
                        is_all_companies: self.model.isAllCompanies ? 1 : 0,
                        company_ids: self.model.companyIds,
                        title: self.model.title,
                        start_date: self.model.startDate,
                        end_date: self.model.endDate,
                        content: content,
                        attachment: self.model.attachment,
                    }

                    const formData = new FormData();

                    for (key in data) {
                        if (key == 'company_ids') {
                            formData.append(key, JSON.stringify(data[key]));
                        } else {
                            formData.append(key, data[key]);
                        }
                    }

                    const response = await axios.post('/announcements', formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        setTimeout(() => {
                            document.location.href = "/announcements";
                            // self.gotoUrl('/offices');
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
        watch: {
            'model.isAllCompanies': function(newValue) {
                if (newValue) {
                    this.model.companyIds = [];
                    $('#select-company').val([]).trigger('change');
                }
            },
        }
    })

    $('#select-company').select2();
    $('#select-company').on('change', function(e) {
        const values = $(this).val();
        app.$data.model.companyIds = values;
    });

    tinymce.init({
        selector: "#kt_docs_tinymce_hidden",
        height: "480",
        menubar: false,
        toolbar: ["styleselect fontselect fontsizeselect",
            "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
            "bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
        ],
        plugins: "advlist autolink link image lists charmap print preview code"
    });

    $(function() {
        $('#input-date-first-date').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment('{{ date("Y-m-d 00:00") }}'),
            locale: {
                format: 'DD/MM/YYYY HH:mm'
            }
            // minYear: 1901,
            // maxYear: parseInt(moment().format('YYYY'), 10)
        }, function(start, end, label) {
            console.log(start.format('YYYY-MM-DD HH:mm'));
            // var years = moment().diff(start, 'years');
            // alert("You are " + years + " years old!");
        });
    });

    $(function() {
        $('#input-date-end-date').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD/MM/YYYY HH:mm'
            },
            startDate: moment('{{ \Carbon\Carbon::parse(date("Y-m-d 00:00"))->addWeek() }}'),
            // minYear: 1901,
            // maxYear: parseInt(moment().format('YYYY'), 10)
        }, function(start, end, label) {
            console.log(start.format('YYYY-MM-DD 00:00'));
            // var years = moment().diff(start, 'years');
            // alert("You are " + years + " years old!");
        });
    });
</script>
@endsection