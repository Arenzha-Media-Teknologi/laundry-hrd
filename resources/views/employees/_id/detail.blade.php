@extends('layouts.app')

@section('title', $employee->name . ' - Detail')

@section('head')

@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <x-employee-detail-card :employee="$employee" />
    @if($completion['incomplete_count'] > 0)
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Notice-->
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-12">
                <!--begin::Icon-->
                <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                        <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                        <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <!--end::Icon-->
                <!--begin::Wrapper-->
                <div class="d-flex flex-stack flex-grow-1">
                    <!--begin::Content-->
                    <div class="fw-bold">
                        <h4 class="text-gray-900 fw-bolder">Lengkapi kelengkapan pegawai!</h4>
                        <div class="fs-6 text-gray-700">Kelengkapan pegawai akan berpengaruh terhadap fitur yang dapat digunakan
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Notice-->
            <!-- begin:Row -->
            <div class="row justify-content-between">
                <!--begin::Col-->
                <div class="col-lg-6">
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-gray-300 border-dashed rounded p-4">
                        <div>
                            <span class="fw-bolder fs-5">Karir</span>
                            @if($completion['items']['career'] == false)
                            <a href="/employees/{{ $employee->id }}/careers">Atur</a>
                            @endif
                        </div>
                        <div>
                            <!--begin::Svg Icon | path: assets/media/icons/duotune/general/gen043.svg-->
                            <span class="svg-icon {{ $completion['items']['career'] == true ? 'svg-icon-success' : 'svg-icon-muted' }} svg-icon-2hx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="black" />
                                </svg></span>
                            <!--end::Svg Icon-->
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-gray-300 border-dashed rounded p-4">
                        <div>
                            <span class="fw-bolder fs-5">Pengaturan Gaji</span>
                            @if($completion['items']['salary'] == false)
                            <a href="/settings/payrolls/members">Atur</a>
                            @endif
                        </div>
                        <div>
                            <!--begin::Svg Icon | path: assets/media/icons/duotune/general/gen043.svg-->
                            <span class="svg-icon {{ $completion['items']['salary'] == true ? 'svg-icon-success' : 'svg-icon-muted' }} svg-icon-2hx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="black" />
                                </svg></span>
                            <!--end::Svg Icon-->
                        </div>
                    </div>
                    <!--end::Completion Item-->
                    <!--begin::Completion Item-->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-gray-300 border-dashed rounded p-4">
                        <div>
                            <span class="fw-bolder fs-5">Pola Kerja</span>
                            @if($completion['items']['working_pattern'] == false)
                            <a href="/employees/{{ $employee->id }}/setting#working-pattern">Atur</a>
                            @endif
                        </div>
                        <div>
                            <!--begin::Svg Icon | path: assets/media/icons/duotune/general/gen043.svg-->
                            <span class="svg-icon {{ $completion['items']['working_pattern'] == true ? 'svg-icon-success' : 'svg-icon-muted' }} svg-icon-2hx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                    <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z" fill="black" />
                                </svg></span>
                            <!--end::Svg Icon-->
                        </div>
                    </div>
                    <!--end::Completion Item-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-lg-5">
                    <!--begin::Heading-->
                    <div class="d-flex text-muted fw-bolder fs-5 mb-3">
                        <span class="flex-grow-1 text-gray-800">Kelengkapan Pegawai</span>
                        <span class="text-gray-800">{{ $completion['completed_count'] }} dari {{ $completion['items_count'] }} selesai</span>
                    </div>
                    <!--end::Heading-->
                    <!--begin::Progress-->
                    <div class="progress h-8px bg-light-primary mb-2">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $completion['percentage'] ?>%" aria-valuenow="{{ $completion['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <!--end::Progress-->
                    <!--begin::Description-->
                    <div class="fs-6 text-gray-600 fw-bold mb-10">{{ $completion['incomplete_count'] }} item belum selesai</div>
                    <!--end::Description-->
                </div>
                <!--end::Col-->
            </div>
            <!-- end:Row -->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Profil Completion-->
    @endif
    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Detail Profil</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <a href="/employees/{{ $employee->id }}/edit" class="btn btn-light-primary align-self-center">Ubah Profil</a>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Nama</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->name }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Jenis Kelamin</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">{{ $employee->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Identitas Diri</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6 text-uppercase">{{ $employee->identity_type }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">No. Identitas</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">{{ $employee->identity_number }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Tanggal Akhir Berlaku Identitas</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">-</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Tempat & Tanggal Lahir</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">{{ $employee->place_of_birth }}, {{ $employee->date_of_birth }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Status Perkawinan</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">{{ $employee->marital_status }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Agama</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6">{{ $employee->religion }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Golongan Darah</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6 text-uppercase">{{ $employee->blood_group }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Pendidikan Terakhir</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6 text-uppercase">{{ $employee->recent_education }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Nama Institusi Pendidikan</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6 text-capitalize">{{ $employee->education_institution_name }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Jurusan / Program Studi</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8 fv-row">
                    <span class="fw-bold text-gray-800 fs-6 text-capitalize">{{ $employee->study_program }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <div class="separator mb-5"></div>
            <h4 class="mb-8">Kontak</h4>
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Email</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->email }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">No. HP</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->phone }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Alamat</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->address }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Nama Kontak Darurat</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->emergency_contact_name }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Hubungan Kontak Darurat</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->emergency_contact_relation }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
            <!--begin::Row-->
            <div class="row mb-7">
                <!--begin::Label-->
                <label class="col-lg-4 fw-bold text-muted">Nomor Telepon Darurat</label>
                <!--end::Label-->
                <!--begin::Col-->
                <div class="col-lg-8">
                    <span class="fw-bolder fs-6 text-gray-800">{{ $employee->emergency_contact_phone }}</span>
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::details View-->
    <!--begin::contact View-->
    <div class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Informasi NPWP</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <button class="btn btn-light-primary align-self-center" type="button" data-bs-toggle="collapse" data-bs-target=".npwpCollapse" aria-expanded="false" aria-controls="npwpCollapse">Ubah NPWP</button>
            <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9">
            <!--begin::Row-->
            <div class="collapse npwpCollapse">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">NPWP</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="npwp.model.number" class="form-control" placeholder="Masukkan NPWP" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Tanggal Mulai NPWP</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="date" v-model="npwp.model.effectiveDate" class="form-control" placeholder="Masukkan tanggal mulai NPWP" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Status Wajib Pajak</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select v-model="npwp.model.status" class="form-select">
                                <option value="">Pilih Status Wajib Pajak</option>
                                @for($i = 0; $i < 4; $i++) <option value="k{{$i}}">K{{ $i }}</option> @endfor
                                    @for($i = 0; $i < 4; $i++) <option value="tk{{$i}}">TK{{ $i }}</option> @endfor
                                        @for($i = 0; $i < 4; $i++) <option value="hb{{$i}}">HB{{ $i }}</option> @endfor
                            </select>
                            <!--end::Input-->
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" :data-kt-indicator="npwp.loading ? 'on' : null" :disabled="npwp.loading" @click="updateNpwp">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="collapse npwpCollapse show">
                <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted">NPWP</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                        <span class="fw-bolder fs-6 text-gray-800">@{{ npwp.model.number }}</span>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted">Mulai Sejak</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                        <span class="fw-bolder fs-6 text-gray-800">@{{ npwp.model.effectiveDate }}</span>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted">Status Wajib Pajak</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                        <span class="fw-bolder fs-6 text-gray-800 text-uppercase">@{{ npwp.model.status }}</span>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::contact View-->
    <div class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header card-header-stretch pb-0">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="m-0">Rekening Bank</h3>
            </div>
            <!--end::Title-->
        </div>
        <!--end::Card header-->
        <!--begin::Tab content-->
        <div class="card-body">
            <!--begin::Card body content-->
            <div>
                <!--begin::Row-->
                <div class="row gx-9 gy-6">
                    <!--begin::Col-->
                    <div v-for="(bankAccount, index) in bankAccount.data" class="col-xl-6">
                        <!--begin::Card-->
                        <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                            <!--begin::Info-->
                            <div class="d-flex flex-column py-2">
                                <!--begin::Owner-->
                                <div class="d-flex align-items-center fs-4 fw-bolder mb-5">@{{ bankAccount.account_owner }}
                                    <span v-if="bankAccount.default" class="badge badge-light-success fs-7 ms-2">Utama</span>
                                </div>
                                <!--end::Owner-->
                                <!--begin::Wrapper-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <!-- <img src="assets/media/svg/card-logos/visa.svg" alt="" class="me-4" /> -->
                                    <!--end::Icon-->
                                    <!--begin::Details-->
                                    <div>
                                        <div class="fs-4 fw-bolder text-gray-600"><span class="fs-4 fw-bolder text-gray-800">@{{ bankAccount.bank_name }}</span> - @{{ bankAccount.account_number }}</div>
                                        <div class="fs-4 fw-bold text-gray-400">@{{ bankAccount.bank_branch }}</div>
                                    </div>
                                    <!--end::Details-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Info-->
                            <!--begin::Actions-->
                            <div class="d-flex align-items-center py-2">
                                <button type="button" class="btn btn-sm btn-light-danger me-3" @click="openBankAccountDeleteConfirmation(bankAccount.id)">Hapus</button>
                                <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#bank_account_edit_modal" @click="onOpenEditModal(bankAccount.id, index)">Ubah</button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-xl-6">
                        <!--begin::Notice-->
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed h-lg-100 p-6">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                <!--begin::Content-->
                                <div class="mb-3 mb-md-0 fw-bold">
                                    <h4 class="text-gray-900 fw-bolder">Note</h4>
                                    <div class="fs-6 text-gray-700 pe-7">Rekening bank dengan label <em>"Utama"</em> adalah rekening utama
                                    </div>
                                </div>
                                <!--end::Content-->
                                <!--begin::Action-->
                                <button type="button" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#bank_account_modal">Tambah Rekening</button>
                                <!--end::Action-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Notice-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Card body content-->
        </div>
        <!--end::Tab content-->
    </div>
    <div class="modal fade" tabindex="-1" id="bank_account_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rekening</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nama Bank</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.add.bankName" class="form-control" placeholder="Masukkan nama bank" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nomor Rekening</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.add.accountNumber" class="form-control" placeholder="Masukkan nomor rekening" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nama Pemilik Rekening</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.add.accountOwner" class="form-control" placeholder="Masukkan nama pemilik rekening" />
                            <!--end::Input-->
                        </div>
                        <!-- <div class="separator"></div> -->
                        <div class="fv-row">
                            <!--begin::Label-->
                            <!-- <label class="fs-6 fw-bold mb-2">Default</label> -->
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" v-model="bankAccount.model.add.default" id="addDefaultCheck" />
                                <label class="form-check-label" for="addDefaultCheck">
                                    <span class="fs-6 fw-bold">Tambah sebagai rekening utama</span>
                                </label>
                            </div>
                            <!--end::Input-->
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" :data-kt-indicator="bankAccount.loading ? 'on' : null" :disabled="bankAccount.loading" @click="addBankAccount">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="bank_account_edit_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Rekening</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nama Bank</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.edit.bankName" class="form-control" placeholder="Masukkan nama bank" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nomor Rekening</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.edit.accountNumber" class="form-control" placeholder="Masukkan nomor rekening" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Nama Pemilik Rekening</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="bankAccount.model.edit.accountOwner" class="form-control" placeholder="Masukkan nama pemilik rekening" />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row">
                            <!--begin::Label-->
                            <!-- <label class="fs-6 fw-bold mb-2">Default</label> -->
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" v-model="bankAccount.model.edit.default" id="editDefaultCheck" />
                                <label class="form-check-label" for="editDefaultCheck">
                                    <span class="fs-6 fw-bold">Atur sebagai rekening utama</span>
                                </label>
                            </div>
                            <!--end::Input-->
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" :data-kt-indicator="bankAccount.loading ? 'on' : null" :disabled="bankAccount.loading" @click="updateBankAccount">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
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
    })
</script>
<script src="{{ asset('assets/js/addons/employeeActivation.js') }}"></script>
<script>
    const employee = <?php echo Illuminate\Support\Js::from($employee) ?>;
    const bankAccounts = <?php echo Illuminate\Support\Js::from($employee->bankAccounts) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                employee,
                bankAccount: {
                    data: bankAccounts,
                    model: {
                        add: {
                            bankName: '',
                            accountNumber: '',
                            accountOwner: '',
                            default: false,
                        },
                        edit: {
                            bankName: '',
                            accountNumber: '',
                            accountOwner: '',
                            default: false,
                            index: null,
                            id: null,
                        },
                    },
                    loading: false,
                },
                npwp: {
                    model: {
                        number: '{{ $employee->npwp_number }}',
                        effectiveDate: '{{ $employee->npwp_effective_date }}',
                        status: '{{ $employee->npwp_status }}',
                    },
                    loading: false,
                }
            }
        },
        methods: {
            async addBankAccount() {
                const self = this;
                self.bankAccount.loading = true;
                try {
                    const payload = {
                        bank_name: self.bankAccount.model.add.bankName,
                        account_number: self.bankAccount.model.add.accountNumber,
                        account_owner: self.bankAccount.model.add.accountOwner,
                        default: self.bankAccount.model.add.default,
                        employee_id: self.employee?.id,
                    }

                    const response = await axios.post('/bank-accounts', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        self.bankAccount.data = data;

                        closeModal('#bank_account_modal');

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.bankAccount.loading = false;
                }
            },
            async updateBankAccount() {
                const self = this;
                self.bankAccount.loading = true;
                try {
                    const bankAccountId = self.bankAccount.model.edit.id;
                    const bankAccountIndex = self.bankAccount.model.edit.index;

                    const payload = {
                        bank_name: self.bankAccount.model.edit.bankName,
                        account_number: self.bankAccount.model.edit.accountNumber,
                        account_owner: self.bankAccount.model.edit.accountOwner,
                        default: self.bankAccount.model.edit.default,
                        employee_id: self.employee?.id,
                    }

                    const response = await axios.post('/bank-accounts/' + bankAccountId, payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        // self.bankAccount.data.push(data);
                        // self.bankAccount.data[bankAccountIndex] = data;
                        self.bankAccount.data = data;

                        closeModal('#bank_account_edit_modal');

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.bankAccount.loading = false;
                }
            },
            onOpenEditModal(id, index) {
                console.log('opened');
                const self = this;
                const [bankAccount] = this.bankAccount.data.filter(account => account.id == id);
                if (bankAccount) {
                    self.bankAccount.model.edit.bankName = bankAccount.bank_name;
                    self.bankAccount.model.edit.accountNumber = bankAccount.account_number;
                    self.bankAccount.model.edit.accountOwner = bankAccount.account_owner;
                    self.bankAccount.model.edit.default = bankAccount.default == 1 ? true : false;
                    self.bankAccount.model.edit.index = index;
                    self.bankAccount.model.edit.id = id;
                }
            },
            openBankAccountDeleteConfirmation(id) {
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
                        return self.sendBankAccountDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendBankAccountDeleteRequest(id) {
                const self = this;
                return axios.delete('/bank-accounts/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.bankAccount.data = self.bankAccount.data.filter(bankAccount => bankAccount.id !== id);
                        // redrawDatatable();
                        toastr.success(message);
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
            async updateNpwp() {
                const self = this;
                self.npwp.loading = true;
                try {
                    const employeeId = self.employee.id;

                    const payload = {
                        npwp_number: self.npwp.model.number,
                        npwp_effective_date: self.npwp.model.effectiveDate,
                        npwp_status: self.npwp.model.status,
                    }

                    const response = await axios.post('/employees/' + employeeId + '/update-npwp', payload);

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        if (data) {
                            self.npwp.model.number = data?.npwp_number;
                            self.npwp.model.effectiveDate = data?.npwp_effective_date;
                            self.npwp.model.status = data?.npwp_status;
                        }

                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.npwp.loading = false;
                }
            }
        }
    })
</script>
@endsection