<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

function numberTowords($num)
{
	$ones = array(
		0 => "",
		1 => "One",
		2 => "Two",
		3 => "Three",
		4 => "Four",
		5 => "Five",
		6 => "Six",
		7 => "Seven",
		8 => "Eight",
		9 => "Nine",
		10 => "Ten",
		11 => "Eleven",
		12 => "Twelve",
		13 => "Thirteen",
		14 => "Fourteen",
		15 => "Fifteen",
		16 => "Sixteen",
		17 => "Seventeen",
		18 => "Eighteen",
		19 => "Nineteen",
		"01" => "One",
		"02" => "Two",
		"03" => "Three",
		"04" => "Four",
		"05" => "Five",
		"06" => "Six",
		"07" => "Seven",
		"08" => "Eight",
		"09" => "Nine",
		"014" => "Fourteen"
	);
	$tens = array( 
		0 => "",
		1 => "Ten",
		2 => "Twenty",
		3 => "Thirty", 
		4 => "Forty", 
		5 => "Fifty", 
		6 => "Sixty", 
		7 => "Seventy", 
		8 => "Eighty", 
		9 => "Ninety" 
	); 
	$hundreds = array( 
		"Hundred", 
		"Thousand", 
		"Million", 
		"Billion", 
		"Trillion", 
		"Quadrillion" 
	); /*limit t quadrillion */

	$num = number_format($num,2,".",","); 
	$num_arr = explode(".",$num); 
	$wholenum = $num_arr[0]; 
	$decnum = $num_arr[1]; 
	$whole_arr = array_reverse(explode(",",$wholenum)); 
	krsort($whole_arr,1); 
	$rettxt = ""; 

	foreach($whole_arr as $key => $i){
	
		while(substr($i,0,1)=="0")
			$i=substr($i,1,5);
			if($i!=="") {
				if($i < 20){ 
					$rettxt .= $ones[$i]; 
				}elseif($i < 100){ 
					if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
					if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
				}else{ 
					if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 

					if(substr($i,1,1)==1){
						if(substr($i,2,1)==0){
							$rettxt .= " ".$tens[substr($i,1,1)];
						}else{
							$rettxt .= " ".$ones[substr($i,1,2)];
						}
					}else{
						if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
						if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
					}
				} 
			}
			
			if($key > 0){ 
				$rettxt .= " ".$hundreds[$key]." "; 
			}
		} 

		if($decnum > 0){
			$rettxt .= " Pesos and ";
			if($decnum < 20){
				if($decnum == 1){
					$rettxt .= $ones[$decnum]. " Centavo";
				}else{
					$rettxt .= $ones[$decnum]. " Centavos";
				}
			}elseif($decnum < 100){
				$rettxt .= $tens[substr($decnum,0,1)];
				$rettxt .= " ".$ones[substr($decnum,1,1)]. " Centavos";
			}
		}else{
			$rettxt .= " Pesos";
		}
	return $rettxt;
}

	$company = $_SESSION['companyid'];

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
			$logotins = $rowcomp['comptin'];
		}

	}
	
	$csalesno = $_REQUEST['id'];
	$sqlhead = mysqli_query($con,"select A.*, B.cname, c.Fname, c.Minit, c.Lname, c.cdesignation, c.cusersign from paybill A left join bank B on A.compcode=B.compcode and A.cbankcode=B.ccode left join users c on A.cpreparedby=c.Userid where A.compcode='$company' and A.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$Payee = $row['cpayee'];
		$Date = $row['dcheckdate'];

		$Particulars = $row['cparticulars'];
		$Amount = $row['npaid']; 

		$Bankname = $row['cname']; 

		$Paymeth = $row['cpaymethod'];
		$Refno = ($row['cpaymethod']=="cheque") ? $row['ccheckno'] : $row['cpayrefno']; 

		$lSent = $row['lsent'];

		$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
		$cpreparedByDesig = $row['cdesignation'];
		$cpreparedBySign = $row['cusersign'];
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Check Voucher</title>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 10pt;
		}
		table {
			border-color: #000000;
			border-collapse: collapse;
		}
		
	</style>
</head>
<body>

<table border="0" width="100%" cellpadding="1px"  id="tblMain">

	<tr>
		<td align="center"> 

				<table border="0" width="100%">
						<tr align="center">
							<td><img src="<?php echo "../".$logosrc; ?>" height="68px"></td>
						</tr>
						<tr align="center">
							<td><font style="font-size: 18px;"><?php echo $logonamz; ?></font></td>
						</tr>
						<tr align="center">
							<td style="padding-bottom: 20px"><font><?php echo $logoaddrs; ?></font></td>
						</tr>
				</table>

		</td>
	</tr>

	<tr>
		<td style="vertical-align: top; padding-top: 10px">

			<table border="0" width="100%">

				<tr>
					<td colspan="4" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;"> <?=($Paymeth=="cheque") ? "CHECK" : "PAYMENT"; ?> VOUCHER </font>
					</td>
				</tr>

				<tr>
					<td style="padding-right: 10px" width="80px">
							<b> Paid To </b>
					</td>
					<td><?=$Payee?></td>

					<td style="padding-right: 10px" width="100px" align="right">
							<b> CV No. </b>
					</td>
					<td style="padding-right: 10px" width="100px">
							<?=$csalesno?>
					</td>
				</tr>
				<tr>
					<td style="padding-right: 10px" width="80px">
						<b> Bank </b>
					</td>
					<td><?=$Bankname?></td>
					<td style="padding-right: 10px" width="100px" align="right">
							<b> Date. </b>
					</td>
					<td style="padding-right: 10px" width="100px">
							<?=$Date?>
					</td>
				</tr>

				<tr>
					<td style="padding-right: 10px" width="80px">
						<b> Check No. </b>
					</td>
					<td><?=$Refno?></td>
					<td style="padding-right: 10px" width="100px" align="right">
							&nbsp;
					</td>
					<td style="padding-right: 10px" width="100px">
						&nbsp;
					</td>
				</tr>
			
			</table>
			<br>
			<table border="1" align="center" width="100%">
	
				<tr>
					<th style="padding: 3px; border-bottom: 1px solid">PARTICULARS</th>
					<th style="padding: 3px; border-bottom: 1px solid">AMOUNT</th>
				</tr>

				<tr>
					<td align="center" style="height: 0.75in" valign="top"><?=$Particulars?></td>
					<td align="center" style="height: 0.75in" valign="top"><?=number_format($Amount,2)?></td>					
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" align="center" width="100%">
	
				<tr>
					<td width="60%" valign="top">
						<font style="font-size: 12px;">Entry</font>
						<table border="1" width="100%" cellpadding="3">
							<tr>
								<th>ACCOUNT TITLE</th>
								<th>DEBIT</th>
								<th>CREDIT</th>
							</tr>
							
							<?php
								$sql2 = mysqli_query($con,"Select A.cacctno, B.cacctdesc, A.entrytyp, sum(A.napplied) as napplied From paybill_t A left join paybill C on A.compcode=C.compcode and A.ctranno=C.ctranno left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$csalesno' group by A.cacctno, B.cacctdesc, A.entrytyp Order by A.nident");

								while($row = mysqli_fetch_array($sql2, MYSQLI_ASSOC)){
							?>
							<tr>
								<td><?=$row['cacctdesc']?></td>
								<td align="right"><?=($row['entrytyp']=="Debit") ? number_format($row['napplied'],2) : ""?></td>
								<td align="right"><?=($row['entrytyp']=="Credit") ? number_format($row['napplied'],2) : ""?></td>
							</tr>
							<?php
								}

								$sql1 = mysqli_query($con,"Select A.cacctno, B.cacctdesc, A.npaid From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$csalesno'");
								while($row = mysqli_fetch_array($sql1, MYSQLI_ASSOC)){
							?>
							<tr>
								<td><?=$row['cacctdesc']?></td>
								<td align="right">&nbsp;</td>
								<td align="right"><?=number_format($row['npaid'],2)?></td>
							</tr>
							<?php
								}
							?>
						</table>
					</td>
					<td>

						&nbsp;

					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="60%" style="margin-top: 20px">		
				<tr>
					<td width="100px">
						Received By: 
					</td>
					<td style="border-bottom: 1px solid #000">
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="60%" style="margin-top: 10px">	
				<tr>
					<td>
						the amount of: 
					</td>
					<td>
						<?=numberTowords($Amount);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br><br>	<br><br>	
      <table border="0" width="100%">
        <tr>
          <th width="25%"> Prepared By </th>
          <th width="25%"> Checked By </th>
          <th width="25%"> Verified By </th>
          <th width="25%"> Approved By </th>
        </tr>

				<?php

					$unapp = "";
					$dalapp = "";
					$tatpp = "";
					$tsentapp = "";

					$tpad0 = "";
					$tpad1 = "";
					$tpad2 = "";
					$tpad3 = "";

					$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, c.cdesignation, c.cusersign from paybill_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cpayno = '$csalesno' order by a.nlevel");

					if (mysqli_num_rows($sqdts)!=0) {
						while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){

							if(intval($row['nlevel'])==1){
								if($row['lapproved']==1){
									$unapp = "<img src = '".$row['cusersign']."?x=".time()."' >";
									$tpad1 = "10px";
								}else{
									$unapp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
									$tpad1 = "50px";
								}
							}

							if(intval($row['nlevel'])==2){
								if($row['lapproved']==1){
									$dalapp = "<img src = '".$row['cusersign']."?x=".time()."' >";
									$tpad2 = "10px";
								}else{
									$dalapp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
									$tpad2 = "50px";
								}
							}

							if(intval($row['nlevel'])==3){
								if($row['lapproved']==1){
									$tatpp = "<img src = '".$row['cusersign']."?x=".time()."' >";
									$tpad3 = "10px";
								}else{
									$tatpp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
									$tpad3 = "50px";
								}
							}
						}
					}

					if($lSent==1){
						$tsentapp = "<img src = '".$cpreparedBySign."?x=".time()."' >";
						$tpad0 = "10px";
					}else{
						$tsentapp = $cpreparedBy."<br>".$cpreparedByDesig;
						$tpad0 = "50px";
					}
				?>

        <tr>
          	<td align="center" style="padding-top: <?=$tpad0?>"> <?=$tsentapp?></td>
          	<td align="center" style="padding-top: <?=$tpad1?>"> <?=$unapp?> </td>
          	<td align="center" style="padding-top: <?=$tpad2?>"> <?=$dalapp?> </td>
			<td align="center" style="padding-top: <?=$tpad3?>"> <?=$tatpp?> </td>
        </tr>
      </table> 
		</td>
	</tr>
</table>


</body>
</html>
