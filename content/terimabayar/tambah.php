<?php

$tgl_awal=$thn_sekarang.'-'.$bln_sekarang.'-01 00:00:00';
$tanggal_akhir=$thn_sekarang.'-'.$bln_sekarang.'-31 23:59:59';
$d = pg_fetch_array(pg_query($conn, "SELECT MAX(no_bukti) as nomor FROM keu_pembayaran where id_divisi='$_SESSION[divisi]' and deleted_at is NULL "));


    $kode_before = substr($d['nomor'],0,7);
    $kode_now="PPL.$thn.";
    if($kode_before==$kode_now){
        $no_urut = (int) substr($d['nomor'],7,6);
        $no_urut++;
        $no_urut_baru = $kode_before.sprintf("%06s",$no_urut);
    }
    else{
        $no_urut_baru = $kode_now.sprintf("%06s",1);
    }

?>

<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item"><a href="terimabayar">Terima Pembayaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
            <h4 class="m-0">Tambah Terima Pembayaran</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <form method="POST" action="aksi-bayar-penjualan">
        <div class="card card-form">
            <div class="row no-gutters">
                <div class="col-lg-4 card-body">
                    <p><strong class="headings-color">No Bukti</strong></p>
                    
                    <h3 style="color: red !important; font-weight: bold;"><?php echo  $no_urut_baru ?></h3>
                </div>
                <div class="col-lg-8 card-form__body card-body">
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Customer <span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="uid_customer" id="id_customer" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT a.uid as uid, a.nama FROM customer a, keu_akun b WHERE cast(a.uid as UUID)=b.uid_data and a.id_divisi='$_SESSION[divisi]' ORDER BY a.nama");
                                while ($r = pg_fetch_array($tampil)) {
                                    echo "<option value='$r[uid]'>$r[nama]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="total_tagihan" class="total_tagihan">
        <input type="hidden" name="total_pembayaran" class="total_pembayaran">
        <!-- <input type="hidden" name="baru[]" class="baru"> -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive scroll">
                    <table class="table table-striped table-bordered data_invoice" id="data_checkbox">
                        <thead class="text-center font-weight-bold bg-light">
                            <tr>
                                <th>Check</th>
                                <th>Tgl/Jam Pembuatan Invoice</th>
                                <th>Tgl/Jam Jatuh Tempo</th>
                                <th>No. Invoice</th>
                                <th>PO House Number</th>
                                <th>Jumlah</th>
                                <th>Terbayar</th>
                                <th width="20%">Nilai Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <table class="table table-striped table-bordered ">
                       
                        <tfoot>
                            <tr>
                                <th class="text-center" >TOTAL TAGIHAN</th>
                                <th id="total_tagihan" class="number-style text-right"> 0</th>
                            </tr>
                            <tr>
                                <th class="text-center" >TOTAL PEMBAYARAN</th>
                                <th id="total_pembayaran" class="number-style text-right">0</th>
                            </tr>
                            <tr>
                                <th class="text-center" >SELISIH</th>
                                <th id="selisih" class="number-style text-right">
                                    <font class="red"> 0</font>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
            </div>
        </div>

        <div class="card card-form">
            <div class="row no-gutters">
                <div class="col-lg-4 card-body">
                    <p><strong class="headings-color">Penerimaan Pembayaran</strong></p>
                    <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
                    <div class="col-lg-12 card-body">
                        <p><h4><strong class="headings-color">Jumlah Deposit</strong></h4></p>
                        <input type="hidden" name ="deposit_value" id="deposit_value">
                        <h3 id="deposit" style="color: red !important; font-weight: bold;"></h3>
                    </div>
                </div>
                <div class="col-lg-8 card-form__body card-body">
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="tanggal" max="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control" name="jam" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Metode Pembayaran<span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="id_metode_bayar" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT * FROM keu_akun_payment_status");
                                while ($r = pg_fetch_array($tampil)) {
                                    echo "<option value='$r[id]'>$r[nama]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Ke Akun Keuangan<span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="uid_akun_terima" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT uid,nama,nomor FROM keu_akun  WHERE (uid_parent ='2e57b1b3-875c-fa51-5b39-1945eca33202' OR uid_parent='2da48470-cef5-2bca-0fb3-f85bf4bc58b0') and deleted_at is NULL");
                                while ($r = pg_fetch_array($tampil)) {
                                    echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Catatan</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="catatan">
                        </div>
                    </div>
                    <!-- effect lebih bayar (DEBET) -->
                    
                    <!-- effect Kurang Bayar bayar (KREDIT) -->
                    <hr>
                    <div class="text-center" id="judul"><b>EFEK SELISIH BAYAR</b></div><br>
<!--                     
                    <label class="label" >Jadikan Piutang Customer</label>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline1" name="radio" class="custom-control-input" value="1">
                        <label class="custom-control-label" for="customRadioInline1">Ya</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline2" name="radio" checked="checked"  class="custom-control-input" value="0">
                        <label class="custom-control-label" for="customRadioInline2" >Tidak</label>
                    </div><br><br> -->
                    <div class="row form-group">
                        <div class="col-12">
                            <button type="button" class="btn btn btn-primary btn-sm" id="btnBarisAkun"><i class="fa fa-plus"></i>Tambah Akun</button>
                            <table class="table table-bordered table-striped" id="akun_kredit">
                                <thead>
                                    <tr>
                                        <th  width="5%">.</th>
                                        <th  width="40%">Akun</th>
                                        <th width="20%">Keterangan</th>
                                        <th width="20%">Jumlah</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="akun_kredit">
                                    <?php
                                    $no = 1;
                                    ?>
                                    
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped">
                                <tfoot>
                                    <tr>
                                        <th class="text-center" id="total_judul" colspan="3">TOTAL EFEK </th>
                                        <th class="number-style text-right"><span id="totalKredit" class="font-weight-bold text-right" name=>0</span></th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" colspan="3">TOTAL SELISIH</th>
                                        <th id="total_selisih" class="number-style text-right">
                                            <font class="red"> 0</font>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div id="error"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-md" id="btnSimpanBayar"><i class="fa fa-save"></i> Simpan dan Bayar</button>
        <a href="terimabayar" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
    
    // Tambah Baris Lebih Bayar
    $("#btnBarisAkun").click(function() {
        var id = $('#akun_kredit tr:last').attr('id');
        if (id == undefined) {
            id = 0;
        }
        $.ajax({
            type: 'POST',
            data: {
                'id': id
            },
            url: 'tambah-akunkredit-penjualan',
            success: function(msg) {
                $('#akun_kredit tr:last').after(msg);
            }
        });
    });
    // -----------
    $('.jumlahKredit').keyup(function() {

        var selisih = $("#selisih").text();
        selisih = selisih.replace(/\./g, "")
        selisih = parseInt(selisih);
        var sum = 0;
        var kredit = 0;
        $('.jumlahKredit').each(function() {
            kredit = $(this).val().replace(".", "");
            kredit = parseInt(kredit.replace(/\./g, ''));
            sum += kredit;
        });
        $("#totalKredit").number(sum, 0, ',', '.');
        if(selisih > 0){
            var total_selisih = number_format(selisih - sum);
        }
        else{
            var total_selisih = number_format(selisih + sum);

        }
        var result = total_selisih.fontcolor("red");
        document.getElementById("total_selisih").innerHTML = result;
    });
    // ---------------------------
    // function hapus kurang Bayar
    function btnHapusKredit(a) {
        // selisih
        var selisih = $("#selisih").text();
        selisih = selisih.replace(/\./g, "")
        selisih = parseInt(selisih);
        // total selisih
        var total_selisih = $("#total_selisih").text();
        total_selisih = total_selisih.replace(/\./g, "")
        total_selisih = parseInt(total_selisih);
        // jumlah kredit
        var nilaiKredit = $("#jumlahKredit" + a).val();
        nilaiKredit = nilaiKredit.replace(/\./g, "")
        nilaiKredit = parseInt(nilaiKredit);
        // total kredit
        var total_kredit = $("#totalKredit").html();
        total_kredit = total_kredit.replace(/\./g, "")
        total_kredit = parseInt(total_kredit);
        var sum = total_kredit - nilaiKredit;

        var conf = confirm("Are you sure you want to delete ?");
        if (conf) {
            // inset nilai kredit
            $("#totalKredit").number(sum, 0, ',', '.');

            // insert total selisih
            if (selisih > 0) {
                var total_selisih = number_format(total_selisih + nilaiKredit);
            } else {
                var total_selisih = number_format(total_selisih - nilaiKredit);
            }
            var result = total_selisih.fontcolor("red");
            document.getElementById("total_selisih").innerHTML = result;


            $("#akun_kredit").find("#jumlahKredit" + a).each(function() {
                $(this).closest("tr").remove();
            });
        }
    }
    // ----------------


    // data transaksi detali
    var id_customer = $("#id_customer").val();

    function number_format(number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    Array.prototype.remove = function() {
        var what, a = arguments,
            L = a.length,
            ax;
        while (L && this.length) {
            what = a[--L];
            while ((ax = this.indexOf(what)) !== -1) {
                this.splice(ax, 1);
            }
        }
        return this;
    };


    // get data po master
    $("#id_customer").change(function() {
        var id_customer = $("#id_customer").val();
        $.ajax({
            url: "getPembayaran",
            type: "GET",
            data: 'id_customer=' + id_customer,
            success: function(response) {
                var response = JSON.parse(response);

                let no = 1;
                $(".data_invoice tbody tr").remove();
                for (var a = 0; a < response.data.length; a++) {
                    var tgl = new Date(response.data[a]["jatuh_tempo"]);
                    // var tanggal = tgl.substr(0, 5);

                    if (response.data) {
                        $(".data_invoice tbody").append("<tr>" +
                            "<td> <input type=\"checkbox\" class=\"get_value\" name='customer[]' onclick =\"check(" + a + "," + (response.data[a]["total"] - response.data[a]["jumlah_terbayar"]) + ")\" value=" + no + " id=\"erman\"/></div></td>" +
                            "<td>" + response.data[a]["lock_date"] + "</td>" +
                            "<td>" + response.data[a]["jatuh_tempo"] + "</td>" +
                            "<td>" + response.data[a]["invoice_number"] + "<input type=\"hidden\" class=\"form-control\" name='invoice_number_"+ no +"' value=\""+ response.data[a]["invoice_number"] +"\"/></td>" +
                            "<td>" + response.data[a]["po_house_number"] + " <input type=\"hidden\" class=\"form-control\" name=\"uid_"+ no +"\" value=\""+response.data[a]["uid_invoice_header"]+"\"/></td>" +
                            "<td>" + number_format(parseInt(response.data[a]["total"])) + "<input type=\"hidden\" class=\"form-control\" name=\"sisa_bayar_"+ no +"\" value=\""+ response.data[a]["sisa_bayar"] +" \"/></td>" +
                            "<td>" + number_format(response.data[a]["jumlah_terbayar"]) + "</td>" +
                            "<td><input type=\"text\" class=\"form-control uang text-right number\" id=\"number" + a + "\" name=\"number_"+ no +"\")\" onclick =\"nilai_pembayaran(" + a + "," + (response.data[a]["total"] - response.data[a]["jumlah_terbayar"]) + ")\" disabled/></td>" );
                    }
                    no++;

                
                }
                if(response.saldo == undefined){
                    $("#deposit_value").val(0);
                }else{
                    $("#deposit_value").val(response.saldo);
                }
                
                $("#deposit").html("Rp. " + number_format(response.saldo));

                $("#selisih").val(response.saldo);
                var result = number_format(response.saldo);
                result = result.fontcolor("red");
                document.getElementById("selisih").innerHTML = result;


            }

        });
    });
    // onclick checkbox
    function check(a, b) {
        var deposit = $("#deposit_value").val();
        var number = $("#number" + a);
        var tagihan = $("#total_tagihan").text();
        tagihan = tagihan.replace(/\./g, "")
        var total_tagihan = parseInt(tagihan);
        if (number.val() == "") {
            // insert nilai number
            $("#number" + a).val(number_format(parseInt(b)));
            $("#number" + a).prop("disabled", false);
            total_tagihan += parseInt(b);

        } else {

            $("#number" + a).val("");
            $("#number" + a).prop("disabled", true);

            total_tagihan -= parseInt(b);
        }
        
        $("#total_tagihan").html(number_format(total_tagihan));
        $("#total_pembayaran").html(number_format(total_tagihan));
        $(".total_pembayaran").val(parseInt(total_tagihan));
        $(".total_tagihan").val(parseInt(total_tagihan));

        
        var total_tagihan = parseInt($(".total_tagihan").val());
        var total_pembayaran = parseInt($(".total_pembayaran").val());
        $(".selisih").val(parseInt(total_tagihan-total_pembayaran + deposit));
        
        var result = number_format(total_tagihan - total_pembayaran + deposit);
                result = result.fontcolor("red");
                document.getElementById("selisih").innerHTML = result;
    }
    // -----------------------

    function nilai_pembayaran(a, b) {
        var number = $("#number" + a);
        var deposit = parseInt($("#deposit_value").val());
        var total_pembayaran = $(".total_pembayaran").val();
        var total_tagihan = $(".total_tagihan").val();
        console.log(total_pembayaran);
        // keyup number
        $(document.body).on('keyup', "#number" + a, function(e) {
            var nilai_pembayaran = $("#number" + a).val();
            nilai_pembayaran = nilai_pembayaran.replace(/\./g, "")
            nilai_pembayaran = parseInt(nilai_pembayaran);

            $("#number" + a).val(number_format(nilai_pembayaran));

            var hasil = total_pembayaran - b;
            var hasil = hasil + nilai_pembayaran;

          
            //insert selish dan total selisih 
            var total_selisih = number_format(hasil - total_tagihan + deposit);
            var result = total_selisih.fontcolor("red");
            $("#total_pembayaran").html(number_format(hasil));
            document.getElementById("selisih").innerHTML = result;
            $(".total_pembayaran").val(parseInt(hasil));
        });
    }
</script>