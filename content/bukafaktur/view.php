

<?php
$a = pg_fetch_array(pg_query($conn, "SELECT no_faktur, uid_akun,tanggal,nama  FROM keu_buka_faktur WHERE uid='$_GET[uid]'"));
$b = pg_fetch_array(pg_query($conn, "SELECT nama FROM keu_akun WHERE uid='$a[uid_akun]'"));

?>


<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="kirimbaayr">Buka Fakrut</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </nav>
            <h4 class="m-0">View Buka Faktur <?= $b["nama"]?></h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-tambah-bukafaktur">
    <!-- <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-12 card-body">
                <p><strong class="headings-color">Nomor Faktur</strong></p>
                
                <h3 style="color: red !important; font-weight: bold;">No. 
                    <?php echo $a["no_faktur"]?></h3>
            </div>
        </div>
    </div> -->
    
    <div class="card">
        <div class="card-body">
        <div class="table-responsive">
        <div class="panel panel-primary">
               <!--<div class="panel-heading navyblue"> INVOICE</div>-->
               <div class="panel-body">
               <div class="row invoice-list header col-sm-12">
                                 <div class="text-center corporate-id col-sm-3">
                                    <img src="images/logo.png" alt="" width="60%">
                                 </div>
                                 <div class="col-sm-4 text-center">
                                    <h4>PT. SUMATERA JAYA TRANSINDO</h4><hr>
                                    <small>KOMP. CLBC, JL. BOULEVARD BARAT BLOK R.10 NO.02, MEDAN</small>
                                    </ul>
                                 </div> 
                                 <div class="col-sm-5">
                                    <h4 class="text-center">FAKTUR INFO</h4><hr>
                                    <ul class="unstyled">
                                       <li>Faktur Number : <strong><?php echo $a["no_faktur"] ?></strong></li>
                                       <li>Faktur Date : <?php echo $a["tanggal"] ?></li>
                                       <li>Faktur Akun : <?php echo $b["nama"] ?></li>
                                       <li>Nama Penerima : <?php echo $a["nama"] ?></li>
                                    </ul>
                                 </div>
                              </div>
                   <table class="table table-striped table-hover">
                   <thead>
                                <tr>
                                    <th width="50px">No.</th>
                                    <th>Nama Akun</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <?php
                            $no = 1;

                            $grand_total = 0;
                            $tampil = pg_query($conn, "SELECT * from keu_buka_faktur_detail where deleted_at is NULL and no_faktur ='$a[no_faktur]' and uid_akun_bank='$a[uid_akun]'");
                            while ($r = pg_fetch_array($tampil)) {
                                $akun = pg_fetch_array(pg_query($conn, "SELECT  nama FROM keu_akun WHERE uid='$r[uid_akun_keperluan]'"));
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $akun["nama"]; ?></td>
                                    <td class="text-right"><?php echo formatAngka($r["jumlah"]); ?></td>
                                    <td class="text-left"><?php echo $r["keterangan"]; ?></td>
                                </tr>
                            <?php
                                $no++;

                                $grand_total += $r['jumlah'];
                            }
                            ?>
                            </tbody>
                            
                   </table><br>
                   <div class="row">
                       <div class="col-lg-4 invoice-block pull-right">
                           <ul class="unstyled amounts">
                               <li><strong>Grand Total :</strong><?php echo "Rp. " . formatAngka($grand_total); ?></li>
                           </ul>
                       </div>
                   </div>
                   <div class="text-center invoice-btn">
                       <button type="button" class="btn btn-danger btn-md btnCetak" id="<?php echo $_GET['uid'];?>"><i class="fa fa-print"></i> Cetak</button>
                   </div>
               </div>
           </div>
                    </div>
                </div>
            </div>
   
            <a href="bukafaktur-<?= $a["uid_akun"] ?>"  class="btn btn-warning btn-md"><i class="fa fa-chevron-left"></i> Kembali</a>
    </form>
</div>


<script type="text/javascript" src="addons/js/masking_form.js"></script><script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-bukafaktur-"+id, "popupWindow", "width=600,height=600,scrollbars=yes");
})
</script>