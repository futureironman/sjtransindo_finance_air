<div class="container-fluid page__heading-container">
    <div class="page__heading d-flex align-items-center">
        <div class="flex">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons icon-20pt">home</i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Log Login</li>
                </ol>
            </nav>
            <h4 class="m-0">Data Log Login</h4>
        </div>
    </div>
</div>

<div class="container-fluid page__container">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataku" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th width="50px">No.</th>
                            <th>Nama</th>
                            <th>Waktu Login</th>
                            <th>Waktu Logout</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function () {
		$('#dataku').DataTable( {
			orderCellsTop: true,
			fixedHeader: true,
			lengthChange: true,
			"scrollX": true,
			dom : 'Bfrtip',
			pageLength:10,
			scrollCollapse: true, 
			processing: true,
            serverSide: true,
            "oLanguage": {
                "oPaginate": {
                "sFirst": "First page", // This is the link to the first page
                "sPrevious": "<i class='fa fa-angle-left'></i>", // This is the link to the previous page
                "sNext": "<i class='fa fa-angle-right'></i>", // This is the link to the next page
                "sLast": "Last page" // This is the link to the last page
                }
            },
			ajax: "data-log-login"
		});
			
		
    });
</script>