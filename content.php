<?php
include "konfig/fungsi_tanggal.php";
include "konfig/library.php";
include "konfig/myencrypt.php";
include "konfig/fungsi_angka.php";
$module=$_GET['module'];

if($module=='home'){
    include "content/home/data.php";
}

else if($module=='coa'){
    include "content/coa/data.php";
}

else if($module=='penjualanexport'){
    include "content/penjualanexport/data.php";
}

else if($module=='penjualanimport'){
    include "content/penjualanimport/data.php";
}

else if($module=='penjualancustomer'){
    include "content/penjualancustomer/data.php";
}

else if($module=='pembelianexport'){
    include "content/pembelianexport/data.php";
}

else if($module=='pembelianimport'){
    include "content/pembelianimport/data.php";
}

else if($module=='pembeliansupplier'){
    include "content/pembeliansupplier/data.php";
}

else if($module=='lapjurnalumum'){
    include "content/lapjurnalumum/data.php";
}

else if($module=='lapbukubesar'){
    include "content/lapbukubesar/data.php";
}

else if($module=='lapbukubesarperiode'){
    include "content/lapbukubesarperiode/data.php";
}

else if($module=='lapbukubesartahunan'){
    include "content/lapbukubesartahunan/data.php";
}

else if($module=='lapneracasaldo'){
    include "content/lapneracasaldo/data.php";
}

else if($module=='lapneraca'){
    include "content/lapneraca/data.php";
}

else if($module=='laplabarugi'){
    include "content/laplabarugi/data.php";
}

else if($module=='lapneracadetail'){
    include "content/lapneracadetail/data.php";
}

else if($module=='lapsaldoakhir'){
    include "content/lapsaldoakhir/data.php";
}

else if($module=='lapsaldoperiode'){
    include "content/lapsaldoperiode/data.php";
}

else if($module=='lapsaldobulan'){
    include "content/lapsaldobulan/data.php";
}

else if($module=='lapsaldotahun'){
    include "content/lapsaldotahun/data.php";
}

else if($module=='profile'){
    include "content/profile/data.php";
}

else if($module=='phsa'){
    include "content/phsa/data.php";
}
else if($module=='persediaan'){
    include "content/persediaan/data.php";
}

else if($module=='ppsa'){
    include "content/ppsa/data.php";
}

else if($module=='jurpen'){
    include "content/jurpen/data.php";
}

//AWAL MENU LOG
else if($module=='log_login'){
    include "content/log_login/data.php";
}
else if($module=='log_activity'){
    include "content/log_activity/data.php";
}
//AKHIR MENU LOG

else if($module=='terimabayar'){
    include "content/terimabayar/data.php";
}

else if($module=='kirimbayar'){
    include "content/kirimbayar/data.php";
}

else if($module=='pindahinternal'){
    include "content/pindahinternal/data.php";
}

else if($module=='pemasukanlain'){
    include "content/pemasukanlain/data.php";
}

else if($module=='pengeluaranlain'){
    include "content/pengeluaranlain/data.php";
}

else if($module=='biaya'){
    include "content/biaya/data.php";
}

else if($module=='fakturkeluar'){
    include "content/fakturkeluar/data.php";
}
else if($module=='bukafaktur'){
    include "content/bukafaktur/data.php";
}
?>