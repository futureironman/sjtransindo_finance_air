<?php
$d=pg_fetch_array(pg_query($conn,"SELECT * FROM pegawai WHERE uid='$_SESSION[login_user]'"));
$password=decrypt($d['password']);
$password2="";
for($i=0;$i<strlen($password);$i++){
	$password2.="*";
}

if($d['foto']!=''){
    $foto="images/pegawai/upload_$d[foto]";
}
else{
    $foto="images/default.jpg";
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
            <h4 class="m-0">Data Profile</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">DATA</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr><td width="150px">Nama</td><td width="10px">:</td><td><?php echo $d['nama'];?></td></tr>
                        <tr><td>Username</td><td>:</td><td><?php echo $d['username'];?></td></tr>
                        <tr><td>Password</td><td>:</td><td><?php echo $password2;?></td></tr>
                        <tr><td>Foto</td><td>:</td><td><img src="<?php echo $foto;?>" class="img-fluid img-thumbnail" width="150px"></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <form method="POST" action="aksi-edit-profile" enctype="multipart/form-data">
                    <div class="card-header">EDIT</div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-md-4 text-right pt-2">Nama</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="nama" value="<?php echo $d['nama'];?>" required disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-4 text-right pt-2">Username</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="username" value="<?php echo $d['username'];?>" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-4 text-right pt-2">Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" name="password" value="<?php echo $password;?>">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-4 text-right pt-2">Foto</label>
                            <div class="col-md-8">
                                <img id="preview_gambar" src="<?php echo $foto;?>" alt="" class="img-thumbnail" width="150px"/>
                                <input type="file" class="form-control" name="fupload" id="fupload" onChange="readURL(this);" accept="image/*">
                                
                                <input type="hidden" name="foto" value="<?php echo $d['foto'];?>" >
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-success"><i class="fa fa-save"></i> SIMPAN</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="addons/js/fungsi_display_image.js"></script>