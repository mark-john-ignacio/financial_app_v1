<?php
if(!isset($_SESSION)){
	session_start();
}

include('../../Connection/connection_string.php');

$column = array('A.ctranno', 'D.cref', 'B.ctradename', 'A.ddate', 'A.dreceived', 'CASE WHEN A.lapproved=1 THEN CASE WHEN a.lvoid=1 THEN "Voided" ELSE "Posted" END WHEN A.lcancelled=1 THEN "Cancelled" ELSE "" END');

$query = "SELECT A.*, B.cname, B.ctradename, B.nlimit, D.cref FROM `ntsalesreturn` A LEFT JOIN `customers` B ON A.`compcode` = B.`compcode` and A.`ccode` = B.`cempid` LEFT JOIN (Select x.ctranno, GROUP_CONCAT(DISTINCT x.creference) as cref from `ntsalesreturn_t` x where x.compcode='".$_SESSION['companyid']."' group by x.ctranno) D on A.ctranno=D.ctranno where A.compcode='".$_SESSION['companyid']."' ";

if(isset($_POST['searchByName']) && $_POST['searchByName'] != '')
{
 $query .= "and (LOWER(B.ctradename) like LOWER('%".$_POST['searchByName']."%') OR LOWER(b.cname) like LOWER('%".$_POST['searchByName']."%') OR LOWER(a.ctranno) like LOWER('%".$_POST['searchByName']."%') OR LOWER(d.cref) like LOWER('%".$_POST['searchByName']."%'))";
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
 $sub_array[] = date_format(date_create($row['dreceived']), "m/d/Y");
 $sub_array[] = $row['lapproved'];
 $sub_array[] = $row['lcancelled'];
 $sub_array[] = $row['ccode'];
 $sub_array[] = $row['nlimit'];
 $sub_array[] = str_replace(",","<br>",$row['cref']);
 $sub_array[] = $row['lvoid'];
 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM ntsalesreturn where compcode='".$_SESSION['companyid']."'";
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