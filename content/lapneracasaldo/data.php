<?php
if(isset($_GET['tanggal_awal'])){
    $tanggal_awal=$_GET['tanggal_awal'];
    $tanggal_akhir=$_GET['tanggal_akhir'];
    $uid_akun=$_GET['uid_akun'];
}
else{
    $tanggal_awal=date("$thn_sekarang-$bln_sekarang-01");
    $tanggal_akhir=$tgl_sekarang;
}
?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Neraca SAldo</li>
                </ol>
            </nav>
            <h4 class="m-0">Laporan Neraca Saldo</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form action="">
        <div class="card card-form d-flex flex-column flex-sm-row">
            <div class="card-form__body card-body-form-group flex">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label>Tanggal Awal</label>
                        <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal;?>">
                    </div>
                    <div class="col-md-4">
                        <label>Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir;?>">
                    </div>
                </div>
            </div>
            <button class="btn bg-white border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" title="Reload" data-toggle="tooltip" data-placement="top"><i class="material-icons text-primary">refresh</i></button>
            <button class="btn btn-danger border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="button" title="Cetak" data-toggle="tooltip" data-placement="top"><i class="material-icons">print</i></button>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width:100%">
                    <thead class="bg-light"> 
                        <tr>
                            <th>Tipe Akun</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>