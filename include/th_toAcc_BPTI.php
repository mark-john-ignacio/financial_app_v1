<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['tran'];
	$typ = $_REQUEST['type'];

	$xsicpaytype = "";

$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	$xvatcode = $row["compvat"];
	$xcomp = ($row["compvat"]=="VAT_REG") ? 1 : 0;
}

//periodic or perpetual for DR entry
$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INVSYSTEM'"); 
								
if (mysqli_num_rows($result)!=0) {
	$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
	$invsystem= $all_course_data['cvalue']; 							
}

if($typ=="SI"){

	$sqlhead = mysqli_query($con,"Select B.cacctcodetype, A.cpaytype from sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid where A.compcode='$company' and A.ctranno='$tran'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cSIsalescodetype = $row["cacctcodetype"];
		$xsicpaytype =  $row["cpaytype"];
	}


}

if($typ=="IN"){

	$sqlhead = mysqli_query($con,"Select B.cacctcodetype from ntsales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid where A.compcode='$company' and A.ctranno='$tran'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cSIsalescodetype = $row["cacctcodetype"];
	}


}


function getDefAcct($id){
	global $company;
	global $con;

	$array = array();
	$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='$id'");
	if (mysqli_num_rows($sqldefacc)!=0) {
		$rowdefacc = mysqli_fetch_assoc($sqldefacc);
		
		$array["id"] = $rowdefacc["cacctno"];
		$array["name"] = $rowdefacc["cacctdesc"];
		
		return $array;
	}else{
		return $array;
	}

}

function getSetAcct($id){
	global $company;
	global $con;

	$sqldefacc = mysqli_query($con,"Select A.cvalue as cacctno, B.cacctdesc from parameters A left join accounts B on A.compcode=B.compcode and A.cvalue=B.cacctno where A.compcode='$company' and A.ccode='$id'");
	if (mysqli_num_rows($sqldefacc)!=0) {
		$rowdefacc = mysqli_fetch_assoc($sqldefacc);
		
		$array["id"] = $rowdefacc["cacctno"];
		$array["name"] = $rowdefacc["cacctdesc"];
		
		return $array;
	}

}

	
	//Delete muna existing if meron pra iwas double;
	mysqli_query($con,"DELETE FROM `glactivity` where compcode='$company' and `ctranno` = '$tran'");
	 
	if($typ=="RR"){
		
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','RR','$tran',B.dreceived,A.cacctcode,C.cacctdesc,SUM(A.namount),0,0,NOW() From receive_t A left join receive B on A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran' group by B.dreceived,A.cacctcode,C.cacctdesc")){
			echo "False";
		}
		else{

			//get Supplier Entry
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','RR','$tran',A.dreceived,A.ccustacctcode,B.cacctdesc,0,A.ngross,0,NOW() From receive A left join accounts B on  A.compcode=B.compcode and A.ccustacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{
				echo "True";
			}
		}
		
	


	}

	else if($typ=="PRet"){
		
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','PR','$tran',B.dreturned,A.cacctcode,C.cacctdesc,0,SUM(A.namount),0,NOW() From purchreturn_t A left join purchreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran' group by B.dreturned,A.cacctcode,C.cacctdesc")){
			echo "False";
		}
		else{

			//get Supplier Entry
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','PR','$tran',A.dreturned,A.ccustacctcode,B.cacctdesc,A.ngross,0,0,NOW() From purchreturn A left join accounts B on  A.compcode=B.compcode and A.ccustacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{
				echo "True";
			}
		}
		
	


	}

	else if($typ=="DR" && $invsystem=="perpetual"){
		
		//Insert DR Side (costcode)
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DR','$tran',B.dcutdate,X.cacctcodecog,C.cacctdesc,SUM(D.ncost),0,0,NOW() From dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno left join items X on A.citemno=X.cpartno and A.compcode=X.compcode left join accounts C on X.compcode=C.compcode and X.cacctcodecog=C.cacctno left join ( Select citemno, sum(ntotqty*ncost) as ncost from tblinvout where compcode='$company' and ctranno='$tran' ) D on A.citemno = D.citemno where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,X.cacctcodecog,C.cacctdesc")){
			echo "False";

		}
		else{
					
				//Insert CR Side (drcode)	
				if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DR','$tran',B.dcutdate,X.cacctcodedr,C.cacctdesc,0,SUM(D.ncost),0,NOW() From dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno left join items X on A.citemno=X.cpartno and A.compcode=X.compcode left join accounts C on X.compcode=C.compcode and X.cacctcodedr=C.cacctno left join ( Select citemno, sum(ntotqty*ncost) as ncost from tblinvout where compcode='$company' and ctranno='$tran' ) D on A.citemno = D.citemno where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
					echo "False";
				}
				else{
								
					echo "True";
				}

		}
		
	}

	// else if($typ == "POS" && $invsystem == "perpetual"){
	// 	if(!mysqli_query($con, "INSERT INTO `glactivity` (`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle, `ndebit`, `ncredit`, `lposted`, `dpostdate`) SELECT '$company', 'POS', '$tran', 'b.ddate, "))
	// }	
	
	else if($typ=="SI"){

		//get Item entry

		//Sales USD
		$qrySI = "INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select compcode, cmodule, ctranno, ddate, acctno, ctitle, ndebit, ncredit, lposted, dpostdate From sales_glactivity where compcode='$company' and ctranno='$tran'";
		
		if (!mysqli_query($con,$qrySI)){
			echo "False";
		}else{
			echo "True";
		}
	}
	
	else if($typ=="POS"){

		//get Item entry
		global $con;
		global $compcode;
		global $xcomp;		
		global $xsicpaytype;

		$x00 = getDefAcct('SALES_CASH');

		$SID = $x00["id"];
		$SNM = $x00["name"];

		$qrySI  = "INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','POS','$tran',A.ddate,'".$SID."','".$SNM."',sum(A.gross),0,0,NOW()
		From (
			Select DATE(B.ddate) as ddate, A.citemno, A.quantity, A.amount, A.gross 
			From pos_t A 
			left join pos B on A.compcode=B.compcode and A.tranno=B.tranno 
			where A.compcode='$company' and A.tranno='$tran'
		) A Group By A.ddate";
			
		if (!mysqli_query($con,$qrySI)){
			
			echo "False";
		}
		else{

			//DISCOUNTS ENTRY - ALSO DEBIT SIDE
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran', B.dcutdate,A.cacctno,C.cacctdesc,ROUND(SUM(A.namount*E.nqty),2),0,0,NOW() From sales_t_disc A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctno=C.cacctid left join sales_t E on A.compcode=E.compcode and A.ctranno=E.ctranno and A.citemnoident=E.nident where A.compcode='$company' and A.ctranno='$tran' Group By B.dcutdate,A.cacctno,C.cacctdesc")){
				echo "False";
				//echo mysqli_error($con);
				$isok = "False";
			}


			//Items Entry	CREDIT SIDE
			if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
				
				$dxsql = "INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,NOW() 
				From (
					Select B.dcutdate, A.citemno, C.cacctid as cacctcode, C.cacctdesc,A.nnetvat + (A.ndiscount*A.nqty) as cCredit
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='$tran' 
				) A Group By A.cacctcode";

				if (!mysqli_query($con,$dxsql)){
				
					echo "False";
				}
				else{
				//VAT Entry
				//get Default SALES_VAT Code
				$Sales_Vat = getDefAcct("SALES_VAT");

				$SID = $Sales_Vat["id"];
				$SNM = $Sales_Vat["name"];
				
				$sqlvat = "Select A.dcutdate, Sum(A.nVat) as nVat
					From (
						Select B.dcutdate, A.citemno, A.nlessvat AS nVat
						From sales_t A 
						left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
						where A.compcode='$company' and A.ctranno='$tran'
					) A HAVING Sum(A.nVat) <> 0";
					
					
					$resvat = mysqli_query($con,$sqlvat);
					$isok = "True";
					if (mysqli_num_rows($resvat)!=0) {
						while($rowvat = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){
							
							if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','SI','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',0,".$rowvat["nVat"].",0, NOW())")){
								echo "False";
								//echo mysqli_error($con);
								$isok = "False";
							}
						
						}
						
						echo $isok;
					}else{
						echo "True";
					}
				
				}
			}		
				
			else{ // pag nde vatable no VAT dapat
				
				if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',B.dcutdate,C.cacctid as cacctcode,C.cacctdesc,0,ROUND(SUM(A.namount),2),0,NOW() From sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,C.cacctid,C.cacctdesc")){
					echo "False";
				}
				else{
					echo "True";
				}
			
			}

		}


	}

	else if($typ=="APV"){
	
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','APV','$tranno',A.dapvdate,B.cacctno,B.ctitle,B.ndebit,B.ncredit,0,NOW() From apv A left join apv_t B on  A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
						echo "False";
					}
					else{
						echo "True";
					}
	
	
	}
	
	else if($typ=="PV"){

		$result = mysqli_query($con,"SELECT * FROM `paybill` WHERE compcode='$company' and ctranno='$tran'"); 
								
		if (mysqli_num_rows($result)!=0) {
			$all = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$varlnoapvref= $all['lnoapvref']; 							
		}

		if($varlnoapvref==0){
				
			//Accounts Payable -> supplier account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Select '$company', 'PV', '$tran', C.dcheckdate, A.cacctno, B.cacctdesc, A.napplied, 0, 0, NOW(), A.cewtcode From paybill_t A left join paybill C on A.compcode=C.compcode and A.ctranno=C.ctranno left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' ")){
				echo "False";
			}
			else{

				//Accounts Payable -> supplier account -> Credit
				if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'PV', '$tran', A.dcheckdate, A.cacctno, B.cacctdesc, 0, A.npaid, 0, NOW() From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' ")){
					echo "False";
				}				
				else{
					echo "True";
				}
				
			}

		}else{

			$witherr = 0;

			$sqlchk = mysqli_query($con,"Select A.*, C.dcheckdate, B.cacctdesc From paybill_t A left join paybill C on A.compcode=C.compcode and A.ctranno=C.ctranno left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran' Order By A.nident");
			while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

				$xscdesc = $row['cacctdesc'];
				if($row['entrytyp']=="Debit"){
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Values('$company','PV', '$tran', '".$row['dcheckdate']."', '".$row['cacctno']."', '".str_replace("'", "\\'",$xscdesc)."', '".$row['napplied']."', 0, 0, NOW(), '".$row['cewtcode']."') ")){
						$witherr = 1;

					}
				}else{
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Values('$company','PV', '$tran', '".$row['dcheckdate']."', '".$row['cacctno']."', '".str_replace("'", "\\'",$xscdesc)."', 0, '".$row['napplied']."', 0, NOW(), '".$row['cewtcode']."') ")){
						$witherr = 1;
					}
				}

			}

			//Credit Account BASE
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'PV', '$tran', A.dcheckdate, A.cacctno, B.cacctdesc, 0, A.npaid, 0, NOW() From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
				$witherr = 1;
			}

			if($witherr == 1){
				echo "False";
			}else{
				echo "True";
			}

		}

	}

	else if($typ=="JE"){
				
		//Accounts Payable -> supplier account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'JE', '$tran', B.djdate, A.cacctno, A.ctitle, A.ndebit, A.ncredit, 0, NOW() From journal_t A left join journal B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran' Order by A.nident")){
				echo "False";
			}
			else{
				echo "True";
			}

	}

	else if($typ=="OR"){
				
		$witherr = 0;
		$result = mysqli_query($con,"SELECT * FROM `receipt` WHERE compcode='$company' and ctranno='$tran'"); 
								
		if (mysqli_num_rows($result)!=0) {
			$all = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$varlnosiref= $all['lnosiref']; 							
		}

		if($varlnosiref==0){
		
			//OR -> Deposit account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'OR', '$tran', A.dcutdate, A.cacctcode, B.cacctdesc, A.namount, 0, 0, NOW() From receipt A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{
					
					//OR -> Customer account -> Credit
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'OR', '$tran', C.dcutdate, A.cacctno, B.cacctdesc, 0, sum(A.napplied) as namount, 0, NOW() From receipt_sales_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno where A.compcode='$company' and A.ctranno='$tran'  Group by C.dcutdate, A.cacctno, B.cacctdesc ")){
						echo "False";
					}
					else{
						echo "True";
					}
				
			}

		}else{

			//BASE DEBIT
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'OR', '$tran', A.dcutdate, A.cacctcode, B.cacctdesc, A.namount, 0, 0, NOW() From receipt A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{

				$sqlchk = mysqli_query($con,"Select A.*, C.dcutdate, B.cacctdesc From receipt_others_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno where A.compcode='$company' and A.ctranno='$tran' Order By A.nidentity");
				while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){

					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Values('$company','OR', '$tran', '".$row['dcutdate']."', '".$row['cacctno']."', '".$row['cacctdesc']."', '".$row['ndebit']."', '".$row['ncredit']."', 0, NOW()) ")){
						$witherr = 1;
					}


				}

				if($witherr == 1){
					echo "False";
				}else{
					echo "True";
				}

			}


		}

	}

	else if($typ=="BD"){
				
		//Bank Deposit -> Debit Account -> Debit

			//if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'BD', '$tran', A.dcutdate, A.cacctcode, B.cacctdesc, A.namount, 0, 0, NOW() From deposit A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran' ")){
				
			//}
		//	else{
					//ORs in details -> debit account -> Credit
					
					$sqlbd = "Select A.ctranno, B.dcutdate, C.cacctcode, D.cacctdesc, C.namount, B.cacctcode as cMainAcct, E.cacctdesc as cMainDesc, A.corno
					From deposit_t A 
					left join deposit B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join receipt C on A.compcode=C.compcode and A.corno=C.ctranno 
					left join accounts D on C.compcode=D.compcode and C.cacctcode=D.cacctid 
					left join accounts E on B.compcode=E.compcode and B.cacctcode=E.cacctid 
					where A.compcode='$company' and A.ctranno='$tran'";

					$resbd = mysqli_query($con,$sqlbd);
					$isok = "True";
					if (mysqli_num_rows($resbd)!=0) {
						while($rowbd = mysqli_fetch_array($resbd, MYSQLI_ASSOC)){
							
							if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`,`crefno`) values ('$company','BD','$tran','".$rowbd ["dcutdate"]."','".$rowbd ["cMainAcct"]."','".$rowbd ["cMainDesc"]."',".$rowbd ["namount"].",0,0, NOW(),'".$rowbd ["corno"]."')")){
								echo "False";
							}
							else{
								if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`,`crefno`) values ('$company','BD','$tran','".$rowbd ["dcutdate"]."','".$rowbd ["cacctcode"]."','".$rowbd ["cacctdesc"]."',0,".$rowbd ["namount"].",0, NOW(),'".$rowbd ["corno"]."')")){
									echo "False";
									$isok = "False";
								}

							}
						
						}
						
						echo $isok;
					}else{
						echo "True";
					}
									
		//	}

	}

	else if($typ=="DM"){ //Ginaya lng sa invoice

			//get Item entry
			global $con;
			global $compcode;
			global $xcomp;		
		
			//get Customer Entry
			
			//Select '$company','DM','$tran',A.dcutdate,B.cacctno,B.cacctdesc,A.ngross,0,0,NOW() From aradj A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'
			
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',D.dcutdate,B.cacctcode,C.cacctdesc,Sum(A.namount),0,0,NOW() From aradj_t A left join aradj D on A.compcode=D.compcode and A.ctranno=D.ctranno left join sales B on A.compcode=B.compcode and A.creference=B.ctranno left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
			
			echo "False";
		}
		else{
		
		//Items Entry	
		
		 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,NOW() 
		From (
			Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,2) Else SUM(A.namount) END as cCredit
			From aradj_t A 
			left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno 
			left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
			left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
			left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
			where A.compcode='$company' and A.ctranno='$tran' 
			group by B.dcutdate,A.cacctcode,C.cacctdesc,A.citemno
		) A Group By A.cacctcode")){
			
				echo "False";
			}
			else{
			//VAT Entry
			//get Default SALES_VAT Code
			$Sales_Vat = getDefAcct("SALES_VAT");

			$SID = $Sales_Vat["id"];
			$SNM = $Sales_Vat["name"];
			
			$sqlvat = "Select A.dcutdate, Sum(A.nVat) as nVat
				From (
					Select B.dcutdate, A.citemno, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) AS nVat
					From aradj_t A 
					left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='$tran'
					group by B.dcutdate, A.citemno
				) A HAVING Sum(A.nVat) <> 0";
				
				
			$resvat = mysqli_query($con,$sqlvat);
			$isok = "True";
			if (mysqli_num_rows($resvat)!=0) {
				while($rowvat = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){
					
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','DM','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',0,".$rowvat["nVat"].",0, NOW())")){
						echo "False";
						//echo mysqli_error($con);
						$isok = "False";
					}
				
				}
				
				echo $isok;
			}else{
				echo "True";
			}
			
			}
		 }		
			
		 else{ // pag nde vatable no VAT dapat
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,0,ROUND(SUM(A.namount),2),0,NOW() From aradj_t A left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
				echo "False";
			}
			else{
				echo "True";
			}
		
		 }

		}


	}//if($typ=="DM")
	
	else if($typ=="CM"){ //Ginaya lng sa invoice pero baliktad entry

			//get Item entry
			global $con;
			global $compcode;
			global $xcomp;		
		
			//get Customer Entry
			
			//Select '$company','DM','$tran',A.dcutdate,B.cacctno,B.cacctdesc,A.ngross,0,0,NOW() From aradj A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'
			
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',D.dcutdate,B.cacctcode,C.cacctdesc,0,Sum(A.namount),0,NOW() From aradj_t A left join aradj D on A.compcode=D.compcode and A.ctranno=D.ctranno left join sales B on A.compcode=B.compcode and A.creference=B.ctranno left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
			
			echo "False";
		}
		else{
		
		//Items Entry	
		
		 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,Sum(A.cCredit),0,0,NOW() 
		From (
			Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,2) Else SUM(A.namount) END as cCredit
			From aradj_t A 
			left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno 
			left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
			left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
			left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
			where A.compcode='$company' and A.ctranno='$tran' 
			group by B.dcutdate,A.cacctcode,C.cacctdesc,A.citemno
		) A Group By A.cacctcode")){
			
				echo "False";
			}
			else{
			//VAT Entry
			//get Default SALES_VAT Code
			$Sales_Vat = getDefAcct("SALES_VAT");

			$SID = $Sales_Vat["id"];
			$SNM = $Sales_Vat["name"];
			
			$sqlvat = "Select A.dcutdate, Sum(A.nVat) as nVat
				From (
					Select B.dcutdate, A.citemno, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) AS nVat
					From aradj_t A 
					left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='$tran'
					group by B.dcutdate, A.citemno
				) A HAVING Sum(A.nVat) <> 0";
				
				
			$resvat = mysqli_query($con,$sqlvat);
			$isok = "True";
			if (mysqli_num_rows($resvat)!=0) {
				while($rowvat = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){
					
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','CM','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',".$rowvat["nVat"].",0,0, NOW())")){
						echo "False";
						//echo mysqli_error($con);
						$isok = "False";
					}
				
				}
				
				echo $isok;
			}else{
				echo "True";
			}
			
			}
		 }		
			
		 else{ // pag nde vatable no VAT dapat
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,ROUND(SUM(A.namount),2),0,0,NOW() From aradj_t A left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
				echo "False";
			}
			else{
				echo "True";
			}
		
		 }

		}


	}//if($typ=="DM")

	
	//Iupdate ang Balance ng COA


	//$sqlacctup = mysqli_query($con,"Select A.cacctno, A.ndebit, A.ncredit from glactivity A where A.compcode='$company' and A.ctranno='$tran'");
	//if (mysqli_num_rows($sqlacctup)!=0) {
	//	while($rowaccnt = mysqli_fetch_array($sqlacctup, MYSQLI_ASSOC)){
			
	//			$AccntCode = $rowaccnt['cacctcode'];
	//			$nDebits = $rowaccnt['ndebit'];
	//			$nCredits = $rowaccnt['ncredit'];
				
				
	//			if($nDebits){
	//			}
	
	//	}
	//}

?>
