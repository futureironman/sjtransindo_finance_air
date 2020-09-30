<?php

    require "../../konfig/koneksi2.php";
    $id_customer = $_GET["id_customer"];
   
    
    $query = $pdo->prepare("SELECT saldo_terkini FROM keu_akun WHERE uid_data=?");
    $query->execute(array($id_customer));
    $saldo=   $query->fetchAll(\PDO::FETCH_ASSOC);

    $query = $pdo->prepare("SELECT
    invoice_header.uid as uid_invoice_header,
    invoice_header.uid_data,
    invoice_header.invoice_number,
    invoice_header.total,
    invoice_header.jumlah_terbayar,
    invoice_header.sisa_bayar,
    invoice_header.jatuh_tempo,
    invoice_header.lock_date,
    
    po_house.po_house_number,
    po_house.uid_master_po,
    
    customer.nama as nama_customer,
    customer.email,
    customer.no_telepon,
    customer.alamat,
    
    pegawai.nama as nama_pegawai
    
    FROM invoice_header
    
    JOIN po_house
    ON invoice_header.uid_data = po_house.uid
    
    JOIN customer
    ON po_house.uid_customer = customer.uid
    
    JOIN pegawai
    ON po_house.uid_pegawai = pegawai.uid
    
    WHERE (invoice_header.sisa_bayar > 0) and (invoice_header.is_lunas IS NULL or  invoice_header.is_lunas ='') AND po_house.uid_customer = ? AND EXISTS(SELECT NULL FROM keu_akun_log a WHERE a.tabel='invoice_header' AND CAST(a.id_data AS uuid)=invoice_header.uid) and po_house.uid_category='2' ORDER BY invoice_header.lock_date ASC");
    $query->execute(array($id_customer));

   $a=   $query->fetchAll(\PDO::FETCH_ASSOC);
   if($saldo[0]["saldo_terkini"] < 0){
   $b["saldo"] = substr($saldo[0]["saldo_terkini"], 1, 100) ?? 0;
   }
   $b["data"] = $a;
   print json_encode($b)
?>