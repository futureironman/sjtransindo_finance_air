<?php
$d=pg_fetch_array(pg_query($conn,"SELECT * FROM keu_akun WHERE uid='$_POST[id]'"));
?>
<form action="aksi-edit-coa" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="uid" value="<?php echo $_POST['id'];?>">
	<div class="modal-dialog modal-md a-lightSpeed">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-standard-title">Edit</h5>
			</div>
			<div class="modal-body" id="form-data">
                <div class="form-group focused">
                    <label class="form-control-label">Nomor</label>
                    <input type="text"  class="form-control form-control-alternative" placeholder="" name="nomor" autofocus required id="nomor" value="<?php echo $d['nomor'];?>">
				</div>
				<div class="form-group focused">
                    <label class="form-control-label">Nama Akun</label>
                    <input type="text"  class="form-control form-control-alternative" placeholder="" name="nama" required value="<?php echo $d['nama'];?>">
                </div>
                <div class="form-group focused">
                    <label class="form-control-label">Jenis Akun</label>
                    <select name="jenis_akun" class="form-control">
						<option value="D" <?php if($d['jenis_akun']=='D'){echo "selected";}?>>Debet</option>
						<option value="K" <?php if($d['jenis_akun']=='K'){echo "selected";}?>>Kredit</option>
					</select>
				</div>
				<!--
				<div class="form-group focused">
                    <label class="form-control-label">Jenis Akun</label>
                    <select name="id_jenis" class="form-control">
						<?php
						$tampil=pg_query($conn,"SELECT * FROM keu_akun_jenis ORDER BY id");
						while($r=pg_fetch_array($tampil)){
							if($r['id']==$d['id_jenis']){
								echo"<option value='$r[id]' selected>$r[nama]</option>";
							}
							else{
								echo"<option value='$r[id]'>$r[nama]</option>";
							}
						}
						?>
					</select>
				</div>
				-->
				<div class="form-group focused">
					<label class="form-control-label">Deskripsi</label>
                    <textarea name="keterangan" class="form-control"><?php echo $d['keterangan'];?></textarea>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success btn-md"><i class="fa fa-save"></i> Simpan</button>
				<button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="fa fa-ban"></i> Batal</button>
			</div>
		</div>
	</div>
</form>