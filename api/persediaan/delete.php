<?php

include "../../konfig/koneksi.php";
include "../../konfig/library.php";

// DELETE ALL
$a = pg_fetch_array(pg_query($conn, "SELECT * FROM detail_barang_log where id='$_POST[id]'"));
$barang = pg_fetch_array(pg_query($conn, "SELECT b.uid FROM detail_barang a, keu_akun b where a.id='$a[id_barang]' and a.uid=b.uid_a"));
$uid_akun_kotak= $barang["uid"];
$total = $a["masuk"] * $a["harga"];
$waktu = substr($a["created_at"], 0,16);
$keterangan= $a["keterangan"];
$uid_akun_terima = '0709a09f-6317-6fbb-6541-670a30dc5657';


        // update saldo terkini Customer
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_akun_kotak'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_akun_terima'");
        
        // delete Jurnal
        $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$uid_akun_kotak'"));
        pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
        
       
        // Soft Delete keu_akun_log AKUN HARGA PENJUALAN dan CUSTOMER
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$waktu' and (uid_akun_efek='$auid_akun_kotak' OR uid_akun_efek='$uid_akun_terima')"; 
        pg_query($conn, $sql);
       
        // Update akun AKUN HARGA PENJUALAN sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $total) WHERE uid_akun='$uid_akun_terima' AND created_at > '$waktu'";
        pg_query($conn, $sql);
       
        // Update akun CUSTOMER  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $total) WHERE uid_akun='$uid_akun_kotak' AND created_at > '$waktu'";
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