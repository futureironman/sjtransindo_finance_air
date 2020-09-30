<?php
$module=$_GET['module'];

?>
<div class="mdk-drawer__content">
    <div class="sidebar sidebar-light sidebar-left simplebar" data-simplebar>
        <div class="sidebar-heading sidebar-m-t">Menu</div>
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php if($module=='home'){echo "active";}?>">
                <a class="sidebar-menu-button" href="home">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">dvr</i>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php if($module=='coa'){echo "active";}?>">
                <a class="sidebar-menu-button" href="coa">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">apps</i>
                    <span class="sidebar-menu-text">Daftar Akun</span>
                </a>
            </li>
            
            <li class="sidebar-menu-item <?php if($module=='penjualanexport' OR $module=='penjualancustomer' OR $module=='penjualanimport'){echo "active open";}?>">
                <a class="sidebar-menu-button" data-toggle="collapse" href="#penjualan">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">cloud_download</i>
                    <span class="sidebar-menu-text">Invoice Penjualan</span>
                    <span class="ml-auto sidebar-menu-toggle-icon"></span>
                </a>
                <ul class="sidebar-submenu collapse" id="penjualan">
                    <li class="sidebar-menu-item <?php if($module=='penjualanexport'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="penjualanexport">
                            <span class="sidebar-menu-text">Export</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='penjualanimport'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="penjualanimport">
                            <span class="sidebar-menu-text">Import</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='penjualancustomer'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="penjualancustomer">
                            <span class="sidebar-menu-text">Customer</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-item <?php if($module=='pembelianexport' OR $module=='pembeliansupplier' OR $module=='pembelianimport'){echo "active open";}?>">
                <a class="sidebar-menu-button" data-toggle="collapse" href="#pembelian">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">cloud_upload</i>
                    <span class="sidebar-menu-text">Invoice Pembelian</span>
                    <span class="ml-auto sidebar-menu-toggle-icon"></span>
                </a>
                <ul class="sidebar-submenu collapse" id="pembelian">
                    <li class="sidebar-menu-item <?php if($module=='pembelianexport'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pembelianexport">
                            <span class="sidebar-menu-text">Export</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='pembelianimport'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pembelianimport">
                            <span class="sidebar-menu-text">Import</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='pembeliansupplier'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pembeliansupplier">
                            <span class="sidebar-menu-text">Supplier</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-item <?php if($module=='terimabayar' OR $module=='kirimbayar' OR $module=='pindahinternal' OR $module=='pemasukanlain' OR $module=='pengeluaranlain' OR $module=='biaya' OR $module=='fakturkeluar' OR $module=='bukafaktur' OR $module=='ppsa' OR $module=='phsa' OR $module=='persediaan'){echo "active open";}?>">
                <a class="sidebar-menu-button" data-toggle="collapse" href="#transaksi">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">receipt</i>
                    <span class="sidebar-menu-text">Transaksi</span>
                    <span class="ml-auto sidebar-menu-toggle-icon"></span>
                </a>
                <ul class="sidebar-submenu collapse" id="transaksi">
                    <li class="sidebar-menu-item <?php if($module=='terimabayar'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="terimabayar">
                            <span class="sidebar-menu-text">Terima Pembayaran</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='kirimbayar'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="kirimbayar">
                            <span class="sidebar-menu-text">Kirim Pembayaran</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='pindahinternal'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pindahinternal">
                            <span class="sidebar-menu-text">Pemindahan Internal</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='pemasukanlain'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pemasukanlain">
                            <span class="sidebar-menu-text">Pemasukan Lain</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='pengeluaranlain'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="pengeluaranlain">
                            <span class="sidebar-menu-text">Pengeluaran Lain</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='biaya'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="biaya">
                            <span class="sidebar-menu-text">Biaya Lain-lain</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='bukafaktur'){echo "active open";}?>">
                        <a class="sidebar-menu-button" data-toggle="collapse" href="#bukafaktur">
                            <span class="sidebar-menu-text">Buka Faktur</span>
                            <span class="ml-auto sidebar-menu-toggle-icon"></span>
                        </a>
                        <ul class="sidebar-submenu collapse" id="bukafaktur">
                        <?php
                            include "config/koneksi.php";
                                $a =pg_query($conn, "SELECT * FROM keu_akun WHERE (uid_parent ='2e57b1b3-875c-fa51-5b39-1945eca33202' OR uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0') and deleted_at is NULL");
                                while($r=pg_fetch_array($a)){ ?>

                                <li class="sidebar-menu-item <?php if($module=='bukafaktur'){echo "active";}?>">
                                <a class="sidebar-menu-button" href="bukafaktur-<?= $r["uid"]?>">
                                    <span class="sidebar-menu-text"><?= $r["nama"]?> </span>
                                </a>
                            </li><?php } ?>
                        </ul>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='ppsa'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="ppsa">
                            <span class="sidebar-menu-text">Pelunasan Piutang Saldo Awal</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='phsa'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="phsa">
                            <span class="sidebar-menu-text">Pelunasan Hutang Saldo Awal</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='persediaan'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="persediaan">
                            <span class="sidebar-menu-text">Persediaan Barang</span>
                        </a>
                    </li>
                        
                </ul>
            </li>           

            
            <li class="sidebar-menu-item <?php if($module=='jurpen'){echo "active";}?>">
                <a class="sidebar-menu-button" href="jurpen">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">memory</i>
                    <span class="sidebar-menu-text">Jurnal Penyesuaian</span>
                </a>
            </li>

            <li class="sidebar-menu-item <?php if($module=='lapjurnalumum' OR $module=='lapbukubesar' OR $module=='lapbukubesarperiode' OR $module=='lapbukubesartahunan' OR $module=='lapneracasaldo' OR $module=='lapneraca' OR $module=='laplabarugi' OR $module=='lapsaldoakhir' OR $module=='lapsaldoperiode' OR $module=='lapsaldobulan' OR $module=='lapsaldotahun'){echo "active open";}?>">
                <a class="sidebar-menu-button" data-toggle="collapse" href="#laporan">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">description</i>
                    <span class="sidebar-menu-text">Laporan</span>
                    <span class="ml-auto sidebar-menu-toggle-icon"></span>
                </a>
                <ul class="sidebar-submenu collapse" id="laporan">
                    <li class="sidebar-menu-item <?php if($module=='lapneraca'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="lapneraca">
                            <span class="sidebar-menu-text">Neraca Keuangan</span>
                        </a>
                    </li>                    
                    <li class="sidebar-menu-item <?php if($module=='laplabarugi'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="laplabarugi">
                            <span class="sidebar-menu-text">Laba Rugi</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='laplabarugidetail'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="laplabarugidetail">
                            <span class="sidebar-menu-text">Laba Rugi Detail</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='lapjurnalumum'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="lapjurnalumum">
                            <span class="sidebar-menu-text">Jurnal Umum</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='lapbukubesar'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="lapbukubesar">
                            <span class="sidebar-menu-text">Buku Besar Bulanan</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='lapbukubesarperiode'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="lapbukubesarperiode">
                            <span class="sidebar-menu-text">Buku Besar Periode</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php if($module=='lapbukubesartahunan'){echo "active";}?>">
                        <a class="sidebar-menu-button" href="lapbukubesartahunan">
                            <span class="sidebar-menu-text">Buku Besar Tahunan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-item <?php if($module=='profile'){echo "active";}?>">
                <a class="sidebar-menu-button" href="profile">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">person</i>
                    <span class="sidebar-menu-text">Profile</span>
                </a>
            </li>
            
            <li class="sidebar-menu-item">
                <a class="sidebar-menu-button" href="keluar">
                    <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-sign-out-alt"></i>
                    <span class="sidebar-menu-text">Logout</span>
                </a>
            </li>
        </ul>
        
    </div>
</div>