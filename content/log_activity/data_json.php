<?php
    session_start();
	$requestData= $_REQUEST;
	 
	
    // getting total number records without any search
    $sql = "SELECT a.*, b.nama AS nama_pegawai, c.nama AS nama_modul FROM log_modul a, pegawai b, modul c WHERE a.uid_pegawai=b.uid  AND a.id_modul=c.id ";
    
    $query=pg_query($conn, $sql ) or die("Data not found..");
    $totalData = pg_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
     
	
  
    
    $query=pg_query($conn, $sql) or die("Data Not Found...");
    $totalFiltered = pg_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
     
	 
    $sql.=" ORDER BY id DESC LIMIT " . $requestData['length'] .
    " OFFSET " . $requestData['start'];
   
	$query=pg_query($conn, $sql) or die("Data Not Found...");
    
	
    
    $data = array();
    
	$no=1;
    while( $row=pg_fetch_array($query) ) {  // preparing an array
		$a=explode(" ",$row['waktu']);
		$waktu=DateToIndo2($a[0])." $a[1]";

        if($row['aksi']=='C'){
            $aksi="<span class='badge badge-info'>Create</span>";
        }
        else if($row['aksi']=='U'){
            $aksi="<span class='badge badge-warning'>Update</span>";
        }
        else if($row['aksi']=='D'){
            $aksi="<span class='badge badge-danger'>Delete</span>";
        }

        $nestedData=array(); 
		$nestedData[] = $no;
		$nestedData[] = $waktu;
		$nestedData[] = $row['nama_modul'];
        $nestedData[] = $aksi;
        $nestedData[] = $r['nama_pegawai'];
		
		//$nestedData[] = $sql;
        $data[] = $nestedData;
		$no++;
    }
   
 
    
    $json_data = array(
        "draw"            => intval($requestData['draw']  ),   //   for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
        "recordsTotal"    => intval( $totalData ),  // total number of records
        "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
        "data"            => $data   // total data array
    );
    
    echo json_encode($json_data);  // send data as json format
?>