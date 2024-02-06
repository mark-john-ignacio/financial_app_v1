<?php
    include('Connection/connection_string.php');
    $company = '001';

    $sql = "SELECT norder FROM  customers_secondary where compcode='$company' and cmaincode = 'CUST072' Order By norder DESC";
    $result = mysqli_query($con, $sql);
    $rowcount=mysqli_num_rows($result);

    if($rowcount>0){
        $row   = mysqli_fetch_row($result);

        print_r($row);

        $chilnonxt = floatval($row[0]) + 1;
		$chilnonxt = str_pad($chilnonxt, 4, '0', STR_PAD_LEFT);

        echo $chilnonxt;
    }else{
        echo "00000000";
    }
   

    echo "<br><br><br>";

    $sdft = "abc@gmail.com,123@gmail.com";
    $array = explode(',', $sdft);
	foreach($array as $value){
		echo $value."<br>";
	}
?>