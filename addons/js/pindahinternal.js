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
            document.location.href = 'aksi-hapus-pindahinternal-' + id;
        }
    });
});

$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-pindahinternal-" + id, "popupWindow", "width=600,height=600,scrollbars=yes");
});