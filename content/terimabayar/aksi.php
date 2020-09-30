<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])) {
    header('location:keluar');
} else {
    include "../../konfig/koneksi.php";
    include "../../konfig/library.php";

    $act = $_GET['act'];
    if ($act == 'tambahakunkreditpenjualan') {
        $no = $_POST['id'] + 1;
?>
        <tr id="<?php echo $no; ?>">
            <td><input type="checkbox" name="check_list[]" value="<?php echo $no; ?>" checked></td>
            <td>
                <select name="uid_akun_<?php echo $no; ?>" class="form-control select2" required maxlength="4" size="4">
                    <option value="">Pilih</option>
                    <?php
                    $tampil = pg_query($conn, "SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                    while ($r = pg_fetch_array($tampil)) {
                        echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                    }
                    ?>
                </select>
            </td>
            <td><input size='1' type="text" class="form-control" name="keterangan_<?php echo $no; ?>"></td>
            <td><input size='1'  type="text" id="jumlahKredit<?= $no ?>" class="form-control money text-right jumlahKredit" name="jumlah_<?php echo $no; ?>"></td>
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
    } else if ($act == 'aksibayarpenjualan') {
        //SIMPAN DANA DI PENERIMAAN DANA : DEBET UNTUK PENERIMAAN KAS


        
        $tgl_awal=$thn_sekarang.'-'.$bln_sekarang.'-01 00:00:00';
        $tanggal_akhir=$thn_sekarang.'-'.$bln_sekarang.'-31 23:59:59';
        
        $d = pg_fetch_array(pg_query($conn, "SELECT MAX(no_bukti) as nomor FROM keu_pembayaran where id_divisi='$_SESSION[divisi]' and deleted_at is NULL "));
        $kode_before = substr($d['nomor'],0,7);
        $kode_now="PPL.$thn.";
        if($kode_before==$kode_now){
            $no_urut = (int) substr($d['nomor'],7,6);
            $no_urut++;
            $no_bukti = $kode_before.sprintf("%06s",$no_urut);
        }
        else{
            $no_bukti = $kode_now.sprintf("%06s",1);
        }
        
        $a = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_akun WHERE uid_data = '$_POST[uid_customer]'"));
        $waktu = $_POST['tanggal'] . ' ' . $_POST['jam'];

        $id_metode_bayar = $_POST["id_metode_bayar"];
        $uid_akun_terima = $_POST["uid_akun_terima"];
        $uid_customer = $a["uid"];
        $deposit = $_POST["deposit_value"];
        $total_tagihan = $_POST["total_tagihan"];
        $total_pembayaran = $_POST["total_pembayaran"];
        $selisih = ($total_pembayaran + $deposit) - $total_tagihan ;
        $catatan = $_POST["catatan"];
        // $sql = "INSERT INTO keu_akun_payment_terima (created_at, waktu_terima, id_metode_bayar, catatan, uid_akun_terima, uid_invoice, jumlah_bayar) VALUES ('$waktu_sekarang', '$waktu', '$_POST[id_metode_bayar]', '$_POST[catatan]', '$_POST[uid_akun_terima]', '$_POST[uid_invoice]', '$jumlah_bayar') RETURNING uid";
     
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
            if (!empty($_POST['customer'])) {
            $keterangan_detail = '';
            $uid_invoice_header ='';
            foreach ($_POST['customer'] as $check) {
                $uid = $_POST["uid_$check"];
                $jumlah = $_POST["number_$check"];
                $jumlah = str_replace(".", "", $jumlah);
                $invoice_number = $_POST["invoice_number_$check"];
                $sisa = $_POST["sisa_bayar_$check"];
                $sisa = $sisa -$jumlah;
                $keterangan_detail .= $_POST["invoice_number_$check"] . ',';
                $uid_invoice_header .= $_POST["uid_$check"] . ',';

                   // check lunas
                   if ($sisa == 0) {
                    $sisa = 0;
                    $is_lunas = 'Y';
                    $jumlah = $jumlah;
                } else {
                    $sisa = $sisa - $deposit;
                    $is_lunas = null;
                    $jumlah = $jumlah + $deposit;
                }

                if ($sisa < 0) {
                    $is_lunas = 'Y';
                } else {
                    $is_lunas = null;
                }

                // Update invoice_header                
                $sql = "UPDATE invoice_header SET status_pembayaran= 'true' , jenis_pembayaran = 'true', jumlah_terbayar = (jumlah_terbayar + $jumlah), sisa_bayar= $sisa,  is_lunas='$is_lunas' WHERE uid='$uid'";
                pg_query($conn, $sql);

                // insert detail pembayaran                
                $sql = "INSERT INTO keu_detail_pembayaran (uid_invoice_header, no_invoice, jumlah, uid_customer, no_bukti) VALUES ('$uid', '$invoice_number', '$jumlah', '$uid_customer', '$no_bukti')";
                pg_query($conn, $sql);


            }
                $uid_invoice_header = substr($uid_invoice_header, 0, -1);
                $keterangan_detail = substr($keterangan_detail, 0, -1);
                // insert keu_pembayaran
                $sql = "INSERT INTO keu_pembayaran (uid_customer, id_metode_bayar, uid_akun_terima, total_tagihan, total_pembayaran, status_pembayaran, created_at, tanggal, catatan, keterangan_detail,uid_invoice_header, no_bukti, jumlah_deposit,id_divisi) VALUES ('$uid_customer', '$id_metode_bayar', '$uid_akun_terima', '$total_tagihan', '$total_pembayaran', '$status_pembayaran' ,'$waktu_sekarang', '$waktu','$_POST[catatan]','$keterangan_detail','$uid_invoice_header','$no_bukti', '$deposit', '$_SESSION[divisi]')";
                pg_query($conn,$sql);
                
                $data = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_pembayaran ORDER BY created_at DESC limit 1 "));
                $uid_pembayaran= $data["uid"];
                
                //PENCATATAN DI JURNAl
                $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti,id_divisi) VALUES ('$waktu', '$uid_pembayaran', 'Pembayaran Piutang', '1', '$keterangan_detail','2')";
                pg_query($conn,$sql);	    
                $data = pg_fetch_array(pg_query($conn, "SELECT MAX(id) as id_jurnal FROM keu_akun_jurnal"));
                $id_jurnal= $data["id_jurnal"];
                    //AKUN CUSTOMER SEBAGAI DEBET
                    $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_terima', '$total_pembayaran')";
                    pg_query($conn,$sql);

                    //AKUN PENJUALAN SEBAGAI KREDIT
                    $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_customer', '$total_pembayaran')";
                    pg_query($conn,$sql);
                }

                // ------------------HISTORY LOG CUSTOMER--------------
                //CEK LOG SALDO TERAKHIR customer
                $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_customer' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                $saldo_kredit = $pembayaran_sebelum - $total_pembayaran;
                //PENCATATAN DI LOG KEU AKUN customer
                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_customer', '$waktu', '25', '$no_bukti', '0', '$total_pembayaran', '$saldo_kredit', '$keterangan_detail', 'keu_pembayaran','$uid_akun_terima')";
                pg_query($conn, $sql);

                // Update keuangan customer sesudah tanggal
                pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $total_pembayaran) WHERE uid='$uid_customer'");
                pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo-$total_pembayaran) WHERE uid_akun='$uid_customer' AND created_at > '$waktu' ");
                // -------------------END HISTORY CUSTOMER ----------------
                // ---------------- HISTORY LOG KAS KECIL(BCA) -----------------
                //CEK SALDO TERAKHIR AKUN KAS KECIL (BCA)
                $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='$uid_akun_terima' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                $saldo_debet = $pembayaran_sebelum + $total_pembayaran;

                //PENCATATAN DI LOG KEU AKUN KAS KECIL (BCA)
                $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun_terima', '$waktu', '25', '$no_bukti', '$total_pembayaran', '0', '$saldo_debet', '$keterangan_detail', 'keu_pembayaran', '$uid_customer')";
                pg_query($conn, $sql);

                // Update keuangan customer sesudah tanggal
                pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total_pembayaran) WHERE uid='$uid_akun_terima'");
                pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $total_pembayaran) WHERE uid_akun='$uid_akun_terima' AND created_at > '$waktu' ");
                //   -------------- END HISTORY KAS KECIL ---------------------------

                
                // ---------------- HISTORY LOG PENDAPATAN -----------------
                //CEK SALDO TERAKHIR AKUN PENDAPATAN
                // $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at < '$waktu' and uid_akun='0709a09f-6317-6fbb-6541-670a30dc5657' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                // $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                // $saldo_kredit = $pembayaran_sebelum + $total_pembayaran;

                // //PENCATATAN DI LOG pendapatan usaha
                // $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, keterangan_detail, tabel,uid_akun_efek) VALUES ('0709a09f-6317-6fbb-6541-670a30dc5657', '$waktu', '25', '$no_bukti', '0', '$total_pembayaran', '$saldo_kredit', '$keterangan_detail', 'keu_pembayaran', '$uid_akun_terima')";
                // pg_query($conn, $sql);

                // // Update keuangan pendapatan sesudah tanggal
                // pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $total_pembayaran) WHERE uid='0709a09f-6317-6fbb-6541-670a30dc5657'");
                // pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $total_pembayaran) WHERE uid_akun='0709a09f-6317-6fbb-6541-670a30dc5657' AND created_at > '$waktu' ");
                //   -------------- END HISTORY KAS KECIL ---------------------------
            
                
                    //SIMPAN DANA DI EFEK KREDIT        
                    if (!empty($_POST['check_list'])) {
                        // var_dump($_POST['check_list']);
                        foreach ($_POST['check_list'] as $check) {
                        $uid_akun = $_POST["uid_akun_$check"];
                        $keterangan = $_POST["keterangan_$check"];
                        $jumlah = $_POST["jumlah_$check"];
                        $jumlah = str_replace(".", "", $jumlah);

                        // Insert Keu_akun_payment
                        pg_query($conn, "INSERT INTO keu_akun_payment_terima_kredit(uid_akun, jumlah, keterangan, uid_terimabayar,tanggal, no_bukti) VALUES ('$uid_akun', '$jumlah', '$keterangan','$uid_akun_terima', '$waktu', '$no_bukti')");


                                // ------------------HISTORY LOG CUSTOMER--------------
                        //CEK LOG SALDO TERAKHIR customer 
                        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at = '$waktu' and uid_akun='$uid_customer' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                        $saldo_kredit = $pembayaran_sebelum - $jumlah;

                        //PENCATATAN DI LOG KEU AKUN customer Efek selisih bayar
                        
                        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_customer', '$waktu', '25',  '0', '$jumlah', '$saldo_kredit','$no_bukti', '$keterangan', 'keu_pembayaran','$uid_akun')";
                        pg_query($conn, $sql);

                        // Update keuangan customer sesudah tanggal
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_customer'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo-$jumlah) WHERE uid_akun='$uid_customer' AND created_at > '$waktu' ");
                        // -------------------END HISTORY CUSTOMER ----------------

                        //CEK SALDO TERAKHIR akun kredit lebih kecil dari tanggal
                        $pembayaran_sebelum = pg_fetch_array(pg_query($conn, "SELECT id, saldo FROM keu_akun_log WHERE created_at <= '$waktu' and uid_akun='$uid_akun' AND deleted_at is NULL ORDER BY id DESC LIMIT 1"));
                        $pembayaran_sebelum = $pembayaran_sebelum["saldo"];
                        $saldo = $pembayaran_sebelum + $jumlah;
            
                        //CEK JENIS AKUN KEUANGAN
                        $a = pg_fetch_array(pg_query($conn, "SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                        $jenis_akun = $a['jenis_akun'];

                        if($jenis_akun == 'D'){
                            //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                            $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan, keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$jumlah', '0', '$saldo','$no_bukti', '$keterangan', 'keu_pembayaran','$uid_akun_terima')";
                            pg_query($conn, $sql);

                        // Update akun efek pembayaran  sesudah tanggal dan saldo terkini
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");
                            
                      
                        //AKUN jurnal SEBAGAI KREDIT
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                        pg_query($conn,$sql);
                        
                    
                    }
                    else{
                        //PENCATATAN DI LOG KEU AKUN EFEK PEMBAYARAN
                        $sql = "INSERT INTO keu_akun_log (uid_akun, created_at, id_status, debet, kredit, saldo, keterangan,keterangan_detail, tabel,uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25','0', '$jumlah','$saldo','$no_bukti', '$keterangan', 'keu_pembayaran','$uid_akun_terima')";
                        pg_query($conn, $sql);
                        
                        // Update akun efek pembayaran  sesudah tanggal
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $jumlah) WHERE uid='$uid_akun'");
                        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $jumlah) WHERE uid='$uid_akun_terima'");
                        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo + $jumlah) WHERE uid_akun='$uid_akun' AND created_at > '$waktu' ");

                        //AKUN jurnal SEBAGAI KREDIT
                        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                        pg_query($conn,$sql);
                    }
                 
                  
                        }
                        foreach ($_POST['customer'] as $check) {
                            $uid = $_POST["uid_$check"];
                            // Update invoice_header                
                            $sql = "UPDATE invoice_header SET  is_lunas='Y' WHERE uid='$uid'";
                            pg_query($conn, $sql);
                        }
                    }

            // insert pegawai_activity_log
            $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_pembayaran where no_bukti='$no_bukti' and deleted_at is NULL "));
            pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('insert terima bayar','$d[uid]','$waktu','$_SESSION[login_user]','keu_pembayaran')");

            header("location: terimabayar");
         }
                        
	elseif($act=='delete'){
        $a = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_pembayaran WHERE uid='$_GET[uid]'"));
        $selisih = $a["total_tagihan"] - $a["total_pembayaran"];

        
        if($selisih > 0){       
        // update saldo terkini Customer
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_tagihan]) WHERE uid='$a[uid_customer]'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total_pembayaran]) WHERE uid='$a[uid_akun_terima]'");

        // Update keuangan akun pembelian
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_pembayaran]) WHERE uid='0709a09f-6317-6fbb-6541-670a30dc5657'");
        }
        else{
            
        // update saldo terkini Customer
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_pembayaran]) WHERE uid='$a[uid_customer]'");

        // update saldo terkini akun bank(BCA)
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini - $a[total_pembayaran]) WHERE uid='$a[uid_akun_terima]'");

        // Update keuangan akun pembelian
        pg_query($conn, "UPDATE keu_akun SET saldo_terkini= (saldo_terkini + $a[total_pembayaran]) WHERE uid='0709a09f-6317-6fbb-6541-670a30dc5657'");
        
        }
        // delete Jurnal
        $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$a[uid]'"));
        echo "SELECT id FROM keu_akun_jurnal WHERE uid_data='$a[uid]'";
        pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
        pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");
        // Soft Delete keu_akun_log-----------------------


        //  cek keu_akun_payment_terima_kredit / selisih bayar
        $cek = pg_query($conn, "SELECT * FROM keu_akun_payment_terima_kredit WHERE tanggal='$a[tanggal]' and uid_terimabayar='$a[uid_akun_terima]' and deleted_at is null");
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

            // delete Jurnal
            $j = pg_fetch_array(pg_query($conn, "SELECT id FROM keu_akun_jurnal WHERE uid_data='$r[uid_akun]'"));
            pg_query($conn, "DELETE FROM keu_akun_jurnal_detail WHERE id_data='$j[id]'");
            pg_query($conn, "DELETE FROM keu_akun_jurnal WHERE id='$j[id]'");

            $cek= pg_fetch_array(pg_query($conn, "SELECT uid_data FROM keu_akun WHERE uid='$r[uid_akun]'"));
            $uid_customer = $cek["uid_data"];
        }

        // Soft Delete keu_akun_log KAS KECIL(BCA) dan CUSTOMER
        $sql="UPDATE keu_akun_log SET deleted_at='$waktu_sekarang' WHERE  created_at = '$a[tanggal]' and (uid_akun_efek='$a[uid_customer]' OR uid_akun_efek='$a[uid_akun_terima]')"; 
        pg_query($conn, $sql);
       
        // Update akun KAS KECIL(BCA)  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo - $a[total_pembayaran]) WHERE uid_akun='$a[uid_akun_terima]' AND created_at > '$a[tanggal]'";
        pg_query($conn, $sql);
       
        // Update akun CUSTOMER  sesudah tanggal
        $sql= "UPDATE keu_akun_log SET saldo= (saldo + $a[total_pembayaran]) WHERE uid_akun='$a[uid_customer]' AND created_at > '$a[tanggal]'";
        pg_query($conn, $sql);

        // UPDATE AKUN PENDAPATAN SESUDAH TANGGAL
        pg_query($conn, "UPDATE keu_akun_log SET saldo= (saldo - $a[total_pembayaran]) WHERE uid_akun='0709a09f-6317-6fbb-6541-670a30dc5657' AND created_at > '$a[tanggal]' ");
        //   -------------- END HISTORY KAS KECIL ---------------------------


        // ---------------------------------------------------
        
        // Soft Delete keu_ditail_pembayaran ----------------------------------
        // $splitID = substr($a["uid_invoice_header"], 0, -1);
        $splitID = explode(",",$a["uid_invoice_header"]);
        foreach($splitID as $row){
            $invoice = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_detail_pembayaran WHERE uid_invoice_header = '$row'"));

            // Update Invoice_header
            $sql = "UPDATE  invoice_header SET status_pembayaran= 'false' , jenis_pembayaran = 'false', jumlah_terbayar = (jumlah_terbayar - $invoice[jumlah]), sisa_bayar=(sisa_bayar + $invoice[jumlah]), is_lunas =null WHERE uid='$row'";
            pg_query($conn, $sql);
          
            // DELETE keu_detail_pembayaran
            $sql = "DELETE FROM keu_detail_pembayaran WHERE uid_invoice_header='$row'";
            pg_query($conn, $sql);
        }

		$sql="UPDATE keu_akun_payment_terima_kredit SET deleted_at='$waktu_sekarang' WHERE tanggal='$a[tanggal]' and uid_terimabayar='$a[uid_akun_terima]'";
		pg_query($conn,$sql);
        
        // Soft Delete keu_akun_payment_terima_kredit
        $sql="UPDATE keu_pembayaran SET deleted_at='$waktu_sekarang' WHERE uid='$_GET[uid]'";
		$result=pg_query($conn,$sql);

        
        // insert pegawai_activity_log
        $d = pg_fetch_array(pg_query($conn, "SELECT uid FROM keu_pembelian where no_bukti='$no_bukti' and deleted_at is NULL "));
        pg_query($conn, "INSERT INTO pegawai_activity_log(activity, effects_to, created_at, uid_pegawai, tabel) VALUES('delete terima bayar','$_GET[uid]','$waktu','$_SESSION[login_user]','keu_pembelian')");

        header("location: terimabayar");
 
	} 
    pg_close($conn);
}
     

?>