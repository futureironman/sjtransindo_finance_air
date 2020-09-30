<?php
//session_start();
function timer(){
	$time=3000;
	$_SESSION['timeout']=time()+$time;
}
function cek_login(){
	$timeout=$_SESSION['timeout'];
	if(time()<$timeout){
		timer();
		return true;
	}else{
		include "konfig/koneksi.php";
		mysqli_query($conn,"UPDATE web_users SET status_login='N' WHERE id='$_SESSION[login_user]'");
		unset($_SESSION['timeout']);
		return false;
	}
}
?>
