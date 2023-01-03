<?php
require_once "../Connection/connection_string.php";
//include('../Connection/connection_string.php');
$term = trim(strip_tags($_GET['term']));//retrieve the search term that autocomplete sends

$qstring = "select citemdesc,cpartno,nretailcost,cunit,ndiscount from items where citemdesc LIKE '%".$term."%'";

//echo $qstring; 
//$result =  mysqli_query($con,$qstring);//query the database for entries containing the term


if ($result=mysqli_query($con,$qstring))
  {
  // Return the number of rows in result set
		while ($row = mysqli_fetch_assoc($result))//loop through the retrieved values
		{
			$row['value']=htmlentities(stripslashes($row['citemdesc']));
			$row['id']=$row['cpartno'].":".$row['nretailcost'].":".$row['cunit'].":".$row['ndiscount'];
			//$row['label']=htmlentities(stripslashes($row['citemdesc']));
			$row_set[] = $row;//build an array
		}
}

echo json_encode($row_set);//format the array into json data

//echo $sql;

?>
