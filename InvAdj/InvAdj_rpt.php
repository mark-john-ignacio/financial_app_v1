<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
  <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">    
 <script type="text/javascript" src="../js/jquery-1.10.22.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  
  
<style>
table tr.activerow{
    background: #3CF;
}
</style>
  <script type="text/javascript">
$(document).ready(function(){
    $("#MyTable").find("input").on("focus", function(){
      $(this).closest("tr").addClass("activerow");
    }).on("blur", function() {
      $(this).closest("tr").removeClass("activerow");
    })
});


function keyz(valz,str,keyCode){
	
	
	var numberPattern = /\d+/g;
	var r = str.match(numberPattern);

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;


	if(valz==""){
		document.getElementById("txtnqtyact"+r).value=0;
	}


	
	if(keyCode==38 || keyCode==40){
		//alert(keyCode)
		if(keyCode==38 && r!=1){
			//alert(r);
			var z = parseFloat(r) - parseFloat(1);
			document.getElementById("txtnqtyact"+z).focus();
			
		}
		
		if(keyCode==40 && r!=lastRow){
			//alert(r);
			var z = parseFloat(r) + parseFloat(1);
			//alert(z);
			document.getElementById("txtnqtyact"+z).focus();
		}
		
	}
	
	var oldqty = document.getElementById("txtnqty"+r).value;
	
	
	var diff = parseFloat(valz) - parseFloat(oldqty);
	if(diff != 0){
		document.getElementById("txtdiff"+r).value = diff;
	}
}

function isNumber(keyCode) {
	return ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 189 || keyCode == 37 || keyCode == 110 || keyCode == 190 || keyCode == 39 || (keyCode >= 96 && keyCode <= 105 || keyCode == 9))
}

function chkform(){
	document.getElementById("frmpos").submit();
}

function prt(x){
	location.href="InvAdj_rptxls.php?txtctranno="+x;
}
</script>
</head>

<body style="padding-left:20px; padding-right:20px; padding-top:10px">
<?php
$varcode = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];
$sql = "Select * From adjustments where compcode='$company' and ctrancode='$varcode'";
$result=mysqli_query($con,$sql);

if (mysqli_num_rows($result)!=0) {
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$lcancelled = $row['lcancelled'];
		$lapproved = $row['lapproved'];
	}
}
?>
<form action="InvAdj_edit.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
<div class="hidden-print">
<div style="float:left">
    <b>TRANSACTION CODE: </b> <?php echo $_REQUEST['txtctranno'];?>
    <input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $_REQUEST['txtctranno'];?>">
</div>
<div style="float:right">
    <img src="../images/toexcel.png" width="30" height="30" style="cursor:pointer" title="Export To Excel" onClick="prt('<?php echo $_REQUEST['txtctranno'];?>');"> 
    &nbsp; | &nbsp; 
	<img src="../images/print2.png" width="30" height="30" style="cursor:pointer" title="Print Transaction" onClick="window.print();"> 
    
    <?php
	$varx = "";
		if(intval($lcancelled)==intval(1)){
			$varx = "Cancelled";
		}
		if(intval($lapproved)==intval(1)){
			$varx = "Posted";
		}
	
	
	if($varx==""){
	?>
    &nbsp; | &nbsp; <img src="../images/edit3.png" width="30" height="30" onClick="return chkform();" title="Edit Transaction" style="cursor:pointer"> 
    <?php
	}
	else {
		echo " | ".$varx;
	}
	?>
</div>
</div>

            <table id="MyTable" class="table table-condensed">

                     <tr>
                    	<td colspan="7" align="center" bgcolor="#999999"><b>WITHOUT ADJUSTMENTS</b></td>
                    </tr>
 					<tr>
                   		<th width="150">Classification</th>
						<th width="150">Code</th>
						<th>Description</th>
                        <th width="100">UOM</th>
						<th width="100">Qty</th>
						<th width="150">Actual Count</th>
                        <th width="150">Adjustment</th>
					</tr>
                   <?php 
					
					$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, d.cunit, a.nqty, a.nactual, a.nadj
					From adjustments_t a
					right join items d on a.citemno=d.cpartno
					left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
					where a.compcode='001' and a.ctrancode='$varcode' and a.nadj = 0
					order by d.cclass, a.citemno";
					//echo $sql;
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					}
					
					$result=mysqli_query($con,$sql);
					
					$cntr = 0;
					$varclass1 = "";
					$varclass2 = "";
					$vartitle = "";
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$cntr = $cntr + 1;
					
					$varclass1 = $row['cdesc'];
					
						if($varclass1!=$varclass2){
							$vartitle = $varclass1;
						}
						else{
							$vartitle = "";
						}	
	
					?>
                    <tr id="tr<?php echo $cntr; ?>">
                    	<td class="vert-align"><b><?php echo $vartitle;?></b></td>
                    	<td class="vert-align"><?php echo $row['citemno'];?></td>
                        <td class="vert-align" nowrap><?php echo $row['citemdesc'];?></td>
                        <td class="vert-align"><?php echo $row['cunit'];?></td>
                        <td align="right"><?php echo $row['nqty'];?></td>
                        <td align="right" ><?php echo $row['nactual'];?></td>
                        <td align="right" >&nbsp;</td>
                    </tr>
                    <?php
					
						$varclass2 = $row['cdesc'];
                    }
					?>
                   <tr>
                    	<td colspan="7" align="center" bgcolor="#999999"><b>WITH ADJUSTMENTS</b></td>
                    </tr>
					<tr>
                   		<th width="150">Classification</th>
						<th width="150">Code</th>
						<th>Description</th>
                        <th width="100">UOM</th>
						<th width="100">Qty</th>
						<th width="150">Actual Count</th>
                        <th width="150">Adjustment</th>
					</tr>
                    <?php 
					$varcode = $_REQUEST['txtctranno'];
					$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, d.cunit, a.nqty, a.nactual, a.nadj
					From adjustments_t a
					right join items d on a.citemno=d.cpartno
					left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
					where a.compcode='001' and a.ctrancode='$varcode' and nadj != 0 and a.nqty > 0
					order by d.cclass, a.citemno";
					$result=mysqli_query($con,$sql);
					
					$cntr = 0;
					$varclass1 = "";
					$varclass2 = "";
					$vartitle = "";
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$cntr = $cntr + 1;
					
					$varclass1 = $row['cdesc'];
					
						if($varclass1!=$varclass2){
							$vartitle = $varclass1;
						}
						else{
							$vartitle = "";
						}	
	
					?>
                    <tr id="tr<?php echo $cntr; ?>">
                    	<td class="vert-align"><b><?php echo $vartitle;?></b></td>
                    	<td class="vert-align"><?php echo $row['citemno'];?></td>
                        <td class="vert-align" nowrap><?php echo $row['citemdesc'];?></td>
                        <td class="vert-align"><?php echo $row['cunit'];?></td>
                        <td align="right"><?php echo $row['nqty'];?></td>
                        <td align="right" ><?php echo $row['nactual'];?></td>
                        <td align="right" ><?php echo $row['nadj'];?></td>
                    </tr>
                    <?php
					
						$varclass2 = $row['cdesc'];
                    }
					?>
                    <tr>
                    	<td colspan="7" align="center" bgcolor="#999999"><b>NEGATIVE VALUES</b></td>
                    </tr>
 					<tr>
                   		<th width="150">Classification</th>
						<th width="150">Code</th>
						<th>Description</th>
                        <th width="100">UOM</th>
						<th width="100">Qty</th>
						<th width="150">Actual Count</th>
                        <th width="150">Adjustment</th>
					</tr>
                 
                    <?php 
					$varcode = $_REQUEST['txtctranno'];
					$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, d.cunit, a.nqty, a.nactual, a.nadj
					From adjustments_t a
					right join items d on a.citemno=d.cpartno
					left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
					where a.compcode='001' and a.ctrancode='$varcode' and nadj != 0 and a.nqty < 0
					order by d.cclass, a.citemno";
					$result=mysqli_query($con,$sql);
					
					$cntr = 0;
					$varclass1 = "";
					$varclass2 = "";
					$vartitle = "";
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$cntr = $cntr + 1;
					
					$varclass1 = $row['cdesc'];
					
						if($varclass1!=$varclass2){
							$vartitle = $varclass1;
						}
						else{
							$vartitle = "";
						}	
	
					?>
                    <tr id="tr<?php echo $cntr; ?>">
                    	<td class="vert-align"><b><?php echo $vartitle;?></b></td>
                    	<td class="vert-align"><?php echo $row['citemno'];?></td>
                        <td class="vert-align" nowrap><?php echo $row['citemdesc'];?></td>
                        <td class="vert-align"><?php echo $row['cunit'];?></td>
                        <td align="right"><?php echo $row['nqty'];?></td>
                        <td align="right" ><?php echo $row['nactual'];?></td>
                        <td align="right" ><?php echo $row['nadj'];?></td>
                    </tr>
                    <?php
					
						$varclass2 = $row['cdesc'];
                    }
					?>


                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="<?php echo $cntr; ?>"> 
       
    </td>

  </tr>
</table>

    </fieldset>
    
        <div id="light" class="white_content">

</form>
</body>
</html>