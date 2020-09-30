<?php

include "../../konfig/koneksi.php";
include "../../konfig/library.php";

// DELETE ALL
$a = pg_fetch_array(pg_query($conn, "SELECT a.total, a.created_at, b.uid_customer FROM invoice_header a, po_house b WHERE a.uid='$_GET[uid]' and a.uid_data=b.uid"));
$uid_akun_terima = 'ddf0a56b-3a77-e50e-e28b-2bf250900513';
        
        // update saldo terkini Customer
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total]) WHERE uid='$a[uid_customer]'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total]) WHERE uid='$uid_akun_terima'");
        
        // delete Jurnal
        $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$a[uid]'"));
        pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
        
       
        // Soft Delete keu_akun_log AKUN HARGA PENJUALAN dan CUSTOMER
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[created_at]' and (uid_akun_efek='$a[uid_customer]' OR uid_akun_efek='$uid_akun_terima')"; 
        pg_query($conn, $sql);
       
        // Update akun AKUN HARGA PENJUALAN sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $a[total]) WHERE uid_akun='$uid_akun_terima' AND created_at > '$a[created_at]'";
        pg_query($conn, $sql);
       
        // Update akun CUSTOMER  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $a[total]) WHERE uid_akun='$a[uid_customer]' AND created_at > '$a[created_at]'";
        pg_query($conn, $sql);

        // ---------------------------------------------------
        
// INPUT TRY
  
$a = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data = '$_POST[uid_customer]'"));
$uid_akun_terima = 'ddf0a56b-3a77-e50e-e28b-2bf250900513';
$invoice_number = $_POST["invoice_number"];
$uid_customer = $a["uid"];
$total = $_POST["total"];
        $data = pg_fetch_array(pg_query($conn, "SELECT uid,created_at FROM invoice_header where deleted_at is null ORDER BY uid ASC limit 1 "));
        $uid_invoice_header= $data["uid"];
        $waktu= $data["created_at"];
         $tampil = pg_query($conn, "SELECT stock_detail FROM invoice_detail WHERE uid_invoice_header ='$uid_invoice_header'"); 
         $keterangan_detail = '';       
         while($r=pg_fetch_array($tampil)){
            $keterangan_detail .= $r["stock_detail"] . ',';
         }

        //PENCATATAN DI JURNAl
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid_invoice_header', 'Penjualan', '4', '$invoice_number')";
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

        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_customer', '$waktu', '25', '$invoice_number','$total', '0', '$saldo_kredit', '$keterangan_detail', 'invoice_header','$uid_akun_terima')";
        pg_query($conn, $sql);

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
        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu', '25', '$invoice_number', '$total', '0', '$saldo_debet', '$keterangan_detail', 'invoice_header', '$uid_customer')";
        $insert = pg_query($conn, $sql);

        // Update keuangan customer sesudah tanggal
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid_akun_terima'");
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $total) WHERE uid_akun='$uid_akun_terima' AND created_at > '$waktu' ");
        //   -------------- END HISTORY KAS KECIL ---------------------------RY KAS KECIL ---------------------------
                 
         // create the product
    if($insert){
  
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