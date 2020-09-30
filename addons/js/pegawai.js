$(".btnTambah").click(function() {
    $.ajax({
        type: 'POST',
        url: 'tambah-pegawai',
        data: {

        },
        beforeSend: function() {
            $(".preloader").show();
        },
        complete: function() {
            $(".preloader").hide();
        },
        success: function(msg) {
            $("#form-modal").html(msg);
            $("#form-modal").modal('show');
        }
    });
});
$(".btnEdit").click(function() {
    var id = this.id;
    $.ajax({
        type: 'POST',
        url: 'edit-pegawai',
        data: {
            'id': id
        },
        beforeSend: function() {
            $(".preloader").show();
        },
        complete: function() {
            $(".preloader").hide();
        },

        success: function(msg) {
            $("#form-modal2").html(msg);
            $("#form-modal2").modal('show');
        }
    });
});

$(".btnHapus").click(function() {
    var id = this.id;
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            document.location.href = 'aksi-hapus-pegawai-' + id;
        }
    });
});