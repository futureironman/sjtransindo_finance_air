<?php
    //session_start();
	$requestData= $_REQUEST;
	 
	
    // getting total number records without any search
    $sql = "SELECT a.*, b.nama AS nama_pegawai FROM log_login a, pegawai b WHERE a.uid_pegawai=b.uid ";
       
    $query=pg_query($conn, $sql ) or die("Data not found..");
    $totalData = pg_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
     
	
  
    
    $query=pg_query($conn, $sql) or die("Data Not Found...");
    $totalFiltered = pg_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
     
	 
    $sql.=" ORDER BY waktu_login DESC LIMIT " . $requestData['length'] .
    " OFFSET " . $requestData['start'];
   
	$query=pg_query($conn, $sql) or die("Data Not Found...");
    
	
    
    $data = array();
    
	$no=1;
    while( $row=pg_fetch_array($query) ) {  // preparing an array
		$a=explode(" ",$row['waktu_login']);
		$waktu_login=DateToIndo2($a[0])." $a[1]";

		$a=explode(" ",$row['waktu_logout']);
		$waktu_logout=DateToIndo2($a[0])." $a[1]";

        $nestedData=array(); 
		$nestedData[] = $no;
		$nestedData[] = $row['nama_pegawai'];
		$nestedData[] = $waktu_login;
		$nestedData[] = $waktu_logout;
		
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