@extends('layouts.app')

@section('title', 'Struktur Organisasi')

@section('prehead')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/custom/jstree/jstree.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pagestyle')
<style>
    #department-datatable_wrapper>.row {
        padding-left: 10px;
    }
</style>
@endsection

@section('content')
<?php
$departmentIcon = 'bi bi-diagram-3';
$designationIcon = 'bi bi-grid';
$jobTitleIcon = 'bi bi-person-badge';
?>
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <div class="row gy-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-4 mb-xl-10">
            <!--begin::List widget 16-->
            <div class="card card-flush h-xl-100">
                <!--begin::Header-->
                <div class="card-header pt-7">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-800">Struktur Organisasi</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-6">Ringkasan struktur organisasi</span>
                    </h3>
                    <!--end::Title-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-4">
                    <div class="separator mb-6"></div>
                    <div class="bg-light rounded p-3 mb-5">
                        <div>
                            <i class="{{ $departmentIcon }} fs-2 text-info"></i><strong>&nbsp;: Departemen</strong>
                        </div>
                        <div class="my-5">
                            <i class="{{ $designationIcon }} fs-2 text-danger"></i><strong>&nbsp;: Bagian</strong>
                        </div>
                        <div>
                            <i class="{{ $jobTitleIcon }} fs-2 text-warning"></i><strong>&nbsp;: Job Title</strong>
                        </div>
                    </div>
                    <!-- begin:Card content -->
                    <div style="max-height: 350px; overflow-y: scroll">
                        <div id="kt_docs_jstree_basic">
                            <ul>
                                @foreach($departments as $department)
                                <li class="py-3" data-jstree='{ "icon" : "<?= $departmentIcon ?> text-info", "parent": "#", "id": "department-<?= $department->id ?>"}'>
                                    <span>{{ $department->name }}</span>
                                    <ul>
                                        @foreach($department->designations as $designation)
                                        <li class="py-1" data-jstree='{ "icon" : "<?= $designationIcon ?> text-danger", "parent": "department-<?= $department->id ?>", "id": "designation<?= $designation->id ?>"}'>
                                            <span>{{ $designation->name }}</span>
                                            <ul>
                                                @foreach($designation->jobTitles as $jobTitle)
                                                <li class="py-1" data-jstree='{ "icon" : "<?= $jobTitleIcon ?> text-warning", "parent": "designation-<?= $designation->id ?>", "id": "job-title-<?= $jobTitle->id ?>"}'>{{ $jobTitle->name }}</li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!-- end:Card content -->
                </div>
                <!--end: Card Body-->
            </div>
            <!--end::List widget 16-->
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-8 mb-5 mb-xl-10">
            <!-- begin::TabNav -->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <div class="d-flex flex-wrap flex-stack gap-5 gap-lg-10">
                    <!--begin:::Tabs-->
                    <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-lg-n2 me-auto">
                        @can('view', App\Models\Department::class)
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#department-tab"><i class="{{ $departmentIcon }}"></i> Departemen</a>
                        </li>
                        <!--end:::Tab item-->
                        @endif
                        @can('view', App\Models\Designation::class)
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#designation-tab"><i class="{{ $designationIcon }}"></i> Bagian</a>
                        </li>
                        <!--end:::Tab item-->
                        @endif
                        @can('view', App\Models\JobTitle::class)
                        <!--begin:::Tab item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#job-title-tab"><i class="{{ $jobTitleIcon }}"></i> Job Title</a>
                        </li>
                        <!--end:::Tab item-->
                        @endcan
                    </ul>
                    <!--end:::Tabs-->
                </div>
                <!--begin::Tab content-->
                <div class="tab-content">
                    <!--begin::Tab pane-->
                    <div class="tab-pane fade active show" id="department-tab" role="tab-panel">
                        <x-structures.department />
                    </div>
                    <!--end::Tab pane-->
                    <!--begin::Tab pane-->
                    <div class="tab-pane fade" id="designation-tab" role="tab-panel">
                        <x-structures.designation />
                    </div>
                    <!--end::Tab pane-->
                    <!--begin::Tab pane-->
                    <div class="tab-pane fade" id="job-title-tab" role="tab-panel">
                        <x-structures.job-title />
                    </div>
                    <!--end::Tab pane-->
                </div>
                <!--end::Tab content-->
            </div>
            <!-- end::TabNav -->
        </div>
        <!--end::Col-->
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/jstree/jstree.bundle.js') }}"></script>
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


        datatable = $('#department-datatable').DataTable({
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

        datatable2 = $('#designation-datatable').DataTable({
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch2 = document.querySelector('[data-kt-designation-table-filter="search"]');
        filterSearch2.addEventListener('keyup', function(e) {
            datatable2.search(e.target.value).draw();
        });

        datatable3 = $('#jobTitle-datatable').DataTable({
            "drawCallback": function() {
                console.log('redraw table...')
            },
            "language": {
                "infoEmpty": " ",
                "zeroRecords": " "
            }
        });

        // const handleSearchDatatable = () => {
        const filterSearch3 = document.querySelector('[data-kt-job-title-table-filter="search"]');
        filterSearch3.addEventListener('keyup', function(e) {
            datatable3.search(e.target.value).draw();
        });
    })
</script>
<script>
    const closeAddModal = (selector) => {
        const addDepartmentModal = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(addDepartmentModal);
        modal.hide();
    }

    const closeEditModal = (selector) => {
        const editDepartmentModal = document.querySelector(selector);
        const modal = bootstrap.Modal.getInstance(editDepartmentModal);
        modal.hide();
    }

    let jstreeView = null;
    let addTreejsNode = null;
    let updateTreejsNode = null;

    $(function() {
        const jstreeOptions = {
            "core": {
                "themes": {
                    "responsive": false
                }
            },
            "types": {
                "default": {
                    "icon": "fa fa-star"
                },
                "file": {
                    "icon": "fa fa-file"
                }
            },
            "plugins": ["types"]
        }

        jstreeView = $('#kt_docs_jstree_basic');

        jstreeView.jstree(jstreeOptions).bind("ready.jstree", function(event, data) {
            $(this).jstree("open_all");
        });

        addTreejsNode = (parent, id, text) => {
            if (jstreeView !== null) {
                jstreeView.jstree().create_node(parent, {
                    id,
                    text
                }, "last", function() {
                    console.log('added')
                });
            }
        }

        updateTreejsNode = (node, text) => {
            if (jstreeView !== null) {
                jstreeView.jstree('rename_node', node, text);
            }
        }
    })


    // const 


    // $('#jstree').jstree().create_node('p2', {
    //     "id": "c3",
    //     "text": "Child 3"
    // }, "last", function() {
    //     alert("Child created");
    // });

    const departments = <?php echo Illuminate\Support\Js::from($departments) ?>;
    const designations = <?php echo Illuminate\Support\Js::from($designations) ?>;
    const jobTitles = <?php echo Illuminate\Support\Js::from($jobTitles) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                departments,
                department: {
                    model: {
                        add: {
                            name: '',
                        },
                        edit: {
                            index: null,
                            id: null,
                            name: '',
                        }
                    },
                    submitLoading: false,
                },
                designations,
                designation: {
                    model: {
                        add: {
                            name: '',
                            departmentId: null,
                        },
                        edit: {
                            index: null,
                            id: null,
                            name: '',
                            departmentId: null,
                        }
                    },
                    submitLoading: false,
                },
                jobTitles,
                jobTitle: {
                    model: {
                        add: {
                            name: '',
                            departmentId: null,
                            designationId: null,
                        },
                        edit: {
                            index: null,
                            id: null,
                            name: '',
                            departmentId: null,
                            designationId: null,
                        }
                    },
                    submitLoading: false,
                },
            }
        },
        computed: {
            filteredAddDesignations() {
                const {
                    departmentId
                } = this.jobTitle.model.add;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id == Number(departmentId))
                }

                return [];
            },
            filteredEditDesignations() {
                const {
                    departmentId
                } = this.jobTitle.model.edit;
                if (departmentId) {
                    return this.designations.filter(designation => designation.department_id == Number(departmentId))
                }

                return [];
            }
        },
        methods: {
            // DEPARTMENT METHODS
            async onSubmitDepartment() {
                let self = this;
                try {
                    const {
                        name,
                    } = self.department.model.add;

                    self.department.submitLoading = true;

                    const response = await axios.post('/departments', {
                        name,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.addDepartment(data);
                        closeAddModal('#department_add_modal');
                        this.resetDepartmentForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong... ' + error
                    }
                    toastr.error(message);
                } finally {
                    self.department.submitLoading = false;
                }
            },
            addDepartment(data) {
                if (data) {
                    this.departments.push(data)
                }
            },
            resetDepartmentForm() {
                this.department.model.add.name = '';
                this.department.model.edit.name = '';
            },
            openDepartmentEditModal(id, index) {
                const [department] = this.departments.filter(department => department.id == id);
                if (department) {
                    this.department.model.edit.index = index
                    this.department.model.edit.id = id
                    this.department.model.edit.name = department.name
                }
            },
            async onSubmitEditDepartment() {
                let self = this;
                try {
                    const {
                        id,
                        index,
                        name
                    } = self.department.model.edit;

                    self.department.submitLoading = true;

                    const response = await axios.post(`/departments/${id}`, {
                        name,
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
                        this.editDepartment(index, data);
                        // redrawDatatable();
                        closeEditModal('#designation_edit_modal');
                        this.resetDepartmentForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.department.submitLoading = false;
                }
            },
            editDepartment(index, data) {
                this.departments.splice(index, 1, data);
            },
            openDepartmentDeleteConfirmation(id) {
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
                        return self.sendDepartmentDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDepartmentDeleteRequest(id) {
                const self = this;
                return axios.delete('/departments/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteDepartment(id);
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
            deleteDepartment(id) {
                this.departments = this.departments.filter(department => department.id !== id);
            },
            // DESIGNATION METHODS
            async onSubmitDesignation() {
                let self = this;
                try {
                    const {
                        name,
                        departmentId
                    } = self.designation.model.add;

                    self.designation.submitLoading = true;

                    const response = await axios.post('/designations', {
                        name,
                        department_id: departmentId,
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
                        this.addDesignation(data);
                        // redrawDatatable();
                        closeAddModal('#designation_add_modal');
                        this.resetDesignationForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.designation.submitLoading = false;
                }
            },
            addDesignation(data) {
                if (data) {
                    this.designations.push(data)
                }
            },
            resetDesignationForm() {
                this.designation.model.add.name = '';
                this.designation.model.edit.name = '';
            },
            openDesignationEditModal(id, index) {
                const [designation] = this.designations.filter(designation => designation.id == id);
                if (designation) {
                    this.designation.model.edit.index = index
                    this.designation.model.edit.id = id
                    this.designation.model.edit.name = designation.name
                    this.designation.model.edit.departmentId = designation.department_id
                }
            },
            async onSubmitEditDesignation() {
                let self = this;
                try {
                    const {
                        id,
                        index,
                        name,
                        departmentId,
                    } = self.designation.model.edit;

                    self.designation.submitLoading = true;

                    const response = await axios.post(`/designations/${id}`, {
                        name,
                        department_id: departmentId,
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
                        this.editDesignation(index, data);
                        // redrawDatatable();
                        closeEditModal('#designation_edit_modal');
                        this.resetDesignationForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.designation.submitLoading = false;
                }
            },
            editDesignation(index, data) {
                this.designations.splice(index, 1, data);
            },
            openDesignationDeleteConfirmation(id) {
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
                        return self.sendDesignationDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDesignationDeleteRequest(id) {
                const self = this;
                return axios.delete('/designations/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteDesignation(id);
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
            deleteDesignation(id) {
                this.designations = this.designations.filter(designation => designation.id !== id);
            },
            // JOB TITLE METHODS
            async onSubmitJobTitle() {
                let self = this;
                try {
                    const {
                        name,
                        departmentId,
                        designationId,
                    } = self.jobTitle.model.add;

                    self.jobTitle.submitLoading = true;

                    const response = await axios.post('/job-titles', {
                        name,
                        department_id: departmentId,
                        designation_id: designationId,
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
                        this.addJobTitle(data);
                        // redrawDatatable();
                        closeAddModal('#jobTitle_add_modal');
                        this.resetJobTitleForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.jobTitle.submitLoading = false;
                }
            },
            addJobTitle(data) {
                if (data) {
                    this.jobTitles.push(data)
                }
            },
            resetJobTitleForm() {
                this.jobTitle.model.add.name = '';
                this.jobTitle.model.add.designationId = null;
                this.jobTitle.model.add.departmentId = null;

                this.jobTitle.model.edit.name = '';
                this.jobTitle.model.edit.designationId = null;
                this.jobTitle.model.edit.departmentId = null;
                this.jobTitle.model.edit.index = null;
                this.jobTitle.model.edit.id = null;
            },
            openJobTitleEditModal(id, index) {
                const [jobTitle] = this.jobTitles.filter(jobTitle => jobTitle.id == id);
                if (jobTitle) {
                    this.jobTitle.model.edit.index = index
                    this.jobTitle.model.edit.id = id
                    this.jobTitle.model.edit.name = jobTitle.name
                    this.jobTitle.model.edit.departmentId = jobTitle.department_id
                    this.jobTitle.model.edit.designationId = jobTitle.designation_id
                }
            },
            async onSubmitEditJobTitle() {
                let self = this;
                try {
                    const {
                        id,
                        index,
                        name,
                        departmentId,
                        designationId
                    } = self.jobTitle.model.edit;

                    self.jobTitle.submitLoading = true;

                    const response = await axios.post(`/job-titles/${id}`, {
                        name,
                        department_id: departmentId,
                        designation_id: designationId,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;

                        this.editJobTitle(index, data);
                        // redrawDatatable();
                        closeEditModal('#jobTitle_edit_modal');
                        this.resetJobTitleForm();
                        toastr.success(message);
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    self.jobTitle.submitLoading = false;
                }
            },
            editJobTitle(index, data) {
                this.jobTitles.splice(index, 1, data);
            },
            openJobTitleDeleteConfirmation(id) {
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
                        return self.sendJobTitleDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendJobTitleDeleteRequest(id) {
                const self = this;
                return axios.delete('/job-titles/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        self.deleteJobTitle(id);
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
            deleteJobTitle(id) {
                this.jobTitles = this.jobTitles.filter(jobTitle => jobTitle.id !== id);
            },
        },
        watch: {
            'jobTitle.model.add.departmentId': function(val) {
                this.jobTitle.model.add.designationId = null;
            },
            'jobTitle.model.edit.departmentId': function(val) {
                this.jobTitle.model.edit.designationId = null;
            },
        }
    })
</script>
@endsection