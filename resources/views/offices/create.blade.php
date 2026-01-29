@extends('layouts.app')

@section('title', 'Tambah Kantor')

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
                <h2>Tambah Kantor</h2>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="my-10">General</h2>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Perusahaan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.companyId" class="form-select mb-2">
                            <option value="">Pilih Perusahaan</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">@{{ company.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Divisi</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <select v-model="model.divisionId" class="form-select mb-2">
                            <option value="">Pilih Divisi</option>
                            <option v-for="division in filteredDivisions" :key="division.id" :value="division.id">@{{ division.name }}</option>
                        </select>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="required form-label">Nama</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" v-model="model.name" class="form-control mb-2" placeholder="Masukkan nama kantor" value="">
                        <!--end::Input-->
                        <!--begin::Description-->
                        <!-- <div class="text-muted fs-7">Nama kantor tidak boleh s</div> -->
                        <!--end::Description-->
                        <!-- <div class="fv-plugins-message-container invalid-feedback"></div> -->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Telepon</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="text" v-model="model.phone" class="form-control mb-2" placeholder="Masukkan nama telepon" value="">
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Alamat</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <textarea v-model="model.address" class="form-control mb-2" rows="5" placeholder="Masukkan alamat"></textarea>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="mb-10 fv-row fv-plugins-icon-container">
                        <!--begin::Label-->
                        <label class="form-label">Jam Buka</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input type="time" v-model="model.openingTime" class="form-control mb-2" placeholder="Masukkan jam buka">
                        <!--end::Input-->
                    </div>
                    <div class="separator"></div>
                    <!--end::Input group-->
                    <h2 class="my-10">Detail Lokasi</h2>
                    <!-- begin::alert -->
                    <div class="alert bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row w-100 p-5 mb-10">
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                        <span class="svg-icon svg-icon-2hx svg-icon-primary me-4 mb-5 mb-sm-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                                <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Content-->
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <h5 class="mb-1">Pastikan titik lokasi sudah akurat</h5>
                            <span>Detail lokasi digunakan untuk menentukan titik radius absensi pegawai. Jika titik lokasi tidak ditemukan pada peta dibawah, gunakan Google Maps dengan cara <a href="https://blogsecond.com/2018/09/mendapatkan-latitude-dan-longitude-gmaps/" target="_blank">disini</a></span>
                        </div>
                        <!--end::Content-->
                    </div>
                    <!-- end::alert -->
                    <!-- begin::map -->
                    <div class="mb-10">
                        <div id="map"></div>
                    </div>
                    <!-- end::map -->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column flex-md-row gap-5">
                        <div class="fv-row flex-row-fluid fv-plugins-icon-container">
                            <!--begin::Label-->
                            <label class="required form-label">Latitude</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.latitude" class="form-control" placeholder="Masukkan latitude" value="">
                            <!--end::Input-->
                        </div>
                        <div class="flex-row-fluid">
                            <!--begin::Label-->
                            <label class="required form-label">Longitude</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" v-model="model.longitude" class="form-control" placeholder="Masukkan longitude">
                            <!--end::Input-->
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!-- begin::submit -->
                    <div class="d-flex justify-content-end my-10">
                        <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-primary" @click="onSubmit">
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
    // var companies = @json('$companies');
    const companies = <?php echo Illuminate\Support\Js::from($companies) ?>;
    const divisions = <?php echo Illuminate\Support\Js::from($divisions) ?>;

    const app = new Vue({
        el: '#kt_content_container',
        data() {
            return {
                companies,
                divisions,
                model: {
                    companyId: null,
                    divisionId: null,
                    name: '',
                    phone: '',
                    address: '',
                    openingTime: '',
                    latitude: -6.200000,
                    longitude: 106.816666,
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
        mounted() {
            const initLatitude = this.model.latitude;
            const initLongitude = this.model.longitude;
            this.initMap(initLatitude, initLongitude)
        },
        methods: {
            // COMPANY METHODS
            async onSubmit() {
                let self = this;
                try {
                    const {
                        // companyId,
                        divisionId,
                        name,
                        phone,
                        address,
                        openingTime,
                        latitude,
                        longitude
                    } = self.model;

                    self.submitLoading = true;

                    const response = await axios.post('/offices', {
                        // company_id: companyId,
                        division_id: divisionId,
                        name,
                        phone,
                        address,
                        opening_time: openingTime,
                        latitude,
                        longitude,
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
                            self.gotoUrl('/offices');
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
            initMap(initLatitude, initLongitude) {
                const self = this;
                mapboxgl.accessToken = `{{ env('MAPBOX_ACCESS_TOKEN') }}`;
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/streets-v11',
                    center: [initLongitude, initLatitude],
                    // center: [106.816666, -6.200000],
                    zoom: 13
                });

                // Add the control to the map.
                var geocoder = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    marker: false,
                    mapboxgl: mapboxgl
                })

                var marker = new mapboxgl.Marker({
                    draggable: true,
                    color: "royalblue"
                })

                marker.setLngLat({
                        lng: initLongitude,
                        lat: initLatitude
                    })
                    .addTo(map);

                function onDragEnd() {
                    var lngLat = marker.getLngLat();
                    console.log('On Drag Longitude: ' + lngLat.lng + ' Latitude: ' + lngLat.lat);
                    self.model.longitude = lngLat.lng;
                    self.model.latitude = lngLat.lat;
                }

                marker.on('dragend', onDragEnd);

                geocoder.on('result', function(e) {
                    marker.remove();
                    marker.setLngLat(e.result.center).addTo(map);
                    self.model.longitude = e.result.center[0];
                    self.model.latitude = e.result.center[1];
                });

                map.on('click', function(e) {
                    marker.remove();
                    marker.setLngLat(e.lngLat).addTo(map);
                    self.model.longitude = e.lngLat.lng;
                    self.model.latitude = e.lngLat.lat;
                });

                map.addControl(
                    geocoder
                );

                map.addControl(
                    new mapboxgl.GeolocateControl({
                        positionOptions: {
                            enableHighAccuracy: true
                        },
                        trackUserLocation: true
                    })
                );
            }
        },
        watch: {
            'model.companyId': function(val) {
                this.model.divisionId = null;
            }
        }
    })
</script>
@endsection