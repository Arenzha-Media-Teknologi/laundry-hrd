$(function () {
    function sendDeactivateRequest(id) {
        // const self = this;
        return axios
            .post("/employees/" + id + "/deactivate")
            .then(function (response) {
                let message = response?.data?.message;
                if (!message) {
                    message = "Data berhasil disimpan";
                }
                // self.deleteOffice(id);
                // redrawDatatable();
                toastr.success(message);
                setTimeout(() => {
                    document.location.reload();
                }, 500);
            })
            .catch(function (error) {
                console.error(error);
                // console.log(error.data);
                let message = error?.response?.data?.message;
                if (!message) {
                    message = "Something wrong...";
                }
                toastr.error(message);
            });
    }

    function sendActivateRequest(id) {
        // const self = this;
        return axios
            .post("/employees/" + id + "/activate")
            .then(function (response) {
                let message = response?.data?.message;
                if (!message) {
                    message = "Data berhasil disimpan";
                }
                // self.deleteOffice(id);
                // redrawDatatable();
                toastr.success(message);
                setTimeout(() => {
                    document.location.reload();
                }, 500);
            })
            .catch(function (error) {
                console.error(error);
                // console.log(error.data);
                let message = error?.response?.data?.message;
                if (!message) {
                    message = "Something wrong...";
                }
                toastr.error(message);
            });
    }

    $("#btn-deactivate").on("click", function () {
        const id = $(this).attr("data-id");
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Pegawai akan dinonaktifkan",
            icon: "warning",
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonText: "Nonaktifkan",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light",
            },
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return sendDeactivateRequest(id);
            },
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
        });
    });

    $("#btn-activate").on("click", function () {
        const id = $(this).attr("data-id");
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Pegawai akan diaktifkan",
            icon: "warning",
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonText: "Aktifkan",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-light",
            },
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return sendActivateRequest(id);
            },
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop: true,
        });
    });
});
