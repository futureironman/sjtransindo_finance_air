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
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0 btnCetak" id="<?php echo $tanggal_awal.','.$tanggal_akhir;?>" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <h6>PENDAPATAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='c32c7fe0-8995-6627-2fe6-bcbf90edc307'");
                    $total_pendapatan=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $total_pendapatan+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PENDAPATAN</td>
                        <td width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($total_pendapatan);?></td>
                    </tr>
                </table>
            </div>
            <h6>PEMBELIAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='7df866d0-d493-68d8-2dde-419e265ebbda'");
                    $total_pembelian=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $total_pembelian+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PEMBELIAN</td>
                        <td width="200px" class="text-right"><?php echo  ($total_pembelian >= 0 ) ? formatAngka($total_pembelian) : "(" . formatAngka(substr($total_pembelian, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <?php $laba_kotor = $total_pendapatan - $total_pembelian; ?>
                        <td colspan="3" class="font-weight-bold font-italic">LABA KOTOR</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba_kotor);?></td>
                    </tr>
                </table>
            </div>
            <h6>BEBAN OPERASIONAL</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='b1fa317d-d2b1-f990-73e9-814706b766f9'");
                    $beban_operasional=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $beban_operasional+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL BEBAN OPERASIONAL</td>
                        <td width="200px" class="text-right"><?php echo  ($beban_operasional >= 0 ) ? formatAngka($beban_operasional) : "(" . formatAngka(substr($beban_operasional, 1)) . ")";?></td>
                    </tr>
                </table>
            </div>
            <h6>BEBAN UMUM & ADM</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f'");
                    $beban_adm=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $beban_adm+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL BEBAN UMUM DAN ADMINISTRASI</td>
                        <td width="200px" class="text-right"><?php echo  ($beban_adm >= 0 ) ? formatAngka($beban_adm) : "(" . formatAngka(substr($beban_adm, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <?php $laba_sebelum_pajak = $laba_kotor - ($beban_operasional + $beban_adm)?>
                        <td colspan="3" class="font-weight-bold font-italic">LABA SEBELUM PAJAK</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba_sebelum_pajak);?></td>
                    </tr>
                </table>
            </div>

            <h6>PAJAK</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='dd6fcc59-fd10-01b2-3054-5d90ec4e60f8'");
                    $total_pajak=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $total_pajak+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PAJAK</td>
                        <td width="200px" class="text-right"><?php echo  ($total_pajak >= 0 ) ? formatAngka($total_pajak) : "(" . formatAngka(substr($total_pajak, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <?php $laba_sebelum_pendapatan_lain = $laba_sebelum_pajak - $total_pajak?>
                        <td colspan="3" class="font-weight-bold font-italic">LABA SEBELUM PENDAPATAN LAIN LAIN</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba_sebelum_pendapatan_lain);?></td>
                    </tr>
                </table>
            </div>

            <h6>PENDAPATAN LAIN LAIN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT uid, nama, nomor, saldo_terkini FROM keu_akun a WHERE deleted_at IS NULL AND uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9'");
                    $total_lain2=0;
                    while($r=pg_fetch_array($tampil)){
                        $sum_debet=pg_fetch_array(pg_query($conn,"SELECT SUM(debet) as debet FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $sum_kredit=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) as kredit FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at between '$tanggal_awal 00:00:00' and '$tanggal_akhir 23:59:59'"));
                        $kredit= $sum_kredit["kredit"];
                        $debet= $sum_debet["debet"];
                        $saldo =$kredit - $debet;
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo  ($saldo >= 0 ) ? formatAngka($saldo) : "(" . formatAngka(substr($saldo, 1)) . ")";?></td>
                        </tr>
                        <?php
                        $total_lain2+=$saldo;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PENDAPATAN LAIN LAIN</td>
                        <td width="200px" class="text-right"><?php echo  ($total_lain2 >= 0 ) ? formatAngka($total_lain2) : "(" . formatAngka(substr($total_lain2, 1)) . ")";?></td>
                    </tr>
                </table>
                <table class="table table-striped">
                    <tr>
                        <?php $laba_bersih = $laba_sebelum_pendapatan_lain + $total_lain2?>
                        <td colspan="3" class="font-weight-bold font-italic">LABA BERSIH</td>
                        <td  width="200px" class="text-right font-weight-bold">Rp. <?php echo formatAngka($laba_bersih);?></td>
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