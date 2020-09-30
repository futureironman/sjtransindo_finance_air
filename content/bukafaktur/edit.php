<?php

$a = pg_fetch_array(pg_query($conn, "SELECT no_faktur, uid_akun  FROM keu_buka_faktur WHERE uid='$_GET[uid]'"));
?>


<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="kirimbaayr">Buka FAKTUR</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h4 class="m-0">Edit Buka Faktur <?= $b["nama"]; ?></h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-edit-bukafaktur">
        <div class="card card-form">
            <div class="row no-gutters">
                <div class="col-lg-12 card-body">
                    <p><strong class="headings-color">Nomor Faktur</strong></p>
                    <!-- <p class="text-muted">* Wajib diisi</p> -->
                    <input type="hidden" name="no_faktur" id="no_faktur" value="<?php echo $a["no_faktur"] ?>">
                    <input type="hidden" id="uid" name="uid" value="<?= $_GET["uid"] ?>">
                    <input type="hidden" id="uid_akun_bank" name="uid_akun_bank" value="<?= $a["uid_akun"] ?>">

                    <h3 style="color: red !important; font-weight: bold;">No.
                        <?php echo $a["no_faktur"] ?></h3>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm btnTambah_detail" data-toggle="tooltip" data-placement="top" title="Tambah Akun">Tambah</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width:100%" id="example">
                        <thead>
                            <tr>
                                <th width="50px">No.</th>
                                <th>Nama Akun</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th width="50px">Aksi</th>
                            </tr>
                        </thead>
                        <?php
                        $no = 1;

                        $grand_total = 0;
                        $total = 0;
                        $tampil = pg_query($conn, "SELECT * from keu_buka_faktur_detail where deleted_at is NULL and (no_faktur ='$a[no_faktur]' OR no_faktur is null) and id_divisi='$_SESSION[divisi]' and uid_akun_bank='$a[uid_akun]'");
                        while ($r = pg_fetch_array($tampil)) {
                            $akun = pg_fetch_array(pg_query($conn, "SELECT  nama FROM keu_akun WHERE uid='$r[uid_akun_keperluan]'"));
                        ?>
                            <tr>
                                <td><?php echo $no; ?></td>
                                <td><?php echo $akun["nama"]; ?></td>
                                <td class="text-right"><?php echo formatAngka($r["jumlah"]); ?></td>
                                <td><?php echo $r["keterangan"]; ?></td>

                                <td>
                                    <button type="button" rel="tooltip" class="btn btn-sm btn-danger btnHapus-detailBukafaktur" data-original-title="Hapus" title="Hapus" id="<?= $r["uid"] ?>"><i class="fa fa-trash"></i></button>
                                    <?php if ($r["no_faktur"] == null) {
                                        $total += $r['jumlah']; ?>
                                        <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r['uid']; ?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                    <?php } ?>
                                </td>

                            </tr>
                        <?php
                            $no++;
                            $grand_total += $r['jumlah'];
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total); ?>
                                    <input type="hidden" name="total_pembayaran" value="<?= $grand_total ?>">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan dan Bayar</button>
        <a href="bukafaktur-<?= $a["uid_akun"] ?>" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>


<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
    // CRUD DETAIL
    $(function() {

        $(".btnTambah_detail").click(function() {
            var uid = $("#uid").val();
            var uid_akun_bank = $("#uid_akun_bank").val();
            $.ajax({
                type: 'POST',
                url: 'edit-tambah-bukafaktur',
                data: {
                    'uid': uid,
                    'uid_akun_bank': uid_akun_bank
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
            var uid_detail = this.id;
            var uid = $("#uid").val();
            $.ajax({
                type: 'POST',
                url: 'edit-edit-bukafaktur',
                data: {
                    'uid_detail': uid_detail,
                    'uid': uid
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

        $("body").on("click", ".btnHapus-detailBukafaktur", function() {
            var id = this.id;
            var uid = $("#uid").val();
            swal({
                    title: "Are you sure?",
                    text: "Data that has been deleted cannot be restored!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel ",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        swal({
                                title: 'Berhasil!',
                                text: 'Data tersebut telah dihapus',
                                type: 'success'
                            },
                            function() {
                                $.ajax({
                                    type: 'POST',
                                    url: 'edit-hapus-detail-bukafaktur',
                                    data: {
                                        'id': id
                                    },
                                    success: function(msg) {
                                        document.location.href = 'edit-bukafaktur-' + uid;
                                    }
                                });

                            });
                    } else {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        });
    });
</script>