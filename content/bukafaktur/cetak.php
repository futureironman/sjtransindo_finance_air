<?php
session_start();
error_reporting(0);
if (empty($_SESSION['login_user'])) {
   header("location:keluar");
} else {
   include "../../konfig/koneksi.php";
   include "../../konfig/library.php";
   include "../../konfig/fungsi_angka.php";
   $a = pg_fetch_array(pg_query($conn, "SELECT no_faktur, uid_akun,tanggal,nama  FROM keu_buka_faktur WHERE uid='$_GET[uid]'"));
   $b = pg_fetch_array(pg_query($conn, "SELECT nama FROM keu_akun WHERE uid='$a[uid_akun]'"));

?>
   <html>

   <head>

      <!-- Bootstrap core CSS -->
      <link type="text/css" href="assets/vendor/datatable/bootstrap.css" rel="stylesheet">
      <!-- <link type="text/css" href="assets/vendor/datatable/dataTables.bootstrap4.min.css" rel="stylesheet"> -->
      <script>
		function myFunction() {
			window.print();
			setTimeout(window.close, 0);
		}
		</script>
<style type='text/css'>


p{
    padding: 0;
    margin: 0;
}
    
    
        @page {
            
            size: 8in 5in;
            margin:0;
            padding:0;
            /* margin: 1mm 1mm 1mm 1mm;  */
        }
    @media print{
        #not_important{
            visibility: hidden;
            display: none;
        }
        img{width:200px; height:80px;}

        

        body{
            font-size: 18px;
            font-family: "Times new roman";
            color:#000; font-weight: bold;
        }
        
   .footer_cetak  {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   text-align: center;
   padding: 10;
   margin: 10;
   font-weight : bold;
   }
    }


   
   .page:after {
  content: counter(page);
}
.padding_right {
   padding-right: 10;
}
.padding_left{
   padding-left: 7;
}

.header {
  overflow: hidden;
  background-color: #f1f1f1;
  padding: 20px 10px;
  
}

.scroll {
  /* border: 1px solid black; */
  width: 100%;
  height: 250px;
}
.scroll_lebih {
  /* border: 1px solid black; */
  width: 100%;
  height: 290px;
}
</style>
   </head>
<?php
$cek =pg_fetch_array(pg_query($conn, "SELECT COUNT(no_faktur) as noFaktur from keu_buka_faktur_detail where deleted_at is NULL and no_faktur ='$a[no_faktur]' and uid_akun_bank='$a[uid_akun]'"));
$jumlah_data = $cek["nofaktur"];
$ulang = 1;
$start = 0;
$end = $jumlah_data;
if($jumlah_data>=10 && $start ==0)
    $end = 10;
$row = 10;
$used = 0;
for($i=0; $i<$jumlah_data; $i++){
    if($i>$row-1){
        $ulang+=1;
        $row+=10;
    }
}

for($i=0; $i<$ulang; $i++){
?>
   <body onload="myFunction()">
         <div class="card">
            <div class="card-body">
                  <div class="panel panel-primary">
                     <div class="panel-body" style="padding-top:10px;">
                              <div class="row invoice-list header col-sm-12" >
                                 <div class="text-center corporate-id col-sm-3">
                                    <img src="images/logo.png" alt="" width="35%">
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
                  <!-- <table border="1" width="100%" style="border-color:#000;">
                     <tr  height="250px">
                        <td > -->
                     <?php if($jumlah_data > 10) { ?><div class="scroll_lebih"> <?php } else{ ?><div class="scroll"> <? } ?>
                        <table border="1" width="100%" style="border-color:#000;">
                           <thead>
                              <tr  class="fs20 text-center" style="color:#000; font-weight:bold;" width="50px">
                                 <th>No.</th>
                                 <th>Nama Akun</th>
                                 <th width="35%">Keterangan</th>
                                 <th width="20%">Jumlah</th>
                              </tr>
                           </thead>
                           <?php
                           $no = 1;
                           $tampil = pg_query($conn, "SELECT a.*, b.nama from keu_buka_faktur_detail a, keu_akun b where a.deleted_at is NULL and a.no_faktur ='$a[no_faktur]' and a.uid_akun_bank='$a[uid_akun]'and  a.uid_akun_keperluan=b.uid");
                           $tampil = pg_fetch_all($tampil);
                           for($k=$start; $k<$end; $k++){
                              if($tampil[$k] != null){
                           ?>
                              <tr class="fs20" style="color:#000; font-weight:bold;">
                                 <td class="padding_left"><?php echo $k+1; ?></td>
                                 <td class="padding_left"><?php echo $tampil[$k]["nama"]; ?></td>
                                 <td class="padding_left"><?php echo $tampil[$k]["keterangan"]; ?></td>
                                 <td class="text-right padding_right"><?php echo formatAngka($tampil[$k]["jumlah"]); ?></td>
                              </tr>
                           <?php
                              $no++;
                              $used++;
                              }
                           }
                           ?>
                           </tbody>
                        
                        <?php if($i==$ulang-1)  {
                           $grand_total='';
                            $tampil = pg_query($conn, "SELECT jumlah from keu_buka_faktur_detail  where deleted_at is NULL and no_faktur ='$a[no_faktur]' and uid_akun_bank='$a[uid_akun]'");
                            while($r=pg_fetch_array($tampil)){
                               $grand_total += $r["jumlah"];
                            }
                           ?>
                        
                           <tfoot> 
                                <tr>
                                    <td colspan="3" class="text-center">TOTAL</td>
                                    <td class="text-right font-weight-bold padding_right"><?php echo formatAngka($grand_total); ?></td>
                                </tr>
                            </tfoot>
                           <?php } ?>
                        </table>
                           <!-- </td></tr></table> -->
                           </div>
                           <?php if($i == $ulang-1) {?>
                        <div class="row text-center ">
                           <div class="col-2 text-center">
                              <p>Disetujui Oleh,</p><br><br>(_____________)
                           </div>       
                           <div class="col-3 text-center">
                              <p>Diperiksa Oleh,</p><br><br>(______________)
                           </div>       
                           <div class="col-2 text-center">
                              <p>Kasir,</p><br><br>(_____________)
                           </div>       
                           <div class="col-2 text-center">
                              <p>Diterima Oleh,</p><br><br>(_____________)
                           </div>       
                           <div class="col-3 text-center">
                              <p>Dibukukan Oleh,</p><br><br>(______________)
                           </div>       
                        </div>
                        <?php } ?>  
                     </div>
                  </div>
               </div>
         </div>


         <?php
$start = $used;
if($jumlah_data-$used > 10){
    $end +=10;
}else{
    $end+= $jumlah_data-$used;
}
}
?>
   </body>

   </html>
<?php }
?>