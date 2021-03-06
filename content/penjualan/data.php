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
                    <li class="breadcrumb-item active" aria-current="page">Penjualan Terbaru</li>
                </ol>
            </nav>
            <h4 class="m-0">Penjualan Terbaru</h4>
        </div>
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

            <!--- Untuk Udara-->

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width:100%" id="example">
                    <thead> 
                        <tr>
                            <th width="50px">No.</th>
                            <th>Tgl/Jam</th>
                            <th>PO House</th>
                            <th>Nomor Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Jumlah Terbayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $tampil=pg_query($conn,"SELECT a.uid, a.po_house_number, b.lock_date, b.invoice_number, c.nama AS nama_customer, b.total, b.jumlah_terbayar, b.is_lunas FROM po_house a, invoice_header b, customer c WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND a.uid_customer=c.uid AND b.id_category='$_SESSION[divisi]' AND a.uid=b.uid_data AND b.lock_date BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND b.total>0 AND b.id_category='$_SESSION[divisi]' ORDER BY lock_date DESC");

                        $grand_total=0;
                        $grand_total_bayar=0;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['lock_date']);
                            $waktu=DateToIndo2($a[0]).' '.$a[1];

                            if($r['is_lunas']=='Y'){
                                $status="<span class='badge badge-success'>PAID</span>";
                            }
                            else{
                                if($r['total']<$r['jumlah_terbayar']){
                                    $status="<span class='badge badge-warning'>PARTIAL</span>";
                                }
                                else{
                                    $status="<span class='badge badge-danger'>UNPAID</span>";
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $waktu;?></td>
                                <td><?php echo $r['po_house_number'];?></td>
                                <td><?php echo $r['invoice_number'];?></td>
                                <td><?php echo $r['nama_customer'];?></td>
                                <td class="text-right"><?php echo formatAngka($r['total']);?></td>
                                <td class="text-right"><?php echo formatAngka($r['jumlah_terbayar']);?></td>
                                <td><?php echo $status;?></td>
                                <td>
                                    <?php
                                    if($r['is_lunas']!='Y'){
                                    ?>
                                    <a href="bayar-penjualan-<?php echo $r['uid'];?>"><button type="button" class="btn btn-success btn-sm" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Pelunasan Piutang"><i class="fa fa-check"></i></button></a>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $no++;
                            $grand_total+=$r['total'];
                            $grand_total_bayar+=$r['jumlah_terbayar'];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">TOTAL</td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total);?></td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total_bayar);?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <a href="sinkron-invoice-penjualan?<?php echo "tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir";?>" class="btn btn-danger"><i class="material-icons">sync</i> SINKRONISASI INVOICE DENGAN AKUN PIUTANG CUSTOMER</a>
            </div>
        </div>
    </div>
</div>
<?php
break;

case "bayar":
    include "bayar.php";
break;
}
?>