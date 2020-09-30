<?php

    require "../../konfig/koneksi2.php";
    $uid_suplier = $_GET["uid_suplier"];

    $query = $pdo->prepare("SELECT saldo_terkini FROM keu_akun WHERE uid_data=?");
    $query->execute(array($uid_suplier));
    $saldo=   $query->fetchAll(\PDO::FETCH_ASSOC);

    $query = $pdo->prepare("SELECT a.uid, a.invoice_number, b.* FROM inv_header_pembelian a, inv_detail_pembelian b 
    WHERE CAST(a.uid AS uuid)=b.uid_inv_header and (b.is_lunas IS NULL or  b.is_lunas ='') AND b.sisa_bayar > 0 AND a.id_divisi='2' AND b.uid_suplier= ?  ORDER BY b.tanggal_pembelian ASC");
    $query->execute(array($uid_suplier));
   $a=   $query->fetchAll(\PDO::FETCH_ASSOC);
   if($saldo[0]["saldo_terkini"] < 0){
   $b["saldo"] = substr($saldo[0]["saldo_terkini"], 1 ,100) ?? 0;
   }
   $b["data"] = $a;

//    for($i=0; $i < count($a); $i++){
//        if($saldo[0]["uid_data"] ?? '' == $a[$i]["uid_suplier"]){
//            $a[$i]["saldo"] = $saldo[0]["saldo"];
//        }
//        else{
           
//         $a[$i]["saldo"] = 0;
//        }
//    }
   print json_encode($b);
?>