<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="biaya">Biaya</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
            <h4 class="m-0">Tambah Biaya</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-tambah-biaya">
    <div class="card card-form">
        <div class="row no-gutters">
            <div class="col-lg-4 card-body">
                <p><strong class="headings-color">Biaya Baru</strong></p>
                <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
            </div>
            <div class="col-lg-8 card-form__body card-body">
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
                    <label class="col-md-4 pt-2">Akun Keuangan <span class="red">*</span></label>
                    <div class="col-md-8">
                        <select name="uid_akun_kas" class="form-control" required>
                            <option value="">Pilih</option>
                        <?php
                            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='2e57b1b3-875c-fa51-5b39-1945eca33202'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                            while($r=pg_fetch_array($tampil)){
                                $saldo = formatAngka($r['saldo_terkini']);
                                echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Nama Biaya <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan nama untuk biaya ini"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="nama_biaya" placeholder="Cth. Biaya Bensin, biaya parkir, dll" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Vendor <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan nama vendor yang Anda bayar atau beli dari untuk biaya ini jika ada"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="vendor" placeholder="Cth. Pertamina, Toko Bangunan, dll" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Akun Beban<span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Pilih akun beban untuk memudahkan Anda membuat laporan"></i></label>
                    <div class="col-md-8">
                        <select name="uid_akun_lawan" class="form-control select2" required >
                        <option value="">Pilih</option>
                        <?php
                        $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent='b1fa317d-d2b1-f990-73e9-814706b766f9' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='e30b8dac-8727-910d-c2d5-2e9c129bf31f'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='32fb1550-355e-23fe-20ff-623edb806fc3'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) 
                        OR a.deleted_at IS NULL AND a.uid_parent='68d46d35-3a9e-ed19-0744-ac5b0a19463a'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                        OR a.deleted_at IS NULL AND a.uid_parent='5e781c46-ab69-6333-449d-9e3bf26caa09'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                        OR a.deleted_at IS NULL AND a.uid_parent='dd6fcc59-fd10-01b2-3054-5d90ec4e60f8'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
                        ORDER BY a.nomor");
                        while($r=pg_fetch_array($tampil)){
                            $saldo = formatAngka($r['saldo_terkini']);
                            echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                        }
                        ?>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Jumlah <span class="red">*</span> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan nominal biaya ini"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control money" name="jumlah" placeholder="Isi nominal transaksi" required>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-md-4 pt-2">Keterangan <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Masukkan keterangan mengenai biaya ini"></i></label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="referensi" placeholder="Cth. Parkir di mall, bensin premium 5L">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan</button>
    <a href="penjualan" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>