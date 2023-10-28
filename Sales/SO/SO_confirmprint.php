<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_SO'");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
		{
			$autopost = $rowauto['cvalue'];
		}
	}

	
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
	
	$csalesno = $_REQUEST['x'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname from so a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$TranDate = $row['ddate'];
		$Date = $row['dcutdate']; 
		$PODate = $row['dpodate']; 
		//$SalesType = $row['csalestype'];
		$Gross = $row['ngross'];
		$PONos = $row['cpono'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="../../css/cssmed.css">
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<style type="text/css">
#tblMain {
/* the image you want to 'watermark' */
background-image: url(../../images/preview.png);
background-position: center;
background-size: contain;
background-repeat: no-repeat;
}

@media print {
.noPrint {
    display:none;
}
}
#menu{
	position: fixed;
	padding-top:0px 0px 0px 0px;
	top: 0px;
	height:30px;
	width:98%;
	border-style:solid;
	background-color:#9FF;
  border:1px solid black;
  opacity:1.0;
}
html, body {
	top:0px;
} 


</style>
<head>
<script type="text/javascript">
function Print(x){

					$.ajax ({
						url: "SO_Tran.php",
						data: { x: x, typ: "POST" },
						async: false,
						dataType: "json",
						success: function( data ) {
							console.log(data);
							$.each(data,function(index,item){
								
								itmstat = item.stat;
								
								if(itmstat!="False"){

									window.parent.document.getElementById("hdnposted").value = 1;
									window.parent.document.getElementById("salesstat").innerHTML = "POSTED";
									window.parent.document.getElementById("salesstat").style.color = "#FF0000";
									window.parent.document.getElementById("salesstat").style.fontWeight = "bold";
				
								}
							});
						}
					});
				
				location.href = "SO_print.php?x="+x;
}

function PrintRed(x){
	location.href = "SO_print.php?x="+x;
}

</script>
</head>

<body>
<br><br>
<table width="100%" border="0" cellpadding="3" style="border-collapse:collapse;" id="tblMain">
  <tr>
    <td colspan="3" align="center"><font size="2"><b>SALES ORDER</b></font></td>
  </tr>
  <tr>
    <td width="100"><b>Customer: </b></td>
    <td><?php echo $CustName;?></td>
		<td rowspan="2" align="center"><b>PO Date</b> <br><br> <?php echo date_format(date_create($PODate),"M d, Y");?></td>
  </tr>
  <tr>
    <td width="100"><b>Control No.: </b></td>
    <td><?=$PONos;?></td>
  </tr>

 
  <tr>
    <td colspan="3">
    
    <table width="100%" border="1" cellpadding="3">
				<tr>
          <th scope="col" height="30" width="20px">No.</th>
          <th scope="col" height="30" width="150px">PO No.</th>
          <th scope="col" height="30" width="150px">Part No.</th>
          <th scope="col" height="30">Item Description</th>
          <th style="text-align: center" scope="col" height="30">Qty</th>
          <th style="text-align: center" scope="col">Unit</th>
        </tr>
      <?php 
				$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from so_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno'");

				if (mysqli_num_rows($sqlbody)!=0) {
				$cntr = 0;
				while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
				$cntr = $cntr + 1;
								
			?>
      
			<tr>
          <td><?=$cntr?></td>
          <td><?php echo strtoupper($rowbody['citemremarks']);?></td>
          <td><?php echo strtoupper($rowbody['citemno']);?></td>
          <td><?php echo strtoupper($rowbody['citemdesc']);?></td>
          <td style="text-align: center"><?=number_format($rowbody['nqty']);?></td>
          <td style="text-align: center"><?php echo $rowbody['cunit'];?></td>
          
        </tr>
      <?php 
		}
		}
	  ?>
       

    </table></td>
  </tr>
</table>

<div align="center" id="menu" class="noPrint">
<div style="float:left;">&nbsp;&nbsp;<strong><font size="-1">Sales Order</font></strong></div>
<div style="float:right;">

<?php 
$strqry = "";
$valsub = "";

if($lPosted==0 && $autopost==1){
	$strqry = "Print('".$csalesno."')";
	$valsub = "PRINT AND POST SO";
}
else{
	$strqry = "PrintRed('".$csalesno."')";
	$valsub = "PRINT SO";
}


?>

<input type="button" value="<?php echo $valsub;?>" onClick="<?php echo $strqry;?>;" class="noPrint"/>

</div>
</div>

</body>
</html>