<?php
if(!isset($_SESSION)){
	session_start();
}

include('../../Connection/connection_string.php');

$column = array('A.ctranno', 'B.ctradename', 'A.ddate', 'A.dreceived', 'A.lapproved', 'A.lcancelled', 'A.ccode', 'B.nlimit', 'B.cname');

$query = "SELECT A.*, B.cname, B.ctradename, B.nlimit FROM `salesreturn` A LEFT JOIN `customers` B ON A.`compcode` = B.`compcode` and A.`ccode` = B.`cempid` where A.compcode='".$_SESSION['companyid']."' ";

if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
{
 $query .= "and B.ctradename like '%".$_POST['searchByName']."%' OR B.cname like '%".$_POST['searchByName']."%' OR A.ctranno like '%".$_POST['searchByName']."%'";
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

foreach($result as $row)
{
 $sub_array = array();
 $sub_array[] = $row['ctranno'];
 $sub_array[] = ($row['ctradename']=="" || $row['ctradename']==null) ? $row['cname'] : $row['ctradename'];
 $sub_array[] = $row['ddate'];
 $sub_array[] = $row['dreceived'];
 $sub_array[] = $row['lapproved'];
 $sub_array[] = $row['lcancelled'];
 $sub_array[] = $row['ccode'];
 $sub_array[] = $row['nlimit'];
 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM salesreturn where compcode='".$_SESSION['companyid']."'";
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