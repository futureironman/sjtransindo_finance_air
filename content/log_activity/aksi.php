
<?php
session_start();
error_reporting(0);
if (empty($_SESSION['login_user'])){
	header('location:keluar');
}
else{
    include "../../konfig/koneksi.php";
	include "../../konfig/library.php";
	include "../../konfig/fungsi_tanggal.php";
	
	$act=$_GET['act'];
	if($act=='data'){
		include "data_json.php";
	}
}
?>