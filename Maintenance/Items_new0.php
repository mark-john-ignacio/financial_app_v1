<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items_new.php";

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
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<script type="text/javascript" src="../js/jquery.js"></script>
    <script type='text/javascript' src='../js/jquery.autocomplete.js'></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />


<script type="text/javascript">
$(function(){
	
	$("#txtsalesacct").autocomplete("get_accnt.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtsalesacct").result(function(event, data, formatted) {
		$("#txtsalesacctD").val(data[1]);
	});


	$("#txtrracct").autocomplete("get_accnt.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtrracct").result(function(event, data, formatted) {
		$("#txtrracctD").val(data[1]);
	});

});


function addunitconv(){
	var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('myUnitTable').insertRow(-1);
	var u=a.insertCell(0);
	var v=a.insertCell(1);
	v.align = "left";
	v.style.padding = "1px";
	var w=a.insertCell(2);
	w.align = "left";
	w.style.padding = "1px";
	var x=a.insertCell(3);
	x.align = "left";
	x.style.padding = "1px";
	var y=a.insertCell(4);
	
	u.innerHTML = "<div id='divselunit"+lastRow+"'></div>";
	v.innerHTML = "<input type='text' class='form-control input-sm' id='txtfactor"+lastRow+"' name='txtfactor"+lastRow+"' value='1' required>";
	w.innerHTML = "<input type='text' class='form-control input-sm' id='txtpurch"+lastRow+"' name='txtpurch"+lastRow+"' value='0.00' required>";
	x.innerHTML = "<input type='text' class='form-control input-sm' id='txtretail"+lastRow+"' name='txtretail"+lastRow+"' value='0.00' required>";
	y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>";
	
	addselect(lastRow);
}

function addselect(nme){
        var xmlhttp;
        if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
          }
        else
          {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function()
        {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
            var res=xmlhttp.responseText;
            document.getElementById("divselunit"+nme).innerHTML=res;
            }
          }
        xmlhttp.open("GET","get_uom.php?x="+nme,true);
        xmlhttp.send();
        }
		
		
function deleteRow(r) {
	var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('myUnitTable').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempcitemno = document.getElementById('selunit' + z);
			var tempcdesc = document.getElementById('txtfactor' + z);
			var tempnqty= document.getElementById('txtpurch' + z);
			var tempcunit= document.getElementById('txtretail' + z);
			
			var x = z-1;
			tempcitemno.id = "selunit" + x;
			tempcitemno.name = "selunit" + x;
			tempcdesc.id = "txtfactor" + x;
			tempcdesc.name = "txtfactor" + x;
			tempnqty.id = "txtpurch" + x;
			tempnqty.name = "txtpurch" + x;
			tempcunit.id = "txtretail" + x;
			tempcunit.name = "txtretail" + x;

		}
}

function addrowcnt(){
	var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	document.getElementById('hdnunitrowcnt').value = lastRow;
	
}
</script>

<script type="text/javascript">
$(document).ready(function() {
    var x_timer;    
    $("#txtcitemno").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_username_ajax(user_name);
        }, 1000);
    });
	
	$("#txtcitemno").blur(function(){
    	if($("#user-result").html()=="CODE IN USE"){
			$("#txtcitemno").val("");	
		}
	});

function check_username_ajax(username){
    $("#user-result").html('<img src="ajax-loader.gif" />');
    $.post('itemcode_checker.php', {'username':username}, function(data) {
      $("#user-result").html(data);
    });
}
});
</script>
</head>

<body style="padding:5px; height:700px">
<form action="Items_newsave.php" name="frmITEM" id="frmITEM" method="post" onSubmit="addrowcnt();">
	<fieldset>
    	<legend>New Item</legend>
<table width="100%" border="0">
  <tr>
    <td width="150"><b>Item Code</b></td>
    <td colspan="3" style="padding:2px"><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtcitemno" name="txtcitemno" tabindex="1" placeholder="Input Item Code.." required /></div><span id="user-result"></span></td>
  </tr>
  <tr>
    <td><b>Description</b></td>
    <td colspan="3" style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Item Description.." required /></div></td>
  </tr>
  <tr>
    <td><b>Unit</b></td>
    <td colspan="3" style="padding:2px">
     <div class="col-xs-2">
        <select id="seluom" name="seluom" class="form-control input-sm selectpicker"  tabindex="3">
			<?php
		$sql = "select * from groupings where ctype='ITMUNIT' order by cdesc";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>   
            <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
            <?php
				}
				

			?>     
        </select>
   </div> <i>* smallest unit</i>
    </td>
  </tr>
  <tr>
    <td><b>Classification</b></td>
    <td colspan="3" style="padding:2px">
    <div class="col-xs-4">
        <select id="selclass" name="selclass" class="form-control input-sm selectpicker"  tabindex="4">
			<?php
		$sql = "select * from groupings where ctype='ITEMCLS' order by cdesc";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>   
            <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
            <?php
				}
				
				
			?>     
        </select>
   </div></td>
  </tr>
  <tr>
    <td><b>Type</b></td>
    <td colspan="3" style="padding:2px"><div class="col-xs-2">
        <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="5">
          <option value="GROCERY">GROCERY</option>
          <option value="CRIPPLES">CRIPPLES</option>
        </select>
   </div></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b>Unit Cost</b></td>
    <td style="padding:2px" width="200"><div class="col-xs-10"><input type="text" class="form-control input-sm" id="txtnpurchcost" name="txtnpurchcost" tabindex="6" value="0.00" required></div></td>
    <td width="110"><b>Sales Acct</b></td>
    <td><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="11" placeholder="Search Acct Title.." required></div> &nbsp;&nbsp;
        	<input type="text" id="txtsalesacctD" name="txtsalesacctD" style="border:none; height:30px" readonly></td>
  </tr>
  <tr>
    <td><b>Retail Cost</b></td>
    <td style="padding:2px"><div class="col-xs-10"><input type="text" class="form-control input-sm" id="txtnretcost" name="txtnretcost" tabindex="8" value="0.00" required></div></td>
    <td><b>WRR Acct</b></td>
    <td><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtrracct" name="txtrracct" tabindex="12" placeholder="Search Acct Title.." required></div> &nbsp;&nbsp;
      <input type="text" id="txtrracctD" name="txtrracctD" style="border:none; height:30px" readonly></td>
  </tr>
  <tr>
    <td><b>Stock on Hand</b></td>
    <td style="padding:2px"><div class="col-xs-10"><input type="text" class="form-control input-sm" id="txtnqty" name="txtnqty" tabindex="9" value="0.00" required></div></td>
    <td><b>Taxable</b></td>
    <td><div class="col-xs-2">
        <select id="seltax" name="seltax" class="form-control input-sm selectpicker"  tabindex="11">
          <option value="1">YES</option>
          <option value="0">NO</option>
        </select>
   </div></td>
  </tr>
  <tr>
    <td style="padding:2px"><b>Discount (%)</b></td>
    <td style="padding:2px"><div class="col-xs-10"><input type="text" class="form-control input-sm" id="txtndiscount" name="txtndiscount" tabindex="10" value="0.00" required></div></td>
    <td style="padding:2px"><b>Tax</b></td>
    <td><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtnTaxRate" name="txtnTaxRate" tabindex="13" value="0.00" required></div></td>
  </tr>
  <tr>
    <td colspan="4" style="padding:2px">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="4" style="padding:2px"><i><b>CONVERTION TABLE</b></i>
    <input type="button" value="Add Convertion" name="btnaddunit" id="btnaddunit" class="btn btn-primary btn-xs" onClick="addunitconv();">
    
    <input name="hdnunitrowcnt" id="hdnunitrowcnt" type="hidden" value="0">
    <br>
        <table width="50%" border="0" cellpadding="2" id="myUnitTable">
          <tr>
            <th scope="col">UNIT</th>
            <th scope="col">FACTOR<br><i>(qty/smallest unit)</i></th>
            <th scope="col">PURCHASE COST</th>
            <th scope="col">RETAIL COST</th>
            <th scope="col">&nbsp;</th>
          </tr>
    </table>
</td>
  </tr>
</table>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> <button type="submit" class="btn btn-success btn-sm" tabindex="14" name="button">SAVE NEW ITEM</button></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</fieldset>
</form>
</body>
</html>