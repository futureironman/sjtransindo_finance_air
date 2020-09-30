<?php
if(isset($_GET['message'])){
    if($_GET['message']=='input'){
        $message = "<div class='alert alert-success'><i class='material-icons ml-2 font-weight-bold'>check_circle</i>Data berhasil ditambahkan</div>";
    }
    else if($_GET['message']=='update'){
        $message = "<div class='alert alert-warning'><i class='material-icons ml-2 font-weight-bold'>check_circle</i> Data berhasil diperbaharui</div>";
    }
    else if($_GET['message']=='delete'){
        $message = "<div class='alert alert-danger'><i class='material-icons ml-2 font-weight-bold'>info_outline</i> Data berhasil dihapuskan</div>";
    }
    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            $("#message").html("<?php echo $message;?>");
            $("#message").fadeOut(3000); 
        });
    </script>
    <?php
}
?>