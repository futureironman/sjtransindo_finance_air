<?php

include "../../konfig/koneksi.php";
        $cek = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id FROM detail_barang_log where deleted_at is NULL"));
        $data = pg_fetch_array(pg_query($conn, "SELECT * FROM detail_barang_log where id='$cek[id]'"));
        $barang = pg_fetch_array(pg_query($conn, "SELECT b.uid FROM detail_barang a, keu_akun b where a.id='$data[id_barang]' and a.uid=b.uid_data"));
        $uid_akun_kotak= $barang["uid"];
        $total = $data["keluar"] * $data["harga"];
        $waktu = substr($data["created_at"], 0,16);
        $keterangan= $data["keterangan"];
        $uid_akun_terima = '0709a09f-6317-6fbb-6541-670a30dc5657';

        
      if($uid_akun_kotak != ''){

      //   $data = pg_query($coon, "SELECT b.stock_name, c.nama FROM invoice_header a, inv_detail_fee b, komoditi c, po_house d where a.uid='$uid_inv_header' and a.uid_data = CAST(b.uid_data AS UUID) and a.uid_data=d.uid 
      //   and d.uid_comodity= CAST(c.uid AS UUID)");

        //PENCATATAN DI JURNAl
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis) VALUES ('$waktu', '$uid_akun_kotak', 'persediaan', '4')";
        pg_query($conn,$sql);	    
        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
        $id_jurnal= $data["id_jurnal"];


        //AKUN CUSTOMER SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_terima', '$total')";
        pg_query($conn,$sql);

        //AKUN PENJUALAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_kotak', '$total')";
        pg_query($conn,$sql);



        // ------------------HISTORY LOG KOTAK--------------
        //CEK LOG SALDO TERAKHIR KOTAK
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_kotak' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN KOTAK

        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_kotak', '$waktu', '$uid_akun_kotak', '25', '$keterangan', '0','$total', '$saldo_kredit', 'detail_barang_log','$uid_akun_terima')";
        $insert = pg_query($conn, $sql);
        // Update keuangan KOTAK sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_kotak'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_akun_kotak' AND created_at > '$waktu' ");
        // -------------------END HISTORY KOTAK ----------------

        // ---------------- HISTORY LOG HARGA PENJUALAN -----------------
        //CEK SALDO TERAKHIR AKUN HARGA PENJUALAN
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_terima' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_debet = $pembayaran_sebelum + $total;

        //PENCATATAN DI LOG KEU AKUN PENJUALAN
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu',  '$uid_akun_kotak','25', '$keterangan', '$total', '0', '$saldo_debet',  'invoice_header', '$uid_akun_kotak')";
         pg_query($conn, $sql);

        // Update keuangan KOTAK sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_terima'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $total) WHERE uid_akun='$uid_akun_terima' AND created_at > '$waktu' ");
        //   -------------- END HISTORY KAS KECIL ---------------------------
        
   
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

    pg_close($conn);
?>