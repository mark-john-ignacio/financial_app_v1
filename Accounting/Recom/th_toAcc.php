<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$typ = $_REQUEST['type'];
	$tran = $_REQUEST['tran'];

$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	$xvatcode = $row["compvat"];
	$xcomp = $row["lcompute"];
}

function getDefAcct($id){
	global $company;
	global $con;

	$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno where A.compcode='$company' and A.ccode='$id'");
	if (mysqli_num_rows($sqldefacc)!=0) {
		$rowdefacc = mysqli_fetch_assoc($sqldefacc);
		
		$array["id"] = $rowdefacc["cacctno"];
		$array["name"] = $rowdefacc["cacctdesc"];
		
		return $array;
	}

}

//GET POST DATE
$sqlpostdte = mysqli_query($con,"Select A.ddate from logfile A where A.compcode='$company' and ctranno='$tran' and cevent in ('POSTED','AUTO POST') order by ddate desc");
if (mysqli_num_rows($sqlpostdte)!=0) {
	$rowdte = mysqli_fetch_assoc($sqlpostdte);
	$dtepost = $rowdte["ddate"];
}


	
	//Delete muna existing if meron pra iwas double;
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$tran'");
	 
	if($typ=="RR"){
		
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','RR','$tran',B.dreceived,A.cacctcode,C.cacctdesc,SUM(A.namount),0,0,'$dtepost' From receive_t A left join receive B on A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran' group by B.dreceived,A.cacctcode,C.cacctdesc")){
			echo "False";
		}
		else{

			//get Supplier Entry
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','RR','$tran',A.dreceived,A.ccustacctcode,B.cacctdesc,0,A.ngross,0,'$dtepost' From receive A left join accounts B on  A.compcode=B.compcode and A.ccustacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{
				echo "True";
			}
		}
		
	


	}


	if($typ=="PRet"){
		
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','PR','$tran',B.dreturned,A.cacctcode,C.cacctdesc,0,SUM(A.namount),0,'$dtepost' From purchreturn_t A left join purchreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran' group by B.dreturned,A.cacctcode,C.cacctdesc")){
			echo "False";
		}
		else{

			//get Supplier Entry
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','PR','$tran',A.dreturned,A.ccustacctcode,B.cacctdesc,A.ngross,0,0,'$dtepost' From purchreturn A left join accounts B on  A.compcode=B.compcode and A.ccustacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
				echo "False";
			}
			else{
				echo "True";
			}
		}
		
	


	}

	
	else if($typ=="DR"){
		
		//Insert DR Side (costcode)
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DR','$tran',B.dcutdate,A.cacctcost,C.cacctdesc,SUM(D.ncost),0,0,'$dtepost' From dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcost=C.cacctno left join ( Select citemno, sum(ntotqty*ncost) as ncost from tblinvout where compcode='$company' and ctranno='$tran' ) D on A.citemno = D.citemno where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcost,C.cacctdesc")){
			echo "False";
		}
		else{
					
				//Insert CR Side (drcode)	
				if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DR','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,0,SUM(D.ncost),0,'$dtepost' From dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join ( Select citemno, sum(ntotqty*ncost) as ncost from tblinvout where compcode='$company' and ctranno='$tran' ) D on A.citemno = D.citemno where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
					echo "False";
				}
				else{
								
					echo "True";
				}

		}
		
	}
	
	else if($typ=="SI"){

			//get Item entry
			global $con;
			global $compcode;
			global $xcomp;		
		
			//get Customer Entry
			
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,B.cacctdesc,A.ngross,0,0,'$dtepost' From sales A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
			
			echo "False";
		}
		else{
		
		//Items Entry	
		
		 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,'$dtepost' 
		From (
			Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,4) Else SUM(A.namount) END as cCredit
			From sales_t A 
			left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
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
					Select B.dcutdate, A.citemno, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 4) AS nVat
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
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
					
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','SI','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',0,".$rowvat["nVat"].",0, '$dtepost')")){
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
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,0,ROUND(SUM(A.namount),4),0,'$dtepost' From sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
				echo "False";
			}
			else{
				echo "True";
			}
		
		 }

		}


	}//if($typ=="SI")
	
	else if($typ=="PV"){
				
		//Accounts Payable -> supplier account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'PV', '$tran', A.dcvdate, A.ccustacctcode, B.cacctdesc, A.ngross, 0, 0, '$dtepost' From paybill A left join accounts B on A.compcode=B.compcode and A.ccustacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran' ")){
				echo "False";
			}
			else{
					//Accounts Payable -> supplier account -> Credit
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'PV', '$tran', A.dcvdate, A.cacctno, B.cacctdesc, 0, A.ngross, 0, '$dtepost' From paybill A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno where A.compcode='$company' and A.ctranno='$tran' ")){
						echo "False";
					}
					else{
						echo "True";
					}
				
			}

	}
	else if($typ=="JE"){
				
		//Accounts Payable -> supplier account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'JE', '$tran', B.djdate, A.cacctno, A.ctitle, A.ndebit, A.ncredit, 0, '$dtepost' From journal_t A left join journal B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran' ")){
				echo "False";
			}
			else{
				echo "True";
				
			}

	}
	else if($typ=="OR"){
				
		//OR -> Customer account -> Credit
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'OR', '$tran', C.dcutdate, A.cacctno, B.cacctdesc, 0, sum(A.namount) as namount, 0, NOW() From receipt_sales_t A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid join receipt C on A.compcode=C.compcode and A.ctranno=C.ctranno where A.compcode='$company' and A.ctranno='$tran'  Group by C.dcutdate, A.cacctno, B.cacctdesc ")){
			echo "False";
		}
		else{
				//OR -> Deposit account -> Debit
				if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'OR', '$tran', A.dcutdate, A.cacctcode, B.cacctdesc, A.namount, 0, 0, NOW() From receipt A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid where A.compcode='$company' and A.ctranno='$tran'")){
					echo "False";
				}
				else{
					echo "True";
				}
			
		}

	}
	else if($typ=="BD"){
				
		//Bank Deposit -> Debit Account -> Debit
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'BD', '$tran', A.dcutdate, A.cacctcode, B.cacctdesc, A.namount, 0, 0, '$dtepost' From deposit A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran' ")){
				echo "False";
			}
			else{
					//ORs in details -> debit account -> Credit
					
					$sqlbd = "Select A.ctranno, B.dcutdate, C.cacctcode, D.cacctdesc, C.namount From deposit_t A left join deposit B on A.compcode=B.compcode and A.ctranno=B.ctranno left join receipt C on A.compcode=C.compcode and A.corno=C.ctranno left join accounts D on C.compcode=D.compcode and C.cacctcode=D.cacctno where A.compcode='$company' and A.ctranno='$tran'";


					$resbd = mysqli_query($con,$sqlbd);
					$isok = "True";
					if (mysqli_num_rows($resbd)!=0) {
						while($rowbd = mysqli_fetch_array($resbd, MYSQLI_ASSOC)){
							
							if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','BD','$tran','".$rowbd ["dcutdate"]."','".$rowbd ["cacctcode"]."','".$rowbd ["cacctdesc"]."',0,".$rowbd ["namount"].",0, '$dtepost')")){
								echo "False";
								$isok = "False";
							}
						
						}
						
						echo $isok;
					}else{
						echo "True";
					}
									
			}

	}
	else if($typ=="DM"){ //Ginaya lng sa invoice

			//get Item entry
			global $con;
			global $compcode;
			global $xcomp;		
		
			//get Customer Entry
			
			//Select '$company','DM','$tran',A.dcutdate,B.cacctno,B.cacctdesc,A.ngross,0,0,NOW() From aradj A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.compcode='$company' and A.ctranno='$tran'
			
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',D.dcutdate,B.cacctcode,C.cacctdesc,Sum(A.namount),0,0,'$dtepost' From aradj_t A left join aradj D on A.compcode=D.compcode and A.ctranno=D.ctranno left join sales B on A.compcode=B.compcode and A.creference=B.ctranno left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
			
			echo "False";
		}
		else{
		
		//Items Entry	
		
		 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,'$dtepost' 
		From (
			Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,4) Else SUM(A.namount) END as cCredit
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
					Select B.dcutdate, A.citemno, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 4) AS nVat
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
					
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','DM','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',0,".$rowvat["nVat"].",0, '$dtepost')")){
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
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','DM','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,0,ROUND(SUM(A.namount),4),0,'$dtepost' From aradj_t A left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
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
			
		if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',D.dcutdate,B.cacctcode,C.cacctdesc,0,Sum(A.namount),0,'$dtepost' From aradj_t A left join aradj D on A.compcode=D.compcode and A.ctranno=D.ctranno left join sales B on A.compcode=B.compcode and A.creference=B.ctranno left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno where A.compcode='$company' and A.ctranno='$tran'")){
			
			echo "False";
		}
		else{
		
		//Items Entry	
		
		 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,Sum(A.cCredit),0,0,'$dtepost' 
		From (
			Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,4) Else SUM(A.namount) END as cCredit
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
					Select B.dcutdate, A.citemno, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 4) AS nVat
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
					
					if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','CM','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',".$rowvat["nVat"].",0,0, '$dtepost')")){
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
			
			if (!mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','CM','$tran',B.dcutdate,A.cacctcode,C.cacctdesc,ROUND(SUM(A.namount),4),0,0,'$dtepost' From aradj_t A left join aradj B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.compcode='$company' and A.ctranno='$tran' group by B.dcutdate,A.cacctcode,C.cacctdesc")){
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
