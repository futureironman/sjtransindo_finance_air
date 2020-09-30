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

<?php 
$a = pg_fetch_array(pg_query($conn, "SELECT * FROM keu_pembayaran WHERE uid='$_GET[uid]'")); ?>
<div class="container-fluid page__container">
    <form method="POST" action="aksi-bayar-penjualan">
        <div class="card card-form">
            <div class="row no-gutters">
                <div class="col-lg-4 card-body">
                    <p><strong class="headings-color">Data Customer</strong></p>
                    <p class="text-muted">* Wajib diisi</p>
                </div>
                <div class="col-lg-8 card-form__body card-body">
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Customer <span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="uid_customer" id="id_customer" class="form-control select2" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT uid, nama FROM customer WHERE id_divisi='$_SESSION[divisi]' ORDER BY nama");
                                while ($r = pg_fetch_array($tampil)) {
                                    if($r["uid"] == $a["uid_customer"]){
                                        echo "<option value='$r[uid]' selected>$r[nama]</option>";
                                    }
                                    else{
                                        echo "<option value='$r[uid]'>$r[nama]</option>";
                                    }
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
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered data_invoice" id="data_checkbox">
                        <thead class="text-center font-weight-bold bg-light">
                            <tr>
                                <th>Check</th>
                                <th>Tgl/Jam Jatuh Tempo</th>
                                <th>No. Invoice</th>
                                <th>PO House Number</th>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>Terbayar</th>
                                <th>Nilai Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="5">TOTAL TAGIHAN</th>
                                <th id="total_tagihan" class="number-style text-right" colspan="2"> 0</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="5">TOTAL PEMBAYARAN</th>
                                <th id="total_pembayaran" class="number-style text-right" colspan="3">0</th>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="5">SELISIH</th>
                                <th id="selisih" class="number-style text-right" colspan="3">
                                    <font class="red"> 0</font>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-form">
            <div class="row no-gutters">
                <div class="col-lg-4 card-body">
                    <p><strong class="headings-color">Penerimaan Pembayaran</strong></p>
                    <p class="text-muted">Mohon masukkan data dengan benar<br>* Wajib diisi</p>
                </div>
                <div class="col-lg-8 card-form__body card-body">
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Tanggal/Jam<span class="red">*</span></label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>">
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control" name="jam" value="<?= $jam?>">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Metode Pembayaran<span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="id_metode_bayar" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT * FROM keu_akun_payment_status");
                                while ($r = pg_fetch_array($tampil)) {
                                    if($r["id"] == $a["id_metode_bayar"]){
                                        echo "<option value='$r[id]' selected>$r[nama]</option>";
                                    }
                                    else{
                                        echo "<option value='$r[id]'>$r[nama]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Ke Akun Keuangan<span class="red">*</span></label>
                        <div class="col-md-8">
                            <select name="uid_akun_terima" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php
                                $tampil = pg_query($conn, "SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND a.jenis_akun='D' AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                                while ($r = pg_fetch_array($tampil)) {
                                    if($r["uid"] == $a["uid_akun_terima"]){
                                        echo "<option value='$r[uid]' selected>$r[nomor] - $r[nama]</option>";
                                    }
                                    else{
                                        echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="col-md-4 pt-2">Catatan</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="catatan" value="<?= $a["catatan"] ?>">
                        </div>
                    </div>
                    <!-- effect lebih bayar (DEBET) -->
                    <!-- <hr>
                    <div class="text-center"><b>EFEK LEBIH BAYAR</b></div>
                    <div class="row form-group">
                        <div class="col-12">
                            <button type="button" class="btn btn btn-primary btn-sm" id="btnBarisAkunDebet"><i class="fa fa-plus"></i>Tambah Akun</button>
                            <table class="table table-bordered table-striped" id="akun_debet">
                                <thead>
                                    <tr>
                                        <th width="50px">.</th>
                                        <th>Akun</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                        <th width="50px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="akun_debet">
                                    <?php
                                    $no = 1;
                                    ?>
                                    <tr id="<?php echo $no; ?>">
                                        <td><input type="checkbox" name="check_list[]" value="<?php echo $no; ?>" checked></td>
                                        <td>
                                            <select name="uid_akun_<?php echo $no; ?>" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <?php
                                                $tampil = pg_query($conn, "SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                                                while ($r = pg_fetch_array($tampil)) {
                                                    echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="keterangan_<?php echo $no; ?>"></td>
                                        <td><input type="text" id="jumlahDebet<?= $no ?>" class="form-control money text-right jumlahDebet" name="jumlah_<?php echo $no; ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm" onclick="btnHapusDebet(<?= $no ?>)"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped">
                                <tfoot>
                                    <tr>
                                        <th class="text-center" colspan="3">TOTAL EFEK LEBIH BAYAR</th>
                                        <th class="number-style text-right"><span id="totalDebet" class="font-weight-bold text-right" name=>0</span></th>
                                    </tr>

                                </tfoot>
                            </table>
                            <div id="error"></div>
                        </div>
                    </div> -->

                    <!-- effect Kurang Bayar bayar (KREDIT) -->
                    <hr>
                    <div class="text-center" id="judul"><b>EFEK SELISIH BAYAR</b></div>
                    <div class="row form-group">
                        <div class="col-12">
                            <button type="button" class="btn btn btn-primary btn-sm" id="btnBarisAkun"><i class="fa fa-plus"></i>Tambah Akun</button>
                            <table class="table table-bordered table-striped" id="akun_kredit">
                                <thead>
                                    <tr>
                                        <th width="50px">.</th>
                                        <th>Akun</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                        <th width="50px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="akun_kredit">
                                    <?php
                                    $no = 1;
                                    ?>
                                    <tr id="<?php echo $no; ?>">
                                        <td><input type="checkbox" name="check_list[]" value="<?php echo $no; ?>" checked></td>
                                        <td>
                                            <select name="uid_akun_<?php echo $no; ?>" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <?php
                                                $tampil = pg_query($conn, "SELECT a.uid, a.nama, a.nomor FROM keu_akun a WHERE a.deleted_at IS NULL AND a.uid_parent IS NOT NULL AND NOT EXISTS(SELECT NULL FROM keu_akun b WHERE b.uid_parent=a.uid) ORDER BY a.nomor");
                                                while ($r = pg_fetch_array($tampil)) {
                                                    echo "<option value='$r[uid]'>$r[nomor] - $r[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="keterangan_<?php echo $no; ?>"></td>
                                        <td><input type="text" id="jumlahKredit<?= $no ?>" class="form-control money text-right jumlahKredit" name="jumlah_<?php echo $no; ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm " onclick="btnHapusKredit(<?= $no ?>)"><i class="fa fa-trash"></i></button></td>
                                    </tr>
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
        <a href="penjualan" class="btn btn-danger btn-md"><i class="fa fa-ban"></i> Batal</a>
    </form>
</div>
<script type="text/javascript" src="addons/js/masking_form.js"></script>
<script type="text/javascript">
    // Tambah Baris Lebih Bayar
    // $("#btnBarisAkunDebet").click(function() {
    //     var id = $('#akun_debet tr:last').attr('id');
    //     if (id == undefined) {
    //         id = 0;
    //     }
    //     $.ajax({
    //         type: 'POST',
    //         data: {
    //             'id': id
    //         },
    //         url: 'tambah-akundebet-penjualan',
    //         success: function(msg) {
    //             $('#akun_debet tr:last').after(msg);
    //         }
    //     });
    // });
    // // -----------

    // // jumlah bayar lebih bayar

    // $('.jumlahDebet').keyup(function() {

    //     var selisih = $("#selisih").text();
    //     selisih = selisih.replace(/\./g, "")
    //     selisih = parseInt(selisih);
    //     var sum = 0;
    //     var kredit = 0;
    //     $('.jumlahDebet').each(function() {
    //         kredit = $(this).val().replace(".", "");
    //         kredit = parseInt(kredit.replace(/\./g, ''));
    //         sum += kredit;
    //     });
    //     $("#totalDebet").number(sum, 0, ',', '.');
    //     if (selisih > 0) {
    //         var total_selisih = number_format(selisih - sum);
    //     } else {
    //         var total_selisih = number_format(selisih + sum);

    //     }
    //     var result = total_selisih.fontcolor("red");
    //     document.getElementById("total_selisih").innerHTML = result;
    // });
    // // ---------------------------
    // // function hapus Lebih Bayar
    // function btnHapusDebet(a) {
    //     var total_selisih = $("#total_selisih").text();
    //     total_selisih = total_selisih.replace(/\./g, "")
    //     total_selisih = parseInt(total_selisih);

    //     var nilaiDebet = $("#jumlahDebet" + a).val();
    //     nilaiDebet = nilaiDebet.replace(/\./g, "")
    //     nilaiDebet = parseInt(nilaiDebet);

    //     var total_debet = $("#totalDebet").html();
    //     total_debet = total_debet.replace(/\./g, "")
    //     total_debet = parseInt(total_debet);
    //     var sum = total_debet - nilaiDebet;

    //     console.log(total_debet);
    //     var conf = confirm("Are you sure you want to delete ?");
    //     if (conf) {
    //         // total debet
    //         $("#totalDebet").number(sum, 0, ',', '.');

    //         // total selisih
    //         var total_selisih = number_format(total_selisih - nilaiDebet);
    //         var result = total_selisih.fontcolor("red");
    //         document.getElementById("total_selisih").innerHTML = result;

    //         $("#akun_debet").find("#jumlahDebet" + a).each(function() {
    //             $(this).closest("tr").remove();
    //         });
    //     }
    // }
    // ----------------

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
                for (var a = 0; a < response.length; a++) {
                    if (response) {
                        $(".data_invoice tbody").append("<tr>" +
                            "<td> <input type=\"checkbox\" class=\"get_value\" name=\"cricketer\" onclick =\"check(" + a + "," + response[a]["total"] + ")\" value=" + response[a]["sisa_bayar"] + " id=\"erman\"/></div></td>" +
                            "<td>" + response[a]["jatuh_tempo"] + "</td>" +
                            "<td>" + response[a]["invoice_number"] + "<input type=\"hidden\" class=\"form-control name=\"invoice_number"+ no +"\" value=\""+ response[a]["uid"] +" \"/></td>" +
                            "<td>" + response[a]["po_house_number"] + " <input type=\"hidden\" class=\"form-control name=\"uid_"+ no +"\" value=\""+ response[a]["uid"] +" \"/></td>" +
                            "<td>" + response[a]["nama_pegawai"] + "</td>" +
                            "<td>" + number_format(response[a]["total"]) + " <input type=\"hidden\" class=\"form-control name=\"sisa_bayar"+no+"\" value=\""+ response[a]["uid"] +" \"/></td>" +
                            "<td>" + number_format(response[a]["jumlah_terbayar"]) + "</td>" +
                            "<td><input type=\"text\" class=\"form-control uang text-right number\" id=\"number" + a + "\" name=\"number " + no +"\")\" onclick =\"nilai_pembayaran(" + a + "," + response[a]["total"] + ")\" disabled/></td>" );
                    }
                    no++;

                }
            }

        });
    });
    // onclick checkbox
    function check(a, b) {
        var number = $("#number" + a);
        var tagihan = $("#total_tagihan").text();
        tagihan = tagihan.replace(/\./g, "")
        var total_tagihan = parseInt(tagihan);
        if (number.val() == "") {
            // insert nilai number
            $("#number" + a).val(number_format(b));
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
    }
    // -----------------------

    function nilai_pembayaran(a, b) {
        var number = $("#number" + a);
        var tagihan = $("#total_tagihan").text();
        tagihan = tagihan.replace(/\./g, "")
        var total_tagihan = parseInt(tagihan);
        // keyup number
        $(document.body).on('keyup', function(e) {
            var nilai_pembayaran = $("#number" + a).val();
            nilai_pembayaran = nilai_pembayaran.replace(/\./g, "")
            nilai_pembayaran = parseInt(nilai_pembayaran);

            $("#number" + a).val(number_format(nilai_pembayaran));

            var hasil = total_tagihan - b;
            var hasil = hasil + nilai_pembayaran;

          
            //insert selish dan total selisih 
            var total_selisih = number_format(hasil - total_tagihan);
            var result = total_selisih.fontcolor("red");
            $("#total_pembayaran").html(number_format(hasil));
            document.getElementById("selisih").innerHTML = result;
            $(".total_pembayaran").val(parseInt(hasil));
        });
    }
</script>