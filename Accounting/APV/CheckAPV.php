<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../../Connection/connection_string.php";
    include "../../Model/helper.php";

    $company = $_SESSION['companyid'];
    $creditcode = $_POST['accts'];

    mysqli_query($con, "Delete From apv_temp Where compcode = '$company'");

    $excel = ExcelRead($_FILES);
    $bnkcnt = 0;
    foreach($excel as $row){
        $bnkcnt++;
        if($bnkcnt>1){
            $nforid = $bnkcnt-1;

            $date = $row[0];
            $cref = $row[1];
            $ctin = $row[2];
            $ccode = $row[3];
            $cacctcode = $row[7];
            $nvatamt = str_replace( ',', '', $row[9]);
            $nvat = str_replace( ',', '', $row[10]);
            $cvatcode = $row[11];
            $ncharges = str_replace( ',', '', $row[12]);
            $nnonvat = str_replace( ',', '', $row[13]);
            $ngross = str_replace( ',', '', $row[14]);

            mysqli_query($con, "INSERT into apv_temp(`compcode`, `nid`, `ddate`, `cref`, `ctin`, `ccode`, `cacctcode`, `nvatamt`, `nvat`, `cvatcode`, `ncharges`, `nnonvat`, `ngross`) VALUES ('$company',".$nforid.",'$date','$cref','$ctin','$ccode','$cacctcode','$nvatamt','$nvat','$cvatcode','$ncharges','$nnonvat','$ngross')");
        }
    }

    $query = mysqli_query($con, "Select A.*, B.cname, C.cacctdesc From apv_temp A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctid Where A.compcode='$company'");
    $bkrectemp = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $bkrectemp[] = $row;
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?h=<?php echo time();?>">
 	 <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
	
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../js/bootstrap3-typeahead.min.js"></script>
	<script src="../../include/autoNumeric.js"></script>
	<!--
	<script src="../../Bootstrap/js/jquery.numeric.js"></script>
	<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
	-->

	<script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
	
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

	<!--
	--
	-- FileType Bootstrap Scripts and Link
	--
	-->
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/bs-icons/font/bootstrap-icons.css?h=<?php echo time();?>"/>
	<link href="../../Bootstrap/bs-file-input/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
	<script src="../../Bootstrap/bs-file-input/js/plugins/buffer.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/plugins/filetype.min.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/js/fileinput.js" type="text/javascript"></script>
	<script src="../../Bootstrap/bs-file-input/themes/explorer-fa5/theme.js" type="text/javascript"></script>

	<style>

		.rowpor:hover{
			background-color: #e5f1f9;
   			cursor: pointer;
		}
	</style>

</head>

<body style="padding:5px">
    <fieldset>
        <legend>APV (Others) Uploaded List</legend>	
        <table class="table table-sm" id="TblMatch">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>OR/SI</th>
                    <th>Supplier Code</th>
                    <th>Supplier Name</th>
                    <th>Particulars</th>
                    <th>Account Code</th>
                    <th>Account Title</th>
                    <th>Vatable Amount</th>
                    <th>VAT</th>
                    <th>Vat Code</th>
                    <th>Charges</th>
                    <th>Non VAT Amount</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($bkrectemp as $row){
                ?>
                    <tr>
                        <td> <?=$row['ddate']?> </td>
                        <td> <?=$row['cref']?> </td>
                        <td> <?=$row['ccode']?> </td>
                        <td> <?=$row['cname']?></td>
                        <td> <?=$row['cacctcode']?> </td>
                        <td> <?=$row['cacctdesc']?></td>
                        <td> </td>
                        <td> <?=$row['nvatamt']?> </td>
                        <td> <?=$row['nvat']?> </td>
                        <td> <?=$row['cvatcode']?> </td>
                        <td> <?=$row['ncharges']?> </td>
                        <td> <?=$row['nnonvat']?> </td>
                        <td> <?=$row['ngross']?> </td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
    </table>
</body>
</html>
