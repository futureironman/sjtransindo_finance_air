$(".btnTambah").click(function() {
    $.ajax({
        type: 'POST',
        url: 'tambah-coa',
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

$(".btnTambah2").click(function() {
    var id = this.id;
    $.ajax({
        type: 'POST',
        url: 'tambah-coa2',
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
            $("#form-modal").html(msg);
            $("#form-modal").modal('show');
        }
    });
});

$(".btnEdit").click(function() {
    var id = this.id;
    $.ajax({
        type: 'POST',
        url: 'edit-coa',
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

$(".btnSetSaldo").click(function() {
    var id = this.id;
    $.ajax({
        type: 'POST',
        url: 'saldoawal-coa',
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
            $("#form-modal").html(msg);
            $("#form-modal").modal('show');
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
            document.location.href = 'aksi-hapus-coa-' + id;
        }
    });
});