<?php
/* Database connection start */
include('../Connection/connection_string.php');

/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$columns = array( 
// datatable column index  => database column name
	0 => 'ctrancode', 
	1 => 'dmonth',
	2 => 'dyear',
	3 => 'cremarks',
	4 => 'ddatetime',
	5 => 'lcancelled',
	6 => 'lapproved',
	7 => 'cpreparedby'
);

// getting total number records without any search
$sql = "SELECT ctrancode, dmonth, dyear, cremarks, ddatetime, lcancelled, lapproved, cpreparedby ";
$sql.=" FROM adjustments ";
//echo $sql;
$query=mysqli_query($con, $sql) or die("Inv-grid.php: get adjustment");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT ctrancode, dmonth, dyear, cremarks, ddatetime, lcancelled, lapproved , cpreparedby";
$sql.=" FROM adjustments WHERE 1=1";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( ctrancode LIKE '".$requestData['search']['value']."%')";    
}
$query=mysqli_query($con, $sql) or die("Inv-grid.php: get adjustment");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($con, $sql) or die("Inv-grid.php: get adjustment");

$varx = "";
$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array

	if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
	$varx = "<a href=\"javascript:;\" onClick=\"trans('POST','".$row["ctrancode"]."')\">POST</a> | <a href=\"javascript:;\" onClick=\"trans('CANCEL','".$row["ctrancode"]."')\">CANCEL</a>";
	
	}
	else{
		if(intval($row['lcancelled'])==intval(1)){
			$varx = "Cancelled";
		}
		if(intval($row['lapproved'])==intval(1)){
			$varx = "Posted";
		}
	}	
	
	$monthName = date('F', mktime(0, 0, 0, $row["dmonth"], 10));
	
	$nestedData=array(); 

	$nestedData[] = "<a href=\"javascript:;\" onClick=\"editfrm('".$row["ctrancode"]."');\">".$row["ctrancode"]."</a>";
	$nestedData[] = $monthName;
	$nestedData[] = $row["dyear"];
	$nestedData[] = $row["cpreparedby"];
	$nestedData[] = $row["ddatetime"];
	$nestedData[] = $varx;
	
	$data[] = $nestedData;
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format

?>
