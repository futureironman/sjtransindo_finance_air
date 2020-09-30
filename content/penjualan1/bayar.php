<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="penjualan">Penjualan Export</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pelunasan Piutang</li>
                </ol>
            </nav>
            <h4 class="m-0">Pelunasan Piutang</h4>
        </div>
    </div>
</div>

<?php
$d=pg_fetch_array(pg_query($conn,"SELECT a.uid, a.po_house_number, a.quantity_deal, a.cost, a.uid_comodity, a.taxes, b.uid AS uid_invoice, b.invoice_number, c.nama AS nama_customer, c.no_telepon, c.alamat, b.total, b.jumlah_terbayar, b.is_lunas, b.lock_date FROM po_house a, invoice_header b, customer c WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND a.uid_customer=c.uid AND a.uid=b.uid_data AND a.uid='$_GET[id]' AND b.id_category='$_SESSION[divisi]' AND b.total>0"));

$a=explode(" ",$d['lock_date']);
$waktu=DateToIndo2($a[0]).' '.$a[1];
if($d['is_lunas']=='Y'){
    $status="<span class='badge badge-success'>PAID</span>";
}
else{
    if($d['total']<$d['jumlah_terbayar']){
        $status="<span class='badge badge-warning'>PARTIAL</span>";
    }
    else{
        $status="<span class='badge badge-danger'>UNPAID</span>";
    }
}
?>
<div class="container-fluid page__container">
    <form method="POST" action="aksi-bayar-penjualan">
    <input type="hidden" name="uid_po_house" value="<?php echo $_GET['id'];?>">
    <input type="hidden" name="uid_invoice" value="<?php echo $d['uid_invoice'];?>">
    <input type="hidden" name="invoice_number" value="<?php echo $d['invoice_number'];?>">
    <div class="card">
        <div class="card-body">
            <?php echo $status;?>
            <div class="px-3">
                <div class="d-flex justify-content-center flex-column text-center my-5 navbar-light">
                    <a href="index.html" class="navbar-brand d-flex flex-column m-0" style="min-width: 0">
                        <img class="navbar-brand-icon mb-2" src="assets/images/stack-logo-blue.svg" width="25" alt="Stack">
                        <span>Invoice</span>
                    </a>
                    <div class="text-muted font-weight-bold"><?php echo $d['invoice_number'];?></div>
                </div>
                <div class="row mb-5">
                    <div class="col-lg">
                        <div class="text-label">CUSTOMER</div>
                        <p class="mb-4">
                            <strong class="text-body"><?php echo $d['nama_customer'];?></strong><br>
                            <?php echo $d['alamat'];?><br>
                            <?php echo $d['no_telepon'];?><br>
                        </p>
                    </div>
                    <div class="col-lg text-right">
                        <div class="text-label">PO HOUSE NUMBER</div>
                        <?php echo $d['po_house_number'];?>
                        <br><br>
                        <div class="text-label">Due date</div>
                        <?php echo $waktu;?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb-5 table-striped table-hover">
                        <thead class="text-center">
                            <tr class="bg-light">
                                <th width="50px">No.</th>
                                <th>Keterangan</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th width="250px">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total=0;
                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM komoditi WHERE uid='$d[uid_comodity]' AND deleted_at IS NULL"));
                            ?>
                                <tr>
                                    <td><?php echo 1;?></td>
                                    <td><?php echo $a['nama'];?></td>
                                    <td class="text-right"><?php echo formatAngka($d['cost']);?></td>
                                    <td class="text-center"><?php echo $d['quantity_deal'];?></td>
                                    <td class="text-right"><?php echo formatAngka($d['quantity_deal']*$d['cost']);?></td>
                                </tr>
                            <?php
                            $total += ($d['quantity_deal']*$d['cost']);
                            $no=2;
                            $tampil=pg_query($conn,"SELECT * FROM inv_detail_fee WHERE uid_data='$d[uid]' ORDER BY id");
                            //echo"SELECT * FROM inv_detail_fee WHERE uid_data='$d[uid]' ORDER BY id";
                            while($r=pg_fetch_array($tampil)){
                                ?>
                                <tr>
                                    <td><?php echo $no;?></td>
                                    <td><?php echo $r['stock_name'];?></td>
                                    <td class="text-right"><?php echo formatAngka($r['price']);?></td>
                                    <td class="text-center"><?php echo $r['quantity'];?></td>
                                    <td class="text-right"><?php echo formatAngka($r['price']*$r['quantity']);?></td>
                                </tr>
                                <?php
                                $no++;
                                $total += ($r['price']*$r['quantity']);
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <?php
                            if($d['taxes']==true){
                            ?>
                            <tr>
                                <td colspan="4">TOTAL</td>
                                <td class="text-right">
                                    <?php echo formatAngka($total);?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">PAJAK 1%</td>
                                <td class="text-right">
                                    <?php echo formatAngka($d['total']-$total);?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td colspan="4" class="font-weight-bold">TOTAL TAGIHAN</td>
                                <td class="text-right">
                                    <?php echo formatAngka($d['total']);?>
                                    <input type="hidden" name="total" id="total" value="<?php echo $d['total'];?>">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="font-weight-bold">JUMLAH PEMBAYARAN</td>
                                <td class="text-right"><input type="text" class="form-control money text-right" name="jumlah_bayar" id="jumlah_bayar" required></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="font-weight-bold" id="teksBayar">SISA PEMBAYARAN</td>
                                <td class="text-right font-weight-bold"><span id="sisaBayar"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-4 card-body">
                <p><strong class="headings-color">Pelunasan Piutang</strong></p>
                <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
            </div>
            <div class="col-lg-8 card-form__body card-body">
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="tanggal">
                    </div>
                    <div class="col-md-4">
                        <input type="time" class="form-control" name="jam">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Metode Pembayaran<span class="red">*</span></label>
                    <div class="col-md-8">
                        <select name="id_metode_bayar" class="form-control" required>
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT * FROM keu_akun_payment_status");
                            while($r=pg_fetch_array($tampil)){
                                echo"<option value='$r[id]'>$r[nama]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Ke Akun Keuangan<span class="red">*</span></label>
                    <div class="col-md-8">
                        <select name="uid_akun_terima" class="form-control" required>
                        <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0' AND a.jenis_akun='D' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202' AND a.jenis_akun='D' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil)){
                                echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Catatan</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="catatan">
                    </div>
                </div>
                <hr>
                <div class="row form-group">
                    <div class="col-12">
                        <button type="button" class="btn btn btn-primary btn-sm" id="btnBarisAkun"><i class="fa fa-plus"></i>Tambah Akun</button>
                        <table class="table table-bordered table-striped" id="akun_kredit">
                            <thead>
                                <tr>
                                    <th width="50px">.</th>
                                    <th>Akun</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="akun_kredit">
                                <?php
                                $no=1;
                                ?>
                                <tr id="<?php echo $no;?>">
                                    <td><input type="checkbox" name="check_list[]" value="<?php echo $no;?>" checked></td>
                                    <td>
                                        <select name="uid_akun_<?php echo $no;?>" class="form-control" required>
                                        <option value="">Pilih</option>
                                        <?php
                                        $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                                        while($r=pg_fetch_array($tampil)){
                                            echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                        }
                                        ?>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="keterangan_<?php echo $no;?>"></td>
                                    <td><input type="text" class="form-control money text-right jumlahKredit" name="jumlah_<?php echo $no;?>"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm btnHapus"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <!--<tfoot>
                                <tr>
                                    <td colspan="3" class="font-weight-bold">TOTAL</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>-->
                        </table>
                        Total : <span id="totalKredit" class="font-weight-bold text-right">0</span>
                        <br>
                        <div id="error"></div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan dan Bayar</button>
    <a href="penjualan" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>

<script type="text/javascript">
    $("#btnBarisAkun").click(function() {
        var id = $('#akun_kredit tr:last').attr('id');
        if(id==undefined){
            id=0;
        }
        $.ajax({
            type: 'POST',
            data:{
                'id' : id
            },
            url: 'tambah-akunkredit-penjualan',
            success: function(msg) {
                $('#akun_kredit tr:last').after(msg);
            }
        });
    });

    $("#akun_kredit").on('click', '.btnHapus', function () {
        $(this).closest('tr').remove();
    });

    $('#jumlah_bayar').keyup(function(){
        var total = $("#total").val();
        var jumlah_bayar = $(this).val();
        jumlah_bayar = jumlah_bayar.replace(".", "");
	   
	    jumlah_bayar = parseInt(jumlah_bayar.replace(/\./g, ''));

        var sisa = total - jumlah_bayar;

        $("#sisaBayar").number(sisa,0,',','.');
    });

    $('.jumlahKredit').keyup(function(){
        var sum = 0;
        var kredit = 0;
        $('.jumlahKredit').each(function()
        {
            kredit = $(this).val().replace(".", "");
            kredit = parseInt(kredit.replace(/\./g, ''));
            sum += kredit;
        });
        $("#totalKredit").number(sum,0,',','.');

        /*
        var total = $("#total").val();
        var jumlah_bayar = $("#jumlah_bayar").val();
        jumlah_bayar = jumlah_bayar.replace(".", "");
	    jumlah_bayar = parseInt(jumlah_bayar.replace(/\./g, ''));

        var sisa = total - (jumlah_bayar+sum);
        if(sisa!=0){
            $("#error").html("<div class='alert alert-warning'>Jumlah debet dan kreditnya tidak sama. Mohon periksa kembali</div>");
            $("#btnSimpanBayar").prop("disabled",true);
        }
        else{
            $("#error").html("");
            $("#btnSimpanBayar").prop("disabled",false);
        }
        */
    });
</script>