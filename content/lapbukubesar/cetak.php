<?php
error_reporting(0);
include "../../konfig/koneksi.php";
include "../../konfig/fungsi_tanggal.php";
include "../../konfig/fungsi_angka.php";
include "../../konfig/fungsi_terbilang.php";

$id_bulan=$_GET['id_bulan'];
$tahun=$_GET['tahun'];
$uid_akun=$_GET['uid_akun'];

$tanggal_awal=date("$tahun-$id_bulan-01");
$tanggal_akhir=date("Y-m-t",strtotime($tanggal_awal));

$a=pg_fetch_array(pg_query($conn,"SELECT nama FROM bulan WHERE id='$id_bulan'"));
$nama_bulan=$a['nama'];
$x=pg_fetch_array(pg_query($conn,"SELECT nomor, nama FROM keu_akun WHERE uid='$uid_akun'"));
?>
<html>
	<head>
		<title>Cetak Laporan Buku Besar Tahunan</title>
		<style type='text/css'>
			@page {
                size : A4;
                margin : 5mm;
			}
								  
			body{
				font-family:Tahoma;
				font-size:0.7rem;
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
				font-size:0.7rem;
			}

            .width-33{
                width:32%;
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

            .width-67{
                width:66%;
                float:left;
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

            .header{
                border-bottom:1px dashed #000;
                padding-bottom: 5px;
            }
            .content-body{
                padding-top: 1rem;
            }

            h3{
                margin:0;
            }

            table{
				border-collapse:collapse;
				font-size:0.9rem;
			}
            thead tr th{
				border:1px solid #000;
				font-weight:700;
				padding:3px;
                font-size:0.7rem;
                background:#F2F2F2;
			}

            tbody tr td{
				border:1px solid #000;
				padding:3px;
                font-size:0.7rem;
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
        <div class="header text-center">
            <h3>LAPORAN BUKU BESAR BULANAN</h3>
            <?php echo "$nama_bulan $tahun";?> <br>
            <?php echo $x['nomor'].' - '.$x['nama'];?>
        </div>
        <div class="content-body">
            <table class="table table-bordered table-hover" style="width:100%">
                <thead class="bg-light"> 
                    <tr>
                        <th width="50px">No.</th>
                        <th>Tanggal/Jam</th>
                        <th>No Bukti/ Nama</th>
                        <th>Referensi</th>
                        <th>Keterangan Detail</th>
                        <th width="80px">Debet</th>
                        <th width="80px">Kredit</th>
                        <th width="80px">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $x = pg_fetch_array(pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_log a, keu_akun_status b WHERE a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.uid_akun='$uid_akun' AND a.id_status=b.id and a.deleted_at is null ORDER BY created_at ASC LIMIT 1"));
                        if($x['created_at']!=''){
                            $a=explode(" ",$x['created_at']);
                            $tanggal_saldoawal = date('Y-m-d', strtotime("-1 day", strtotime($a[0])));
                        }
                        else{
                            $tanggal_saldoawal = date('Y-m-d', strtotime("-1 day", strtotime($tanggal_awal)));
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td><?php echo DateToIndo2($tanggal_saldoawal);?> 23:59:59</td>
                            <td class="font-weight-bold">Saldo Awal</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right font-weight-bold"><?php echo formatAngka($x['saldo']);?></td>
                        </tr>
                    <?php
                        
                    $no=1;
                    $tampil=pg_query($conn,"SELECT a.*, b.nama FROM keu_akun_log a, keu_akun_status b WHERE a.created_at BETWEEN '$tanggal_awal 00:00:00' AND '$tanggal_akhir 23:59:59' AND a.uid_akun='$uid_akun' AND a.id_status=b.id and a.deleted_at is null ORDER BY created_at ASC");
                    $total_debet=0;
                    $total_kredit=0;
                    $saldo=0;
                    while($r=pg_fetch_array($tampil)){
                        $a=explode(" ",$r['created_at']);
                        $waktu = DateToIndo2($a[0]).' '.$a[1];

                        if($r['uid_akun_efek']!=NULL){
                            $a=pg_fetch_array(pg_query($conn,"SELECT nomor, nama FROM keu_akun WHERE uid='$r[uid_akun_efek]'"));
                            $nomor_referensi="$a[nomor]";
                        }
                        else{
                            $nomor_referensi="";
                        }

                        if($r['tabel']=='keu_buka_faktur_detail' AND $r['kredit']!='0'){
                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(b.uid) AS tot FROM keu_buka_faktur a, keu_buka_faktur_detail b WHERE a.no_faktur=b.no_faktur AND a.uid='$r[id_data]'"));
                            $rowspan="$c[tot]";

                            $a=pg_fetch_array(pg_query($conn,"SELECT b.jumlah, b.uid_akun_keperluan, c.nomor, c.nama, a.nama AS nama_penerima, b.keterangan FROM keu_buka_faktur a, keu_buka_faktur_detail b, keu_akun c WHERE a.no_faktur=b.no_faktur AND b.uid_akun_keperluan=c.uid AND a.uid='$r[id_data]' ORDER BY b.created_at ASC LIMIT 1"));

                            $debet = 0;
                            $kredit = $a['jumlah'];
                            $saldo_detail = $saldo - $kredit;
                            
                            ?>
                            <tr>
                                <td rowspan="<?php echo $rowspan;?>"><?php echo $no;?></td>
                                <td rowspan="<?php echo $rowspan;?>"><?php echo $waktu;?></td>
                                <td rowspan="<?php echo $rowspan;?>">
                                    <?php echo $r['nama'];
                                    if($r['keterangan']!=''){
                                        echo"<br>&nbsp; &nbsp;<b>$r[keterangan]</b><br><i>&nbsp; &nbsp; &nbsp; &nbsp;$a[nama_penerima]</i>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $a['nomor'];?></td>
                                <td><?php echo $a['keterangan'];?></td>
                                <td class="text-right"><?php echo formatAngka($debet);?></td>
                                <td class="text-right"><?php echo formatAngka($kredit);?></td>
                                <td class="text-right"><?php echo formatAngka($saldo_detail);?></td>
                            </tr>
                            <?php
                            $data=pg_query($conn,"SELECT b.jumlah, b.uid_akun_keperluan, c.nomor, c.nama, b.keterangan FROM keu_buka_faktur a, keu_buka_faktur_detail b, keu_akun c WHERE a.no_faktur=b.no_faktur AND b.uid_akun_keperluan=c.uid AND a.uid='$r[id_data]' ORDER BY b.created_at ASC OFFSET 1");
                            while($d=pg_fetch_array($data)){
                                $debet = 0;
                                $kredit = $d['jumlah'];
                                $saldo_detail = $saldo_detail - $kredit;
                                ?>
                                <tr>
                                    <td><?php echo $d['nomor'];?></td>
                                    <td><?php echo $d['keterangan'];?></td>
                                    <td class="text-right"><?php echo formatAngka($debet);?></td>
                                    <td class="text-right"><?php echo formatAngka($kredit);?></td>
                                    <td class="text-right"><?php echo formatAngka($saldo_detail);?></td>
                                </tr>
                                <?php
                            }
                            $no++;
                        }
                        else{
                        ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $waktu;?></td>
                                <td>
                                    <?php echo $r['nama'];
                                    if($r['keterangan']!=''){
                                        echo"<br>&nbsp; &nbsp;<small>$r[keterangan]</small>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $nomor_referensi;?></td>
                                <td><?php echo $r['keterangan_detail'];?></td>
                                <td class="text-right"><?php echo formatAngka($r['debet']);?></td>
                                <td class="text-right"><?php echo formatAngka($r['kredit']);?></td>
                                <td class="text-right"><?php echo formatAngka($r['saldo']);?></td>
                            </tr>
                            <?php
                            $total_debet+=$r['debet'];
                            $total_kredit+=$r['kredit'];
                            $no++;
                        }
                        $saldo = $r['saldo'];
                    }
                    
                    ?>
                </tbody>
            </table>
        </div>
	</body>
</html>