<?php
switch ($_GET['act']) {
    default:
        if (isset($_GET['tanggal_awal'])) {
            $tanggal_awal = $_GET['tanggal_awal'];
            $tanggal_akhir = $_GET['tanggal_akhir'];
        } else {
            $tanggal_awal = date("Y-m-01");
            $tanggal_akhir = date("Y-m-d");
        }
?>

        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Persediaan Barang</li>
                        </ol>
                    </nav>
                    <h4 class="m-0">Persediaan Barang</h4>
                </div>
                <!-- <a href="tambah-terimabayar"><button type="button" class="btn btn-info ml-3"><i class="fa fa-plus"></i> Tambah</button></a> -->
            </div>
        </div>
<?php 
?>
        <div class="container-fluid page__container">
            <!-- <form action="">
                <div class="card card-form d-flex flex-column flex-sm-row">
                    <div class="card-form__body card-body-form-group flex">
                        <div class="form-group row">
                            <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Awal</label>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>">
                            </div>
                            <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Akhir</label>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                            </div>
                        </div>
                    </div>
                    <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit"><i class="material-icons text-primary">refresh</i></button>
                </div>
            </form> -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="width:100%" id="example">
                            <thead>
                                <tr>
                                    <th width="50px">No.</th>
                                    <th>Tangga Pemesanan</th>
                                    <th>Nama Pemesan</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Barang</th>
                                    <th>Harga Barang </th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <?php
                            $no = 1;

                            $jumlah_barang = 0;
                            $harga_satuan = 0;
                            $total = 0;
                            $tampil = pg_query($conn, "SELECT * from pemesanan_barang");
                            while ($r = pg_fetch_array($tampil)) {
                               $pegawai = pg_fetch_array(pg_query($conn, "SELECT nama FROM pegawai WHERE uid='$r[nama_pemesan]'"));
                               $barang = pg_fetch_array(pg_query($conn, "SELECT nama_barang FROM detail_barang WHERE id='$r[id_barang]'"));
                                
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo DateToIndo($r['alias_tanggal_pemesanan']); ?></td>
                                    <td><?php echo $pegawai['nama']; ?></td>
                                    <td><?php echo $barang['nama_barang']; ?></td>
                                    <td class="text-right"><?php echo $r['jumlah_permintaan']; ?></td>
                                    <td class="text-right"><?php echo formatAngka($r['harga_satuan']); ?></td>
                                    <td class="text-right"><?php echo formatAngka($r['total_harga']); ?></td>
                                    <td class="text-center">
                                       <?php if($r["verify"] == ''){echo "<spam class='btn btn-danger btn-sm'>Belum di Bayar"; }else { echo "<spam class='btn btn-warning btn-sm'>Lunas Pada Tanggal : $r[verify]"; }?>
                                    
                                    </td>
                                    <td class="text-center">
                                       <?php if($r["verify"] == ''){ ?>
                                            <button type="button" rel="tooltip" class="btn btn-sm btn-warning  btnVerify" data-original-title="Verify" title="Verify" id="<?= $r["id"] ?>"><i class="fa fa-check"></i></button>
                                       <?php }else { ?>
                                            <button type="button" rel="tooltip" class="btn btn-sm btn-danger  btnEdit" data-original-title="Edit" title="Edit" id="<?= $r["id"] ?>"><i class="fa fa-edit"></i></button>
                                       <?php }?>
                                    
                                    </td>

                                </tr>
                            <?php
                                $no++;

                                $jumlah_barang += $r['jumlah_permintaan'];
                                $harga_satuan += $r['harga_satuan'];
                                $total += $r['total_harga'];
                            }
                            ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">TOTAL</td>
                                    <td class="text-right font-weight-bold"><?php echo formatAngka($jumlah_barang); ?></td>
                                    <td class="text-right font-weight-bold"><?php echo formatAngka($harga_satuan); ?></td>
                                    <td class="text-right font-weight-bold"><?php echo formatAngka($total); ?></td>
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


            $("body").on("click", ".btnVerify", function() {
                var id = this.id;
                  $.ajax({
                     type: 'POST',
                     url: 'verify-persediaan',
                     data: 'id=' + id,
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
            $("body").on("click", ".btnEdit", function() {
                var id = this.id;
                  $.ajax({
                     type: 'POST',
                     url: 'edit-persediaan',
                     data: 'id=' + id,
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
        </script>
<?php
break;
    case "tambah":
    include "tambah.php";
break;
    case "view":
    include "view.php";
break;
}
?>