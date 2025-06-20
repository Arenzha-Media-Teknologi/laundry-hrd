@extends('layouts.app')

@section('title', ($employee->name ?? '') . ' - Ubah')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
        color: rgb(26, 26, 26);
    }
</style>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <form class="form d-flex flex-column flex-lg-row" enctype="multipart/form-data" @submit.prevent="onSubmit">
        <!--begin::Aside column-->
        <div class=" d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-250px mb-7 me-lg-10">
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>ID Pegawai</h2>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body text-center pt-0">
                    <input type="text" :value="employeeNumberWithCounter" class="form-control form-control-solid form-control-sm mb-2" placeholder="Nomor pegawai" disabled />
                </div>
            </div>
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
                    <div class="image-input <?= !isset($employee->photo) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/avatars/blank.svg') ?>)" id="employee_photo">
                        <!--begin::Preview existing avatar-->
                        <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee->photo) ? 'background-image: url(' . $employee->photo . ')' : ''  ?>"></div>
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
        </div>
        <!--end::Aside column-->

        <!--begin::Main column-->
        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
            <!--begin::General options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Umum</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Nama:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.name" class="form-control form-control-sm mb-2" placeholder="Masukkan nama pegawai" />
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">

                                </div>
                                <div class="col-md-8">
                                    <div class="text-muted fs-7">Nama lengkap sesuai KTP</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center pt-lg-2">
                                <div class="col-md-4">
                                    <label class="form-label fs-7 required">Jenis Kelamin:</label>
                                </div>
                                <div class="col-md-8 d-flex">
                                    <div class="me-3">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input v-model="model.gender" class="form-check-input" type="radio" id="genderRadio1" value="male" />
                                            <label class="form-check-label" for="genderRadio1">
                                                Laki-laki
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input v-model="model.gender" class="form-check-input" type="radio" id="genderRadio2" value="female" />
                                            <label class="form-check-label" for="genderRadio2">
                                                Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-content mb-8"><span class="text-muted">Pekerjaan</span></div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7 required">Tanggal Mulai Bekerja:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="date" v-model="model.startWorkDate" class="form-control form-control-sm mb-2" placeholder="Masukkan tanggal mulai bekerja" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7 required">Status Pegawai:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.employmentStatus" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Status</option>
                                        <option value="tetap">Tetap</option>
                                        <option value="tidak_tetap">Tidak tetap</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">PTKP:</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-select form-select-sm" v-model="model.npwp.status">
                                        <option value="">Pilih PTKP</option>
                                        @for($i = 0; $i < 4; $i++) <option value="k{{$i}}">K{{ $i }}</option> @endfor
                                            @for($i = 0; $i < 4; $i++) <option value="tk{{$i}}">TK{{ $i }}</option> @endfor
                                                @for($i = 0; $i < 4; $i++) <option value="hb{{$i}}">HB{{ $i }}</option> @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Perusahaan:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.companyId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Perusahaan</option>
                                        <option v-for="(company, index) in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Divisi:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.divisionId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Divisi</option>
                                        <option v-for="(division, index) in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Kantor:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.officeId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Kantor</option>
                                        <option v-for="(office, index) in filteredOffices" :key="office.id" :value="office.id">@{{ office.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Departemen:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.departmentId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Departemen</option>
                                        <option v-for="(department, index) in departments" :key="department.id" :value="department.id">@{{ department.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Bagian:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.designationId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Bagian</option>
                                        <option v-for="(designation, index) in filteredDesignations" :key="designation.id" :value="designation.id">@{{ designation.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row align-items-center mb-8">
                        <div class="col-md-2">
                            <label class="required form-label fs-7">Job Title:</label>
                        </div>
                        <div class="col-md-4">
                            <select v-model="model.jobTitleId" class="form-select form-select-sm mb-2">
                                <option value="">Pilih Job Title</option>
                                <option v-for="(jobTitle, index) in filteredJobTitles" :key="jobTitle.id" :value="jobTitle.id">@{{ jobTitle.name }}</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Job Title:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.jobTitleId" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Job Title</option>
                                        <option v-for="(jobTitle, index) in filteredJobTitles" :key="jobTitle.id" :value="jobTitle.id">@{{ jobTitle.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Tipe:</label>
                                </div>
                                <div class="col-md-8 d-flex">
                                    <div class="me-3">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" v-model="model.employmentType" id="staffRadio1" value="staff" />
                                            <label class="form-check-label" for="staffRadio1">
                                                Staff
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" v-model="model.employmentType" id="staffRadio2" value="non_staff" />
                                            <label class="form-check-label" for="staffRadio2">
                                                Non Staff
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator mb-8"></div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Tempat Lahir:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.placeOfBirth" class="form-control form-control-sm mb-2" placeholder="Masukkan tempat lahir" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Tanggal Lahir:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="date" v-model="model.dateOfBirth" class="form-control form-control-sm mb-2" placeholder="Masukkan tempat lahir" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Umur:</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="text" :value="employeeAge" class="form-control form-control-sm" disabled />
                                        <span class="input-group-text">Tahun</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-8">
                            <div class="row align-items-start">
                                <div class="col-md-3">
                                    <label class="form-label fs-7 required">Alamat Tinggal:</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea v-model="model.address" class="form-control form-control-sm" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Email:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.email" class="form-control form-control-sm mb-2" placeholder="Masukkan email" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">HP:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.phone" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor handphone" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center mb-6">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">KTP:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.identityNumber" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor KTP" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <div class="image-input <?= !isset($employee->identity_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="ktp_photo">
                                        <!--begin::Preview existing avatar-->
                                        <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee->identity_image) ? 'background-image: url(' . $employee->identity_image . ')' : ''  ?>"></div>
                                        <!--end::Preview existing avatar-->
                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                            <!--begin::Icon-->
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--end::Icon-->
                                            <!--begin::Inputs-->
                                            <input type="file" ref="identityImage" name="avatar" accept=".png, .jpg, .jpeg" @change="handleIdentityImageChange" />
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
                                    <div class="text-muted fs-7">Foto KTP (hanya format .jpg, .jpeg, .png)</div>
                                    <!--end::Description-->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center mb-6">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">NPWP:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.npwp.number" class="form-control form-control-sm" placeholder="Masukkan NPWP">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <div class="image-input <?= !isset($employee->npwp_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="npwp_photo">
                                        <!--begin::Preview existing avatar-->
                                        <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee->npwp_image) ? 'background-image: url(' . $employee->npwp_image . ')' : ''  ?>"></div>
                                        <!--end::Preview existing avatar-->
                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                            <!--begin::Icon-->
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--end::Icon-->
                                            <!--begin::Inputs-->
                                            <input type="file" ref="NPWPImage" name="avatar" accept=".png, .jpg, .jpeg" @change="handleNPWPImageChange" />
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
                                    <div class="text-muted fs-7">Foto NPWP (hanya format .jpg, .jpeg, .png)</div>
                                    <!--end::Description-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-10">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Pendidikan Akhir:</label>
                                </div>
                                <div class="col-md-8">
                                    <select v-model="model.recentEducation" class="form-select form-select-sm mb-2">
                                        <option value="">Pilih Pendidikan Terakhir</option>
                                        <option value="sd">SD</option>
                                        <option value="smp">SMP</option>
                                        <option value="sma">SMA</option>
                                        <option value="universitas">Universitas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Nama Institusi:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.educationInstitutionName" class="form-control form-control-sm mb-2" placeholder="Masukkan nama institusi pendidikan" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-content mb-10"><span class="text-muted">SIM</span></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row align-items-center mb-8">
                                <div class="col-md-2">
                                    <label class="form-label fs-7">Jenis SIM:</label>
                                </div>
                                <div class="col-md-4">
                                    <select v-model="model.driverLicenseType" class="form-select form-select-sm">
                                        <option value="">Pilih Jenis SIM</option>
                                        <option value="a">A</option>
                                        <option value="b1">B1</option>
                                        <option value="b2">B2</option>
                                        <option value="c">C</option>
                                        <option value="d">D</option>
                                        <option value="a umum">A Umum</option>
                                        <option value="b1 umum">B1 Umum</option>
                                        <option value="b2 umum">B2 Umum</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary btn-sm" @click="addDriversLicense">Tambahkan SIM <span class="text-uppercase">@{{ model.driverLicenseType }}</span></button>
                                </div>
                            </div>
                            <div v-if="model.driversLicenses.length < 1" class="text-center text-muted">
                                <span><i class="bi bi-inbox fs-3x text-gray-300"></i></span>
                                <p class=" fw-bold">Belum ada SIM</p>
                            </div>
                            <div v-for="(driversLicense, index) in model.driversLicenses" class="card card-bordered card-flush mb-8" :key="index">
                                <div class="card-header">
                                    <div class="card-title">SIM&nbsp;<span class="text-uppercase">@{{ driversLicense.type }}</span></div>
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-active-light-danger text-danger" @click.prevent="removeDriversLicense(index)"><i class="bi bi-trash text-danger"></i> Hapus</a>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-4">
                                            <div class="image-input image-input-outline mb-3" :class="driversLicense.image || 'image-input-empty'" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-150px h-150px" :style="`background-image: url('${driversLicense.previewImage}')`"></div>
                                                <!--end::Preview existing avatar-->
                                                <!--begin::Label-->
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                                    <!--begin::Icon-->
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <!--end::Icon-->
                                                    <!--begin::Inputs-->
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" @change="handleDriversLicenseImage($event, index)" />
                                                    <input type="hidden" name="avatar_remove" />
                                                    <!--end::Inputs-->
                                                </label>
                                                <!--end::Label-->
                                                <!--begin::Cancel-->
                                                <span v-if="driversLicense.image" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-bs-toggle="tooltip" title="Hapus" style="position: absolute; left: 90%; top: 90%;" @click="removeDriversLicenseImage(index)">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                                <!--end::Cancel-->
                                            </div>
                                            <!--end::Image input-->
                                            <!--begin::Description-->
                                            <div class="text-muted fs-7">Foto SIM (Hanya format .jpg, .jpeg, .png)</div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="row mb-7 align-items-center">
                                                <label class="col-lg-4 fw-bold text-gray-700 required">Nomor #</label>
                                                <div class="col-lg-8">
                                                    <input type="text" v-model="driversLicense.number" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="row mb-7 align-items-center">
                                                <label class="col-lg-4 fw-bold text-gray-700 required">Tanggal Berakhir</label>
                                                <div class="col-lg-8">
                                                    <input type="date" v-model="driversLicense.expireDate" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Contact options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Kontak Darurat</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="mb-8">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <label class="form-label fs-7">Nama:</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" v-model="model.emergencyContactName" class="form-control form-control-sm mb-2" placeholder="Masukkan nama" />
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">HP:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.emergencyContactPhone" class="form-control form-control-sm mb-2" placeholder="Masukkan nomor handphone" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fs-7">Hubungan:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" v-model="model.emergencyContactRelation" class="form-control form-control-sm mb-2" placeholder="Masukkan hubungan kontak darurat" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- begin::collapse -->
                </div>
                <!--end::Card header-->
            </div>

            <!--begin::Contact options-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title d-block">
                        <h2>Approver Pengajuan</h2>
                        <div>
                            <small>Pilih pegawai yang mengkonfirmasi pengajuan</small>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <label class="form-label fs-7">Izin:</label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select form-select-sm" id="select-permission-approver">
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($employees as $approver)
                                    <option value="{{ $approver->id }}">{{ $approver->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <label class="form-label fs-7">Lembur:</label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select form-select-sm" id="select-overtime-approver">
                                    <option value="">Pilih Pegawai</option>
                                    @foreach($employees as $overtimeApproverEmployee)
                                    <option value="{{ $overtimeApproverEmployee->id }}">{{ $overtimeApproverEmployee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
            </div>

            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Penggajian</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <div class="card-body pt-0">
                    <h5>Nominal</h5>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Gaji Pokok:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" v-model="model.salary.gaji_pokok" class="form-control form-control-sm" placeholder="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Uang Harian:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" v-model="model.salary.uang_harian" class="form-control form-control-sm" placeholder="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-8">
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Lembur:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" v-model="model.salary.lembur" class="form-control form-control-sm" placeholder="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="required form-label fs-7">Tunjangan:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" v-model="model.salary.tunjangan" class="form-control form-control-sm" placeholder="0" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="separator mb-5"></div>
                    <div v-cloak v-if="model.divisionId == '12'">
                        <h5 class="mb-5">Gaji Pegawai AerPlus</h5>
                        <div class="row mb-8">
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fs-7">Uang Makan:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" v-model="model.salary.uang_makan" class="form-control form-control-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-8">
                            <div class="col-md-6">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label fs-7">Tunjangan Harian (Leader):</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" v-model="model.salary.tunjangan_harian" class="form-control form-control-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="separator mb-5"></div>
                    </div>
                    <h5 class="mb-5">Gaji Harian</h5>
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="model.salary.magenta_daily_salary" id="checkGetMagentaDailySalary" />
                                <label class="form-check-label" for="checkGetMagentaDailySalary">
                                    <span class="fs-7 fw-bold text-gray-800">Dapat gaji harian magenta</span>
                                </label>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="model.salary.aerplus_daily_salary" id="checkGetAerplusDailySalary" />
                                <label class="form-check-label" for="checkGetAerplusDailySalary">
                                    <span class="fs-7 fw-bold text-gray-800">Dapat gaji harian</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Asuransi</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <div class="row mb-8">
                        <div class="col-md-12">
                            <div class="row align-items-center mb-8">
                                <div class="col-md-2">
                                    <label class="form-label fs-7">Jenis:</label>
                                </div>
                                <div class="col-md-4">
                                    <select v-model="model.insuranceType" class="form-select form-select-sm">
                                        <option value="">Pilih Jenis Asuransi</option>
                                        <option value="bpjs_ketenagakerjaan">BPJS Ketenagakerjaan</option>
                                        <option value="bpjs_mandiri">BPJS Mandiri</option>
                                        @foreach($private_insurances as $insurance)
                                        <option value="private_{{ $insurance->id }}">{{ $insurance->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary btn-sm" @click="addInsurance">Tambah</button>
                                </div>
                            </div>
                            <div v-if="model.insurances.length < 1" class="text-center text-muted">
                                <span><i class="bi bi-inbox fs-3x text-gray-300"></i></span>
                                <p class="fw-bold mt-3">Belum ada Asuransi</p>
                            </div>
                            <div v-for="(insurance, index) in model.insurances" class="card card-bordered card-flush mb-8">
                                <div class="card-header">
                                    <div class="card-title">@{{ insurance.name }}</div>
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-active-light-danger text-danger" @click.prevent="removeInsurance(index)"><i class="bi bi-trash text-danger"></i> Hapus</a>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-4">
                                            <div class="image-input image-input-outline mb-3" data-kt-image-input="true" :class="insurance.image || 'image-input-empty'" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)">
                                                <!--begin::Preview existing avatar-->
                                                <div class="image-input-wrapper w-150px h-150px" :style="`background-image: url('${insurance.previewImage}')`"></div>
                                                <!--end::Preview existing avatar-->
                                                <!--begin::Label-->
                                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                                    <!--begin::Icon-->
                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                    <!--end::Icon-->
                                                    <!--begin::Inputs-->
                                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" @change="handleInsuranceImage($event, index)" />
                                                    <input type="hidden" name="avatar_remove" />
                                                    <!--end::Inputs-->
                                                </label>
                                                <!--end::Label-->
                                                <span v-if="insurance.image" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-bs-toggle="tooltip" title="Hapus" style="position: absolute; left: 90%; top: 90%;" @click="removeInsuranceImage(index)">
                                                    <i class="bi bi-x fs-2"></i>
                                                </span>
                                            </div>
                                            <!--end::Image input-->
                                            <!--begin::Description-->
                                            <div class="text-muted fs-7">Foto Kartu Asuransi (Hanya format .jpg, .jpeg, .png)</div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="row mb-7 align-items-center">
                                                <label class="col-lg-4 fw-bold text-gray-700 required">No. Polis #</label>
                                                <div class="col-lg-8">
                                                    <input type="text" v-model="insurance.number" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="row mb-7 align-items-center">
                                                <label class="col-lg-4 fw-bold text-gray-700 required">Tahun Mulai</label>
                                                <div class="col-lg-8">
                                                    <input type="number" v-model="insurance.startYear" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card header-->
            </div>
            <!--end::Contact options-->
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
    const privateInsurances = <?php echo Illuminate\Support\Js::from($private_insurances) ?>;
    const driversLicenses = <?php echo Illuminate\Support\Js::from($drivers_licenses) ?>;
    const employeeInsurances = <?php echo Illuminate\Support\Js::from($employee_insurances) ?>;

    Vue.prototype.moment = moment;

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
                privateInsurances,
                getResourcesLoading: false,
                model: {
                    name: '{{ $employee->name }}',
                    gender: '{{ $employee->gender }}',
                    placeOfBirth: '{{ $employee->place_of_birth }}',
                    dateOfBirth: '{{ $employee->date_of_birth }}',
                    identityType: '{{ $employee->identity_type }}',
                    identityNumber: '{{ $employee->identity_number }}',
                    identityImage: '{{ $employee->identity_image }}',
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
                    address: '{{ preg_replace( "/\r|\n/", "", $employee->address ) }}',
                    emergencyContactName: '{{ $employee->emergency_contact_name }}',
                    emergencyContactRelation: '{{ $employee->emergency_contact_relation }}',
                    emergencyContactPhone: '{{ $employee->emergency_contact_phone }}',
                    startWorkDate: '{{ $employee->start_work_date }}',
                    employmentStatus: '{{ $employee?->activeCareer?->status ?? "" }}',
                    companyId: '{{ $employee?->office?->division?->company_id }}',
                    divisionId: '{{ $employee?->office?->division_id }}',
                    officeId: '{{ $employee->office_id }}',
                    oldCompanyId: '{{ $employee?->office?->division?->company_id }}',
                    oldDivisionId: '{{ $employee?->office?->division_id }}',
                    departmentId: '{{ $employee?->activeCareer?->jobTitle?->designation->department->id ?? "" }}',
                    designationId: '{{ $employee?->activeCareer?->jobTitle?->designation->id ?? "" }}',
                    jobTitleId: '{{ $employee?->activeCareer?->jobTitle?->id ?? "" }}',
                    employmentType: '{{ $employee->type }}',
                    photo: '{{ $employee->photo }}',
                    oldPhoto: '{{ $employee->photo }}',
                    oldEmployeeNumber: '{{ $old_employee_number }}',
                    oldEmployeePrefixNumber: '{{ $employee->number }}',
                    npwp: {
                        image: '{{ $employee->npwp_image }}',
                        number: '{{ $employee->npwp_number }}',
                        effectiveDate: '',
                        status: '{{ $employee->npwp_status }}',
                    },
                    salary: {
                        gaji_pokok: '{{ $salary_values["gaji_pokok"] }}',
                        tunjangan: '{{ $salary_values["tunjangan"] }}',
                        uang_harian: '{{ $salary_values["uang_harian"] }}',
                        lembur: '{{ $salary_values["lembur"] }}',
                        uang_makan: '{{ $salary_values["uang_makan"] }}',
                        tunjangan_harian: '{{ $salary_values["tunjangan_harian"] }}',
                        magenta_daily_salary: Number.parseInt('{{ $employee->magenta_daily_salary }}'),
                        aerplus_daily_salary: Number.parseInt('{{ $employee->aerplus_daily_salary }}'),
                    },
                    driversLicenses,
                    insuranceType: '',
                    insurances: employeeInsurances,
                    permissionApprover: '{{ $employee->permission_approver_id }}',
                    overtimeApprover: '{{ $employee->overtime_approver_id }}',
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
                    return this.divisions.filter(division => division.company_id == companyId);
                }

                return [];
            },
            filteredOffices() {
                const {
                    divisionId
                } = this.model;
                if (divisionId) {
                    return this.offices.filter(office => office.division_id == divisionId);
                }

                return [];
            },
            filteredDesignations() {
                const {
                    departmentId
                } = this.model;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id == departmentId);
                }

                return [];
            },
            filteredJobTitles() {
                const {
                    designationId
                } = this.model;
                if (designationId) {
                    return this.jobTitles.filter(jobTitle => jobTitle.designation_id == designationId);
                }

                return [];
            },
            employeeNumber() {
                const {
                    companyId,
                    divisionId
                } = this.model;
                const currentDate = new Date();
                const year = currentDate.getFullYear().toString().substr(2);

                let companyInitial = '[ID PERUSAHAAN]';
                let divisionInitial = '[ID DIVISI]';

                if (companyId) {
                    const [company] = this.companies.filter(company => company.id == companyId);
                    if (company) {
                        companyInitial = company?.initial;
                    }
                }

                if (divisionId) {
                    const [division] = this.divisions.filter(division => division.id == divisionId);
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
            employeeAge() {
                if (moment && this.model.dateOfBirth) {
                    const dateOfBirth = moment(this.model.dateOfBirth);
                    const now = moment();
                    return now.diff(dateOfBirth, 'years');
                }

                return '';
            }
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
                        identityImage,
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
                        employmentType,
                        photo,
                        npwp,
                        salary,
                        driversLicenses,
                        insurances,
                        permissionApprover,
                        overtimeApprover,
                    } = self.model;

                    const [employeeNumber] = self.employeeNumber;

                    const requestBody = {
                        name,
                        number: employeeNumber,
                        gender,
                        place_of_birth: placeOfBirth,
                        date_of_birth: dateOfBirth,
                        identity_type: identityType,
                        identity_number: identityNumber,
                        identity_image: identityImage,
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
                        office_id: officeId,
                        // department_id: departmentId,
                        // designation_id: designationId,
                        job_title_id: jobTitleId,
                        type: employmentType,
                        npwp_number: npwp.number,
                        npwp_effective_date: npwp.effectiveDate,
                        npwp_status: npwp.status,
                        npwp_image: npwp.image,
                        salary_gaji_pokok: salary.gaji_pokok,
                        salary_tunjangan: salary.tunjangan,
                        salary_uang_harian: salary.uang_harian,
                        salary_lembur: salary.lembur,
                        salary_uang_makan: salary.uang_makan,
                        salary_tunjangan_harian: salary.tunjangan_harian,
                        magenta_daily_salary: salary.magenta_daily_salary == true ? 1 : 0,
                        aerplus_daily_salary: salary.aerplus_daily_salary == true ? 1 : 0,
                        photo,
                        drivers_licenses: driversLicenses,
                        insurances,
                        permission_approver_id: permissionApprover,
                        overtime_approver_id: overtimeApprover,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        if (Array.isArray(requestBody[property])) {
                            formData.append(property, JSON.stringify(requestBody[property]));
                        } else {
                            formData.append(property, requestBody[property]);
                        }
                    }

                    if (driversLicenses.length > 0) {
                        for (const index in driversLicenses) {
                            // console.log(`${index}: ${array[index].a}`);
                            formData.append('drivers_license_image_' + index, driversLicenses[index].image);
                        }
                    }

                    if (insurances.length > 0) {
                        for (const index in insurances) {
                            // console.log(`${index}: ${array[index].a}`);
                            const insuranceType = insurances[index].type;
                            const insuranceId = insurances[index].id;
                            if (insuranceType == 'bpjs') {
                                if (insuranceId == 'bpjs_mandiri') {
                                    formData.append('bpjs_mandiri_image', insurances[index].image);
                                } else if (insuranceId == 'bpjs_ketenagakerjaan') {
                                    formData.append('bpjs_ketenagakerjaan_image', insurances[index].image);
                                }
                            } else if (insuranceType == 'private') {
                                formData.append('private_insurance_image_' + index, insurances[index].image);
                            }
                        }
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
                        setTimeout(() => {
                            // self.gotoUrl('/offices');
                            window.location.href = '/employees';
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
                // console.log(imageRef);
                const file = imageRef.files[0];
                if (file) {
                    this.model.photo = file
                }
            },
            addDriversLicense() {
                const type = this.model.driverLicenseType;

                if (!type) {
                    return null;
                }

                const {
                    driversLicenses
                } = this.model;

                const driversLicensesTypes = driversLicenses.map(license => license.type);

                if (driversLicensesTypes.includes(type)) {
                    return null;
                }

                if (driversLicensesTypes.length == 3) {
                    return null;
                }

                this.model.driversLicenses.push({
                    image: null,
                    type,
                    number: '',
                    expireDate: '',
                    previewImage: '',
                });

                // const imageInputElement = document.querySelector("#driversLicenseImage" + (this.model.driversLicenses.length - 1));
                // const imageInput = new KTImageInput(imageInputElement);
            },
            removeDriversLicense(index) {
                this.model.driversLicenses.splice(index, 1);
            },
            handleDriversLicenseImage: function(event, index) {
                // const imageRef = this.$refs.image;
                // const file = imageRef.files[0];
                // if (file) {
                //     this.model.photo = file
                // }
                const file = event.target.files[0];
                if (file) {
                    this.model.driversLicenses[index].image = file;
                    const previewImageObjectUrl = URL.createObjectURL(file);
                    this.model.driversLicenses[index].previewImage = previewImageObjectUrl;
                    // const previewImageEl = document.getElementById('imageInputWrapper' + index);
                    // previewImageEl.setAttribute('placeholder-image', previewImageEl.style.backgroundImage);
                    // previewImageEl.style.backgroundImage = `url("${URL.createObjectURL(file)}")`;
                }
            },
            removeDriversLicenseImage: function(index) {
                console.log('clicked index' + index)
                if (typeof index !== "undefined") {
                    this.model.driversLicenses[index].image = null;
                    this.model.driversLicenses[index].previewImage = '';
                    // const previewImageEl = document.getElementById('imageInputWrapper' + index);
                    // previewImageEl.setAttribute('style', '');
                }
            },
            handleIdentityImageChange: function() {
                const imageRef = this.$refs.identityImage;
                const file = imageRef.files[0];
                if (file) {
                    this.model.identityImage = file
                }
            },
            handleNPWPImageChange: function() {
                const imageRef = this.$refs.NPWPImage;
                const file = imageRef.files[0];
                if (file) {
                    this.model.npwp.image = file
                }
            },
            addInsurance() {
                const insuranceTypeId = this.model.insuranceType;

                if (!insuranceTypeId) {
                    return null;
                }

                let insuranceData = null;
                const insuranceDataDefault = {
                    image: '',
                    number: '',
                    startYear: '',
                    previewImage: '',
                }

                if (insuranceTypeId == 'bpjs_ketenagakerjaan') {
                    insuranceData = {
                        id: 'bpjs_ketenagakerjaan',
                        type: 'bpjs',
                        name: 'BPJS Ketenagakerjaan',
                        ...insuranceDataDefault,
                    }
                } else if (insuranceTypeId == 'bpjs_mandiri') {
                    insuranceData = {
                        id: 'bpjs_mandiri',
                        type: 'bpjs',
                        name: 'BPJS Mandiri',
                        ...insuranceDataDefault,
                    }
                } else {
                    const splittedInsuranceTypeId = insuranceTypeId.split('_');
                    if (typeof splittedInsuranceTypeId[0] !== "undefined") {
                        if (splittedInsuranceTypeId[0] == 'private') {
                            const privateInsurances = this.privateInsurances;
                            // const privateInsurancesIds = privateInsurances.map(insurance => insurance.id);
                            const [selectedPrivateInsurance] = privateInsurances.filter(insurance => insurance.id == splittedInsuranceTypeId[1]);
                            if (selectedPrivateInsurance) {
                                insuranceData = {
                                    ...selectedPrivateInsurance,
                                    type: 'private',
                                    ...insuranceDataDefault,
                                }
                            }
                        }
                    }
                }

                if (insuranceData !== null) {
                    this.model.insurances.push(insuranceData);
                }
            },
            removeInsurance(index) {
                this.model.insurances.splice(index, 1);
            },
            handleInsuranceImage: function(event, index) {
                const file = event.target.files[0];
                if (file) {
                    this.model.insurances[index].image = file;
                    const previewImageObjectUrl = URL.createObjectURL(file);
                    this.model.insurances[index].previewImage = previewImageObjectUrl;
                }
            },
            removeInsuranceImage: function(index) {
                if (typeof index !== "undefined") {
                    this.model.insurances[index].image = null;
                    this.model.insurances[index].previewImage = '';
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
        var ktpPhotoElement = document.querySelector("#ktp_photo");
        var npwpPhotoElement = document.querySelector("#npwp_photo");
        // console.log(imageInputElement);
        var imageInput = KTImageInput.getInstance(imageInputElement);
        var ktpPhotoInput = KTImageInput.getInstance(ktpPhotoElement);
        var npwpPhotoInput = KTImageInput.getInstance(npwpPhotoElement);
        // console.log(imageInput); 
        // imageInput.on("kt.imageinput.change", function() {
        //     console.log("kt.imageinput.change event is changes");
        // });

        imageInput.on("kt.imageinput.canceled", function() {
            app.$data.model.photo = '';
        });
        ktpPhotoInput.on("kt.imageinput.canceled", function() {
            app.$data.model.identityImage = '';
        });
        npwpPhotoInput.on("kt.imageinput.canceled", function() {
            app.$data.model.npwp.image = '';
        });

        imageInput.on("kt.imageinput.removed", function() {
            console.log("kt.imageinput.removed event is fired");
        });

        if (app.$data.model.permissionApprover) {
            $('#select-permission-approver').val(app.$data.model.permissionApprover).trigger('change');
        }

        $('#select-permission-approver').select2();
        $('#select-permission-approver').on('change', function() {
            const value = $(this).val();
            app.$data.model.permissionApprover = value;
        })

        if (app.$data.model.overtimeApprover) {
            $('#select-overtime-approver').val(app.$data.model.overtimeApprover).trigger('change');
        }

        $('#select-overtime-approver').select2();
        $('#select-overtime-approver').on('change', function() {
            const value = $(this).val();
            app.$data.model.overtimeApprover = value;
        })
    })
</script>
@endsection