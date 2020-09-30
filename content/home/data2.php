<div class="container-fluid page__heading-container">
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
    <div class="row card-group-row">
        <div class="col-lg-3 col-md-3 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center red">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2 text-center">Jumlah Invoice Piutang Belum di Bayar</div><br>
                    <div class="tex-left">
                        <?php
                        $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM invoice_header WHERE sisa_bayar > 0 and deleted_at IS NULL")); 
                        $b=pg_fetch_array(pg_query($conn,"SELECT SUM(sisa_bayar) AS tot FROM invoice_header WHERE sisa_bayar > 0 and deleted_at IS NULL")); 
                        // echo  = formatAngka($c['tot']);
                        ?>
                        <div class="form-group">
                           <b> <span>Jumlah Invoice :</span>
                            <span><?= formatAngka($a['tot'])?></span>
                        </div>
                        <div class="form-group">
                            <span>Total Tagihan :</span>
                            <span><?= formatAngka($b['tot'])?></span></b>
                        </div>
                    </div>
                    <div class="text-sent">&nbsp;</div>
                </div> 
            </div>
        </div>
        <div class="col-lg-3 col-md-3 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2 text-center">Jumlah Invoice Piutang Sudah di Bayar</div><br>
                    <div class="text-left">
                    <?php
                        $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(uid) AS tot FROM invoice_header WHERE is_lunas='Y' and deleted_at IS NULL")); 
                        $b=pg_fetch_array(pg_query($conn,"SELECT SUM(jumlah_terbayar) AS tot FROM invoice_header WHERE is_lunas ='Y'  and deleted_at IS NULL")); 
                    ?>
                     <div class="form-group">
                           <b> <span>Jumlah Invoice :</span>
                            <span><?= formatAngka($a['total'])?></span>
                        </div>
                        <div class="form-group">
                            <span>Total Tagihan :</span>
                            <span><?= formatAngka($b['total'])?></span></b>
                        </div>
                    </div>
                    <div class="text-sent">&nbsp;</div>
                </div>
            </div>
        </div>
       
        <div class="col-lg-3 col-md-3 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-cente red">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2 text-center">Jumlah Invoice Pembelian Belum di Bayar</div><br>
                    <div class="text-left">
                    <?php
                        $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(id) AS total FROM inv_detail_pembelian WHERE jumlah_terbayar is null ")); 
                        $b=pg_fetch_array(pg_query($conn,"SELECT SUM(total) AS total FROM inv_detail_pembelian WHERE jumlah_terbayar  is null")); 
                    ?>
                     <div class="form-group">
                           <b> <span>Jumlah Invoice :</span>
                            <span><?= formatAngka($a['total'])?></span>
                        </div>
                        <div class="form-group">
                            <span>Total Tagihan :</span>
                            <span><?= formatAngka($b['total'])?></span></b>
                        </div>
                    </div>
                    <div class="text-sent">&nbsp;</div>
                </div>
            </div>
        </div>
       
        <div class="col-lg-3 col-md-3 card-group-row__col">
            <div class="card card-group-row__card card-body card-body-x-lg flex-row align-items-center">
                <div class="flex">
                    <div class="card-header__title text-muted mb-2 text-center">Jumlah Invoice Pembelian Sudah di Bayar</div><br>
                    <div class="text-left">
                    <?php
                        $a=pg_fetch_array(pg_query($conn,"SELECT COUNT(id) AS total FROM inv_detail_pembelian WHERE is_lunas ='Y' ")); 
                        $b=pg_fetch_array(pg_query($conn,"SELECT SUM(jumlah_terbayar) AS total FROM inv_detail_pembelian WHERE is_lunas='Y'")); 
                    ?>
                     <div class="form-group">
                           <b> <span >Jumlah Invoice :</span>
                            <span><?= formatAngka($a['total'])?></span>
                        </div>
                        <div class="form-group">
                            <span>Total Tagihan :</span>
                            <span><?= formatAngka($b['total'])?></span></b>
                        </div>
                    </div>
                    <div class="text-sent">&nbsp;</div>
                </div>
            </div>
        </div>
       
    </div>
    
</div>

<script src="assets/vendor/highcharts/highcharts.js"></script>
<script src="assets/vendor/highcharts/exporting.js"></script>
<script src="assets/vendor/highcharts/export-data.js"></script>
<script src="assets/vendor/highcharts/accessibility.js"></script>