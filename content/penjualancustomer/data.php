<?php
if(isset($_GET['tanggal_awal'])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
    $uid_customer=$_GET['uid_customer'];
}
else{
    $tanggal_awal=date("Y-m-01");
    $tanggal_akhir=date("Y-m-d");
    $uid_customer="";
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Penjualan Per Customer</li>
                </ol>
            </nav>
            <h4 class="m-0">Penjualan Per Customer</h4>
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
                        <label>Customer</label>
                        <select name="uid_customer" class="form-control select2">
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT uid, nama FROM customer WHERE id_divisi='$_SESSION[divisi]' ORDER BY nama");
                            while($r=pg_fetch_array($tampil)){
                                if($r['uid']==$uid_customer){
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
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0 btnCetak" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
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
                            <th>Jatuh Tempo</th>
                            <th>PO House</th>
                            <th>Nomor Invoice</th>
                            <th>Total</th>
                            <th>Jumlah Terbayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $tampil=pg_query($conn,"SELECT a.uid, a.po_house_number, b.lock_date, b.invoice_number, b.total, b.jumlah_terbayar, b.is_lunas, CAST(b.jatuh_tempo AS DATE) FROM po_house a, invoice_header b WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND b.id_category='$_SESSION[divisi]' AND a.uid=b.uid_data AND b.lock_date BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND b.total>0 AND a.uid_customer='$uid_customer' ORDER BY lock_date ASC");

                        $grand_total=0;
                        $grand_total_bayar=0;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['lock_date']);
                            $waktu=DateToIndo2($a[0]).' '.$a[1];

                            if($r['is_lunas']=='Y' OR $r['jumlah_terbayar']==$r['total']){
                                $status="<span class='badge badge-success'>LUNAS</span>";
                            }
                            else{
                                if($r['total']>$r['jumlah_terbayar']){
                                    $status="<span class='badge badge-warning'>SEBAGIAN</span>";
                                }
                                if($r['jumlah_terbayar']=='0'){
                                    $status="<span class='badge badge-danger'>BELUM DIBAYAR</span>";
                                }
                            }

                            $a=explode(" ",$r['jatuh_tempo']);
                            $jatuh_tempo = DateToIndo3($a[0]);
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $waktu;?></td>
                                <td><?php echo $jatuh_tempo;?></td>
                                <td><?php echo $r['po_house_number'];?></td>
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
                            <td colspan="5">TOTAL</td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total);?></td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($grand_total_bayar);?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <a href="sinkron-invoice-penjualancustomer?<?php echo "tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&uid_customer=$uid_customer";?>" class="btn btn-danger"><i class="material-icons">sync</i> SINKRONISASI INVOICE DENGAN AKUN PIUTANG CUSTOMER</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(".btnCetak").click(function() {
    var id = this.id;
    window.open("cetak-penjualancustomer?<?php echo "tanggal_awal=$tanggal_awal&tanggal_akhir=$tanggal_akhir&uid_customer=$uid_customer";?>", "popupWindow", "width=600,height=600,scrollbars=yes");
})
</script>