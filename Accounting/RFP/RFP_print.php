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
	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, c.Fname, c.Minit, c.Lname, c.cdesignation , b.ctin, d.captype, c.cusersign
	from rfp a 
	left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode 
	left join users c on a.cpreparedby=c.Userid 
	left join apv d on a.compcode=d.compcode and a.capvno=d.ctranno 
  	where a.compcode='$company' and a.ctranno = '$csalesno'");

	//left join bank e on a.compcode=e.compcode and a.cbankcode=e.ccode
	//left join accounts f on e.compcode=f.compcode and e.cacctno=f.cacctid   , e.cacctno as acctbank, f.cacctdesc as acctdescbank
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$RefAPV = $row['capvno'];
			$cAPtype = $row['captype'];

			$cpaymeth = $row['cpaymethod']; 

			$cBank = $row['cbankname'];
			$cBankAcct = $row['cbankacctno'];
			$cBankAcNm = $row['cbankacctname'];

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
			$lSent = $row['lsent'];

			$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
			$cpreparedByDesig = $row['cdesignation'];
			$cpreparedBySign = $row['cusersign'];
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

  	$xsql = "select A.ctranno, B.cacctno, A.cpaymentfor, Sum(B.ncredit) as ntotamt, 
  	CASE WHEN B.cacctno='".$disregVAT."' THEN SUM(B.ndebit) ELSE 0 END as ntotvat, 
  	CASE WHEN B.cacctno='".$disregEWT."' THEN SUM(B.ncredit) ELSE 0 END as ntotewt, 
 	CASE WHEN B.cacctno not in ('".implode("','",$disreg)."') THEN SUM(B.ncredit) ELSE 0 END as ntotdue
 	from apv A left join apv_t B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join accounts C on B.compcode=C.compcode and B.cacctno=C.cacctid
  	where A.compcode='$company' 
	and A.ctranno in (Select capvno from rfp_t where compcode='$company' and ctranno='$csalesno')	
  	Group By A.ctranno, B.cacctno, A.cpaymentfor, A.ngross";

	//echo $xsql."<br><br>";

  	$dparticdet = array();
	$totsAPVAT = array();
	$totsAPEWT = array();
	$sqldtlss = mysqli_query($con,$xsql);
	if (mysqli_num_rows($sqldtlss)!=0) {
		while($row = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){
				

			if($row['cacctno'] != $disregVAT && $row['cacctno'] != $disregEWT){
				if($row['ntotamt'] != 0 && $row['ntotdue'] != 0){
					$dparticdet[] = $row;
				}
			}

			if($row['cacctno'] == $disregVAT){
				$totsAPVAT[$row['ctranno']] = $row['ntotvat'];
			}

			if($row['cacctno'] == $disregEWT){
				$totsAPEWT[$row['ctranno']] = $row['ntotewt'];
			}
		
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
		/* The standalone checkbox square*/
		.checkbox {
			width:20px;
			height:20px;
			border: 1px solid #000;
			display: inline-block;
		}

		/* This is what simulates a checkmark icon */
		.checkbox.checked:after {
			content: '';
			display: block;
			width: 4px;
			height: 7px;
		
			/* "Center" the checkmark */
			position:relative;
			top:4px;
			left:7px;
			
			border: solid #000;
			border-width: 0 2px 2px 0;
			transform: rotate(45deg);
		}
	</style>
</head>

<body onLoad="window.print()"> <!---->

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
								<td width="100px"><b>Payee: </b></td>
								<td><?=$CustName?></td>
								<td width="100px"><b>Date</b></td>
								<td width="150px" align="right"><?=date("F d, Y", strtotime($Date))?></td>
							</tr>
                			<tr>
                  				<td width="100px"><b>TIN</b></td>
								<td><?=$cTin?></td>
								<td width="100px"><b>Due Date</b></td>
								<td width="150px" align="right"><?=date("F d, Y", strtotime($DateNeeded))?></td>
							</tr>
                			<tr>
                  				<td width="100px"><b>Address</b></td>
								<td><?=$CustAdd?></td>
								<td width="100px" colspan="2"><b>Document Reference:</b></td>
							</tr>
                			<tr>
								<td width="100px" colspan="2"><b>Mode of Payment</b></td>				
								<td width="100px" colspan="2" align="right">
									<?php
									if(count($refinvsx) > 0){
										echo "SI#: ";
										echo implode("<br>",$refinvsx);
									}
									?>
								</td>
							</tr>
							<tr>
                  				<td width="100px">&nbsp;</td>
								<td>
									<table border="0" width="100%" style="border-collapse:collapse" cellpadding="2">
										<tr>
											
                  							<td width="10%" nowrap valign="middle">Bank Transfer</td>
											<td width="5%"><div class="checkbox<?=($cpaymeth=="bank transfer") ? " checked" : ""?>"></div></td>
											<td>Bank Name: <?=$cBank?></td>
										</tr>
										<tr>											
                  							<td width="10%" nowrap valign="middle">&nbsp;</td>
											<td width="5%">&nbsp;</td>
											<td>Account #: <?=$cBankAcct?></td>
										</tr>
										<tr>											
                  							<td width="10%" nowrap valign="middle">&nbsp;</td>
											<td width="5%">&nbsp;</td>
											<td>Account Name: <?=$cBankAcNm?></td>
										</tr>
										<tr>
											
                  							<td valign="middle">Check</td>
											<td><div class="checkbox<?=($cpaymeth=="cheque") ? " checked" : ""?>"></div></td>
											<td>&nbsp;</td>
										</tr>
										<tr>											
											<td valign="middle">Cash</td>
											<td><div class="checkbox<?=($cpaymeth=="cash") ? " checked" : ""?>"></div></td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</td>
								<td width="100px" colspan="2">&nbsp;</td>
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

				<tr>
					<td class="tdpadx" colspan='5'><?=$cremakrs;?></td> 
				</tr>

				<?php 
         			$tottopay = 0;
		 			$aprowcnt = 0;

					$xsql = "Select distinct capvno, cacctno, npayable from rfp_t where compcode='$company' and ctranno = '$csalesno' and cacctno not in ('".implode("','",$disreg)."')";

					$sqlhead = mysqli_query($con,$xsql);

					$totamt = 0;
					$vatamt = 0;
					$ewtamt = 0;
					$dueamt = 0;
          			foreach($sqlhead as $rowdtls){
						$aprowcnt++;

						foreach($dparticdet as $rssxz){
							if($rowdtls['capvno']==$rssxz['ctranno'] && $rowdtls['cacctno']==$rssxz['cacctno']){
								$totamt = $rssxz['ntotamt'];
								$dueamt = $rssxz['ntotdue'];
							}

						}

            			$tottopay = $tottopay + $rowdtls['npayable'];
				?>

				<tr>
					<td align="center" class="tdpadx"><?=$rowdtls['capvno']?></td> 
					<td align="right" class="tdpadx tdright" nowrap>
						<?php
							if(isset($totsAPEWT[$rowdtls['capvno']])){
								echo number_format((floatval($totamt) + floatval($totsAPEWT[$rowdtls['capvno']])),2);
							}
						?>
					</td>					
					<td align="right" class="tdpadx tdright" nowrap>
						<?php
							if(isset($totsAPVAT[$rowdtls['capvno']])){
								if(floatval($totsAPVAT[$rowdtls['capvno']])!=0) {
									echo number_format( $totsAPVAT[$rowdtls['capvno']],2);
								}else{
									echo "-";
								}
							}else{
								"-";
							}
						?>
					</td>
					<td align="right" class="tdpadx tdright" nowrap>
						<?php
							if(isset($totsAPEWT[$rowdtls['capvno']])){
								if(floatval($totsAPEWT[$rowdtls['capvno']])!=0) {
									echo number_format( $totsAPEWT[$rowdtls['capvno']],2);
								}else{
									echo "-";
								}
							}else{
								"-";
							}
						?>
					</td>
					<td align="right" class="tdpadx tdright" nowrap>
					<?php
						//if(floatval($dueamt) == floatval($tottopay)){

							//echo "<span><font size=\"2\">".number_format($dueamt,2)."</font></span>";

						//}else{

							echo number_format($rowdtls['npayable'],2);

						//}
					?>
							
					</td>
					
				</tr>



				<?php 
					}
					if($aprowcnt > 1){
				?>
				<tr>
					<td align="right" class="tdpadx tdright" colspan="5">
						<b><span ><font size="2"><?php echo number_format($tottopay,2);?> </font></span></b>
					</td>						
				</tr>
				<?php
					}
				?>

			</table>

			<!--
        <div style="padding-top: 10px !important;"><b>Entry</b></div>
        <table border="0" width="60%" style="border-collapse: collapse;">  
          <tr>
            <th style="border: 1px solid" class="tdpadx">Account</th>
            <th style="border: 1px solid" class="tdpadx">Debit</th>
            <th style="border: 1px solid" class="tdpadx">Credit</th>
          </tr>

          <?php
					
          $ntotdebit = 0;
          $xsql = "select A.cacctno, A.cacctdesc, A.npayable as namt
          from rfp_t A
          where A.compcode='$company' and A.ctranno = '$csalesno' 
          Group By A.cacctno, A.cacctdesc";

					//echo $xsql;
					$forpay = 0;
          $sqlhead = mysqli_query($con,$xsql);
          if (mysqli_num_rows($sqlhead)!=0) {
            while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
              if(floatval($row['namt']) != 0){
								$forpay = $forpay + floatval($row['namt']);
					
          ?> 

          <tr>
            <td> <?//=$row['cacctdesc']?> </td>
            <td align="right" class="tdpadx tdright" nowrap><?php// echo number_format($row['namt'],2);?></td>
            <td>&nbsp;</td>
          </tr>
          <?php
              }
            }
          }
          ?>

          <tr>
            <td><?//=$Bankacctdesc?></td>
            <td>&nbsp;</td>
            <td align="right" class="tdpadx tdright" nowrap><?php// echo number_format($forpay,2);?></td>
          </tr>

        </table>
				-->
      <div style="padding-top: 20px !important"><i>Amount in words: </i> <?=strtoupper(numberTowords($tottopay));?></div>
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
					$tsentapp = "";

					$tpad0 = "";
					$tpad1 = "";
					$tpad2 = "";
					$tpad3 = "";

					$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, c.cdesignation, c.cusersign from rfp_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.crfpno = '$csalesno' order by a.nlevel");

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