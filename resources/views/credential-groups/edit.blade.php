@extends('layouts.app')

@section('title', 'Ubah Grup')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<!-- <link href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script> -->
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

@section('bodyscript')
<!-- <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script> -->
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h3>Ubah Grup</h3>
            </div>
            <div class="card-toolbar">
                <!-- <button class="btn btn-primary">Simpan</button> -->
                <button type="button" id="kt_modal_edit_company_submit" :data-kt-indicator="loading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress">Mengirim data...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row align-items-center my-10">
                <div class="col-md-2">
                    <label class="required form-label fs-7">Nama:</label>
                </div>
                <div class="col-md-4">
                    <input type="text" v-model="model.name" class="form-control form-control-sm" placeholder="Masukkan nama grup" />
                </div>
            </div>
            <!--begin::Table-->
            <!-- <pre>@{{ model }}</pre> -->
            <table class="table align-middle table-row-bordered table-sm">
                <!--begin::Table head-->
                <?php $colspan = 5 ?>
                <thead class="bg-light-primary">
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0 align-middle">
                        <th class="min-w-100px ps-3" rowspan="2">Nama Akses</th>
                        <th colspan="{{ $colspan - 1 }}" class="text-center">Hak Akses</th>
                    </tr>
                    <tr class="text-start text-gray-700 fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.checkedAll.view" class="form-check-input" type="checkbox" id="checkedAllView" />
                                    <label class="form-check-label" for="checkedAllView">
                                        Lihat
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.checkedAll.add" class="form-check-input" type="checkbox" id="checkedAllAdd" />
                                    <label class="form-check-label" for="checkedAllAdd">
                                        Tambah
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.checkedAll.edit" class="form-check-input" type="checkbox" id="checkedAllEdit" />
                                    <label class="form-check-label" for="checkedAllEdit">
                                        Ubah
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.checkedAll.delete" class="form-check-input" type="checkbox" id="checkedAllDelete" />
                                    <label class="form-check-label" for="checkedAllDelete">
                                        Hapus
                                    </label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <?php $tab = html_entity_decode('&nbsp;&nbsp;&nbsp;&nbsp;'); ?>
                <!--begin::Table body-->
                <tbody class="fw-bold text-gray-600">
                    @foreach($permissions as $permission)
                    <tr>
                        <td colspan="{{ $colspan }}" class="ps-3 py-5" style="background-color: #fafafa;">
                            <span class="fw-bold fs-6 text-gray-800">{{ $permission['header'] }}
                            </span>
                        </td>
                    </tr>
                    @foreach($permission['subheaders'] as $subheader)
                    <tr>
                        <td class="ps-3 py-5">
                            <span class="text-gray-700"><i class="bi bi-caret-right"></i> {{ $subheader['name'] }}</span>
                        </td>
                        <td class="text-center">
                            @if(in_array('view', $subheader['items']))
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.permissions" class="form-check-input" type="checkbox" value="view_{{ $subheader['value'] }}" id="{{ $subheader['value'] }}ViewPermissionCheck" />
                                    <label class="form-check-label" for="{{ $subheader['value'] }}ViewPermissionCheck">

                                    </label>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array('add', $subheader['items']))
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.permissions" class="form-check-input" type="checkbox" value="add_{{ $subheader['value'] }}" id="{{ $subheader['value'] }}AddPermissionCheck" />
                                    <label class="form-check-label" for="{{ $subheader['value'] }}AddPermissionCheck">

                                    </label>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array('edit', $subheader['items']))
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.permissions" class="form-check-input" type="checkbox" value="edit_{{ $subheader['value'] }}" id="{{ $subheader['value'] }}EditPermissionCheck" />
                                    <label class="form-check-label" for="{{ $subheader['value'] }}EditPermissionCheck">
                                    </label>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(in_array('delete', $subheader['items']))
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-check-custom form-check-sm">
                                    <input v-model="model.permissions" class="form-check-input" type="checkbox" value="delete_{{ $subheader['value'] }}" id="{{ $subheader['value'] }}DeletePermissionCheck" />
                                    <label class="form-check-label" for="{{ $subheader['value'] }}DeletePermissionCheck">

                                    </label>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
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
    const permissions = <?php echo Illuminate\Support\Js::from($permissions) ?>;
    const credentialPermissions = <?php echo Illuminate\Support\Js::from(json_decode($credential_group->permissions)) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                permissions,
                model: {
                    name: '{{ $credential_group->name }}',
                    permissions: credentialPermissions,
                    checkedAll: {
                        view: false,
                        add: false,
                        edit: false,
                        delete: false,
                    }
                },
                loading: false,
            }
        },
        computed: {
            onlyPermissions() {
                return this.permissions.map(permission => {
                    const items = permission.subheaders.map(subheader => {
                        const mergedItems = subheader.items.map(item => `${item}_${subheader.value}`);

                        return mergedItems;
                    }).flat();


                    return items;
                }).flat();
            }
        },
        methods: {
            typeOnlyPermissions(method = 'view') {
                return this.onlyPermissions.filter(permission => {
                    const splittedPermission = permission.split('_');
                    const [type] = splittedPermission;

                    if (type == method) return true;

                    return false;

                });
            },
            async onSubmit() {
                let self = this;
                try {
                    const {
                        name,
                        permissions
                    } = self.model;

                    self.loading = true;

                    const response = await axios.post('/credential-groups/{{ $credential_group->id }}', {
                        name,
                        permissions,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
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
                    self.loading = false;
                }
            },
        },
        watch: {
            'model.checkedAll.view': function(isCheckedAllView) {
                const self = this;
                const type = 'view';
                const methodOnlyPermissions = self.typeOnlyPermissions(type);
                if (isCheckedAllView) {
                    // const newPermissions = Array.from(new Set(...[
                    //     ...self.model.permissions,
                    //     methodOnlyPermissions,
                    // ]))
                    const newPermissions = [
                        ...self.model.permissions,
                        ...methodOnlyPermissions,
                    ];
                    self.model.permissions = newPermissions;
                } else {
                    self.model.permissions = self.model.permissions.filter(permission => !methodOnlyPermissions.includes(permission));
                }
            },
            'model.checkedAll.add': function(isCheckedAllView) {
                const self = this;
                const type = 'add';
                const methodOnlyPermissions = self.typeOnlyPermissions(type);
                if (isCheckedAllView) {
                    const newPermissions = [
                        ...self.model.permissions,
                        ...methodOnlyPermissions,
                    ];
                    self.model.permissions = newPermissions;
                } else {
                    self.model.permissions = self.model.permissions.filter(permission => !methodOnlyPermissions.includes(permission));
                }
            },
            'model.checkedAll.edit': function(isCheckedAllView) {
                const self = this;
                const type = 'edit';
                const methodOnlyPermissions = self.typeOnlyPermissions(type);
                if (isCheckedAllView) {
                    const newPermissions = [
                        ...self.model.permissions,
                        ...methodOnlyPermissions,
                    ];
                    self.model.permissions = newPermissions;
                } else {
                    self.model.permissions = self.model.permissions.filter(permission => !methodOnlyPermissions.includes(permission));
                }
            },
            'model.checkedAll.delete': function(isCheckedAllView) {
                const self = this;
                const type = 'delete';
                const methodOnlyPermissions = self.typeOnlyPermissions(type);
                if (isCheckedAllView) {
                    const newPermissions = [
                        ...self.model.permissions,
                        ...methodOnlyPermissions,
                    ];
                    self.model.permissions = newPermissions;
                } else {
                    self.model.permissions = self.model.permissions.filter(permission => !methodOnlyPermissions.includes(permission));
                }
            },
        }
    })
</script>
@endsection