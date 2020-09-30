<?php
switch($_GET['act']){
default:
if(isset($_GET['tanggal_awal'])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
}
else{
    $tanggal_awal=$minggu_lalu;
    $tanggal_akhir=date("Y-m-d");
}

$a = pg_fetch_array(pg_query($conn, "SELECT nomor,nama FROM keu_akun WHERE uid='$_GET[uid]'"));
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buka Faktur Pembayaran</li>
                </ol>
            </nav>
            <h4 class="m-0">Buka Faktur <?= $a["nama"]?></h4>
        </div>
        <a href="tambah-bukafaktur-<?= $_GET["uid"] ?>"><button type="button" class="btn btn-info ml-3"><i class="fa fa-plus"></i> Tambah</button></a>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Awal</label>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal;?>">
                    </div>
                    <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Akhir</label>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit"><i class="material-icons text-primary">refresh</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
        <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="width:100%" id="example">
                            <thead>
                                <tr>
                                    <th width="50px">No.</th>
                                    <th>Tgl/Jam Pembayaran</th>
                                    <th>No Faktur</th>
                                    <th>Keterangan Detail</th>
                                    <th>Total</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <?php
                            $no = 1;

                            $grand_total = 0;
                            $grand_total_bayar = 0;
                            $tampil = pg_query($conn, "SELECT * from keu_buka_faktur where deleted_at is NULL and created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' and uid_akun='$_GET[uid]'");
                            while ($r = pg_fetch_array($tampil)) {
                                $a = explode(" ", $r['tanggal']);
                                $waktu = DateToIndo($a[0]) . ' ' . $a[1];
                                $b = explode(" ", $r['created_at']);
                                $waktu_input = DateToIndo($b[0]) . ' ' . $b[1];
                                // $supplier = pg_fetch_array(pg_query($conn, "SELECT nama FROM master_supplier WHERE uid='$r[uid_supplier]'"));
                                // $akun = pg_fetch_array(pg_query($conn, "SELECT  nama FROM keu_akun WHERE uid='$r[uid_akun_terima]'"));
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $waktu_input; ?></td>
                                    <td><?php echo $waktu; ?></td>
                                    <td><a href="view-bukafaktur-<?php echo $r["uid"]; ?>"><?php echo $r["no_faktur"]; ?></a></td>
                                    <td><?php $data = explode(',',$r["keterangan_detail"]); 
                                            foreach($data as $row){
                                                $a =pg_fetch_array(pg_query($conn, "SELECT nama FROM keu_akun where uid ='$row'"));
                                                $a = '['.$a["nama"].'] ';
                                                echo $a;
                                            }
                                        ?>
                                    </td>
                                    <td class="text-right"><?php echo formatAngka($r['total']); ?></td>
                                    <td>
                                        <a type="button" class="btn btn-warning btn-sm" href="edit-bukafaktur-<?php echo $r['uid']; ?>" title="Edit"><i class="fa fa-edit"></i></a>
                                        <button type="button" rel="tooltip" class="btn btn-sm btn-danger btnHapus-bukafaktur" data-original-title="Hapus" title="Hapus" id="<?= $r["uid"] ?>"><i class="fa fa-trash"></i></button>
                                    </td>

                                </tr>
                            <?php
                                $no++;

                                $grand_total += $r['total'];
                            }
                            ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">TOTAL</td>
                                    <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Hapus Terima Bayar


            $("body").on("click", ".btnHapus-bukafaktur", function() {
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
                                    document.location.href = 'aksi-hapus-bukafaktur-' + id;
                                });
                        } else {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
            });
        </script>
<?php
break;

case "tambah":
    include "tambah.php";
break;
case "edit":
    include "edit.php";
break;

case "view":
    include "view.php";
break;
case "cetakvue ":
    include "cetak.php";
break;

}
?>