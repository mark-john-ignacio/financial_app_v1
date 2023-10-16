<?php
    if(!isset($_SESSION)){
        session_start();
    }

    require_once "../../Connection/connection_string.php";
	include('../../include/denied.php');

    $company = $_SESSION['companyid'];
	$ctranno = $_REQUEST['txtctranno'];
	$arr = array();

    //ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
		if($row['ccode']=="EWTPAY"){
            $disregEWT = $row['cacctno'];
        }
	}

    $sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		$arr['company'] = array(
			'name' => $row['compname'],
			'tin' => str_replace("-", " ", $row['comptin']),
			'add' => $row['compadd'],
			'zip' => $row['compzip']
		);
	}

	$sql = "select * from paybill where compcode='$company' and ctranno = '$ctranno' order by dtrandate DESC";

	$result = mysqli_query($con, $sql);

	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arr['paybill'] = array(
			'name' => $row['ccode']
		);
	}

	$supplier = $arr['paybill']['name'];
	$sql = "select * from suppliers 
			WHERE compcode='$company' and ccode = '$supplier'  
			Order By nid DESC";
	$result = mysqli_query ($con, $sql);

	$result = mysqli_query($con, $sql);

	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$address = $row['chouseno'];
		if($row['ccity'] != ""){
			$address .= ", ". $row['ccity'];
		}
		if($row['cstate'] != ""){
			$address .= ", " . $row['cstate'];
		}

		$arr['supplier'] = array(
			'tradename' => $row['ctradename'],
			'tin' => str_replace("-", " ", $row['ctin']),
			'address' => $address,
			'zip' => $row['czip']
		);
	}

	$sql = 	"select a.cacctno, a.ctitle, a.ndebit, a.ncredit, a.cremarks, a.csubsidiary, a.cacctrem, a.cewtcode, a.newtrate 
	from apv_t a
	where a.compcode = '$company' and a.ctranno = '$ctranno' order by a.nidentity";


	$sql = "SELECT a.*, a.napplied, b.ncredit, b.*
	FROM `paybill_t` a
	LEFT JOIN `apv_t` b on a.compcode = b.compcode and a.capvno = b.ctranno";
	$result = mysqli_query($con, $sql);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arr['paybill'] = array(
			"title" => $row['ctitle'],
			"atc" => $row['cewtcode'],
			"apply" => $row['napplied']
		);
	}

    $arr['quarter'] = array(
		'first' => array('01', '02', '03'),
		'second' => array('04', '05', '06'),
		'third' => array('07', '08', '09'),
		'fourth' => array('10', '11', '12')
	);

	// $dmonth = date("m", strtotime($dpaydate));
	// $dyear = date("Y", strtotime($dpaydate));

	// $arr['date'] = array('first' => "", 'second' => "");

	// if(in_array($dmonth, $arr['quarter']['first'])){

	// 		$arr['date']['first'] = "0131".$dyear;
	// 		$arr['date']['second'] = "0331".$dyear;

	// } elseif(in_array($dmonth, $arr['quarter']['second'])){

	// 		$arr['date']['first'] = "0401".$dyear;
	// 		$arr['date']['second'] = "0630".$dyear;

	// } elseif(in_array($dmonth, $arr['quarter']['third'])){

	// 		$arr['date']['first'] = "0701".$dyear;
	// 		$arr['date']['second'] = "0930".$dyear;

	// } elseif(in_array($dmonth, $arr['quarter']['fourth'])){

	// 		$arr['date']['first'] = "1001".$dyear;
	// 		$arr['date']['second'] = "1231".$dyear;

	// }
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>BIR 2307 Form</title>
		<style>
			body{
				font-size: 26;
			}
			table {
				border-color: #000000;
				border-collapse: collapse;
			}
			.company-tin {
				position: absolute;
				font-size: 27;
				top: 560px;
				left: 465px;
				letter-spacing: 17px;
			}
			.company-name {
				position: absolute;
				top: 625px;
				left: 100px;
			}
			.company-address {
				position: absolute;
				top: 690px;
				left: 100px;
			}
			.company-zip {
				position: absolute;
				top: 680px;
				left: 89%;
				letter-spacing: 15px;
			}

			.supplier-tin {
				position: absolute;
				font-size: 27;
				top: 310px;
				left: 470px;
				letter-spacing: 17px;
			}
			.supplier-name {
				position: absolute;
				top: 370px;
				left: 100px;
			}
			.supplier-address {
				position: absolute;
				top: 430px;
				left: 100px;
			}
			.supplier-zip {
				position: absolute;
				top: 430px;
				left: 89%;
				letter-spacing: 15px;
			}
			.ap-table {
				position: absolute;
				top: 810px;
				left: 60px
			}
		</style>
	</head>
    <body>
		<div class="form-container" style="font-size: 0.9em;font-weight: bold;" >
				<img src="../../bir_forms/BIR_2307_Form.svg" width="100%" >

				<div class="supplier-tin"> <?= $arr['supplier']['tin'] ?> </div>
				<div class="supplier-name"><?= $arr['supplier']['tradename'] ?></div>
				<div class="supplier-zip"> <?= $arr['supplier']['zip'] ?> </div>
				<div class="supplier-address"> <?= $arr['supplier']['address'] ?> </div>

				<div class="company-tin"><?= $arr['company']['tin'] ?></div> 
				<div class="company-name"><?= $arr['company']['name'] ?></div>
				<div class="company-address"><?= $arr['company']['add'] ?></div>
				<div class="company-zip"> <?= $arr['company']['zip'] ?> </div>

				<div class="ap-table"> 
					<table>
						<tbody>
							<tr>
								<td><?= $arr['paybill']['title'] ?></td>
								<td><?= $arr['paybill']['atc'] ?> </td>
								
							</tr>
						</tbody>
					</table>
					
				</div>

				
		</div>
    </body>
</html>