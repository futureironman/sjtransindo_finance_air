<?php
//error_reporting(0);
include "konfig/koneksi.php";
include "konfig/library.php";
//include("konfig/myencrypt.php");
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
	// username and password sent from Form
	$username = pg_escape_string($conn, $_POST['username']);
	//Here converting passsword into MD5 encryption.
	$password = pg_escape_string($conn, $_POST['password']);

	//$result=pg_query($conn,"SELECT * FROM pegawai WHERE email='$username'");
	$buat = '"create"';
	$edit = '"update"';
	$baca = '"read"';
	$hapus = '"delete"';

	$row = pg_fetch_array(pg_query($conn, "SELECT * FROM pegawai WHERE email='$username' AND deleted_at IS NULL AND id_divisi='2'"));
	if ($row['uid'] != '') {
		if (password_verify($password, $row['password'])){
			echo "ok";
			include "timeout.php";
			timer();

			session_regenerate_id();
			$sid = session_id();
			$_SESSION['login_user'] = $row['uid'];
			$_SESSION['divisi'] = $row['id_divisi'];
			$_SESSION['id_session'] = $sid;
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['login_finance'] = 1;

		}
		else{
			echo "0";
		}
	}
}
?>