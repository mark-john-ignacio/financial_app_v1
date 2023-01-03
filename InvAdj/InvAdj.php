<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvAdj_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

$company = $_SESSION['companyid'];

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

<form action="InvAdj_save.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
   
    <div class='col-xs-10 nopadwdown'>
        <div class="control-group">
        <div class="controls form-inline">
    <b>SELECT MONTH AND YEAR: </b>
    <select name="selm" id="selm" class="form-control">
        <option value="<?php echo date("m");?>"><?php echo strftime("%B");?></option>
    	<option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    
        <select name="sely" id="sely" class="form-control">
        <option value="<?php echo date("Y");?>"><?php echo date("Y");?></option>
    	<option value="<?php echo date("Y",strtotime("-1 year"));?>"><?php echo date("Y",strtotime("-1 year"));?></option>
		</select>
    </div>
    </div>
    </div>
    <div class='col-xs-7 nopadwdown'>
   <input type="text" name="txtrem" id="txtrem" value="" placeholder="Remarks..." class="form-control input-sm" maxlength="90">
   </div>
    <br>
            <table id="MyTable" class="table table-condensed-small">

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
					$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, d.cunit, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
					From tblinventory a
					right join items d on a.citemno=d.cpartno
					left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
					where a.compcode='$company'
					group by d.cclass, c.cdesc,a.citemno, d.citemdesc, d.cunit
					having COALESCE((Sum(nqtyin)-sum(nqtyout)),0) <> 0
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
                        <input type='hidden' value='<?php echo $row['nqty'];?>' name='txtnqty<?php echo $cntr; ?>' id='txtnqty<?php echo $cntr; ?>'>
                        <input type='hidden' value='<?php echo 0;?>' name='txtncost<?php echo $cntr; ?>' id='txtncost<?php echo $cntr; ?>'>
                        <input type='hidden' value='<?php echo 0;?>' name='txtnprice<?php echo $cntr; ?>' id='txtnprice<?php echo $cntr; ?>'>
                        </td>
                        <td class="vert-align">
						<?php
                        	$varqty = $row['nqty'];
							if($varqty < 0){
								$varqty = 0;
							}
							else{
								$varqty = $row['nqty'];
							}
                        ?>
                        <input type='text' value='<?php echo $varqty;?>' name='txtnqtyact<?php echo $cntr; ?>' id='txtnqtyact<?php echo $cntr; ?>' class='numeric form-control input-xs' style='text-align:right'>

                        </td>
                        <td class="vert-align">
                        <?php
                        	$vardiff = "";
							if($row['nqty'] < 0){
								$vardiff = 0 - $row['nqty'];
							}
							else{
								$vardiff = "";
							}
                        ?>

                        <input type='text' value='<?php echo $vardiff;?>' name='txtdiff<?php echo $cntr; ?>' id='txtdiff<?php echo $cntr; ?>' class='form-control input-xs' style='text-align:right' readonly>
                        </td>
                    </tr>
                    
                    <?php
                    }
					
					mysqli_close($con);
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
