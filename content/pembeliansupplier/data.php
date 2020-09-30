<?php
if(isset($_GET['tanggal_awal'])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
    $uid_supplier=$_GET['uid_supplier'];
}
else{
    $tanggal_awal=date("Y-m-01");
    $tanggal_akhir=date("Y-m-d");
    $uid_supplier="";
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pembelian Per Supplier</li>
                </ol>
            </nav>
            <h4 class="m-0">Pembelian Per Supplier</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label>Tanggal Awal</label>
                        <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal;?>">
                    </div>
                    <div class="col-md-4">
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir;?>">
                    </div>
                    <div class="col-md-4">
                        <label>Supplier</label>
                        <select name="uid_supplier" class="form-control select2">
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT uid, nama FROM master_supplier WHERE id_divisi='$_SESSION[divisi]' ORDER BY nama");
                            while($r=pg_fetch_array($tampil)){
                                if($r['uid']==$uid_supplier){
                                    echo"<option value='$r[uid]' selected>$r[nama]</option>";
                                }
                                else{
                                    echo"<option value='$r[uid]'>$r[nama]</option>";
                                }
                            }
                            ?>
                        </select>
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
                            <th>Tgl</th>
                            <th>PO Master</th>
                            <th>Nomor Invoice</th>
                            <th>Total</th>
                            <th>Jumlah Terbayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $tampil=pg_query($conn,"SELECT a.uid, a.po_master_number, b.invoice_number, c.total, c.jumlah_terbayar, CAST(c.tanggal_pembelian AS DATE), c.is_lunas FROM po_master a, inv_header_pembelian b, inv_detail_pembelian c WHERE a.deleted_at IS NULL AND a.uid_category='$_SESSION[divisi]' AND a.uid=b.uid_data AND CAST(b.uid AS uuid)=c.uid_inv_header AND c.uid_suplier='$uid_supplier' AND CAST(c.tanggal_pembelian AS DATE) BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' ORDER BY CAST(c.tanggal_pembelian AS DATE) ASC");

                        $grand_total=0;
                        $grand_total_bayar=0;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['lock_date']);
                            $waktu=DateToIndo2($a[0]).' '.$a[1];

                            if($r['is_lunas']=='Y' OR $r['jumlah_terbayar']>=$r['total']){
                                $status="<span class='badge badge-success'>LUNAS</span>";
                            }
                            if($r['total']>$r['jumlah_terbayar'] AND $r['jumlah_terbayar']!=NULL){
                                $status="<span class='badge badge-warning'>SEBAGIAN</span>";
                            }
                            if($r['jumlah_terbayar']==NULL or $r['jumlah_terbayar']=='0'){
                                $status="<span class='badge badge-danger'>BELUM DIBAYAR</span>";
                            }
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo DateToIndo2($r['tanggal_pembelian']);?></td>
                                <td><?php echo $r['po_master_number'];?></td>
                                <td><?php echo $r['invoice_number'];?></td>
                                <td class="text-right"><?php echo formatAngka($r['total']);?></td>
                                <td class="text-right"><?php echo formatAngka($r['jumlah_terbayar']);?></td>
                                <td><?php echo $status;?></td>
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
                            <td colspan="4">TOTAL</td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total);?></td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total_bayar);?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <a href="sinkron-invoice-pembeliansupplier?<?php echo "tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&uid_supplier=$uid_supplier";?>" class="btn btn-danger"><i class="material-icons">sync</i> SINKRONISASI INVOICE DENGAN AKUN HUTANG SUPPLIER</a>
            </div>
        </div>
    </div>
</div>