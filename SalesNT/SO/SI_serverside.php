<?php
if(!isset($_SESSION)){
	session_start();
}

include('../../Connection/connection_string.php');

$column = array('A.ctranno', 'B.cname', 'A.ddate', 'A.dcutdate', 'A.lapproved', 'A.lcancelled', 'A.ccode', 'B.nlimit');

$query = "SELECT * FROM `ntso` A LEFT JOIN `customers` B ON A.`compcode` = B.`compcode` and A.`ccode` = B.`cempid` where A.compcode='".$_SESSION['companyid']."' ";

if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
{
 $query .= "and B.cname like '%".$_POST['searchByName']."%' OR A.ctranno like '%".$_POST['searchByName']."%'";
}

if(isset($_POST['order']))
{
 $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY A.ddate DESC ';
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

//'A.ctranno', 'A.cdrprintno', 'B.ctradename', 'A.dcutdate', 'A.lapproved', 'A.lcancelled', 'A.ccode', 'B.nlimit'

foreach($result as $row)
{
 $sub_array = array();
 $sub_array[] = $row['ctranno'];
 $sub_array[] = $row['cname'];
 $sub_array[] = date_format(date_create($row['ddate']),"M d, Y H:i:s");
 $sub_array[] = date_format(date_create($row['dcutdate']),"M d, Y");
 $sub_array[] = number_format($row['ngross'],2);
 $sub_array[] = $row['lapproved'];
 $sub_array[] = $row['lcancelled'];
 $sub_array[] = $row['ccode'];
 $sub_array[] = $row['nlimit'];
 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM ntso where compcode='".$_SESSION['companyid']."'";
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