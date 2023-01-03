<?php
if(!isset($_SESSION)){
	session_start();
}
include('../Connection/connection_string.php');
$company = $_SESSION['companyid'];

$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
if (mysqli_num_rows($sqlhead)!=0) {
	$row = mysqli_fetch_assoc($sqlhead);
	$xvatcode = $row["compvat"];
	$xcomp = $row["lcompute"];
}

function getDefAcct($id){
	global $company;

	$sqldefacc = mysqli_query($con,"Select A.cacctno, B.cacctdesc from accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctno where A.compcode='$company' and A.ccode='$id'");
	if (mysqli_num_rows($sqldefacc)!=0) {
		$rowdefacc = mysqli_fetch_assoc($sqlhead);
		
		$array["id"] = $rowdefacc["cacctno"];
		$array["name"] = $rowdefacc["cacctdesc"];
		
		return $array;
	}

}


function WRREntry($cwrrno){
	//get Item entry
	global $con;
	global $compcode;
	
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$cwrrno'");
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','WRR','$cwrrno',B.dcutdate,A.cacctcode,C.cacctdesc,SUM(A.namount),0,0,NOW() From receive_t A left join receive B on A.ctranno=B.ctranno left join accounts C on A.cacctcode=C.cacctno where A.ctranno='$cwrrno' group by B.dcutdate,A.cacctcode,C.cacctdesc");
	

	//get Supplier Entry
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','WRR','$cwrrno',A.dcutdate,A.ccustacctcode,B.cacctdesc,0,A.ngross,0,NOW() From receive A left join accounts B on A.ccustacctcode=B.cacctno where A.ctranno='$cwrrno'");

}


function SIEntry($cposno){
	//get Item entry
	global $con;
	global $compcode;
	global $xcomp;
	
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$cposno'");


	//get Customer Entry
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',A.dcutdate,A.cacctcode,B.cacctdesc,A.ngross,0,0,NOW() From sales A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctno where A.ctranno='$cposno'");

//Items Entry	

 if($xcomp==1){ // Pag ung mismo may ari system ay Vatable
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',A.dcutdate,A.cacctcode,A.cacctdesc,0,Sum(A.cCredit),0,NOW() 
From (
    Select B.dcutdate, A.citemno, A.cacctcode, C.cacctdesc, CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND(SUM(A.namount)/(1 + (D.nrate/100)) ,2) Else SUM(A.namount) END as cCredit
    From sales_t A 
    left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
    left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno 
    left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode 
    left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode 
    where A.compcode='$compcode' and A.ctranno='$cposno' 
    group by B.dcutdate,A.cacctcode,C.cacctdesc,A.citemno
) A Group By A.cacctcode");
	
	//VAT Entry
	//get Default SALES_VAT Code
	$Sales_Vat = getDefAcct("SALES_VAT");
	
	$SID = $Sales_Vat[0];
	$SNM = $Sales_Vat[1];
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',A.dcutdate,'$SID','$SNM',0, SUM(A.nVat)
From(
Select B.dcutdate,A.citemno,
CASE WHEN E.lcompute=1 OR D.nrate<>0 Then ROUND((SUM(A.namount)/(1 + (D.nrate/100))) * ((D.nrate/100)), 2) Else SUM(A.namount) END AS nVat
From sales_t A 
left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno
left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode
left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode
where A.ctranno='SI121700000'
group by A.citemno, B.dcutdate
    ) A");
	
	
	
 }
 else{ // pag nde vatable no VAT dapat
 	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','POS','$cposno',B.dcutdate,A.cacctcode,C.cacctdesc,0,ROUND(SUM(A.namount,2)),0,NOW() From sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno left join accounts C on A.compcode=C.compcode and A.cacctcode=C.cacctno left join taxcode D on A.compcode=D.compcode and A.ctaxcode=D.ctaxcode left join vatcode E on B.compcode=E.compcode and B.cvatcode=E.cvatcode where A.csalesno='$cposno' group by B.dcutdate,A.cacctcode,C.cacctdesc");

 }


}


function PurRetEntry($cwrrno){
	//get Item entry
	global $con;
	global $compcode;
	
	mysqli_query($con,"DELETE FROM `glactivity` where `ctranno` = '$cwrrno'");
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','PURCHRET','$cwrrno',B.dcutdate,A.cacctcode,C.cacctdesc,0,SUM(A.namount),0,NOW() From purchreturn_t A left join purchreturn B on A.ctranno=B.ctranno left join accounts C on A.cacctcode=C.cacctno where A.ctranno='$cwrrno' group by B.dcutdate,A.cacctcode,C.cacctdesc");
	

	//get Supplier Entry
	
	mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$compcode','PURCHRET','$cwrrno',A.dcutdate,A.ccustacctcode,B.cacctdesc,A.ngross,0,0,NOW() From purchreturn A left join accounts B on A.ccustacctcode=B.cacctno where A.ctranno='$cwrrno'");

}


?>
