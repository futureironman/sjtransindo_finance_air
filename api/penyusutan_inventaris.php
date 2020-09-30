<?php
include "../konfig/koneksi.php";
$nilai_awal = $_POST['nilai_awal'];

$jumlah_bayar = $_POST['penyusutan'];
$waktu = $_POST['waktu'];
$id_data = $_POST['id_data'];
$tabel = "penyusutan_asset";
$id_divisi = $_POST['id_divisi'];

if($id_divisi=='1'){
    $uid_akun_beban_inventaris="214eb6b5-a6d8-bbfe-4a61-fa8cbb8eab75";
    $uid_akun_akk_penyusutan_inventaris="b151acf8-11f3-969a-b61e-2ae3a3f257ca";
}
else{
    $uid_akun_beban_inventaris="b423de6b-a749-50d9-17f9-71afa086a02d";
    $uid_akun_akk_penyusutan_inventaris="40d6b9f6-f0ab-d5d9-756f-e913062cb4eb";
}


//EFEK TERHADAP DATA INVENTARIS
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_beban_inventaris' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_beban_inventaris', '$waktu', '54', '$jumlah_bayar', '0', '$saldo', '$id_data', '$tabel', '$uid_akun_akk_penyusutan_inventaris')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_beban_inventaris' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_beban_inventaris'");


//EFEK TERHADAP AKUMULASI PENYUSUTAN
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_akk_penyusutan_inventaris' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_akk_penyusutan_inventaris', '$waktu', '54', '0', '$jumlah_bayar', '$saldo', '$id_data', '$tabel', '$uid_akun_beban_inventaris')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_akk_penyusutan_inventaris' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_akk_penyusutan_inventaris'");
?>