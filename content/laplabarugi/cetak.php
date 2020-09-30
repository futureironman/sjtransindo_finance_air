<?php
session_start();
error_reporting(0);
if (empty($_SESSION['login_user'])) {
   header("location:keluar");
} else {
   include "../../konfig/koneksi.php";
   include "../../konfig/library.php";
   include "../../konfig/fungsi_angka.php";
   $tanggal_awal = $_GET["tanggal_awal"];
   $tanggal_akhir = $_GET["tanggal_akhir"];
?>
   <html>

   <head>
   <style type="text/css" media="print">
        @page 
        {
            size: auto;   /* auto is the current printer page size */
            margin: 0mm;  /* this affects the margin in the printer settings */
        }

        body 
        {
            background-color:#FFFFFF; 
            border: solid 1px black ;
            margin: 0px;  /* the margin on the content before printing */
       }
    </style>
      <!-- Bootstrap core CSS -->
      <link type="text/css" href="assets/vendor/datatable/bootstrap.css" rel="stylesheet">
      <!-- <link type="text/css" href="assets/vendor/datatable/dataTables.bootstrap4.min.css" rel="stylesheet"> -->
      <script>
		function myFunction() {
			window.print();
			setTimeout(window.close, 0);
		}
		</script>
   
   </head>

   <body onload="myFunction()">
        
<div class="container-fluid page__container">
<br><br>
<div class=" text-center"><h3><u><b>  LAPORAN LABA/RUGI  </b></u></h3> <h6>Tanggal : <?= $tanggal_akhir ?></h6> <hr></div>
    <div class="card">
        <div class="card-body">
            <h6>PENDAPATAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $laba = 0;
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='c32c7fe0-8995-6627-2fe6-bcbf90edc307' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor ASC");
                    $total_pendapatan=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $total_pendapatan-=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $total_pendapatan+=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }
                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic">TOTAL PENDAPATAN</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo formatAngka($total_pendapatan);?></td>
                    </tr>
                </table>
            </div>
            <h6>PEMBELIAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='7df866d0-d493-68d8-2dde-419e265ebbda' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    $total_pembelian=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $total_pembelian+=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $total_pembelian-=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }

                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic">TOTAL PEMBELIAN</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo formatAngka($total_pembelian);?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">LABA KOTOR</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>
            <h6>BEBAN OPERASIONAL</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='b1fa317d-d2b1-f990-73e9-814706b766f9' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    $beban_operasional=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $beban_operasional+=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $beban_operasional-=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }

                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic">TOTAL BEBAN OPERASIONAL</td>
                        <td class="text-right font-weight-bold"><?php echo  ($beban_operasional >= 0 ) ? formatAngka($beban_operasional) : "(" . formatAngka(substr($beban_operasional, 1)) . ")";?></td>
                    </tr>
                </table>
            </div>
            <h6>BEBAN UMUM & ADM</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                     $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini, jenis_akun FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f' ORDER BY nomor");
                    $beban_adm=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $beban_adm+=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $beban_adm-=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }
                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini, jenis_akun FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r[uid]' ORDER BY nomor");
                        
                        while($r2=pg_fetch_array($tampil2)){
                            $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r2[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $a["debet"];
                            $kredit= $a["kredit"];
                            
                            if($r['jenis_akun']=='D'){ 
                                $saldo = $debet - $kredit;
                                $beban_adm+=$saldo;
                                $saldo2='( '.formatAngka($saldo).' )';
                                $laba-=$saldo;
                            }
                            else{
                                $saldo = $kredit - $debet;
                                $beban_adm-=$saldo;
                                $saldo2=formatAngka($saldo);
                                $laba+=$saldo;
                            }
                            if($saldo>0){
                            ?>
                            <tr>
                                <td><?php echo $r2['nomor'].' - '.$r2['nama'];?></td>
                                <td class="text-right"><?php echo $saldo2;?></td>
                            </tr>
                            <?php
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic">TOTAL BEBAN UMUM DAN ADMINISTRASI</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo formatAngka($beban_adm);?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA SEBELUM PAJAK</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>

            <h6>PAJAK</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='dd6fcc59-fd10-01b2-3054-5d90ec4e60f8' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    $total_pajak=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $total_pajak+=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $total_pajak-=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }
                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PAJAK</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo formatAngka($total_pajak);?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA SEBELUM PENDAPATAN LAIN LAIN</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>

            <h6>PENDAPATAN LAIN LAIN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='50dfe8a6-9e98-202d-8317-903ada6b60c7' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    $total_lain2=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) AS debet, SUM(kredit) AS kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        if($r['jenis_akun']=='D'){ 
                            $saldo = $debet - $kredit;
                            $total_lain2+=$saldo;
                            $saldo2='( '.formatAngka($saldo).' )';
                            $laba-=$saldo;
                        }
                        else{
                            $saldo = $kredit - $debet;
                            $total_lain2-=$saldo;
                            $saldo2=formatAngka($saldo);
                            $laba+=$saldo;
                        }
                        if($saldo>0){
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo2;?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PENDAPATAN LAIN LAIN</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo formatAngka($total_lain2);?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA BERSIH</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
   </body>

   </html>
<?php }
?>