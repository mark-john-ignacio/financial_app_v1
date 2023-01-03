<?php
require_once "../Connection/connection_string.php";
//include('../Connection/connection_string.php');
$term = trim(strip_tags($_GET['term']));//retrieve the search term that autocomplete sends

$qstring = "select cname,cempid,nlimit from customers where cname LIKE '%".$term."%'";

//echo $qstring; 
//$result =  mysqli_query($con,$qstring);//query the database for entries containing the term


if ($result=mysqli_query($con,$qstring))
  {
  // Return the number of rows in result set
		while ($row = mysqli_fetch_assoc($result))//loop through the retrieved values
		{
	
	$c_id = $row['cempid'];
	if(!file_exists("../imgcust/".$c_id .".jpg")){
		$imgsrc = "../imgcust/emp.jpg";
	}
	else{
		$imgsrc = "../imgcust/".$c_id .".jpg";
	}



			$row['value']=htmlentities(stripslashes($row['cname']));
			$row['id']=$row['cempid'].":".$row['nlimit'].":".$imgsrc;
			//$row['label']=htmlentities(stripslashes($row['citemdesc']));
			$row_set[] = $row;//build an array
		}
}

echo json_encode($row_set);//format the array into json data

//echo $sql;

?>
