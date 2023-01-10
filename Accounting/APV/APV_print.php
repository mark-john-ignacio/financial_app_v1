<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
			$companytin = $rowcomp['comptin'];
		}

	}
	
	$cpono = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname,c.Fname,c.Lname,c.Minit from apv a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and a.ctranno = '$cpono'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cpaymentfor'];
		$Date = $row['dapvdate'];
		//$ChkNo = $row['cchkno'];
		
		$PreparedBy = $row['Lname'].", ".$row['Fname']." ".$row['Minit'];

		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lPrintPosted = $row['lprintposted'];
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<style type="text/css">
html,
	body {
		margin:0;
		padding:0;
		height:100%;
		font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	#container {
		min-height:100%;
		position:relative;
	}
	#header {
		padding:10px;
	}
	#body {
		padding:10px;
		padding-bottom:60px;	/* Height of the footer */
	}
	#footer {
		position:absolute;
		bottom:5px;
		width:100%;
	}
	/* other non-essential CSS */
	#header p,
	#header h1 {
		margin:0;
		padding:10px 0 0 10px;
	}
	#footer p {
		margin:0;
		padding:10px;
	}
	
	@page { size:8.5in 11in; margin: 0.5cm }
</style>

</head>

<body onLoad="window.print();">
<div id="container">
<div id="header">
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td><font size="3"><b><?php echo $companyname;?></b></font></td>
    <td colspan="2" align="center"><font size="3"><b>APV</b></font></td>
  </tr>
  <tr>
    <!--<td><font size="2"><b><?php// echo $companydesc;?></b></font></td>-->
    <td><font size="2"><b><?php echo $companyadd;?></b></font></td>
    <td width="100">Number:</td>
    <td width="150"><?php echo $cpono;?></td>
  </tr>
  <tr>
    <td><font size="2"><b>TIN #<?php echo $companytin;?></b></font></td>
    <td width="100">Date:</td>
    <td width="150"><?php echo $Date;?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">
    <?php
        	if($lPrintPosted==1){
				//echo "<font color='#FF0000'><b><i>ORIGINAL APV ALREADY PRINTED<i></b></font>";
			}
		?>
    </td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
</div>
<div id="body">
<table width="100%" border="0" cellpadding="2" cellspacing="0">
      <tr>
        <td>Account Code</td>
        <td>Account Description</td>
        <td>Debit</td>
        <td>Credit</td>
        <td>Sub</td>
        </tr>
      <tr>
        <td colspan="5"><div style="padding-left:100px">
        <br>
        <b>PARTICULARS:</b><br>
        <div style="padding-left:20px">
        <?php echo nl2br($Remarks);?>
        </div> </div>
        </td>
        </tr>
      <tr>
        <td colspan="5">&nbsp;</td>
      </tr>
      <?php
	  
	$sqlhead = mysqli_query($con,"select a.*,b.cname from apv_t a left join customers b on a.compcode=b.compcode and a.csubsidiary=b.cempid where a.compcode='$company' and a.ctranno = '$cpono'");
	
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){     
	  ?>
      <tr>
        <td><?php echo $row['cacctno']?></td>
        <td><?php echo $row['ctitle']?></td>
        <td><?php 
		if($row['ndebit']!=0){ 
			echo $row['ndebit'];
		}
		?></td>
        <td><?php 
		if($row['ncredit']!=0){ 
			echo $row['ncredit'];
		}
		?></td>
        <td><?php echo $row['csubsidiary']?></td>
      </tr>
      <?php
		}
	}
	  ?>

    </table>
</div>
<div id="footer">
    <div style="padding:20px">
        	<div style="float:left; width:30%;"><b>Prepared By:<br><br><br>&nbsp;&nbsp;&nbsp;</b><?php echo $PreparedBy;?></div>
            <div style="float:right; width:60%;" align="left"><b>Approved By:</b></div>
    </div
></div>
</div>

<?php
//POST RECORD
if($lPrintPosted==0){
	//mysqli_query($con,"Update apv set lprintposted=1, lapproved=1 where compcode='$company' and ctranno='$cpono'");
}

$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');
mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `cmachine`, `cremarks`) 
	values('$cpono','$preparedby',NOW(),'PRINTED','$compname','Printed Record')");

?>

<script type="text/javascript">
	//window.opener.document.getElementById("hdnposted").value = 1;
	//window.opener.document.getElementById("salesstat").innerHTML = "POSTED";
	//window.opener.document.getElementById("salesstat").style.color = "#FF0000";
	//window.opener.document.getElementById("salesstat").style.fontWeight = "bold";

</script>

</body>
</html>