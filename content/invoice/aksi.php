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
	if ($act=='sinkronpenjualanexport'){
		
		//AKUN PENJUALAN
		$uid_akun_pembelian="0709a09f-6317-6fbb-6541-670a30dc5657";
		//GET ALL CUSTOMER FINANCIAL ACCOUNT

		$tampil=pg_query($conn,"SELECT a.uid, b.uid AS uid_akun, b.jenis_akun FROM customer a, keu_akun b WHERE CAST(a.uid AS UUID)=b.uid_data AND b.linked_table='customer' AND b.deleted_at IS NULL ORDER BY a.nama");
		while($r=pg_fetch_array($tampil)){
			//CEK INVOICE DAN MASUKKAN KE DALAM PIUTANG CUSTOMER
			$data=pg_query($conn,"SELECT b.uid, b.lock_date, b.created_at, b.invoice_number, b.total, b.jumlah_terbayar, b.sisa_bayar FROM po_house a, invoice_header b, customer c WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND b.id_category='$_SESSION[divisi]' AND b.total>0 AND a.uid=b.uid_data AND a.uid_customer=c.uid AND c.uid='$r[uid]' AND b.lock_date BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log d WHERE d.uid_akun='$r[uid_akun]' AND CAST(d.id_data AS UUID)=b.uid AND d.tabel='invoice_header') ORDER BY b.created_at ASC, b.lock_date ASC");

			while($d=pg_fetch_array($data)){
				$x = explode(" ",$d['created_at']);
				$y = explode(" ",$d['lock_date']);
				$total = intval($d['total']);

				if($y['1']=='00:00:00'){
					$waktu_data = $y[0].' '.$x[1];
				}
				else{
					$waktu_data = $d['lock_date'];
				}
				
				//AKUN CUSTOMER
				//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$r[uid_akun]'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$r[uid_akun]' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));
				if($a['saldo_terkini']<0){
					$saldo_terkini_x = $a['saldo_terkini']*(-1);
					//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
					if($a['saldo_terkini']>=$d['sisa_bayar']){
						$sql="UPDATE invoice_header SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE uid='$d[uid]'";
					}
					else{
						$sql="UPDATE invoice_header SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE uid='$d[uid]'";
					}
					pg_query($conn,$sql);
					
				}
				$saldo = intval($a['saldo_terkini']+$total);
				
				
				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$r[uid_akun]', '$waktu_data', '15', '$d[invoice_number]', '$total', '0', '$saldo', '$d[uid]', 'invoice_header', '$uid_akun_pembelian')";

				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$r[uid_akun]' AND created_at>'$waktu_data'");


				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$r[uid_akun]'");


				//AKUN PENJUALAN
				//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));
				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '5', '$d[invoice_number]', '0', '$total', '$saldo', '$d[uid]', 'invoice_header', '$r[uid_akun]')";
				
				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


				//PENCATATAN DI JURNAL
				
				$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[uid]', 'Penjualan Air Freight', '1', '$d[invoice_number]')  RETURNING id";
				$a=pg_fetch_array(pg_query($conn,$sql));
				$id_jurnal=$a['id'];

				//AKUN CUSTOMER SEBAGAI DEBET
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$r[uid_akun]', '$total')";
				pg_query($conn,$sql);

				//AKUN PENJUALAN SEBAGAI KREDIT
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
				pg_query($conn,$sql);
			}
		}
		
		header("location: penjualanexport?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]");
	}

	else if ($act=='sinkronpenjualancustomer'){
		$uid_akun_pembelian="0709a09f-6317-6fbb-6541-670a30dc5657";

		$a=pg_fetch_array(pg_query($conn,"SELECT uid FROM keu_akun WHERE linked_table='customer' AND deleted_at IS NULL AND uid_data='$_GET[uid_customer]'"));
		$uid_akun=$a['uid'];

		//CEK INVOICE DAN MASUKKAN KE DALAM PIUTANG CUSTOMER
		$data=pg_query($conn,"SELECT b.uid, b.created_at, b.lock_date, b.created_at, b.invoice_number, b.total, b.jumlah_terbayar, b.sisa_bayar FROM po_house a, invoice_header b, customer c WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND b.id_category='$_SESSION[divisi]' AND b.total>0 AND a.uid=b.uid_data AND a.uid_customer=c.uid AND c.uid='$_GET[uid_customer]' AND b.lock_date BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log d WHERE d.uid_akun='$uid_akun' AND CAST(d.id_data AS UUID)=b.uid AND d.tabel='invoice_header') ORDER BY b.created_at ASC, b.lock_date ASC");

		while($d=pg_fetch_array($data)){
			$x = explode(" ",$d['created_at']);
			$y = explode(" ",$d['lock_date']);

			$total = intval($d['total']);

			if($y['1']=='00:00:00'){
				$waktu_data = $y[0].' '.$x[1];
			}
			else{
				$waktu_data = $d['lock_date'];
			}
			//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
			//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini FROM keu_akun WHERE uid='$uid_akun'"));
			$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

			if($a['saldo_terkini']<0){
				$saldo_terkini_x = $a['saldo_terkini']*(-1);
				//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
				if($a['saldo_terkini']>=$d['sisa_bayar']){
					$sql="UPDATE invoice_header SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE uid='$d[uid]'";
				}
				else{
					$sql="UPDATE invoice_header SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE uid='$d[uid]'";
				}
				echo $sql.'<br>';
				pg_query($conn,$sql);
				
			}

			$saldo = intval($a['saldo_terkini']+$total);

						
			$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun', '$waktu_data', '15', '$d[invoice_number]', '$total', '0', '$saldo', '$d[uid]', 'invoice_header', '$uid_akun_pembelian')";

			pg_query($conn,$sql);

			pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun' AND created_at>'$waktu_data'");


			//UPDATE SALDO DI KEU AKUN
			pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun'");


			//AKUN PENJUALAN
			//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
			//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
			$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

			$saldo = intval($a['saldo_terkini']+$total);

			$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '5', '$d[invoice_number]', '0', '$total', '$saldo', '$d[uid]', 'invoice_header', '$uid_akun')";

			pg_query($conn,$sql);

			
			pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");
			//UPDATE SALDO DI KEU AKUN
			pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


			//PENCATATAN DI JURNAL	
			$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[uid]', 'Penjualan Air Freight', '1', '$d[invoice_number]') RETURNING id";
			$a=pg_fetch_array(pg_query($conn,$sql));
			$id_jurnal=$a['id'];

			//AKUN CUSTOMER SEBAGAI DEBET
			$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun', '$total')";
			pg_query($conn,$sql);

			//AKUN PENJUALAN SEBAGAI KREDIT
			$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
			pg_query($conn,$sql);
		}

		header("location: penjualancustomer?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]&uid_customer=$_GET[uid_customer]");
	}

	else if ($act=='sinkronpenjualanimport'){
		
		//AKUN PENJUALAN
		$uid_akun_pembelian="0709a09f-6317-6fbb-6541-670a30dc5657";
		//GET ALL CUSTOMER FINANCIAL ACCOUNT

		$tampil=pg_query($conn,"SELECT a.uid, b.uid AS uid_akun, b.jenis_akun FROM customer a, keu_akun b WHERE CAST(a.uid AS UUID)=b.uid_data AND b.linked_table='customer' AND b.deleted_at IS NULL ORDER BY a.nama");
		while($r=pg_fetch_array($tampil)){
			//CEK INVOICE DAN MASUKKAN KE DALAM PIUTANG CUSTOMER
			$data=pg_query($conn,"SELECT b.uid, b.created_at, b.lock_date, b.invoice_number, b.total, b.jumlah_terbayar, b.sisa_bayar FROM po_master a, invoice_header b, customer c WHERE a.import='true' AND a.deleted_at IS NULL AND b.deleted_at IS NULL AND b.id_category='$_SESSION[divisi]' AND b.total>0 AND a.uid=b.uid_data AND a.customer_import=CAST(c.uid AS uuid) AND c.uid='$r[uid]' AND b.lock_date BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log d WHERE d.uid_akun='$r[uid_akun]' AND CAST(d.id_data AS UUID)=b.uid AND d.tabel='invoice_header') ORDER BY b.created_at ASC, b.lock_date ASC");

			while($d=pg_fetch_array($data)){
				$total = intval($d['total']);
				$x = explode(" ",$d['created_at']);
				$y = explode(" ",$d['lock_date']);

				if($y['1']=='00:00:00'){
					$waktu_data = $y[0].' '.$x[1];
				}
				else{
					$waktu_data = $d['lock_date'];
				}
				
				
				//AKUN CUSTOMER
				//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$r[uid_akun]'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$r[uid_akun]' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

				if($a['saldo_terkini']<0){
					$saldo_terkini_x = $a['saldo_terkini']*(-1);
					//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
					if($a['saldo_terkini']>=$d['sisa_bayar']){
						$sql="UPDATE invoice_header SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE uid='$d[uid]'";
					}
					else{
						$sql="UPDATE invoice_header SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE uid='$d[uid]'";
					}
					pg_query($conn,$sql);
					
				}

				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$r[uid_akun]', '$waktu_data', '15', '$d[invoice_number]', '$total', '0', '$saldo', '$d[uid]', 'invoice_header', '$uid_akun_pembelian')";

				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$r[uid_akun]' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$r[uid_akun]'");


				//AKUN PENJUALAN
				//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));
				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '5', '$d[invoice_number]', '0', '$total', '$saldo', '$d[uid]', 'invoice_header', '$r[uid_akun]')";
				
				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


				//PENCATATAN DI JURNAL
				
				$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[uid]', 'Penjualan Air Freight', '1', '$d[invoice_number]')  RETURNING id";
				$a=pg_fetch_array(pg_query($conn,$sql));
				$id_jurnal=$a['id'];

				//AKUN CUSTOMER SEBAGAI DEBET
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$r[uid_akun]', '$total')";
				pg_query($conn,$sql);

				//AKUN PENJUALAN SEBAGAI KREDIT
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
				pg_query($conn,$sql);
			}
		}
		
		header("location: penjualanimport?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]");
	}

	else if ($act=='sinkronpembelianexport'){
		
		//AKUN HARGA POKOK PEMBELIAN
		$uid_akun_pembelian="9e3e2adc-c6d7-ff33-2dda-ecea464d48d8";
		//GET ALL SUPPLIER FINANCIAL ACCOUNT

		$tampil=pg_query($conn,"SELECT a.uid, b.uid AS uid_akun, b.jenis_akun FROM master_supplier a, keu_akun b WHERE CAST(a.uid AS UUID)=b.uid_data AND b.linked_table='master_supplier' AND b.deleted_at IS NULL ORDER BY a.nama");
		while($r=pg_fetch_array($tampil)){
			//CEK INVOICE DAN MASUKKAN KE DALAM HUTANG SUPPLIER
			$data=pg_query($conn,"SELECT c.id, b.invoice_number, c.total, c.jumlah_terbayar, c.sisa_bayar, CAST(c.tanggal_pembelian AS DATE), c.created_at, c.is_lunas, d.nama AS nama_supplier FROM po_master a, inv_header_pembelian b, inv_detail_pembelian c, master_supplier d WHERE a.deleted_at IS NULL AND a.uid_category='$_SESSION[divisi]' AND a.import='false' AND a.uid=b.uid_data AND CAST(b.uid AS uuid)=c.uid_inv_header AND c.uid_suplier=d.uid  AND d.uid='$r[uid]' AND CAST(c.tanggal_pembelian AS DATE) BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log e WHERE e.uid_akun='$r[uid_akun]' AND e.id_data=CAST(c.id AS CHARACTER VARYING) AND e.tabel='invoice_header') ORDER BY CAST(c.tanggal_pembelian AS DATE) ASC, c.created_at ASC");

			while($d=pg_fetch_array($data)){
				$x = explode(" ",$d['created_at']);
				$waktu_data = $d['tanggal_pembelian'].' '.$a[1];

				$total = intval($d['total']);
				//AKUN SUPPLIER
				//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$r[uid_akun]'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$r[uid_akun]' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

				$saldo = intval($a['saldo_terkini']+$total);

				if($a['saldo_terkini']<0){
					$saldo_terkini_x = $a['saldo_terkini']*(-1);
					//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
					if($a['saldo_terkini']>=$d['sisa_bayar']){
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE id='$d[uid]'";
					}
					else{
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE id='$d[id]'";
					}
					pg_query($conn,$sql);
					
				}
				
				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$r[uid_akun]', '$waktu_data', '10', '$d[invoice_number]', '0', '$total', '$saldo', '$d[id]', 'inv_detail_pembelian', '$uid_akun_pembelian')";

				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$r[uid_akun]' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$r[uid_akun]'");


				//AKUN HARGA POKOK PENJUALAN
				//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY id DESC LIMIT 1"));
				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '12', '$d[invoice_number]', '$total', '0', '$saldo', '$d[id]', 'inv_detail_pembelian', '$r[uid_akun]')";
				
				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


				//PENCATATAN DI JURNAL
				
				$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[id]', 'Pembelian Air Freight', '1', '$d[invoice_number]')  RETURNING id";
				$a=pg_fetch_array(pg_query($conn,$sql));
				$id_jurnal=$a['id'];

				//AKUN HPP SEBAGAI DEBET
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
				pg_query($conn,$sql);

				//AKUN SUPPLIER SEBAGAI KREDIT
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$r[uid_akun]', '$total')";
				pg_query($conn,$sql);
			}
			
		}
		
		header("location: pembelianexport?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]");
	}

	else if ($act=='sinkronpembelianimport'){
		
		//AKUN HARGA POKOK PEMBELIAN
		$uid_akun_pembelian="9e3e2adc-c6d7-ff33-2dda-ecea464d48d8";
		//GET ALL SUPPLIER FINANCIAL ACCOUNT

		$tampil=pg_query($conn,"SELECT a.uid, b.uid AS uid_akun, b.jenis_akun FROM master_supplier a, keu_akun b WHERE CAST(a.uid AS UUID)=b.uid_data AND b.linked_table='master_supplier' AND b.deleted_at IS NULL ORDER BY a.nama");
		while($r=pg_fetch_array($tampil)){
			//CEK INVOICE DAN MASUKKAN KE DALAM HUTANG SUPPLIER
			$data=pg_query($conn,"SELECT c.id, b.invoice_number, c.total, c.jumlah_terbayar, c.sisa_bayar, CAST(c.tanggal_pembelian AS DATE), c.created_at, c.is_lunas, d.nama AS nama_supplier FROM po_master a, inv_header_pembelian b, inv_detail_pembelian c, master_supplier d WHERE a.deleted_at IS NULL AND a.uid_category='$_SESSION[divisi]' AND a.import='true' AND a.uid=b.uid_data AND CAST(b.uid AS uuid)=c.uid_inv_header AND c.uid_suplier=d.uid  AND d.uid='$r[uid]' AND CAST(c.tanggal_pembelian AS DATE) BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log e WHERE e.uid_akun='$r[uid_akun]' AND e.id_data=CAST(c.id AS CHARACTER VARYING) AND e.tabel='invoice_header') ORDER BY CAST(c.tanggal_pembelian AS DATE) ASC, c.created_at ASC");

			while($d=pg_fetch_array($data)){
				$x = explode(" ",$d['created_at']);
				$waktu_data = $d['tanggal_pembelian'].' '.$a[1];

				$total = intval($d['total']);

				//AKUN SUPPLIER
				//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$r[uid_akun]'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$r[uid_akun]' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));
				$saldo = intval($a['saldo_terkini']+$total);

				if($a['saldo_terkini']<0){
					$saldo_terkini_x = $a['saldo_terkini']*(-1);
					//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
					if($a['saldo_terkini']>=$d['sisa_bayar']){
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE id='$d[uid]'";
					}
					else{
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE id='$d[id]'";
					}
					pg_query($conn,$sql);
					
				}
				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$r[uid_akun]', '$waktu_data', '10', '$d[invoice_number]', '0', '$total', '$saldo', '$d[id]', 'inv_detail_pembelian', '$uid_akun_pembelian')";

				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$r[uid_akun]' AND created_at>'$waktu_data'");


				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$r[uid_akun]'");


				//AKUN HARGA POKOK PENJUALAN
				//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '12', '$d[invoice_number]', '$total', '0', '$saldo', '$d[id]', 'inv_detail_pembelian', '$r[uid_akun]')";
				
				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");

				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


				//PENCATATAN DI JURNAL
				
				$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[id]', 'Pembelian Air Freight', '1', '$d[invoice_number]')  RETURNING id";
				$a=pg_fetch_array(pg_query($conn,$sql));
				$id_jurnal=$a['id'];

				//AKUN HPP SEBAGAI DEBET
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
				pg_query($conn,$sql);

				//AKUN SUPPLIER SEBAGAI KREDIT
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$r[uid_akun]', '$total')";
				pg_query($conn,$sql);
			}
			
		}
		
		header("location: pembelianimport?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]");
	}

	else if ($act=='sinkronpembeliansupplier'){
		
		//AKUN HARGA POKOK PEMBELIAN
		$uid_akun_pembelian="9e3e2adc-c6d7-ff33-2dda-ecea464d48d8";
		//GET ALL SUPPLIER FINANCIAL ACCOUNT

		$tampil=pg_query($conn,"SELECT a.uid, b.uid AS uid_akun, b.jenis_akun FROM master_supplier a, keu_akun b WHERE CAST(a.uid AS UUID)=b.uid_data AND b.linked_table='master_supplier' AND b.deleted_at IS NULL AND a.uid='$_GET[uid_supplier]' ORDER BY a.nama");
		while($r=pg_fetch_array($tampil)){
			//CEK INVOICE DAN MASUKKAN KE DALAM HUTANG SUPPLIER
			$data=pg_query($conn,"SELECT c.id, b.invoice_number, c.total, c.jumlah_terbayar, c.sisa_bayar, CAST(c.tanggal_pembelian AS DATE), c.created_at, c.is_lunas FROM po_master a, inv_header_pembelian b, inv_detail_pembelian c WHERE a.deleted_at IS NULL AND a.uid_category='$_SESSION[divisi]' AND a.import='false' AND a.uid=b.uid_data AND CAST(b.uid AS uuid)=c.uid_inv_header AND c.uid_suplier='$r[uid]' AND CAST(c.tanggal_pembelian AS DATE) BETWEEN '$_GET[tanggal_awal] 00:00:00' AND '$_GET[tanggal_akhir] 23:59:59' AND NOT EXISTS(SELECT NULL FROM keu_akun_log e WHERE e.uid_akun='$r[uid_akun]' AND e.id_data=CAST(c.id AS CHARACTER VARYING) AND e.tabel='invoice_header') ORDER BY CAST(c.tanggal_pembelian AS DATE) ASC, c.created_at ASC");

			while($d=pg_fetch_array($data)){
				$x = explode(" ",$d['created_at']);
				$waktu_data = $d['tanggal_pembelian'].' '.$x[1];

				$total = intval($d['total']);

				//AKUN SUPPLIER
				//GET LAST SALDO DARI LOG AKUN : KREDIT BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$r[uid_akun]'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$r[uid_akun]' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));

				if($a['saldo_terkini']<0){
					$saldo_terkini_x = $a['saldo_terkini']*(-1);
					//CEK APAKAH SALDO TERKINI YANG SISA NEGATIF LEBIH BESAR DARI PADA SISA BAYAR
					if($saldo_terkini_x>=$d['sisa_bayar']){
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar='$total', sisa_bayar='0', is_lunas='Y' WHERE id='$d[id]'";
					}
					else{
						$sql="UPDATE inv_detail_pembelian SET jumlah_terbayar=(jumlah_terbayar+$saldo_terkini_x), sisa_bayar=(sisa_bayar-$saldo_terkini_x) WHERE id='$d[id]'";
					}
					pg_query($conn,$sql);
					
				}

				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$r[uid_akun]', '$waktu_data', '10', '$d[invoice_number]', '0', '$total', '$saldo', '$d[id]', 'inv_detail_pembelian', '$uid_akun_pembelian')";

				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$r[uid_akun]' AND created_at>'$waktu_data'");
				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$r[uid_akun]'");


				//AKUN HARGA POKOK PENJUALAN
				//GET LAST SALDO DARI LOG AKUN : DEBET BERTAMBAH
				//$a=pg_fetch_array(pg_query($conn,"SELECT saldo_terkini, jenis_akun FROM keu_akun WHERE uid='$uid_akun_pembelian'"));
				$a=pg_fetch_array(pg_query($conn,"SELECT id, saldo AS saldo_terkini FROM keu_akun_log WHERE uid_akun='$uid_akun_pembelian' AND created_at<='$waktu_data' ORDER BY created_at DESC LIMIT 1"));
				$saldo = intval($a['saldo_terkini']+$total);

				$sql="INSERT INTO keu_akun_log (uid_akun, created_at, id_status, keterangan, debet, kredit, saldo, id_data, tabel, uid_akun_efek) VALUES ('$uid_akun_pembelian', '$waktu_data', '12', '$d[invoice_number]', '$total', '0', '$saldo', '$d[id]', 'inv_detail_pembelian', '$r[uid_akun]')";
				
				pg_query($conn,$sql);

				pg_query($conn,"UPDATE keu_akun_log SET saldo=(saldo+$total) WHERE uid_akun='$uid_akun_pembelian' AND created_at>'$waktu_data'");
				
				//UPDATE SALDO DI KEU AKUN
				pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$saldo' WHERE uid='$uid_akun_pembelian'");


				//PENCATATAN DI JURNAL
				
				$sql="INSERT INTO keu_akun_jurnal (waktu, uid_data, keterangan, id_jenis, no_bukti) VALUES ('$waktu_data', '$d[id]', 'Pembelian Air Freight', '1', '$d[invoice_number]')  RETURNING id";
				$a=pg_fetch_array(pg_query($conn,$sql));
				$id_jurnal=$a['id'];

				//AKUN HPP SEBAGAI DEBET
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, debet) VALUES ('$id_jurnal', '$uid_akun_pembelian', '$total')";
				pg_query($conn,$sql);

				//AKUN SUPPLIER SEBAGAI KREDIT
				$sql="INSERT INTO keu_akun_jurnal_detail (id_data, uid_akun, kredit) VALUES ('$id_jurnal', '$r[uid_akun]', '$total')";
				pg_query($conn,$sql);
			}
			
		}
		
		header("location: pembeliansupplier?tanggal_awal=$_GET[tanggal_awal]&tanggal_akhir=$_GET[tanggal_akhir]&uid_supplier=$_GET[uid_supplier]");
	}
}
?>