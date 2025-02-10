@extends('layouts.app')

@section('title', $employee->name . ' - Asuransi')

@section('head')

@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">BPJS</h3>
            </div>
            <div class="card-toolbar">
                @can('createInsurance', App\Models\Employee::class)
                @if(!isset($employee->bpjs->ketenagakerjaan_number) || !isset($employee->bpjs->mandiri_number))
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Tambah
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @if(!isset($employee->bpjs->ketenagakerjaan_number))
                        <li><a class="dropdown-item" data-bs-toggle="collapse" href="#collapseAddBpjsKetenagakerjaan" role="button" aria-expanded="false" aria-controls="collapseAddBpjsKetenagakerjaan">BPJS Ketenagakerjaan</a></li>
                        @endif
                        @if(!isset($employee->bpjs->mandiri_number))
                        <li><a class="dropdown-item" data-bs-toggle="collapse" href="#collapseAddBpjsMandiri" role="button" aria-expanded="false" aria-controls="collapseAddBpjsMandiri">BPJS Mandiri</a></li>
                        @endif
                    </ul>
                </div>
                @endif
                @endcan
            </div>
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <div class="collapse" id="collapseAddBpjsKetenagakerjaan">
                <div class="row justify-content-between">
                    <div class="col-lg-4">
                        <label class="col-lg-4 fw-bold text-gray-700 d-block mb-5">Kartu Asuransi</label>
                        <div class="image-input <?= !isset($employee?->bpjs->ketenagakerjaan_card_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="bpjs_ketenagakerjaan_photo_add">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee?->bpjs->ketenagakerjaan_card_image) ? 'background-image: url(' . $employee?->bpjs->ketenagakerjaan_card_image . ')' : ''  ?>"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                <!--begin::Icon-->
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--end::Icon-->
                                <!--begin::Inputs-->
                                <input type="file" ref="ketenagakerjaanCardImageAdd" accept=".png, .jpg, .jpeg" @change="handleFileChange('ketenagakerjaanCardImageAdd')" />
                                <input type="hidden" name="avatar_remove" />
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Hapus" @click="removeFile('ketenagakerjaanCardImageAdd')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar" @click="removeFile('ketenagakerjaanCardImageAdd')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Foto hanya boleh file dengan format .jpg, .jpeg, .png</div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row mb-7 align-items-center">
                            <label class="col-lg-4 fw-bold text-gray-700 required">Polis</label>
                            <div class="col-lg-8">
                                <input type="text" v-model="bpjs.model.ketenagakerjaanNumber" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row mb-7 align-items-center">
                            <label class="col-lg-4 fw-bold text-gray-700 required">Tahun Mulai Polis</label>
                            <div class="col-lg-8">
                                <select v-model="bpjs.model.ketenagakerjaanStartYear" class="form-select form-select-sm">
                                    <?php
                                    $limitYear = (int) date('Y');
                                    for ($i = 2020; $i <= $limitYear; $i++) {
                                        $nYear = $i;
                                    ?>
                                        <option value="<?= $nYear ?>"><?= $nYear ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <label class="col-lg-4 fw-bold text-gray-700"></label>
                            <div class="col-lg-8 text-end">
                                <button class="btn btn-light me-3" data-bs-toggle="collapse" data-bs-target="#collapseAddBpjsKetenagakerjaan" aria-expanded="false" aria-controls="collapseAddBpjsKetenagakerjaan">Batal</button>
                                <button type="button" :data-kt-indicator="bpjs.loading ? 'on' : null" class="btn btn-primary" :disabled="bpjs.loading" @click="updateBpjs">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Menyimpan...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse" id="collapseAddBpjsMandiri">
                <div class="row justify-content-between">
                    <div class="col-lg-4">
                        <label class="col-lg-4 fw-bold text-gray-700 d-block mb-5">Kartu Asuransi</label>
                        <div class="image-input <?= !isset($employee?->bpjs->mandiri_card_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="bpjs_ketenagakerjaan_photo">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee?->bpjs->mandiri_card_image) ? 'background-image: url(' . $employee?->bpjs->mandiri_card_image . ')' : ''  ?>"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                <!--begin::Icon-->
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--end::Icon-->
                                <!--begin::Inputs-->
                                <input type="file" ref="mandiriCardImage" accept=".png, .jpg, .jpeg" @change="handleFileChange('mandiriCardImage')" />
                                <input type="hidden" name="avatar_remove" />
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Hapus" @click="removeFile('mandiriCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar" @click="removeFile('mandiriCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Foto hanya boleh file dengan format .jpg, .jpeg, .png</div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row mb-7 align-items-center">
                            <label class="col-lg-4 fw-bold text-gray-700 required">Polis</label>
                            <div class="col-lg-8">
                                <input type="text" v-model="bpjs.model.mandiriNumber" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <label class="col-lg-4 fw-bold text-gray-700"></label>
                            <div class="col-lg-8 text-end">
                                <button class="btn btn-light me-3" data-bs-toggle="collapse" data-bs-target="#collapseAddBpjsMandiri" aria-expanded="false" aria-controls="collapseAddBpjsMandiri">Batal</button>
                                <button type="button" :data-kt-indicator="bpjs.loading ? 'on' : null" class="btn btn-primary" :disabled="bpjs.loading" @click="updateBpjs">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Menyimpan...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($employee->bpjs->ketenagakerjaan_number))
            <div class="d-flex justify-content-between align-items-center mb-8">
                <h4>BPJS Ketenagakerjaan</h4>
                <div class="text-end border rounded">
                    @can('deleteInsurance', App\Models\Employee::class)
                    <a href="#" class="btn btn-active-light-danger text-danger" @click.prevent="openBpjsDeleteConfirm('ketenagakerjaan')"><i class="bi bi-trash text-danger"></i> Hapus</a>
                    @endcan
                    @can('updateInsurance', App\Models\Employee::class)
                    <a data-bs-toggle="collapse" href=".collapseEditBpjsKetenagakerjaan" role="button" aria-expanded="false" aria-controls="collapseEditBpjsKetenagakerjaan" class="btn btn-active-light-primary text-primary">
                        <i class="bi bi-pencil text-primary"></i>
                        Ubah
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row justify-content-between">
                <div class="col-lg-4">
                    <div class="collapse show collapseEditBpjsKetenagakerjaan">
                        <div class="card card-bordered" style="width: auto;">
                            <div class="card-body p-2">
                                <div>
                                    @if(isset($employee?->bpjs->ketenagakerjaan_card_image))
                                    <img src="<?= $employee?->bpjs->ketenagakerjaan_card_image ?>" alt="Kartu BPJS Ketenagakerjaan" style="width: 100%; height: 200px; object-fit: cover; object-position: center;" loading="lazy" />
                                    @else
                                    <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="image" style="width: 100%; height: 200px; object-fit: cover; object-position: center;" loading="lazy" />
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="text-muted m-0 mt-3">
                                        <em>Kartu BPJS Ketenagakerjaan</em>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse collapseEditBpjsKetenagakerjaan">
                        <label class="col-lg-4 fw-bold text-gray-700 d-block mb-5">Kartu Asuransi</label>
                        <div class="image-input <?= !isset($employee?->bpjs->ketenagakerjaan_card_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="bpjs_ketenagakerjaan_photo">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee?->bpjs->ketenagakerjaan_card_image) ? 'background-image: url(' . $employee?->bpjs->ketenagakerjaan_card_image . ')' : ''  ?>"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                <!--begin::Icon-->
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--end::Icon-->
                                <!--begin::Inputs-->
                                <input type="file" ref="ketenagakerjaanCardImage" accept=".png, .jpg, .jpeg" @change="handleFileChange('ketenagakerjaanCardImage')" />
                                <input type="hidden" name="avatar_remove" />
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Hapus" @click="removeFile('ketenagakerjaanCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar" @click="removeFile('ketenagakerjaanCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Foto hanya boleh file dengan format .jpg, .jpeg, .png</div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Polis #</label>
                        <div class="col-lg-6">
                            <!-- <span class="fw-bolder fs-6 text-gray-800">729398721389</span> -->
                            <!-- <input type="text" class="form-control form-control-sm"> -->
                            <div class="collapse show collapseEditBpjsKetenagakerjaan">
                                <span class="fw-bolder fs-6 text-gray-800">
                                    @if(isset($employee->bpjs->ketenagakerjaan_number))
                                    {{$employee->bpjs->ketenagakerjaan_number}}
                                    @else
                                    <em class="text-muted">Tidak ada</em>
                                    @endif
                                </span>
                            </div>
                            <div class="collapse collapseEditBpjsKetenagakerjaan">
                                <input type="text" v-model="bpjs.model.ketenagakerjaanNumber" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-6 fw-bold text-muted">Tahun Mulai Polis</label>
                        <div class="col-lg-4">
                            <!-- <span class="fw-bolder fs-6 text-gray-800">729398721389</span> -->
                            <div class="collapse show collapseEditBpjsKetenagakerjaan">
                                <span class="fw-bolder fs-6 text-gray-800">
                                    @if(isset($employee->bpjs->ketenagakerjaan_start_year))
                                    {{$employee->bpjs->ketenagakerjaan_start_year}}
                                    @else
                                    <em class="text-muted">Tidak ada</em>
                                    @endif
                                </span>
                            </div>
                            <div class="collapse collapseEditBpjsKetenagakerjaan">
                                <!-- <input type="date" class="form-control form-control-sm"> -->
                                <select v-model="bpjs.model.ketenagakerjaanStartYear" class="form-select form-select-sm">
                                    <?php
                                    $limitYear = (int) date('Y');
                                    for ($i = 2020; $i <= $limitYear; $i++) {
                                        $nYear = $i;
                                    ?>
                                        <option value="<?= $nYear ?>"><?= $nYear ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php
                    // $bpjsValue = null;
                    // if (isset($employee->bpjsValues[0])) {
                    //     $bpjsValue = $employee->bpjsValues[0];
                    // }
                    $bpjsValue = $employee->bpjsValues[0] ?? null;
                    ?>
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Jaminan Hari Tua (JHT)</label>
                        <div class="col-lg-6">
                            <span class="fw-bolder fs-6 text-gray-800">Rp. {{ isset($bpjsValue) ? number_format($bpjsValue['jht'], 0, ',', '.') : '' }}</span>
                            <!-- <input type="text" class="form-control form-control-sm"> -->
                        </div>
                    </div>
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Jaminan Kecelekaan Kerja (JKK)</label>
                        <div class="col-lg-6">
                            <span class="fw-bolder fs-6 text-gray-800">Rp. {{ isset($bpjsValue) ?  number_format($bpjsValue['jkk'], 0, ',', '.') : '' }}</span>
                            <!-- <input type="text" class="form-control form-control-sm"> -->
                        </div>
                    </div>
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Jaminan Kematian (JK)</label>
                        <div class="col-lg-6">
                            <span class="fw-bolder fs-6 text-gray-800">Rp. {{ isset($bpjsValue) ?  number_format($bpjsValue['jkm'], 0, ',', '.') : '' }}</span>
                            <!-- <input type="text" class="form-control form-control-sm"> -->
                        </div>
                    </div>
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Jaminan Pensiun (JP)</label>
                        <div class="col-lg-4">
                            <span class="fw-bolder fs-6 text-gray-800">Rp. {{ isset($bpjsValue) ?  number_format($bpjsValue['jp'], 0, ',', '.') : '' }}</span>
                            <!-- <input type="text" class="form-control form-control-sm"> -->
                        </div>
                    </div>
                    <div class="collapse collapseEditBpjsKetenagakerjaan text-end">
                        <!-- <button class="btn btn-primary">Simpan</button> -->
                        <button type="button" :data-kt-indicator="bpjs.loading ? 'on' : null" class="btn btn-primary" :disabled="bpjs.loading" @click="updateBpjs">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Menyimpan...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="separator my-5"></div>
            @endif
            @if(isset($employee->bpjs->mandiri_number))
            <div class="d-flex justify-content-between align-items-center mb-8">
                <h4>BPJS Mandiri</h4>
                <div class="text-end border rounded">
                    @can('deleteInsurance', App\Models\Employee::class)
                    <a href="#" class="btn btn-active-light-danger text-danger" @click.prevent="openBpjsDeleteConfirm('mandiri')"><i class="bi bi-trash text-danger"></i> Hapus</a>
                    @endcan
                    @can('updateInsurance', App\Models\Employee::class)
                    <a data-bs-toggle="collapse" href=".collapseEditBpjsMandiri" role="button" aria-expanded="false" aria-controls="collapseEditBpjsMandiri" class="btn btn-active-light-primary text-primary">
                        <i class="bi bi-pencil text-primary"></i>
                        Ubah
                    </a>
                    @endcan
                </div>
            </div>
            <div class="row justify-content-between">
                <div class="col-lg-4">
                    <div class="collapse show collapseEditBpjsMandiri">
                        <div class="card card-bordered" style="width: auto;">
                            <div class="card-body p-2">
                                <div>
                                    @if(isset($employee?->bpjs->mandiri_card_image))
                                    <img src="<?= $employee?->bpjs->mandiri_card_image ?>" alt="Kartu BPJS Ketenagakerjaan" style="width: 100%; height: 200px; object-fit: cover; object-position: center;" loading="lazy" />
                                    @else
                                    <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="image" style="width: 100%; height: 200px; object-fit: cover; object-position: center;" loading="lazy" />
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="text-muted m-0 mt-3">
                                        <em>Kartu BPJS Mandiri</em>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse collapseEditBpjsMandiri">
                        <label class="col-lg-4 fw-bold text-gray-700 d-block mb-5">Kartu Asuransi</label>
                        <div class="image-input <?= !isset($employee?->bpjs->mandiri_card_image) ? 'image-input-empty' : '' ?> image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="bpjs_ketenagakerjaan_photo">
                            <!--begin::Preview existing avatar-->
                            <div class="image-input-wrapper w-150px h-150px" style="<?= isset($employee?->bpjs->mandiri_card_image) ? 'background-image: url(' . $employee?->bpjs->mandiri_card_image . ')' : ''  ?>"></div>
                            <!--end::Preview existing avatar-->
                            <!--begin::Label-->
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                <!--begin::Icon-->
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <!--end::Icon-->
                                <!--begin::Inputs-->
                                <input type="file" ref="mandiriCardImage" accept=".png, .jpg, .jpeg" @change="handleFileChange('mandiriCardImage')" />
                                <input type="hidden" name="avatar_remove" />
                                <!--end::Inputs-->
                            </label>
                            <!--end::Label-->
                            <!--begin::Cancel-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Hapus" @click="removeFile('mandiriCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Cancel-->
                            <!--begin::Remove-->
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar" @click="removeFile('mandiriCardImage')">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <!--end::Remove-->
                        </div>
                        <!--end::Image input-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7">Foto hanya boleh file dengan format .jpg, .jpeg, .png</div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row mb-7 align-items-center">
                        <label class="col-lg-6 fw-bold text-muted">Polis #</label>
                        <div class="col-lg-6">
                            <div class="collapse show collapseEditBpjsMandiri">
                                <span class="fw-bolder fs-6 text-gray-800">
                                    @if(isset($employee->bpjs->mandiri_number))
                                    {{$employee->bpjs->mandiri_number}}
                                    @else
                                    <em class="text-muted">Tidak ada</em>
                                    @endif
                                </span>
                            </div>
                            <div class="collapse collapseEditBpjsMandiri">
                                <input type="text" v-model="bpjs.model.mandiriNumber" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse collapseEditBpjsMandiri text-end">
                    <!-- <button class="btn btn-primary">Simpan</button> -->
                    <button type="button" :data-kt-indicator="bpjs.loading ? 'on' : null" class="btn btn-primary" :disabled="bpjs.loading" @click="updateBpjs">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Menyimpan...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
            @endif
        </div>
        <!--end::Card body-->
    </div>
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Asuransi Swasta</h3>
            </div>
            <div class="card-toolbar">
                @can('createInsurance', App\Models\Employee::class)
                <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target=".addEmployeeInsurance" aria-expanded="false" aria-controls="addEmployeeInsurance">
                    <i class="bi bi-plus fs-1"></i> Tambah Asuransi Pegawai
                </button>
                @endcan
            </div>
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <div class="collapse addEmployeeInsurance">
                <div class="pb-8">
                    <div class="card card-bordered">
                        <div class="card-body">
                            <h4 class="mb-8">Tambah Asuransi</h4>
                            <div class="row justify-content-between">
                                <div class="col-lg-4">
                                    <label class="col-lg-4 fw-bold text-gray-700 d-block mb-5">Kartu Asuransi</label>
                                    <div class="image-input image-input-empty image-input-outline mb-3" data-kt-image-input="true" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)" id="employee_photo">
                                        <!--begin::Preview existing avatar-->
                                        <div class="image-input-wrapper w-150px h-150px"></div>
                                        <!--end::Preview existing avatar-->
                                        <!--begin::Label-->
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                            <!--begin::Icon-->
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--end::Icon-->
                                            <!--begin::Inputs-->
                                            <input type="file" ref="privateInsuranceImage" name="avatar" accept=".png, .jpg, .jpeg" @change="handleFileChange('privateInsuranceImage')" />
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
                                </div>
                                <div class="col-lg-7">
                                    <div class="row mb-7 align-items-center">
                                        <label class="col-lg-4 fw-bold text-gray-700 required">Jenis Asuransi</label>
                                        <div class="col-lg-8">
                                            <!-- <span class="fw-bolder fs-6 text-gray-800">729398721389</span> -->
                                            <select v-model="privateInsurance.model.id" class="form-select">
                                                <option value="">Pilih Jenis Asuransi</option>
                                                @foreach($private_insurances as $private_insurance)
                                                <option value="{{ $private_insurance->id }}">{{ $private_insurance->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-7 align-items-center">
                                        <label class="col-lg-4 fw-bold text-gray-700 required">Polis #</label>
                                        <div class="col-lg-8">
                                            <!-- <span class="fw-bolder fs-6 text-gray-800">729398721389</span> -->
                                            <input v-model="privateInsurance.model.number" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-7 align-items-center">
                                        <label class="col-lg-4 fw-bold text-gray-700 required">Tahun Mulai Polis</label>
                                        <div class="col-lg-4">
                                            <!-- <span class="fw-bolder fs-6 text-gray-800">729398721389</span> -->
                                            <!-- <input type="year" class="form-control"> -->
                                            <select v-model="privateInsurance.model.startYear" class="form-select">
                                                <?php
                                                $limitYear = (int) date('Y');
                                                for ($i = 2020; $i <= $limitYear; $i++) {
                                                    $nYear = $i;
                                                ?>
                                                    <option value="<?= $nYear ?>"><?= $nYear ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <label class="col-lg-4 fw-bold text-gray-700"></label>
                                        <div class="col-lg-8 text-end">
                                            <button type="button" :data-kt-indicator="privateInsurance.loading ? 'on' : null" class="btn btn-primary" :disabled="privateInsurance.loading" @click="addPrivateInsurance">
                                                <span class="indicator-label">Simpan</span>
                                                <span class="indicator-progress">Menyimpan...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($employee->privateInsurances) == 0)
            <div class="text-center text-muted">
                <i class="bi bi-inbox fs-4x text-muted"></i>
                <p class="text-muted fs-6">Belum memiliki asuransi swasta</p>
            </div>
            @endif
            <div v-for="(employeePrivateInsurance, index) in employeePrivateInsurances">
                <div class="d-flex justify-content-between mb-8">
                    <div>
                        <h4>@{{ employeePrivateInsurance.name }}</h4>
                    </div>
                    <div class="text-end border rounded">
                        @can('deleteInsurance', App\Models\Employee::class)
                        <a href="#" class="btn btn-active-light-danger text-danger" @click.prevent="openPrivateInsuranceDeleteConfirm(employeePrivateInsurance.id)">
                            <i class="bi bi-trash text-danger"></i>
                            Hapus
                        </a>
                        @endcan
                        @can('updateInsurance', App\Models\Employee::class)
                        <a data-bs-toggle="collapse" :href="'.privateInsuranceCollapse' + employeePrivateInsurance.id" role="button" aria-expanded="false" :aria-controls="'privateInsuranceCollapse' + employeePrivateInsurance.id" class="btn btn-active-light-primary text-primary">
                            <i class="bi bi-pencil text-primary"></i>
                            Ubah
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-lg-4">
                        <div class="collapse show" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                            <div class="card card-bordered" style="width: auto;">
                                <div class="card-body p-2">
                                    <div>
                                        <img :src="employeePrivateInsurance?.pivot?.card_image || '<?= asset('assets/media/svg/files/blank-image.svg') ?>'" alt="Kartu BPJS Ketenagakerjaan" style="width: 100%; height: 200px; object-fit: cover; object-position: center;" />
                                    </div>
                                    <div class="text-center">
                                        <p class="text-muted m-0 mt-3">
                                            <em>Kartu Asuransi @{{ employeePrivateInsurance.name }}</em>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                            <div class="image-input image-input-outline mb-3" data-kt-image-input="true" :class="employeePrivateInsurance?.new_card_image ? '' : 'image-input-empty'" style="background-image: url(<?= asset('assets/media/svg/files/blank-image.svg') ?>)">
                                <!--begin::Preview existing avatar-->
                                <div class="image-input-wrapper w-150px h-150px" :style="`background-image: url('${employeePrivateInsurance?.new_card_image}')`"></div>
                                <!--end::Preview existing avatar-->
                                <!--begin::Label-->
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah">
                                    <!--begin::Icon-->
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <!--end::Icon-->
                                    <!--begin::Inputs-->
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" @change="handlePrivateInsuranceImage($event, index)" />
                                    <input type="hidden" name="avatar_remove" />
                                    <!--end::Inputs-->
                                </label>
                                <!--end::Label-->
                                <span v-if="employeePrivateInsurance?.new_card_image" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-bs-toggle="tooltip" title="Hapus" style="position: absolute; left: 90%; top: 90%;" @click="removePrivateInsuranceImage(index)">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                                <!--begin::Remove-->
                                <!-- <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar" @click="removePrivateInsuranceImage(index)">
                                    <i class="bi bi-x fs-2"></i>
                                </span> -->
                                <!--end::Remove-->
                            </div>
                            <!--end::Image input-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Foto Kartu Asuransi (Hanya format .jpg, .jpeg, .png)</div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row mb-7 align-items-center">
                            <label class="col-lg-6 fw-bold text-muted">Polis #</label>
                            <div class="col-lg-6">
                                <div class="collapse show" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                                    <span class="fw-bolder fs-6 text-gray-800">@{{ employeePrivateInsurance.pivot.number || '' }}</span>
                                </div>
                                <div class="collapse" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                                    <input type="text" v-model="employeePrivateInsurance.new_number" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-7">
                            <label class="col-lg-6 fw-bold text-muted">Tahun Mulai Polis</label>
                            <div class="col-lg-4">
                                <div class="collapse show" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                                    <span class="fw-bolder fs-6 text-gray-800">@{{ employeePrivateInsurance.pivot.start_year || '' }}</span>
                                </div>
                                <div class="collapse" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                                    <select v-model="employeePrivateInsurance.new_start_year" class="form-select form-select-sm">
                                        <?php
                                        $limitYear = (int) date('Y');
                                        for ($i = 2020; $i <= $limitYear; $i++) {
                                            $nYear = $i;
                                        ?>
                                            <option value="<?= $nYear ?>"><?= $nYear ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="row mb-7 align-items-center">
                                <label class="col-lg-6 fw-bold text-muted">Total Premi</label>
                                <div class="col-lg-6">
                                    <span class="fw-bolder fs-6 text-gray-800">
                                        Rp.
                                        <span v-if="employeePrivateInsurance.values.length > 0">
                                            @{{ currencyFormat(nullSafe(employeePrivateInsurance.values[0].total_premi)) }}
                                        </span>
                                        <span v-else>
                                            0
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-7 align-items-center">
                                <label class="col-lg-6 fw-bold text-muted">Premi Kesehatan</label>
                                <div class="col-lg-6">
                                    <span class="fw-bolder fs-6 text-gray-800">Rp.
                                        <span v-if="employeePrivateInsurance.values.length > 0">
                                            @{{ currencyFormat(nullSafe(employeePrivateInsurance.values[0].kesehatan)) }}
                                        </span>
                                        <span v-else>0</span>
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-7 align-items-center">
                                <label class="col-lg-6 fw-bold text-muted">Premi Tabungan</label>
                                <div class="col-lg-6">
                                    <span class="fw-bolder fs-6 text-gray-800">Rp. @{{ currencyFormat(employeePrivateInsurance.premi_tabungan) }}</span>
                                </div>
                            </div>
                            <div class="row mb-7 align-items-center">
                                <label class="col-lg-6 fw-bold text-muted">Premi Kematian</label>
                                <div class="col-lg-4">
                                    <span class="fw-bolder fs-6 text-gray-800">Rp. @{{ currencyFormat(employeePrivateInsurance.premi_kematian) }}</span>
                                </div>
                            </div>
                            <div class="row mb-7 align-items-center">
                                <label class="col-lg-6 fw-bold text-muted">Nilai Tunai Tabungan</label>
                                <div class="col-lg-4">
                                    <span class="fw-bolder fs-6 text-gray-800">Rp.
                                        <span v-if="employeePrivateInsurance.values.length > 0">
                                            @{{ currencyFormat(nullSafe(employeePrivateInsurance.values[0].kesehatan)) }}
                                        </span>
                                        <span v-else>0</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end collapse" :class="'privateInsuranceCollapse' + employeePrivateInsurance.id">
                            <!-- <button class="btn btn-primary btn-sm" @click="updatePrivateInsurance(employeePrivateInsurance.id)">Simpan Perubahan</button> -->
                            <button type="button" :data-kt-indicator="employeePrivateInsurance.loading ? 'on' : null" class="btn btn-primary btn-sm" :disabled="employeePrivateInsurance.loading" @click="updatePrivateInsurance(employeePrivateInsurance.id)">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Menyimpan...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="separator mt-5 mb-8"></div>
            </div>
        </div>
        <!--end::Card body-->
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
            return axios.delete('/loans/' + id)
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
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    Vue.prototype.moment = moment

    const employeePrivateInsurances = <?php echo Illuminate\Support\Js::from($employee->privateInsurances) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                // statistics,
                loading: false,
                bpjs: {
                    model: {
                        ketenagakerjaanNumber: '<?= $employee->bpjs->ketenagakerjaan_number ?? "" ?>',
                        ketenagakerjaanStartYear: '<?= $employee->bpjs->ketenagakerjaan_start_year ?? "" ?>',
                        ketenagakerjaanImage: '<?= $employee->bpjs->ketenagakerjaan_card_image ?? null ?>',
                        mandiriNumber: '<?= $employee->bpjs->mandiri_number ?? "" ?>',
                        mandiriImage: '<?= $employee->bpjs->mandiri_card_image ?? null ?>',
                    },
                    loading: false,
                },
                privateInsurance: {
                    model: {
                        image: null,
                        id: '',
                        number: '',
                        startYear: '',
                    },
                    loading: false,
                },
                employeePrivateInsurances: employeePrivateInsurances.map(insurance => {
                    insurance.new_number = insurance.pivot.number;
                    insurance.new_start_year = insurance.pivot.start_year;
                    insurance.new_card_image = insurance.pivot.card_image;
                    insurance.loading = false;
                    return insurance;
                }),
            }
        },
        methods: {
            openBpjsDeleteConfirm(type = '') {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'BPJS ' + type + ' akan dihapus',
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
                        return this.deleteBpjs(type);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                });
            },
            async deleteBpjs(type = '') {
                const self = this;

                const {
                    ketenagakerjaanNumber,
                    ketenagakerjaanStartYear,
                    ketenagakerjaanImage,
                    mandiriNumber,
                    mandiriImage,
                } = self.bpjs.model;

                let requestBody = {};

                if (type == 'ketenagakerjaan') {
                    requestBody = {
                        ketenagakerjaan_number: null,
                        ketenagakerjaan_start_year: null,
                        ketenagakerjaan_image: null,
                        mandiri_number: mandiriNumber,
                        mandiri_image: mandiriImage,
                    };
                } else if (type == 'mandiri') {
                    requestBody = {
                        ketenagakerjaan_number: ketenagakerjaanNumber,
                        ketenagakerjaan_start_year: ketenagakerjaanStartYear,
                        ketenagakerjaan_image: ketenagakerjaanImage,
                        mandiri_number: null,
                        mandiri_image: null,
                    };
                }

                // const formData = new FormData();

                // for (const property in requestBody) {
                //     formData.append(property, requestBody[property]);
                // }

                return axios.post('/employees/{{ $employee->id }}/update-bpjs', requestBody)
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
            },
            async updateBpjs() {
                let self = this;
                try {
                    const {
                        ketenagakerjaanNumber,
                        ketenagakerjaanStartYear,
                        ketenagakerjaanImage,
                        mandiriNumber,
                        mandiriImage,
                    } = self.bpjs.model;

                    const requestBody = {
                        ketenagakerjaan_number: ketenagakerjaanNumber,
                        ketenagakerjaan_start_year: ketenagakerjaanStartYear,
                        ketenagakerjaan_image: ketenagakerjaanImage,
                        mandiri_number: mandiriNumber,
                        mandiri_image: mandiriImage,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    self.bpjs.loading = true;

                    const response = await axios.post('/employees/{{ $employee->id }}/update-bpjs', formData);

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
                    self.bpjs.loading = false;
                }
            },
            async addPrivateInsurance() {
                let self = this;
                try {
                    const {
                        image,
                        id,
                        number,
                        startYear,
                    } = self.privateInsurance.model;

                    const requestBody = {
                        image,
                        private_insurance_id: id,
                        number,
                        start_year: startYear,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    self.privateInsurance.loading = true;

                    const response = await axios.post('/employees/{{ $employee->id }}/create-private-insurance', formData);

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
                    self.privateInsurance.loading = false;
                }
            },
            async updatePrivateInsurance(id) {
                let self = this;
                const [insurance] = self.employeePrivateInsurances.filter(insurance => insurance.id == id);
                try {
                    if (!insurance) {
                        throw new Error('FATAL ERROR: Asuransi tidak ada')
                    }

                    insurance.loading = true;

                    const requestBody = {
                        private_insurance_id: insurance.id,
                        number: insurance.new_number,
                        start_year: insurance.new_start_year,
                        image: insurance.new_card_image,
                    };

                    const formData = new FormData();

                    for (const property in requestBody) {
                        formData.append(property, requestBody[property]);
                    }

                    const response = await axios.post('/employees/{{ $employee->id }}/update-private-insurance', formData);

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
                    insurance.loading = false;
                }
            },
            handleFileChange: function(ref) {
                const imageRef = this.$refs[ref];
                const file = imageRef.files[0];
                if (file) {
                    if (ref == 'ketenagakerjaanCardImage') {
                        this.bpjs.model.ketenagakerjaanImage = file;
                    } else if (ref == 'mandiriCardImage') {
                        this.bpjs.model.mandiriImage = file;
                    } else if (ref == 'privateInsuranceImage') {
                        this.privateInsurance.model.image = file;
                    }
                }
            },
            removeFile: function(ref) {
                if (ref == 'ketenagakerjaanCardImage') {
                    this.bpjs.model.ketenagakerjaanImage = null;
                } else if (ref == 'mandiriCardImage') {
                    this.bpjs.model.mandiriImage = null;
                }
            },
            openPrivateInsuranceDeleteConfirm(id) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Semua data asuransi untuk pegawai ini akan terhapus",
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
                        return this.sendDeletePrivateInsuranceRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDeletePrivateInsuranceRequest(id) {
                const self = this;
                return axios.delete('/employees/{{ $employee->id }}/delete-private-insurance?private_insurance_id=' + id)
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
            },
            nullSafe(key) {
                if (typeof key !== 'undefined') {
                    return key;
                }

                return 0;
            },
            currencyFormat(number) {
                return new Intl.NumberFormat('De-de').format(number);
            },
            handlePrivateInsuranceImage: function(event, index) {
                const file = event.target.files[0];
                if (file) {
                    this.employeePrivateInsurances[index].new_card_image = file;
                    // const previewImageObjectUrl = URL.createObjectURL(file);
                    // this.employeePrivateInsurances[index].previewImage = previewImageObjectUrl;
                }
            },
            removePrivateInsuranceImage: function(index) {
                if (typeof index !== "undefined") {
                    this.employeePrivateInsurances[index].new_card_image = null;
                    // this.model.insurances[index].previewImage = '';
                }
            },

        }
    })
</script>
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