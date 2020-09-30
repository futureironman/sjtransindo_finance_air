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
<div class=" text-center"><h3><u><b>  LAPORAN NERACA  </b></u></h3> <h6>Tanggal : <?= $tanggal_akhir ?></h6> <hr></div>
   
<div class="container-fluid page__container">
    <div class="row card-group-row">
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>AKTIVA</h6>
                <table class="table table-striped">

                  <!-- aktiva LANCAR -->
                  <tr>
                    <?php
                        $aktiva_lancar = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='3414d575-0457-f81a-a0e3-4be018a67be9'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $aktiva_lancar["nama"];?></td>
                    </tr>
                    
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='3414d575-0457-f81a-a0e3-4be018a67be9'  ORDER BY nomor ASC");
                    $total_aktiva_lancar = 0;
                    while($r=pg_fetch_array($tampil)){
                        $total_aktiva=0;
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'");
                        while($a = pg_fetch_array($tampil2)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $debet - $kredit;
                        $total_aktiva+=$saldo;
                        }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $r['nomor'].' - '.$r["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_aktiva);?></td>
                    </tr>
                    <?php
                    $tampil3=pg_query($conn,"SELECT uid as uid_akun, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'  ORDER BY nomor ASC");
                    // echo "SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'";
                    while($row=pg_fetch_array($tampil3)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $sum_debet["debet"];
                        $kredit= $sum_kredit["kredit"];
                        $saldo = $debet - $kredit;
                        if($saldo != 0){
                            
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $row['nomor'].'-'.$row["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                        $total_aktiva_lancar+=$total_aktiva;
                        }
                    }
                    ?>
                    <tr>
                        <?php $hasil_akhir_aktiva_lancar = $total_aktiva_lancar; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $aktiva_lancar["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_akhir_aktiva_lancar);?></td>
                    </tr>
                    <!-- END aktiva LANCAR -->


                    <!-- aktiva Tetap -->
                    <tr>
                    
                    <td class="font-weight-bold font-italic"></td>
                    </tr>
                    <tr>
                    <?php
                        $aktiva_tetap = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='547c0b6e-a43e-1b7d-1714-ff034f511b07'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $aktiva_tetap["nama"];?></td>
                    </tr>
                    
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='547c0b6e-a43e-1b7d-1714-ff034f511b07'  ORDER BY nomor ASC");
                    $total_aktiva_tetap = 0;
                    while($r=pg_fetch_array($tampil)){
                        $hasil_aktiva_tetap=0;
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'");
                        while($a = pg_fetch_array($tampil2)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $debet - $kredit;
                            $hasil_aktiva_tetap+=$saldo;
                        }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $r['nomor'].' - '.$r["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_aktiva_tetap);?></td>
                    </tr>
                    <?php
                    $tampil3=pg_query($conn,"SELECT uid as uid_akun, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'  ORDER BY nomor ASC");
                    // echo "SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'";
                    while($row=pg_fetch_array($tampil3)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $sum_debet["debet"];
                        $kredit= $sum_kredit["kredit"];
                        $saldo = $debet - $kredit;
                        if($saldo != 0){
                            
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $row['nomor'].'-'.$row["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $total_aktiva_tetap+=$hasil_aktiva_tetap;
                    }
                    ?>
                    <tr>
                        <?php $hasil_akhir_aktiva_tetap = $total_aktiva_tetap; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $aktiva_tetap["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_akhir_aktiva_tetap);?></td>
                    </tr>
                    <!-- END AKTIVA TETAP -->



                </table>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>PASIVA</h6>
                <table class="table table-striped">
                   

                     <!-- HUTANG LANCAR -->
                     <tr>
                    <?php
                        $hutang_lancar = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='e97aa038-cb58-755b-ca04-26f9b0c04e50'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $hutang_lancar["nama"];?></td>
                    </tr>
                    
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='e97aa038-cb58-755b-ca04-26f9b0c04e50'  ORDER BY nomor ASC");
                    $total_hutang_lancar = 0;
                    while($r=pg_fetch_array($tampil)){
                        $hasil_hutang_lancar=0;
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'");
                        while($a = pg_fetch_array($tampil2)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $kredit - $debet;
                        $hasil_hutang_lancar+=$saldo;
                        }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $r['nomor'].' - '.$r["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_hutang_lancar);?></td>
                    </tr>
                    <?php
                    $tampil3=pg_query($conn,"SELECT uid as uid_akun, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'  ORDER BY nomor ASC");
                    // echo "SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'";
                    while($row=pg_fetch_array($tampil3)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $kredit - $debet;
                        if($saldo != 0){
                            
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $row['nomor'].'-'.$row["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $total_hutang_lancar+=$hasil_hutang_lancar;
                    }
                    ?>
                    <tr>
                        <?php $hasil_akhir_hutang_lancar = $total_hutang_lancar; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $hutang_lancar["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_akhir_hutang_lancar);?></td>
                    </tr>
                    <!-- END HUTANG LANCAR -->

                    <!-- EKUITASR -->
                    <tr>
                    
                    <td class="font-weight-bold font-italic"></td>
                    </tr>
                    <tr>
                    <?php
                        $ekuitas = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='c89207f4-3ddf-b06e-8df9-cc71980b3324'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $ekuitas["nama"];?></td>
                    </tr>
                    
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='c89207f4-3ddf-b06e-8df9-cc71980b3324'  ORDER BY nomor ASC");
                    $total_ekuitas = 0;
                    while($r=pg_fetch_array($tampil)){
                        $hasil_ekuitas=0;
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'");
                        while($a = pg_fetch_array($tampil2)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $kredit - $debet;
                        $hasil_ekuitas+=$saldo;
                        }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $r['nomor'].' - '.$r["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_ekuitas);?></td>
                    </tr>
                    <?php
                    $tampil3=pg_query($conn,"SELECT uid as uid_akun, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'  ORDER BY nomor ASC");
                    // echo "SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'";
                    while($row=pg_fetch_array($tampil3)){
                            $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$row[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $sum_debet["debet"];
                            $kredit= $sum_kredit["kredit"];
                            $saldo = $kredit - $debet;
                        if($saldo != 0){
                            
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $row['nomor'].'-'.$row["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                        $total_ekuitas+=$hasil_ekuitas;
                        }
                    }
                    ?>
                    <tr>
                        <?php $hasil_akhir_ekuitas = $total_ekuitas; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $ekuitas["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_akhir_ekuitas);?></td>
                    </tr>
                    <!-- END EKUITAS -->



                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold font-italic">
                        TOTAL AKTIVA
                        </td>
                        
                        <?php $total_aktiva = $hasil_akhir_aktiva_lancar + $hasil_akhir_aktiva_tetap; ?>
                        <td class="font-weight-bold text-right"><?php echo formatAngka($total_aktiva);?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold font-italic">
                        TOTAL PASIVA
                        </td>
                        
                        <?php $total_pasiva = $hasil_akhir_hutang_lancar + $hasil_akhir_ekuitas; ?>
                        <td class="font-weight-bold text-right"><?php echo formatAngka($total_pasiva);?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
   </body>

   </html>
<?php }
?>