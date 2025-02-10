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
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Perusahaan" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                    <!--begin::Filter-->
                    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->Filter
                    </button>
                    <!--begin::Menu 1-->
                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
                        <!--begin::Header-->
                        <div class="px-7 py-5">
                            <div class="fs-4 text-dark fw-bolder">Filter Options</div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Separator-->
                        <div class="separator border-gray-200"></div>
                        <!--end::Separator-->
                        <!--begin::Content-->
                        <div class="px-7 py-5">
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <!--begin::Label-->
                                <label class="form-label fs-5 fw-bold mb-3">Month:</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-customer-table-filter="month" data-dropdown-parent="#kt-toolbar-filter">
                                    <option></option>
                                    <option value="aug">August</option>
                                    <option value="sep">September</option>
                                    <option value="oct">October</option>
                                    <option value="nov">November</option>
                                    <option value="dec">December</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <!--begin::Label-->
                                <label class="form-label fs-5 fw-bold mb-3">Payment Type:</label>
                                <!--end::Label-->
                                <!--begin::Options-->
                                <div class="d-flex flex-column flex-wrap fw-bold" data-kt-customer-table-filter="payment_type">
                                    <!--begin::Option-->
                                    <label class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                        <input class="form-check-input" type="radio" name="payment_type" value="all" checked="checked" />
                                        <span class="form-check-label text-gray-600">All</span>
                                    </label>
                                    <!--end::Option-->
                                    <!--begin::Option-->
                                    <label class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                        <input class="form-check-input" type="radio" name="payment_type" value="visa" />
                                        <span class="form-check-label text-gray-600">Visa</span>
                                    </label>
                                    <!--end::Option-->
                                    <!--begin::Option-->
                                    <label class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                        <input class="form-check-input" type="radio" name="payment_type" value="mastercard" />
                                        <span class="form-check-label text-gray-600">Mastercard</span>
                                    </label>
                                    <!--end::Option-->
                                    <!--begin::Option-->
                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="payment_type" value="american_express" />
                                        <span class="form-check-label text-gray-600">American Express</span>
                                    </label>
                                    <!--end::Option-->
                                </div>
                                <!--end::Options-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true" data-kt-customer-table-filter="reset">Reset</button>
                                <button type="submit" class="btn btn-primary" data-kt-menu-dismiss="true" data-kt-customer-table-filter="filter">Apply</button>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Menu 1-->
                    <!--end::Filter-->
                    <!--begin::Export-->
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_customers_export_modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black" />
                                <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black" />
                                <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->Export
                    </button>
                    <!--end::Export-->
                    <!--begin::Add customer-->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_company">Tambah Perusahaan</button>
                    <!--end::Add customer-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-customer-table-toolbar="selected">
                    <div class="fw-bolder me-5">
                        <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">Delete Selected</button>
                </div>
                <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
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
                        <th class="min-w-125px">Direktur Utama</th>
                        <th class="min-w-125px">Direktur</th>
                        <th class="text-end min-w-70px pe-2">Actions</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
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
                            <span class="text-gray-800" v-cloak>@{{ company.name }}</span>
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
                            <span class="text-gray-800" v-cloak>@{{ company.presidentDirector?.name }}</span>
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
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
                            <h3 class="text-gray-600">General</h3>
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
                                    <input type="text" v-model="companyModel.add.name" class="form-control form-control-sm" placeholder="Masukkan telepon" />
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
                                    <input type="text" v-model="companyModel.add.initial" class="form-control form-control-sm" placeholder="Masukkan email" />
                                    <!--end::Input-->
                                </div>
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

                            <h3 class="text-gray-600">NPWP</h3>
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
                            <h3 class="text-gray-600">Direksi</h3>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Komisaris</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="president-director-select-add-form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
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
        <div class="modal-dialog modal-lg modal-dialog-centered mw-650px">
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
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">Direktur Utama</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select class="form-select form-select-sm" id="president-director-select-edit-form" ref="president_director_select_edit_form">
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
                                    <select class="form-select form-select-sm" id="director-select-edit-form" ref="director_select_edit_form">
                                        <!-- <option value=""></option> -->
                                    </select>
                                    <!--end::Input-->
                                </div>
                            </div>
                            <div class="row mb-10">
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">NPWP</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.npwp.number" class="form-control form-control-sm" placeholder="Masukkan NPWP" />
                                    <!--end::Input-->
                                </div>
                                <div class="col-md-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold mb-2">
                                        <span>Alamat NPWP</span>
                                        <!-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i> -->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" v-model="companyModel.edit.npwp.address" class="form-control form-control-sm" placeholder="Masukkan alamat NPWP" />
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
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
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
                        address: '',
                        phone: '',
                        email: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            commisioner: '',
                            presidentDirector: '',
                            director: '',
                        },
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
                        address,
                        npwp,
                        commissioner,
                    } = self.companyModel.add;

                    self.submitCompanyLoading = true;

                    const response = await axios.post('/companies', {
                        name,
                        initial,
                        address,
                        npwp_number: npwp.number,
                        npwp_address: npwp.address,
                        president_director: commissioner.presidentDirector,
                        director: commissioner.director,
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
                    this.companyModel.edit.address = company.address
                    this.companyModel.edit.npwp.number = company.npwp_number
                    this.companyModel.edit.npwp.address = company.npwp_address
                    //-------
                    this.companyModel.edit.commissioner.presidentDirector = company.president_director
                    const presidentDirectorEl = this.$refs['president_director_select_edit_form'];
                    $(presidentDirectorEl).val(company.president_director).trigger('change');
                    //--------
                    this.companyModel.edit.commissioner.director = company.director
                    const directorEl = this.$refs['director_select_edit_form'];
                    $(directorEl).val(company.director).trigger('change');
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
                        address,
                        npwp,
                        commissioner,
                    } = self.companyModel.edit;

                    self.submitEditCompanyLoading = true;

                    const response = await axios.post(`/companies/${id}`, {
                        name,
                        initial,
                        address,
                        npwp_number: npwp.number,
                        npwp_address: npwp.address,
                        president_director: commissioner.presidentDirector,
                        director: commissioner.director,
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
                        address: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
                            presidentDirector: '',
                            director: '',
                        },
                    },
                    edit: {
                        index: null,
                        id: null,
                        name: '',
                        initial: '',
                        address: '',
                        npwp: {
                            number: '',
                            address: '',
                        },
                        commissioner: {
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


        $('#president-director-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
        });

        $('#president-director-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.presidentDirector = value;
        });

        $('#director-select-add-form').select2({
            data: select2Data,
            templateResult: formatState,
        });

        $('#director-select-add-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.add.commissioner.director = value;
        });

        // EDIT
        $('#president-director-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
        });

        $('#president-director-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.presidentDirector = value;
        });

        $('#director-select-edit-form').select2({
            data: select2Data,
            templateResult: formatState,
        });

        $('#director-select-edit-form').on('change', function(e) {
            const value = $(this).val();
            app.$data.companyModel.edit.commissioner.director = value;
        });
    })
</script>
@endsection