<?php
include "konfig/koneksi.php";
include "konfig/library.php";

$tampil=pg_query($conn,"SELECT a.uid, a.nama FROM customer a WHERE a.id_divisi='2' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE CAST(a.uid AS uuid)=b.uid_data AND b.linked_table='customer' AND b.deleted_at IS NULL)");

$uid_akun_parent="90dc67fb-71a3-bf14-958e-3feb14f7e450";
while($r=pg_fetch_array($tampil)){
    $d=pg_fetch_array(pg_query($conn,"SELECT MAX(nomor) AS nomor FROM keu_akun WHERE uid_parent='$uid_akun_parent' AND deleted_at IS NULL"));

    $no_urut = (int) substr($d['nomor'],5,3);
    $no_urut++;
    $no_urut_baru = "1103.".sprintf("%03s",$no_urut);

    $nama = "PIUTANG USAHA ".$r['nama'];

    $sql="INSERT INTO keu_akun (nomor, created_at, nama, id_divisi, uid_parent, jenis_akun, linked_table, uid_data) VALUES ('$no_urut_baru', '$waktu_sekarang', '$nama', '2', '$uid_akun_parent', 'D', 'customer', '$r[uid]') RETURNING uid";

    echo "$sql<br><br>";
    pg_query($conn,$sql);
}
?>