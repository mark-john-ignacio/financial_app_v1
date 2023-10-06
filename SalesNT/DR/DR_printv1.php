<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

//	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_DR'");
//	if(mysqli_num_rows($sqlauto) != 0){
//		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
//		{
//			$autopost = $rowauto['cvalue'];
//		}
//	}

	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
            $compphone = $rowcomp['cpnum'];
            $companyemail = $rowcomp['email'];
			$companytin = $rowcomp['comptin'];
            $ptucode = $rowcomp['ptucode'];
            $ptudate = $rowcomp['ptudate'];
		}

	}
	
	$csalesno = $_REQUEST['x'];
    $sql = "select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin,c.cname as cdelname from 
    ntdr a 
    left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
    left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid 
    where a.compcode='$company' and a.ctranno = '$csalesno'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
            $CustCode = $row['ccode'];
            $CustName = $row['cdelname'];
            $CustDelName = $row['cname'];
            $Remarks = $row['cremarks'];
            $Date = $row['dcutdate'];
            $address = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
            $cTin = $row['ctin'];
    
            //$SalesType = $row['csalestype'];
            $Gross = $row['ngross'];
            $cTerms = $row['cterms'];
            
            $lCancelled = $row['lcancelled'];
            $lPosted = $row['lapproved'];
            $lPrintPosted = $row['lprintposted'];
        }
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print V1</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>
<body style='padding: 0;' onload='window.print()'>
    <!-- Header -->
    <div id='header' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 100%; '>
                <img src='../../images/SLogo.png' alt='Sert technology Logo' width='100%' height="100%">
            </div>
            <div class='col-sm' style='width: 100%; text-align: justify; text-justify: inter-word;'>
                    <h5 class='nopadding'><?= $companyadd ?></h5>
                    <!-- <h5 class='nopadding'>Tel/Fax: </h5> -->
                    <h5 class='nopadding'>Mobile No.: <?= $compphone ?></h5>
                    <!-- <h5 class='nopadding'>Manila Line: </h5> -->
                    <h5 class='nopadding'>Email: <?= $companyemail ?></h5>
                    <!-- <h5 class='nopadding'>Website: www.serttech.com</h5> -->
                    <h5 class='nopadding'>VAT Reg. TIN: <?= $companytin ?></h5>
            </div>
            <div class='col-sm' style='width: 100%; margin: 5%; text-align: center;'>
                <h1>Delivery Receipt</h1>
                <h2>No. 00001</h2>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div id='body' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
        <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;"> &nbsp; </span> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;">Date: </span> <?= $Date ?> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Sold To: </span> <?= $CustName ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;">P.O. Terms: </span>  </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5 class='nopadding'><span style="font-weight: bold;">TIN: </span> <?= $cTin ?></h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5 class='nopadding'><span style="font-weight: bold;">Payment Terms: </span> <?= $cTerms ?> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Address: </span> <?= $address ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;"> Business Style: </span> <?= $CustName  ?></h5>
            </div>
        </div>
    </div>


    <!-- Body List of Data -->
    <div class='container' id='item' style='width: 100%; height: 415px; top: 0;'>
        <div class='row'>
            <table class='table' id='salestable' > 
                <thead style='border: .5 solid black;'>
                    <tr>
                        <th>QTY.</th>
                        <th>UNIT</th>
                        <th>ITEM No.</th>
                        <th style='width: 60%'>ITEM DESCRIPTION</th>
                    </tr>
                    
                </thead>
                <tbody>
                        <?php
                            $sql = "select a.*,b.citemdesc from ntdr_t a 
                            left join items b on a.citemno=b.cpartno 
                            where a.compcode='$company' and a.ctranno = '$csalesno'";
                            
                            $query = mysqli_query($con, $sql);
                            while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)):
                        ?>
                            <tr>
                                <td><?= $row['nqty'] ?></td>
                                <td><?= $row['cunit'] ?></td>
                                <td><?= $row['citemno'] ?></td>
                                <td><?= $row['citemdesc'] ?></td>
                            </tr>
                        <?php 
                            endwhile;
                        ?>
                </tbody>
            </table>
        </div>
    </div>


    <div id='footer' class='container' style='width: 100%; margin-top: 2px; '>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 20%; font-size: 9px; font-weight: bold;'>
                <h5>PTU No.: <?= $ptucode ?></h5>
                <h5>Date Issued: <?= $ptudate ?></h5>
                <h5>Inclusive Serial No.: <?= $csalesno ?></h5>
                <h5>Timestamp: <?= date('m-d-Y') ?></h5>
            </div>
            <div class='col-sm' style='width: 40%; '>
                <div style='font-size: 10px; margin-left: 5px; font-weight: bold; width: 100%;'>Issued By:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
            </div>
            <div class='col-sm' style='width: 40%; border: 1 solid black '>
                <div style='font-size: 10px; margin-left: 5px; font-weight: bold; width: 100%; text-align: center;'>Received the merchandise in good order and condition:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
                
            </div>
        </div>
        <div style='font-size: 12px; font-weight: bold; width: 100%; text-align: right;'>THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES</div>
    </div>
</body>
</html>