<?php
include "../../konfig/fungsi_tanggal.php";
include "../../konfig/fungsi_angka.php";
include "../../konfig/fungsi_terbilang.php";

$d=pg_fetch_array(pg_query($conn,"SELECT a.uid, a.nomor AS nomor_transaksi, a.waktu, a.created_at, a.id_jenis, b.nomor AS nomor_akun_asal, b.nama AS nama_akun_asal, c.nomor AS nomor_akun_tujuan, c.nama AS nama_akun_tujuan, a.jumlah, a.keterangan FROM keu_akun_transaksi_lain a, keu_akun b, keu_akun c WHERE a.uid_akun_kas=b.uid AND a.uid_akun_lawan=c.uid AND a.uid='$_GET[id]'"));

$a=explode(" ",$d['waktu']);
$waktu = DateToIndo3($a[0]).' '.$a[1];

$a=pg_fetch_array(pg_query($conn,"SELECT nama FROM keu_akun_transaksi_lain_jenis WHERE id='$d[id_jenis]'"));
$nama_transaksi=$a['nama'];
?>
<html>
	<head>
		<title>Cetak <?php echo $d['nomor_transaksi'];?></title>
		<style type='text/css'>
			@page {
                size: 8in 5in;
                padding:0.2mm;
                /* margin: 1mm 1mm 1mm 1mm;  */
            }
								  
			body{
				font-family:Tahoma;
				font-size:0.8rem;
                line-height: 1rem;
			}
		
			.text-left{
				text-align:left;
			}
			.text-center{
				text-align:center;
			}
			
			.text-right{
				text-align:right;
			}
			
			table{
				border-collapse:collapse;
				font-size:0.8rem;
			}

            .width-33{
                width:32%;
                float:left;
                margin-bottom: 5px;
                margin-right:5px;
                padding:2px;
            }

            .width-67{
                width:65%;
                float:left;
                margin-bottom: 5px;
                margin-right:5px;
                padding:2px;
            }

            .width-50{
                width:48%;
                float:left;
                text-align: center;
                font-weight: bold;
                margin-bottom: 5px;
                margin-right:5px;
                padding:2px;
            }

            .width-100{
                width:100%;
                text-align: center;
                font-weight: bold;
                margin-bottom: 5px;
                margin-right:5px;
                padding:2px;
            }
            .clear{
                clear:both;
            }

            .border-div{
                border:1px dashed #F2F2F2;
            }

            h6{
                margin:5px 0 5px 0;
            }

            .img-fluid{
                max-width: 100%;
                height:auto;
            }

            .content-body{
                border-top:1px dashed #000;
                padding-top: 2rem;
            }

            h3, h4, h5{
                margin:0;
            }

            .float-left{
                float:left;
            }

            .width-20{
                float:left;
                width:20%;
            }
		</style>
		<script>
		function myFunction() {
			window.print();
		    setTimeout(window.close, 0);
		}
		</script>
	</head>
	<body onload="myFunction()">
        <div class="width-67">
            <img src="images/logo.png" style="max-height:60px; float:left; margin-right:20px">
            <h4>PT. SUMATERA JAYA TRANSINDO</h4><hr>
            <small>KOMP. CLBC, JL. BOULEVARD BARAT BLOK R.10 NO.02, MEDAN</small>
        </div>
        <div class="width-33 text-right">
            <h5><?php echo $nama_transaksi;?></h5>
            <?php echo $waktu;?><br>
            <b>#<?php echo $d['nomor_transaksi'];?></b>
        </div>
        <div class="clear"></div>
        <div class="content-body">
            <table>
                <tr style="height:30px"><td width="150px">TRANSFER DARI</td><td width="10px">:</td><td><?php echo "$d[nomor_akun_asal] - $d[nama_akun_asal]";?></td></tr>
                <tr style="height:30px"><td>SETOR KE-</td><td>:</td><td><?php echo "$d[nomor_akun_tujuan] - $d[nama_akun_tujuan]";?></td></tr>
                <tr style="height:30px"><td>JUMLAH</td><td>:</td><td><b>Rp<?php echo formatAngka($d['jumlah']);?></b></td></tr>
                <tr style="height:30px"><td>TERBILANG</td><td>:</td><td><i><?php echo penyebut($d['jumlah']);?> rupiah</i></td></tr>
            </table>
        </div>
        <br><br>
        <div class="width-20 text-center">
            <p>Disetujui Oleh,</p><br><br>(_____________)
        </div>
        <div class="width-20 text-center">
        <p>Diperiksa Oleh,</p><br><br>(______________)
        </div>
        <div class="width-20 text-center">
            <p>Kasir,</p><br><br>(_____________)
        </div>
        <div class="width-20 text-center">
            <p>Diterima Oleh,</p><br><br>(_____________)
        </div>
        <div class="width-20 text-center">
            <p>Dibukukan Oleh,</p><br><br>(______________)
        </div>
	</body>
</html>