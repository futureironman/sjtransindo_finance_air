<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])) {
    header('location:keluar');
} else {
    include "../../konfig/koneksi.php";
    include "../../konfig/library.php";

    $act = $_GET['act'];
    if ($act == 'tambahakunkreditpembelian') {
        $no = $_POST['id'] + 1;
?>
        <tr id="<?php echo $no; ?>">
            <td><input type="checkbox" name="check_list[]" value="<?php echo $no; ?>" checked></td>
            <td>
                <select name="uid_akun_<?php echo $no; ?>" class="form-control  select2" required>
                    <option value="">Pilih</option>
                    <?php
                    $tampil = pg_query($conn, "SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while ($r = pg_fetch_array($tampil)) {
                        echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                    }
                    ?>
                </select>
            </td>
            <td><input type="text" class="form-control" name="keterangan_<?php echo $no; ?>"></td>
            <td><input type="text" id="jumlahKredit<?= $no ?>" class="form-control money text-right jumlahKredit" name="jumlah_<?php echo $no; ?>"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="btnHapusKredit(<?= $no ?>)"><i class="fa fa-trash"></i></button></td>
        </tr>

        <script type="text/javascript" src="addons/js/masking_form.js"></script>
        <script>
         $(document).ready(function() {
             $(".select2").select2();
         });
     </script>
        <script type="text/javascript">
            // jumlah bayar kurang bayar

            $('.jumlahKredit').keyup(function() {

                var selisih = $("#selisih").text();
                selisih = selisih.replace(/\./g, "")
                selisih = parseInt(selisih);
                var sum = 0;
                var kredit = 0;
                $('.jumlahKredit').each(function() {
                    kredit = $(this).val().replace(".", "");
                    kredit = parseInt(kredit.replace(/\./g, ''));
                    sum += kredit;
                });
                $("#totalKredit").number(sum, 0, ',', '.');
                if (selisih > 0) {
                    var total_selisih = number_format(selisih - sum);
                } else {
                    var total_selisih = number_format(selisih + sum);

                }
                var result = total_selisih.fontcolor("red");
                document.getElementById("total_selisih").innerHTML = result;
            });
            // ---------------------------
            // function hapus kurang Bayar
            function btnHapusKredit(a) {
                // selisih
                var selisih = $("#selisih").text();
                selisih = selisih.replace(/\./g, "")
                selisih = parseInt(selisih);
                // total selisih
                var total_selisih = $("#total_selisih").text();
                total_selisih = total_selisih.replace(/\./g, "")
                total_selisih = parseInt(total_selisih);
                // jumlah kredit
                var nilaiKredit = $("#jumlahKredit" + a).val();
                nilaiKredit = nilaiKredit.replace(/\./g, "")
                nilaiKredit = parseInt(nilaiKredit);
                // total kredit
                var total_kredit = $("#totalKredit").html();
                total_kredit = total_kredit.replace(/\./g, "")
                total_kredit = parseInt(total_kredit);
                var sum = total_kredit - nilaiKredit;

                var conf = confirm("Are you sure you want to delete ?");
                if (conf) {
                    // inset nilai kredit
                    $("#totalKredit").number(sum, 0, ',', '.');

                    // insert total selisih
                    if (selisih > 0) {
                        var total_selisih = number_format(total_selisih + nilaiKredit);
                    } else {
                        var total_selisih = number_format(total_selisih - nilaiKredit);
                    }
                    var result = total_selisih.fontcolor("red");
                    document.getElementById("total_selisih").innerHTML = result;


                    $("#akun_kredit").find("#jumlahKredit" + a).each(function() {
                        $(this).closest("tr").remove();
                    });
                }
            }
            // ----------------
        </script>
<?php
    } else if ($act == 'aksibayarpembelian') {
        //SIMPAN DANA DI PENERIMAAN DANA : DEBET UNTUK PENERIMAAN KAS
        
        $a = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data = '$_POST[uid_supplier]'"));

        $waktu = $_POST['tanggal'] . ' ' . $_POST['jam'];
        $id_metode_bayar = $_POST["id_metode_bayar"];
        $uid_akun_bayar = $_POST["uid_akun_bayar"];
        $no_bukti = $_POST["no_bukti"];
        $uid_supplier = $a["uid"];
        $total_tagihan = $_POST["total_tagihan"];
        $total_pembayaran = $_POST["total_pembayaran"];
        $selisih = is_int($total_pembayaran) - is_int($total_tagihan);
        $catatan = $_POST["catatan"];

        if($selisih > 0){
            $status_pembayaran = "Lebih Bayar";
        }
        elseif($selisih < 0){
            $status_pembayaran = "Kurang bayar";
        }
        elseif($selisih == 0){
            $status_pembayaran = "Lunas";
        }
        //Aksi pembayaran di table invoice_header    
        // var_dump($_POST['baru']);
            if (!empty($_POST['supplier'])) {
            $keterangan_detail = '';
            $id_invoice_header ='';
            foreach ($_POST['supplier'] as $check) {
                $id = $_POST["id_$check"];
                $jumlah = $_POST["number_$check"];
                $jumlah = str_replace(".", "", $jumlah);
                $total = $_POST["total_$check"];
                $total = str_replace(".", "", $total);
                $invoice_number = $_POST["invoice_number_$check"];
                $sisa_bayar = $total - $jumlah;
                $keterangan_detail .= $_POST["invoice_number_$check"] . ',';
                $id_invoice_header .= $_POST["id_$check"] . ',';

                // check lunas
                if($sisa_bayar == 0){
                    $sisa_bayar = 0;
                    $is_lunas = 'Y';
                }
                else {
                    $sisa_bayar = $sisa_bayar;
                    $is_lunas= null;
                }
                
                // Update invoice_header                
                $sql = "UPDATE inv_detail_pembelian SET jumlah_terbayar = (jumlah_terbayar + $jumlah), sisa_bayar=(sisa_bayar  - $sisa_bayar), is_lunas='$is_lunas' WHERE id='$id'";
                pg_query($conn, $sql);

                // insert detail pembayaran                
                $sql = "INSERT INTO keu_detail_pembelian (id_invoice_header, no_invoice, jumlah, uid_supplier,no_bukti) VALUES ('$id', '$invoice_number', '$jumlah', '$uid_supplier', '$no_bukti')";
                pg_query($conn, $sql);

            }
                $id_invoice_header = substr($id_invoice_header, 0, -1);
                $keterangan_detail = substr($keterangan_detail, 0, -1);
                // insert keu_pembelian
                pg_query($conn, "INSERT INTO keu_pembelian (uid_supplier, id_metode_bayar, uid_akun_bayar, total_tagihan, total_pembayaran, status_pembayaran, created_at, tanggal, catatan, keterangan_detail,id_invoice_header,no_bukti) VALUES ('$uid_supplier', '$id_metode_bayar', '$uid_akun_bayar', '$total_tagihan', '$total_pembayaran', '$status_pembayaran' ,'$waktu_sekarang', '$waktu','$_POST[catatan]','$keterangan_detail','$id_invoice_header','$no_bukti')");


                $data = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_pembelian where deleted_at is null ORDER BY uid ASC limit 1 "));
                $uid_pembayaran= $data["uid"];
                
                
                //PENCATATAN DI JURNAl
                $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid_pembayaran', 'Pembayaran Pembelian', '1', '$keterangan_detail')";
                pg_query($conn,$sql);	    
                $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                $id_jurnal= $data["id_jurnal"];


                //AKUN SUPPLIER SEBAGAI DEBET
                $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_supplier', '$total_pembayaran')";
                pg_query($conn,$sql);

                //AKUN AKUN BAYAR SEBAGAI KREDIT
                $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_bayar', '$total_pembayaran')";
                pg_query($conn,$sql);


                // ------------------HISTORY LOG supplier--------------
                //CEK LOG SALDO TERAKHIR supplier
                $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_supplier' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
                $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                $saldo_debet = $pembayaran_sebelum - $total_pembayaran;
                
                //PENCATATAN DI LOG KEU AKUN supplier
                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_supplier', '$waktu', '25', '$no_bukti', '0', '$total_pembayaran', '$saldo_debet', '$keterangan_detail', 'keu_pembelian','$uid_supplier')";
                pg_query($conn, $sql);

                // Update keuangan supplier sesudah tanggal
                pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_pembayaran) WHERE uid='$uid_supplier'");
                pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo-$total_pembayaran) WHERE uid_akun='$uid_supplier' AND created_at > '$waktu' ");
                // -------------------END HISTORY supplier ----------------
 
                // ---------------- HISTORY LOG KAS KECIL(BCA) -----------------
                //CEK SALDO TERAKHIR AKUN KAS KECIL (BCA)
                $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_bayar' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
                $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                $saldo_kredit = $pembayaran_sebelum - $total_pembayaran;

                //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun_bayar', '$waktu', '25', '$no_bukti', '0', '$total_pembayaran', '$saldo_kredit', '$keterangan_detail', 'keu_pembelian', '$uid_akun_bayar')";
                pg_query($conn, $sql);

                // Update keuangan KAS(BCA) sesudah tanggal
                pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_pembayaran) WHERE uid='$uid_akun_bayar'");
                pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $total_pembayaran) WHERE uid_akun='$uid_akun_bayar' AND created_at > '$waktu' ");
                //   -------------- END HISTORY KAS KECIL ---------------------------

                }
                    
                    //SIMPAN DANA DI EFEK KREDIT        
                    if (!empty($_POST['check_list'])) {
                        // var_dump($_POST['check_list']);
                        foreach ($_POST['check_list'] as $check) {
                        $uid_akun = $_POST["uid_akun_$check"];
                        $keterangan = $_POST["keterangan_$check"];
                        $jumlah = $_POST["jumlah_$check"];
                        $jumlah = str_replace(".", "", $jumlah);

                        // Insert Keu_akun_payment
                        pg_query($conn, "INSERT INTO keu_efek_selisih_bayar_pembelian(uid_akun, jumlah, keterangan, uid_terimabayar,tanggal) VALUES ('$uid_akun', '$jumlah', '$keterangan','$uid_akun_bayar', '$waktu')");


                          //CEK SALDO TERAKHIR akun kredit lebih kecil dari tanggal
                        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at < '$waktu' ORDER BY id DESC LIMIT 1"));
                        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                        $saldo = $pembayaran_sebelum + $jumlah;
              
                        //CEK JENIS AKUN KEUANGAN
                        $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                        $jenis_akun = $a['jenis_akun'];

                         if($jenis_akun == 'D'){
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah', '0', '$saldo', '$no_bukti', '$keterangan', 'keu_keuangan','$uid_akun_bayar')";
                            pg_query($conn, $sql);

                        // Update akun efek pembayaran  sesudah tanggal
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_akun_bayar'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");

                            
                        //PENCATATAN DI JURNAl
                        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid_akun', 'Efek Pembayaran', '1', '$keterangan')";
                        pg_query($conn,$sql);	    
                        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                        $id_jurnal= $data["id_jurnal"];

                        //AKUN CUSTOMER SEBAGAI DEBET
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                        pg_query($conn,$sql);

                        //AKUN PENJUALAN SEBAGAI KREDIT
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_bayar', '$jumlah')";
                        pg_query($conn,$sql);
                    
                        }
                        else{
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan,keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah','0','$saldo', '$no_bukti', '$keterangan', 'keu_keuangan','$uid_akun_bayar')";
                            pg_query($conn, $sql);
                            
                            // Update akun efek pembayaran  sesudah tanggal
                            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_akun_bayar'");
                            pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");

                              //PENCATATAN DI JURNAl
                        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$uid_akun', 'Efek Pembayaran', '1', '$keterangan')";
                        pg_query($conn,$sql);	    
                        $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                        $id_jurnal= $data["id_jurnal"];
                        //AKUN SUPPLIER SEBAGAI DEBET
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                        pg_query($conn,$sql);

                        //AKUN AKUN SEBAGAI KREDIT
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_bayar', '$jumlah')";
                        pg_query($conn,$sql);
                    }
                }
                foreach ($_POST['supplier'] as $check) {
                    $id = $_POST["id_$check"];
                    // Update invoice_header                
                    $sql = "UPDATE inv_detail_pembelian SET  is_lunas='Y' WHERE id='$id'";
                    pg_query($conn, $sql);
                }
            }  
            header("location: kirimbayar");
            // echo "INSERT INTO keu_detail_pembelian (id_invoice_header, no_invoice, jumlah, uid_supplier,'no_bukti') VALUES ('$id', '$invoice_number', '$jumlah', '$uid_supplier', '$no_bukti')";
         }
                        
	elseif($act=='delete'){
        $a = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_pembelian WHERE uid='$_GET[uid]'"));
        
        // update saldo terkini supplier
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_pembayaran]) WHERE uid='$a[uid_supplier]'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_pembayaran]) WHERE uid='$a[uid_akun_bayar]'");
        
        // delete Jurnal
        $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$a[uid]'"));
        pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
       
   // Soft Delete keu_akun_log-----------------------
        //  cek keu_akun_payment_terima_kredit
        $cek = pg_query($conn, "SELECT * FROM keu_efek_selisih_bayar_pembelian WHERE tanggal='$a[tanggal]' and uid_terimabayar='$a[uid_akun_bayar]' and deleted_at is null");
        while($r=pg_fetch_array($cek)){
            // update saldo terkini
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $r[jumlah]) WHERE uid='$r[uid_akun]'");
            pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $r[jumlah]) WHERE uid='$r[uid_terimabayar]'");

            // Soft Delete keu_akun_log efek pembayaran
            $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[tanggal]' and uid_akun='$r[uid_akun]'"; 
            pg_query($conn, $sql);

            // Update akun efek bayar  sesudah tanggal
            $sql= "UPDATE keu_akun_log SET saldo= (saldo - $r[jumlah]) WHERE uid_akun='$r[uid_akun]' AND created_at > '$r[tanggal]'";
            pg_query($conn, $sql);
            // Update akun KAS KECIL (BACA)  sesudah tanggal
            $sql= "UPDATE keu_akun_log SET saldo= (saldo - $r[jumlah]) WHERE uid_akun='$r[uid_terimabayar]' AND created_at > '$r[tanggal]'";
            pg_query($conn, $sql);

            // delete Jurnal
            $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$r[uid_akun]'"));
            pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
            pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
        }

        // Soft Delete keu_akun_log KAS KECIL(BCA) dan supplier
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[tanggal]' and (uid_akun_efek='$a[uid_supplier]' OR uid_akun_efek='$a[uid_akun_bayar]')"; 
        pg_query($conn, $sql);
       
        // Update akun KAS KECIL(BCA)  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo + $a[total_pembayaran]) WHERE uid_akun='$a[uid_akun_bayar]' AND created_at > '$a[tanggal]'";
        pg_query($conn, $sql);
       
        // Update akun supplier  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo + $a[total_pembayaran]) WHERE uid_akun='$a[uid_supplier]' AND created_at > '$a[tanggal]'";
        pg_query($conn, $sql);

        // ---------------------------------------------------
        
        // Soft Delete keu_ditail_pembayaran ----------------------------------
        // $splitID = substr($a["id_invoice_header"], 0, -1);
        $splitID = explode(",",$a["id_invoice_header"]);
        foreach($splitID as $row){
            $invoice = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_detail_pembelian WHERE id_invoice_header = '$row'"));
            // Update inv_detail_pembelian
            $sql = "UPDATE inv_detail_pembelian SET jumlah_terbayar = (jumlah_terbayar + $invoice
            [jumlah]), sisa_bayar=(sisa_bayar + $invoice[jumlah]), is_lunas = null WHERE id='$row'";
            pg_query($conn, $sql);
            
            // DELETE keu_detail_pembelian
            $sql = "DELETE FROM keu_detail_pembelian WHERE id_invoice_header='$row'";
            pg_query($conn, $sql);
        }

		$sql="UPDATE keu_efek_selisih_bayar_pembelian SET deleted_at='$waktu_sekarang' WHERE tanggal='$a[tanggal]' and uid_terimabayar='$a[uid_akun_bayar]'";
		pg_query($conn,$sql);
        
        // Soft Delete keu_efek_selisih_bayar_pembelian 
        $sql="UPDATE keu_pembelian SET deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
		$result=pg_query($conn,$sql);

        header("location: kirimbayar");
	}
    pg_close($conn);
}

?>