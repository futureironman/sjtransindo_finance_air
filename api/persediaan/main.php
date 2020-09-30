<?php

include "../../konfig/koneksi.php";
        $cek = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id FROM detail_barang_log where deleted_at is NULL"));
        $data = pg_fetch_array(pg_query($conn, "SELECT * FROM detail_barang_log where id='$cek[id]'"));
        
        // uid kotak 
        $barang = pg_fetch_array(pg_query($conn, "SELECT b.uid FROM detail_barang a, keu_akun b where a.id='$data[id_barang]' and a.uid=b.uid_data and b.uid_parent ='232df725-a344-472a-9e66-022c47611b21'"));
        $uid_akun_kotak = $barang["uid"];

        // uid kotak beban
        $barang = pg_fetch_array(pg_query($conn, "SELECT b.uid FROM detail_barang a, keu_akun b where a.id='$data[id_barang]' and a.uid=b.uid_data  and b.uid_parent='b1fa317d-d2b1-f990-73e9-814706b766f9'"));
        $uid_beban_kotak = $barang["uid"];


        $total = $data["total"];
        $waktu = substr($data["created_at"], 0,16);
        $keterangan= $data["keterangan"];
        // $uid_akun_terima = '0709a09f-6317-6fbb-6541-670a30dc5657';
        $masuk= $data["masuk"];
        $keluar= $data["keluar"];
        $hasil=$masuk-$keluar;
        $tipe=$data["tipe"];
        echo $hasil;
      if($uid_akun_kotak != '' || $uid_beban_kotak != ''){

        if ($hasil > 0){
            
        $akun = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data='$data[uid_supplier]'"));
        $uid_akun= $akun["uid"];
        //PENCATATAN DI JURNAl
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis,id_divisi) VALUES ('$waktu', '$uid_akun', 'persediaan', '4','2')";
        pg_query($conn,$sql);	    
        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
        $id_jurnal= $data["id_jurnal"];
        
        //AKUN CUSTOMER SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$total')";
        pg_query($conn,$sql);

        //AKUN PENJUALAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun', '$total')";
        pg_query($conn,$sql);

        // ------------------HISTORY LOG supplier--------------
        //CEK LOG SALDO TERAKHIR supplier
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN supplier

        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '$uid_akun', '4', '$keterangan', '0','$total', '$saldo_kredit', 'detail_barang_log','$uid_akun_kotak')";
        $insert = pg_query($conn, $sql);
        // Update keuangan supplier sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");


        
        // ------------------HISTORY LOG KOTAK--------------
        //CEK LOG SALDO TERAKHIR KOTAK
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_kotak' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN KOTAK
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_kotak', '$waktu', '$uid_akun', '4', '$keterangan','$total','0','$saldo_kredit', 'detail_barang_log','$uid_akun')";
        $insert = pg_query($conn, $sql);
        echo $sql;
        // Update keuangan KOTAK sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_kotak'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_akun_kotak' AND created_at > '$waktu' ");
        
        // -------------------END HISTORY KOTAK ----------------


        // ---------------- HISTORY LOG HARGA PENJUALAN -----------------
        //CEK SALDO TERAKHIR AKUN HARGA PENJUALAN
        // $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_terima' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        // $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        // $saldo_debet = $pembayaran_sebelum + $total;

        // //PENCATATAN DI LOG KEU AKUN PENJUALAN
        // $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu',  '$uid_akun','4', '$keterangan', '$total', '0', '$saldo_debet',  'invoice_header', '$uid_akun')";
        //  pg_query($conn, $sql);

        // // Update keuangan KOTAK sesudah tanggal
        // pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_terima'");
        // pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $total) WHERE uid_akun='$uid_akun_terima' AND created_at > '$waktu' ");
        //   -------------- END HISTORY KAS KECIL ---------------------------
   
        if($tipe == 'penyesuaian'){
            
        // ------------------HISTORY LOG KOTAK--------------
            //CEK LOG SALDO TERAKHIR KOTAK
            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_beban_kotak' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
            $saldo_kredit = $pembayaran_sebelum - $total;
            
            //PENCATATAN DI LOG KEU AKUN KOTAK
            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_beban_kotak', '$waktu', '$uid_beban_kotak', '7', '$keterangan', '0','$total', '$saldo_kredit', 'keu_akun','$uid_beban_kotak')";
            $insert = pg_query($conn, $sql);

            // Update keuangan KOTAK sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_beban_kotak'");
            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo-$total) WHERE uid_akun='$uid_beban_kotak' AND created_at > '$waktu' ");
        }
    } else{
        

        // ------------------HISTORY LOG KOTAK--------------
        //CEK LOG SALDO TERAKHIR KOTAK
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_kotak' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum - $total;
        
        //PENCATATAN DI LOG KEU AKUN KOTAK
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_kotak', '$waktu', '$uid_beban_kotak', '6', '$keterangan', '0','$total', '$saldo_kredit', 'keu_akun','$uid_beban_kotak')";
        $insert = pg_query($conn, $sql);

        // Update keuangan KOTAK sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_akun_kotak'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo-$total) WHERE uid_akun='$uid_akun_kotak' AND created_at > '$waktu' ");

        
        // ------------------HISTORY LOG BEBAN KOTAK--------------
        //CEK LOG SALDO TERAKHIR BEBAN KOTAK
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_beban_kotak' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_debet = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN BEBAN KOTAK
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_beban_kotak', '$waktu', '$uid_akun_kotak', '6', '$keterangan', '$total','0', '$saldo_debet', 'keu_akun','$uid_akun_kotak')";
        $insert = pg_query($conn, $sql);

        // Update keuangan BEBAN KOTAK sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_beban_kotak'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_beban_kotak' AND created_at > '$waktu' ");

    
    }
   
        // set response code - 201 created
        http_response_code(200);
  
        // tell the user
        echo json_encode(array("message" => "Product was created."));
    }
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(404);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create product."));
    }
echo "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_kotak' and uid_parent ='232df725-a344-472a-9e66-022c47611b21'";
    pg_close($conn);
?>