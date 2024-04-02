<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<form name="frmrecurr" id="frmrecurr" method="post" action="Quote_recurr.php">
	<input type="hidden" name="dtargetbill" id="dtargetbill" value="<?=$_POST['date_trans']?>"/>
</form>	

<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Quote_new.php";

	require_once "../../Connection/connection_string.php";

	require_once "../../include/denied.php";
	require_once "../../include/access2.php";

	//POST RECORD
	$company = $_SESSION['companyid'];
	$preparedby = $_SESSION['employeeid'];
	$compname = php_uname('n');

	$dDelDate = $_POST['date_trans'];

	$status = "True";

	function gettranno(){
		global $con;
		global $company;

		$dmonth = date("m");
		$dyear = date("y");

		$chkSales = mysqli_query($con,"select * from quote where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
		if (mysqli_num_rows($chkSales)==0) {
			$cSINo = "QO".$dmonth.$dyear."00000";
		}
		else {
			while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
				$lastSI = $row['ctranno'];
			}
			
			
			if(substr($lastSI,2,2) <> $dmonth){
				$cSINo = "QO".$dmonth.$dyear."00000";
			}
			else{
				$baseno = intval(substr($lastSI,6,5)) + 1;
				$zeros = 5 - strlen($baseno);
				$zeroadd = "";
				
				for($x = 1; $x <= $zeros; $x++){
					$zeroadd = $zeroadd."0";
				}
				
				$baseno = $zeroadd.$baseno;
				$cSINo = "QO".$dmonth.$dyear.$baseno;
			}
		}

		return $cSINo;
	}

	$alldet = array();
	$get=mysqli_query($con,"Select * From quote_t where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");
	while($row = mysqli_fetch_array($get, MYSQLI_ASSOC))
	{
		$alldet[$row['ctranno']][] = $row;
	}

	$alldetinfo = array();
	$get=mysqli_query($con,"Select * From quote_t_info where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");
	while($row = mysqli_fetch_array($get, MYSQLI_ASSOC))
	{
		$alldetinfo[$row['ctranno']][] = $row;
	}

	$status=="True";
	$result=mysqli_query($con,"Select * From quote where compcode='$company' and ctranno in ('".implode("','",$_POST["allbox"])."')");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dQuoteDate = $_POST['dt'.$row['ctranno']];
		$tran = gettranno();


		if (!mysqli_query($con,"INSERT INTO quote(`compcode`, `ctranno`, `ccode`, `cdelcode`, `ddate`, `ccontactname`, `ccontactdesig`, `ccontactdept`, `ccontactemail`, `ccontactsalut`, `cvattype`, `cterms`, `cdelinfo`, `cservinfo`, `dcutdate`, `ngross`, `nbasegross`, `cremarks`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `cpreparedby`, `csalestype`, `quotetype`, `crecurrtype`, `dtrandate`, `cacceptedby`,`cfrom_tranno`) Values('$company', '$tran', '".$row['ccode']."', '".$row['cdelcode']."', NOW(), '".$row['ccontactname']."', '".$row['ccontactdesig']."', '".$row['ccontactdept']."', '".$row['ccontactemail']."', '".str_replace("'","\'",$row['ccontactsalut'])."', '".$row['cvattype']."', '".$row['cterms']."', '".$row['cdelinfo']."', '".$row['cservinfo']."', STR_TO_DATE('$dQuoteDate', '%m/%d/%Y'), '".$row['ngross']."', '".$row['nbasegross']."', '".$row['cremarks']."', '".$row['ccurrencycode']."', '".$row['ccurrencydesc']."', '".$row['nexchangerate']."', '".$row['cpreparedby']."','".$row['csalestype']."', '".$row['quotetype']."', '".$row['crecurrtype']."', '$dDelDate', '".$row['cacceptedby']."','".$row['ctranno']."')")){
			$status = "False";	
		}else{


			mysqli_query($con,"UPDATE quote set lgen=1 Where compcode='$company' and ctranno='".$row['ctranno']."'");

			foreach($alldet[$row['ctranno']] as $row2){

				$refcidenttran = $company.$tran."P".$row2['nident'];

				if (!mysqli_query($con,"INSERT INTO quote_t(`compcode`, `cidentity`, `ctranno`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, nbaseamount , `cmainunit`,`nfactor`) values('$company', '$refcidenttran', '$tran', '".$row2['nident']."', '".$row2['citemno']."', '".$row2['nqty']."', '".$row2['cunit']."', '".$row2['nprice']."', '".$row2['namount']."', '".$row2['nbaseamount']."', '".$row2['cmainunit']."', ".$row2['nfactor'].")")){
					$status = "False";	
				}

			}

			if(isset($alldetinfo[$row['ctranno']])){
				foreach($alldetinfo[$row['ctranno']] as $row3){

					$refcidenttran = $company.$tran."P".$row3['nident'];
	
					if (!mysqli_query($con,"INSERT INTO quote_t_info(`compcode`, `cidentity`, `ctranno`, `nident`, `nrefident`, `citemno`, `cfldnme`, `cvalue`) values('$company', '$refcidenttran', '$tran', '".$row3['nident']."', '".$row3['nrefident']."', '".$row3['citemno']."', '".$row3['cfldnme']."', '".$row3['cvalue']."')")){
						$status = "False";	
					}
	
				}	
			}

		}
	}



			if($status=="True"){
?>

				<script>
					alert('Records Succesfully Generated');
					$("#frmrecurr").submit();
				</script>
<?php
			}else{
?>
				<script>
					alert('Error Generating transactions!');
					$("#frmrecurr").submit();
				</script>
<?php
			}
?>