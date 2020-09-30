<?php
if(isset($_GET['id_bulan'])){
    $id_bulan=$_GET['id_bulan'];
    $tahun=$_GET['tahun'];
}
else{
    $id_bulan=$bln_sekarang;
    $tahun=$thn_sekarang;
}

$tanggal_awal=date("$tahun-$id_bulan-01");
$tanggal_akhir=date("Y-m-t",strtotime($tanggal_awal));
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan Laba Rugi</li>
                </ol>
            </nav>
            <h4 class="m-0">Laporan Laba Rugi </h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <label class="col-md-2 pt-2 text-right">Bulan</label>
                    <div class="col-md-4">
                        <select name="id_bulan" class="form-control">
                            <?php
                            $tampil=pg_query($conn,"SELECT * FROM bulan");
                            while($r=pg_fetch_array($tampil)){
                                if($r['id']==$id_bulan){
                                    echo"<option value='$r[id]' selected>$r[nama]</option>";
                                }
                                else{
                                    echo"<option value='$r[id]'>$r[nama]</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <label class="col-md-2 pt-2 text-right">Tahun</label>
                    <div class="col-md-4">
                        <input type="number" class="form-control" name="tahun" value="<?php echo $tahun;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" title="Reload" data-toggle="tooltip" data-placement="top"><i class="material-icons text-primary">refresh</i></button>
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0 btnCetak" id="<?php echo $tanggal_awal.','.$tanggal_akhir;?>" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <?php
    $laba = 0;
    ?>
    <div class="card">
        <div class="card-body">
            <b>PENDAPATAN</b>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
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

                        if($saldo!=0){
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
            <b>PEMBELIAN</b>
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

                        if($saldo!=0){
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
                        <td width="200px" class="text-right font-weight-bold"><?php echo  ($total_pembelian >= 0 ) ? formatAngka($total_pembelian) : "(" . formatAngka(substr($total_pembelian, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA KOTOR </td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>
            <b>BEBAN OPERASIONAL</b>
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

                        if($saldo!=0){
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
            <b>BEBAN UMUM & ADM</b>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini, jenis_akun FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f' ORDER BY nomor");
                    //$tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini, a.jenis_akun FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
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
                        if($saldo!=0){
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
                            if($saldo!=0){
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
                        <td class="text-right font-weight-bold"><?php echo  ($beban_adm >= 0 ) ? formatAngka($beban_adm) : "(" . formatAngka(substr($beban_adm, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA SEBELUM PAJAK</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>

            <b>PAJAK</b>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    //$tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini, jenis_akun FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='dd6fcc59-fd10-01b2-3054-5d90ec4e60f8'");
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
                        if($saldo!=0){
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
                        <td class="font-weight-bold font-italic">TOTAL PAJAK</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo  ($total_pajak >= 0 ) ? formatAngka($total_pajak) : "(" . formatAngka(substr($total_pajak, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <td class="font-weight-bold font-italic">LABA SEBELUM PENDAPATAN LAIN LAIN</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba);?></td>
                    </tr>
                </table>
            </div>

            <b>PENDAPATAN LAIN LAIN</b>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    //$tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini, jenis_akun FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9' ORDER BY nomor");
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
                        if($saldo!=0){
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
                        <td class="font-weight-bold font-italic">TOTAL PENDAPATAN LAIN LAIN</td>
                        <td width="200px" class="text-right font-weight-bold"><?php echo  ($total_lain2 >= 0 ) ? formatAngka($total_lain2) : "(" . formatAngka(substr($total_lain2, 1)) . ")";?></td>
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


<script type="text/javascript" src="addons/js/masking_form.js"></script><script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-laplabarugi?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>", "popupWindow", "width=600,height=600,scrollbars=yes");
})
</script>

<?php
$a=pg_fetch_array(pg_query($conn,"SELECT * FROM keu_akun_bulan_labarugi WHERE id_bulan='$id_bulan' AND tahun='$tahun' AND id_divisi='$_SESSION[divisi]'"));
if($a['id']!=''){
    $sql="UPDATE keu_akun_bulan_labarugi SET saldo='$laba', created_at='$waktu_sekarang' WHERE id='$a[id]'";
}
else{
    $sql="INSERT INTO keu_akun_bulan_labarugi (id_bulan, tahun, saldo, created_at, id_divisi) VALUES ('$id_bulan', '$tahun', '$laba', '$waktu_sekarang', '$_SESSION[divisi]')";
}
pg_query($conn,$sql);
?>