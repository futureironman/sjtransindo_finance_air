<?php
if(isset($_GET['tanggal'])){
    $tanggal=$_GET['tanggal'];
}
else{
    $tanggal=$tgl_sekarang;
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
                        <input type="date" class="form-control" name="tanggal" value="<?php echo $tanggal;?>">
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
                    <?php
                    $grand_total=0;
                    $tampil=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun  WHERE uid_parent='11af7b2b-a15d-e47d-e585-ac55a125882c' AND deleted_at IS NULL ORDER BY nomor");
                    while($r=pg_fetch_array($tampil)){
                        $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL"));
                        if($c['ada']>0){
                            $saldo="";
                        }
                        else{
                            $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                            $saldo=formatAngka($a['saldo']);   
                            $grand_total+=$a['saldo'];
                        }
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo;?></td>
                        </tr>
                        <?php
                        $tampil2=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun  WHERE uid_parent='$r[uid]' AND deleted_at IS NULL ORDER BY nomor");
                        while($r2=pg_fetch_array($tampil2)){
                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL"));
                            if($c['ada']>0){
                                $saldo="";
                            }
                            else{
                                $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r2[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                                $saldo=formatAngka($a['saldo']);   
                                $grand_total+=$a['saldo'];
                            }
                            ?>
                            <tr>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r2['nomor'].' - '.$r2['nama'];?></td>
                                <td class="text-right"><?php echo $saldo;?></td>
                            </tr>
                            <?php
                            $tampil3=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL ORDER BY nomor");
                            $total=0;
                            while($r3=pg_fetch_array($tampil3)){
                                $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r3[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                                ?>
                                <tr>
                                    <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php echo $r3['nomor'].' - '.$r3['nama'];?></td>
                                    <td class="text-right"><?php echo formatAngka($a['saldo']);?></td>
                                </tr>
                                <?php
                                $total+=$a['saldo'];
                                $grand_total+=$a['saldo'];
                            }
                            ?>
                            <tr>
                                <td class="font-weight-bold font-italic">
                                &nbsp; &nbsp; &nbsp; &nbsp;  TOTAL
                                </td>
                                <td class="font-weight-bold text-right"><?php echo formatAngka($total);?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>PASIVA</h6>
                <table class="table table-striped">
                    <?php
                    $grand_total_pasiva=0;
                    $tampil=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun  WHERE uid_parent='31a2d2f5-9fba-f00f-065d-225dbdf0fbd3' AND deleted_at IS NULL OR uid_parent='c89207f4-3ddf-b06e-8df9-cc71980b3324' AND deleted_at IS NULL ORDER BY nomor");
                    while($r=pg_fetch_array($tampil)){
                        $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL"));
                        if($c['ada']>0){
                            $saldo="";
                        }
                        else{
                            $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                            $saldo=formatAngka($a['saldo']);   
                            $grand_total_pasiva+=$a['saldo'];
                        }
                        ?>
                        <tr>
                            <td><?php echo $r['nomor'].' - '.$r['nama'];?></td>
                            <td class="text-right"><?php echo $saldo;?></td>
                        </tr>
                        <?php
                        $tampil2=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL ORDER BY nomor");
                        while($r2=pg_fetch_array($tampil2)){
                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS ada FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL"));
                            if($c['ada']>0){
                                $saldo="";
                            }
                            else{
                                $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r2[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                                $saldo=formatAngka($a['saldo']);   
                                $grand_total_pasiva+=$a['saldo'];
                            }
                            ?>
                            <tr>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php echo $r2['nomor'].' - '.$r2['nama'];?></td>
                                <td class="text-right"><?php echo $saldo;?></td>
                            </tr>
                            <?php
                            $tampil3=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL ORDER BY nomor");
                            $total=0;
                            while($r3=pg_fetch_array($tampil3)){
                                $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r3[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                                ?>
                                <tr>
                                    <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<?php echo $r3['nomor'].' - '.$r3['nama'];?></td>
                                    <td class="text-right"><?php echo formatAngka($a['saldo']);?></td>
                                </tr>
                                <?php
                                $total+=$a['saldo'];
                                $grand_total_pasiva+=$a['saldo'];
                            }
                            ?>
                            <tr>
                                <td class="font-weight-bold font-italic">
                                &nbsp; &nbsp; &nbsp; &nbsp;  TOTAL
                                </td>
                                <td class="font-weight-bold text-right"><?php echo formatAngka($total);?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold font-italic">
                        GRAND TOTAL
                        </td>
                        <td class="font-weight-bold text-right"><?php echo formatAngka($grand_total);?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold font-italic">
                        GRAND TOTAL
                        </td>
                        <td class="font-weight-bold text-right"><?php echo formatAngka($grand_total_pasiva);?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>