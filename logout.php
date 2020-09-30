<?php
session_start();
include_once "konfig/koneksi.php";
include_once "konfig/library.php";
if(!empty($_SESSION['login_admin']))
{
	pg_query($conn,"UPDATE pegawai SET is_login='N' WHERE uid='$_SESSION[login_user]'");
	
	pg_query($conn,"UPDATE log_login SET waktu_logout='$waktu_sekarang' WHERE id_session='$_SESSION[id_session]'");

	session_unset(); 
	session_destroy();
	
}
header("location:index.php");
?>