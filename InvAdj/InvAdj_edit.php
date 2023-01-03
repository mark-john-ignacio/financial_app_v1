<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvAdj_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>

<style>
table tr.activerow{
    background: #3CF;
}
</style>


</head>

<body style="padding-left:20px; padding-right:20px; padding-top:10px">
<?php
$varcode = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];
$sql = "Select * From adjustments where compcode='$company' and ctrancode='$varcode'";
$result=mysqli_query($con,$sql);

if (mysqli_num_rows($result)!=0) {
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$cremarks = $row['cremarks'];
		$dmonth = $row['dmonth'];
		$dyear = $row['dyear'];
	}
}

?>
<form action="InvAdj_editsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $varcode ;?>">
        
        <div class='col-xs-10'>
        <div class="control-group">
        <div class="controls form-inline">
    <b>SELECT MONTH AND YEAR: </b>
    <select name="selm" id="selm" class="form-control">
    	<option value="01" <?php echo $dmonth=='01' ? 'selected' : '';?>>January</option>
        <option value="02" <?php echo $dmonth=='02' ? 'selected' : '';?>>February</option>
        <option value="03" <?php echo $dmonth=='03' ? 'selected' : '';?>>March</option>
        <option value="04" <?php echo $dmonth=='04' ? 'selected' : '';?>>April</option>
        <option value="05" <?php echo $dmonth=='05' ? 'selected' : '';?>>May</option>
        <option value="06" <?php echo $dmonth=='06' ? 'selected' : '';?>>June</option>
        <option value="07" <?php echo $dmonth=='07' ? 'selected' : '';?>>July</option>
        <option value="08" <?php echo $dmonth=='08' ? 'selected' : '';?>>August</option>
        <option value="09" <?php echo $dmonth=='09' ? 'selected' : '';?>>September</option>
        <option value="10" <?php echo $dmonth=='10' ? 'selected' : '';?>>October</option>
        <option value="11" <?php echo $dmonth=='11' ? 'selected' : '';?>>November</option>
        <option value="12" <?php echo $dmonth=='12' ? 'selected' : '';?>>December</option>
    </select>
    
        <select name="sely" id="sely" class="form-control">
            <option value="<?php echo $dyear;?>"><?php echo $dyear;?></option>
            <option value="<?php echo date("Y");?>"><?php echo date("Y");?></option>
            <option value="<?php echo date("Y",strtotime("-1 year"));?>"><?php echo date("Y",strtotime("-1 year"));?></option>
		</select>
    </div>
    </div>
    </div>
   <div class='col-xs-7' style="padding-top:10px;padding-bottom:10px;">
   <input type="text" name="txtrem" id="txtrem" value="<?php echo $cremarks;?>" placeholder="Remarks..." class="form-control input-sm" maxlength="90">
   </div>

    <br>
            <table id="MyTable" class="table table-condensed">

					<tr>
						<th width="150">Code</th>
						<th>Description</th>
                        <th width="100">UOM</th>
						<th width="100">Qty</th>
						<th width="150">Actual Count</th>
                        <th width="150">Adjustment</th>
					</tr>
					<tbody class="tbody">
                    <?php 
					$varcode = $_REQUEST['txtctranno'];
					$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, d.cunit, a.nqty, a.nactual, a.nadj
					From adjustments_t a
					right join items d on a.citemno=d.cpartno
					left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
					where a.compcode='001' and a.ctrancode='$varcode' 
					order by d.cclass, a.citemno";
					$result=mysqli_query($con,$sql);
					
					$cntr = 0;
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$cntr = $cntr + 1;
	
					?>
                    <tr id="tr<?php echo $cntr; ?>">
                    	<td class="vert-align">
                        <?php echo $row['citemno'];?>
                        <input type='hidden' value='<?php echo $row['citemno'];?>' name='txtcitemno<?php echo $cntr; ?>' id='txtcitemno<?php echo $cntr; ?>'>
                        </td>
                        <td class="vert-align">
                        <?php echo $row['citemdesc'];?>
                        <input type='hidden' value='<?php echo $row['citemdesc'];?>' name='txtcdesc<?php echo $cntr; ?>' id='txtcdesc<?php echo $cntr; ?>'>
                        </td>
                        <td class="vert-align">
                         <?php echo $row['cunit'];?>
                        <input type='hidden' value='<?php echo $row['cunit'];?>' name='txtcunit<?php echo $cntr; ?>' id='txtcunit<?php echo $cntr; ?>'>                       
                        </td>
                        <td align="right" class="vert-align"><?php echo $row['nqty'];?>
                        <input type='hidden' value='<?php echo $row['nqty'];?>' name='txtnqty<?php echo $cntr; ?>' id='txtnqty<?php echo $cntr; ?>'></td>
                        <td class="vert-align">
                        <input type='text' value='<?php echo $row['nactual'];?>' name='txtnqtyact<?php echo $cntr; ?>' id='txtnqtyact<?php echo $cntr; ?>' class='numeric form-control input-xs' style='text-align:right'>

                        </td>
                        <td class="vert-align">
                        <?php
                        	$vardiff = $row['nadj'];
							if($vardiff == 0){
								$vardiff = "";
							}
							else{
								$vardiff = $row['nadj'];
							}
                        ?>

                        <input type='text' value='<?php echo $vardiff;?>' name='txtdiff<?php echo $cntr; ?>' id='txtdiff<?php echo $cntr; ?>' class='form-control input-xs' style='text-align:right' readonly>
                        </td>
                    </tr>
                    <?php
                    }
					?>
                    </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="<?php echo $cntr; ?>"> 
 
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(F2)    </button>
    
    
    
    </td>

  </tr>
</table>

    </fieldset>
    
        <div id="light" class="white_content">

</form>
</body>
</html>

<script type="text/javascript">
$(document).ready(function(){
    $("#MyTable").find("input").on("focus", function(){
      $(this).closest("tr").addClass("activerow");
    }).on("blur", function() {
      $(this).closest("tr").removeClass("activerow");
    })
	
	
	
	$("input.numeric").numeric();
		$("input.numeric").on("click, focus", function () {
		$(this).select();
	});
									
	$("input.numeric").on("keyup", function (e) {
		keyz($(this).val(), $(this).attr('name'));
		
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			var nme = $(this).attr("name");
			var r = nme.replace( /^\D+/g, '');
			
			if(e.keyCode==38 && r!=1){
				//alert(r);
				var z = parseInt(r) - 1;
				document.getElementById("txtnqtyact"+z).focus();
				
			}
			
			if(e.keyCode==40 && r!=lastRow){
				
				var z = parseInt(r) + 1;
				//alert(z);
				document.getElementById("txtnqtyact"+z).focus();
			}

	});

});


function keyz(valz,str){
	
	var numberPattern = /\d+/g;
	var r = str.match(numberPattern);
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	if(valz==""){
		document.getElementById("txtnqtyact"+r).value=0;
	}
	
	var oldqty = document.getElementById("txtnqty"+r).value;
	
	
	var diff = parseFloat(valz) - parseFloat(oldqty);
	if(diff != 0){
		document.getElementById("txtdiff"+r).value = diff;
	}
	else{
		document.getElementById("txtdiff"+r).value = "";
	}
}

function chkform(){
	document.getElementById("frmpos").submit();
}
</script>
