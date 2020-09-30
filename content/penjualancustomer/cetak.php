<?php
session_start();
include "../../konfig/fungsi_tanggal.php";
include "../../konfig/fungsi_angka.php";
include "../../konfig/koneksi.php";

$tanggal_awal=$_GET['tanggal_awal'];
$tanggal_akhir=$_GET['tanggal_akhir'];
$uid_customer=$_GET['uid_customer'];

$tampil=pg_query($conn,"SELECT a.uid, a.po_house_number, b.lock_date, b.invoice_number, b.total, b.jumlah_terbayar, b.is_lunas, CAST(b.jatuh_tempo AS DATE) FROM po_house a, invoice_header b WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL AND b.id_category='$_SESSION[divisi]' AND a.uid=b.uid_data AND b.lock_date BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND b.total>0 AND a.uid_customer='$uid_customer' AND b.total>b.jumlah_terbayar AND b.is_lunas IS NULL ORDER BY b.invoice_number, b.lock_date ASC");

$a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$uid_customer'"));
$nama_customer=$a['nama'];

?>
<html>
	<head>
        <title>Data Invoice</title>
        <link type="text/css" href="assets/vendor/datatable/bootstrap.css" rel="stylesheet">
        
		<style type='text/css'>
			@page {
				size: A4;
			}
								  
			body{
				font-family:Tahoma;
				font-size:0.9rem;
                line-height: 1.5rem;
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
				font-size:0.9rem;
                width:100%;
			}
			
            table, th, td {
                border: 1px solid black;
                padding:5px;
            }
            /*
			thead tr th{
				border-top:1px dashed #000;
				border-bottom:1px dashed #000;
				font-weight:300;
				padding:5px 0;
			}
            */

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
        </style>
        <script src="assets/vendor/jquery.min.js"></script>
        <script src="assets/vendor/bootstrap.min.js"></script>
		<script>
		function myFunction() {
			window.print();
			setTimeout(window.close, 0);
		}
		</script>
	</head>
	<body onload="myFunction()">
        <div class="container-fluid">
            <img src="images/logo.png" class="img-fluid" style="max-height:60px; float:left">
            <h5 class="text-center">DATA TAGIHAN INVOICE<br><?php echo $nama_customer;?></h5>
            <div class="clear"></div>
            <hr>
            <table>
                <thead> 
                    <tr>
                        <th width="50px">No.</th>
                        <th>Tgl/Jam</th>
                        <th>Jatuh Tempo</th>
                        <th>PO House</th>
                        <th>Nomor Invoice</th>
                        <th>Total</th>
                        <th>Jumlah Terbayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    $grand_total=0;
                    $grand_total_bayar=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=explode(" ",$r['lock_date']);
                        $waktu=DateToIndo2($a[0]).' '.$a[1];

                        $a=explode(" ",$r['jatuh_tempo']);
                        $jatuh_tempo = DateToIndo3($a[0]);
                        ?>
                        <tr>
                            <td><?php echo $no;?></td>
                            <td><?php echo $waktu;?></td>
                            <td><?php echo $jatuh_tempo;?></td>
                            <td><?php echo $r['po_house_number'];?></td>
                            <td><?php echo $r['invoice_number'];?></td>
                            <td class="text-right"><?php echo formatAngka($r['total']);?></td>
                            <td class="text-right"><?php echo formatAngka($r['jumlah_terbayar']);?></td>
                        </tr>
                        <?php
                        $no++;
                        $grand_total+=$r['total'];
                        $grand_total_bayar+=$r['jumlah_terbayar'];
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><b>TOTAL</b></td>
                        <td class="text-right"><?php echo formatAngka($grand_total);?></td>
                        <td class="text-right"><?php echo formatAngka($grand_total_bayar);?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
	</body>
</html>