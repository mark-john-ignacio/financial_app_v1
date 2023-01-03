<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=CoopInventoryAdj.xls");
header("Pragma: no-cache"); 	
header("Expires: 0");

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
      
</head>

<body>
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
					$varcode = $_REQUEST['txtctranno'];
					$company = $_SESSION['companyid'];
					
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
                    	<td class="vert-align">="<?php echo $row['citemno'];?>"</td>
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
                    	<td class="vert-align">="<?php echo $row['citemno'];?>"</td>
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
                    	<td class="vert-align">="<?php echo $row['citemno'];?>"</td>
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
</body>
</html>