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
	if ($act=='akunlawan'){
        if($_POST['id_jenis']=='2'){
            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
        }
        else if($_POST['id_jenis']=='3'){
            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='df562c24-4212-e630-2519-e6a837631384' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='ee912c5e-19d6-c3f9-17db-3c3615529887'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
        }
        else if($_POST['id_jenis']=='4'){
            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='e97aa038-cb58-755b-ca04-26f9b0c04e50' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='fd63c345-8b6f-0a02-da8a-920e69d12037'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
        }
        else if($_POST['id_jenis']=='5'){
            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='66dc70f2-9e6e-be0e-07a5-205e18625e03' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
        }
        else if($_POST['id_jenis']=='6'){
            $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor, a.saldo_terkini FROM keu_akun a WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent='50dfe8a6-9e98-202d-8317-903ada6b60c7' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='c32c7fe0-8995-6627-2fe6-bcbf90edc307'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) OR a.deleted_at IS NULL AND a.uid_parent='e5576e62-46af-812e-8a6b-ef0420e0e5c9'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) 
            OR a.deleted_at IS NULL AND a.uid_parent='7df866d0-d493-68d8-2dde-419e265ebbda'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
            OR a.deleted_at IS NULL AND a.uid_parent='dd0af3a6-ff44-6c73-3aff-2c262d89ce59'AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid)
            ORDER BY a.nomor");
        }
        echo"<option value=''>Pilih</option>";
        while($r=pg_fetch_array($tampil)){
            echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
        }
	}

    else if ($act=='input'){
        //SIMPAN DATA DAHULU DI KEU AKUN TRANSAKSI LAIN
        $jumlah_bayar=str_replace(".","",$_POST['jumlah']);
        $waktu = $_POST['tanggal'].' '.$_POST['jam'];

        $d=pg_fetch_array(pg_query($conn,"SELECT nama, kode FROM keu_akun_transaksi_lain_jenis WHERE id='$_POST[id_jenis]'"));
        $nama_jenis=$d['nama'];
        $kode_jenis=$d['kode'];
        //cek status untuk keu akun log
        if($_POST['id_jenis']=='2'){
            $id_status=32;
        }
        else if($_POST['id_jenis']=='3'){
            $id_status=34;
        }
        else if($_POST['id_jenis']=='4'){
            $id_status=36;
        }
        else if($_POST['id_jenis']=='5'){
            $id_status=38;
        }
        else if($_POST['id_jenis']=='6'){
            $id_status=40;
        }

        $d=pg_fetch_array(pg_query($conn,"SELECT MAX(nomor) AS nomor FROM keu_akun_transaksi_lain WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND nomor LIKE '$kode_jenis%'"));
		$kode_before = substr($d['nomor'],0,4);
        $kode_now=$kode_jenis.$thn;
        
		if($kode_before==$kode_now){
			$no_urut = (int) substr($d['nomor'],4,6);
			$no_urut++;
			$no_urut_baru = $kode_before.sprintf("%06s",$no_urut);
		}
		else{
			$no_urut_baru = $kode_now.sprintf("%06s",1);
        }

        $sql="INSERT INTO keu_akun_transaksi_lain (uid_akun_kas, uid_akun_lawan, created_at, waktu, id_jenis, jumlah, keterangan, nomor, id_divisi) VALUES ('$_POST[uid_akun_kas]', '$_POST[uid_akun_lawan]', '$waktu_sekarang', '$waktu', '$_POST[id_jenis]', '$jumlah_bayar', '$_POST[referensi]', '$no_urut_baru', '$_SESSION[divisi]') RETURNING uid";
        $d=pg_fetch_array(pg_query($conn,$sql));
        $uid_transaksi_lain=$d['uid'];

        
        //AKUN KAS BERSIFAT DEBET
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY created_at DESC LIMIT 1"));
        $saldo = $d['saldo']+$jumlah_bayar;

        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek, keterangan_detail) VALUES ('$_POST[uid_akun_kas]', '$waktu', '$id_status', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]', '$_POST[referensi]')";

        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
        
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");
        


        //AKUN KAS YANG AKAN DI KREDITKAN
        //CEK SALDO 
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY created_at DESC LIMIT 1"));

        $e=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]'"));
        if($e['jenis_akun']=='D'){
            $saldo = $d['saldo']-$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");

        }
        else{
            $saldo = $d['saldo']+$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            //UPDATE SALDO DI KEU AKUN
            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");
        }
        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek, keterangan_detail) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '$id_status', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]', '$_POST[referensi]')";

        pg_query($conn,$sql);

        

        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table, id_divisi) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '$_POST[id_jenis]', '$_POST[referensi]', 'keu_akun_transaksi_lain', '$_SESSION[divisi]') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN KAS SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN LAWAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);


		// insert pegawai_activity_log
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert transaksi lain','$uid_transaksi_lain','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");
        header("location: pemasukanlain");

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
        
        //INSERT ULANG LAGI
        //cek status untuk keu akun log
        if($_POST['id_jenis']=='2'){
            $id_status=32;
        }
        else if($_POST['id_jenis']=='3'){
            $id_status=34;
        }
        else if($_POST['id_jenis']=='4'){
            $id_status=36;
        }
        else if($_POST['id_jenis']=='5'){
            $id_status=38;
        }
        else if($_POST['id_jenis']=='6'){
            $id_status=40;
        }

        $d=pg_fetch_array(pg_query($conn,"SELECT nama FROM keu_akun_transaksi_lain_jenis WHERE id='$_POST[id_jenis]'"));
        $nama_jenis=$d['nama'];

        $sql="UPDATE keu_akun_transaksi_lain SET waktu='$waktu', jumlah='$jumlah_bayar', keterangan='$_POST[referensi]', alasan_edit='$_POST[alasan_edit]', id_jenis='$_POST[id_jenis]', updated_at='$waktu_sekarang', uid_akun_kas='$_POST[uid_akun_kas]', uid_akun_lawan='$_POST[uid_akun_lawan]' WHERE uid='$_POST[uid]'";
        pg_query($conn,$sql);
        $uid_transaksi_lain=$_POST['uid'];

        
        //AKUN KAS BERSIFAT DEBIT
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY created_at DESC LIMIT 1"));
        $saldo = $d['saldo']+$jumlah_bayar;

        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek, keterangan_detail) VALUES ('$_POST[uid_akun_kas]', '$waktu', '$id_status', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]', '$_POST[referensi]')";

        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
        
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");
        


        //AKUN KAS YANG AKAN DI KREDITKAN
        //CEK SALDO 
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY created_at DESC LIMIT 1"));

        $e=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]'"));
        if($e['jenis_akun']=='D'){
            $saldo = $d['saldo']-$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");

        }
        else{
            $saldo = $d['saldo']+$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            //UPDATE SALDO DI KEU AKUN
            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");
        }
        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek, keterangan_detail) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '$id_status', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]', '$_POST[referensi]')";

        pg_query($conn,$sql);

        

        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table, id_divisi) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '$_POST[id_jenis]', '$_POST[referensi]', 'keu_akun_transaksi_lain', '$_SESSION[divisi]') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN KAS SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN LAWAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        
		// insert pegawai_activity_log
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('update transaksi lain','$_POST[uid]','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");
        
        header("location: pemasukanlain");
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
		// $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun where no_bukti='$no_bukti' and deleted_at is NULL "));
		pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('delete transaksi lain','$_GET[id]','$waktu_sekarang','$_SESSION[login_user]','keu_akun_transaksi_lain')");

        header("location: pemasukanlain");
    }

    else if ($act=='cetak'){
        include "cetak.php";
    }
	pg_close($conn);
}
?>