<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])) {
    header('location:keluar');
} else {
    include "../../konfig/koneksi.php";
    include "../../konfig/library.php";

    $act = $_GET['act'];
    if ($act == 'input') {
        
        //SIMPAN DANA DI PENERIMAAN DANA : DEBET UNTUK PENERIMAAN KAS
        $waktu = $_POST['tanggal'] . ' ' . $_POST['jam'];

        $no_faktur = $_POST["no_faktur"];
        $uid_akun_bank = $_POST["uid_akun_bank"];
        $total_pembayaran = $_POST["total_pembayaran"];
                    
                   
                        $cek = pg_query($conn, "SELECT * FROM keu_buka_faktur_detail WHERE no_faktur is null and deleted_at is null and uid_akun_bank='$uid_akun_bank'");
                        $keterangan_detail ='';
                        while($r=pg_fetch_array($cek)){
                            $uid_akun = $r["uid_akun_keperluan"];
                            $keterangan = $r["keterangan"];
                            $jumlah = $r["jumlah"];
                            $keterangan_detail .= $r["uid_akun_keperluan"] . ',';
                            
                          //CEK SALDO TERAKHIR akun kredit lebih kecil dari tanggal
                        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
                        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                        $saldo = $pembayaran_sebelum + $jumlah;
              
                        //CEK JENIS AKUN KEUANGAN
                        $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                        $jenis_akun = $a['jenis_akun'];

                         if($jenis_akun == 'D'){
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah', '0', '$saldo', '$keterangan', 'keu_buka_faktur_detail','$uid_akun_bank')";
                            pg_query($conn, $sql);

                        // Update akun efek pembayaran  sesudah tanggal
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");

                         
                        }
                        else{
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25','0','$jumlah','$saldo', '$keterangan', 'keu_buka_faktur_detail','$uid_akun_bank')";
                            pg_query($conn, $sql);
                            
                            // Update akun efek pembayaran  sesudah tanggal
                            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
                    }

                     //PENCATATAN DI JURNAl
                     $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid_akun', 'Buka Faktur', '1', '$keterangan')";
                     pg_query($conn,$sql);	    
                     $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                     $id_jurnal= $data["id_jurnal"];
                     //AKUN CUSTOMER SEBAGAI DEBET
                     $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                     pg_query($conn,$sql);
                }
                
                    //CEK SALDO TERAKHIR akun KAS KECIL (BCA) lebih kecil dari tanggal
                    
                    $keterangan_detail = substr($keterangan_detail, 0, -1);
                    $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_bank' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
                    $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                    $saldo = $pembayaran_sebelum - $total_pembayaran;

                     //PENCATATAN DI LOG KEU AKUN EFEK KAS KECIL (BCA)
                     $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan_detail, tabel) VALUES ('$uid_akun_bank', '$waktu', '25','0','$total_pembayaran','$saldo', '$keterangan_detail', 'keu_buka_faktur_detail')";
                     pg_query($conn, $sql);
                     
                     // Update akun efek KAS KECIL (BCA)  sesudah tanggal
                     pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_pembayaran) WHERE uid='$uid_akun_bank'");
                     pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total_pembayaran) WHERE uid_akun='$uid_akun_bank' AND created_at > '$waktu' ");
                       
                    // insert KEU BUKA FAKTUR
                    pg_query($conn, "INSERT INTO keu_buka_faktur (tanggal, no_faktur, total, uid_akun, keterangan_detail) VALUES ('$waktu', '$no_faktur', '$total_pembayaran', '$uid_akun_bank', '$keterangan_detail')");
                    
                    // UPDATE KEU BUKA FAKTUR DETAIL 
                    $sql= "UPDATE keu_buka_faktur_detail SET no_faktur= '$no_faktur', tanggal='$waktu' WHERE no_faktur is null and deleted_at is null and uid_akun_bank='$uid_akun_bank'";
                    pg_query($conn,$sql);	

            header("location: bukafaktur-$uid_akun_bank");
         }
                        
	elseif($act=='delete'){
        
        $a = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_buka_faktur WHERE uid='$_GET[uid]'"));
        
        // Soft Delete keu_akun_log KAS KECIL(BCA) 
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[tanggal]' and uid_akun='$a[uid_akun]'"; 
        pg_query($conn, $sql);
        // Update akun KAS KECIL(BCA)  sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total]) WHERE uid='$a[uid_akun]'");
        $sql= "UPDATE keu_akun_log SET saldo= (saldo + $a[total]) WHERE uid_akun='$a[uid_akun]' AND created_at > '$a[tanggal]'";
        pg_query($conn, $sql);
       
        // ---------------------------------------------------
        
        $splitID = explode(",",$a["keterangan_detail"]);
        foreach($splitID as $row){
           // Soft Delete keu_akun_log KEPERLUAN 
            $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[tanggal]' and uid_akun='$row'"; 
            pg_query($conn, $sql);
            // Update akun KEPERLUAN sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total]) WHERE uid='$row'");
            $sql= "UPDATE keu_akun_log SET saldo= (saldo - $a[total]) WHERE uid_akun='$row' AND created_at > '$a[tanggal]'";
            pg_query($conn, $sql);
            
            // DELETE keu_detail_pembayaran
            $sql = "DELETE FROM keu_buka_faktur_detail WHERE uid_akun_keperluan='$row' and tanggal ='$a[tanggal]'";
            pg_query($conn, $sql);
        }

        // Soft Delete keu_akun_payment_terima_kredit
        $sql="UPDATE keu_buka_faktur SET deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
		pg_query($conn,$sql);
        header("location: bukafaktur-$_GET[uid]");
    }
    
elseif($act=='input_detail'){    
    $jumlah = $_POST["jumlah"];
    $jumlah = str_replace(".", "", $jumlah);
    // Insert Keu_akun_payment
    $sql = "INSERT INTO keu_buka_faktur_detail(uid_akun_bank, uid_akun_keperluan, jumlah, keterangan) VALUES ('$_POST[uid_akun_bank]', '$_POST[uid_akun]', '$jumlah', '$_POST[keterangan]')";
    pg_query($conn, $sql);
    
    header("location: tambah-bukafaktur-$_POST[uid_akun_bank]");
}
    
elseif($act=='update_detail'){    
    $jumlah = $_POST["jumlah"];
    $jumlah = str_replace(".", "", $jumlah);
    // Insert Keu_akun_payment
    $sql = "UPDATE keu_buka_faktur_detail set uid_akun_keperluan='$_POST[uid_akun]', jumlah='$jumlah', keterangan='$_POST[keterangan]' WHERE uid='$_POST[uid]'";
    pg_query($conn, $sql);

    header("location: tambah-bukafaktur-$_POST[uid_akun_bank]");
}
    
elseif($act=='delete_detail'){    
    $a = pg_fetch_array(pg_query($conn, "SELECT uid_akun_bank FROM keu_buka_faktur_detail WHERE  uid='$_GET[uid]'"));
    $sql = "UPDATE keu_buka_faktur_detail set deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
    pg_query($conn, $sql);

    header("location: tambah-bukafaktur-$a[uid_akun_bank]");
}

    
elseif($act=='tambah_detail'){
    include "tambah_detail.php";
}
elseif($act=='edit_detail'){
    include "edit_detail.php";
}
    pg_close($conn);
}
                    
?>