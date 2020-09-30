<?php
function encrypt($string){
	$hasil = base64_encode(base64_encode($string));
	return $hasil;
}

function decrypt($string){
	$hasil = base64_decode(base64_decode($string));
	return $hasil;
}
?>