$(document).ready(function() {
    var table = $('#example').DataTable({
        lengthChange: false,
        "oLanguage": {
            "oPaginate": {
                "sFirst": "First page", // This is the link to the first page
                "sPrevious": "<i class='fas fa-angle-left'></i>", // This is the link to the previous page
                "sNext": "<i class='fas fa-angle-right'></i>", // This is the link to the next page
                "sLast": "Last page" // This is the link to the last page
            }
        },
        buttons: ['copy', 'excel', 'pdf', 'colvis']
    });

    /*
    table.buttons().container()
        .appendTo( '#example_wrapper .col-md-6:eq(0)' );
    */
});