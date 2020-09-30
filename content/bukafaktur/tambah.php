

<?php
$a = pg_fetch_array(pg_query($conn, "SELECT nomor,nama FROM keu_akun WHERE uid='$_GET[uid]'"));
$akun=substr($a["nomor"], 2, 5);


$tgl_awal=$thn_sekarang.'-'.$bln_sekarang.'-01 00:00:00';
$tanggal_akhir=$thn_sekarang.'-'.$bln_sekarang.'-31 23:59:59';
$d = pg_fetch_array(pg_query($conn, "SELECT MAX(no_faktur) as nomor FROM keu_buka_faktur where uid_akun='$_GET[uid]' and id_divisi='$_SESSION[divisi]' and deleted_at is NULL "));


    $kode_before = substr($d['nomor'],0,11);
    $kode_now="U-$akun.$thn.";
    if($kode_before==$kode_now){
        $no_urut = (int) substr($d['nomor'],11,6);
        $no_urut++;
        $no_urut_baru = $kode_before.sprintf("%06s",$no_urut);
    }
    else{
        $no_urut_baru = $kode_now.sprintf("%06s",1);
    }


?>


<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="kirimbaayr">Buka FAKTUR</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
            <h4 class="m-0">Tambah Buka Faktur <?= $a["nama"]?></h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-tambah-bukafaktur">
    
    <input type="hidden" name ="no_faktur" id="no_faktur" value="<?php echo $no_urut_baru?>">
    <input type="hidden" id="uid_akun_bank" name="uid_akun_bank" value="<?= $_GET["uid"] ?>" >
    <input type="hidden" id="kode_akun" name="kode_akun" value="<?= $akun ?>" >
    <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-4 card-body">
                <p><strong class="headings-color">Nomor Faktur</strong></p>
                <!-- <p class="text-muted">* Wajib diisi</p> -->
                
                <h3 style="color: red !important; font-weight: bold;">No. 
                    <?php echo $no_urut_baru?></h3>
            </div>
            <div class="col-lg-8 card-form__body card-body">
            <div class="row form-group">
                        <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="tanggal" max="<?= date('Y-m-d')?>" required>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control" name="jam" max="<?= date('Y-m-d')?>" required>
                        </div>
                        <label class="col-md-4 pt-2">Nama<span class="red">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-sm btnTambah_detail" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun">Tambah</button>
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
                            $tampil = pg_query($conn, "SELECT * from keu_buka_faktur_detail where deleted_at is NULL and no_faktur is null and id_divisi='$_SESSION[divisi]' and uid_akun_bank='$_GET[uid]'");
                            while ($r = pg_fetch_array($tampil)) {
                                $akun = pg_fetch_array(pg_query($conn, "SELECT  nama FROM keu_akun WHERE uid='$r[uid_akun_keperluan]'"));
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $akun["nama"]; ?></td>
                                    <td class="text-right"><?php echo formatAngka($r["jumlah"]); ?></td>
                                    <td><?php echo $r["keterangan"]; ?></td>
                                    
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                        <button type="button" rel="tooltip" class="btn btn-sm btn-danger btnHapus-detailBukafaktur" data-original-title="Hapus" title="Hapus" id="<?= $r["uid"] ?>"><i class="fa fa-trash"></i></button>
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
                                        <input type="hidden" name="total_pembayaran" value="<?= $grand_total?>">
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
   
        <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan dan Bayar</button>
        <a href="bukafaktur-<?= $_GET["uid"] ?>" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>


<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
   
// CRUD DETAIL
$(function(){

    $(".btnTambah_detail").click(function() {
        var uid_akun_bank = $("#uid_akun_bank").val();
    $.ajax({
        type: 'POST',
        url: 'tambah-detail-bukafaktur',
        data: 'uid_akun_bank=' + uid_akun_bank,
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
        url: 'edit-detail-bukafaktur',
        data: {
            'uid': id
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
                        document.location.href = 'aksi-hapus-detail-bukafaktur-' + id;
                    });
            } else {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });
});
</script>