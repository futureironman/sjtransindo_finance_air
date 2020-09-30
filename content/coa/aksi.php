<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])){
	header('location:keluar');
}
else{
	include "../../konfig/koneksi.php";
	include "../../konfig/library.php";

	$act=$_GET['act'];
	if ($act=='cek'){
		
	}
	
	elseif($act=='tambah'){
		include "tambah.php";
	}
	elseif($act=='input'){
		$sql="INSERT INTO keu_akun (nomor, created_at, nama, keterangan, uid_pegawai, id_divisi, jenis_akun, saldo_terkini) VALUES ('$_POST[nomor]', '$waktu_sekarang', '$_POST[nama]', '$_POST[keterangan]', '$_SESSION[login_user]', '$_SESSION[divisi]', '$_POST[jenis_akun]', '0') RETURNING uid";

		$d=pg_fetch_array(pg_query($conn,$sql));
		
		// insert pegawai_activity_log
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert keu akun','$d[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun')");

		header("location: coa?message=input");
	}
	
	elseif($act=='tambah2'){
		include "tambah2.php";
	}

	elseif($act=='input2'){
		//$a=pg_fetch_array(pg_query($conn,"SELECT id_jenis FROM keu_akun WHERE uid='$_POST[uid_parent]'"));
		//$id_jenis=$a['id_jenis'];

		$sql="INSERT INTO keu_akun (nomor, created_at, nama, keterangan, uid_pegawai, id_divisi, uid_parent, jenis_akun, saldo_terkini) VALUES ('$_POST[nomor]', '$waktu_sekarang', '$_POST[nama]', '$_POST[keterangan]', '$_SESSION[login_user]', '$_SESSION[divisi]', '$_POST[uid_parent]', '$_POST[jenis_akun]', '0') RETURNING uid";

		$d=pg_fetch_array(pg_query($conn,$sql));

		//pg_query($conn,"INSERT INTO log_modul(uid_pegawai, waktu, id_modul, aksi, id_data) VALUES ('$_SESSION[login_user]', '$waktu_sekarang', '4', 'C', '$d[uid]')");
		
		// insert pegawai_activity_log
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert detail akun','$d[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun')");

		header("location: coa?message=input");
	}

	elseif($act=='edit'){
		include "edit.php";
	}
	
	elseif ($act=='update'){
		$sql="UPDATE keu_akun SET nomor='$_POST[nomor]', nama='$_POST[nama]', keterangan='$_POST[keterangan]', updated_at='$waktu_sekarang', jenis_akun='$_POST[jenis_akun]' WHERE uid='$_POST[uid]'";
		$result=pg_query($conn,$sql);

		//pg_query($conn,"INSERT INTO log_modul(uid_pegawai, waktu, id_modul, aksi, id_data) VALUES ('$_SESSION[login_user]', '$waktu_sekarang', '4', 'U', '$_POST[id]')");

		// insert pegawai_activity_log
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('update keu akun','$d[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun')");
		header("location: coa?message=update");
	}
	
	elseif($act=='delete'){
		$sql="UPDATE keu_akun SET deleted_at='$waktu_sekarang' WHERE uid='$_GET[id]'";
		$result=pg_query($conn,$sql);

		//pg_query($conn,"INSERT INTO log_modul(uid_pegawai, waktu, id_modul, aksi, id_data) VALUES ('$_SESSION[login_user]', '$waktu_sekarang', '4', 'D', '$_GET[id]')");

		// insert pegawai_activity_log
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('delete keu akun','$_GET[id]','$waktu_sekarang','$_SESSION[login_user]','keu_akun')");
		header("location: coa?message=delete");
	}

	elseif($act=='saldoawal'){
		include "saldoawal.php";
	}

	elseif($act=='inputsaldoawal'){
		//$d=pg_fetch_array(pg_query($conn,"SELECT a.*, b.efek_tambah FROM keu_akun a, keu_akun_jenis b WHERE a.id_jenis=b.id AND a.uid='$_POST[uid_akun]'"));
		
		$d=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun]'"));

		$saldo=str_replace(".","",$_POST['saldoawal']);
		
		$waktu = $_POST['tanggal'].' '.$_POST['jam'];

		if($_POST['status']=='minus'){
			$saldo = "-".$saldo;
		}

		//CEK DAHULU APAKAH SUDAH ADA FINANCE LOGNYA
		//JIKA SUDAH ADA
		$c=pg_fetch_array(pg_query($conn,"SELECT id FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun]'"));
		if($c['id']!=''){

			$d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
			$saldo_terkini=$d['saldo']+$saldo;

			//INSERT DAHULU 1 YANG DIINPUT INI
			$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo) VALUES ('$_POST[uid_akun]', '$waktu', '1', '$_POST[keterangan]', '0', '0', '$saldo_terkini')";
			pg_query($conn,$sql);

			//UPDATE KE BAGIAN KEU AKUN
			if($_POST['id_jenis']==''){
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$saldo) WHERE uid='$_POST[uid_akun]'");
			}
			else{
				pg_query($conn,"UPDATE keu_akun SET linked_table='$_POST[id_jenis]', saldo_terkini=(saldo_terkini+$saldo), uid_data='$_POST[uid_data]' WHERE uid='$_POST[uid_akun]'");
			}

			//LOOPING HITUNG ULANG LAGI
			/*
			$tampil=pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun]' AND created_at>'$waktu' ORDER BY created_at DESC");
			while($r=pg_fetch_array($tampil)){
				$saldo_baru=$r['saldo']+$saldo;
				//HITUNG ULANG SALDONYA
				pg_query($conn,"UPDATE keu_akun_log SET saldo='$saldo_baru' WHERE id='$r[id]'");
			}
			*/
			
			pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$saldo) WHERE uid_akun='$_POST[uid_akun]' AND created_at>'$waktu'");

			//UPDATE SALDO TERAKHIR
			//pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$saldo) WHERE uid='$_POST[uid_akun]'");
		}
		else{
			//JIKA BELUM ADA

			//INPUT KE BAGIAN FINANCE LOG
			
			$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo) VALUES ('$_POST[uid_akun]', '$waktu', '1', '$_POST[keterangan]', '0', '0', '$saldo')";
			pg_query($conn,$sql);

			
			//UPDATE KE BAGIAN KEU AKUN
			if($_POST['id_jenis']==''){
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$_POST[uid_akun]'");
			}
			else{
				pg_query($conn,"UPDATE keu_akun SET linked_table='$_POST[id_jenis]', saldo_terkini='$saldo', uid_data='$_POST[uid_data]' WHERE uid='$_POST[uid_akun]'");
			}

			//echo"UPDATE keu_akun SET linked_table='$_POST[id_jenis]', saldo_terkini='$saldo', uid_data='$_POST[uid_data]' WHERE uid='$_POST[uid_akun]'";
			//CEK DAHULU APAKAH SUDAH DI SET UNTUK SALDO BULANAN
			/*
			$bulan = date("m",strtotime($_POST['tanggal']));
			$tahun = date("Y",strtotime($_POST['tanggal']));

			$a=pg_fetch_array(pg_query($conn,"SELECT id FROM keu_akun_bulan_saldo WHERE id_bulan='$bulan' AND tahun='$tahun' AND uid_akun='$_POST[uid_akun]'"));
			if($a['id']==''){
				$sql="INSERT INTO keu_akun_bulan_saldo (id_bulan, tahun, uid_akun, saldo_awal) VALUES ('$bulan', '$tahun', '$_POST[uid_akun]', '$saldo')";
			}
			
			pg_query($conn,$sql);
			*/
		}
		
		// insert pegawai_activity_log
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('set saldo awal','$_POST[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun')");
		header("location: coa");
	}
	pg_close($conn);
}
?>