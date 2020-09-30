<?php

    include "../../konfig/koneksi2.php";
    $tampil=pg_query($conn,"SELECTSELECT
    invoice_header.uid,
    invoice_header.uid_data,
    invoice_header.invoice_number,
    invoice_header.total,
    invoice_header.jumlah_terbayar,
    invoice_header.sisa_bayar,

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

WHERE (invoice_header.sisa_bayar > 0) AND po_house.uid_customer = '$id_customer'");
    return $r=pg_fetch_array($tampil);
?>