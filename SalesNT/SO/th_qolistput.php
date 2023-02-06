<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];

		$avail = $_REQUEST['itmbal'];
		$date1 = date("Y-m-d");
	/*
		if($avail==1){
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, 1 as nqty, a.nprice, a.namount, a.nbaseamount, a.cmainunit as qtyunit, 
			a.nfactor, ifnull(c.nqty,0) as totqty2 
			from quote_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(Select x.creference,x.citemno,sum(x.nqty) as nqty
				 From so_t x
				 left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno
				 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.citemno = '".$_REQUEST['itm']."'";
		}
		else{
			$sql = "select a.nident, a.ctranno, a.citemno as cpartno, b.citemdesc, a.cunit, a.nqty as totqty, a.nprice, a.namount, a.nbaseamount, a.cmainunit as qtyunit,
			a.nfactor, ifnull(c.nqty,0) as totqty2, ifnull(d.nqty,0) AS nqty
			from quote_t a 
			left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
			left join
				(Select x.creference,x.citemno,sum(x.nqty) as nqty
				 From so_t x
				 left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno
				 Where x.creference='".$_REQUEST['id']."' and y.lcancelled=0
				 group by x.creference,x.citemno
				 ) c on a.ctranno=c.creference and a.citemno=c.citemno
			left join 
				(
					select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit, X.citemno, X.nfactor
					From tblinventory X
					where X.compcode='$company' and X.dcutdate <= '$date1'
					Group by X.cmainunit, X.citemno
				) d on a.citemno=d.citemno
	
			WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."' and a.citemno = '".$_REQUEST['itm']."'";

		}
*/

//items list
	@$arritmdesc = array();
	$itmlst = mysqli_query ($con, "Select * from items where compcode='$company'");	
	if (mysqli_num_rows($itmlst)!=0){
		while($row = mysqli_fetch_array($itmlst, MYSQLI_ASSOC)){
			@$arritmdesc[$row['cpartno']]=$row['citemdesc'];
		}
	}

	//get all quotation
	$resq = mysqli_query ($con, "Select A.*, B.cvattype From quote_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno = '".$_REQUEST['id']."' and A.nident='".$_REQUEST['itm']."'");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrresq[]=$row;
		}
	}

	//get all existing SO
	@$arrinv = array();
	$resq = mysqli_query ($con, "Select creference, nrefident, citemno, sum(nqty) as nqty From so_t a left join so b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 group by creference, nrefident,citemno");
	if (mysqli_num_rows($resq)!=0){
		while($row = mysqli_fetch_array($resq, MYSQLI_ASSOC)){
			@$arrinv[]=$row;
		}
	}

	if($avail==0){
		//get items inventory
		@$arrinventiry = array();
		$resinv = mysqli_query ($con, "select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, X.cmainunit, X.citemno, X.nfactor From tblinventory X where X.compcode='$company' and X.dcutdate <= '$date1' Group by X.cmainunit, X.citemno");
		if (mysqli_num_rows($resinv)!=0){
			while($rowinv = mysqli_fetch_array($resinv, MYSQLI_ASSOC)){
				@$arrinventiry[]=$rowinv;
			}
		}
	}

	
	foreach($arrresq as $row){

		//Serach item if existing in SO
		$inarray = "No";
		$nqty2 = 0;
		foreach(@$arrinv as $rsibnv){
			if($row['ctranno']==$rsibnv['creference']){
				if($row['citemno']==$rsibnv['citemno'] && $row['nident']==$rsibnv['nrefident']){
					$nqty2 = $rsibnv['nqty']; 
				}
			}
		}

		//if for inventory cheking, search sa inventory if exist
		$availinvtory = 1;
		if($avail==0){
			foreach(@$arrinventiry as $rxinv){
				if($row['citemno']==$rxinv['citemno']){
					$availinvtory = $rxinv['nqty']; 
				}
			}
		}

		$nqty1 = $row['nqty'];
		
		 $json['id'] = $row['citemno'];
	   $json['desc'] = @$arritmdesc[$row['citemno']]; //$row['citemdesc'];	
		 $json['nqty'] = $availinvtory;
		 $json['totqty'] = $nqty1 - $nqty2;
		 $json['cqtyunit'] = $row['cmainunit'];
		 $json['cunit'] = $row['cunit'];
		 $json['nfactor'] = $row['nfactor'];
		 $json['nprice'] = $row['nprice'];
		 $json['nbaseamount'] = $row['nbaseamount'];
		 $json['namount'] = $row['namount'];	
		 $json['xref'] = $row['ctranno'];
		 $json['nident'] = $row['nident'];
		 $json['ctaxcode'] = ($row['cvattype']=="VatIn") ? "VT" : "NT";
		 $json2[] = $json;

	//}
	
	}


	echo json_encode($json2);


?>
