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

        $nama = $_POST["nama"];
        $uid_akun_bank = $_POST["uid_akun_bank"];
        $total_pembayaran = $_POST["total_pembayaran"];                

        $d = pg_fetch_array(pg_query($conn, "SELECT MAX(no_faktur) as nomor FROM keu_buka_faktur where id_divisi='$_SESSION[divisi]' and uid_akun='$uid_akun_bank' and deleted_at is NULL "));
        $kode_before = substr($d['nomor'],0,11);
        $kode_now="U-$_POST[kode_akun].$thn.";
        if($kode_before==$kode_now){
            $no_urut = (int) substr($d['nomor'],11,6);
            $no_urut++;
            $no_faktur = $kode_before.sprintf("%06s",$no_urut);
        }
        else{
            $no_faktur = $kode_now.sprintf("%06s",1);
        }
                    
                        $cek = pg_query($conn, "SELECT a.*, b.nama FROM keu_buka_faktur_detail a, keu_akun b WHERE a.no_faktur is null and a.deleted_at is null and a.uid_akun_bank='$uid_akun_bank' and a.uid_akun_keperluan=b.uid ");
                        $keterangan_detail ='';
                        $nama_akun = '';
                        while($r=pg_fetch_array($cek)){
                            $uid_akun = $r["uid_akun_keperluan"];
                            $keterangan = $r["keterangan"];
                            $jumlah = $r["jumlah"];
                            $keterangan_detail .= $r["uid_akun_keperluan"] . ',';
                            $nama_akun .= $r["nama"] . ',';
                            
                          //CEK SALDO TERAKHIR akun kredit lebih kecil dari tanggal
                        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                        $saldo = $pembayaran_sebelum + $jumlah;
                        $saldo_kredit = $pembayaran_sebelum - $jumlah;
              
                        //CEK JENIS AKUN KEUANGAN
                        $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                        $jenis_akun = $a['jenis_akun'];
                        
                    
                        if($jenis_akun == 'D'){
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel, id_data, uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah', '0', '$saldo', '$no_faktur', '$keterangan', 'keu_buka_faktur_detail','$uid_buka_faktur_detail','$r[uid]','$uid_akun_bank')";
                            pg_query($conn, $sql);

                        // Update akun efek pembayaran  sesudah tanggal
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");

                         
                        }
                        else{
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan,'keterangan_detail', tabel,id_data,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25','$jumlah','0','$saldo_kredit', '$no_faktur','$keterangan', 'keu_buka_faktur_detail','$r[uid]','$uid_akun_bank')";
                            pg_query($conn, $sql);
                            
                            // Update akun efek pembayaran  sesudah tanggal
                            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_akun'");
                            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
                    }

                     //PENCATATAN DI JURNAl
                     $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti,id_divisi) VALUES ('$waktu', '$uid_akun', 'Buka Faktur', '1', '$no_faktur','2')";
                     pg_query($conn,$sql);	    
                     $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                     $id_jurnal= $data["id_jurnal"];
                     //AKUN CUSTOMER SEBAGAI DEBET
                     $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                     pg_query($conn,$sql);
                }
                
                    //CEK SALDO TERAKHIR akun KAS KECIL (BCA) lebih kecil dari tanggal
                    
                    $keterangan_detail = substr($keterangan_detail, 0, -1);
                    $nama_akun = substr($nama_akun, 0, -1);
                    $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun_bank' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                    $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                    $saldo = $pembayaran_sebelum - $total_pembayaran;

                    // insert KEU BUKA FAKTUR
                    pg_query($conn, "INSERT INTO keu_buka_faktur (tanggal, no_faktur, total, uid_akun, keterangan_detail, created_at,nama, id_divisi) VALUES ('$waktu', '$no_faktur', '$total_pembayaran', '$uid_akun_bank', '$keterangan_detail', '$waktu_sekarang','$nama', '$_SESSION[divisi]')");
                    
                    // UPDATE KEU BUKA FAKTUR DETAIL 
                    $sql= "UPDATE keu_buka_faktur SET no_faktur= '$no_faktur', tanggal='$waktu' WHERE no_faktur is null and deleted_at is null and uid_akun_bank='$uid_akun_bank'";
                    pg_query($conn,$sql);	

                    
                    $data = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_buka_faktur ORDER BY created_at DESC limit 1 "));
                    $uid_buka_faktur= $data["uid"];
        
                    //PENCATATAN DI LOG KEU AKUN EFEK KAS KECIL (BCA)
                     $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel,id_data) VALUES ('$uid_akun_bank', '$waktu', '25','0','$total_pembayaran','$saldo', '$no_faktur', '$nama_akun', 'keu_buka_faktur','$uid_buka_faktur')";
                     pg_query($conn, $sql);
                     
                     // Update akun efek KAS KECIL (BCA)  sesudah tanggal
                     pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_pembayaran) WHERE uid='$uid_akun_bank'");
                     pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total_pembayaran) WHERE uid_akun='$uid_akun_bank' AND created_at > '$waktu' ");
                       
                     
                    // update table keu_buka_daktur
                    $sql = "UPDATE keu_buka_faktur_detail set no_faktur='$no_faktur', tanggal='$waktu' WHERE uid_akun_bank='$uid_akun_bank' and no_faktur is null and deleted_at is null";
                    pg_query($conn, $sql);
            
            // insert pegawai_activity_log
            $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_buka_faktur where no_faktur='$no_faktur' and deleted_at is NULL "));
            pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert buka faktur','$d[uid]','$waktu','$_SESSION[login_user]','keu_buka_faktur')");
            header("location: bukafaktur-$uid_akun_bank");
         }
               
	elseif($act=='edit'){
            //SIMPAN DANA DI PENERIMAAN DANA : DEBET UNTUK PENERIMAAN KAS
            $cek = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_buka_faktur WHERE uid='$_POST[uid]'"));
            $waktu = $cek["tanggal"];
            $no_faktur = $_POST["no_faktur"];
            $uid_akun_bank = $_POST["uid_akun_bank"];
            $total_pembayaran = $_POST["total_pembayaran"];
                        
                            $cek1 = pg_query($conn, "SELECT a.*, b.nama FROM keu_buka_faktur_detail a, keu_akun b WHERE a.uid_akun_keperluan=b.uid and (a.no_faktur is null OR a.no_faktur='$no_faktur') and a.deleted_at is null and a.uid_akun_bank='$uid_akun_bank'");
                            $nama_akun ='';
                            $keterangan_detail ='';
                            while($r=pg_fetch_array($cek1)){
                                $nama_akun .= $r["nama"] . ',';
                                $keterangan_detail .= $r["uid_akun_keperluan"] . ',';
                            }
       
                            $cek2 = pg_query($conn, "SELECT * FROM keu_buka_faktur_detail WHERE no_faktur is null and deleted_at is null and uid_akun_bank='$uid_akun_bank'");
                            $total_jumlah=0;
                            while($r=pg_fetch_array($cek2)){
                                $uid_akun = $r["uid_akun_keperluan"];
                                $keterangan = $r["keterangan"];
                                $jumlah = $r["jumlah"];
                                $total_jumlah += $r["jumlah"];
                                
                              //CEK SALDO TERAKHIR akun kredit lebih kecil dari tanggal
                            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                            $saldo = $pembayaran_sebelum + $jumlah;
                            $saldo_kredit = $pembayaran_sebelum - $jumlah;
                  
                            //CEK JENIS AKUN KEUANGAN
                            $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                            $jenis_akun = $a['jenis_akun'];
                            
                             if($jenis_akun == 'D'){
                                //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel,id_data,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah', '0', '$saldo', '$no_faktur', '$keterangan', 'keu_buka_faktur_detail','$r[uid]', '$uid_akun_bank')";
                                pg_query($conn, $sql);
    
                            // Update akun efek pembayaran  sesudah tanggal
                            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
    
                             
                            }
                            else{
                                //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan,'keterangan_detail', tabel,id_data,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25','$jumlah','0','$saldo_kredit', '$no_faktur','$keterangan', 'keu_buka_faktur_detail','$r[uid]','$uid_akun_bank')";
                                pg_query($conn, $sql);
                                
                                // Update akun efek pembayaran  sesudah tanggal
                                pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_akun'");
                                pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
                        }
    
                         //PENCATATAN DI JURNAl
                         $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti,id_divisi) VALUES ('$waktu', '$uid_akun', 'Buka Faktur', '1', '$no_faktur','2')";
                         pg_query($conn,$sql);	    
                         $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                         $id_jurnal= $data["id_jurnal"];
                         //AKUN CUSTOMER SEBAGAI DEBET
                         $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                         pg_query($conn,$sql);
                    }
                    
                        //CEK SALDO TERAKHIR akun KAS KECIL (BCA) lebih kecil dari tanggal
                        
                        $keterangan_detail = substr($keterangan_detail, 0, -1);
                        $nama_akun = substr($nama_akun, 0, -1);
    
                         //PENCATATAN DI LOG KEU AKUN EFEK KAS KECIL (BCA)
                         pg_query($conn, "UPDATE keu_akun_log SET kredit= (kredit + $total_jumlah), saldo=(saldo + $total_jumlah), keterangan_detail='$nama_akun' WHERE created_at = '$waktu' and  uid_akun='$uid_akun_bank' AND deleted_at is NULL");
                         
                         // Update akun efek KAS KECIL (BCA)  sesudah tanggal
                         pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_jumlah) WHERE uid='$uid_akun_bank'");
                         pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total_jumlah) WHERE uid_akun='$uid_akun_bank' AND created_at > '$waktu' ");
                           
                         // UPDATE KEU BUKA FAKTUR DETAIL 
                         $sql= "UPDATE keu_buka_faktur SET no_faktur= '$no_faktur', tanggal='$waktu' WHERE no_faktur is null and deleted_at is null and uid_akun_bank='$uid_akun_bank'";
                         pg_query($conn,$sql);	

                        // update table keu_buka_daktur
                        $sql = "UPDATE keu_buka_faktur_detail set no_faktur='$no_faktur', tanggal='$waktu' WHERE uid_akun_bank='$uid_akun_bank' and no_faktur is null and deleted_at is null";
                        pg_query($conn, $sql);

                         
                         // update KEU BUKA FAKTUR
                         pg_query($conn, "UPDATE keu_buka_faktur SET total='$total_pembayaran', keterangan_detail='$keterangan_detail' WHERE uid='$_POST[uid]'");

                         
            // insert pegawai_activity_log
            pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('edit buka faktur','$_POST[uid]','$waktu','$_SESSION[login_user]','keu_buka_faktur')");
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
        
        
        // insert pegawai_activity_log
        pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('delete buka faktur','$_GET[uid]','$waktu','$_SESSION[login_user]','keu_buka_faktur')");

        header("location: bukafaktur-$a[uid_akun]");
    }
    
    // input detail
elseif($act=='input_detail'){    
    $jumlah = $_POST["jumlah"];
    $jumlah = str_replace(".", "", $jumlah);
    // Insert Keu_akun_payment
    $sql = "INSERT INTO keu_buka_faktur_detail(uid_akun_bank, uid_akun_keperluan, jumlah, keterangan,created_at,id_divisi) VALUES ('$_POST[uid_akun_bank]', '$_POST[uid_akun]', '$jumlah', '$_POST[keterangan]','$waktu_sekarang','$_SESSION[divisi]')";
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
    $r = pg_fetch_array(pg_query($conn, "SELECT uid_akun_bank FROM keu_buka_faktur_detail WHERE  uid='$_GET[uid]'"));  
    if($r["no_faktur"] == null){
        $sql = "UPDATE keu_buka_faktur_detail set deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
        pg_query($conn, $sql);
    }else{
        
        $sql = "UPDATE keu_buka_faktur_detail set deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
        pg_query($conn, $sql);
        
        $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$r[uid_akun_keperluan]'"));
        $jenis_akun = $a['jenis_akun'];

        //delete transaksi di akun_log
        pg_query($conn, "UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE created_at = '$r[tanggal]' and  uid_akun='$r[uid_akun_keperluan]' and keterangan='$r[no_faktur]'");

        // delete keu_jurnal_detail
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE created_at = '$r[tanggal]' and  uid_data='$r[uid_akun_keperluan]' and no_bukti='$r[no_faktur]'");

        if($jenis_akun == 'D'){
            pg_query($conn, "UPDATE keu_akun_log SET saldo=(saldo - $r[jumlah]) WHERE created_at = '$r[tanggal]' and  uid_akun='$r[uid_akun_keperluan]' AND deleted_at is NULL and keterangan='$r[no_faktur]'");
        }
        else{
            pg_query($conn, "UPDATE keu_akun_log SET saldo=(saldo + $r[jumlah]) WHERE created_at = '$r[tanggal]' and  uid_akun='$r[uid_akun_keperluan]' AND deleted_at is NULL and keterangan='$r[no_faktur]'");

        }
                         
    }

    header("location: tambah-bukafaktur-$a[uid_akun_bank]");
}
    
    // edit input detail
elseif($act=='edit_input_detail'){    
    $jumlah = $_POST["jumlah"];
    $jumlah = str_replace(".", "", $jumlah);
    // Insert Keu_akun_payment
    $sql = "INSERT INTO keu_buka_faktur_detail(uid_akun_bank, uid_akun_keperluan, jumlah, keterangan) VALUES ('$_POST[uid_akun_bank]', '$_POST[uid_akun]', '$jumlah', '$_POST[keterangan]')";
    pg_query($conn, $sql);
    
    header("location: edit-bukafaktur-$_POST[uid]");
}
    
elseif($act=='edit_update_detail'){    
    $jumlah = $_POST["jumlah"];
    $jumlah = str_replace(".", "", $jumlah);
    // Insert Keu_akun_payment
    $sql = "UPDATE keu_buka_faktur_detail set uid_akun_keperluan='$_POST[uid_akun]', jumlah='$jumlah', keterangan='$_POST[keterangan]' WHERE uid='$_POST[uid_detail]'"; 
    pg_query($conn, $sql);

    header("location: edit-bukafaktur-$_POST[uid]");
}
    
elseif($act=='edit_delete_detail'){    
    $sql = "UPDATE keu_buka_faktur_detail set deleted_at='$waktu_sekarang' WHERE uid='$_POST[id]'";
    pg_query($conn, $sql);

    echo 1;
}

    
elseif($act=='tambah_detail'){
    include "tambah_detail.php";
}
elseif($act=='edit_detail'){
    include "edit_detail.php";
}
    
elseif($act=='edit_tambah_detail'){
    include "edit_tambah_detail.php";
}
elseif($act=='edit_edit_detail'){
    include "edit_edit_detail.php";
}
    pg_close($conn);
}
                    
?>