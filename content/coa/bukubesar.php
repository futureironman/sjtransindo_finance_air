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
<form action="">
    <div class="card card-form d-flex flex-column flex-sm-row">
        <div class="card-form__body card-body-form-group flex">
            <div class="form-group row">
                <label class="col-md-2 text-right pt-2" for="bulan">Bulan</label>
                <div class="col-md-3">
                    <select name="id_bulan" class="form-control">
                        <?php
                        $tampil=pg_query($conn,"SELECT * FROM bulan ORDER BY id ASC");
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
                <label class="col-md-2 text-right pt-2" for="filter_name">Tahun</label>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="tahun" value="<?php echo $tahun;?>">
                </div>
            </div>
        </div>
        <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit"><i class="material-icons text-primary">refresh</i></button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="180px">Waktu</th>
                        <th>Keterangan</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tampil=pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_log a, keu_akun_status b WHERE a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.uid_akun='$_GET[id]' AND a.id_status=b.id ORDER BY created_at ASC");
                    $total_debet=0;
                    $total_kredit=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=explode(" ",$r['created_at']);
                        $waktu = DateToIndo2($a[0]).' '.$a[1];
                        ?>
                        <tr>
                            <td><?php echo $waktu;?></td>
                            <td>
                                <?php echo $r['nama'];
                                if($r['keterangan']!=''){
                                    echo"<br>&nbsp; &nbsp; &nbsp; &nbsp;<small>$r[keterangan]</small>";
                                }
                                ?>
                            </td>
                            <td class="text-right"><?php echo formatAngka($r['debet']);?></td>
                            <td class="text-right"><?php echo formatAngka($r['kredit']);?></td>
                            <td class="text-right"><?php echo formatAngka($r['saldo']);?></td>
                        </tr>
                        <?php
                        $total_debet+=$r['debet'];
                        $total_kredit+=$r['kredit'];
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-center font-weight-bold">TOTAL</td>
                        <td class="text-right font-weight-bold"><?php echo formatAngka($total_debet);?></td>
                        <td class="text-right font-weight-bold"><?php echo formatAngka($total_kredit);?></td>
                        <td class="text-right font-weight-bold"><?php echo formatAngka($total_debet-$total_kredit);?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</form>