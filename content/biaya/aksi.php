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

        $d=pg_fetch_array(pg_query($conn,"SELECT kode, nama FROM keu_akun_transaksi_lain_jenis WHERE id='11'"));
        $nama_jenis=$d['nama'];
        $kode_jenis=$d['kode'];
        $id_status=50;
        

        $d=pg_fetch_array(pg_query($conn,"SELECT MAX(nomor) AS nomor FROM keu_akun_transaksi_lain WHERE deleted_at IS NULL AND nomor LIKE '$kode_jenis%'"));
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

        $sql="INSERT INTO keu_akun_transaksi_lain (uid_akun_kas, uid_akun_lawan, created_at, waktu, id_jenis, jumlah, keterangan, nama_biaya, vendor, nomor) VALUES ('$_POST[uid_akun_kas]', '$_POST[uid_akun_lawan]', '$waktu_sekarang', '$waktu', '11', '$jumlah_bayar', '$_POST[referensi]', '$_POST[nama_biaya]', '$_POST[vendor]', '$no_urut_baru') RETURNING uid";
        $d=pg_fetch_array(pg_query($conn,$sql));
        $uid_transaksi_lain=$d['uid'];

        
        //AKUN KAS BERSIFAT KREDIT
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']-$jumlah_bayar;

        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek, keterangan_detail) VALUES ('$_POST[uid_akun_kas]', '$waktu', '$id_status', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]', '$_POST[referensi]')";

        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
        
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");
        


        //AKUN KAS YANG AKAN DI DEBETKAN
        //CEK SALDO 
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

        $e=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]'"));
        if($e['jenis_akun']=='D'){
            $saldo = $d['saldo']+$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");

        }
        else{
            $saldo = $d['saldo']-$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            //UPDATE SALDO DI KEU AKUN
            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");
        }
        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '$id_status', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";

        pg_query($conn,$sql);

        

        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '11', '$_POST[referensi]', 'keu_akun_transaksi_lain') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN LAWAN SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN KAS SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        header("location: biaya");

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

        
        $id_jenis="11";
        $id_status="50";

        $d=pg_fetch_array(pg_query($conn,"SELECT nama FROM keu_akun_transaksi_lain_jenis WHERE id='$id_jenis'"));
        $nama_jenis=$d['nama'];

        $sql="UPDATE keu_akun_transaksi_lain SET waktu='$waktu', jumlah='$jumlah_bayar', keterangan='$_POST[referensi]', alasan_edit='$_POST[alasan_edit]', updated_at='$waktu_sekarang', vendor='$_POST[vendor]', nama_biaya='$_POST[nama_biaya]' WHERE uid='$_POST[uid]'";
        pg_query($conn,$sql);
        $uid_transaksi_lain=$_POST['uid'];

        
        //AKUN KAS SEBAGAI KREDIT
        //CEK SALDO TERAKHIR AKUN KAS TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
        $saldo = $d['saldo']-$jumlah_bayar;

        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_kas]', '$waktu', '$id_status', '0', '$jumlah_bayar', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_lawan]')";

        pg_query($conn,$sql);

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_kas]' AND created_at>'$waktu'");
        
        //UPDATE SALDO DI KEU AKUN
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_kas]'");
        


        //AKUN KAS YANG AKAN DI KREDITKAN
        //CEK SALDO 
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

        $e=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$_POST[uid_akun_lawan]'"));
        if($e['jenis_akun']=='D'){
            $saldo = $d['saldo']+$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");

        }
        else{
            $saldo = $d['saldo']-$jumlah_bayar;
            pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_lawan]' AND created_at>'$waktu'");

            //UPDATE SALDO DI KEU AKUN
            pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$_POST[uid_akun_lawan]'");
        }
        
        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_lawan]', '$waktu', '$id_status', '$jumlah_bayar', '0', '$saldo', '$uid_transaksi_lain', 'keu_akun_transaksi_lain', '$_POST[uid_akun_kas]')";

        pg_query($conn,$sql);

        

        //PENCATATAN DI JURNAL
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti, linked_table) VALUES ('$waktu', '$uid_transaksi_lain', '$nama_jenis', '$id_jenis', '$_POST[referensi]', 'keu_akun_transaksi_lain') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN LAWAN SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_lawan]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN KAS SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$_POST[uid_akun_kas]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        
        //header("location: biaya");
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

        header("location: biaya");
    }

    else if ($act=='cetak'){
        include "cetak.php";
    }
	pg_close($conn);
}
?>