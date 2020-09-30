<?php
if(isset($_GET['tanggal_awal']) && isset($_GET["tanggal_akhir"])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
}
else{
    $tanggal_awal= date("Y-m-01");
    $tanggal_akhir=$tgl_sekarang;
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Neraca Keuangan</li>
                </ol>
            </nav>
            <h4 class="m-0">Laporan Neraca Keuangan</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <label class="col-md-2 pt-2 text-right">Per Tanggal</label>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal;?>">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" title="Reload" data-toggle="tooltip" data-placement="top"><i class="material-icons text-primary">refresh</i></button>
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <div class="row card-group-row">
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>AKTIVA</h6>
                <table class="table table-striped">
                    <!-- AKTIVA LANCAR -->
                    <tr>
                    <?php
                        $aktiva_lancar = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='3414d575-0457-f81a-a0e3-4be018a67be9'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $aktiva_lancar["nama"];?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0'");
                    $total_kas=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_kas+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='2da48470-cef5-2bca-0fb3-f85bf4bc58b0'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_kas);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'");
                    $total_bank=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_bank+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='2e57b1b3-875c-fa51-5b39-1945eca33202'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_bank);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='90dc67fb-71a3-bf14-958e-3feb14f7e450'");
                    $total_piutang_usaha=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_piutang_usaha+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='90dc67fb-71a3-bf14-958e-3feb14f7e450'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_piutang_usaha);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='90dc67fb-71a3-bf14-958e-3feb14f7e450'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='39cb84be-c33c-6f15-d690-3a6c0cbbd99b'");
                    $total_piutang_karyawan=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_piutang_karyawan+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='39cb84be-c33c-6f15-d690-3a6c0cbbd99b'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_piutang_karyawan);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='39cb84be-c33c-6f15-d690-3a6c0cbbd99b'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='54ea416e-dfa5-a5a3-6704-5b4d8bd0260c'");
                    $total_piutang_lain2=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_piutang_lain2+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='54ea416e-dfa5-a5a3-6704-5b4d8bd0260c'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_piutang_lain2);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='54ea416e-dfa5-a5a3-6704-5b4d8bd0260c'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='c07508f9-27d7-6d54-d180-e512c00c7c9c'");
                    $total_biaya=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_biaya+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='c07508f9-27d7-6d54-d180-e512c00c7c9c'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_biaya);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='c07508f9-27d7-6d54-d180-e512c00c7c9c'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='232df725-a344-472a-9e66-022c47611b21'");
                    $total_inventory=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_inventory+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='232df725-a344-472a-9e66-022c47611b21'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_inventory);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='232df725-a344-472a-9e66-022c47611b21'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='cf280894-41b2-8d49-3fa7-cfe2ca4f164c'");
                    $total_perkiraan_sementara=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_perkiraan_sementara+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='cf280894-41b2-8d49-3fa7-cfe2ca4f164c'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_perkiraan_sementara);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='cf280894-41b2-8d49-3fa7-cfe2ca4f164c'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    <tr>
                        <?php $total_aktiva_lancar = $total_kas + $total_bank + $total_piutang_usaha + $total_piutang_karyawan + $total_piutang_lain2 + $total_biaya + $total_inventory + $total_perkiraan_sementara ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $aktiva_lancar["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_aktiva_lancar);?></td>
                    </tr>
                    <!-- END AKTIVA LANCAR -->

                    <!-- AKTIVA TETAP -->
                    <tr>
                    <?php
                        $aktiva_tetap = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='547c0b6e-a43e-1b7d-1714-ff034f511b07'"));
                    ?>
                        <td class="font-weight-bold font-italic"><?php echo $aktiva_tetap["nama"];?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='df562c24-4212-e630-2519-e6a837631384'");
                    $total_inventaris=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_inventaris+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='df562c24-4212-e630-2519-e6a837631384'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_inventaris);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='df562c24-4212-e630-2519-e6a837631384'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    } 
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='ee912c5e-19d6-c3f9-17db-3c3615529887'");
                    $total_kendaraan=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_kendaraan+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='ee912c5e-19d6-c3f9-17db-3c3615529887'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_kendaraan);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='ee912c5e-19d6-c3f9-17db-3c3615529887'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    }
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='9df0f5c4-372d-392d-3be8-d958478e936a'");
                    $total_gedung=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        ?>
                        <?php
                        $total_gedung+=$saldo;
                    }
                    $data = pg_fetch_array(pg_query($conn, "SELECT nama, nomor FROM keu_akun WHERE deleted_at IS NULL AND uid='9df0f5c4-372d-392d-3be8-d958478e936a'"))
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $data['nomor'].' - '.$data["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_gedung);?></td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='9df0f5c4-372d-392d-3be8-d958478e936a'");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $a["debet"];
                        $kredit= $a["kredit"];
                        $saldo = $debet - $kredit;
                        if($saldo != 0){
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r['nomor'].'-'.$r["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                    } 
                    ?>
                     <tr>
                        <?php $total_aktiva_tetap = $total_inventaris + $total_kendaraan + $total_gedung; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $aktiva_tetap["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_aktiva_tetap);?></td>
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
                        $total_hutang=0;
                        $tampil2=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'");
                        while($a = pg_fetch_array($tampil2)){
                            $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $a["debet"];
                            $kredit= $a["kredit"];
                            $saldo = $debet - $kredit;
                        $total_hutang+=$saldo;
                        }
                    ?>
                    <tr>
                        <td class="font-weight-bold font-italic"><?php echo $r['nomor'].' - '.$r["nama"];?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_hutang);?></td>
                    </tr>
                    <?php
                    $tampil3=pg_query($conn,"SELECT uid as uid_akun, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'  ORDER BY nomor ASC");
                    // echo "SELECT uid, nama, nomor, saldo_terkini FROM keu_akun WHERE deleted_at IS NULL AND uid_parent='$r[uid]'";
                    while($row=pg_fetch_array($tampil3)){
                        $b=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$row[uid_akun]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $b["debet"];
                        $kredit= $b["kredit"];
                        $saldo = $debet - $kredit; 
                        if($saldo != 0){
                            
                        ?>
                        <tr>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $row['nomor'].'-'.$row["nama"];?></td>
                            <td class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        }
                        $total_hutang_lancar+=$total_hutang;
                        }
                    }
                    ?>
                    <tr>
                        <?php $hasil_akhir = $total_hutang_lancar; ?>
                        <td class="font-weight-bold font-italic">TOTAL <?= $hutang_lancar["nama"] ?></td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($hasil_akhir);?></td>
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
                            $a=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$a[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                            $debet= $a["debet"];
                            $kredit= $a["kredit"];
                            $saldo = $debet - $kredit;
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
                        $b=pg_fetch_array(pg_query($conn,"SELECT debet, kredit FROM keu_akun_log WHERE uid_akun='$row[uid_akun]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $debet= $b["debet"];
                        $kredit= $b["kredit"];
                        $saldo = $debet - $kredit; 
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
                        
                        <?php $total_aktiva = $total_aktiva_lancar + $total_aktiva_tetap; ?>
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
                        
                        <?php $total_pasiva = $hasil_akhir + $hasil_akhir_ekuitas; ?>
                        <td class="font-weight-bold text-right"><?php echo formatAngka($total_pasiva);?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>