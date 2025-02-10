@extends('layouts.app')

@section('title', 'Perusahaan')

@section('prehead')
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
    <!-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_4">Perusahaan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Link 2</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_6">Link 3</a>
        </li>
    </ul> -->
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0">Daftar Perusahaan</h1>
            <p class="text-muted mb-0">Daftar semua perusahaan</p>
        </div>
        <div class="d-flex">
            @can('create', App\Models\Company::class)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_company"><i class="bi bi-plus-lg"></i> Tambah Perusahaan</button>
            @endcan
        </div>
    </div>
    <div class="separator my-5" style="border-bottom-width: 3px;"></div>
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control w-300px ps-15" placeholder="Cari Perusahaan" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body ">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6" id="kt_customers_table">
                <!--begin::Table head-->
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <!-- <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_customers_table .form-check-input" value="1" />
                            </div>
                        </th> -->
                        <th class="min-w-125px ps-2">Nama</th>
                        <th class="min-w-125px">ID</th>
                        <th class="min-w-125px">Alamat</th>
                        <th class="min-w-125px">Komisaris Utama</th>
                        <th class="min-w-125px">Komisaris</th>
                        <th class="min-w-125px">Direktur Utama</th>
                        <th class="min-w-125px">Direktur</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="text-gray-600">
                    <tr v-for="(company, index) in companies" :key="company.id">
                        <!--begin::Checkbox-->
                        <!-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" />
                            </div>
                        </td> -->
                        <!--end::Checkbox-->
                        <!--begin::Name=-->
                        <td class="ps-2">
                            <span class="text-gray-800 fw-bold" v-cloak>@{{ company.name }}</span>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Email=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.initial }}</span>
                        </td>
                        <!--end::Email=-->
                        <!--begin::Company=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.address }}</span>
                        </td>
                        <!--end::Company=-->
                        <!--begin::Company=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.main_commissioner?.name }}</span>
                        </td>
                        <!--end::Company=-->
                        <!--begin::Company=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.commisioner?.name }}</span>
                        </td>
                        <!--end::Company=-->
                        <!--begin::Company=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.president_director?.name }}</span>
                        </td>
                        <!--end::Company=-->
                        <!--begin::Company=-->
                        <td>
                            <span class="text-gray-800" v-cloak>@{{ company.director?.name }}</span>
                        </td>
                        <!--end::Company=-->
                        <!--begin::Action=-->
                        <td class="text-end pe-2">
                            <div class="d-flex justify-content-end">
                                <!--begin::Share link-->

                                <button type="button" class="btn btn-sm btn-icon btn-light-info ms-2" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_company" @click="openCompanyEditModal(company.id, index)">
                                    <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </button>

                                <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2" @click="openCompanyDeleteConfirmation(company.id)">
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
                                @can('delete', App\Models\Company::class)
                                @endcan
                            </div>
                        </td>
                        <!--end::Action=-->
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
    <!--begin::Modals-->
    <!--begin::Modal - Company - Add-->
    <div class="modal fade" id="kt_modal_add_company" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="kt_modal_add_company_form" data-kt-redirect="../../demo1/dist/apps/customers/list.html">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_add_company_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Tambah Perusahaan</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="kt_modal_add_company_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-10 px-lg-17">
                        <!--begin::Scroll-->
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_company_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_company_header" data-kt-scroll-wrappers="#kt_modal_add_company_scroll" data-kt-scroll-offset="300px">
                            <h3 class="text-gray-600 fw-normal">General</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required fs-6 fw-bold mb-2">Nama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.name" class="form-control form-control-sm" placeholder="Masukkan nama" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="required">ID</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.initial" class="form-control form-control-sm" placeholder="Masukkan ID (Contoh: MM, CC, OL)" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Telepon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.phone" class="form-control form-control-sm" placeholder="Masukkan telepon" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Email</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.email" class="form-control form-control-sm" placeholder="Masukkan email" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-12">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Jenis</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <div class="d-flex">
                                        <div class="form-check form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="radio" v-model="companyModel.add.type" value="retail" id="radioAddCompanyTypeRetail" />
                                            <label class="form-check-label" for="radioAddCompanyTypeRetail">
                                                Retail
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" v-model="companyModel.add.type" value="non_retail" id="radioAddCompanyTypeNonRetail" />
                                            <label class="form-check-label" for="radioAddCompanyTypeNonRetail">
                                                Non Retail
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-primary mb-10">
                                <span>Perusahaan <strong>retail</strong> tidak memotong cuti dari cuti bersama</span>
                            </div>

                            <div class="row mb-10">
                                <!--begin::Input group-->
                                <div class="col-md-12">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Alamat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <!-- <input type="text" class="form-control form-control-solid" placeholder="" name="description" /> -->
                                    <!--end::Input-->
                                    <textarea v-model="companyModel.add.address" class="form-control form-control-sm" cols="4" placeholder="Masukkan alamat"></textarea>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <div class="separator mb-10"></div>

                            <h3 class="text-gray-600 fw-normal">NPWP</h3>
                            <div class="mb-10">

                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">NPWP</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="companyModel.add.npwp.number" class="form-control form-control-sm" placeholder="Masukkan NPWP" />
                                <!--end::Input-->

                            </div>
                            <div class="mb-10">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">
                                    <span>Alamat NPWP</span>
                                    <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea type="text" v-model="companyModel.add.npwp.address" class="form-control form-control-sm" placeholder="Masukkan alamat NPWP"></textarea>
                                <!--end::Input-->
                            </div>
                            <div class="separator mb-10"></div>
                            <h3 class="text-gray-600 fw-normal">Direksi</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Komisaris Utama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="main-commisioner-select-add-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Komisaris</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="commisioner-select-add-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Direktur Utama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="president-director-select-add-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span>Direktur</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="director-select-add-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="separator mb-10"></div>
                            <h3 class="text-gray-600 fw-normal">BCA Bisnis</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Corporate ID</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.corporateId" class="form-control form-control-sm" placeholder="Masukkan Corporate ID" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Company Code</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.companyCode" class="form-control form-control-sm" placeholder="Masukkan Company Code" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Business Type</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select v-model="companyModel.add.businessType" class="form-select form-select-sm">
                                        <option value="">Pilih Business Type</option>
                                        @foreach($company_business_types as $company_business_type)
                                        <option value="{{ $company_business_type->id }}">{{ $company_business_type->name }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Debited Account</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.add.debitedAccount" class="form-control form-control-sm">
                                    <!--end::Input-->
                                </div>
                            </div>
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="kt_modal_add_company_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="kt_modal_add_company_submit" :data-kt-indicator="submitCompanyLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitCompany">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - Company - Add-->
    <!--begin::Modal - Company - Edit-->
    <div class="modal fade" id="kt_modal_edit_company" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Form-->
                <form class="form" action="#" id="kt_modal_edit_company_form" data-kt-redirect="../../demo1/dist/apps/customers/list.html">
                    <!--begin::Modal header-->
                    <div class="modal-header" id="kt_modal_edit_company_header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bolder">Ubah Perusahaan</h2>
                        <!--end::Modal title-->
                        <!--begin::Close-->
                        <div id="kt_modal_edit_company_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body py-10 px-lg-17">
                        <!--begin::Scroll-->
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_add_company_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_company_header" data-kt-scroll-wrappers="#kt_modal_add_company_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="required fs-6 fw-bold mb-2">Nama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.name" class="form-control form-control-sm" placeholder="Masukkan nama" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="required">ID</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.initial" class="form-control form-control-sm" placeholder="Masukkan ID (Contoh: MM, CC, OL)" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Telepon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.phone" class="form-control form-control-sm" placeholder="Masukkan telepon" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Email</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.email" class="form-control form-control-sm" placeholder="Masukkan email" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-12">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Jenis</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <div class="d-flex">
                                        <div class="form-check form-check-custom form-check-solid me-3">
                                            <input class="form-check-input" type="radio" v-model="companyModel.edit.type" value="retail" id="radioEditCompanyTypeRetail" />
                                            <label class="form-check-label" for="radioEditCompanyTypeRetail">
                                                Retail
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" v-model="companyModel.edit.type" value="non_retail" id="radioEditCompanyTypeNonRetail" />
                                            <label class="form-check-label" for="radioEditCompanyTypeNonRetail">
                                                Non Retail
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-primary mb-10">
                                <span>Perusahaan <strong>retail</strong> tidak memotong cuti dari cuti bersama</span>
                            </div>
                            <div class="row mb-10">
                                <!--begin::Input group-->
                                <div class="col-md-12">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Alamat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <!-- <input type="text" class="form-control form-control-solid" placeholder="" name="description" /> -->
                                    <!--end::Input-->
                                    <textarea v-model="companyModel.edit.address" class="form-control form-control-sm" cols="4" placeholder="Masukkan alamat"></textarea>
                                </div>
                                <!--end::Input group-->
                            </div>
                            <div class="separator mb-10"></div>

                            <h3 class="text-gray-600 fw-normal">NPWP</h3>
                            <div class="mb-10">

                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">NPWP</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" v-model="companyModel.edit.npwp.number" class="form-control form-control-sm" placeholder="Masukkan NPWP" />
                                <!--end::Input-->

                            </div>
                            <div class="mb-10">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold mb-2">
                                    <span>Alamat NPWP</span>
                                    <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea type="text" v-model="companyModel.edit.npwp.address" class="form-control form-control-sm" placeholder="Masukkan alamat NPWP"></textarea>
                                <!--end::Input-->
                            </div>
                            <div class="separator mb-10"></div>
                            <h3 class="text-gray-600 fw-normal">Direksi</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Komisaris Utama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="main-commisioner-select-edit-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Komisaris</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="commisioner-select-edit-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Direktur Utama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="president-director-select-edit-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span>Direktur</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="director-select-edit-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="separator mb-10"></div>
                            <h3 class="text-gray-600 fw-normal">BCA Bisnis</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Corporate ID</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.corporateId" class="form-control form-control-sm" placeholder="Masukkan Corporate ID" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span class="">Company Code</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.companyCode" class="form-control form-control-sm" placeholder="Masukkan Company Code" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Business Type</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select v-model="companyModel.edit.businessType" class="form-select form-select-sm">
                                        <option value="">Pilih Business Type</option>
                                        @foreach($company_business_types as $company_business_type)
                                        <option value="{{ $company_business_type->id }}">{{ $company_business_type->name }}</option>
                                        @endforeach
                                    </select>
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class=" fs-6 fw-bold mb-2">Debited Account</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.debitedAccount" class="form-control form-control-sm">
                                    <!--end::Input-->
                                </div>
                            </div>
                        </div>
                        <!--end::Scroll-->
                    </div>
                    <!--end::Modal body-->
                    <!--begin::Modal footer-->
                    <div class="modal-footer flex-center">
                        <!--begin::Button-->
                        <button type="reset" id="kt_modal_edit_company_cancel" class="btn btn-light me-3">Batal</button>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="button" id="kt_modal_edit_company_submit" :data-kt-indicator="submitEditCompanyLoading ? 'on' : null" class="btn btn-primary" @click="onSubmitEditCompany">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                    <!--end::Modal footer-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
    <!--end::Modal - Company - Edit-->
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


        datatable = $('#kt_customers_table').DataTable({
            "columnDefs": [{
                "targets": 3,
                // "searchable": false
                "className": "text-end",
            }],
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    })
</script>
<script>
    const redrawDatatable = () => {
        datatable.draw();
    }

    const closeCompanyAddModal = () => {
        const addCompanyModal = document.querySelector('#kt_modal_add_company');
        const modal = bootstrap.Modal.getInstance(addCompanyModal);
        modal.hide();
    }

    const closeCompanyEditModal = () => {
        const addCompanyModal = document.querySelector('#kt_modal_edit_company');
        const modal = bootstrap.Modal.getInstance(addCompanyModal);
        modal.hide();
    }

    // var companies = @json('$companies');
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies: companies,
                companyModel: {
                    add: {
                        name: '',
                        initial: '',
                        address: '',
                        phone: '',
                        email: '',
                        corporateId: '',
                        companyCode: '',
                        businessType: '',
                        debitedAccount: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            mainCommissioner: '',
                            commisioner: '',
                            presidentDirector: '',
                            director: '',
                        },
                        type: 'non_retail',
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        initial: '',
                        address: '',
                        phone: '',
                        email: '',
                        corporateId: '',
                        companyCode: '',
                        businessType: '',
                        debitedAccount: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            mainCommissioner: '',
                            commisioner: '',
                            presidentDirector: '',
                            director: '',
                        },
                        type: ''
                    }
                },
                submitCompanyLoading: false,
                submitEditCompanyLoading: false,
            }
        },
        methods: {
            // COMPANY METHODS
            async onSubmitCompany() {
                let self = this;
                try {
                    const {
                        name,
                        initial,
                        phone,
                        email,
                        address,
                        type,
                        npwp,
                        commissioner,
                        corporateId,
                        companyCode,
                        businessType,
                        debitedAccount,
                    } = self.companyModel.add;

                    self.submitCompanyLoading = true;

                    const response = await axios.post('/companies', {
                        name,
                        initial,
                        phone,
                        email,
                        type,
                        address,
                        npwp_number: npwp.number,
                        npwp_address: npwp.address,
                        main_commissioner: commissioner.mainCommissioner,
                        commisioner: commissioner.commisioner,
                        president_director: commissioner.presidentDirector,
                        director: commissioner.director,
                        corporate_id: corporateId,
                        company_code: companyCode,
                        business_type: businessType,
                        debited_account: debitedAccount,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        // const rowData = [
                        //     textFormatter(name),
                        //     textFormatter(initial),
                        //     textFormatter(address),
                        //     actionButton(data?.id)
                        // ]

                        // addTableRow(rowData)
                        this.addCompany(data);
                        // redrawDatatable();
                        closeCompanyAddModal();
                        this.resetCompanyForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitCompanyLoading = false;
                }
            },
            addCompany(data) {
                if (data) {
                    this.companies.push(data)
                }
            },
            openCompanyDeleteConfirmation(id) {
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
                        return self.sendCompanyDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendCompanyDeleteRequest(id) {
                const self = this;
                return axios.delete('/companies/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteCompany(id);
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
            deleteCompany(id) {
                this.companies = this.companies.filter(company => company.id !== id);
            },
            openCompanyEditModal(id, index) {
                // console.log(this.$refs);
                const [company] = this.companies.filter(company => company.id === id);
                if (company) {
                    // console.log(company)
                    this.companyModel.edit.index = index
                    this.companyModel.edit.id = company.id
                    this.companyModel.edit.name = company.name
                    this.companyModel.edit.initial = company.initial
                    this.companyModel.edit.phone = company.phone
                    this.companyModel.edit.email = company.email
                    this.companyModel.edit.address = company.address
                    this.companyModel.edit.type = company.type
                    // corporateId,
                    //     companyCode,
                    //     businessType,
                    this.companyModel.edit.corporateId = company.corporate_id
                    this.companyModel.edit.companyCode = company.company_code
                    this.companyModel.edit.businessType = company.business_type
                    this.companyModel.edit.debitedAccount = company.debited_account
                    this.companyModel.edit.npwp.number = company.npwp_number
                    this.companyModel.edit.npwp.address = company.npwp_address
                    //-------
                    this.companyModel.edit.commissioner.mainCommissioner = company.main_commissioner;
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    if (company.main_commissioner?.id) {
                        $('#main-commisioner-select-edit-form').val(company.main_commissioner?.id).trigger('change');
                    }
                    //-------
                    this.companyModel.edit.commissioner.commisioner = company.commisioner;
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    if (company.commisioner?.id) {
                        $('#commisioner-select-edit-form').val(company.commisioner?.id).trigger('change');
                    }
                    //--------
                    //-------
                    this.companyModel.edit.commissioner.presidentDirector = company.president_director
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    // const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    if (company.president_director?.id) {
                        $('#president-director-select-edit-form').val(company.president_director?.id).trigger('change');
                    }
                    //--------
                    this.companyModel.edit.commissioner.director = company.director
                    // const directorEl = this.$refs['director_select_edit_form'];
                    if (company.director?.id) {
                        $('#director-select-edit-form').val(company.director?.id).trigger('change');
                    }
                }
            },
            async onSubmitEditCompany() {
                let self = this;
                try {
                    const {
                        index,
                        id,
                        name,
                        initial,
                        phone,
                        email,
                        address,
                        type,
                        npwp,
                        commissioner,
                        corporateId,
                        companyCode,
                        businessType,
                        debitedAccount,
                    } = self.companyModel.edit;

                    self.submitEditCompanyLoading = true;

                    const response = await axios.post(`/companies/${id}`, {
                        name,
                        initial,
                        phone,
                        email,
                        address,
                        type,
                        npwp_number: npwp.number,
                        npwp_address: npwp.address,
                        main_commissioner: commissioner.mainCommissioner,
                        commisioner: commissioner.commisioner,
                        president_director: commissioner.presidentDirector,
                        director: commissioner.director,
                        corporate_id: corporateId,
                        company_code: companyCode,
                        business_type: businessType,
                        debited_account: debitedAccount,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        // const rowData = [
                        //     textFormatter(name),
                        //     textFormatter(initial),
                        //     textFormatter(address),
                        //     actionButton(data?.id)
                        // ]

                        // addTableRow(rowData)
                        // this.addCompany(data);
                        this.editCompany(index, data);
                        // redrawDatatable();
                        closeCompanyEditModal();
                        this.resetCompanyForm();
                        toastr.success(message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.submitEditCompanyLoading = false;
                }
            },
            editCompany(index, data) {
                this.companies.splice(index, 1, data);
            },
            resetCompanyForm() {
                this.companyModel = {
                    add: {
                        name: '',
                        initial: '',
                        phone: '',
                        email: '',
                        address: '',
                        type: 'non_retail',
                        corporateId: '',
                        companyCode: '',
                        businessType: '',
                        debitedAccount: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            mainCommissioner: '',
                            commisioner: '',
                            presidentDirector: '',
                            director: '',
                        },
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        initial: '',
                        phone: '',
                        email: '',
                        address: '',
                        type: '',
                        corporateId: '',
                        companyCode: '',
                        businessType: '',
                        debitedAccount: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            mainCommissioner: '',
                            commisioner: '',
                            presidentDirector: '',
                            director: '',
                        },
                    }
                }
            }
        },
    })
</script>
<script>
    let select2Data = [];
    if (Array.isArray(employees)) {
        const newOptions = employees.map(employee => ({
            id: employee.id || '',
            text: employee.name || '',
            jobTitle: employee?.active_career?.job_title?.name || '',
            company: employee?.office?.division?.company?.name || '',
            division: employee?.office?.division?.name || '',
            office: employee?.office?.name || '',
            selected: false,
            // selected: employee.id == timeOffEmployeeId ? true : false,
        }));

        select2Data = [...select2Data, ...newOptions];
    }

    $(function() {

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }

            var $state = $(
                `
                <div class="">
                    <div>
                        <span class="fw-bold">${state.text}</span><em>${state.jobTitle && ' - ' + state.jobTitle}</em>
                    </div>
                    <div>
                        <small>${ state.office } - ${ state.division } - ${state.company}</small>
                    </div>
                </div>
                `
            );
            return $state;
        };


        $('#main-commisioner-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_add_company'),
        });

        $('#main-commisioner-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.mainCommissioner = value;
        });

        $('#commisioner-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_add_company'),
        });

        $('#commisioner-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.commisioner = value;
        });

        $('#president-director-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_add_company')
        });

        $('#president-director-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.presidentDirector = value;
        });

        $('#director-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_add_company')
        });

        $('#director-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.director = value;
        });

        // EDIT
        $('#main-commisioner-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_edit_company'),
            allowClear: true,
            placeholder: 'Pilih pegawai',
        });

        $('#main-commisioner-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.mainCommissioner = value;
        });

        $('#commisioner-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_edit_company'),
            allowClear: true,
            placeholder: 'Pilih pegawai',
        });

        $('#commisioner-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.commisioner = value;
        });

        $('#president-director-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_edit_company'),
            allowClear: true,
            placeholder: 'Pilih pegawai',
        });

        $('#president-director-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.presidentDirector = value;
        });

        $('#director-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
            dropdownParent: $('#kt_modal_edit_company'),
            allowClear: true,
            placeholder: 'Pilih pegawai',
        });

        $('#director-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.director = value;
        });
    })
</script>
@endsection