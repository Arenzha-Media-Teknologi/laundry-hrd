@extends('layouts.app')

@section('title', 'Pengaturan - Kehadiran')

@section('head')

@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card card-flush" id="kt_profile_details_view">
        <div class="card-header">
            <div class="card-title">
                <h1>Pengaturan (Kehadiran)</h1>
            </div>
        </div>
        <div class="card-body">
            <h3 class="mb-5" id="working-pattern">Quotes</h3>
            <!--begin::Input group-->
            <div class="fv-row row mb-15">
                <!--begin::Col-->
                <div class="col-md-3">
                    <!--begin::Label-->
                    <label class="fs-6 fw-bold">Quotes</label>
                    <div class="text-muted fs-7">Quotes adalah kalimat yang akan ditampilkan setelah pegawai melakukan absensi di dalam aplikasi
                    </div>
                    <!--end::Label-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-9">
                    <div v-cloak v-if="quotes.mode == 'add'" class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" v-model="quotes.model.add.quotes" class="form-control form-control-sm" placeholder="Hari ini adalah hari terbaikmu">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success btn-sm" :data-kt-indicator="quotes.loading ? 'on' : null" :disabled="quotes.loading" @click="addQuotes()">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div v-cloak v-if="quotes.mode == 'edit'" class="row mb-3">
                        <div class="col-md-8">
                            <input type="text" v-model="quotes.model.edit.quotes" class="form-control form-control-sm" placeholder="Hari ini adalah hari terbaikmu">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info btn-sm" :data-kt-indicator="quotes.loading ? 'on' : null" :disabled="quotes.loading" @click="updateQuotes()">
                                <span class="indicator-label">Ubah</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <button class="btn btn-icon btn-light-danger btn-sm" @click="changeMode('add')">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 500px;">
                        <table class="table align-middle table-row-dashed">
                            <thead class="bg-light-primary">
                                <tr>
                                    <th class="ps-2">Quotes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotes as $quote)
                                <tr>
                                    <td class="ps-2">{{ $quote->quotes }}</td>
                                    <td class="pe-2 text-end">
                                        <button class="btn btn-icon btn-danger btn-sm" @click="openDeleteConfirmation({{ $quote->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button class="btn btn-icon btn-info btn-sm" @click="onClickEdit({{ $quote->id }}, '{{ $quote->quotes }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Col-->
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Profil Completion-->
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
    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                quotes: {
                    model: {
                        add: {
                            quotes: '',
                        },
                        edit: {
                            id: null,
                            quotes: '',
                        }
                    },
                    mode: 'add',
                    loading: false,
                },
            }
        },
        methods: {
            async addQuotes() {
                let self = this;
                try {
                    self.quotes.loading = true;

                    const body = {
                        quotes: self.quotes.model.add.quotes,
                    }

                    if (!body.quotes) {
                        return toastr.warning('Quotes harus diisi');
                    }

                    const response = await axios.post('/attendance-quotes', body);

                    if (response) {
                        // console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        document.location.reload();
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    // self.quotes.loading = false;
                }
            },
            async updateQuotes() {
                let self = this;
                try {
                    self.quotes.loading = true;

                    const body = {
                        quotes: self.quotes.model.edit.quotes,
                    }

                    if (!body.quotes) {
                        return toastr.warning('Quotes harus diisi');
                    }

                    const response = await axios.post(`/attendance-quotes/${self.quotes.model.edit.id}`, body);

                    if (response) {
                        // console.log(response)
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Perubahan berhasil disimpan'
                        }

                        const data = response?.data?.data;
                        toastr.success(message + '. Mengalihkan..');
                        document.location.reload();
                    }
                } catch (error) {
                    let message = error?.response?.data?.message;
                    if (!message) {
                        message = 'Something wrong...'
                    }
                    toastr.error(message);
                } finally {
                    // self.quotes.loading = false;
                }
            },
            openDeleteConfirmation(id) {
                const self = this;
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Quotes akan dihapus",
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
                        return self.sendDeleteRequest(id);
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                })
            },
            sendDeleteRequest(id) {
                const self = this;
                return axios.delete('/attendance-quotes/' + id)
                    .then(function(response) {
                        let message = response?.data?.message;
                        if (!message) {
                            message = 'Data berhasil disimpan'
                        }
                        // self.delete(id);
                        // redrawDatatable();
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
            onClickEdit(id, quotes) {
                this.quotes.mode = 'edit';
                this.quotes.model.edit.id = id;
                this.quotes.model.edit.quotes = quotes;
            },
            changeMode(mode) {
                this.quotes.mode = mode;
            },
        }
    })
</script>
@endsection