<?php
$d=pg_fetch_array(pg_query($conn,"SELECT * FROM keu_akun_transaksi_lain WHERE uid='$_GET[id]'"));

$a=explode(" ",$d['waktu']);
$tanggal=$a[0];
$jam=$a[1];
?>
<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="phsa">Pelunasan Hutang Saldo Awal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
            <h4 class="m-0">Edit Pelunasan Hutang Saldo Awal</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-edit-phsa">
    <input type="hidden" name="uid" value="<?php echo $_GET['id'];?>">
    <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-4 card-body">
                <p><strong class="headings-color">Pelunasan Hutang Saldo Awal</strong></p>
                <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
            </div>
            <div class="col-lg-8 card-form__body card-body">
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="tanggal" value="<?php echo $tanggal;?>" required max="<?php echo $tgl_sekarang;?>">
                    </div>
                    <div class="col-md-4">
                        <input type="time" class="form-control" name="jam" value="<?php echo $jam;?>" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Akun Bank/Kas <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih akun bank atau kas yang menerima dana dalam perusahaan"></i></label>
                    <div class="col-md-8">
                        <select name="uid_akun_kas" class="form-control" required>
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil)){
                                $saldo = formatAngka($r['saldo_terkini']);
                                if($r['uid']==$d['uid_akun_kas']){
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
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Akun Supplier <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih akun yang akan dikredit pada modul Akunting"></i></label>
                    <div class="col-md-8">
                        <select name="uid_akun_lawan" class="form-control select2" required id="akun_lawan">
                            <option value="">Pilih</option>
                            <?php
                            $tampil2=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='9fafb128-3890-4441-9c7a-8612b0db79a3' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil2)){
                                $a=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r[uid]'"));
                                $b=pg_fetch_array(pg_query($conn,"SELECT SUM(kredit) AS kredit FROM keu_akun_log WHERE id_status='3' AND uid_akun='$r[uid]'"));
                                $saldo = formatAngka($a['saldo']-$b['kredit']);
                                if($r['uid']==$d['uid_akun_lawan']){
                                    echo"<option value='$r[uid]' selected>$r[nomor] - $r[nama] ( $saldo )</option>";
                                }
                                else{
                                    echo"<option value='$r[uid]'>$r[nomor] - $r[nama] ( $saldo )</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Jumlah <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan nominal transaksi ini"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control money" name="jumlah" placeholder="Isi nominal transaksi" value="<?php echo formatAngka($d['jumlah']);?>" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Referensi <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan keterangan mengenai transaksi ini atau keterangan dari buku bank atau rekening koran Anda untuk memudahkan rekonsiliasi"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="referensi" placeholder="Cth. Transfer SMS, Setoran, Pinjaman, dll" value="<?php echo $d['keterangan'];?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Keterangan Perubahan <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan keterangan mengapa transaksi ini berubah"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="alasan_edit" required>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan</button>
    <a href="pemasukanlain" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
$("#id_jenis").change(function() {
	var id_jenis=$("#id_jenis").val();
	var data = 'id_jenis='+id_jenis;
	$.ajax({
		type: "POST",
		url: "data-akunlawan-pemasukanlain",
		data: data,
		beforeSend: function(){ 
			$(".preloader").show();
		},
		complete: function(){
			$(".preloader").hide();
		},
		success: function(data){
			$("#akun_lawan").html(data);
            console.log(data);
		}
	});
});
</script>