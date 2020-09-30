<form action="aksi-tambah-coa" method="POST" enctype="multipart/form-data">
	<div class="modal-dialog modal-md a-lightSpeed">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-standard-title">Tambah</h5>
			</div>
			<div class="modal-body" id="form-data">
                <div class="form-group focused">
                    <label class="form-control-label">Nomor</label>
                    <input type="text"  class="form-control form-control-alternative" placeholder="" name="nomor" autofocus required id="nomor">
				</div>
				<div class="form-group focused">
                    <label class="form-control-label">Nama Akun</label>
                    <input type="text"  class="form-control form-control-alternative" placeholder="" name="nama" required>
				</div>
				<div class="form-group focused">
                    <label class="form-control-label">Jenis Akun</label>
                    <select name="jenis_akun" class="form-control">
						<option value="D">Debet</option>
						<option value="K">Kredit</option>
					</select>
				</div>
				<!--<div class="form-group focused">
                    <label class="form-control-label">Jenis Akun</label>
                    <select name="id_jenis" class="form-control">
						<?php
						$tampil=pg_query($conn,"SELECT * FROM keu_akun_jenis ORDER BY id");
						while($r=pg_fetch_array($tampil)){
							echo"<option value='$r[id]'>$r[nama]</option>";
						}
						?>
					</select>
				</div>-->
				<div class="form-group focused">
					<label class="form-control-label">Deskripsi</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                </div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success btn-md"><i class="fa fa-save"></i> Simpan</button>
				<button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="fa fa-ban"></i> Batal</button>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">

</script>