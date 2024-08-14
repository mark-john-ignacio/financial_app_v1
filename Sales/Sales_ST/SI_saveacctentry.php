<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['trancode'];

	$xcomp = "";
	$xsicpaytype = "";

	mysqli_query($con,"DELETE FROM `sales_glactivity` WHERE compcode='$company' and ctranno='$tran'"); 

	$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$xvatcode = $row["compvat"];
		$xcomp = ($row["compvat"]=="VAT_REG") ? 1 : 0;
	}

	$sidets = array();
	$result = mysqli_query($con,"SELECT * FROM `sales` WHERE compcode='$company' and ctranno='$tran'"); 								
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$sidets = $all_course_data; 							
	}

	function getDefAcct($id, $cdesc = ""){
		global $company;
		global $con;

		$iswhe = "";
		if($cdesc!=""){
			$iswhe = " and A.cdescription='$cdesc'";
		}
	
		$array = array();
		$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='$id'".$iswhe);
		if (mysqli_num_rows($sqldefacc)!=0) {
			$rowdefacc = mysqli_fetch_assoc($sqldefacc);
			
			$array["id"] = $rowdefacc["cacctno"];
			$array["name"] = $rowdefacc["cacctdesc"];
			
			return $array;
		}else{

			$array["id"] = "";
			$array["name"] = "";

			return $array;
		}
	
	}

	//Debit AR Account							
	//Sales USD
	$qrySI1 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,C.cacctid,C.cacctdesc,A.nbasegross,0,0,NOW() From sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid left join accounts C on B.compcode=C.compcode and B.cacctcodesales=C.cacctno where A.compcode='$company' and A.ctranno='$tran'";

	//Sales PHP
	if(floatval($sidets['nexchangerate']) > 1){
		$qrySI2 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,C.cacctid,C.cacctdesc,ROUND(A.ngross-A.nbasegross,2),0,0,NOW() From sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid left join accounts C on B.compcode=C.compcode and B.cacctcodesalesex=C.cacctno where A.compcode='$company' and A.ctranno='$tran'";
	}
	
	if (!mysqli_query($con,$qrySI1)){
		
		echo "False";
	}
	else{
		if(floatval($sidets['nexchangerate']) > 1){
			mysqli_query($con,$qrySI2);
		}

		//if may gross sales discount
		$Sales_Disc = getDefAcct("GROSS_SALES_DISCOUNT");
		$SID = $Sales_Disc["id"];
		$SNM = $Sales_Disc["name"];

		$qrySIDSC = "Select IFNULL(B.ngrossdisc,0) as namount, B.dcutdate
		From sales B where B.compcode='$company' and B.ctranno='$tran'";

		$resdsc= mysqli_query($con,$qrySIDSC);
		$isok = "True";
		if (mysqli_num_rows($resdsc)!=0) {
			while($rowdsc = mysqli_fetch_array($resdsc, MYSQLI_ASSOC)){

				if(floatval($rowdsc["namount"]) != 0){
				
					if (!mysqli_query($con,"INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) values ('$company','SI','$tran','".$rowdsc["dcutdate"]."','$SID','$SNM',".$rowdsc["namount"].",0,0, NOW())")){
						echo "False";
						//echo mysqli_error($con);
						$isok = "False";
					}

				}
			
			}
		
		}

		//EWT ENTRY - ALSO DEBIT SIDE
		$Sales_Ewt = getDefAcct("EWTREC");

		$SID = $Sales_Ewt["id"];
		$SNM = $Sales_Ewt["name"];

		$qrySIEWT = "Select B.dcutdate,C.cacctid,C.cacctdesc,B.cewtcode,sum(B.newt) as newtgross
		From sales B
		left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno 
		left join wtaxcodes D on B.compcode=D.compcode and B.cewtcode=D.ctaxcode 
		where B.compcode='$company' and B.ctranno='$tran'";

		$resewt = mysqli_query($con,$qrySIEWT);
		$isok = "True";
		if (mysqli_num_rows($resewt)!=0) {
			while($rowewt = mysqli_fetch_array($resewt, MYSQLI_ASSOC)){

				if(floatval($rowewt["newtgross"]) != 0){
				
					if (!mysqli_query($con,"INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) values ('$company','SI','$tran','".$rowewt["dcutdate"]."','$SID','$SNM',".$rowewt["newtgross"].",0,0, NOW(),'".$rowewt["cewtcode"]."')")){
						echo "False";
						//echo mysqli_error($con);
						$isok = "False";
					}

				}
			
			}
		
		}

		//Credit Side Sales
		$nicomeaccount = "";
		$ifGo = "True";
		$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INCOME_ACCOUNT'"); 								
		if (mysqli_num_rows($result)!=0) {
			$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
			$nicomeaccount = $all_course_data['cvalue']; 							
		}

		if($nicomeaccount=="customer"){

			//Sales USD
			$qrySI1 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,ROUND(Sum(A.cCredit),2),0,NOW() 
			From (
				Select B.dcutdate, A.citemno, F.cacctid as cacctcode, F.cacctdesc, CASE WHEN A.nrate<>0 Then ROUND(SUM(A.nbaseamount)/(1 + (A.nrate/100)) ,2) Else SUM(A.nbaseamount) END as cCredit
				From sales_t A 
				left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
				left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
				left join accounts F on C.compcode=F.compcode and C.cacctcodesalescr=F.cacctno
				where A.compcode='$company' and A.ctranno='$tran' 
				group by B.dcutdate,F.cacctid,F.cacctdesc,A.citemno
			) A Group By A.cacctcode";

			//Sales PHP
			if(floatval($sidets['nexchangerate']) > 1){
				$qrySI2 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,ROUND(Sum(A.cCredit),2),0,NOW() 
				From (
					Select B.dcutdate, A.citemno, F.cacctid as cacctcode, F.cacctdesc, CASE WHEN A.nrate<>0 Then ROUND((SUM(A.nbaseamount)/(1 + (A.nrate/100)) * B.nexchangerate) ,2) Else SUM(A.namount) END as cCredit
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
					left join accounts F on C.compcode=F.compcode and C.cacctcodesalescr=F.cacctno
					where A.compcode='$company' and A.ctranno='$tran' 
					group by B.dcutdate,F.cacctid,F.cacctdesc,A.citemno
				) A Group By A.cacctcode";
			}

			if (!mysqli_query($con,$qrySI1)){
				$ifGo = "False";
			}else{
				if(floatval($sidets['nexchangerate']) > 1){
					if (!mysqli_query($con,$qrySI2)){
						$ifGo = "False";
					}
				}
			}
		
		}elseif($nicomeaccount=="si"){

			$Sales_Disc = getDefAcct("INCOME_ACCOUNT", $sidets['cpaytype']);
			$SID = $Sales_Disc["id"];
			$SNM = $Sales_Disc["name"];

			//Sales USD
			$qrySI1 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,'$SID','$SNM',0,ROUND(sum(A.nnet+A.nexempt+A.nzerorated),2),0,NOW() From sales A where A.compcode='$company' and A.ctranno='$tran'";

			//Sales PHP
			if(floatval($sidets['nexchangerate']) > 1){
				$qrySI2 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,'$SID','$SNM',0,ROUND(((sum(A.nnet+A.nexempt+A.nzerorated)*A.nexchangerate) - (sum(A.nnet+A.nexempt+A.nzerorated))),2),0,NOW() From sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid left join accounts C on B.compcode=C.compcode and B.cacctcodesalescr=C.cacctno where A.compcode='$company' and A.ctranno='$tran'";
			}

			if (!mysqli_query($con,$qrySI1)){
				$ifGo = "False";
			}else{
				if(floatval($sidets['nexchangerate']) > 1){
					if (!mysqli_query($con,$qrySI2)){
						$ifGo = "False";
					}
				}
			}
		}elseif($nicomeaccount=="item"){

			//USD
			$qrySI1 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,NOW() 
			From (
				Select B.dcutdate, A.citemno, C.cacctid as cacctcode, C.cacctdesc, CASE WHEN D.nrate<>0 Then ROUND(SUM(A.nbaseamount)/(1 + (D.nrate/100)) ,2) Else ROUND(SUM(A.nbaseamount),2) END as cCredit
				From sales_t A 
				left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
				left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
				left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
				left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
				where A.compcode='$company' and A.ctranno='$tran' 
				group by B.dcutdate,C.cacctid,C.cacctdesc,A.citemno
			) A Group By A.cacctcode";

			//PHP
			if(floatval($sidets['nexchangerate']) > 1){
				$qrySI2 = "INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','SI','$tran',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,NOW() 
				From (
					Select B.dcutdate, A.citemno, C.cacctid as cacctcode, C.cacctdesc, CASE WHEN D.nrate<>0 Then ROUND((SUM(A.nbaseamount)/(1 + (D.nrate/100))*B.nexchangerate) ,2) Else ROUND(SUM(A.namount),2) END as cCredit
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='$tran' 
					group by B.dcutdate,C.cacctid,C.cacctdesc,A.citemno
				) A Group By A.cacctcode";
			}
				
			if (!mysqli_query($con,$qrySI1)){
				$ifGo = "False";
			}else{
				if(floatval($sidets['nexchangerate']) > 1){
					if (!mysqli_query($con,$qrySI2)){
						$ifGo = "False";
					}
				}
			}

		}


		if($ifGo == "True"){

			//check script ng VAT if tatama na ung kuha
			if(floatval($sidets['nvat']>0)){
				//VAT Entry
				//get Default SALES_VAT Code
				$Sales_Vat = getDefAcct("SALES_VAT");

				$SID = $Sales_Vat["id"];
				$SNM = $Sales_Vat["name"];
				
				$sqlvat = "Select A.dcutdate, A.ctaxcode, Sum(A.nVat) as nVat
				From (
					Select B.dcutdate, A.ctaxcode, ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) AS nVat
					From sales_t A 
					left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
					left join accounts C on B.compcode=C.compcode and B.cacctcode=C.cacctno 
					left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
					left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
					where A.compcode='$company' and A.ctranno='$tran'
					group by B.dcutdate, A.ctaxcode
				) A HAVING Sum(A.nVat) <> 0";
				
				
				$resvat = mysqli_query($con,$sqlvat);
				$isok = "True";
				if (mysqli_num_rows($resvat)!=0) {
					while($rowvat = mysqli_fetch_array($resvat, MYSQLI_ASSOC)){
						
						if (!mysqli_query($con,"INSERT INTO `sales_glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) values ('$company','SI','$tran','".$rowvat["dcutdate"]."','$SID','$SNM',0,".$rowvat["nVat"].",0, NOW(),'".$rowvat["ctaxcode"]."')")){
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

		//afetr neto baguhin ung nsa main th_toACCBPTI file to copy lng ung final entry sa glactivity table

	}