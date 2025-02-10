@extends('layouts.app')

@section('title', 'Tambah Pengajuan Lembur')

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

  .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
    color: #000 !important;
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
        <h2>Tambah Pengajuan Lembur</h2>
      </div>
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body">
      <div class="row justify-content-between align-items-center">
        <div class="col-md-6">
          <h2 class="mb-0">SURAT PERINTAH LEMBUR - METAPRINT</h2>
        </div>
        <div class="col-md-3 row align-items-center">
          <div class="col-sm-4">
            <label class="form-label mb-0"><strong>Nomor</strong></label>
          </div>
          <div class="col-sm-8 d-flex align-items-center">
            <div class="me-2">
              <input type="text" v-model="model.number" class="form-control form-control-solid" disabled>
            </div>
            <div v-cloak v-if="getNumberLoading">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-4">
        <div style="border-bottom: 2px solid #6c6c6c;" class="mb-1"></div>
        <div style="border-bottom: 4px solid #6c6c6c;" class="mb-2"></div>
      </div>
      <div class="mt-10">
        <div class="row justify-content-between">
          <div class="col-md-5">
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>TGL/HARI</strong></label>
              </div>
              <div class="col-sm-8">
                <div class="row align-items-center">
                  <div class="col-sm-5">
                    <input type="date" v-model="model.date" class="form-control form-control-sm w-100" :disabled="getNumberLoading">
                  </div>
                  <div class="col-sm-1">
                    <strong class="fs-1">/</strong>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm w-100" disabled :value="getDayFromDate">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row mb-7">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>Jenis Lemburan</strong></label>
              </div>
              <div class="col-sm-8">
                <div class="">
                  <div class="mb-5">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" v-model="model.type" value="cetak" id="overtimeTypeCetak" name="overtime_type">
                      <label class="form-check-label" for="overtimeTypeCetak">
                        Cetak
                      </label>
                    </div>
                  </div>
                  <div class="mb-5">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" v-model="model.type" value="finishing" id="overtimeTypeFinishing" name="overtime_type">
                      <label class="form-check-label" for="overtimeTypeFinishing">
                        Finishing
                      </label>
                    </div>
                  </div>
                  <div class="mb-0">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" v-model="model.type" value="other" id="overtimeTypeOther" name="overtime_type">
                      <label class="form-check-label" for="overtimeTypeOther">
                        Other
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-cloak v-if="model.type == 'other'" class="row justify-content-between mt-10">
          <div class="col-md-5">
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>Judul</strong></label>
              </div>
              <div class="col-sm-8">
                <input type="text" v-model="model.title" class="form-control form-control-sm">
              </div>
            </div>
          </div>
        </div>
        <div v-cloak v-else class="row justify-content-between mt-10">
          <div class="col-md-5">
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>No. Job Order #</strong></label>
              </div>
              <div class="col-sm-8">
                <input type="text" v-model="model.jobOrderNumber" class="form-control form-control-sm">
              </div>
            </div>
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>Pesanan</strong></label>
              </div>
              <div class="col-sm-8">
                <input type="text" v-model="model.order" class="form-control form-control-sm" placeholder="Dus Amoxilin">
              </div>
            </div>
            <div class="row align-items-center">
              <div class="col-sm-4">
                <label class="form-label mb-0"><strong>Delivery</strong></label>
              </div>
              <div class="col-sm-6">
                <input type="date" v-model="model.deliveryDate" class="form-control form-control-sm">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>Pelanggan</strong></label>
              </div>
              <div class="col-sm-8">
                <input type="text" v-model="model.customer" class="form-control form-control-sm" placeholder="Kalbe">
              </div>
            </div>
            <div class="row mb-7 align-items-center">
              <div class="col-sm-4">
                <label class=" form-label mb-0"><strong>Qty Order</strong></label>
              </div>
              <div class="col-sm-8">
                <input type="text" v-model="model.orderQuantity" class="form-control form-control-sm">
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="table-responsive mt-10">
        <table class="table table-bordered">
          <thead class="text-center align-middle bg-light">
            <tr>
              <th rowspan="2"></th>
              <th rowspan="2">Pegawai</th>
              <th rowspan="2">Keterangan</th>
              <th colspan="3" class="text-center">Jam Pengajuan</th>
              <th rowspan="2"></th>
            </tr>
            <tr>
              <th>Masuk</th>
              <th>Keluar</th>
              <th>Jumlah (Menit)</th>
            </tr>
          </thead>
          <tbody class="text-center align-middle">
            <!-- <tr style="background-color: #fffbe4;"> -->
            <tr>
              <td colspan="6" class="text-start ps-3"><strong>PIC:</strong></td>
            </tr>
            <!-- #fff8d2 -->
            <!-- <tr style="background-color: #fffbe4;"> -->
            <template v-for="(pic, index) in memberPic">
              <tr :key="`pic_row_1_${index}`">
                <td class="text-center">
                  <span v-cloak v-if="picValidations[index].message">
                    <i class="bi bi-exclamation-circle-fill text-danger"></i>
                  </span>
                </td>
                <td class="ps-3">
                  <select2 :options="employees" v-model="pic.employeeId"></select2>
                </td>
                <td>
                  <input type="text" v-model="pic.description" class="form-control form-control-sm" placeholder="Masukkan keterangan">
                </td>
                <td>
                  <input type="time" v-model="pic.clockIn" class="form-control form-control-sm">
                </td>
                <td>
                  <input type="time" v-model="pic.clockOut" class="form-control form-control-sm">
                </td>
                <td class="d-flex justify-content-center">
                  <input type="text" class="form-control form-control-sm text-end" style="width: 100px;" :value="picOvertimes[index]" disabled>
                </td>
                <td></td>
              </tr>
              <tr :key="`pic_row_2_${index}`">
                <td></td>
                <td class="text-start" colspan="5" style="padding-top: 0.1rem;">
                  <small class="text-danger">@{{ picValidations[index].message }}</small>
                </td>
              </tr>
            </template>
            <tr>
              <!-- <tr style="background-color: #f3fcff;"> -->
              <td colspan="6" class="text-start ps-3"><strong>Anggota:</strong></td>
            </tr>
            <template v-for="(member, index) in members">
              <tr :key="`row_1_${index}`">
                <td class="text-center">
                  <span v-cloak v-if="memberValidations[index].message">
                    <i class="bi bi-exclamation-circle-fill text-danger"></i>
                  </span>
                </td>
                <td class="ps-3">
                  <select2 :options="employees" v-model="member.employeeId"></select2>
                </td>
                <td>
                  <input type="text" v-model="member.description" class="form-control form-control-sm" placeholder="Masukkan keterangan">
                </td>
                <td>
                  <input type="time" v-model="member.clockIn" class="form-control form-control-sm">
                </td>
                <td>
                  <input type="time" v-model="member.clockOut" class="form-control form-control-sm">
                </td>
                <td class="d-flex justify-content-center">
                  <input type="text" class="form-control form-control-sm text-end" style="width: 100px;" :value="memberOvertimes[index]" disabled>
                </td>
                <td class="pe-3">
                  <button class="btn btn-icon btn-danger btn-sm" @click="deleteMember(index)"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
              <tr :key="`row_2_${index}`">
                <td></td>
                <td class="text-start" colspan="5" style="padding-top: 0.1rem;">
                  <small class="text-danger">@{{ memberValidations[index].message }}</small>
                </td>
              </tr>
            </template>
            <tr>
              <td colspan="6" class="text-start ps-3">
                <button class="btn btn-primary btn-sm" @click="addMember()"><i class="bi bi-plus-lg"></i> Tambah Anggota</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- <div class="mt-10">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=" form-label mb-0"><strong>Alasan Selisih Jam Lembur</strong></label>
                    </div>
                    <div class="col-sm-6">
                        <textarea v-model="model.differenceNote" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div> -->
      <div class="separator my-10" style="border-width: 2px;"></div>
      <div class="row justify-content-between mt-10">
        <div class="col-md-4">
          <div class="card card-bordered" style="overflow: hidden;">
            <div class="card-body p-0">
              <div style="width: 100%; height: 4px; background-color: #636e72;"></div>
              <div class="p-5">
                <div class="mb-3">
                  <label class="form-label mb-0"><strong>Disiapkan</strong></label>
                </div>
                <div>
                  <select2 :options="allEmployees" v-model="model.preparedBy" disabled></select2>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-bordered" style="overflow: hidden;">
            <div class="card-body p-0">
              <div style="width: 100%; height: 4px; background-color: #636e72;"></div>
              <div class="p-5">
                <div class="mb-3">
                  <label class="form-label mb-0"><strong>Diajukan oleh</strong></label>
                </div>
                <div>
                  <select2 :options="allEmployees" v-model="model.submittedBy"></select2>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card card-bordered" style="overflow: hidden;">
            <div class="card-body p-0">
              <div style="width: 100%; height: 4px; background-color: #636e72;"></div>
              <div class="p-5">
                <div class="mb-3">
                  <label class=" form-label mb-0"><strong>Diketahui</strong></label>
                </div>
                <div>
                  <select2 :options="allEmployees" v-model="model.knownBy"></select2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer text-end">
      <button type="button" :data-kt-indicator="submitLoading ? 'on' : null" class="btn btn-success" @click="save" style="min-width: 200px;" :disabled="disabledSaveButton">
        <span class="indicator-label"><i class="bi bi-check-lg"></i> Simpan</span>
        <span class="indicator-progress">Mengirim data...
          <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
      </button>
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
  const employees = <?php echo Illuminate\Support\Js::from($employees) ?>;
  const allEmployees = <?php echo Illuminate\Support\Js::from($all_employees) ?>;

  const app = new Vue({
    el: '#kt_content_container',
    data() {
      return {
        employees,
        allEmployees,
        model: {
          number: '{{ $number }}',
          date: '{{ date("Y-m-d") }}',
          type: 'cetak',
          jobOrderNumber: '',
          order: '',
          deliveryDate: '{{ date("Y-m-d") }}',
          customer: '',
          orderQuantity: 0,
          differenceNote: '',
          title: '',
          preparedBy: '{{ Auth::user()->employee->id ?? "" }}',
          submittedBy: '',
          knownBy: '',
        },
        memberPic: [{
          employeeId: '',
          description: '',
          clockIn: '',
          clockOut: '',
          overtime: 0,
          type: 'pic',
        }],
        members: [],
        submitLoading: false,
        getNumberLoading: false,
      }
    },
    computed: {
      disabledSaveButton() {
        if (this.memberValidations.filter(validation => validation.message).length > 0) {
          return true;
        }

        if (this.picValidations.filter(validation => validation.message).length > 0) {
          return true;
        }

        return false;
      },
      picValidations() {
        return this.validateItems([...this.memberPic], [...this.memberPic, ...this.members]);
      },
      memberValidations() {
        return this.validateItems([...this.members], [...this.memberPic, ...this.members]);
      },
      picOvertimes() {
        return this.getItemOvertimes(this.memberPic);
      },
      memberOvertimes() {
        return this.getItemOvertimes(this.members);
      },
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
      getDayFromDate() {
        const days = [
          'Senin',
          'Selasa',
          'Rabu',
          'Kamis',
          'Jumat',
          'Sabtu',
          'Minggu'
        ];

        const date = this.model.date;

        const dayIndex = moment(date).format('E');

        return days[dayIndex - 1];
      }
    },
    methods: {
      validateItems(items, duplicationItems) {
        const employeeIdCount = {};
        duplicationItems.forEach(item => {
          if (item.employeeId) {
            if (!employeeIdCount[item.employeeId]) {
              employeeIdCount[item.employeeId] = 0;
            }
            employeeIdCount[item.employeeId]++;
          }
        });

        return items.map((item, index, arr) => {
          let message = '';

          if (item.employeeId && employeeIdCount[item.employeeId] > 1) {
            message = 'Pegawai tidak boleh sama';
          }

          if (!item.clockOut) {
            message = 'Jam keluar harus diisi';
          }

          if (!item.clockIn) {
            message = 'Jam masuk harus diisi';
          }

          if (!item.description) {
            message = 'Katerangan harus diisi';
          }

          if (!item.employeeId) {
            message = 'Pegawai harus dipilih';
          }

          return {
            message: message,
          }
        });
      },
      async getNumber() {
        const self = this;
        try {
          self.getNumberLoading = true;

          const response = await axios.get(`/overtime-applications-v2/number?date=${self.model.date}`);
          if (response) {
            self.model.number = response?.data?.data || '';
          }
        } catch (error) {
          let message = error?.response?.data?.message;
          if (!message) {
            message = 'Something wrong...'
          }
          toastr.error(message);
        } finally {
          self.getNumberLoading = false;
        }
      },
      getItemOvertimes(items) {
        return items.map(item => {
          if (item.clockIn && item.clockOut) {
            const diffInMinutes = moment('2020-01-01 ' + item.clockOut).diff(moment('2020-01-01 ' + item.clockIn), 'minutes');
            return diffInMinutes;
          }
          return 0;
        })
      },
      // COMPANY METHODS
      async save() {
        let self = this;
        try {
          if (this.model.type != "other") {
            if (this.model.orderQuantity == "" || this.model.orderQuantity == 0) {
              Swal.fire({
                title: 'Kesalahan',
                text: "Qty tidak boleh kosong atau nol",
                icon: 'warning',
              });
              return;
            }
          }

          self.submitLoading = true;

          const {
            model
          } = self;

          let nonOtherAttributes = {
            job_order_number: model.jobOrderNumber,
            order: model.order,
            delivery_date: model.deliveryDate,
            customer: model.customer,
            order_quantity: model.orderQuantity,
          }

          let otherAttributes = {
            title: model.title,
          }

          if (model.type == "other") {
            nonOtherAttributes = {};
          } else {
            otherAttributes = {};
          }

          const attributes = {
            ...nonOtherAttributes,
            ...otherAttributes
          };

          const combinedMembers = [
            ...self.memberPic.map((pic, index) => ({
              ...pic,
              overtime: self.picOvertimes[index],
            })),
            ...self.members.map((member, index) => ({
              ...member,
              overtime: self.memberOvertimes[index],
            })),
          ];

          const response = await axios.post('/overtime-applications-v2', {
            number: model.number,
            date: model.date,
            type: model.type,
            difference_note: model.differenceNote,
            ...attributes,
            prepared_by: model.preparedBy,
            submitted_by: model.submittedBy,
            known_by: model.knownBy,
            members: combinedMembers,
          });

          if (response) {
            console.log(response)
            let message = response?.data?.message;
            if (!message) {
              message = 'Data berhasil disimpan'
            }

            const data = response?.data?.data;
            toastr.success(message + '. Mengalihkan..');

            document.location.href = "/overtime-applications-v2";
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
      addMember() {
        const memberData = {
          employeeId: '',
          description: '',
          clockIn: '',
          clockOut: '',
          overtime: 0,
          type: 'member',
        }

        this.members.push(memberData);
      },
      deleteMember(index) {
        this.members.splice(index, 1);
      },
    },
    watch: {
      'model.date': function(newValue, oldValue) {
        this.getNumber();
      }
    },
    components: {
      select2: {
        // props: ['options', 'value', 'defaultoption'],
        props: {
          options: {
            default: [],
          },
          value: {
            default: '',
          },
          defaultoption: {
            required: false,
            default: 'Pilih Pegawai',
          },
        },
        template: `
        <select @change="$emit('input', $event.target.value)" class="form-select form-select-sm">
            <option value="">@{{ defaultoption }}</option>
          <option v-for="option in options" :key="option.id" :value="option.id">
            @{{ option.name }}
          </option>
        </select>
      `,
        mounted() {
          const selectOptions = {
            ...this.options,
          };

          $(this.$el)
            .select2(selectOptions)
            .val(this.value)
            .trigger('change')
            .on('change', (e) => {
              this.$emit('input', e.target.value);
            });
        },
        watch: {
          value(newValue) {
            $(this.$el).val(newValue).trigger('change');
          },
          options() {
            $(this.$el).select2('destroy').select2(this.options);
          },
        },
        destroyed() {
          $(this.$el).off().select2('destroy');
        },
      },
    }
  })
</script>
@endsection