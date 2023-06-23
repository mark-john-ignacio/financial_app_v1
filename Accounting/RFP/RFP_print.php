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
		}

	}

  //ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
  $disregVAT = "";
  $disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
    if($row['ccode']=="PURCH_VAT"){
      $disregVAT = $row['cacctno'];
    }

    if($row['ccode']=="EWTPAY"){
      $disregEWT = $row['cacctno'];
    }
	}
	
  //header
	$csalesno = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, c.Fname, c.Minit, c.Lname, c.cdesignation , b.ctin, d.captype, e.cacctno as acctbank, f.cacctdesc as acctdescbank
  from rfp a 
  left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
  left join users c on a.cpreparedby=c.Userid 
  left join apv d on a.compcode=d.compcode and a.capvno=d.ctranno 
  left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode
  left join accounts f on e.compcode=f.compcode and e.cacctno=f.cacctid 
  where a.compcode='$company' and a.ctranno = '$csalesno'");

  if (mysqli_num_rows($sqlhead)!=0) {
    while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
      $RefAPV = $row['capvno'];
      $cAPtype = $row['captype'];

      $Bankacct = $row['acctbank'];
      $Bankacctdesc = $row['acctdescbank'];

      $CustCode = $row['ccode'];
      $CustName = $row['cname'];

      $CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate']." ".$row['ccountry'];

      $Date = $row['ddate'];
      $DateNeeded = $row['dtransdate'];
      $Gross = $row['ngross'];
			$GrossBal = $row['nbalance'];

      $cTin = $row['ctin'];

			$cremakrs = $row['cremarks'];
      
      $lCancelled = $row['lcancelled'];
      $lPosted = $row['lapproved'];

      $cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
      $cpreparedByDesig = $row['cdesignation'];
    }
  }

  //get reference invoices
  $refinvsx = array();
  $sqldtlss = mysqli_query($con,"Select A.crefinv from apv_d A where A.compcode='$company' and A.ctranno = '$RefAPV'");
  if (mysqli_num_rows($sqldtlss)!=0) {
    while($row = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){
      $refinvsx[] = $row['crefinv'];
    }
  }

  //get details
  if($cAPtype=="Purchases" || $cAPtype=="PurchAdv"){

    $xsql = "select A.ctranno, A.cpaymentfor, Sum(B.namount) as ntotamt, SUM(B.nvatamt) as ntotvat, Sum(B.newtamt) as ntotewt, Sum(ndue) as ntotdue
    from apv A left join apv_d B on A.compcode=B.compcode and A.ctranno=B.ctranno
    where A.compcode='$company' and A.ctranno = '$RefAPV'
    Group By A.ctranno, A.cpaymentfor, A.ngross";

  }else{

    $xsql = "select A.ctranno, A.cpaymentfor, Sum(B.ncredit) as ntotamt, 
    CASE WHEN B.cacctno='".$disregVAT."' THEN SUM(B.ndebit) ELSE 0 END as ntotvat, 
    CASE WHEN B.cacctno='".$disregEWT."' THEN SUM(B.ncredit) ELSE 0 END as ntotewt, 
    CASE WHEN B.cacctno not in ('".implode("','",$disreg)."') THEN SUM(B.ncredit) ELSE 0 END as ntotdue
    from apv A left join apv_t B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join accounts C on B.compcode=C.compcode and B.cacctno=C.cacctid
    where A.compcode='$company' and A.ctranno = '$RefAPV' and C.ccategory='LIABILITIES'
    Group By A.ctranno, A.cpaymentfor, A.ngross";

  }

  $dparticdet = array();
  $sqldtlss = mysqli_query($con,$xsql);
  if (mysqli_num_rows($sqldtlss)!=0) {
    while($row = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){
      $dparticdet[] = $row;
    }
  }

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 9pt;
		}
		.tdpadx{
			padding-top: 5px; 
			padding-bottom: 5px
		}
		.tddetz{
			border-left: 1px solid; 
			border-right: 1px solid;
		}
		.tdright{
			padding-right: 10px;
		}
		
	</style>
</head>

<body onLoad="window.print()">

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

			<table border="0" width="100%" style="border-collapse:collapse">
				<tr>
					<td colspan="2" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;"> REQUEST FOR PAYMENT </font>
					</td>
				</tr>

				<tr>
					<td style="padding-bottom: 10px">
						<font style="font-size: 14px;">&nbsp;</font>
					</td>

					<td align="right" style="padding-bottom: 10px">
					<font style="font-size: 14px;"><b>No.:</b> <?=$csalesno?></font>
					</td>
				</tr>

				<tr>
					<td colspan="2">
							<table border="0" width="100%" style="border-collapse:collapse" cellpadding="2">
								<tr>
									<td width="100px">
											<b>Payee: </b>
									</td>
									<td>
											<?=$CustName?>
									</td>
									<td width="100px">
											<b>Date</b>
									</td>
									<td width="100px" align="right">
                    <?=date("F d, Y", strtotime($Date))?>
									</td>
								</tr>
                <tr>
                  <td width="100px">
											<b>TIN</b>
									</td>
									<td>
											<?=$cTin?>
									</td>
									<td width="100px">
											<b>Due Date</b>
									</td>
									<td width="100px" align="right">
                    <?=date("F d, Y", strtotime($DateNeeded))?>
									</td>
								</tr>
                <tr>
                  <td width="100px">
											<b>Address  </b>
									</td>
									<td>
                    <?=$CustAdd?>
									</td>
									<td width="100px" colspan="2">
                    <b>Document Reference:  </b>
									</td>
								</tr>
                <tr>
                  <td width="100px" colspan="2">
											&nbsp;
									</td>
									
									<td width="100px" colspan="2" align="right">
                    <?php
                      if(count($refinvsx) > 0){
                        echo "SI#: ";
                        echo implode("<br>",$refinvsx);
                      }
                    ?>
									</td>
								</tr>
							</table>
					</td>
        </tr>							
			</table>

			<table border="0" align="center" width="100%" style="padding-top: 10px">
	
				<tr>
					<th class="tdpadx">Particulars</th>
					<th class="tdpadx" align="right">Amount</th>
					<th class="tdpadx" align="right">VAT</th>
					<th class="tdpadx" align="right"><b>Less EWT</b></th>
					<th class="tdpadx" align="right"><b>Total For Payment</b></th>
				</tr>

				<?php 
          $tottopay = 0;
          foreach($dparticdet as $rowdtls){
            $tottopay = $rowdtls['ntotdue'];
				?>

				<tr>
					<td align="center" class="tdpadx"><?=$cremakrs;?></td>
					<td align="right" class="tdpadx tdright" nowrap><?php echo number_format($rowdtls['ntotamt'],2);?></td>					
					<td align="right" class="tdpadx tdright" nowrap><?=(floatval($rowdtls['ntotvat'])!=0) ? number_format($rowdtls['ntotvat'],2) : "-";?></td>
					<td align="right" class="tdpadx tdright" nowrap><?=(floatval($rowdtls['ntotewt'])!=0) ? number_format($rowdtls['ntotewt'],2) : "-";?></td>
					<td align="right" class="tdpadx tdright" nowrap>
					<?php
						if(floatval($Gross) == floatval($tottopay)){

							echo "<span style=\"border-bottom: 5px solid #000; border-bottom-style: double\"><font size=\"2\">".number_format($rowdtls['ntotdue'],2)."</font></span>";

						}else{

							echo number_format($rowdtls['ntotdue'],2);

						}
					?>
							
					</td>
					
				</tr>

				<?php 
					}
					$xlabel = "";
					if(floatval($Gross) < floatval($tottopay)){
						
						if(floatval($GrossBal)==floatval($Gross)){
							$xlabel = "Completion Payment Amount";
						}else{
							$xlabel = "Partial Payment Amount";
						}
				?>
					<tr>
						<td align="right" class="tdpadx tdright" colspan="5" ><b><?=$xlabel?>: &nbsp;&nbsp;&nbsp;<span style="border-bottom: 5px solid #000; border-bottom-style: double"><font size="2"><?php echo number_format($Gross,2);?> </font></span></b></td>						
					</tr>
				<?php
					}
				?>

			</table>

        <div style="padding-top: 10px !important;"><b>Entry</b></div>
        <table border="0" width="60%" style="border-collapse: collapse;">  
          <tr>
            <th style="border: 1px solid" class="tdpadx">Account</th>
            <th style="border: 1px solid" class="tdpadx">Debit</th>
            <th style="border: 1px solid" class="tdpadx">Credit</th>
          </tr>

          <?php
          $ntotdebit = 0;
          $xsql = "select A.cacctno, B.cacctdesc,
          CASE WHEN A.cacctno not in ('".implode("','",$disreg)."') AND B.ccategory='LIABILITIES' THEN SUM(A.ncredit) ELSE 0 END as namt
          from apv_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid
          where A.compcode='$company' and A.ctranno = '$RefAPV' 
          Group By A.cacctno, B.cacctdesc";

					//echo $xsql;
					$forpay = 0;
          $sqlhead = mysqli_query($con,$xsql);
          if (mysqli_num_rows($sqlhead)!=0) {
            while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
              if(floatval($row['namt']) != 0){
								$forpay = $forpay + floatval($row['namt']);
          ?> 

          <tr>
            <td> <?=$row['cacctdesc']?> </td>
            <td align="right" class="tdpadx tdright" nowrap><?php echo number_format($row['namt'],2);?></td>
            <td>&nbsp;</td>
          </tr>
          <?php
              }
            }
          }
          ?>

          <tr>
            <td><?=$Bankacctdesc?></td>
            <td>&nbsp;</td>
            <td align="right" class="tdpadx tdright" nowrap><?php echo number_format($forpay,2);?></td>
          </tr>

        </table>

      <div style="padding-top: 20px !important"><i>Amount in words: </i> <?=strtoupper(numberTowords($forpay));?></div>
		</td>
	</tr>
  <tr>
		<td style="vertical-align: bottom;">
			<br><br>	<br><br>	
      <table border="0" width="100%">
        <tr>
          <th width="25%"> Prepared By </th>
          <th width="25%"> Checked By </th>
          <th width="25%"> Noted By </th>
          <th width="25%"> Approved By </th>
        </tr>

				<?php

					$unapp = "";
					$dalapp = "";
					$tatpp = "";

					$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, c.cdesignation, c.cusersign from rfp_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.crfpno = '$csalesno' order by a.nlevel");

					if (mysqli_num_rows($sqdts)!=0) {
						while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){

							if(intval($row['nlevel'])==1){
								if($row['lapproved']==1){
									$unapp = "<img src = '".$row['cusersign']."?x=".time()."' >";
								}else{
									$unapp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
								}
							}

							if(intval($row['nlevel'])==2){
								if($row['lapproved']==1){
									$dalapp = "<img src = '".$row['cusersign']."?x=".time()."' >";
								}else{
									$dalapp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
								}
							}

							if(intval($row['nlevel'])==3){
								if($row['lapproved']==1){
									$tatpp = "<img src = '".$row['cusersign']."?x=".time()."' >";
								}else{
									$tatpp  = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname']."<br>".$row['cdesignation'];
								}
							}
						}
					}
				?>

        <tr>
          <td align="center" style="padding-top: 50px"> <?php echo $cpreparedBy;?><br><?=$cpreparedByDesig?> </td>
          <td align="center" style="padding-top: 50px"> <?=$unapp?> </td>
          <td align="center" style="padding-top: 50px"> <?=$dalapp?> </td>
					<td align="center" style="padding-top: 50px"> <?=$tatpp?> </td>
        </tr>
      </table>            
    </td>
  </tr>
</table>


</body>
</html>