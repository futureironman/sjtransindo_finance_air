<?php
$d=pg_fetch_array(pg_query($conn,"SELECT nomor, nama, jenis_akun, keterangan FROM keu_akun WHERE uid='$_POST[id]'"));
if($d['jenis_akun']=='D'){
    $jenis_akun="Debet";
}
else{
    $jenis_akun="Kredit";
}
?>
<form action="aksi-saldoawal-coa" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="uid_akun" value="<?php echo $_POST['id'];?>">
	<div class="modal-dialog modal-md a-lightSpeed" id="form_modal">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-standard-title">Saldo Awal</h5>
			</div>
			<div class="modal-body" id="form-data">
                <table class="table table-striped">
                    <tr>
                        <td width="150px">Nomor Akun</td><td width="10px">:</td><td><?php echo $d['nomor'];?></td>
                    </tr>
                    <tr>
                        <td>Nama</td><td>:</td><td><?php echo $d['nama'];?></td>
                    </tr>
                    <tr>
                        <td>Jenis Akun</td><td>:</td><td><?php echo $jenis_akun;?></td>
                    </tr>
                    <tr>
                        <td>Keterangan</td><td>:</td><td><?php echo $d['keterangan'];?></td>
                    </tr>
                </table>
                <div class="form-group focused">
                    <label class="form-control-label">Saldo Awal</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <select name="status" class="form-control">
                                <option value="plus">+</option>
                                <option value="minus">-</option>
                            </select>
                        </div>
                        <input type="text" class="form-control form-control-alternative money" placeholder="" name="saldoawal" required>
                    </div>
                </div>
                <div class="form-group focused">
                    <label class="form-control-label">Tanggal / Jam</label>
                    <div class="row">
                        <div class="col-md-7">
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                        <div class="col-md-5">
                            <input type="time" class="form-control" name="jam" required>
                        </div>
                    </div>
				</div>
                <div class="form-group focused">
                    <label class="form-control-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                </div>
                <div class="form-group focused">
                    <label class="form-control-label">Linked Type <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Silahkan dipilih jika ingin menambah/mengubah linked data"></i></label>
                    <select name="id_jenis" class="form-control" id="id_jenis">
                        <option value="">No Link</option>
                        <?php
                        $tampil=pg_query($conn,"SELECT * FROM keu_akun_jenis_linked ORDER BY id ASC");
                        while($r=pg_fetch_array($tampil)){
                            echo"<option value='$r[tabel_data]'>$r[nama]</option>";
                        }
                        ?>
                    </select>
                </div>
                <div id="linked"></div>
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
    $("#id_jenis").change(function() {
        var id_jenis=$("#id_jenis").val();
        if(id_jenis!=''){
            var data = 'id_jenis='+id_jenis;
            $.ajax({
                type: "POST",
                url: "data-linked",
                data: data,
                cache: false,
                success: function(data){
                    $("#linked").html(data);
                }
            });
        }
        else{
            $("#linked").html("");
        }
    });

    $(document).ready(function() {
             $(".select2").select2();
         });
</script>