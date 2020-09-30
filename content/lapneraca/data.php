<?php
if(isset($_GET['tanggal'])){
    $tanggal=$_GET['tanggal'];
}
else{
    $tanggal= $tgl_sekarang;
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
        </div>
    </form>
    <div class="row card-group-row">
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>AKTIVA</h6>
                <table class="table">
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            AKTIVA LANCAR
                        </td>
                    </tr>
                    <?php
                    $total_aktiva_lancar = 0;

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            PIUTANG DAGANG
                        </td>
                    </tr>
                    <?php

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='90dc67fb-71a3-bf14-958e-3feb14f7e450' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            PIUTANG KARYAWAN
                        </td>
                    </tr>
                    <?php

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='39cb84be-c33c-6f15-d690-3a6c0cbbd99b' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            PIUTANG LAIN-LAIN
                        </td>
                    </tr>
                    <?php

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='54ea416e-dfa5-a5a3-6704-5b4d8bd0260c' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            BIAYA DIBAYAR DIMUKA
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='c07508f9-27d7-6d54-d180-e512c00c7c9c' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            INVENTORY
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='232df725-a344-472a-9e66-022c47611b21' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            PERSEDIAAN SEMENTARA
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='cf280894-41b2-8d49-3fa7-cfe2ca4f164c' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_lancar+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="2" class="font-weight-bold">
                            TOTAL AKTIVA LANCAR
                        </td>
                        <td class="font-weight-bold border-dark border-top text-right">
                            <?php echo formatAngka($total_aktiva_lancar);?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            AKTIVA TETAP
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            INVENTARIS KANTOR
                        </td>
                    </tr>
                    <?php
                    $total_aktiva_tetap = 0;

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='df562c24-4212-e630-2519-e6a837631384' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_tetap+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            KENDERAAN
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='ee912c5e-19d6-c3f9-17db-3c3615529887' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_tetap+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            GEDUNG
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='9df0f5c4-372d-392d-3be8-d958478e936a' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_aktiva_tetap+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="2" class="font-weight-bold">
                            TOTAL AKTIVA TETAP
                        </td>
                        <td class="font-weight-bold border-dark border-top text-right">
                            <?php echo formatAngka($total_aktiva_tetap);?>
                        </td>
                    </tr>
                </table>
                
            </div>
        </div>

        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <h6>PASIVA</h6>
                <table class="table">
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            HUTANG LANCAR
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            HUTANG DAGANG
                        </td>
                    </tr>
                    <?php
                    $total_hutang_lancar = 0;

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='9fafb128-3890-4441-9c7a-8612b0db79a3' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_hutang_lancar+=$saldo;
                        }
                    }

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='e97aa038-cb58-755b-ca04-26f9b0c04e50' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_hutang_lancar+=$saldo;
                        }
                    }
                    ?>
                    
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            HUTANG LAIN-LAIN
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='fd63c345-8b6f-0a02-da8a-920e69d12037' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];
                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_hutang_lancar+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="2" class="font-weight-bold">
                            TOTAL HUTANG LANCAR
                        </td>
                        <td class="font-weight-bold border-dark border-top text-right">
                            <?php echo formatAngka($total_hutang_lancar);?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            MODAL
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            MODAL
                        </td>
                    </tr>
                    <?php
                    $total_modal = 0;

                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='66dc70f2-9e6e-be0e-07a5-205e18625e03' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) AND a.uid!='2cb87d6a-26e4-8be2-0767-69ca4af05292' ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_modal+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            LABA TAHUN BERJALAN
                        </td>
                        <?php
                        $tampil=pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_bulan_labarugi a, bulan b WHERE a.tahun='$thn_sekarang' AND a.id_bulan=b.id  AND a.id_divisi='$_SESSION[divisi]'
                         ORDER BY a.id_bulan ASC");
                        $total_laba = 0;
                        while($r=pg_fetch_array($tampil)){
                            ?>
                            <tr>
                                <td colspan="2">LABA BERJALAN <?php echo strtoupper($r['nama']).' '.$r['tahun'];?></td>
                                <td class="text-right">
                                    <?php echo formatAngka($r['saldo']);?>
                                </td>
                            </tr>
                            <?php
                            $total_modal+=$r['saldo'];
                            $total_laba+=$r['saldo'];
                        }
                        ?>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='c89207f4-3ddf-b06e-8df9-cc71980b3324' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_modal+=$saldo;
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="font-weight-bold">
                            DIVIDEN
                        </td>
                    </tr>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='cde30090-27b7-31cd-06c3-cf6438411de0' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while($r=pg_fetch_array($tampil)){
                        $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$r[uid]' AND created_at <='$tanggal 23:59:59' ORDER BY created_at DESC, id DESC LIMIT 1"));
                        $saldo = $a['saldo'];

                        if($saldo!=NULL){
                        ?>
                        <tr>
                            <td width="85px"><?php echo $r['nomor'];?></td>
                            <td><?php echo $r['nama'];?></td>
                            <td class="text-right">
                                <?php echo formatAngka($saldo);?>
                            </td>
                        </tr>
                        <?php
                        $total_modal+=$saldo;
                        }
                    }
                    ?>

                    <tr>
                        <td colspan="2" class="font-weight-bold">
                            TOTAL MODAL
                        </td>
                        <td class="font-weight-bold border-dark border-top text-right">
                            <?php echo formatAngka($total_modal);?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold border-top border-dark">
                            TOTAL AKTIVA
                        </td>
                        <td class="font-weight-bold border-top border-dark text-right">
                            <?php echo formatAngka($total_aktiva_lancar+$total_aktiva_tetap);?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 card-group-row__col">
            <div class="card card-group-row__card card-body align-items-center">
                <table class="table">
                    <tr>
                        <td class="font-weight-bold border-top border-dark">
                            TOTAL PASSIVA
                        </td>
                        <td class="font-weight-bold border-top border-dark text-right">
                            <?php echo formatAngka($total_hutang_lancar+$total_modal);?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="addons/js/masking_form.js"></script><script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-lapneraca?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>", "popupWindow", "width=600,height=600,scrollbars=yes");
})
</script>