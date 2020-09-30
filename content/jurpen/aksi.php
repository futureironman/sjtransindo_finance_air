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
	if ($act=='input'){
        //SIMPAN DATA DAHULU DI KEU AKUN TRANSAKSI LAIN
        $jumlah_bayar=str_replace(".","",$_POST['jumlah']);
        $waktu = $_POST['tanggal'].' '.$_POST['jam'];

        $a=pg_fetch_array(pg_query($conn,"SELECT kode FROM keu_akun_transaksi_lain_jenis WHERE id='15'"));
        $kode_jenis=$a['kode'];
        $nama_jenis="Jurnal Penyesuaian";

        $d=pg_fetch_array(pg_query($conn,"SELECT MAX(nomor) AS nomor FROM keu_akun_transaksi_lain WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND nomor LIKE '$kode_jenis%'"));
		$kode_before = substr($d['nomor'],0,6);
        $kode_now=$kode_jenis.$thn.$bln_sekarang;
        
		if($kode_before==$kode_now){
			$no_urut = (int) substr($d['nomor'],6,4);
			$no_urut++;
			$no_urut_baru = $kode_before.sprintf("%04s",$no_urut);
		}
		else{
			$no_urut_baru = $kode_now.sprintf("%04s",1);
        }

        $sql="INSERT INTO keu_akun_transaksi_lain (uid_akun_kas, uid_akun_lawan, created_at, waktu, id_jenis, jumlah, keterangan, nomor, id_divisi) VALUES ('$_POST[uid_akun_kas]', '$_POST[uid_akun_lawan]', '$waktu_sekarang', '$waktu', '15', '$jumlah_bayar', '$_POST[referensi]', '$no_urut_baru', '$_SESSION[divisi]') RETURNING uid";
        $d=pg_fetch_array(pg_query($conn,$sql));
        $uid_transaksi_lain=$d['uid'];

        //AKUN KAS ASAL 
        //CEK APAKAH SIFATNYA DEBET ATAU KREDIT
        $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_kas]' AND deleted_at IS NULL"));
        
        //CEK SALDO TERAKHIR AKUN
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']-$jumlah_bayar;

        if($a['jenis_akun']=='D'){
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_kas]', '$waktu', '52', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]')";
        }
        else{
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_kas]', '$waktu', '52', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]')";
            
        }
        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
            
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");


        
        //AKUN KAS TUJUAN
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']+$jumlah_bayar;

        //CEK APAKAH SIFATNYA DEBET ATAU KREDIT
        $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]' AND deleted_at IS NULL"));
        if($a['jenis_akun']=='D'){
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '52', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";
        }
        else{
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '52', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";
        }
        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");


        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '15', '$_POST[referensi]', 'keu_akun_transaksi_lain') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN KAS SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN KAS SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);


		// insert pegawai_activity_log
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert jurnal penyesuaian','$uid_transaksi_lain','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");
        header("location: jurpen");

    }

    else if ($act=='update'){
        //HAPUS DATA LAMA
        $tampil=pg_query($conn,"SELECT * FROM keu_akun_log WHERE id_data='$_POST[uid]' AND tabel='keu_akun_transaksi_lain'");
        while($r=pg_fetch_array($tampil)){
            if($r['debet']=='0'){
                //INI BERARTI SALDO BERTAMBAH
                $kredit=$r['kredit'];
                $sql="UPDATE keu_akun_log SET saldo=(saldo+$kredit) WHERE uid_akun='$r[uid_akun]' AND created_at>='$r[created_at]'";
                pg_query($conn,$sql);

                //UPDATE AKUN SALDO
                pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$kredit) WHERE uid='$r[uid_akun]'");
            }
            else{
                //INI BERARTO SALDO BERKURANG
                $debet=$r['debet'];
                $sql="UPDATE keu_akun_log SET saldo=(saldo-$debet) WHERE uid_akun='$r[uid_akun]' AND created_at>='$r[created_at]'";
                pg_query($conn,$sql);

                //UPDATE AKUN SALDO
                pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$debet) WHERE uid='$r[uid_akun]'");
            }
            
        }

        //HAPUS DARI TRANSAKSI LOG
        pg_query($conn,"DELETE FROM keu_akun_log  WHERE id_data='$_POST[uid]' AND tabel='keu_akun_transaksi_lain'");

        //HAPUS DARI JURNAL
        $c=pg_fetch_array(pg_query($conn,"SELECT id FROM keu_akun_jurnal WHERE uid_data='$_POST[uid]' AND linked_table='keu_akun_transaksi_lain'"));

        pg_query($conn,"DELETE FROM keu_akun_jurnal WHERE uid_data='$_POST[uid]' AND linked_table='keu_akun_transaksi_lain'");

        pg_query($conn,"DELETE FROM keu_akun_jurnal_detail WHERE id_data='$c[id]'");


        //SIMPAN DATA BARU DI KEU AKUN TRANSAKSI LAIN
        $jumlah_bayar=str_replace(".","",$_POST['jumlah']);
        $waktu = $_POST['tanggal'].' '.$_POST['jam'];

        $sql="UPDATE keu_akun_transaksi_lain SET waktu='$waktu', jumlah='$jumlah_bayar', keterangan='$_POST[referensi]', alasan_edit='$_POST[alasan_edit]', updated_at='$waktu_sekarang', uid_akun_kas='$_POST[uid_akun_kas]', uid_akun_lawan='$_POST[uid_akun_lawan]' WHERE uid='$_POST[uid]'";
        $uid_transaksi_lain=$_POST['uid'];
        pg_query($conn,$sql);
        

        //AKUN KAS ASAL 
        //CEK APAKAH SIFATNYA DEBET ATAU KREDIT
        $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_kas]' AND deleted_at IS NULL"));
        
        //CEK SALDO TERAKHIR AKUN
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']-$jumlah_bayar;

        if($a['jenis_akun']=='D'){
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_kas]', '$waktu', '52', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]')";
        }
        else{
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_kas]', '$waktu', '52', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]')";
            
        }
        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
            
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");


        
        //AKUN KAS TUJUAN
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']+$jumlah_bayar;

        //CEK APAKAH SIFATNYA DEBET ATAU KREDIT
        $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]' AND deleted_at IS NULL"));
        if($a['jenis_akun']=='D'){
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '52', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";
        }
        else{
            $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '52', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";
        }
        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");


        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '15', '$_POST[referensi]', 'keu_akun_transaksi_lain') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN KAS SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN KAS SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        

		// insert pegawai_activity_log
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('update jurnal penyesuaian','$_POST[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");
        header("location: jurpen");
    }

    else if ($act=='delete'){
        //HAPUS DATA LAMA
        $tampil=pg_query($conn,"SELECT * FROM keu_akun_log WHERE id_data='$_GET[id]' AND tabel='keu_akun_transaksi_lain'");
        while($r=pg_fetch_array($tampil)){
            if($r['debet']=='0'){
                //INI BERARTI SALDO BERTAMBAH
                $kredit=$r['kredit'];
                $sql="UPDATE keu_akun_log SET saldo=(saldo+$kredit) WHERE uid_akun='$r[uid_akun]' AND created_at>='$r[created_at]'";
                pg_query($conn,$sql);

                //UPDATE AKUN SALDO
                pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$kredit) WHERE uid='$r[uid_akun]'");
            }
            else{
                //INI BERARTO SALDO BERKURANG
                $debet=$r['debet'];
                $sql="UPDATE keu_akun_log SET saldo=(saldo-$debet) WHERE uid_akun='$r[uid_akun]' AND created_at>='$r[created_at]'";
                pg_query($conn,$sql);

                //UPDATE AKUN SALDO
                pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$debet) WHERE uid='$r[uid_akun]'");
            }
            
        }

        //HAPUS DARI TRANSAKSI LOG
        pg_query($conn,"DELETE FROM keu_akun_log  WHERE id_data='$_GET[id]' AND tabel='keu_akun_transaksi_lain'");

        //HAPUS DARI JURNAL
        $c=pg_fetch_array(pg_query($conn,"SELECT id FROM keu_akun_jurnal WHERE uid_data='$_GET[id]' AND linked_table='keu_akun_transaksi_lain'"));

        pg_query($conn,"DELETE FROM keu_akun_jurnal WHERE uid_data='$_GET[id]' AND linked_table='keu_akun_transaksi_lain'");

        pg_query($conn,"DELETE FROM keu_akun_jurnal_detail WHERE id_data='$c[id]'");


        $sql="UPDATE keu_akun_transaksi_lain SET deleted_at='$waktu_sekarang' WHERE uid='$_GET[id]'";
        pg_query($conn,$sql);

		// insert pegawai_activity_log
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('delete jurnal penyesuaian','$_GET[id]','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");
        header("location: jurpen");
    }

    else if ($act=='cetak'){
        include "cetak.php";
    }
	pg_close($conn);
}
?>