<?php

include "../../konfig/koneksi.php";
include "../../konfig/library.php";

// DELETE ALL
$a = pg_fetch_array(pg_query($conn, "SELECT a.total, a.created_at, b.uid_customer FROM invoice_header a, po_house b WHERE a.uid='$_GET[uid]' and a.uid_data=b.uid"));
$uid_akun_terima = '0709a09f-6317-6fbb-6541-670a30dc5657';
        
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