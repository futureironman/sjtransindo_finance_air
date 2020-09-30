<?php
switch($_GET['act']){
default:
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NOMOR AKUN</th>
                            <th>NAMA AKUN</th>
                            <th>KATEGORI AKUN</th>
                            <th>DESKRIPSI</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tampil=pg_query($conn,"SELECT a.uid, a.nomor, a.nama, a.keterangan, a.linked_table, a.uid_data, b.nama AS nama_kategori FROM keu_akun a, keu_akun_jenis b WHERE a.id_divisi='$_SESSION[divisi]' AND a.deleted_at IS NULL AND a.uid_parent IS NULL AND a.id_jenis=b.id ORDER BY a.nomor ASC");
                        $no=1;
                        while($r=pg_fetch_array($tampil)){

                            //UNTUK CEK APAKAH ADA CHILD DARI AKUN INI
                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                            $ada=$c['tot'];

                            $e1=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r[uid]'"));
                            ?>
                            <tr>
                                <td><?php echo $r['nomor'];?></td>
                                <td><?php echo $r['nama'];?></td>
                                <td><?php echo $r['nama_kategori'];?></td>
                                <td><?php echo $r['keterangan'];?></td>
                                <td>

                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                
                <div id="accordion">
                    <?php
                    
                    while($r=pg_fetch_array($tampil)){
                        
                        
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm">
                                    <a class="collapsed card-link" data-toggle="collapse" href="#collapse_<?php echo $r['nomor'];?>"><?php echo $r['nomor'].' - '.$r['nama'];?></a>
                                </div>
                                <div class="col-sm-auto">
                                    <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                    <?php
                                    if($ada=='0' AND $e1['saldo']==''){
                                        ?>                                    
                                        <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>
                                        <?php  
                                    }
                                    if($e1['saldo']==''){
                                        ?>
                                        <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <a href="view-coa-<?php echo $r['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>
                                        <?php
                                    }
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div id="collapse_<?php echo $r['nomor'];?>" class="collapse" data-parent="#accordion">
                            <div class="card-body">
                                <?php
                                if($ada>0){
                                    $no++;
                                    ?>
                                    <div id="accordion_<?php echo $no;?>">
                                        <?php
                                        $tampil2=pg_query($conn,"SELECT uid, nomor, nama, keterangan, linked_table, uid_data FROM keu_akun WHERE uid_parent='$r[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                        while($r2=pg_fetch_array($tampil2)){
                                            
                                            //UNTUK CEK APAKAH AKUN INI MEMPUNYAI TURUNAN AKUN
                                            $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                            $ada=$c['tot'];

                                            $e2=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r2[uid]'"));
                                        ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-sm">
                                                        <a class="collapsed card-link" data-toggle="collapse" href="#collapse_<?php echo $r2['nomor'];?>"><?php echo $r2['nomor'].' - '.$r2['nama'];?></a>
                                                    </div>
                                                    <div class="col-sm-auto">
                                                        <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                                        <?php
                                                        if($ada=='0' AND $e2['saldo']==''){
                                                            ?>
                                                            <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>
                                                            <?php  
                                                        }
                                                        if($e2['saldo']==''){
                                                            ?>
                                                            <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r2['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                            <?php
                                                        }
                                                        else{
                                                            ?>
                                                            <a href="view-coa-<?php echo $r2['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>
                                                            <?php
                                                        }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapse_<?php echo $r2['nomor'];?>" class="collapse " data-parent="#accordion_<?php echo $no;?>">
                                                <div class="card-body">
                                                    <?php
                                                    if($ada>0){
                                                        $no++;
                                                        ?>
                                                        <div id="accordion_<?php echo $no;?>">
                                                            <?php
                                                            $tampil3=pg_query($conn,"SELECT uid, nomor, nama, jenis_akun, keterangan, linked_table, uid_data FROM keu_akun WHERE uid_parent='$r2[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                                            while($r3=pg_fetch_array($tampil3)){
                                                                
                                                                //UNTUK CEK APAKAH AKUN INI MEMPUNYAI TURUNAN ATAU TIDAK
                                                                $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE uid_parent='$r3[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                                                $ada=$c['tot'];

                                                                $e3=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r3[uid]'"));
                                                            ?>
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col-sm">
                                                                            <a class="collapsed card-link" data-toggle="collapse" href="#collapse_<?php echo $r3['nomor'];?>"><?php echo $r3['nomor'].' - '.$r3['nama'];?></a>
                                                                        </div>
                                                                        <div class="col-sm-auto">
                                                                            <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                                                            <?php
                                                                            if($ada=='0' AND $e3['saldo']==''){
                                                                                ?>
                                                                                <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>
                                                                                <?php  
                                                                            }
                                                                            if($e3['saldo']==''){
                                                                                ?>
                                                                                <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r3['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                                                <?php
                                                                            }
                                                                            else{
                                                                                ?>
                                                                                <a href="view-coa-<?php echo $r3['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="collapse_<?php echo $r3['nomor'];?>" class="collapse " data-parent="#accordion_<?php echo $no;?>">
                                                                    <div class="card-body">
                                                                        <?php
                                                                        if($ada>0){
                                                                            $no++;
                                                                            ?>
                                                                            <div id="accordion_<?php echo $no;?>">
                                                                                <?php
                                                                                $tampil4=pg_query($conn,"SELECT uid, nomor, nama, jenis_akun, keterangan, linked_table, uid_data FROM keu_akun WHERE uid_parent='$r3[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                                                                while($r4=pg_fetch_array($tampil4)){
                                                                                    //UNTUK CEK APAKAH AKUN INI MEMPUNYAI TURUNAN ATAU TIDAK
                                                                                    $c=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM keu_akun WHERE uid_parent='$r4[uid]' AND deleted_at IS NULL AND id_divisi='$_SESSION[divisi]'"));
                                                                                    $ada=$c['tot'];

                                                                                    $e=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r4[uid]'"));
                                                                                ?>
                                                                                <div class="card">
                                                                                    <div class="card-header">
                                                                                        <div class="row">
                                                                                            <div class="col-sm">
                                                                                                <a class="collapsed card-link" data-toggle="collapse" href="#collapse_<?php echo $r4['nomor'];?>"><?php echo $r4['nomor'].' - '.$r4['nama'];?></a>
                                                                                            </div>
                                                                                            <div class="col-sm-auto">
                                                                                                <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                                                                                <?php
                                                                                                if($ada=='0' AND $e['saldo']==''){
                                                                                                    ?>
                                                                                                    <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>
                                                                                                    <?php
                                                                                                }
                                                                                                if($e['saldo']==''){
                                                                                                    ?>
                                                                                                    <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r4['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                                                                    <?php
                                                                                                }
                                                                                                else{
                                                                                                    ?>
                                                                                                    <a href="view-coa-<?php echo $r4['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>
                                                                                                    <?php
                                                                                                }
                                                                                                ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div id="collapse_<?php echo $r4['nomor'];?>" class="collapse " data-parent="#accordion_<?php echo $no;?>">
                                                                                        <div class="card-body">
                                                                                        <?php
                                                                                            if($ada>0){
                                                                                                $no++;
                                                                                                ?>
                                                                                                <div id="accordion_<?php echo $no;?>">
                                                                                                    <?php
                                                                                                    $tampil5=pg_query($conn,"SELECT uid, nomor, nama, jenis_akun, keterangan, linked_table, uid_data FROM keu_akun WHERE uid_parent='$r4[uid]' AND deleted_at IS NULL ORDER BY nomor");
                                                                                                    while($r5=pg_fetch_array($tampil5)){
                                                                                                         $e=pg_fetch_array(pg_query($conn,"SELECT saldo, created_at FROM keu_akun_log WHERE id_status='1' AND uid_akun='$r5[uid]'"));
                                                                                                    ?>
                                                                                                    <div class="card">
                                                                                                        <div class="card-header">
                                                                                                            <div class="row">
                                                                                                                <div class="col-sm">
                                                                                                                    <a class="collapsed card-link" data-toggle="collapse" href="#collapse_<?php echo $r5['nomor'];?>"><?php echo $r5['nomor'].' - '.$r5['nama'];?></a>
                                                                                                                </div>
                                                                                                                <div class="col-sm-auto">
                                                                                                                    <a href="view-coa-<?php echo $r5['uid'];?>"><button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fa fa-search"></i></button></a>
                                                                                                                   
                                                                                                                    <button type="button" class="btn btn-warning btn-sm btnEdit" id="<?php echo $r5['uid'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                                                                                                    <?php
                                                                                                                   
                                                                                                                    if($e['saldo']==''){
                                                                                                                        ?>
                                                                                                                        <button type="button" class="btn btn-success btn-sm btnSetSaldo" id="<?php echo $r5['uid'];?>" data-toggle="tooltip" data-placement="top" title="Setting Saldo Awal"><i class="fa fa-cog"></i></button>

                                                                                                                        <button type="button" class="btn btn-primary btn-sm btnTambah2" id="<?php echo $r5['uid'];?>" data-toggle="tooltip" data-placement="top" title="Tambah Akun"><i class="fa fa-plus"></i></button>
                                                                                                                    <?php  
                                                                                                                    }
                                                                                                                    ?>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div id="collapse_<?php echo $r5['nomor'];?>" class="collapse " data-parent="#accordion_<?php echo $no;?>">
                                                                                                            <div class="card-body">
                                                                                                                <?php
                                                                                                                if($e['saldo']!=''){
                                                                                                                    $a=explode(" ",$e['created_at']);
                                                                                                                    $waktu=DateToIndo2($a[0]).' '.$a[1];
                                                                                                                    ?>
                                                                                                                    Saldo Awal pada tanggal <?php echo 
                                                                                                                    $waktu;?> : <b><?php echo formatAngka($e['saldo']);?></b>
                                                                                                                <?php
                                                                                                                }

                                                                                                                if($r5['linked_table']!=NULL){
                                                                                                                    if($r5['linked_table']=='customer'){
                                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$r5[uid_data]'"));
                                                                                                                        $status="customer : $a[nama]";
                                                                                                                    }
                                                                                                                    else if($r5['linked_table']=='pegawai'){
                                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$r5[uid_data]'"));
                                                                                                                        $status="pegawai : $a[nama]";
                                                                                                                    }
                                                                                                                    else if($r5['linked_table']=='master_supplier'){
                                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM master_supplier WHERE uid='$r5[uid_data]'"));
                                                                                                                        $status="supplier : $a[nama]";
                                                                                                                    }
                                                                                                                    ?>
                                                                                                                    <div class="alert alert-success py-1 mt-1 m-0">
                                                                                                                        <small>Sudah terhubung dengan akun <?php echo $status;?></small>
                                                                                                                    </div>
                                                                                                                    <?php
                                                                                                                }
                                                                                                                ?>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <?php
                                                                                                }
                                                                                                ?>
                                                                                                </div>
                                                                                                <?php 
                                                                                            }
                                                                                            else{
                                                                                                $a=explode(" ",$e['created_at']);
                                                                                                $waktu=DateToIndo2($a[0]).' '.$a[1];
                                                                                                ?>
                                                                                                Saldo Awal pada tanggal <?php echo 
                                                                                                $waktu;?> : <b><?php echo formatAngka($e['saldo']);?></b>
                                                                                            <?php
                                                                                                if($r4['linked_table']!=NULL){
                                                                                                    if($r4['linked_table']=='customer'){
                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$r4[uid_data]'"));
                                                                                                        $status="customer : $a[nama]";
                                                                                                    }
                                                                                                    else if($r4['linked_table']=='pegawai'){
                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$r4[uid_data]'"));
                                                                                                        $status="pegawai : $a[nama]";
                                                                                                    }
                                                                                                    else if($r4['linked_table']=='master_supplier'){
                                                                                                        $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM master_supplier WHERE uid='$r4[uid_data]'"));
                                                                                                        $status="supplier : $a[nama]";
                                                                                                    }
                                                                                                    ?>
                                                                                                    <div class="alert alert-success py-1 mt-1 m-0">
                                                                                                        <small>Sudah terhubung dengan akun <?php echo $status;?></small>
                                                                                                    </div>
                                                                                                    <?php
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                            </div>
                                                                            <?php 
                                                                        }
                                                                        else{
                                                                            $a=explode(" ",$e3['created_at']);
                                                                            $waktu=DateToIndo2($a[0]).' '.$a[1];
                                                                            ?>
                                                                            Saldo Awal pada tanggal <?php echo 
                                                                            $waktu;?> : <b><?php echo formatAngka($e3['saldo']);?></b>
                                                                        <?php
                                                                            if($r3['linked_table']!=NULL){
                                                                                if($r3['linked_table']=='customer'){
                                                                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$r3[uid_data]'"));
                                                                                    $status="customer : $a[nama]";
                                                                                }
                                                                                else if($r3['linked_table']=='pegawai'){
                                                                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$r3[uid_data]'"));
                                                                                    $status="pegawai : $a[nama]";
                                                                                }
                                                                                else if($r3['linked_table']=='master_supplier'){
                                                                                    $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM master_supplier WHERE uid='$r3[uid_data]'"));
                                                                                    $status="supplier : $a[nama]";
                                                                                }
                                                                                ?>
                                                                                <div class="alert alert-success py-1 mt-1 m-0">
                                                                                    <small>Sudah terhubung dengan akun <?php echo $status;?></small>
                                                                                </div>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                        </div>
                                                        <?php 
                                                    }
                                                    else{
                                                        $a=explode(" ",$e2['created_at']);
                                                        $waktu=DateToIndo2($a[0]).' '.$a[1];
                                                        ?>
                                                        Saldo Awal pada tanggal <?php echo 
                                                        $waktu;?> : <b><?php echo formatAngka($e2['saldo']);?></b>
                                                    <?php
                                                        if($r2['linked_table']!=NULL){
                                                            if($r2['linked_table']=='customer'){
                                                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$r2[uid_data]'"));
                                                                $status="customer : $a[nama]";
                                                            }
                                                            else if($r2['linked_table']=='pegawai'){
                                                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$r2[uid_data]'"));
                                                                $status="pegawai : $a[nama]";
                                                            }
                                                            else if($r2['linked_table']=='master_supplier'){
                                                                $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM master_supplier WHERE uid='$r2[uid_data]'"));
                                                                $status="supplier : $a[nama]";
                                                            }
                                                            ?>
                                                            <div class="alert alert-success py-1 mt-1 m-0">
                                                                <small>Sudah terhubung dengan akun <?php echo $status;?></small>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <?php 
                                }
                                else{
                                    $a=explode(" ",$e1['created_at']);
                                    $waktu=DateToIndo2($a[0]).' '.$a[1];
                                    ?>
                                    Saldo Awal pada tanggal <?php echo 
                                    $waktu;?> : <b><?php echo formatAngka($e1['saldo']);?></b>
                                <?php
                                    if($r['linked_table']!=NULL){
                                        if($r['linked_table']=='customer'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM customer WHERE uid='$r[uid_data]'"));
                                            $status="customer : $a[nama]";
                                        }
                                        else if($r['linked_table']=='pegawai'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$r[uid_data]'"));
                                            $status="pegawai : $a[nama]";
                                        }
                                        else if($r['linked_table']=='master_supplier'){
                                            $a=pg_fetch_array(pg_query($conn,"SELECT nama FROM master_supplier WHERE uid='$r[uid_data]'"));
                                            $status="supplier : $a[nama]";
                                        }
                                        ?>
                                        <div class="alert alert-success py-1 mt-1 m-0">
                                            <small>Sudah terhubung dengan akun <?php echo $status;?></small>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?> 
                </div>
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