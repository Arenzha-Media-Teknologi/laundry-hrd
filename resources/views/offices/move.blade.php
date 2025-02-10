@extends('layouts.app')

@section('title', 'Pindah Kantor')

@section('head')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
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
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Pindah Kantor</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- <h2 class="my-10">General</h2> -->
                    <!--begin::Input group-->
                    <div class="fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Pegawai</label>
                        <!--end::Label-->
                        <select class="form-select" multiple id="select-employees">
                        </select>
                    </div>
                    <!-- begin::submit -->

                    <!-- end::submit -->
                </div>
                <div class="col-lg-2 text-center">
                    <i class="bi bi-arrow-right fs-1 text-dark"></i>
                </div>
                <!--end::Card header-->
                <div class="col-lg-4">
                    <div class="fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Kantor Baru</label>
                        <!--end::Label-->
                        <select class="form-select" id="select-office">
                            @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end my-10">
                <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
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
    // var companies = @json('$companies');
    const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;
    const offices = <?php echo Illuminate\Support\Js::from($offices) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisions,
                model: {
                    employeeIds: [],
                    newOfficeId: '',
                },
                submitLoading: false,
            }
        },
        computed: {
            filteredDivisions() {
                let self = this;
                const {
                    companyId
                } = self.model;
                if (companyId) {
                    return self.divisions.filter(division => division.company_id == companyId);
                }

                return [];
            },
        },
        methods: {
            // COMPANY METHODS
            async onSubmit() {
                let self = this;
                try {
                    const {
                        employeeIds,
                        newOfficeId,
                    } = self.model;

                    if (!employeeIds.length || !newOfficeId) {
                        return alert('Form tidak boleh kosong');
                    }

                    self.submitLoading = true;

                    const response = await axios.post('/offices/move', {
                        employee_ids: employeeIds,
                        office_id: newOfficeId,
                    });

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
            gotoUrl(url = null) {
                if (url) {
                    document.location.href = url
                }
            },
        },
    })
</script>
<script>
    $(function() {

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }

            // const employee = JSON.parse(state.text);

            var $state = $(
                `
                <div>
                    <div><strong>${ state.name || 'PEGAWAI' }</strong></div>
                    <div>${ state?.office?.name || 'KANTOR' }</div>
                </div>
                `
            );
            return $state;
        };

        function formatSelection(state) {
            // const employee = JSON.parse(state.text);
            return state?.name || '';
        }

        const dataEmployees = employees.map(employee => {
            employee.text = employee.name;
            return employee;
        })

        $('#select-employees').select2({
            data: dataEmployees,
            templateResult: formatState,
            templateSelection: formatSelection,
        });

        $('#select-employees').on('change', function() {
            const values = $(this).val();
            app.$data.model.employeeIds = values;
        })

        // OFFICE
        function formatStateOffice(state) {
            if (!state.id) {
                return state.text;
            }

            var $state = $(
                `
                <div>
                    <div><strong>${ state.name || 'KANTOR' }</strong></div>
                    <div>${ state?.division?.name || 'DIVISI' } - ${ state?.division?.company?.name || 'PERUSAHAAN' }</div>
                </div>
                `
            );
            return $state;
        };

        function formatSelectionOffice(state) {
            if (!state.id) {
                return state.text;
            }
            // const employee = JSON.parse(state.text);
            return `${state?.name || ''} - ${ state?.division?.name || 'DIVISI' } - ${ state?.division?.company?.name || 'PERUSAHAAN'}`;
        }

        let dataOffices = offices.map(office => {
            office.text = office.name;
            return office;
        })

        dataOffices = [{
                id: '',
                text: 'Pilih Kantor Baru',
                selected: true,
            },
            ...dataOffices,
        ]

        $('#select-office').select2({
            data: dataOffices,
            templateResult: formatStateOffice,
            templateSelection: formatSelectionOffice,
        });

        $('#select-office').on('change', function() {
            const value = $(this).val();
            app.$data.model.newOfficeId = value;
        })
    })
</script>
@endsection