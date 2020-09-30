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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Laba Rugi</li>
                </ol>
            </nav>
            <h4 class="m-0">Laporan Laba Rugi</h4>
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
    <div class="card">
        <div class="card-body">
            <h6>PENDAPATAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='50dfe8a6-9e98-202d-8317-903ada6b60c7' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='c32c7fe0-8995-6627-2fe6-bcbf90edc307'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)ORDER BY a.nomor");
                    $total=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC LIMIT 1"));
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px"></td>
                            <td width="200px" class="text-right"><?php echo formatAngka($a['saldo']);?></td>
                        </tr>
                        <?php
                        $total+=$a['saldo'];
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL PENDAPATAN</td>
                        <td width="200px" class="text-right font-weight-bold">Rp<?php echo formatAngka($total);?></td>
                    </tr>
                </table>
            </div>
            <h6 class="mt-4">BEBAN</h6>
            <div class="ml-3">
                <table class="table table-striped">
                    <?php
                    $total_beban=0;
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='b1fa317d-d2b1-f990-73e9-814706b766f9' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='32fb1550-355e-23fe-20ff-623edb806fc3'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) 
                    OR a.deleted_at IS NULL AND a.uid_parent='68d46d35-3a9e-ed19-0744-ac5b0a19463a'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                    OR a.deleted_at IS NULL AND a.uid_parent='5e781c46-ab69-6333-449d-9e3bf26caa09'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                    OR a.deleted_at IS NULL AND a.uid_parent='dd6fcc59-fd10-01b2-3054-5d90ec4e60f8'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                    ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at<='$tanggal 23:59:59' ORDER BY created_at DESC LIMIT 1"));
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td width="200px" class="text-right"><?php echo formatAngka($a['saldo']);?></td>
                            <td width="200px"></td>
                        </tr>
                        <?php
                        $total_beban+=$a['saldo'];
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">TOTAL BEBAN BIAYA</td>
                        <td width="200px" class="text-right font-weight-bold">Rp<?php echo formatAngka($total_beban);?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="font-weight-bold font-italic">LABA BERSIH</td>
                        <td width="200px" class="text-right font-weight-bold text-underline">Rp<?php echo formatAngka($total-$total_beban);?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>