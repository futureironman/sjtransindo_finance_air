<?php
//error_reporting(0);
include "../../konfig/koneksi.php";
//$a=pg_fetch_array(pg_query($conn,"SELECT tabel_data FROM keu_akun_jenis_linked WHERE id='$_POST[id_jenis]'"));
if($_POST['id_jenis']=='customer'){
	$sql="SELECT a.uid, a.nama FROM customer a WHERE NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_data=CAST(a.uid AS UUID)) ORDER BY nama ASC";
}
else if($_POST['id_jenis']=='master_supplier'){
	$sql="SELECT a.uid, a.nama FROM master_supplier a WHERE a.deleted_at IS NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_data=a.uid) ORDER BY nama ASC";
}
else if($_POST['id_jenis']=='pegawai'){
	$sql="SELECT a.uid, a.nama FROM pegawai a WHERE a.deleted_at IS NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_data=CAST(a.uid AS UUID)) ORDER BY nama ASC";
}
else if($_POST['id_jenis']=='detail_barang'){
	$sql="SELECT uid, nama_barang AS nama FROM detail_barang ORDER BY nama_barang";
}
else if($_POST['id_jenis']=='asset'){
	$sql="SELECT uid, nama_asset AS nama FROM asset ORDER BY nama_asset";
}
?>
<div class="form-group focused">
	<label class="form-control-label">Data</label>
	<select name="uid_data" class="form-control">
		<?php
		$tampil=pg_query($conn,$sql);
		while($r=pg_fetch_array($tampil)){
			echo"<option value='$r[uid]'>$r[nama]</option>";
		}
		?>
	</select>
</div>