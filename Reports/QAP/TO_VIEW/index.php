<?php
    if(!isset($_SESSION)) {
        session_start();
    }

    $_SESSION['pageid'] = "BIRQAP";

    include("../../../Connection/connection_string.php");
    include('../../../include/denied.php');
    include('../../../include/access.php');
    include('../../../Model/helper.php');

    $company = $_SESSION['companyid'];

    //get default EWT acct code
	@$ewtpaydef = "";
	@$ewtpaydefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno'];
			@$ewtpaydefdsc = $row['cacctdesc']; 
		}
	}


    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    $list = $query -> fetch_assoc();
    $company_detail = [
        'name' => $list['compname'],
        'trade' => $list['compdesc'],
        'address' => $list['compadd'],
        'tin' => TinValidation($list['comptin'])
    ];

    $year = date("Y", strtotime($_POST['years']));

    $apv = array();
    $xendingmonth = "";
    switch($_POST['selqrtr']){
        case 1:
            $months = "1,2,3";
            $xendingmonth = 3;
            break;
        case 2:
            $months = "4,5,6";
            $xendingmonth = 6;
            break;
        case 3:
            $months = "7,8,9";
            $xendingmonth = 9;
            break;
        case 4:
            $months = "10,11,12";
            $xendingmonth = 12;
            break;
        default: 
            $months = "";
            break;
    }
    $sql = "SELECT a.ncredit, a.cewtcode, a.newtrate, a.ctranno, b.ngross, b.dapvdate, c.cname, CONCAT_WS(', ', c.chouseno, c.ccity) as caddress, c.ctin, d.cdesc 
        FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON b.compcode = c.compcode AND b.ccode = c.ccode 
        LEFT JOIN groupings d ON c.compcode = d.compcode AND c.csuppliertype = d.ccode AND d.ctype = 'SUPTYP'
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) in ($months) AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and a.cacctno='$ewtpaydef' and IFNULL(a.cewtcode,'') <> '' and a.ncredit>0 Order By b.dapvdate, a.ctranno";
    
    //echo $sql."<br>";
    $query = mysqli_query($con, $sql);               
    while($row = $query -> fetch_assoc()){
        $apv[] = $row;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap-datetimepicker.css">


    <script src="../../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../../include/autoNumeric.js"></script>

    <script src="../../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../../Bootstrap/js/moment.js"></script>
    <script src="../../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>
    <div style="padding: 10px;">
        
        <table width="100%" border=0 cellpadding="3px">
            <tr>
                <td><h4 style="margin: 0">QUARTERLY ALPHABETICAL LIST OF PAYEES SUBJECTED TO EXPANDED WITHHOLDING TAX & PAYEES WHOSE INCOME PAYMENTS ARE EXEMPT</h4></td>
            </tr>
            <tr>
                <?php
                    $dateObj   = DateTime::createFromFormat('!m', $xendingmonth);
                    $xendingmonth = $dateObj->format('F');
                ?>
                <td><h4  style="margin: 0">FOR THE QUARTER ENDING <?=$xendingmonth?>, <?=$_POST['years']?></h4></td>
            </tr>

            <tr>
                <td style="padding-top: 10px"><h4  style="margin: 0">TIN: <?=$company_detail['tin']?> </h4></td>
            </tr>
            <tr>
                <td style="padding-bottom: 20px"><h4  style="margin: 0">WITHHOLDING AGENT'S NAME: <?=$company_detail['name']?></h4></td>
            </tr>
        </table>

        <table class="table table-sm" id="QAPList" style="font-size: 11px !important">
            <thead>
                <tr>
                    <th>TRANSACTION DATE</th>
                    <th style="padding-left: 10px">CV REFERENCE NO.</th>
                    <th style="padding-left: 10px">VENDOR TIN</th>
                    <th style="padding-left: 10px">VENDOR NAME</th>
                    <th style="padding-left: 10px">VENDOR ADDRESS</th>
                    <th style="padding-left: 10px">W/TAX CODE</th>
                    <th style="padding-left: 10px">W/TAX RATE</th>
                    <th style="padding-left: 10px">W/TAX BASE AMOUNT</th>
                    <th style="padding-left: 10px">W/TAX AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($apv as $rs2){
                ?>
                <tr>
                    <td><?=$rs2['dapvdate']?></th>
                    <td style="padding-left: 10px"><?=$rs2['ctranno']?></th>
                    <td style="padding-left: 10px"><?=$rs2['ctin']?></th>
                    <td style="padding-left: 10px"><?=$rs2['cname']?></th>
                    <td style="padding-left: 10px"><?=$rs2['caddress']?></th>
                    <td style="padding-left: 10px"><?=$rs2['cewtcode']?></th>
                    <td style="padding-left: 10px"><?=(floatval($rs2['newtrate']) / 100) . "%"?></th>
                    <td style="padding-left: 10px"><?=number_format($rs2['ngross'],2)?></th>
                    <td style="padding-left: 10px"><?=number_format($rs2['ncredit'],2)?></th>
                </tr>
                <?php
                    }
                ?>

            </tbody>
        </table>

    </div>
</body>
</html>
