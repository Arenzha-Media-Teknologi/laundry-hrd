@extends('layouts.app')

@section('title', 'Ubah Pola kerja')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!-- begin::card -->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header">
            <div class="card-title">
                <h2>Tambah Pola kerja</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="my-10">General</h2>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Nama</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" v-model="model.name" class="form-control mb-2" placeholder="Cth: Pola Kerja Finance" value="">
                        <!--end::Input-->
                        <!--begin::Description-->
                        <!-- <div class="text-muted fs-7">Nama pola kerja tidak boleh s</div> -->
                        <!--end::Description-->
                        <!-- <div class="fv-plugins-message-container invalid-feedback"></div> -->
                    </div>
                    <!--end::Input group-->
                    <div class="separator"></div>
                    <!--end::Input group-->
                    <div class="row">
                        <div class="col-xl-6">
                            <h2 class="my-10">Detail Hari</h2>
                        </div>
                        <div class="col-xl-6">

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="fw-bolder fs-6 text-gray-500">
                                    <td>Hari</td>
                                    <td>Status</td>
                                    <td>Jam Masuk</td>
                                    <td>Jam Keluar</td>
                                    <td>Lembur</td>
                                    <td>Jam Mulai Lembur</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in model.items">
                                    <td>
                                        <span v-cloak class="fw-bolder fs-6 text-gray-800 d-inline-block ps-3">
                                            @{{ item.dayName }}
                                        </span>
                                    </td>
                                    <td>
                                        <select v-model="item.day_status" class="form-select form-select-sm">
                                            <option value="workday">Hari Kerja</option>
                                            <option value="holiday">Hari Libur</option>
                                        </select>
                                    </td>
                                    <td>
                                        <!--begin::Input-->
                                        <input type="time" v-model="item.clock_in" class="form-control form-control-sm" placeholder="Masukkan jam masuk" value="">
                                        <!--end::Input-->
                                    </td>
                                    <td>
                                        <!--begin::Input-->
                                        <input type="time" v-model="item.clock_out" class="form-control form-control-sm" placeholder="Masukkan jam keluar" value="">
                                        <!--end::Input-->
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" v-model="item.have_overtime" id="flexCheckDefault" />
                                                <label class="form-check-label" for="flexCheckDefault">

                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pe-3">
                                        <input type="time" v-model="item.overtime_start_time" class="form-control form-control-sm" placeholder="Masukkan jam keluar">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- begin::submit -->
                    <div class="d-flex justify-content-end my-10">
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit" :disabled="submitLoading">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Mengirim data...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!-- end::submit -->
                </div>
                <!--end::Card header-->
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
    let workingPatternItems = <?php echo Illuminate\Support\Js::from($working_pattern->items) ?>;
    const dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
    workingPatternItems = workingPatternItems.map((item, index) => {
        item.dayName = dayNames[index];
        return item;
    })
    // for (let i = 0; i < 7; i++) {
    //     workingPatternItems.push({
    //         order: i + 1,
    //         dayName: dayNames[i],
    //         day_status: i < 5 ? 'workday' : 'holiday',
    //         clock_in: '08:00',
    //         clock_out: '16:00',
    //     })
    // }

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                model: {
                    name: '{{ $working_pattern->name }}',
                    items: workingPatternItems,
                },
                submitLoading: false,
            }
        },
        methods: {
            // COMPANY METHODS
            async onSubmit() {
                let self = this;
                try {
                    const isEmptyOvertimeStartTime = this.model.items.some(item => item.have_overtime == 1 && !item.overtime_start_time);

                    if (isEmptyOvertimeStartTime) {
                        return toastr.warning('Jam mulai lembur harus diisi');
                    }

                    const isIncorrectOvertimeStartTime = this.model.items.some(item => {
                        if (item.have_overtime == 1) {
                            const clockOut = new Date(`1970-01-01T${item.clock_out}:00`);
                            const overtimeStartFrom = new Date(`1970-01-01T${item.overtime_start_time}:00`);

                            if (overtimeStartFrom < clockOut) {
                                return true;
                            }
                        }
                    });

                    if (isIncorrectOvertimeStartTime) {
                        return toastr.warning('Jam mulai lembur harus lebih besar dari jam keluar');
                    }

                    const {
                        name,
                        items
                    } = self.model;

                    self.submitLoading = true;

                    const response = await axios.post('/working-patterns/{{ $working_pattern->id }}', {
                        name,
                        items,
                    });

                    if (response) {
                        console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        setTimeout(() => {
                            self.gotoUrl('/working-patterns');
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
            gotoUrl(url = null) {
                if (url) {
                    document.location.href = url
                }
            },
        },
        // watch: {
        //     'model.items': {
        //         handler(newValue, oldValue) {
        //             model.items.forEach(item => {
        //                 if(item.day_status == '')
        //             })
        //         },
        //         deep: true,
        //     }
        // }
    })
</script>
@endsection