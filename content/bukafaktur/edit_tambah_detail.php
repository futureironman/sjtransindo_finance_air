<form action="edit-tambah-detail-bukafaktur" method="POST" enctype="multipart/form-data">
	<div class="modal-dialog modal-md a-lightSpeed">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-standard-title">Tambah</h5>
			</div>
			<div class="modal-body" id="form-data">
         <div class="form-group focused">
            <input type="hidden" value="<?= $_POST["uid_akun_bank"] ?>" name="uid_akun_bank">
            <input type="hidden" value="<?= $_POST["uid"] ?>" name="uid">
              
            <div class="form-group focused">
                    <label class="form-control-label">Jenis Akun</label>
                    <select name="uid_akun" class="form-control modal_select2">
						<?php
						$tampil=pg_query($conn,"SELECT * FROM keu_akun where id_divisi='$_SESSION[divisi]'");
						while($r=pg_fetch_array($tampil)){
							echo"<option value='$r[uid]'>$r[nama]</option>";
						}
						?>
					</select>
				</div>       
            <div class="form-group focused">
               <label class="form-control-label">Jumlah</label>
               <div class="input-group mb-3">
                  <input type="text" class="form-control form-control-alternative money" placeholder="" name="jumlah" required>
               </div>
            </div>
			
				<div class="form-group focused">
					<label class="form-control-label">Keterangan</label>
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
<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
$('.modal_select2').select2({
        dropdownParent: $('#form-modal')
    });
</script>