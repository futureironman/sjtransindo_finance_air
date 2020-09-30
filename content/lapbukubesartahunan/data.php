<?php
if(isset($_GET['tahun'])){
    $tahun=$_GET['tahun'];
    $uid_akun=$_GET['uid_akun'];
}
else{
    $tahun=$thn_sekarang;
    $uid_akun="";
}

$tanggal_awal=date("$tahun-01-01");
$tanggal_akhir=date("$tahun-12-31");

$e=pg_fetch_array(pg_query($conn,"SELECT * FROM keu_akun WHERE uid='$uid_akun'"));
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buku Besar Tahunan</li>
                </ol>
            </nav>
            <h4 class="m-0">Buku Besar Tahunan</h4>
        </div>
    </div>
</div>
<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <div class="col-md-3">
                        <label>Tahun</label>
                        <input type="number" class="form-control" name="tahun" value="<?php echo $tahun;?>">
                    </div>
                    <div class="col-md-5">
                        <label>Akun</label>
                        <select name="uid_akun" class="form-control select2">
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil)){
                                if($r['uid']==$uid_akun){
                                    echo"<option value='$r[uid]' selected>$r[nomor] - $r[nama]</option>";
                                }
                                else{
                                    echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" title="Reload" data-toggle="tooltip" data-placement="top"><i class="material-icons text-primary">refresh</i></button>
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0 btnCetak" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <?php
            if($uid_akun==''){
                ?>
                <div class="alert alert-danger">
                    Silahkan pilih dahulu akun keuangan untuk dapat melihat laporan buku besar
                </div>
                <?php
            }
            else{
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead class="bg-light"> 
                        <tr>
                            <th width="50px">No.</th>
                            <th>Tanggal/Jam</th>
                            <th>No Bukti/ Nama</th>
                            <th>Referensi</th>
                            <th>Keterangan Detail</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $x = pg_fetch_array(pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_log a, keu_akun_status b WHERE a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.uid_akun='$uid_akun' AND a.id_status=b.id and a.deleted_at is null ORDER BY created_at ASC LIMIT 1"));
                        if($x['created_at']!=''){
                            $a=explode(" ",$x['created_at']);
                            $tanggal_saldoawal = date('Y-m-d', strtotime("-1 day", strtotime($a[0])));
                        }
                        else{
                            $tanggal_saldoawal = date('Y-m-d', strtotime("-1 day", strtotime($tanggal_awal)));
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td><?php echo DateToIndo2($tanggal_saldoawal);?> 23:59:59</td>
                            <td class="font-weight-bold">Saldo Awal</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($x['saldo']);?></td>
                        </tr>
                        <?php
                        $no=1;
                        $tampil=pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_log a, keu_akun_status b WHERE a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.uid_akun='$uid_akun' AND a.id_status=b.id and a.deleted_at is null ORDER BY created_at ASC");
                        $total_debet=0;
                        $total_kredit=0;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['created_at']);
                            $waktu = DateToIndo2($a[0]).' '.$a[1];

                            if($r['uid_akun_efek']!=NULL){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nomor, nama FROM keu_akun WHERE uid='$r[uid_akun_efek]'"));
                                $nomor_referensi="$a[nomor]";
                            }
                            else{
                                $nomor_referensi="";
                            }

                            if($r['tabel']=='keu_buka_faktur' AND $r['kredit']!='0'){
                                $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(b.uid) AS tot FROM keu_buka_faktur a, keu_buka_faktur_detail b WHERE a.no_faktur=b.no_faktur AND a.uid='$r[id_data]'"));
                                $rowspan="$c[tot]";

                                $a=pg_fetch_array(pg_query($conn,"SELECT b.jumlah, b.uid_akun_keperluan, c.nomor, c.nama, a.nama AS nama_penerima, b.keterangan FROM keu_buka_faktur a, keu_buka_faktur_detail b, keu_akun c WHERE a.no_faktur=b.no_faktur AND b.uid_akun_keperluan=c.uid AND a.uid='$r[id_data]' ORDER BY b.created_at ASC LIMIT 1"));

                                $debet = 0;
                                $kredit = $a['jumlah'];
                                $saldo_detail = $saldo - $kredit;
                                
                                ?>
                                <tr>
                                    <td rowspan="<?php echo $rowspan;?>"><?php echo $no;?></td>
                                    <td rowspan="<?php echo $rowspan;?>"><?php echo $waktu;?></td>
                                    <td rowspan="<?php echo $rowspan;?>">
                                        <?php echo $r['nama'];
                                        if($r['keterangan']!=''){
                                            echo"<br>&nbsp; &nbsp; &nbsp; &nbsp;<b>$r[keterangan]</b><br><i>&nbsp; &nbsp; &nbsp; &nbsp; $a[nama_penerima]</i>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $a['nomor'];?></td>
                                    <td><?php echo $a['keterangan'];?></td>
                                    <td class="text-right"><?php echo formatAngka($debet);?></td>
                                    <td class="text-right"><?php echo formatAngka($kredit);?></td>
                                    <td class="text-right"><?php echo formatAngka($saldo_detail);?></td>
                                </tr>
                                <?php
                                $data=pg_query($conn,"SELECT b.jumlah, b.uid_akun_keperluan, c.nomor, c.nama, b.keterangan FROM keu_buka_faktur a, keu_buka_faktur_detail b, keu_akun c WHERE a.no_faktur=b.no_faktur AND b.uid_akun_keperluan=c.uid AND a.uid='$r[id_data]' ORDER BY b.created_at ASC OFFSET 1");
                                while($d=pg_fetch_array($data)){
                                    $debet = 0;
                                    $kredit = $d['jumlah'];
                                    $saldo_detail = $saldo_detail - $kredit;
                                    ?>
                                    <tr>
                                        <td><?php echo $d['nomor'];?></td>
                                        <td><?php echo $d['keterangan'];?></td>
                                        <td class="text-right"><?php echo formatAngka($debet);?></td>
                                        <td class="text-right"><?php echo formatAngka($kredit);?></td>
                                        <td class="text-right"><?php echo formatAngka($saldo_detail);?></td>
                                    </tr>
                                    <?php
                                }
                                $no++;
                            }
                            else{
                            ?>
                                <tr>
                                    <td><?php echo $no;?></td>
                                    <td><?php echo $waktu ;?></td>
                                    <td>
                                        <?php echo $r['nama'];
                                        if($r['keterangan']!=''){
                                            echo"<br>&nbsp; &nbsp; &nbsp; &nbsp;<small>$r[keterangan]</small>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $nomor_referensi;?></td>
                                    <td><?php 
                                        $ex = explode(",",$r["keterangan_detail"]);
                                        foreach($ex as $row){
                                            echo "$row <br>";
                                        } ?></td>
                                    <td class="text-right"><?php echo formatAngka($r['debet']);?></td>
                                    <td class="text-right"><?php echo formatAngka($r['kredit']);?></td>
                                    <td class="text-right"><?php echo formatAngka($r['saldo']);?></td>
                                </tr>
                                <?php
                                $total_debet+=$r['debet'];
                                $total_kredit+=$r['kredit'];
                                $no++;
                            }
                            $saldo = $r['saldo'];
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-lapbukubesartahunan?uid_akun=<?php echo $uid_akun;?>&tahun=<?php echo $tahun;?>" + id, "popupWindow", "width=600,height=600,scrollbars=yes");
});
</script>