

<?php
$a = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_pembayaran WHERE uid='$_GET[uid]'"));
$customer = pg_fetch_array(pg_query($conn, "SELECT b.nama as nama_customer, b.alamat, c.nama FROM keu_akun a, customer b, master_divisi c where a.uid='$a[uid_customer]' AND a.uid_data=CAST(b.uid AS UUID) AND b.id_divisi=c.id"));
$akun_terima = pg_fetch_array(pg_query($conn, "SELECT nama FROM keu_akun WHERE uid='$a[uid_akun_terima]'"));

?>


<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="terimabayar">Terima Bayar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </nav>
            <h4 class="m-0">View Transaksi Pelunasan Piutang</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-tambah-bukafaktur">
    <div class="card">
        <div class="card-body">
        <div class="">
        <div class="panel panel-primary">
               <!--<div class="panel-heading navyblue"> INVOICE</div>-->
               <div class="panel-body">
                   <div class="row invoice-list">
                       <div class="text-center corporate-id">
                           <img src="../../images/logo.png" alt="" width="40%">
                       </div>
                       <div class="col-lg-4 col-sm-6">
                           <h4>INVOICE INFO</h4>
                           <ul class="unstyled">
                               <li>Nomor Bukti		: <strong><?php echo $a["no_bukti"]?></strong></li>
                               <li>Tanggal Pembayaran		: <?php echo $a["tanggal"]?></li>
                               <li>Akun Pembayaran		: <?php echo $akun_terima["nama"]?></li>
                           </ul>
                       </div>
                       <div class="col-lg-4 col-sm-6">
                           <h4>CUSTOMER INFO</h4>
                           <ul class="unstyled">
                               <li>Nama		: <?php echo $customer["nama_customer"]?></li>
                               <li>Alamat		: <?php echo $customer["alamat"]?></li>
                               <li>divisi		: <?php echo $customer["nama"]?></li>
                           </ul>
                       </div>
                   </div>
                   <table class="table table-striped table-hover">
                   <thead>
                                <tr>
                                    <th width="50px">No.</th>
                                    <th>Invoice Number</th>
                                    <th class="text-center">Jumlah Pembayaran</th>
                                </tr>
                            </thead>
                            <?php
                            $no = 1;
                            $grand_total = 0;   $splitID = explode(",",$a["uid_invoice_header"]);
                           
                            $jumlah = pg_query($conn, "SELECT jumlah FROM keu_detail_pembayaran WHERE no_bukti='$a[no_bukti]' and uid_customer='$a[uid_customer]' UNION ALL SELECT jumlah FROM keu_akun_payment_terima_kredit WHERE no_bukti='$a[no_bukti]' and tanggal='$a[tanggal]'");
                            $total= '';
                            while($row = pg_fetch_array($jumlah)){
                                $total += $row["jumlah"];
                            }

                                $invoice = pg_query($conn, "SELECT uid FROM keu_detail_pembayaran WHERE no_bukti='$a[no_bukti]' and uid_customer='$a[uid_customer]' UNION ALL SELECT uid FROM keu_akun_payment_terima_kredit WHERE no_bukti='$a[no_bukti]' and tanggal='$a[tanggal]'");
                            while ($r = pg_fetch_array($invoice)) {
                                $b = pg_fetch_array(pg_query($conn, "SELECT jumlah, no_invoice FROM keu_detail_pembayaran WHERE uid='$r[uid]'"));
                                $c = pg_fetch_array(pg_query($conn, "SELECT a.jumlah, b.nama FROM keu_akun_payment_terima_kredit a, keu_akun b WHERE a.uid='$r[uid]' and a.uid_akun=b.uid"));
                                if($b["jumlah"] != ''){
                                    $jumlah_b = $b["jumlah"];
                                }
                                else{
                                    $jumlah_b='';
                                }
                                if($c["jumlah"] != ''){
                                    $jumlah_c = $c["jumlah"];
                                }
                                else{
                                    $jumlah_c='';
                                }
                            ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $b["no_invoice"] . $c["nama"]; ?></td>
                                    <td class="text-right"><?php echo formatAngka($jumlah_b) . formatAngka($jumlah_c); ?></td>
                                </tr>
                            <?php
                                $no++;
                            }
                            ?>
                            </tbody>
                            
                   </table><br>
                   <div class="row">
                       <div class="col-lg-4 invoice-block pull-right">
                           <ul class="unstyled amounts">
                               <li><strong>Grand Total :</strong><?php echo "Rp. " . formatAngka($total); ?></li>
                           </ul>
                       </div>
                   </div>
                   <div class="text-center invoice-btn">
                       <!--<a class="btn btn-info btn-lg" href="cetak-terimabayar-<?= $_GET["uid"] ?>"><i class="fa fa-print"></i> Print </a>-->
                       <button type="button" class="btn btn-danger btn-md btnCetak" id="<?php echo $_GET['uid'];?>"><i class="fa fa-print"></i> Cetak</button>
                   </div>
               </div>
           </div>
                    </div>
                </div>
            </div>
   
        <a href="terimabayar" class="btn btn-warning btn-md"><i class="fa fa-chevron-left"></i> Kembali</a>
    </form>
</div>


<script type="text/javascript" src="addons/js/masking_form.js"></script>

<script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-terimabayar-"+id, "popupWindow", "width=600,height=600,scrollbars=yes");
})
</script>