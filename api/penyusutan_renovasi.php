<?php
include "../konfig/koneksi.php";
$nilai_awal = $_POST['nilai_awal'];

$jumlah_bayar = $_POST['penyusutan'];
$waktu = $_POST['waktu'];
$id_data = $_POST['id_data'];
$tabel = "penyusutan_asset";
$id_divisi = $_POST['id_divisi'];

//1 : laut
//2 : udara

if($id_divisi=='1'){
    $uid_akun_beban_renovasi="5ed6a249-45e8-fcb1-136e-220a334fd8f7";
    $uid_akun_akk_penyusutan_renovasi="fb7d1988-c6a4-7d93-04ff-218538e9ebf9";
}
else{
    $uid_akun_beban_renovasi="5ed6a249-45e8-fcb1-136e-220a334fd8f7";
    $uid_akun_akk_penyusutan_renovasi="fb7d1988-c6a4-7d93-04ff-218538e9ebf9";
}

//EFEK TERHADAP DATA renovasi
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_beban_renovasi' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_beban_renovasi', '$waktu', '54', '$jumlah_bayar', '0', '$saldo', '$id_data', '$tabel', '$uid_akun_akk_penyusutan_renovasi')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_beban_renovasi' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_beban_renovasi'");


//EFEK TERHADAP AKUMULASI PENYUSUTAN
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_akk_penyusutan_renovasi' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_akk_penyusutan_renovasi', '$waktu', '54', '0', '$jumlah_bayar', '$saldo', '$id_data', '$tabel', '$uid_akun_beban_renovasi')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_akk_penyusutan_renovasi' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_akk_penyusutan_renovasi'");
?>