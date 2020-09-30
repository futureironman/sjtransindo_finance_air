<?php

include "../../konfig/koneksi.php";
        $invoice_number = $_POST["invoice_number"];
        $a = pg_fetch_array(pg_query($conn, "SELECT a.total, a.invoice_number, a.uid as uid_invoice_header, c.uid as uid_customer, a.lock_date FROM invoice_header a, customer b, keu_akun c, po_master d WHERE a.invoice_number='$invoice_number' and a.uid_data=d.uid and CAST(b.uid AS UUID)=c.uid_data and c.linked_table = 'customer' AND CAST(b.uid AS UUID)=d.customer_import and NOT EXISTS (SELECT NULL FROM keu_akun_log e where e.uid_akun=c.uid and e.id_data = '$invoice_number')"));
        
      if($a["invoice_number"] != ''){
        $uid_invoice_header= $a["uid_invoice_header"];
        $uid_akun_terima = '0709a09f-6317-6fbb-6541-670a30dc5657';
        $invoice_number = $a["invoice_number"];
        $uid_customer = $a["uid_customer"];
        $total = $a["total"];
        $waktu= $a["lock_date"];

      //   $data = pg_query($coon, "SELECT b.stock_name, c.nama FROM invoice_header a, inv_detail_fee b, komoditi c, po_house d where a.uid='$uid_invoice_header' and a.uid_data = CAST(b.uid_data AS UUID) and a.uid_data=d.uid 
      //   and d.uid_comodity= CAST(c.uid AS UUID)");

         $tampil = pg_query($conn, "SELECT stock_detail FROM invoice_detail WHERE uid_invoice_header ='$uid_invoice_header'"); 
         $keterangan_detail = '';       
         while($r=pg_fetch_array($tampil)){
            $keterangan_detail .= $r["stock_detail"] . ',';
         }

        //PENCATATAN DI JURNAl
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti,id_divisi) VALUES ('$waktu', '$uid_invoice_header', 'Penjualan', '4', '','2')";
        pg_query($conn,$sql);	    
        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
        $id_jurnal= $data["id_jurnal"];


        //AKUN CUSTOMER SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_terima', '$total')";
        pg_query($conn,$sql);

        //AKUN PENJUALAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_customer', '$total')";
        pg_query($conn,$sql);



        // ------------------HISTORY LOG CUSTOMER--------------
        //CEK LOG SALDO TERAKHIR customer
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_customer' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_kredit = $pembayaran_sebelum + $total;
        
        //PENCATATAN DI LOG KEU AKUN customer
        
        $keterangan_detail = substr($keterangan_detail, 0, -1);

        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_customer', '$waktu', '$uid_invoice_header', '25', '$invoice_number','$total', '0', '$saldo_kredit', '$keterangan_detail', 'invoice_header','$uid_akun_terima')";
        $insert = pg_query($conn, $sql);
        // Update keuangan customer sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_customer'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid_customer' AND created_at > '$waktu' ");
        // -------------------END HISTORY CUSTOMER ----------------

        // ---------------- HISTORY LOG HARGA PENJUALAN -----------------
        //CEK SALDO TERAKHIR AKUN HARGA PENJUALAN
        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_terima' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
        $saldo_debet = $pembayaran_sebelum + $total;

        //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_data, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu',  '$uid_invoice_header','25', '$invoice_number', '0', '$total', '$saldo_debet', '$keterangan_detail', 'invoice_header', '$uid_customer')";
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

    pg_close($conn);
?>