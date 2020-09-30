<?php
if(isset($_GET['tahun'])){
    $tahun=$_GET['tahun'];
}
else{
    $tahun=$thn_sekarang;
}
$tanggal_awal=date("$tahun-01-01");
$tanggal_akhir=date("Y-12-t",strtotime($tanggal_awal));
?>
<form action="">
    <div class="card card-form d-flex flex-column flex-sm-row">
        <div class="card-form__body card-body-form-group flex">
            <div class="form-group row">
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
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Waktu</th>
                            <th>No. Invoice</th>
                            <th>PO House</th>
                            <th>Total</th>
                            <th>Jlh Terbayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $a=pg_fetch_array(pg_query($conn,"SELECT uid_data FROM keu_akun WHERE uid='$_GET[id]'"));
                        $tampil=pg_query($conn,"SELECT b.lock_date, b.invoice_number, a.po_house_number, b.total, b.jumlah_terbayar, b.is_lunas FROM po_house a, invoice_header b WHERE a.uid=b.uid_data AND a.uid_customer='$a[uid_data]' AND a.uid_category='$_SESSION[divisi]' AND b.lock_date BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59'AND b.total>'0' ORDER BY b.lock_date ASC, a.created_at ASC");
                        $total=0;
                        while($r=pg_fetch_array($tampil)){
                            $a=explode(" ",$r['lock_date']);
                            $waktu = DateToIndo2($a[0]).' '.$a[1];

                            if($r['is_lunas']=='Y'){
                                $status="<span class='badge badge-success'>LUNAS</span>";
                            }
                            else{
                                $status="<span class='badge badge-danger'>BELUM LUNAS</span>";
                            }
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $waktu;?></td>
                                <td><?php echo $r['invoice_number'];?></td>
                                <td><?php echo $r['po_house_number'];?></td>
                                <td class="text-right"><?php echo formatAngka($r['total']);?></td>
                                <td class="text-right"><?php echo formatAngka($r['jumlah_terbayar']);?></td>
                                <td><?php echo $status;?></td>
                            </tr>
                            <?php
                            $no++;
                            $total+=$r['total'];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center font-weight-bold">TOTAL</td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($total);?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>