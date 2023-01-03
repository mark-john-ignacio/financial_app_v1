<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');



    
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ABOVECL'"); 

	if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		$nallowval = $all_course_data["cvalue"];
	}		


	$result1 = mysqli_query($con,"SELECT dcutdate FROM `sales` order By csalesno desc Limit 1"); 

	if (mysqli_num_rows($result1)!=0) {
	 $all_course_data1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
	 
		$ndcutdate = $all_course_data1["dcutdate"];
	}		

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css">
    
  <script type="text/javascript" src="../js/jquery.js"></script>
  
  <script type="text/javascript" src="lib/js/bootstrap-select.js"></script>
  <script src="../js/bootstrap.min.js"></script>


<script type='text/javascript' src='../js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />


<script language="javascript" type="text/javascript" src="../js/datetimepicker.js"></script>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 113) { //F2
		return chkform();
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		  window.location.replace("POS.php?f=");
	  }
	});


$(function(){
	
//Search Cust name
	$("#txtcust").autocomplete("get_loancustomer.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtcust").result(function(event, data, formatted) {
		$("#txtcustid").val(data[1]);
		$('#txtcdept').val(data[2]);
	});


	$('#txtAmount, #txtDeduct').keyup(function() {
		var qty = parseFloat($('#txtAmount').val());
		var price = parseFloat($('#txtDeduct').val());
		
		//alert(qty+"-"+price);
		
		if(isNaN(qty)==false && isNaN(price)==false){

		var totmonth = qty / price;
		var totmonth = totmonth / 2;

			if(totmonth % 1 != 0){
				var xyzmonth = parseInt(totmonth)+0.5;
				$('#txtmonths').val(xyzmonth);
				
				
				var x = qty - ((parseInt(totmonth)*2) * price);
				$("#statmsgz").html("Last CutOff Deduction Amt: " + x);
			}
			else{
				var xyzmonth = totmonth;
				$('#txtmonths').val(xyzmonth);
			}
			
			$("#statmsgz2").html(xyzmonth*2+" CUTOFF");
		
		}
		
	});

});



function chkform(){
	document.getElementById("frmpos").submit();
}
</script>

  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

    <style>
    .black_overlay{
        display: none;
        position: absolute;
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        background-color: black;
        z-index:1001;
        -moz-opacity: 0.5;
        opacity:.50;
        filter: alpha(opacity=50);
    }
    .white_content {
        display: none;
        position: absolute;
		top: 50%;
		left: 50%;
		  /* bring your own prefixes */
		transform: translate(-50%, -50%);
        width: 80%;
        height: 80%;
        padding: 5px;
        border: 5px solid SteelBlue ;
        background-color: white;
        z-index:1002;
        overflow: auto;
    }
</style>
</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="SharesDed_newsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Authorization to Deduct</legend>	
        <table width="100%" border="0">
  <tr>
    <tH width="100" rowspan="4" valign="top">
    <span style="padding:2px"><img src="../images/blueX.png" width="100" height="100" style="border:solid 1px  #06F;" name="imgemp" id="imgemp"></span>
    </tH>
    <tH width="120">&nbsp;CUSTOMER</tH>
    <td style="padding:2px">
              <input type="hidden" id="txtcustid" name="txtcustid">
            <div class="col-xs-10">
              <input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60">
              </div> 
    </td>
    <td style="padding:2px">&nbsp;<b>TOTAL AMOUNT</b></td>
    <td style="padding:2px"><div class="col-xs-7">
      <input type="text" class="form-control input-sm" id="txtAmount" name="txtAmount" width="20px" tabindex="2"  style="text-align:right; font-size:20px; font-weight:bold;">
    </div></td>
    </tr>
  <tr>
    <tH width="120">&nbsp;DEPARTMENT</tH>
    <td style="padding:2px"><div class="col-xs-10"><input type="text" class="form-control input-sm" id="txtcdept" name="txtcdept" width="20px" readonly></div></td>
    <td style="padding:2px" width="200">&nbsp;<b>Amt Per Deduction(cutoff)</b></td>
    <td style="padding:2px"><div class="col-xs-7">
      <input type="text" id="txtDeduct" name="txtDeduct" style="text-align:right; font-size:20px; font-weight:bold;" class="form-control" tabindex="3">
    </div></td>
    </tr>
  <tr>
    <tH>&nbsp;PURPOSE</tH>
    <td style="padding:2px"><div class="col-xs-10">
      <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="6">
        <option value="SHARES">ADDITIONAL SHARES</option>
        <option value="SAVINGS">DEPOSIT FOR SAVINGS ACCOUNT</option>
      </select>
    </div></td>
    <th><span style="padding:2px">&nbsp;<b>EFFECTIVITY CUTOFF</b></span></th>
    <td style="padding:2px;">
      <div class="col-xs-7">
        <select id="selcut" name="selcut" class="form-control input-sm selectpicker"  tabindex="4">
        	<option value="">- SELECT CUT OFF PERIOD - </option>
          <?php
		$sql = "select num, dfrom, dto from cutcodes Where posted=0 order by num";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>
          <option value="<?php echo $row['num'];?>"><?php echo date_format(date_create($row["dfrom"]),'m/d/Y');?> - <?php echo date_format(date_create($row["dto"]),'m/d/Y')?></option>
          <?php
				}
				

			?>
        </select>
      </div>
    </div></td> 
  </tr>
  <tr>
    <tH nowrap>&nbsp;</tH>
    <td style="padding:2px">&nbsp;</td>
    <td style="padding:2px">&nbsp;<b> # OF MONTHS</b></td>
    <td style="padding:2px"><div class="col-xs-2">
      <input type="text" class="form-control input-sm" id="txtmonths" name="txtmonths" width="20px" tabindex="5" value="1" style="text-align:right; font-size:20px; font-weight:bold;">
    </div>
      <div id="statmsgz2" style="display:inline; padding-left:10px;  font-size:20px"> 2 CUTOFF </div></td>
    </tr>
  <tr>
    <tH colspan="4">&nbsp;</tH>
    <tH><div id="statmsgz" style="display:inline; padding-left:10px;"></tH>
  </tr>
      </table>
        <br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> <button type="button" class="btn btn-success btn-sm" tabindex="7" onClick="return chkform();">SAVE<br> (F2)</button></td>
    </tr>
</table>

    </fieldset>
    
</form>

</body>
</html>