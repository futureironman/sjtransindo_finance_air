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
                    <li class="breadcrumb-item active" aria-current="page">Jurnal Umum</li>
                </ol>
            </nav>
            <h4 class="m-0">Jurnal Umum</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <label class="col-md-2 text-right pt-2" for="filter_name">Bulan</label>
                    <div class="col-md-3">
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
                    <label class="col-md-2 text-right pt-2" for="filter_name">Tahun</label>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="tahun" value="<?php echo $thn_sekarang;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" title="Reload" data-toggle="tooltip" data-placement="top"><i class="material-icons text-primary">refresh</i></button>
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="width:100%">
                    <thead class="bg-light"> 
                        <tr>
                            <th width="50px">No.</th>
                            <th>Tanggal/Jam</th>
                            <th>Nomor Bukti</th>
                            <th>Ref</th>
                            <th>Keterangan</th>
                            <th>Debet</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        $no=1;
                        $tampil=pg_query($conn,"SELECT * FROM keu_akun_jurnal WHERE id_divisi='$_SESSION[divisi]' AND waktu BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' ORDER BY waktu ASC, id ASC");
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['waktu']);
                            $waktu=DateToIndo2($a[0]).' '.$a[1];

                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(id) AS tot FROM keu_akun_jurnal_detail WHERE id_data='$r[id]'"));
                            $jumlah_data=$c['tot'];

                            $a=pg_fetch_array(pg_query($conn,"SELECT a.debet, a.kredit, b.nama, b.nomor FROM keu_akun_jurnal_detail a, keu_akun b WHERE a.uid_akun=b.uid AND id_data='$r[id]' LIMIT 1"));
                            if($a['debet']!=''){
                                $debet=formatAngka($a['debet']);
                                $kredit="";
                            }
                            else{
                                $debet="";
                                $kredit=formatAngka($a['kredit']);
                            }
                            ?>
                            <tr>
                                <td rowspan="<?php echo $jumlah_data;?>"><?php echo $no;?></td>
                                <td rowspan="<?php echo $jumlah_data;?>"><?php echo $waktu;?></td>
                                <td rowspan="<?php echo $jumlah_data;?>"><?php echo $r['no_bukti']."<br><small class='font-italic'>$r[keterangan]</small>";?></td>
                                <td><?php echo $a['nomor'];?></td>
                                <td><?php echo $a['nama'];?></td>
                                <td><?php echo $debet;?></td>
                                <td><?php echo $kredit;?></td>
                            </tr>
                            <?php
                            $data=pg_query($conn,"SELECT a.debet, a.kredit, b.nama, b.nomor FROM keu_akun_jurnal_detail a, keu_akun b WHERE a.uid_akun=b.uid AND id_data='$r[id]' OFFSET 1");
                            while($d=pg_fetch_array($data)){
                                if($d['debet']!=''){
                                    $debet=formatAngka($d['debet']);
                                    $kredit="";
                                }
                                else{
                                    $debet="";
                                    $kredit=formatAngka($d['kredit']);
                                }
                                ?>
                                <tr>
                                    <td><?php echo $d['nomor'];?></td>
                                    <td><?php echo $d['nama'];?></td>
                                    <td class="text-right"><?php echo $debet;?></td>
                                    <td class="text-right"><?php echo $kredit;?></td>
                                </tr>
                                <?php
                            }
                            $no++;
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>