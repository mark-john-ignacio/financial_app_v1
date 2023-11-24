<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');


$csalesno = $_REQUEST['txtctranno'];

$sqlhead = mysqli_query($con,"select a.* from savingshares a where a.ctranno = '$csalesno'");

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min34.css">
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    
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
	});


$(function(){
	
		$("#txtcustid1").keyup(function(event){
		if(event.keyCode == 13){
				
			$.ajax({
			type:'post',
			url:'get_customerid.php',
			data: 'c_id='+ $(this).val(),                 
			success: function(value){
				//alert(value);
				if(value!=""){
					var data = value.split(":");
					$('#txtcust1').val(data[0]);
					document.getElementById("txtnamount1").focus();
				}
				else{
					$('#txtcustid1').val("");
					$('#txtcust1').val("");
				}
			},
			error: function(){
				$('#txtcustid1').val("");
				$('#txtcust1').val("");
			}
			});

		}
		
	});

//Search Cust name
	$("#txtcust1").autocomplete("get_customer.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtcust1").result(function(event, data, formatted) {
		$("#txtcustid1").val(data[1]);
	});
	
	$("#txtnamount1").keyup(function(event){
	if(event.keyCode == 13){
		addrow();
	}
	});

	
});


function isNumber(keyCode) {
	return ((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode == 189 || keyCode == 37 || keyCode == 110 || keyCode == 190 || keyCode == 39 || (keyCode >= 96 && keyCode <= 105 || keyCode == 9))
}

function addrow(){

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTable').insertRow(-1);
	var u=a.insertCell(0);
		u.style.width = "150px";
		u.style.padding = "2px";
	var v=a.insertCell(1);
		v.style.width = "350px";
		v.style.padding = "2px";
	var w=a.insertCell(2);
		w.style.width = "150px";
		w.style.padding = "2px";
	var x=a.insertCell(3);

	u.innerHTML = "<input type='text' id='txtcustid"+lastRow+"' name='txtcustid"+lastRow+"' class='form-control input-sm' placeholder='Member Code...' tabindex='1'>";


	v.innerHTML = "<input type='text' name='txtcust"+lastRow+"' id='txtcust"+lastRow+"' class='form-control input-sm' tabindex='1' placeholder='Search Member Name...'>";


	w.innerHTML = "<input type='text' id='txtnamount"+lastRow+"' name='txtnamount"+lastRow+"' class='form-control input-sm' placeholder='Amount...' tabindex='1' onkeydown='return isNumber(event.keyCode)'>";

	x.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' value='delete' onClick=\"deleteRow(this);\"/>";
	
	document.getElementById("txtcustid"+lastRow).focus();


		$("#txtcustid"+lastRow).keyup(function(event){
			//alert("hello");
		if(event.keyCode == 13){
				
		$.ajax({
        type:'post',
        url:'get_customerid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){
			//alert(value);
			if(value!=""){
				$('#txtcust'+lastRow).val(value);
				document.getElementById("txtnamount"+lastRow).focus();
			}
			else{
				$('#txtcustid'+lastRow).val("");
				$('#txtcust'+lastRow).val("");
			}
		},
		error: function(){
			$('#txtcustid'+lastRow).val("");
			$('#txtcust'+lastRow).val("");
		}
		});

		}
		
	});

//Search Cust name
	$("#txtcust"+lastRow).autocomplete("get_customer.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtcust"+lastRow).result(function(event, data, formatted) {
		$("#txtcustid"+lastRow).val(data[1]);
	});
	
	$("#txtnamount"+lastRow).keyup(function(event){
	if(event.keyCode == 13){
		addrow();
	}
	});



}

function deleteRow(r){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 document.getElementById('hdnrowcnt').value = lastRow - 2;
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;

	for (z=i+1; z<=lastRow; z++){
			var tempcustid = document.getElementById('txtcustid' + z);
			var tempcustname = document.getElementById('txtcust' + z);
			var tempamt= document.getElementById('txtnamount' + z);
			var tempdel= document.getElementById("row_" + z + "_delete");

			var x = z-1;
			tempcustid.id = "txtcustid" + x;
			tempcustid.name = "txtcustid" + x;
			tempcustname.id = "txtcust" + x;
			tempcustname.name = "txtcust" + x;
			tempamt.id = "txtnamount" + x;
			tempamt.name = "txtnamount" + x;
			
			tempdel.id = "row_" + x + "_delete";

	}
}

function chkform(x){
		
	var ISOK = "YES";
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

//alert(lastRow);

	if(lastRow>=1){
	var msgz = "";		
		for (z=1; z<=lastRow; z++){
				if(document.getElementById("txtcustid"+z).value == ""){
				
					msgz = msgz + "\n Blank member ID: row " + z;
				
				}
				if(document.getElementById("txtcust"+z).value == ""){
					msgz = msgz + "\n Blank member name: row " + z;
				}
				
				if(document.getElementById("txtnamount"+z).value == ""){
					msgz = msgz + "\n Blank amount: row " + z;
				}
				
				if(document.getElementById("txtnamount"+z).value == 0){
					msgz = msgz + "\n Zero amount: row " + z;
				}
		}
	}


		if(msgz!=""){
			alert("Details Error: "+msgz);
			return false;
			ISOK = "NO";
		}


	if(ISOK=="YES"){
	    document.getElementById("hdnrowcnt").value = lastRow;
		document.getElementById("frmpos").submit();
	}
	
}

function disabled(){

	document.getElementById("txtremarks").disabled = true;
	document.getElementById("seltype").disabled = true;
	document.getElementById("selcut").disabled = true;

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	for (z=1; z<=lastRow; z++){
		document.getElementById("txtcustid"+z).disabled = true;
		document.getElementById("txtcust"+z).disabled = true;
		document.getElementById("txtnamount"+z).disabled = true;
		document.getElementById("row_"+z+"_delete").className = "btn btn-danger btn-xs disabled";
	}

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Shares_edit.php";
		document.getElementById(frm).submit();
	}
}

function enabled(){

	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{


		document.getElementById("txtctranno").value = document.getElementById("hdnctranno").value;
		document.getElementById("txtctranno").readOnly = true;

		document.getElementById("txtremarks").disabled = false;
		document.getElementById("seltype").disabled = false;
		document.getElementById("selcut").disabled = false;
	
		var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
		var lastRow = tbl.length-1;
	
		for (z=1; z<=lastRow; z++){
			document.getElementById("txtcustid"+z).disabled = false;
			document.getElementById("txtcust"+z).disabled = false;
			document.getElementById("txtnamount"+z).disabled = false;
			document.getElementById("row_"+z+"_delete").className = "btn btn-danger btn-xs";
		}

	}

}
</script>

  <style type='text/css'>

.deleterow{cursor:pointer}

		.container{
			width: 800px;
			margin: 0 auto;
		}



		ul.tabs{
			margin: 0px;
			padding: 0px;
			list-style: none;
		}
		ul.tabs li{
			background: none;
			color: #222;
			display: inline-block;
			padding: 10px 15px;
			cursor: pointer;
		}

		ul.tabs li.current{
			background: #ededed;
			color: #222;
		}

		.tab-content{
			display: none;
			background: #ffffff;
			padding: 15px;
		}

		.tab-content.current{
			display: inherit;
		}

  </style>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtremarks').focus(); disabled();">
<?php

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$cType = $row['ctype'];
		$cCutCode = $row['cutcode'];
		$cRemarks = $row['cremarks'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
	}

?>
<form action="Shares_editsave.php" name="frmpos" id="frmpos" method="post" onSubmit="return false;">
	<fieldset>
    	<legend>Shares &amp; Savings</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>Trans No.:</tH>
    <td colspan="2" style="padding:2px;">
      <div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $csalesno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
      
      <input type="hidden" name="hdnctranno" id="hdnctranno" value="<?php echo $csalesno;?>">
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
    </td>
    <tH align="center"><div id="salesstat" style="text-align:center">
      <?php
	if($lCancelled==1){
		echo "<font color='#FF0000'><b>CANCELLED</b></font>";
	}
	
	if($lPosted==1){
		echo "<font color='#FF0000'><b>POSTED</b></font>";
	}
	?>
    </div></tH>
    </tr>
  <tr>
    <tH width="150">Transaction Type:</tH>
    <td style="padding:2px;" width="500">
        <div class="col-xs-5">
        <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="1">
          <option value="SHARES" <?php if($cType=="SHARES"){ echo "selected"; }?>>SHARES</option>
          <option value="SAVINGS" <?php if($cType=="SAVINGS"){ echo "selected"; }?>>SAVINGS</option>
        </select>
   </div>

    </td>
    <tH width="100"><span style="padding:2px">Cut Off Date:</span></tH>
    <td style="padding:2px;">
            <div class="col-xs-8">
        <select id="selcut" name="selcut" class="form-control input-sm selectpicker"  tabindex="2">
			<?php
		$sql = "select num, dfrom, dto from cutcodes where posted=0 order by num";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>   
            <option value="<?php echo $row['num'];?>" <?php if($cCutCode==$row['num']){ echo "selected"; }?>><?php echo date_format(date_create($row["dfrom"]),'m/d/Y');?> - <?php echo date_format(date_create($row["dto"]),'m/d/Y')?></option>
            <?php
				}
				

			?>     
        </select>
   </div>


    </td>
  </tr>
  <tr>
    <tH width="150" valign="top">Remarks:</tH>
    <td colspan="3" valign="top" style="padding:2px"><div class="col-xs-10">
      <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks" tabindex="3"><?php echo $cRemarks;?></textarea>
    </div></td>
    </tr>
  </table>
<br>

         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	<input type="hidden" name="hdnrowcnt" id="hdnrowcnt">
            <table id="MyTable" class="MyTable" cellpadding"3px" width="100%" border="0">

					<tr>
						<th style="border-bottom:1px solid #999" colspan="2">Name</th>
                        <th style="border-bottom:1px solid #999">Amount</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                    
                    
                    <?php
                    	$sqlbody = mysqli_query($con,"select a.*,b.cname from savingshares_t a left join customers b on a.ccode=b.cempid where a.ctranno = '$csalesno' order by a.nidentity");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;

					?>
                    <tr>
 						<td style="padding:2px" width="150">
        	<input type="text" id="txtcustid<?php echo $cntr;?>" name="txtcustid<?php echo $cntr;?>" class="form-control input-sm" placeholder="Member Code..." tabindex="1" value="<?php echo $rowbody["ccode"];?>">
                      
                        </td>
                        <td style="padding:2px" width="350">
        	<input type="text" class="form-control input-sm" id="txtcust<?php echo $cntr;?>" name="txtcust<?php echo $cntr;?>" tabindex="1" placeholder="Search Member Name..." value="<?php echo $rowbody["cname"];?>">

                        </td>
                        <td style="padding:2px" width="150">
                        <input type="text" id="txtnamount<?php echo $cntr;?>" name="txtnamount<?php echo $cntr;?>" class="form-control input-sm" placeholder="Amount..." tabindex="1" onkeydown="return isNumber(event.keyCode)" value="<?php echo $rowbody["namount"];?>" style="text-align:right">
                        
                        </td>
                        <td><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick="deleteRow(this);"/></td>
                     </tr>
                     <?php
                     
							}
						}?>   
                   </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">    
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Shares.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Shares_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(F3)
    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(F8)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(F2)    </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>
</form>
<?php

}
else{
?>
<form action="Shares_edit.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Shares &amp; Savings</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100"> Trans No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $csalesno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>TRANSACTION DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>

</body>
</html>