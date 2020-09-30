<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])) {
    header('location:keluar');
} else {
    include "../../konfig/koneksi.php";
    include "../../konfig/library.php";

    $act = $_GET['act'];
    if ($act == 'verify') { ?>
         <form action="aksi-verify-persediaan" method="POST" enctype="multipart/form-data">
            <div class="modal-dialog modal-md a-lightSpeed">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="modal-standard-title">Verify</h5>
                  </div>
                  <div class="modal-body" id="form-data">
                  <div class="form-group focused">
                     <input type="hidden" value="<?= $_POST["id"] ?>" name="id">
                     
                     <div class="form-group focused">
                           <label class="form-control-label">Jenis Akun</label>
                           <select name="uid_akun" class="form-control modal_select2">
                           <?php
                           $tampil=pg_query($conn,"SELECT * FROM keu_akun WHERE uid_parent ='2e57b1b3-875c-fa51-5b39-1945eca33202' OR uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0'");
                           while($r=pg_fetch_array($tampil)){
                              echo"<option value='$r[uid]'>$r[nama]</option>";
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group focused">
                        <label class="form-control-label">Keterangan</label>
                           <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-success btn-md"><i class="fa fa-save"></i> Simpan</button>
                     <button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="fa fa-ban"></i> Batal</button>
                  </div>
               </div>
            </div>
         </form>
         <script type="text/javascript" src="addons/js/masking_form.js"></script>
         <script type="text/javascript">
         $('.modal_select2').select2({
               dropdownParent: $('#form-modal')
            });
         </script>
      <?php
   }
    elseif ($act == 'edit') { 
      $a = pg_fetch_array(pg_query($conn, "SELECT * FROM pemesanan_barang WHERE id='$_POST[id]'"));
       ?>
         <form action="aksi-edit-persediaan" method="POST" enctype="multipart/form-data">
            <div class="modal-dialog modal-md a-lightSpeed">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="modal-standard-title">Verify</h5>
                  </div>
                  <div class="modal-body" id="form-data">
                  <div class="form-group focused">
                     <input type="hidden" value="<?= $_POST["id"] ?>" name="id">
                     
                     <div class="form-group focused">
                           <label class="form-control-label">Jenis Akun</label>
                           <select name="uid_akun" class="form-control modal_select2">
                           <?php
                           $tampil=pg_query($conn,"SELECT * FROM keu_akun WHERE uid_parent ='2e57b1b3-875c-fa51-5b39-1945eca33202' OR uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0'");
                           while($r=pg_fetch_array($tampil)){
                              if($r["uid"] == $a["uid_akun_bayar"]){
                                 echo"<option value='$r[uid]' selected>$r[nama]</option>";
                              } else{
                                 echo"<option value='$r[uid]'>$r[nama]</option>";
                              }
                                 
                           }
                           ?>
                        </select>
                     </div>
                     <div class="form-group focused">
                        <label class="form-control-label">Keterangan</label>
                           <textarea name="keterangan" class="form-control"> <?= $a["keterangan_detail"] ?></textarea>
                        </div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-success btn-md"><i class="fa fa-save"></i> Simpan</button>
                     <button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="fa fa-ban"></i> Batal</button>
                  </div>
               </div>
            </div>
         </form>
         <script type="text/javascript" src="addons/js/masking_form.js"></script>
         <script type="text/javascript">
         $('.modal_select2').select2({
               dropdownParent: $('#form-modal')
            });
         </script>
      <?php
   }
                        
	elseif($act=='aksiverify'){
      $tgl_awal=$thn_sekarang.'-'.$bln_sekarang.'-01 00:00:00';
      $tanggal_akhir=$thn_sekarang.'-'.$bln_sekarang.'-31 23:59:59';
      $data = pg_fetch_array(pg_query($conn, "SELECT no_bukti as noBukti FROM pemesanan_barang  ORDER BY id DESC limit 1 "));
      $noBukti= $data["nobukti"];
      $noBukti = explode('.',$noBukti,2);
      if ($noBukti[1] == NULL) {
          $noBukti = substr($thn_sekarang, 0, 2)  . $bln_sekarang. '0'. 0 + 1;
      } else {
          $noBukti = $noBukti[1] + 1 ;
      }
      $no_bukti = "PB.". $noBukti;

         $tz = 'Asia/Jakarta';
         $dt = new DateTime("now", new DateTimeZone($tz));
         $waktu = $dt->format('Y-m-d G:i');
          $a = pg_fetch_array(pg_query($conn, "SELECT * FROM pemesanan_barang WHERE id='$_POST[id]'"));
          $b = pg_fetch_array(pg_query($conn, "SELECT uid FROM detail_barang WHERE id = '$a[id_barang]'"));

        $total = $a["total_harga"];
        $uid_akun=$_POST["uid_akun"];
        $akun = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data ='$b[uid]'"));
        $uid = $akun["uid"];

            // UPDATE TABLE pemesanan_barang
            pg_query($conn, "UPDATE pemesanan_barang SET verify='$waktu', no_bukti='$no_bukti', keterangan_detail='$_POST[keterangan]', uid_akun_bayar='$uid_akun' WHERE id='$_POST[id]'");

            // ------------------HISTORY LOG PERSEDIAAN BARANG--------------
            //CEK LOG SALDO TERAKHIR PERSEDIAAN BARANG
            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
            $saldo_debet = $pembayaran_sebelum + $total;
            //PENCATATAN DI LOG KEU AKUN PERSEDIAAN BARANG
            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, keterangan_detail, debet, kredit, saldo, tabel,uid_akun_efek) VALUES ('$uid', '$waktu', '25', '$no_bukti', '$_POST[keterangan]', '$total', '0', '$saldo_debet', 'pemesanan_barang','$uid_akun')";
            pg_query($conn, $sql);

            // Update keuangan PERSEDIAAN BARANG sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid'");
            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid' AND created_at > '$waktu' ");
            // -------------------END HISTORY PERSEDIAAN BARANG ----------------

            // ---------------- HISTORY LOG KAS KECIL(BCA) -----------------
            //CEK SALDO TERAKHIR AKUN KAS KECIL (BCA)
            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
            $saldo_kredit = $pembayaran_sebelum - $total;

            //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, keterangan_detail, debet, kredit, saldo, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$no_bukti', '$_POST[keterangan]', '0', '$total', '$saldo_kredit', 'pemesanan_barang', '$uid')";
            pg_query($conn, $sql);

            // Update keuangan customer sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_akun'");
            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
            //   -------------- END HISTORY KAS KECIL ---------------------------

         //PENCATATAN DI JURNAl
         $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid', 'Pembayaran Persediaan Barang', '1', '$no_bukti')";
         pg_query($conn,$sql);	    
         $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
         $id_jurnal= $data["id_jurnal"];
           
            //AKUN CUSTOMER SEBAGAI DEBET
            $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid', '$total')";
            pg_query($conn,$sql);

            //AKUN PENJUALAN SEBAGAI KREDIT
            $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun', '$total')";
            pg_query($conn,$sql);
         
        header("location: persediaan");
        // echo "UPDATE invoice_header SET status_pembayaran= 'false' , jenis_pembayaran = 'false', jumlah_terbayar = '0', total=(total - $invoice[jumlah]), sisa_bayar=(sisa_bayar - $invoice[jumlah]), is_lunas =null WHERE uid='$row'";
	}
                        
	elseif($act=='update'){
      $tz = 'Asia/Jakarta';
      $dt = new DateTime("now", new DateTimeZone($tz));
      $waktu = $dt->format('Y-m-d G:i');

      // HAPUS DATA 
         $a = pg_fetch_array(pg_query($conn, "SELECT * FROM pemesanan_barang WHERE id='$_POST[id]'"));
         $detail = pg_fetch_array(pg_query($conn, "SELECT uid FROM detail_barang WHERE id = '$a[id_barang]'"));
         $akun = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data ='$detail[uid]'"));
         $uid = $akun["uid"];
        
        // update saldo terkini Customer
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_harga]) WHERE uid='$a[uid_akun_bayar]'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total_harga]) WHERE uid='$uid'");
        
        // delete Jurnal
        $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$uid'"));
        pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
        
       
        // Soft Delete keu_akun_log AKUN HARGA PENJUALAN dan CUSTOMER
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu' WHERE  created_at = '$a[verify]' and (uid_akun_efek='$a[uid_akun_bayar]' OR uid_akun_efek='$uid')"; 
        pg_query($conn, $sql);
       
        // Update akun AKUN HARGA PENJUALAN sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $a[total_harga]) WHERE uid_akun='$uid' AND created_at > '$a[verify]'";
        pg_query($conn, $sql);
       
        // Update akun CUSTOMER  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo + $a[total_harga]) WHERE uid_akun='$a[uid_akun_bayar]' AND created_at > '$a[verify]'";
        pg_query($conn, $sql);

        // ---------------------------------------------------



      // INSERT ULANG 

          $a = pg_fetch_array(pg_query($conn, "SELECT * FROM pemesanan_barang WHERE id='$_POST[id]'"));
          $no_bukti = $a["no_bukti"];
          $b = pg_fetch_array(pg_query($conn, "SELECT uid FROM detail_barang WHERE id = '$a[id_barang]'"));

        $total = $a["total_harga"];
        $uid_akun=$_POST["uid_akun"];
        $akun = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data ='$b[uid]'"));
        $uid = $akun["uid"];

            // UPDATE TABLE pemesanan_barang
            pg_query($conn, "UPDATE pemesanan_barang SET verify='$waktu', keterangan_detail='$_POST[keterangan]', uid_akun_bayar='$uid_akun' WHERE id='$_POST[id]'");

            // ------------------HISTORY LOG PERSEDIAAN BARANG--------------
            //CEK LOG SALDO TERAKHIR PERSEDIAAN BARANG
            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
            $saldo_debet = $pembayaran_sebelum + $total;
            //PENCATATAN DI LOG KEU AKUN PERSEDIAAN BARANG
            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, keterangan_detail, debet, kredit, saldo, tabel,uid_akun_efek) VALUES ('$uid', '$waktu', '25', '$no_bukti', '$_POST[keterangan]', '$total', '0', '$saldo_debet', 'pemesanan_barang','$uid_akun')";
            pg_query($conn, $sql);

            // Update keuangan PERSEDIAAN BARANG sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total) WHERE uid='$uid'");
            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo+$total) WHERE uid_akun='$uid' AND created_at > '$waktu' ");
            // -------------------END HISTORY PERSEDIAAN BARANG ----------------

            // ---------------- HISTORY LOG KAS KECIL(BCA) -----------------
            //CEK SALDO TERAKHIR AKUN KAS KECIL (BCA)
            $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
            $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
            $saldo_kredit = $pembayaran_sebelum - $total;

            //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, keterangan_detail, debet, kredit, saldo, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$no_bukti', '$_POST[keterangan]', '0', '$total', '$saldo_kredit', 'pemesanan_barang', '$uid')";
            pg_query($conn, $sql);

            // Update keuangan customer sesudah tanggal
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total) WHERE uid='$uid_akun'");
            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
            //   -------------- END HISTORY KAS KECIL ---------------------------

         //PENCATATAN DI JURNAl
         $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid', 'Pembayaran Persediaan Barang', '1', '$no_bukti')";
         pg_query($conn,$sql);	    
         $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
         $id_jurnal= $data["id_jurnal"];
           
            //AKUN CUSTOMER SEBAGAI DEBET
            $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid', '$total')";
            pg_query($conn,$sql);

            //AKUN PENJUALAN SEBAGAI KREDIT
            $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun', '$total')";
            pg_query($conn,$sql);
         
        header("location: persediaan");
	}
    pg_close($conn);
}
     

?>