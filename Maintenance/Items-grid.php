<?php
/* Database connection start */
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$company = $_SESSION['companyid'];

$columns = array( 
// datatable column index  => database column name
	0 => 'part_no', 
	1 => 'description',
	2 => 'main_uom',
	3 => 'status'
);

// getting total number records without any search
$sql = "SELECT cpartno, citemdesc, cunit, cstatus ";
$sql.= "FROM items Where compcode='$company'";
$query=mysqli_query($con, $sql) or die("items-grid.php: get items");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT cpartno, citemdesc, cunit, cstatus ";
	$sql.=" FROM items";
	$sql.=" WHERE compcode='$company' and cpartno LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR citemdesc LIKE '".$requestData['search']['value']."%' ";
	$query=mysqli_query($con, $sql) or die("items-grid.php: get items");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	//$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($con, $sql) or die("items-grid.php: get items"); // again run query with limit
	
} else {	

	$sql = "SELECT cpartno, citemdesc, cunit, cstatus ";
	$sql.= " FROM items";
//	$sql.= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($con, $sql) or die("items-grid.php: get items");
	
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array

                        

						if($row['cstatus']=="ACTIVE"){
						 	$varx = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['cpartno'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						}
						else{
							$varx = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['cpartno'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						}

                        


	$nestedData=array(); 

	$nestedData[] = "<a href=\"javascript:;\" onClick=\"editfrm('".$row["cpartno"]."')\"> ".$row["cpartno"]."</a>";
	$nestedData[] = $row["citemdesc"]."<div class=\"itmalert alert alert-danger nopadding\" id=\"itm".$row["cpartno"]."\" style=\"display: inline\";></div>";
	$nestedData[] = $row["cunit"];
	$nestedData[] = "<div id=\"itmstat". $row['cpartno'] ."\" >".$varx."</div>";
	
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
