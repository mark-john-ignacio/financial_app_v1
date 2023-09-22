<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
$company = $_SESSION['companyid'];


$column = array('a.ctranno', 'a.quotetype', 'CONCAT(a.ccode,"-",COALESCE(b.ctradename, b.cname))', 'ddate', 'CASE WHEN a.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN a.lcancelled=1 THEN "Cancelled" ELSE "" END');

$query = "select a.*,b.cname from quote a left join customers b on a.`compcode` = b.`compcode` and a.ccode=b.cempid and a.compcode=b.compcode where a.compcode='$company'";

if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
{
 $query .= "and (ctranno like '%".$_POST['searchByName']."%' OR ccode like '%".$_POST['searchByName']."%' OR cname like '%".$_POST['searchByName']."%')";
}

if(isset($_POST['order']))
{
 $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY ddate DESC ';
}

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();



$data = array();

foreach($result as $row)
{

	$queryChkex = "select * from so_t where compcode='".$company."' and creference = '".$row['ctranno']."'";
 	$statEx = $connect->prepare($queryChkex);
 	$statEx->execute();
 	$chexst = $statEx->rowCount();


 $sub_array = array();
 $sub_array[] = $row['ctranno']; 
 $sub_array[] = $row['ccode'];
 $sub_array[] = $row['cname'];
 $sub_array[] = $row['ddate'];
 $sub_array[] = $row['ngross'];
 $sub_array[] = $row['lapproved'];
 $sub_array[] = $row['lcancelled'];
 $sub_array[] = $chexst;
 $sub_array[] = ucfirst($row['quotetype']);
 $sub_array[] = $row['lsent'];
 $sub_array[] = $row['quotetype'];
 $sub_array[] = $row['lvoid'];
 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $company = $_SESSION['companyid'];
 $query = "select a.*,b.cname from quote a left join customers b on a.ccode=b.cempid and a.compcode=b.compcode where a.compcode='$company'";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 "draw"       =>  intval($_POST["draw"]),
 "recordsTotal"   =>  count_all_data($connect),
 "recordsFiltered"  =>  $number_filter_row,
 "data"       =>  $data
);

echo json_encode($output);

?>