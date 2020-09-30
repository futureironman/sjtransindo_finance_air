<?php

    require "../../konfig/koneksi2.php";
    $uid_suplier = $_GET["uid_suplier"];
    
    $query = $pdo->prepare("SELECT saldo_terkini FROM keu_akun WHERE uid_data=?");
    $query->execute(array($uid_suplier));
    $saldo=   $query->fetchAll(\PDO::FETCH_ASSOC);

    $query = $pdo->prepare("SELECT a.* ,  b.uid, b.nama_barang, c.nama FROM detail_barang_log a, detail_barang b , pegawai c
    WHERE b.uid= ? and b.id=a.id_barang and a.uid_customer=CAST(c.uid AS uuid)  AND a.sisa_bayar > 0 and is_lunas is null ORDER BY a.created_at ASC");
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