<div class="container-fluid page__container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <h4 class="m-0">Dashboard</h4>
        </div>
    </div>
</div>


<div class="container-fluid page__container">
    <div class="alert alert-soft-warning d-flex align-items-center card-margin" role="alert">
        <i class="material-icons mr-3">error_outline</i>
        <div class="text-body"><strong>Hello <?php echo $pegawai['nama'];?>.</strong> Selamat datang di Sistem Informasi Keuangan PT. Sumatera Jaya Transindo.</div>
    </div>
    <div class="card-group">
        <div class="card card-body text-center">
            <div class="d-flex flex-row align-items-center">
                <div class="card-header__title m-0"> <i class="material-icons icon-muted icon-30pt">assessment</i> Invoice Lewat Jatuh Tempo</div>
                <?php
                    $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot, SUM(total) AS total FROM invoice_header WHERE CAST(lock_date AS DATE)<'$tgl_sekarang 00:00:00'")); 
                ?>
                <div class="text-amount ml-auto"><?php echo formatAngka($a['tot']);?> / <small class="font-weight-bold">Rp<?php echo formatAngka($a['total']);?></small></div>
            </div>
        </div>
        <div class="card card-body text-center">
            <div class="d-flex flex-row align-items-center">
                <div class="card-header__title m-0"><i class="material-icons icon-muted icon-30pt">shopping_basket</i> Invoice Pembelian Belum Dibayar</div>
                <?php
                $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(id) AS tot, SUM(total) AS total FROM inv_detail_pembelian WHERE jumlah_terbayar IS NULL")); 
                ?>
                <div class="text-amount ml-auto"><?php echo formatAngka($a['tot']);?> / <small class="font-weight-bold">Rp<?php echo formatAngka($a['total']);?></small></div>
            </div>
        </div>
    </div>
</div>