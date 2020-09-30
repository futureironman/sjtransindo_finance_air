function readURL(input) { // Mulai membaca inputan gambar
    var val = $('#fupload').val();

    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
        case 'jpg':
        case 'png':
        case 'jpeg':
        case 'JPG':
        case 'PNG':
        case 'JPEG':
            var size = parseFloat($("#fupload")[0].files[0].size / 5000).toFixed(2);
            if (size <= 5000) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    var image = new Image();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                        image.onload = function() {
                            $('#preview_gambar')
                                .attr('src', e.target.result)
                                .width('200px');
                        }
                    }
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#fupload').val('');
                alert("Maksimal 5 MB ");
                return false;
            }
            break;
        default:
            $('#fupload').val('');
            alert("Invalid extension");
            $('#preview_gambar')
                .attr('src', '../images/default.jpg')
                .width('200px');
            return false;
            break;
    }
}

function readURL2(input) { // Mulai membaca inputan gambar
    var val = $('#fupload2').val();

    switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
        case 'jpg':
        case 'png':
        case 'jpeg':
        case 'JPG':
        case 'PNG':
        case 'JPEG':
            var size = parseFloat($("#fupload2")[0].files[0].size / 5000).toFixed(2);
            if (size <= 5000) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    var image = new Image();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                        image.onload = function() {
                            $('#preview_gambar2')
                                .attr('src', e.target.result)
                                .width('200px');
                        }
                    }
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#fupload2').val('');
                alert("Maksimal 5 MB ");
                return false;
            }
            break;
        default:
            $('#fupload2').val('');
            alert("Invalid extension");
            $('#preview_gambar2')
                .attr('src', '../images/default.jpg')
                .width('200px');
            return false;
            break;
    }
}