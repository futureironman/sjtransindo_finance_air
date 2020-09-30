<?php
date_default_timezone_set("Asia/Jakarta");
$seminggu = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
$hari = date("w");
$hari_ini = $seminggu[$hari];

$tgl_sekarang = date("Y-m-d");
$tgl_kemarin = date("Y-m-d", strtotime("-1 days"));
$tgl_skrg     = date("d");
$bln_sekarang = date("m");
$thn_sekarang = date("Y");
$jam_sekarang = date("H:i:s");
$waktu_sekarang = date("Y-m-d H:i:s");
$nama_bln=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
                    "Juni", "Juli", "Agustus", "September", 
                    "Oktober", "November", "Desember");

$date = date_create($waktu_sekarang);			
$waktu_end = date_add($date, date_interval_create_from_date_string('2 hours'));
$waktu_end = date_format($waktu_end, 'Y-m-d H:i:s');

$tahun = date('Y');
$bulan = date('m');
$thn = substr($tahun,-2);
$kode_now = $thn.$bulan;
$kode_now_tgl = $thn.$bulan.$tgl_skrg;

$today=date('Y-m-d');
$besok = date('Y-m-d', strtotime("+1 day", strtotime($today)));
$kemarin = date('Y-m-d', strtotime("-1 day", strtotime($today)));
$minggu_lalu = date('Y-m-d', strtotime("-7 day", strtotime($today)));
?>
