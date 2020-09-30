<?php
session_start();
error_reporting(0);
if (empty($_SESSION['login_user'])){
	header('location:keluar');
}
else{
	include "../../konfig/koneksi.php";
    include "../../konfig/library.php";
    include "../../konfig/fungsi_thumb.php";
    include "../../konfig/myencrypt.php";

	$act=$_GET['act'];
    
    if ($act=='update'){
        $acak			 = rand(1,99);
		$lokasi_file     = $_FILES['fupload']['tmp_name'];
		$tipe_file       = $_FILES['fupload']['type'];
		$nama_file       = $_FILES['fupload']['name'];
		$nama_file_unik  = $acak.$nama_file;
		
		if ($_FILES["fupload"]["error"] > 0 OR empty($lokasi_file)){
			$nama_file_unik = "$_POST[foto]";
		}
	  
		else{
            UploadPegawai($nama_file_unik);
            unlink("../../images/pegawai/$_POST[foto]");
        }

        $password=encrypt($_POST['password']);
        
		$sql="UPDATE pegawai SET username='$_POST[username]', password='$password', foto='$nama_file_unik', updated_at='$waktu_sekarang' WHERE uid='$_SESSION[login_user]'";
		$result=pg_query($conn,$sql);

		pg_query($conn,"INSERT INTO log_modul(uid_pegawai, waktu, id_modul, aksi, id_data) VALUES ('$_SESSION[login_user]', '$waktu_sekarang', '23', 'U', '$_POST[id]')");

		header("location: profile");
    }
    
	pg_close($conn);
}
?>