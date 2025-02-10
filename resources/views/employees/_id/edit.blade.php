@extends('layouts.app')

@section('title', $employee->name . ' - Ubah')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <form class="form d-flex flex-column flex-lg-row" enctype="multipart/form-data" @submit.prevent="onSubmit">
        <!--begin::Aside column-->
        <div class=" d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
            <!--begin::Thumbnail settings-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Foto</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body text-center pt-0">
                    <!--begin::Image input-->
                    <div class="image-input <?= $employee->photo == null ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/avatars/blank.svg') ?>)" id="employee_photo">
                        <!--begin::Preview existing avatar-->
                        <div class="image-input-wrapper w-150px h-150px" style="background-image: url(<?= $employee->photo ?>)"></div>
                        <!--end::Preview existing avatar-->
                        <!--begin::Label-->
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                            <!--begin::Icon-->
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <!--end::Icon-->
                            <!--begin::Inputs-->
                            <input type="file" ref="image" v-on:change="handleFileUpload" name="avatar" accept=".png, .jpg, .jpeg" />
                            <input type="hidden" name="avatar_remove" />
                            <!--end::Inputs-->
                        </label>
                        <!--end::Label-->
                        <!--begin::Cancel-->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Hapus">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <!--end::Cancel-->
                        <!--begin::Remove-->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        <!--end::Remove-->
                    </div>
                    <!--end::Image input-->
                    <!--begin::Description-->
                    <div class="text-muted fs-7">Foto hanya boleh file dengan format .jpg, .jpeg, .png</div>
                    <!--end::Description-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Thumbnail settings-->
        </div>
        <!--end::Aside column-->
        <!--begin::Main column-->
        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <!--begin::General options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>General</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Input group-->
                    <div class="d-flex flex-wrap gap-5 mb-6">
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Nama</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.name" class="form-control form-control-sm mb-2" placeholder="Masukkan nama pegawai" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Nama yang dimasukkan adalah nama lengkap sesuai KTP</div>
                            <!--end::Description-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Jenis Kelamin</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <!-- <input type="text" class="form-control form-control-sm mb-2" placeholder="Masukkan nama pegawai" /> -->
                            <select v-model="model.gender" class="form-select form-select-sm mb-2">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-wrap gap-5 mb-6">
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Tempat Lahir</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.placeOfBirth" class="form-control form-control-sm mb-2" placeholder="Masukkan tempat lahir" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Tanggal Lahir</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" v-model="model.dateOfBirth" class="form-control form-control-sm mb-2" placeholder="Masukkan tempat lahir" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-wrap gap-5 mb-6">
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Identitas Diri</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select v-model="model.identityType" class="form-select form-select-sm mb-2">
                                <option value="">Pilih Jenis Identitas</option>
                                <option value="ktp">KTP</option>
                                <!-- <option value="passport">Passport</option>
                                <option value="kitas">KITAS</option>
                                <option value="kitap">KITAP</option> -->
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Nomor Identitas</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.identityNumber" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor identitas" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <div class="d-flex justify-content-end">
                        <div class="text-end">
                            <div class="d-flex align-items-center text-end justify-content-end">
                                <a data-bs-toggle="collapse" href="#collapseAddonForm" role="button" aria-expanded="false" aria-controls="collapseAddonForm"><strong>Informasi Tambahan&nbsp;</a></strong>
                                <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr072.svg-->
                                <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                    </svg></span>
                                <!--end::Svg Icon-->
                                </a>
                            </div>
                            <span class="text-muted">(SIM dan lain-lain)</span>
                        </div>
                    </div>
                    <!-- begin::collapse -->
                    <div class="collapse" id="collapseAddonForm">
                        <div class="mb-3">
                            <strong class="fs-4">Informasi Tambahan</strong>
                        </div>
                        <!--begin::Input group-->
                        <!-- <div class="d-flex flex-wrap gap-5 mb-6">
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <label class="form-label">Status Perkawinan</label>
                                <select v-model="model.maritalStatus" class="form-select form-select-sm mb-2">
                                    <option value="">Pilih Status Perkawinan</option>
                                    <option value="lajang">Lajang</option>
                                    <option value="menikah">Menikah</option>
                                    <option value="duda">Duda</option>
                                    <option value="janda">Janda</option>
                                </select>
                            </div>
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <label class="form-label">Agama</label>
                                <select v-model="model.religion" class="form-select form-select-sm mb-2">
                                    <option value="">Pilih Agama</option>
                                    <option value="islam">Islam</option>
                                    <option value="kristen">Kristen</option>
                                    <option value="katolik">Katolik</option>
                                    <option value="hindu">Hindu</option>
                                    <option value="buddha">Buddha</option>
                                    <option value="khinghucu">Khonghucu</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                        </div> -->
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-wrap gap-5 mb-6">
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Jenis SIM</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select v-model="model.driverLicenseType" class="form-select form-select-sm mb-2">
                                    <option value="">Pilih Jenis SIM</option>
                                    <option value="a">A</option>
                                    <option value="b1">B1</option>
                                    <option value="b2">B2</option>
                                    <option value="c2">C</option>
                                    <option value="d">D</option>
                                    <option value="a umum">A Umum</option>
                                    <option value="b1 umum">B1 Umum</option>
                                    <option value="b2 umum">B2 Umum</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Nomor SIM</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.driverLicenseNumber" class="form-control form-control-sm mb-2" placeholder="Masukkan program studi" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-wrap gap-5 mb-6">
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Golongan Darah</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <!-- <input type="text" class="form-control form-control-sm mb-2" placeholder="Masukkan nama pegawai" /> -->
                                <select v-model="model.bloodGroup" class="form-select form-select-sm mb-2">
                                    <option value="">Pilih Golongan Darah</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="ab">AB</option>
                                    <option value="o">O</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Pendidikan Terakhir</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <!-- <input type="text" class="form-control form-control-sm mb-2" placeholder="Masukkan nama pegawai" /> -->
                                <select v-model="model.recentEducation" class="form-select form-select-sm mb-2">
                                    <option value="">Pilih Pendidikan Terakhir</option>
                                    <option value="sd">SD</option>
                                    <option value="smp">SMP</option>
                                    <option value="sma">SMA</option>
                                    <option value="smea">SMEA</option>
                                    <option value="smk">SMK</option>
                                    <option value="stm">STM</option>
                                    <option value="d1">D1</option>
                                    <option value="d2">D2</option>
                                    <option value="d3">D3</option>
                                    <option value="d4">D4</option>
                                    <option value="s1">S1</option>
                                    <option value="s2">S2</option>
                                    <option value="s3">S3</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-wrap gap-5 mb-6">
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Nama Institusi Pendidikan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.educationInstitutionName" class="form-control form-control-sm mb-2" placeholder="Masukkan nama institusi pendidikan" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Jurusan / Program Studi</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.studyProgram" class="form-control form-control-sm mb-2" placeholder="Masukkan program studi" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!-- end::collapse -->
                </div>
                <!--end::Card header-->
            </div>
            <!--end::General options-->
            <!--begin::Contact options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Kontak</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Input group-->
                    <div class="d-flex flex-wrap gap-5 mb-6">
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <label class="form-label">Email</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="email" v-model="model.email" class="form-control form-control-sm mb-2" placeholder="Masukkan email pegawai" />
                            <!--end::Input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Email yang dimasukkan adalah email valid</div>
                            <!--end::Description-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="form-label">Telepon</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.phone" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor telepon / handphone" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="form-label">Alamat</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <!-- <input type="email" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor telepon / handphone" /> -->
                        <textarea v-model="model.address" rows="3" class="form-control form-control-sm mb-2" placeholder="Masukkan alamat"></textarea>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <div class="d-flex justify-content-end">
                        <div class="text-end">
                            <div class="d-flex align-items-center text-end justify-content-end">
                                <a data-bs-toggle="collapse" href="#collapseEmergencyContact" role="button" aria-expanded="false" aria-controls="collapseEmergencyContact"><strong>Kontak Darurat&nbsp;</a></strong>
                                <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr072.svg-->
                                <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                    </svg></span>
                                <!--end::Svg Icon-->
                                </a>
                            </div>
                            <span class="text-muted">(Informasi kontak darurat)</span>
                        </div>
                    </div>
                    <!-- begin::collapse -->
                    <div class="collapse" id="collapseEmergencyContact">
                        <div class="mb-3">
                            <strong class="fs-4">Kontak Darurat</strong>
                        </div>
                        <!--begin::Input group-->
                        <div class="d-flex flex-wrap gap-5 mb-6">
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Nama</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.emergencyContactName" class="form-control form-control-sm mb-2" placeholder="Masukkan nama kontak darurat" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row w-100 flex-md-root fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="form-label">Hubungan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="model.emergencyContactRelation" class="form-control form-control-sm mb-2" placeholder="Masukkan hubungan kontak darurat" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="mb-6 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Telepon</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.emergencyContactPhone" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor telepon / handphone darurat" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!-- end::collapse -->
                </div>
                <!--end::Card header-->
            </div>
            <!--end::Contact options-->
            <!--begin::Employment options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Pekerjaan</h2>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" :data-kt-indicator="getResourcesLoading ? 'on' : null" class="btn btn-sm btn-light" :disabled="getResourcesLoading" @click="getResources">
                            <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr029.svg-->
                            <span class="svg-icon svg-icon-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.5 20.7259C14.6 21.2259 14.2 21.826 13.7 21.926C13.2 22.026 12.6 22.0259 12.1 22.0259C9.5 22.0259 6.9 21.0259 5 19.1259C1.4 15.5259 1.09998 9.72592 4.29998 5.82592L5.70001 7.22595C3.30001 10.3259 3.59999 14.8259 6.39999 17.7259C8.19999 19.5259 10.8 20.426 13.4 19.926C13.9 19.826 14.4 20.2259 14.5 20.7259ZM18.4 16.8259L19.8 18.2259C22.9 14.3259 22.7 8.52593 19 4.92593C16.7 2.62593 13.5 1.62594 10.3 2.12594C9.79998 2.22594 9.4 2.72595 9.5 3.22595C9.6 3.72595 10.1 4.12594 10.6 4.02594C13.1 3.62594 15.7 4.42595 17.6 6.22595C20.5 9.22595 20.7 13.7259 18.4 16.8259Z" fill="black" />
                                    <path opacity="0.3" d="M2 3.62592H7C7.6 3.62592 8 4.02592 8 4.62592V9.62589L2 3.62592ZM16 14.4259V19.4259C16 20.0259 16.4 20.4259 17 20.4259H22L16 14.4259Z" fill="black" />
                                </svg></span>
                            <!--end::Svg Icon-->
                            <span class="indicator-label">Refresh</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Alert-->
                    <div class="alert bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                        <!--begin::Icon-->
                        <span class="svg-icon svg-icon-2hx svg-icon-primary me-4 mb-5 mb-sm-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                                <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="black" />
                            </svg>
                        </span>
                        <!--end::Icon-->

                        <!--begin::Wrapper-->
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <!--begin::Title-->
                            <h4 class="fw-bold">Info</h4>
                            <!--end::Title-->
                            <!--begin::Content-->
                            <span class="text-gray-700 fs-6">Jika tidak menemukan data yang dicari, Klik "Tambah Perusahaan/Divisi/..." > Simpan data baru > Klik tombol "Refresh"</span>
                            <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Alert-->
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="form-label">Nomor Pegawai Saat Ini</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" :value="model.oldEmployeeNumber" class="form-control form-control-solid form-control-sm mb-2" placeholder="Nomor pegawai" disabled />
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row flex-wrap gap-5 mb-6">
                        <!--begin::Input group-->
                        <div class="col-md-6">
                            <!--begin::Label-->
                            <label class="form-label">Tanggal Mulai Bekerja</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" v-model="model.startWorkDate" class="form-control form-control-sm mb-2" placeholder="Masukkan tanggal mulai bekerja" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Input group-->
                    <div class="separator my-10"></div>
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="form-label">Nomor Pegawai Baru</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" :value="employeeNumberWithCounter" class="form-control form-control-solid form-control-sm mb-2" placeholder="Nomor pegawai" disabled />
                        <!--end::Input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Nomor pegawai akan di buat secara otomatis dengan format: <strong>[INISIAL PERUSAHAAN]-[INSIAL DIVISI]-[TAHUN INPUT PEGAWAI][COUNTER/URUTAN]</strong></div>
                        <!--end::Description-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="required form-label">Perusahaan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.companyId" class="form-select form-select-sm mb-2">
                            <option value="">Pilih Perusahaan</option>
                            <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                        </select>
                        <!--end::Input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 d-flex justify-content-end">
                            <a href="/companies" target="_blank">Tambah Perusahaan</a>
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="required form-label">Divisi</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.divisionId" class="form-select form-select-sm mb-2">
                            <option value="">Pilih Divisi</option>
                            <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                        </select>
                        <!--end::Input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 d-flex justify-content-between align-items-center">
                            <span><em v-cloak v-if="!model.companyId">Pilih perusahaan</em></span>
                            <a href="/divisions" target="_blank">Tambah Divisi</a>
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-6 fv-row">
                        <!--begin::Label-->
                        <label class="required form-label">Kantor</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.officeId" class="form-select form-select-sm mb-2">
                            <option value="">Pilih Kantor</option>
                            <option v-for="(office, index) in filteredOffices" :key="office.id" :value="office.id">@{{ office.name }}</option>
                        </select>
                        <!--end::Input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 d-flex justify-content-between align-items-center">
                            <span><em v-cloak v-if="!model.divisionId">Pilih divisi</em></span>
                            <a href="/offices" target="_blank">Tambah Kantor</a>
                        </div>
                        <!--end::Description-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Card header-->
            </div>
            <!--end::Employment options-->
            <div class="d-flex justify-content-end">
                <!--begin::Button-->
                <a href="/employees" class="btn btn-light me-5" @click.prevent="onCancelSave">Batalkan</a>
                <!--end::Button-->
                <!--begin::Button-->
                <button type="submit" id="kt_ecommerce_add_category_submit" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" :disabled="submitLoading">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                <!--end::Button-->
            </div>
        </div>
        <!--end::Main column-->
    </form>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection

@section('pagescript')
<script>
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;
    const departments = <?php echo Illuminate\Support\Js::from($departments) ?>;
    const designations = <?php echo Illuminate\Support\Js::from($designations) ?>;
    const jobTitles = <?php echo Illuminate\Support\Js::from($jobTitles) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisions,
                offices,
                departments,
                designations,
                jobTitles,
                getResourcesLoading: false,
                model: {
                    name: '{{ $employee->name }}',
                    gender: '{{ $employee->gender }}',
                    placeOfBirth: '{{ $employee->place_of_birth }}',
                    dateOfBirth: '{{ $employee->date_of_birth }}',
                    identityType: '{{ $employee->identity_type }}',
                    identityNumber: '{{ $employee->identity_number }}',
                    driverLicenseType: '{{ $employee->driver_license_type }}',
                    driverLicenseNumber: '{{ $employee->driver_license_number }}',
                    maritalStatus: '{{ $employee->marital_status }}',
                    religion: '{{ $employee->religion }}',
                    bloodGroup: '{{ $employee->blood_group }}',
                    recentEducation: '{{ $employee->recent_education }}',
                    educationInstitutionName: '{{ $employee->education_institution_name }}',
                    studyProgram: '{{ $employee->study_program }}',
                    email: '{{ $employee->email }}',
                    phone: '{{ $employee->phone }}',
                    address: '{{ $employee->address }}',
                    emergencyContactName: '{{ $employee->emergency_contact_name }}',
                    emergencyContactRelation: '{{ $employee->emergency_contact_relation }}',
                    emergencyContactPhone: '{{ $employee->emergency_contact_phone }}',
                    startWorkDate: '{{ $employee->start_work_date }}',
                    employmentStatus: '{{ $employee->employment_status }}',
                    companyId: '{{ $employee?->office?->division?->company_id }}',
                    divisionId: '{{ $employee?->office?->division_id }}',
                    officeId: '{{ $employee->office_id }}',
                    oldCompanyId: '{{ $employee?->office?->division?->company_id }}',
                    oldDivisionId: '{{ $employee?->office?->division_id }}',
                    departmentId: '',
                    designationId: '',
                    jobTitleId: '',
                    photo: '{{ $employee->photo }}',
                    oldPhoto: '{{ $employee->photo }}',
                    oldEmployeeNumber: '{{ $old_employee_number }}',
                    oldEmployeePrefixNumber: '{{ $employee->number }}',
                },
                submitLoading: false,
            }
        },
        computed: {
            filteredDivisions() {
                const {
                    companyId
                } = this.model;
                if (companyId) {
                    return this.divisions.filter(division => division.company_id === Number(companyId));
                }

                return [];
            },
            filteredOffices() {
                const {
                    divisionId
                } = this.model;
                if (divisionId) {
                    return this.offices.filter(office => office.division_id === Number(divisionId));
                }

                return [];
            },
            filteredDesignations() {
                const {
                    departmentId
                } = this.model;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id === Number(departmentId));
                }

                return [];
            },
            filteredJobTitles() {
                const {
                    designationId
                } = this.model;
                if (designationId) {
                    return this.jobTitles.filter(jobTitle => jobTitle.designation_id === Number(designationId));
                }

                return [];
            },
            employeeNumber() {
                const {
                    companyId,
                    divisionId
                } = this.model;
                const currentDate = new Date();
                const year = currentDate.getFullYear();

                let companyInitial = '[INISIAL PERUSAHAAN]';
                let divisionInitial = '[INISIAL DIVISI]';

                if (companyId) {
                    const [company] = this.companies.filter(company => company.id === Number(companyId));
                    if (company) {
                        companyInitial = company?.initial;
                    }
                }

                if (divisionId) {
                    const [division] = this.divisions.filter(division => division.id === Number(divisionId));
                    if (division) {
                        divisionInitial = division?.initial;
                    }
                }

                const number = `${companyInitial}-${divisionInitial}`

                return number;
            },
            employeeNumberWithYear() {
                const currentDate = new Date();
                const year = currentDate.getFullYear().toString().substr(2);
                return `${this.employeeNumber}-${year}`;
            },
            employeeNumberWithCounter() {
                const counter = '[COUNTER]';
                return `${this.employeeNumberWithYear}${counter}`;
            },
        },
        methods: {
            async onSubmit() {
                let self = this;
                try {
                    const {
                        name,
                        gender,
                        placeOfBirth,
                        dateOfBirth,
                        identityType,
                        identityNumber,
                        driverLicenseType,
                        driverLicenseNumber,
                        maritalStatus,
                        religion,
                        bloodGroup,
                        recentEducation,
                        educationInstitutionName,
                        studyProgram,
                        email,
                        phone,
                        address,
                        emergencyContactName,
                        emergencyContactRelation,
                        emergencyContactPhone,
                        startWorkDate,
                        employmentStatus,
                        companyId,
                        divisionId,
                        officeId,
                        departmentId,
                        designationId,
                        jobTitleId,
                        photo,
                        oldPhoto,
                        oldDivisionId,
                    } = self.model;

                    const [employeeNumber] = self.employeeNumber;

                    const requestBody = {
                        name,
                        number: (self.model.divisionId !== self.model.oldDivisionId) ? employeeNumber : self.model.oldEmployeePrefixNumber,
                        gender,
                        place_of_birth: placeOfBirth,
                        date_of_birth: dateOfBirth,
                        identity_type: identityType,
                        identity_number: identityNumber,
                        driver_license_type: driverLicenseType,
                        driver_license_number: driverLicenseNumber,
                        marital_status: maritalStatus,
                        religion,
                        blood_group: bloodGroup,
                        recent_education: recentEducation,
                        education_institution_name: educationInstitutionName,
                        study_program: studyProgram,
                        email,
                        phone,
                        address,
                        emergency_contact_name: emergencyContactName,
                        emergency_contact_relation: emergencyContactRelation,
                        emergency_contact_phone: emergencyContactPhone,
                        start_work_date: startWorkDate,
                        employment_status: employmentStatus,
                        company_id: companyId,
                        division_id: divisionId,
                        old_division_id: oldDivisionId,
                        office_id: officeId,
                        // department_id: departmentId,
                        // designation_id: designationId,
                        job_title_id: jobTitleId,
                        photo,
                        old_photo: oldPhoto,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    self.submitLoading = true;

                    const response = await axios.post('/employees/{{ $employee->id }}', formData);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        // setTimeout(() => {
                        //     self.gotoUrl('/offices');
                        // }, 500);
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
            async getResources() {
                let self = this;
                try {
                    self.getResourcesLoading = true;

                    const response = await axios.get('/api/global/create-employee-resources');

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.companies = data.companies;
                        this.divisions = data.divisions;
                        this.offices = data.offices;
                        this.departments = data.departments;
                        this.designations = data.designations;
                        this.jobTitles = data.job_titles;
                        // toastr.success(message);
                    }
                } catch (error) {
                    console.log(error)
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.getResourcesLoading = false;
                }
            },
            onCancelSave() {
                const confirmed = confirm('Apakah anda yakin? data tidak akan tersimpan');
                if (confirmed) {
                    document.location.href = '/employees';
                }
            },
            handleFileUpload: function() {
                const imageRef = this.$refs.image;
                const file = imageRef.files[0];
                if (file) {
                    this.model.photo = file
                }
            },
        },
        watch: {
            'model.companyId': function(val) {
                this.model.divisionId = "";
            },
            'model.divisionId': function(val) {
                this.model.officeId = "";
            },
            'model.departmentId': function(val) {
                this.model.designationId = "";
            },
            'model.designationId': function(val) {
                this.model.jobTitleId = "";
            },
        }
    });
</script>
<script>
    $(function() {
        var imageInputElement = document.querySelector("#employee_photo");
        // console.log(imageInputElement);
        var imageInput = KTImageInput.getInstance(imageInputElement);
        // console.log(imageInput); 
        // imageInput.on("kt.imageinput.change", function() {
        //     console.log("kt.imageinput.change event is changes");
        // });

        imageInput.on("kt.imageinput.canceled", function() {
            // console.log("kt.imageinput.canceled event is fired");
            app.$data.model.photo = '';
        });

        imageInput.on("kt.imageinput.removed", function() {
            app.$data.model.photo = '';
            console.log("kt.imageinput.removed event is fired");
        });
    })
</script>
@endsection