<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>

              	<?php
				$citemno = $_REQUEST['txtcitemno'];
				//echo $citemno;
				if($citemno <> ""){
					
					$sql = "select items.*, A1.cacctdesc as salescode, A2.cacctdesc as wrrcode from items LEFT JOIN accounts A1 ON (items.cacctcodesales = A1.cacctno) LEFT JOIN accounts A2 ON (items.cacctcodewrr = A2.cacctno) where items.cpartno='$citemno'";
				}else{
					header('Items.php');
					die();
				}
				
				$sqlhead=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				if (mysqli_num_rows($sqlhead)!=0) {
					while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

						$cItemNo = $row['cpartno'];
						$cBarNo= $row['cscancode'];
						$cItemDesc = $row['citemdesc'];
						$cUnit = $row['cunit'];
						$cClass = $row['cclass'];
						$cType = $row['ctype']; 
						$PurchCost = $row['npurchcost'];
						$RetCost = $row['nretailcost'];
						$Qty = $row['nqty'];
						$SalesCode = $row['cacctcodesales'];
						$WRRCode = $row['cacctcodewrr'];
						$Discount = $row['ndiscount'];
						$TaxRate = $row['ntax'];
						$Seltax = $row['ltaxinc'];
						$Status = $row['cstatus'];
						$MarkUp = $row['nmarkup'];
						
						$SalesCodeDesc = $row['salescode'];
						$WRRCodeDesc = $row['wrrcode'];

					}
				}
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

function disable(){
	var form = document.getElementById("ItemEdit");
	var elements = form.elements;
	
	for (var i = 0, len = elements.length; i < len; ++i) {
			elements[i].readOnly = true;
	
		if(elements[i].tagName === 'SELECT') {
			elements[i].disabled = true;
		}
	}

	document.getElementById("btnSave").disabled = true;	
	document.getElementById("btnEdit").disabled = false;
	document.getElementById("btnUndo").disabled = false;	
	document.getElementById("btnMain").disabled = false;
	document.getElementById("btnNew").disabled = false;	
}

function enabled(){
	var form = document.getElementById("ItemEdit");
	var elements = form.elements;
	for (var i = 0, len = elements.length; i < len; ++i) {
		elements[i].readOnly = false;

		if(elements[i].tagName === 'SELECT') {
			elements[i].disabled = false;
		}

	}
	
	document.getElementById("btnEdit").disabled = true;	
	document.getElementById("btnSave").disabled = false;
	document.getElementById("btnUndo").disabled = true;	
	document.getElementById("btnMain").disabled = true;
	document.getElementById("btnNew").disabled = true;	

}

function trans(x){
	window.location.href = "items_tran.php?q="+x+"&itmno="+document.getElementById("txtcitemno").value;
}


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


function popwin(id){
var page = 'uploadpicItms.php?id='+id;
var name = 'popwin';
var w = 300;
var h = 200;
var myleft = (screen.width)?(screen.width-w)/2:100;
var mytop = (screen.height)?(screen.height-h)/2:100;
var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
myPopup = window.open(page, name, setting);
return false;
}

</script>
</head>

<body style="padding:5px; height:700px" onLoad="disable();">
<form action="Items_editsave.php" name="ItemEdit" id="ItemEdit" method="post" onSubmit="addrowcnt();">
	<fieldset>
    	<legend>Item Details (<b>Status: <?php echo $Status; ?></b>)</legend>
<table width="100%" border="0">
  <tr>
    <td width="150" rowspan="5"><?php 
	if(!file_exists("../imgitm/".$citemno.".jpg")){
		$imgsrc = "../images/blueX.png";
	}
	else{
		$imgsrc = "../imgitm/".$citemno.".jpg";
	}
	?>
    <img src="<?php echo $imgsrc;?>" width="150" height="150">
    
    </td>
    <td width="200">&nbsp;<b>Item Code</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtcitemno" name="txtcitemno" tabindex="1" value="<?php echo $cItemNo;?>"></div><input type="hidden" id="txtcitemnoold" name="txtcitemnoold" value="<?php echo $cItemNo;?>"></td>
  </tr>
  <tr>
    <td width="200">&nbsp;<b>Bar Code</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtcbar" name="txtcbar" tabindex="1" value="<?php echo $cBarNo;?>"></div></td>
  </tr>
  <tr>
    <td width="200" height="37">&nbsp;<b>Description</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" value="<?php echo $cItemDesc ;?>" required /></div></td>
  </tr>
  <tr>
    <td width="200">&nbsp;<b>Unit</b></td>
    <td colspan="2" style="padding:2px">
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
            <option value="<?php echo $row['ccode'];?>" <?php if ($row['ccode']==$cUnit){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
            <?php
				}
				

			?>     
        </select>
   </div>
    </td>
  </tr>
  <tr>
    <td width="200">&nbsp;<b>Classification</b></td>
    <td colspan="2" style="padding:2px">
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
            <option value="<?php echo $row['ccode'];?>" <?php if ($row['ccode']==$cClass){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
            <?php
				}
				
			?>     
        </select>
   </div></td>
  </tr>
  <tr>
    <td width="150" align="center"><input type="button" name="btnupload" id="btnupload" value="UPLOAD IMAGE" onClick="popwin('<?php echo $cItemNo;?>');"></td>
    <td width="200">&nbsp;<b>Type</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-2">
        <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="5">
			<?php
		$sql = "select * from groupings where ctype='ITEMTYP' order by cdesc";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>   
            <option value="<?php echo $row['ccode'];?>" <?php if ($row['ccode']==$cType){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
            <?php
				}
				
				
			?>     
        </select>
   </div></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b>Unit Cost</b></td>
    <td width="200"  style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtnpurchcost" name="txtnpurchcost" tabindex="6" value="<?php echo $PurchCost;?>" required />
    </div></td>
    <td style="padding:2px" width="100"><b>Sales Acct</b></td>
    <td><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="9" value="<?php echo $SalesCodeDesc; ?>" required /></div> &nbsp;&nbsp;
      <input type="text" id="txtsalesacctD" name="txtsalesacctD" style="border:none; height:30px" readonly value="<?php echo $SalesCode; ?>"></td>
    </tr>
  <tr>
    <td><b>Retail Cost</b></td>
    <td width="200" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtnretcost" name="txtnretcost" tabindex="7" value="<?php echo $RetCost;?>" required />
    </div></td>
    <td width="100" style="padding:2px"><b>WRR Acct</b></td>
    <td><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtrracct" name="txtrracct" tabindex="10" value="<?php echo $WRRCodeDesc; ?>" required /></div> &nbsp;&nbsp;
      <input type="text" id="txtrracctD" name="txtrracctD" style="border:none; height:30px" readonly value="<?php echo $WRRCode; ?>"></td>
    </tr>
  <tr>
    <td><b>Stock on Hand</b></td>
    <td width="200" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtnqty" name="txtnqty" tabindex="8" value="<?php echo $Qty;?>" required />
    </div></td>
    <td width="100" style="padding:2px"><b>Taxable/Tax</b></td>
    <td><div class="col-xs-2">
      <select id="seltax" name="seltax" class="form-control input-sm selectpicker"  tabindex="11">
        <option value="1"  <?php if ($Seltax==1){ echo "selected"; } ?>>YES</option>
        <option value="0"  <?php if ($Seltax==0){ echo "selected"; } ?>>NO</option>
        </select>
    </div>
      <div class="col-xs-2">
        <input type="text" class="form-control input-sm" id="txtnTaxRate" name="txtnTaxRate" tabindex="12" value="<?php echo $TaxRate;?>" required />
    </div></td>
    </tr>
  <tr>
    <td style="padding:2px"><b>Discount (%)</b></td>
    <td width="200" style="padding:2px"><div class="col-xs-10">
      <input type="text" class="form-control input-sm" id="txtndiscount" name="txtndiscount" tabindex="8" value="<?php echo $Discount;?>" required />
    </div></td>
    <td width="100" style="padding:2px"><b>MarkUp (%)</b></td>
    <td><div class="col-xs-2">
      <input type="text" class="form-control input-sm" id="txtnMarkUp" name="txtnMarkUp" tabindex="12" value="<?php echo $MarkUp;?>" required />
    </div></td>
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
                    <?php 
						$sqlbody = mysqli_query($con,"select * from `items_factor` where `cpartno` = '$citemno'");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
						
					?>
		<tr>
          	<td>
            <div id='divselunit<?php echo $cntr; ?>'>
        <select id="selunit<?php echo $cntr; ?>" name="selunit<?php echo $cntr; ?>" class="form-control input-sm selectpicker">
			<?php
		$sql = "select * from groupings where ctype='ITMUNIT' order by cdesc";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>   
            <option value="<?php echo $row['ccode'];?>" <?php if ($row['ccode']==$rowbody['cunit']){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
            <?php
				}
			?>     
        </select>
   </div>
            </td>
            <td align="left" style="padding:1px;"><input type='text' class='form-control input-sm' id='txtfactor<?php echo $cntr; ?>' name='txtfactor<?php echo $cntr; ?>' value='<?php echo $rowbody['nfactor']; ?>' required></td>
            <td align="left" style="padding:1px;"><input type='text' class='form-control input-sm' id='txtpurch<?php echo $cntr; ?>' name='txtpurch<?php echo $cntr; ?>' value='<?php echo $rowbody['npurchcost'] ?>' required></td>
            <td align="left" style="padding:1px;"><input type='text' class='form-control input-sm' id='txtretail<?php echo $cntr; ?>' name='txtretail<?php echo $cntr; ?>' value='<?php echo $rowbody['nretailcost'] ?>' required></td>
            <td align="left" style="padding:1px;"><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr; ?>_delete' value='delete' onClick="deleteRow(this);"/></td>
          </tr>
          <?php
							}
						}
		  ?>
    </table>
</td>
  </tr>

</table>

<br>
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Items.php?f=';" id="btnMain" name="btnMain">
  <table align="center">
    <tr>
      <td><img src="../images/back.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Back to Main</td>
    </tr>
  </table>
</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Items_new.php';" id="btnNew" name="btnNew">
  <table align="center">
    <tr>
      <td><img src="../images/New.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>New</td>
    </tr>
  </table>
</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="trans('<?php echo $Status;?>');" id="btnUndo" name="btnUndo">
  <table align="center">
    <tr>
      <td><img src="../images/<?php echo $Status;?>.png" width="20" height="20"/></td>
    </tr>
    <tr>
    <td><?php 
	if($Status=="ACTIVE"){
		echo "INACTIVATE";
	}
	else{
		echo "ACTIVATE";
	}
	?></td>
    </tr>
  </table>

    </button>

    <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit">
   <table align="center">
    <tr>
      <td><img src="../images/edit2.png" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Edit</td>
    </tr>
  </table>
    </button>
    
    <button type="submit" class="btn btn-success btn-sm" tabindex="13" id="btnSave" name="btnSave">
   <table align="center">
    <tr>
      <td><img src="../images/diskette.jpg" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Save</td>
    </tr>
  </table>
    </button>

</fieldset>
</form>

<?php 
mysqli_close($con);
?>
</body>
</html>