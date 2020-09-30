<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="pengeluaranlain">Pengeluaran Lain</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
            <h4 class="m-0">Tambah Pengeluaran Lain</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-tambah-pengeluaranlain">
    <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-4 card-body">
                <p><strong class="headings-color">Pengeluaran Lain</strong></p>
                <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
            </div>
            <div class="col-lg-8 card-form__body card-body">
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Kategori <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih kategori pengeluaran lain"></i></label>
                    <div class="col-md-8">
                        <select name="id_jenis" class="form-control" required id="id_jenis">
                            <option value="">Pilih</option>
                            <?php
                            $tampil=pg_query($conn,"SELECT * FROM keu_akun_transaksi_lain_jenis WHERE id BETWEEN '7' AND '10' OR id='16' ORDER BY id");
                            while($r=pg_fetch_array($tampil)){
                                echo"<option value='$r[id]'>$r[nama]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                    <div class="col-md-4">
                        <input type="date" class="form-control" name="tanggal" required max="<?php echo $tgl_sekarang;?>">
                    </div>
                    <div class="col-md-4">
                        <input type="time" class="form-control" name="jam" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Akun Bank/Kas <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih akun bank atau kas yang mengeluarkan dana dalam perusahaan"></i></label>
                    <div class="col-md-8">
                        <select name="uid_akun_kas" class="form-control" required>
                        <option value="">Pilih</option>
                        <?php
                            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil)){
                                $saldo = formatAngka($r['saldo_terkini']);
                                echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Akun Debet <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih akun yang akan didebit pada modul Akunting"></i></label>
                    <div class="col-md-8">
                        <select name="uid_akun_lawan" class="form-control select2" required id="akun_lawan">
                        <option value="">Pilih</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Jumlah <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan nominal transaksi ini"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control money" name="jumlah" placeholder="Isi nominal transaksi" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Referensi <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan keterangan mengenai transaksi ini atau keterangan dari buku bank atau rekening koran Anda untuk memudahkan rekonsiliasi"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="referensi" placeholder="Cth. Transfer SMS, Setoran, Pinjaman, dll">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan</button>
    <a href="pengeluaranlain" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
$("#id_jenis").change(function() {
	var id_jenis=$("#id_jenis").val();
	var data = 'id_jenis='+id_jenis;
	$.ajax({
		type: "POST",
		url: "data-akunlawan-pengeluaranlain",
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