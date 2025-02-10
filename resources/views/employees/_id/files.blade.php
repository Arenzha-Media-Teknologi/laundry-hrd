@extends('layouts.app')

@section('title', $employee->name . ' - Pinjaman')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <div class="d-flex flex-wrap flex-stack my-5">
        <!--begin::Heading-->
        <h3 class="fw-bolder my-2">Daftar File

        </h3>
        <!--end::Heading-->
        <!--begin::Controls-->
        <div class="d-flex my-2">
            <!--begin::Search-->
            <!-- <div class="d-flex align-items-center position-relative me-4">
                <span class="svg-icon svg-icon-3 position-absolute translate-middle-y top-50 ms-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                    </svg>
                </span>
                <input type="text" id="kt_filter_search" class="form-control form-control-sm form-control-solid bg-body fw-bold fs-7 w-150px ps-11" placeholder="Search">
            </div> -->
            <!--end::Search-->
            <button type="button" class="btn btn-primary btn-sm fw-bolder" data-bs-toggle="modal" data-bs-target="#add_file_modal">
                <span class="svg-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM16 13.5L12.5 13V10C12.5 9.4 12.6 9.5 12 9.5C11.4 9.5 11.5 9.4 11.5 10L11 13L8 13.5C7.4 13.5 7 13.4 7 14C7 14.6 7.4 14.5 8 14.5H11V18C11 18.6 11.4 19 12 19C12.6 19 12.5 18.6 12.5 18V14.5L16 14C16.6 14 17 14.6 17 14C17 13.4 16.6 13.5 16 13.5Z" fill="black" />
                        <rect x="11" y="19" width="10" height="2" rx="1" transform="rotate(-90 11 19)" fill="black" />
                        <rect x="7" y="13" width="10" height="2" rx="1" fill="black" />
                        <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
                    </svg></span>
                Tambah File
            </button>
        </div>
        <!--end::Controls-->
    </div>
    <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
        <?php
        $defaultFiles = [
            [
                'key' => 'identity_image',
                'name' => 'KTP'
            ],
            [
                'key' => 'npwp_image',
                'name' => 'NPWP'
            ],
            [
                'key' => 'bpjs_ketenagakerjaan_image',
                'name' => 'BPJS Ketenagakerjaan'
            ],
            [
                'key' => 'bpjs_mandiri_image',
                'name' => 'BPJS Mandiri'
            ],
        ];

        $issetDefaultFileCount = 0;
        ?>
        @foreach($defaultFiles as $defaultFile)
        <?php $imageUrl = $employee->{$defaultFile['key']}; ?>
        @isset($imageUrl)
        <?php $issetDefaultFileCount += 1; ?>
        <!--begin::Col-->
        <div class="col-md-6 col-lg-4 col-xl-3">
            <!--begin::Card-->
            <div class="card h-100">
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                    <!--begin::Name-->
                    <a href="{{ $imageUrl }}" class="text-gray-800 text-hover-primary d-flex flex-column" data-fancybox="gallery" data-caption="{{ $defaultFile['name'] }} {{ $employee->name }}">
                        <!--begin::Image-->
                        <div class="symbol symbol-100px mb-5">
                            <img src="{{ $imageUrl }}" alt="{{ $defaultFile['name'] }} {{ $employee->name }}" loading="lazy">
                        </div>
                        <!--end::Image-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bolder mb-2">{{ $defaultFile['name'] }}</div>
                        <!--end::Title-->
                    </a>
                    <!--end::Name-->
                    <!--begin::Description-->
                    <!-- <div class="fs-7 fw-bold text-gray-400"></div> -->
                    <!--end::Description-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
        @endisset
        @endforeach

        <?php $issetDriversLicenseImage = 0; ?>
        @foreach($employee->driversLicenses as $index => $driversLicense)
        <?php $imageUrl = $driversLicense->image; ?>
        @isset($imageUrl)
        <?php $issetDriversLicenseImage += 1; ?>
        <!--begin::Col-->
        <div class="col-md-6 col-lg-4 col-xl-3">
            <!--begin::Card-->
            <div class="card h-100">
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                    <!--begin::Name-->
                    <a href="{{ $imageUrl }}" class="text-gray-800 text-hover-primary d-flex flex-column" data-fancybox="gallery" data-caption="SIM {{ strtoupper($driversLicense->type) }} {{ $employee->name }}">
                        <!--begin::Image-->
                        <div class="symbol symbol-100px mb-5">
                            <img src="{{ $imageUrl }}" alt="SIM {{ strtoupper($driversLicense->type) }} {{ $employee->name }}" loading="lazy">
                        </div>
                        <!--end::Image-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bolder mb-2 text-uppercase">SIM {{ $driversLicense->type }}</div>
                        <!--end::Title-->
                    </a>
                    <!--end::Name-->
                    <!--begin::Description-->
                    <!-- <div class="fs-7 fw-bold text-gray-400"></div> -->
                    <!--end::Description-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
        @endisset
        @endforeach

        @if(count($files) == 0 && $issetDefaultFileCount == 0 && $issetDriversLicenseImage == 0)
        <div class="col-md-12 text-center px-5 fs-5 fw-bold">
            <span class="text-muted">Tidak ada file</span>
        </div>
        @endif
        @foreach($files as $file)
        <!--begin::Col-->
        <div class="col-md-6 col-lg-4 col-xl-3">
            <!--begin::Card-->
            <div class="card h-100">
                <!--begin::Card body-->
                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                    <!--begin::Name-->
                    <div class="d-flex justify-content-end align-items-center mb-5">
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2 btn-delete" data-id="{{ $file->id }}">
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
                        <button type="button" class="btn btn-sm btn-icon btn-light-primary ms-2" data-bs-toggle="modal" data-bs-target="#edit_file_modal" @click="onOpenEditModal({{ $file->id }})">
                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                            <span class="svg-icon svg-icon-5 m-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="black" />
                                    <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="black" />
                                    <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </button>
                    </div>
                    <?php
                    $icon = asset('assets/media/svg/files/blank-image.svg');
                    $contentType = $file?->content_type;
                    $fileUrl = $file?->url;
                    // $icon = '';
                    // if ($file->content_type !== null) {
                    //     $explodedContentType = explode('/', $file->content_type);
                    //     if (isset($explodedContentType[0])) {
                    //         $fileType = $explodedContentType[0];
                    //     }
                    // }

                    $imageMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    $pdfMimeTypes = ['application/pdf'];
                    $excelMimeTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

                    if (in_array($contentType, $imageMimeTypes)) {
                        $icon = $file->url;
                    } else if (in_array($contentType, $pdfMimeTypes)) {
                        $icon = asset('assets/media/svg/files/pdf.svg');
                    } else if (in_array($contentType, $excelMimeTypes)) {
                        $icon = asset('assets/media/svg/files/doc.svg');
                    }

                    ?>
                    <a href="{{ $file->url }}" <?= in_array($contentType, $imageMimeTypes) ? '' : 'target="_blank"' ?> class="text-gray-800 text-hover-primary d-flex flex-column" <?= in_array($contentType, $imageMimeTypes) ? 'data-fancybox="gallery" data-caption="' . $file->name . '"' : '' ?>>
                        <!--begin::Image-->
                        <div class="symbol symbol-100px mb-5">

                            <img src="{{ $icon }}" alt="{{ $file->name }}" loading="lazy">
                        </div>
                        <!--end::Image-->
                        <!--begin::Title-->
                        <div class="fs-5 fw-bolder mb-2">{{ $file->name }}</div>
                        <!--end::Title-->
                    </a>
                    <!--end::Name-->
                    <!--begin::Description-->
                    <div class="fs-7 fw-bold text-gray-400">Diunggah pada {{ $file->created_at }}</div>
                    <!--end::Description-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
        @endforeach
    </div>
    <div class="modal fade" tabindex="-1" id="add_file_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah File</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <form autocomplete="off">
                        <div class="mb-10">
                            <label for="file_name" class="required form-label">Nama</label>
                            <input type="text" v-model="model.name" id="file_name" class="form-control form-control-solid" placeholder="Masukkan nama file" />
                        </div>
                        <div class="mb-10">
                            <label for="file_attachment" class="required form-label">File (Max. 5 MB)</label>
                            <input type="file" id="file_attachment" class="form-control" @change="onChangeInputFile($event)" />
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <!--begin::Button-->
                    <button type="submit" id="kt_ecommerce_add_category_submit" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" :disabled="loading" @click="onSubmit">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="edit_file_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah File</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <form autocomplete="off">
                        <div class="mb-10">
                            <label for="file_name" class="required form-label">Nama</label>
                            <input type="text" v-model="editModel.name" id="file_name" class="form-control form-control-solid" placeholder="Masukkan nama file" />
                        </div>
                        <div class="mb-10">
                            <label for="file_name" class="required form-label">File Lama</label>
                            <div>
                                <a :href="editModel.oldFile" target="_blank">@{{ editModel.oldFile }}</a>
                            </div>
                        </div>
                        <div class="mb-10">
                            <label for="file_attachment" class="required form-label">File Baru (Max. 5 MB)</label>
                            <input type="file" id="file_attachment" class="form-control" @change="onChangeEditInputFile($event)" />
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <!--begin::Button-->
                    <button type="submit" id="kt_ecommerce_add_category_submit" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" :disabled="loading" @click="onUpdate">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> -->
<!-- <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script> -->
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
@endsection
@section('pagescript')
<script>
    moment.locale('id');
    const closeModal = (selector) => {
        const element = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(element);
        modal.hide();
    }

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

        function sendDeleteRequest(id) {
            const self = this;
            return axios.delete('/employee-files/' + id)
                .then(function(response) {
                    let message = response?.data?.message;
                    if (!message) {
                        message = 'Data berhasil disimpan'
                    }
                    toastr.success(message);
                    document.location.reload();
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
        };


        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const id = $(this).attr('data-id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Komponen gaji untuk pinjaman akan ikut terhapus",
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
                    return sendDeleteRequest(id);
                },
                allowOutsideClick: () => !Swal.isLoading(),
                backdrop: true,
            })
        })
    })
</script>
<script>
    Vue.prototype.moment = moment

    const employee = <?php echo Illuminate\Support\Js::from($employee) ?>;
    const files = <?php echo Illuminate\Support\Js::from($files) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employee,
                files,
                model: {
                    name: '',
                    file: null,
                },
                editModel: {
                    id: null,
                    name: '',
                    oldFile: null,
                    file: null,
                },
                loading: false,
            }
        },
        methods: {
            onChangeInputFile(e) {
                console.log(e.target.files[0]);
                if (e.target.files && e.target.files.length > 0) {
                    this.model.file = e.target.files[0];
                } else {
                    if (this.model.file) {
                        this.model.file = null;
                    }
                }
            },
            onChangeEditInputFile(e) {
                console.log(e.target.files[0]);
                if (e.target.files && e.target.files.length > 0) {
                    this.editModel.file = e.target.files[0];
                } else {
                    if (this.editModel.file) {
                        this.editModel.file = null;
                    }
                }
            },
            async onSubmit() {
                let self = this;
                try {
                    const {
                        name,
                        file,
                    } = self.model;

                    const requestBody = {
                        name,
                        file,
                        employee_id: self.employee.id,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    self.loading = true;

                    const response = await axios.post('/employee-files', formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        // closeModal('#add_file_modal');
                        document.location.reload();
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.loading = false;
                }
            },
            async onUpdate() {
                let self = this;
                try {
                    const {
                        id,
                        name,
                        file,
                    } = self.editModel;

                    const requestBody = {
                        name,
                        file,
                        employee_id: self.employee.id,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    self.loading = true;

                    const response = await axios.post('/employee-files/' + id, formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        // closeModal('#add_file_modal');
                        document.location.reload();
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.loading = false;
                }
            },
            onOpenEditModal(id) {
                const self = this;
                if (id) {
                    const [file] = self.files.filter(file => file.id == id);
                    if (file) {
                        self.editModel.id = file.id;
                        self.editModel.name = file.name;
                        self.editModel.oldFile = file.url;
                    }
                }
            }
        }
    })
</script>
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    const bgColor = {
        primary: KTUtil.getCssVariableValue('--bs-primary'),
        success: KTUtil.getCssVariableValue('--bs-success'),
        danger: KTUtil.getCssVariableValue('--bs-danger'),
        info: KTUtil.getCssVariableValue('--bs-info'),
        warning: KTUtil.getCssVariableValue('--bs-warning'),
        light: KTUtil.getCssVariableValue('--bs-dark'),
    }
</script>
@endsection