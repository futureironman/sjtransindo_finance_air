<?php

include "../../konfig/koneksi.php";
        // $uid_inv_header = $_POST["uid_inv_header"];
        // // $a = pg_fetch_array(pg_query($conn, "SELECT a.total, a.uid_inv_header, b.uid as uid_supplier, a.tanggal_pembelian FROM inv_detail_pembelian a, keu_akun b WHERE a.uid_inv_header='$_POST[uid_inv_header]' and a.uid_suplier=b.uid_data  and b.linked_table = 'master_supplier' AND NOT EXISTS (SELECT NULL FROM keu_akun_log c where c.uid_akun=b.uid and c.id_data = '$_POST[uid_inv_header]')"));
      
       
        // $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_data FROM inv_detail_pembelian"));
        $a = pg_fetch_array(pg_query($conn, "SELECT a.total, a.uid_inv_header, b.uid as uid_supplier, a.tanggal_pembelian FROM inv_detail_pembelian a, keu_akun b WHERE a.id='$_POST[id]' and a.uid_suplier=b.uid_data  and b.linked_table = 'master_supplier'"));

        
      if($a["uid_inv_header"] != ''){
        $uid_akun_terima = '9e3e2adc-c6d7-ff33-2dda-ecea464d48d8';
        $uid_inv_header = $a["uid_inv_header"];
        $uid_supplier = $a["uid_supplier"];
        $total = $a["total"];
        $waktu= $a["tanggal_pembelian"];

      //   $data = pg_query($coon, "SELECT b.stock_name, c.nama FROM invoice_header a, inv_detail_fee b, komoditi c, po_house d where a.uid='$uid_inv_header' and a.uid_data = CAST(b.uid_data AS UUID) and a.uid_data=d.uid 
      //   and d.uid_comodity= CAST(c.uid AS UUID)");

        //PENCATATAN DI JURNAl
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti,id_divisi) VALUES ('$waktu', '$uid_inv_header', 'Penjualan', '4', '$uid_inv_header','2')";
        pg_query($conn,$sql);	    
        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
        $id_jurnal= $data["id_jurnal"];


        //AKUN CUSTOMER SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_terima', '$total')";
        pg_query($conn,$sql);

        //AKUN PENJUALAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_supplier', '$total')";
        pg_query($conn,$sql);



        // ------------------HISTORY LOG CUSTOMER--------------
        //CEK LOG SALDO TERAKHIR customer
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_supplier' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN customer

        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_supplier', '$waktu', '$uid_inv_header', '25', '$uid_inv_header','0', '$total', '$saldo_kredit', 'invoice_header','$uid_akun_terima')";
        echo "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_supplier', '$waktu', '$uid_inv_header', '25', '$uid_inv_header','0', '$total', '$saldo_kredit', 'invoice_header','$uid_akun_terima')";
        $insert = pg_query($conn, $sql);
        // Update keuangan customer sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_supplier'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_supplier' AND created_at > '$waktu' ");
        // -------------------END HISTORY CUSTOMER ----------------

        // ---------------- HISTORY LOG HARGA PENJUALAN -----------------
        //CEK SALDO TERAKHIR AKUN HARGA PENJUALAN
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_terima' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_debet = $pembayaran_sebelum + $total;

        //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo,tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu',  '$uid_inv_header','25', '$uid_inv_header', '$total', '0', '$saldo_debet',  'invoice_header', '$uid_supplier')";
         pg_query($conn, $sql);

        // Update keuangan customer sesudah tanggal
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
    // echo "SELECT a.total, a.uid_inv_header, b.uid as uid_supplier, a.tanggal_pembelian FROM inv_detail_pembelian a, keu_akun b WHERE a.uid_inv_header='$_POST[uid_inv_header]' and a.uid_suplier=b.uid_data  and b.linked_table = 'master_supplier' AND NOT EXISTS (SELECT NULL FROM keu_akun_log c where c.uid_akun=b.uid and c.id_data = '$_POST[uid_inv_header]')";
    pg_close($conn);
    
?>