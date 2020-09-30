<?php
include "../konfig/koneksi.php";
$nilai_awal = $_POST['nilai_awal'];

$uid_barang = $_POST['uid_barang'];
$jumlah_bayar = $_POST['penyusutan'];
$waktu = $_POST['waktu'];
$id_data = $_POST['id_data'];
$tabel = "penyusutan_asset";
$id_divisi = "$_POST[id_divisi]";

$uid_akun_beban_penyusutan="9424758d-1902-7bdf-0f73-7c218889eece";
$a=pg_fetch_array(pg_query($conn,"SELECT uid FROM keu_akun WHERE uid_data='$uid_barang' AND linked_table='asset' AND jenis_akun='K'"));
$uid_akun_akhir=$a['uid'];


//EFEK TERHADAP DATA INVENTARIS
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_beban_penyusutan' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_beban_penyusutan', '$waktu', '54', '$jumlah_bayar', '0', '$saldo', '$id_data', '$tabel', '$uid_akun_akhir')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_beban_penyusutan' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_beban_penyusutan'");


//EFEK TERHADAP AKUMULASI PENYUSUTAN
$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_akhir' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

$saldo = $d['saldo']+$jumlah_bayar;

$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_akhir', '$waktu', '54', '0', '$jumlah_bayar', '$saldo', '$id_data', '$tabel', '$uid_akun_beban_penyusutan')";

pg_query($conn,$sql);

pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$uid_akun_akhir' AND created_at>'$waktu'");

//UPDATE SALDO DI KEU AKUN
pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$uid_akun_akhir'");
?>