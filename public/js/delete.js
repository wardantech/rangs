$(document).on("submit", '.delete', function (e) {
    //This function use for sweetalert confirm message
    e.preventDefault();
    var form = this;

    swal({
        title: "Are you sure you want to Delete?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            form.submit();
        }
    });
});
