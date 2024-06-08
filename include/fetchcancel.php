<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../Connection/connection_string.php');
	$company = $_SESSION['companyid'];

	if(isset($_POST["id"]))
	{
		$output = '';
		$query = "SELECT * FROM logfile WHERE compcode='$company' and ctranno='".$_POST["id"]."' and cevent='".$_POST["stat"]."' Order by ddate DESC LIMIT 1";

		$result = mysqli_query($con, $query);
		while($row = mysqli_fetch_array($result))
		{
			$output = '
			<label>'.$_POST["stat"].' By : '.$row['cuser'].'</label><br>
			<label>Date/Time : '.$row['ddate'].'</label><br>
			<label>Reason : '.$row['cancel_rem'].'</label>';
		}
	echo $output;
	}

?>