<?php
session_start();
//error_reporting(0);
if (empty($_SESSION['login_user'])){
	header('location:keluar');
}
else{
	include "../../konfig/koneksi.php";
	include "../../konfig/library.php";

	$act=$_GET['act'];
	if ($act=='tambahakunkreditpenjualan'){
		$no=$_POST['id']+1;
		?>
		<tr id="<?php echo $no;?>">
            <td><input type="checkbox" name="check_list[]" value="<?php echo $no;?>" checked></td>
            <td>
                <select name="uid_akun_<?php echo $no;?>" class="form-control" required>
                <option value="">Pilih</option>
                <?php
                $tampil=pg_query($conn,"SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                while($r=pg_fetch_array($tampil)){
                    echo"<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                }
                ?>
                </select>
            </td>
            <td><input type="text" class="form-control" name="keterangan_<?php echo $no;?>"></td>
            <td><input type="text" class="form-control money text-right jumlahKredit" name="jumlah_<?php echo $no;?>"></td>
            <td><button type="button" class="btn btn-danger btn-sm btnHapus"><i class="fa fa-trash"></i></button></td>
        </tr>

        <script type="text/javascript" src="addons/js/masking_form.js"></script>

        <script type="text/javascript">
            $('.jumlahKredit').keyup(function(){
                var sum = 0;
                var kredit = 0;
                $('.jumlahKredit').each(function()
                {
                    kredit = $(this).val().replace(".", "");
                    kredit = parseInt(kredit.replace(/\./g, ''));
                    sum += kredit;
                });
                $("#totalKredit").number(sum,0,',','.');

                /*
                var total = $("#total").val();
                var jumlah_bayar = $("#jumlah_bayar").val();
                jumlah_bayar = jumlah_bayar.replace(".", "");
                jumlah_bayar = parseInt(jumlah_bayar.replace(/\./g, ''));

                var sisa = total - (jumlah_bayar+sum);
                if(sisa!=0){
                    $("#error").html("<div class='alert alert-warning'>Jumlah debet dan kreditnya tidak sama. Mohon periksa kembali</div>");
                    $("#btnSimpanBayar").prop("disabled",true);
                }
                else{
                    $("#error").html("");
                    $("#btnSimpanBayar").prop("disabled",false);
                }
                */
            });
        </script>
		<?php
	}

    else if ($act=='aksibayarpenjualan'){
        //MENDAPATKAN UID AKUN CUSTOMER
        $d=pg_fetch_array(pg_query($conn,"SELECT c.uid AS uid_akun FROM invoice_header a, po_house b, keu_akun c WHERE a.uid_data=b.uid AND a.uid='$_POST[uid_invoice]' AND CAST(b.uid_customer AS UUID)=c.uid_data AND c.linked_table='customer' AND c.deleted_at IS NULL"));
        $uid_akun_customer=$d['uid_akun'];

        //SIMPAN DANA DI PENERIMAAN DANA : DEBET UNTUK PENERIMAAN KAS
        $jumlah_bayar=str_replace(".","",$_POST['jumlah_bayar']);
        $waktu = $_POST['tanggal'].' '.$_POST['jam'];
        
        $sql="INSERT INTO keu_akun_payment_terima (created_at, waktu_terima, id_metode_bayar, catatan, uid_akun_terima, uid_invoice, jumlah_bayar) VALUES ('$waktu_sekarang', '$waktu', '$_POST[id_metode_bayar]', '$_POST[catatan]', '$_POST[uid_akun_terima]', '$_POST[uid_invoice]', '$jumlah_bayar') RETURNING uid";

        
        $d=pg_fetch_array(pg_query($conn,$sql));
        $uid_inv_payment_terima=$d['uid'];

        //SIMPAN DANA DI EFEK KREDIT        
        if(!empty($_POST['check_list'])) {
			foreach($_POST['check_list'] as $check) {
                $uid_akun=$_POST["uid_akun_$check"];
                $keterangan=$_POST["keterangan_$check"];
                $jumlah=$_POST["jumlah_$check"];
                $jumlah=str_replace(".","",$jumlah);

                pg_query($conn,"INSERT INTO keu_akun_payment_terima_kredit(uid_terima, uid_akun, jumlah, keterangan) VALUES ('$uid_inv_payment_terima', '$uid_akun', '$jumlah', '$keterangan')");

                //CEK JENIS AKUN KEUANGAN
                $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                $jenis_akun=$a['jenis_akun'];

                //CEK SALDO TERAKHIR
                $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));
                            
                if($jenis_akun=='D'){
                    //PENCATATAN DI LOG KEU AKUN
                    $saldo = $d['saldo']+$jumlah;
                    $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$keterangan', '$jumlah', '0', '$saldo', '$_POST[uid_invoice]', 'invoice_header', '$_POST[uid_akun_terima]')";
                }
                else{
                    //PENCATATAN DI LOG KEU AKUN
                    $saldo = $d['saldo']+$jumlah;
                    $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun', '$waktu', '25', '$keterangan', '0', '$jumlah', '$saldo', '$_POST[uid_invoice]', 'invoice_header', '$_POST[uid_akun_terima]')";
                }
                pg_query($conn,$sql);
                
                //LOOPING HITUNG ULANG LAGI
                /*
                $tampil=pg_query($conn,"SELECT id, debet, kredit, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at>'$waktu' ORDER BY created_at DESC");
                while($r=pg_fetch_array($tampil)){
                    $saldo_baru = $r['saldo']+$jumlah;
                    
                    //HITUNG ULANG SALDONYA
                    pg_query($conn,"UPDATE keu_akun_log SET saldo='$saldo_baru' WHERE id='$r[id]'");
                }
                */
                pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah) WHERE uid_akun='$uid_akun' AND created_at>'$waktu'");

                $c=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$uid_akun' ORDER BY created_at DESC LIMIT 1"));
                //UPDATE SALDO TERAKHIR
			    pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$c[saldo]' WHERE uid='$uid_akun'");
            }
        }


        //PENCATATAN DI LOG AKUN : DEBET BERTAMBAH
        //-------------
        //DANA KAS BERTAMBAH EFEK DEBET

        //CEK SALDO TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_terima]' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

        $saldo = $d['saldo']+$jumlah_bayar;

        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$_POST[uid_akun_terima]', '$waktu', '20', '$_POST[invoice_number]', '$jumlah_bayar', '0', '$saldo', '$_POST[uid_invoice]', 'invoice_header', '$uid_akun_customer')";

        pg_query($conn,$sql);

        //LOOPING HITUNG ULANG LAGI
        /*
        $tampil=pg_query($conn,"SELECT id, debet, kredit, saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_terima]' AND created_at>'$waktu' ORDER BY created_at DESC");
        while($r=pg_fetch_array($tampil)){
            $saldo = $r['saldo']+$jumlah_bayar;
            
            //HITUNG ULANG SALDONYA
            pg_query($conn,"UPDATE keu_akun_log SET saldo='$saldo' WHERE id='$r[id]'");
        }
        */

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$jumlah_bayar) WHERE uid_akun='$_POST[uid_akun_terima]' AND created_at>'$waktu'");

        //UPDATE SALDO DI KEU AKUN
        //$c=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$_POST[uid_akun_terima]' ORDER BY created_at DESC LIMIT 1"));
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini+$jumlah_bayar) WHERE uid='$_POST[uid_akun_terima]'");
                

        //-------------- 
        //PIUTANG KARYAWAN EFEK KREDIT
        $d=pg_fetch_array(pg_query($conn,"SELECT a.*, b.uid_customer, c.uid AS uid_akun, c.jenis_akun, c.saldo_terkini FROM invoice_header a, po_house b, keu_akun c WHERE a.uid_data=b.uid AND a.uid='$_POST[uid_invoice]' AND CAST(b.uid_customer AS UUID)=c.uid_data AND c.linked_table='customer' AND c.deleted_at IS NULL"));
        $uid_akun_customer=$d['uid_akun'];
        //CEK SALDO TERAKHIR
        $d=pg_fetch_array(pg_query($conn,"SELECT id, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_customer' AND created_at<='$waktu' ORDER BY id DESC LIMIT 1"));

        $saldo = $d['saldo']-$jumlah_bayar;

        $sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_customer', '$waktu', '20', '$_POST[invoice_number]', '0', '$jumlah_bayar', '$saldo', '$_POST[uid_invoice]', 'invoice_header', '$_POST[uid_akun_terima]')";

        pg_query($conn,$sql);

        //LOOPING HITUNG ULANG LAGI
        /*
        $tampil=pg_query($conn,"SELECT id, debet, kredit, saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_customer' AND created_at>'$waktu' ORDER BY created_at DESC");
        while($r=pg_fetch_array($tampil)){
            $saldo_baru = $r['saldo']-$jumlah_bayar;
            
            //HITUNG ULANG SALDONYA
            pg_query($conn,"UPDATE keu_akun_log SET saldo='$saldo_baru' WHERE id='$r[id]'");
        }
        */

        pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo-$jumlah_bayar) WHERE uid_akun='$uid_akun_customer' AND created_at>'$waktu'");

        //UPDATE SALDO DI KEU AKUN
        //$c=pg_fetch_array(pg_query($conn,"SELECT saldo FROM keu_akun_log WHERE uid_akun='$uid_akun_customer' ORDER BY created_at DESC LIMIT 1"));
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini=(saldo_terkini-$jumlah_bayar) WHERE uid='$uid_akun_customer'");


        //PENCATATAN DI JURNAL	
        $sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu', '$_POST[uid_invoice]', 'Pelunasan Piutang', '1', '$_POST[invoice_number]') RETURNING id";
        $a=pg_fetch_array(pg_query($conn,$sql));
        $id_jurnal=$a['id'];

        //AKUN CUSTOMER SEBAGAI DEBET
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$_POST[uid_akun_terima]', '$jumlah_bayar')";
        pg_query($conn,$sql);

        //AKUN PENJUALAN SEBAGAI KREDIT
        $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_customer', '$jumlah_bayar')";
        pg_query($conn,$sql);

        if(!empty($_POST['check_list'])) {
			foreach($_POST['check_list'] as $check) {
                $uid_akun=$_POST["uid_akun_$check"];
                $keterangan=$_POST["keterangan_$check"];
                $jumlah=$_POST["jumlah_$check"];
                $jumlah=str_replace(".","",$jumlah);

                //CEK JENIS AKUN KEUANGAN
                $a=pg_fetch_array(pg_query($conn,"SELECT jenis_akun FROM keu_akun WHERE uid='$uid_akun'"));
                $jenis_akun=$a['jenis_akun'];

                if($jenis_akun=='D'){
                    $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                    pg_query($conn,$sql);
                }
                else{
                    $sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun', '$jumlah')";
                    pg_query($conn,$sql);
                }
            }
        }

        //EFEK PENULISAN PEMBYARAN KE INVOICE
        $d=pg_fetch_array(pg_query($conn,"SELECT total FROM invoice_header WHERE uid='$_POST[uid_invoice]'"));

        if($d['total']>$jumlah_bayar){
            $sisa=$d['total']-$jumlah_bayar;
            $sql="UPDATE invoice_header SET jumlah_terbayar='$jumlah_bayar', sisa_bayar='$sisa' WHERE uid='$_POST[uid_invoice]'";
        }
        else{
            $sql="UPDATE invoice_header SET jumlah_terbayar='$d[total]', sisa_bayar='0', is_lunas='Y' WHERE uid='$_POST[uid_invoice]'";
        }
        pg_query($conn,$sql);

        header("location: penjualan");

    }
	pg_close($conn);
}
?>