<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
error_reporting(0);
session_start();
include "timeout.php";
date_default_timezone_set("Asia/Jakarta");
if($_SESSION['login_finance']==1){
	if(!cek_login()){
		$_SESSION['login_finance'] = 0;
	}
}
if($_SESSION['login_finance']==0){
	header('location:logout.php');
}
else{
    include "konfig/koneksi.php";
    $pegawai=pg_fetch_array(pg_query($conn,"SELECT nama FROM pegawai WHERE uid='$_SESSION[login_user]'"));
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
<style>
.scroll {
  /* border: 1px solid black; */
  width: 200px;
  height: 300px;
  overflow: scroll;
}
</style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Finance SJTransindo</title>

    <!-- Prevent the demo from appearing in search engines -->
    <meta name="robots" content="noindex">

    <!-- Simplebar -->
    <link type="text/css" href="assets/vendor/simplebar.min.css" rel="stylesheet">

    <!-- App CSS -->
    <link type="text/css" href="assets/css/app.css?v1" rel="stylesheet">
    <link type="text/css" href="assets/css/app.rtl.css?v1" rel="stylesheet">

    <!--Material Datatable-->
    
    <link rel="stylesheet" href="assets/vendor/sweetalert/dist/sweetalert.css">
    <link type="text/css" href="assets/vendor/datatable/bootstrap.css" rel="stylesheet">
    <link type="text/css" href="assets/vendor/datatable/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link type="text/css" href="assets/css/vendor-material-icons.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-material-icons.rtl.css" rel="stylesheet">

    <!-- Font Awesome FREE Icons -->
    <link type="text/css" href="assets/css/vendor-fontawesome-free.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-fontawesome-free.rtl.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link type="text/css" href="assets/css/vendor-flatpickr.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-flatpickr.rtl.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-flatpickr-airbnb.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-flatpickr-airbnb.rtl.css" rel="stylesheet">

    <!-- Dropzone -->
    <link type="text/css" href="assets/css/vendor-dropzone.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-dropzone.rtl.css" rel="stylesheet">

    <!-- Select2 -->
    <link type="text/css" href="assets/css/vendor-select2.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-select2.rtl.css" rel="stylesheet">
    <link type="text/css" href="assets/vendor/select2/select2.min.css" rel="stylesheet">

    <link type="text/css" href="addons/css/custom.css?v1" rel="stylesheet">

    <link rel="shortcut icon" href="images/logo.png">

        <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
    <link href="assets/css/slidebars.css" rel="stylesheet">
    <link href="assets/css/invoice-print.css" rel="stylesheet" media="print">


    
    <!-- jQuery -->
    <script src="assets/vendor/jquery.min.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js" type="text/javascript"></script>
</head>

<body class="layout-default">
    <div class="preloader"></div>
    <!-- Header Layout -->
    <div class="mdk-header-layout js-mdk-header-layout">
        <!-- Header -->
        <?php include "header.php";?>
        <!-- // END Header -->

        <!-- Header Layout Content -->
        <div class="mdk-header-layout__content">

            <div class="mdk-drawer-layout js-mdk-drawer-layout" data-push data-responsive-width="992px">
                <div class="mdk-drawer-layout__content page">
                    <?php include "content.php";?>
                </div>
                <!-- // END drawer-layout__content -->
                
                <div class="mdk-drawer  js-mdk-drawer" id="default-drawer" data-align="start">
                    <?php 
                    include "nav_drawer.php";
                    ?>
                    
                </div>
            </div>
            <!-- // END drawer-layout -->

        </div>
        <!-- // END header-layout__content -->

    </div>
    <!-- // END header-layout -->
    
    <div id="form-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-small-title" aria-hidden="true"></div>
    <div id="form-modal2" class="modal fade animate" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="true"></div>

    <!-- Bootstrap -->
    <script src="assets/vendor/popper.min.js"></script>
    <script src="assets/vendor/bootstrap.min.js"></script>

    <!-- Simplebar -->
    <script src="assets/vendor/simplebar.min.js"></script>

    <!-- DOM Factory -->
    <script src="assets/vendor/dom-factory.js"></script>

    <!-- MDK -->
    <script src="assets/vendor/material-design-kit.js"></script>

    <!-- Sweet Alert-->
    <script src="assets/vendor/sweetalert2.all.min.js"></script>

    <!-- Datatable-->
    <script src="assets/vendor/datatable/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatable/dataTables.bootstrap4.min.js"></script>
    <!-- App -->
    <script src="assets/js/toggle-check-all.js"></script>
    <script src="assets/js/check-selected-row.js"></script>
    <script src="assets/js/dropdown.js"></script>
    <script src="assets/js/sidebar-mini.js"></script>
    <script src="assets/js/app.js"></script>


    <!-- js placed at the end of the document so the pages load faster -->
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/respond.min.js" ></script>

  <!--right slidebar-->
  <script src="assets/js/slidebars.min.js"></script>

    <!--common script for all pages-->
    <script src="assets/js/common-scripts.js"></script>


<!-- swallert -->
    <script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>

    <!-- Flatpickr -->
    <script src="assets/vendor/flatpickr/flatpickr.min.js"></script>
    <script src="assets/js/flatpickr.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="assets/vendor/jquery.mask.min.js"></script>

    <!-- Dropzone -->
    <script src="assets/vendor/dropzone.min.js"></script>
    <script src="assets/js/dropzone.js"></script>

    <!-- Select2 -->
    <script src="assets/vendor/select2/select2.min.js"></script>
    <!-- <script src="assets/js/select2.js"></script> -->
    <script>
         $(document).ready(function() {
             $(".select2").select2();
         });
     </script>
    <script>
         $(document).ready(function() {
             $(".sel2").select2();
         });
     </script>
    <!-- Masking-->
    <script src="assets/vendor/masking/jquery.mask.js"></script>

    <script src="assets/vendor/jquery.number.js"></script>

    <!-- Global Settings -->
    <script src="assets/js/settings.js"></script>

    <script src="addons/js/datatable.js"></script>

    <script type="text/javascript">
        $('.panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
        });

        $('.panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
        });
    </script>
</body>

</html>
<?php
}
?>