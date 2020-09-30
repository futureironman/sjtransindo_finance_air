<?php
switch($_GET['act']){
default:

//HITUNG ULANG SALDO
$tampil=pg_query($conn,"SELECT uid, saldo_terkini FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent IS NULL ORDER BY nomor");
while($r=pg_fetch_array($tampil)){
    $tampil2=pg_query($conn,"SELECT uid, saldo_terkini FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r[uid]' ORDER BY nomor");
    while($r2=pg_fetch_array($tampil2)){
        $tampil3=pg_query($conn,"SELECT uid, saldo_terkini FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r2[uid]' ORDER BY nomor");
        while($r3=pg_fetch_array($tampil3)){
            $tampil4=pg_query($conn,"SELECT uid, saldo_terkini FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r3[uid]' ORDER BY nomor");
            while($r4=pg_fetch_array($tampil4)){
                $c=pg_fetch_array(pg_query($conn,"SELECT SUM(saldo_terkini) AS saldo FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r4[uid]'"));
                pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$c[saldo]' WHERE uid='$r4[uid]'");
            }
            $c=pg_fetch_array(pg_query($conn,"SELECT SUM(saldo_terkini) AS saldo FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r3[uid]'"));
            pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$c[saldo]' WHERE uid='$r3[uid]'");
        }
        $c=pg_fetch_array(pg_query($conn,"SELECT SUM(saldo_terkini) AS saldo FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r2[uid]'"));
        pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$c[saldo]' WHERE uid='$r2[uid]'");
    }
    $c=pg_fetch_array(pg_query($conn,"SELECT SUM(saldo_terkini) AS saldo FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent='$r[uid]'"));
    pg_query($conn,"UPDATE keu_akun SET saldo_terkini='$c[saldo]' WHERE uid='$r[uid]'");
}
?>
<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daftar Akun</li>
                </ol>
            </nav>
            <h4 class="m-0">Data Daftar Akun</h4>
        </div>
        <button type="button" class="btn btn-info ml-3 btnTambah"><i class="fa fa-plus"></i> Tambah</button>
    </div>
</div>

<div class="container-fluid page__container">
    <?php
    $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
    if($c['tot']=='0'){
    ?>
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <img src="images/nodata.svg" class="img-fluid mt-3" style="max-width:200px">
                <p class="mt-4">
                    <b>Belum ada data akun</b><br>
                    Mulai terlebih dahulu dengan pembuatan daftar akun
                </p>
            </div>
        </div>
    <?php
    }
    else{
    ?>
        <div class="card">
            <div class="card-body">
                <div id="message"></div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>AKUN</th>
                            <th>KATEGORI AKUN</th>
                            <th>DESKRIPSI</th>
                            <th>SALDO</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tampil=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE id_divisi='$_SESSION[divisi]' AND deleted_at IS NULL AND uid_parent IS NULL ORDER BY nomor ASC");
                        $no=1;
                        while($r=pg_fetch_array($tampil)){

                            //UNTUK CEK APAKAH ADA CHILD DARI AKUN INI
                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                            $ada=$c['tot'];
                            
                            $e1=pg_fetch_array(pg_query($conn,"SELECT created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r[uid]'"));

                            if($r['jenis_akun']=='D'){
                                $kategori="<span class='badge badge-success'>DEBET</span>";
                            }
                            else{
                                $kategori="<span class='badge badge-danger'>KREDIT</span>";
                            }
                            $keterangan="";
                            if($r['linked_table']=='customer'){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r[linked_table] WHERE uid='$r[uid_data]'"));
                                $keterangan = "<small>Sudah terhubung ke akun customer : $a[nama]</small>";
                            }

                            else if($r['linked_table']=='pegawai'){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r[linked_table] WHERE CAST(uid AS VARCHAR)='$r[uid_data]'"));
                                $keterangan = "<small>Sudah terhubung ke akun pegawai : $a[nama]</small>";
                            }

                            else if($r['linked_table']=='master_supplier'){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r[linked_table] WHERE uid='$r[uid_data]'"));
                                $keterangan = "<small>Sudah terhubung ke akun supplier : $a[nama]</small>";
                            }

                            else if($r['linked_table']=='detail_barang'){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nama_barang AS nama FROM $r[linked_table] WHERE uid='$r[uid_data]'"));
                                $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                            }

                            else if($r['linked_table']=='asset'){
                                $a=pg_fetch_array(pg_query($conn,"SELECT nama_asset AS nama FROM $r[linked_table] WHERE uid='$r[uid_data]'"));
                                $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                            }
                            ?>
                            <tr>
                                <td <?php if($ada=='0' AND $e1['created_at']==''){echo "class='font-italic'";}?>><?php echo $r['nomor'].' - '.$r['nama'];?>
                                    <?php
                                    if($keterangan!=''){
                                        ?>
                                        <br><?php echo $keterangan;?></td>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td><?php echo $kategori;?></td>
                                <td><?php echo $r['keterangan'];?></td>
                                <td class="font-weight-bold text-right"><?php echo formatAngka($r['saldo_terkini']);?></td>
                                <td>
                                    <!--<a href="view-coa-<?php echo $r['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>-->
                                    <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                <?php
                                    if($ada=='0' AND $e1['saldo']==''){
                                        ?>                                    
                                        <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>

                                        <button type="button" class="btn btn-danger btn-sm btnHapus" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>
                                        <?php  
                                    }
                                    if($e1['saldo']==''){
                                        ?>
                                        <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            //TURUNAN KE-2
                            $tampil2=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun  WHERE uid_parent='$r[uid]' AND deleted_at IS NULL ORDER BY nomor");
                            while($r2=pg_fetch_array($tampil2)){
                                $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot, SUM(saldo_terkini) AS saldo FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                $ada=$c['tot'];

                                $e2=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r2[uid]'"));

                                if($r2['jenis_akun']=='D'){
                                    $kategori="<span class='badge badge-success'>DEBET</span>";
                                }
                                else{
                                    $kategori="<span class='badge badge-danger'>KREDIT</span>";
                                }

                                $keterangan="";
                                if($r2['linked_table']=='customer'){
                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r2[linked_table] WHERE uid='$r2[uid_data]'"));
                                    $keterangan = "<small>Sudah terhubung ke akun customer : $a[nama]</small>";
                                }
    
                                else if($r2['linked_table']=='pegawai'){
                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r2[linked_table] WHERE CAST(uid AS VARCHAR)='$r2[uid_data]'"));
                                    $keterangan = "<small>Sudah terhubung ke akun pegawai : $a[nama]</small>";
                                }
    
                                else if($r2['linked_table']=='master_supplier'){
                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r2[linked_table] WHERE uid='$r2[uid_data]'"));
                                    $keterangan = "<small>Sudah terhubung ke akun supplier : $a[nama]</small>";
                                }
                               
                                else if($r2['linked_table']=='detail_barang'){
                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama_barang AS nama FROM $r2[linked_table] WHERE uid='$r2[uid_data]'"));
                                    $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                }
                                else if($r2['linked_table']=='asset'){
                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama_asset AS nama FROM $r2[linked_table] WHERE uid='$r2[uid_data]'"));
                                    $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                }
                                ?>
                                <tr>
                                    <td <?php if($ada=='0' AND $e2['saldo']==''){echo "class='font-italic'";}?>>
                                        &nbsp; &nbsp; &nbsp; <?php echo $r2['nomor'].' - '.$r2['nama'];?>
                                        <?php
                                        if($keterangan!=''){
                                            ?>
                                            <br>&nbsp; &nbsp; &nbsp; <?php echo $keterangan;?></td>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $kategori;?></td>
                                    <td><?php echo $r2['keterangan'];?></td>
                                    <td class="font-weight-bold text-right"><?php echo formatAngka($r2['saldo_terkini']);?></td>
                                    <td>
                                        <!--<a href="view-coa-<?php echo $r2['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>-->
                                        <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                    <?php
                                        if($ada=='0' AND $e2['saldo']==''){
                                            ?>                                    
                                            <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>

                                            <button type="button" class="btn btn-danger btn-sm btnHapus" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>
                                            <?php  
                                        }
                                        if($e2['saldo']==''){
                                            ?>
                                            <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <?php

                                //TURUNAN KE-3
                                $tampil3=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                while($r3=pg_fetch_array($tampil3)){
                                    $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot, SUM(saldo_terkini) AS saldo FROM keu_akun WHERE uid_parent='$r3[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                    $ada=$c['tot'];

                                    
                                    $e3=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r3[uid]'"));


                                    if($r3['jenis_akun']=='D'){
                                        $kategori="<span class='badge badge-success'>DEBET</span>";
                                    }
                                    else{
                                        $kategori="<span class='badge badge-danger'>KREDIT</span>";
                                    }

                                    $keterangan="";
                                    if($r3['linked_table']=='customer'){
                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r3[linked_table] WHERE uid='$r3[uid_data]'"));
                                        $keterangan = "<small>Sudah terhubung ke akun customer : $a[nama]</small>";
                                    }
        
                                    else if($r3['linked_table']=='pegawai'){
                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r3[linked_table] WHERE CAST(uid AS VARCHAR)='$r3[uid_data]'"));
                                        $keterangan = "<small>Sudah terhubung ke akun pegawai : $a[nama]</small>";
                                    }
        
                                    else if($r3['linked_table']=='master_supplier'){
                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r3[linked_table] WHERE uid='$r3[uid_data]'"));
                                        $keterangan = "<small>Sudah terhubung ke akun supplier : $a[nama]</small>";
                                    }

                                    else if($r3['linked_table']=='detail_barang'){
                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama_barang AS nama FROM $r3[linked_table] WHERE uid='$r3[uid_data]'"));
                                        $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                    }

                                    else if($r3['linked_table']=='asset'){
                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama_asset AS nama FROM $r3[linked_table] WHERE uid='$r3[uid_data]'"));
                                        $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                    }
                                    ?>
                                    <tr>
                                        <td <?php if($ada=='0' AND $e3['saldo']==''){echo "class='font-italic'";}?>>
                                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r3['nomor'].' - '.$r3['nama'];?>
                                            <?php
                                            if($keterangan!=''){
                                                ?>
                                                <br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $keterangan;?></td>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $kategori;?></td>
                                        <td><?php echo $r3['keterangan'];?></td>
                                        <td class="font-weight-bold text-right"><?php echo formatAngka($r3['saldo_terkini']);?></td>
                                        <td>
                                            <!--<a href="view-coa-<?php echo $r3['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>-->
                                            <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                        <?php
                                            if($ada=='0' AND $e3['saldo']==''){
                                                ?>                                    
                                                <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>

                                                <button type="button" class="btn btn-danger btn-sm btnHapus" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>
                                                <?php  
                                            }
                                            if($e3['saldo']==''){
                                                ?>
                                                <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php

                                    //TURUNAN KE-4
                                    $tampil4=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data, jenis_akun, saldo_terkini FROM keu_akun WHERE uid_parent='$r3[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                    while($r4=pg_fetch_array($tampil4)){
                                        $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot, SUM(saldo_terkini) AS saldo FROM keu_akun WHERE uid_parent='$r4[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                        $ada=$c['tot'];
                                        
                                        $e4=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r4[uid]'"));

                                        if($r4['jenis_akun']=='D'){
                                            $kategori="<span class='badge badge-success'>DEBET</span>";
                                        }
                                        else{
                                            $kategori="<span class='badge badge-danger'>KREDIT</span>";
                                        }

                                        $keterangan="";
                                        if($r4['linked_table']=='customer'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r4[linked_table] WHERE uid='$r4[uid_data]'"));
                                            $keterangan = "<small>Sudah terhubung ke akun customer : $a[nama]</small>";
                                        }
            
                                        else if($r4['linked_table']=='pegawai'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r4[linked_table] WHERE CAST(uid AS VARCHAR)='$r4[uid_data]'"));
                                            $keterangan = "<small>Sudah terhubung ke akun pegawai : $a[nama]</small>";
                                        }
            
                                        else if($r4['linked_table']=='master_supplier'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM $r4[linked_table] WHERE uid='$r4[uid_data]'"));
                                            $keterangan = "<small>Sudah terhubung ke akun supplier : $a[nama]</small>";
                                        }

                                        else if($r4['linked_table']=='detail_barang'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama_barang AS nama FROM $r4[linked_table] WHERE uid='$r4[uid_data]'"));
                                            $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                        }

                                        else if($r4['linked_table']=='asset'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama_asset AS nama FROM $r4[linked_table] WHERE uid='$r4[uid_data]'"));
                                            $keterangan = "<small>Sudah terhubung ke akun barang : $a[nama]</small>";
                                        }
                                        ?>
                                        <tr>
                                            <td <?php if($ada=='0' AND $e4['saldo']==''){echo "class='font-italic'";}?>>
                                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $r4['nomor'].' - '.$r4['nama'];?>
                                                <?php
                                                if($keterangan!=''){
                                                ?>
                                                <br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $keterangan;?></td>
                                                <?php
                                                }
                                                ?>
                                            <td><?php echo $kategori;?></td>
                                            <td><?php echo $r4['keterangan'];?></td>
                                            <td class="font-weight-bold text-right"><?php echo formatAngka($r4['saldo_terkini']);?></td>
                                            <td>
                                                <!--<a href="view-coa-<?php echo $r4['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>-->
                                                
                                                <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                            <?php
                                                if($ada=='0' AND $e4['saldo']==''){
                                                    ?>                                    
                                                    <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>

                                                    <button type="button" class="btn btn-danger btn-sm btnHapus" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fa fa-trash"></i></button>
                                                    <?php  
                                                }
                                                if($e4['saldo']==''){
                                                    ?>
                                                    <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
                
                
            </div>
        </div>
    <?php
    }
    ?>
</div>

<?php
include "content/alert.php";
?>

<script type="text/javascript" src="addons/js/coa.js"></script>
<?php
break;

case "view":
    include "view.php";
break;
}
?>