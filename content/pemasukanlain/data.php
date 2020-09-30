<?php
switch($_GET['act']){
default:
if(isset($_GET['tanggal_awal'])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
}
else{
    $tanggal_awal=date("Y-m-01");
    $tanggal_akhir=date("Y-m-d");
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pemasukan Lain</li>
                </ol>
            </nav>
            <h4 class="m-0">Pemasukan Lain</h4>
        </div>
        <a href="tambah-pemasukanlain"><button type="button" class="btn btn-info ml-3"><i class="fa fa-plus"></i> Tambah</button></a>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Awal</label>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal;?>">
                    </div>
                    <label class="col-md-2 text-right pt-2" for="filter_name">Tanggal Akhir</label>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit"><i class="material-icons text-primary">refresh</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width:100%" id="example">
                    <thead> 
                        <tr>
                            <th width="50px">No.</th>
                            <th>No. Transaksi</th>
                            <th>Waktu Input</th>
                            <th>Waktu Transaksi</th>
                            <th>Jenis Transaksi</th>
                            <th>Akun Debet</th>
                            <th>Dikreditkan Dari-</th>
                            <th>Jumlah</th>
                            <th>Referensi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $tampil=pg_query($conn,"SELECT a.uid, a.nomor AS nomor_transaksi, a.waktu, a.created_at, b.nomor AS nomor_akun_asal, b.nama AS nama_akun_asal, c.nomor AS nomor_akun_tujuan, c.nama AS nama_akun_tujuan, a.jumlah, a.keterangan, d.nama AS jenis_transaksi FROM keu_akun_transaksi_lain a, keu_akun b, keu_akun c, keu_akun_transaksi_lain_jenis d WHERE a.id_divisi='$_SESSION[divisi]' AND a.uid_akun_kas=b.uid AND a.uid_akun_lawan=c.uid AND a.id_jenis=d.id AND a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.deleted_at IS NULL AND a.id_jenis BETWEEN '2' AND '6' ORDER BY a.created_at DESC");
                        $no=1;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['waktu']);
                            $waktu = DateToIndo2($a[0]).' '.$a[1];

                            $a=explode(" ",$r['created_at']);
                            $created_at = DateToIndo2($a[0]).' '.$a[1];
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $r['nomor_transaksi'];?></td>
                                <td><?php echo $created_at;?></td>
                                <td><?php echo $waktu;?></td>
                                <td><?php echo $r['jenis_transaksi'];?></td>
                                <td><?php echo "$r[nomor_akun_asal] - $r[nama_akun_asal]";?></td>
                                <td><?php echo "$r[nomor_akun_tujuan] - $r[nama_akun_tujuan]";?></td>
                                <td class="text-right"><?php echo formatAngka($r['jumlah']);?></td>
                                <td><?php echo $r['keterangan'];?></td>
                                <td>
                                    <a href="edit-pemasukanlain-<?php echo $r['uid'];?>"><button type="button" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger btn-sm btnHapus" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>
                                    <button type="button" class="btn btn-success btn-sm btnCetak" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Cetak"><i class="fa fa-print"></i></button>
                                </td>
                            </tr>
                            <?php
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="addons/js/pemasukanlain.js"></script>
<?php
break;

case "tambah":
    include "tambah.php";
break;

case "edit":
    include "edit.php";
break;
}
?>