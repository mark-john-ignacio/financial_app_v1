<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Suppliers_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
              	<?php
				$citemno = $_REQUEST['txtcitemno'];
				
				if($citemno <> ""){
					
					$sql = "select suppliers.*, A1.cacctdesc as salescode from suppliers LEFT JOIN accounts A1 ON (suppliers.cacctcode = A1.cacctno) where suppliers.ccode='$citemno'";
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

						$cCustCode = $row['ccode'];
						$cCustName = $row['cname'];
						$GroceryID = $row['cacctcode'];
						$GroceryDesc = $row['salescode'];
						//$CrippID = $row['cacctcodecripples'];
						//$CrippDesc = $row['crippcode'];
						$Status = $row['cstatus'];
						$Terms = $row['cterms'];
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
$(document).ready(function() {
    var x_timer;    
    $("#txtcitemno").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_username_ajax(user_name);
        }, 100);
    });
	
	$("#txtcitemno").blur(function(){
    	if($("#user-result").html()=="CODE IN USE"){
			$("#txtcitemno").val("");	
		}
	});

function check_username_ajax(username){
    $("#user-result").html('<img src="ajax-loader.gif" />');
    $.post('suppcode_checker.php', {'username':username}, function(data) {
      $("#user-result").html(data);
    });
}
});

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


	$("#txtcrippacct").autocomplete("get_accnt.php", {
		width: 260,
		matchContains: true,
		mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
	
	$("#txtcrippacct").result(function(event, data, formatted) {
		$("#txtcrippacctD").val(data[1]);
	});

});

function disable(){
	var form = document.getElementById("frmCust");
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
	var form = document.getElementById("frmCust");
	var elements = form.elements;
	for (var i = 0, len = elements.length; i < len; ++i) {
		
		if(elements[i].id!="txtcitemno"){
			elements[i].readOnly = false;
	
			if(elements[i].tagName === 'SELECT') {
				elements[i].disabled = false;
			}
		}

	}
	
	document.getElementById("btnEdit").disabled = true;	
	document.getElementById("btnSave").disabled = false;
	document.getElementById("btnUndo").disabled = true;	
	document.getElementById("btnMain").disabled = true;
	document.getElementById("btnNew").disabled = true;	

}

function trans(x){
	window.location.href = "Suppliers_tran.php?q="+x+"&itmno="+document.getElementById("txtcitemno").value;
}

function popwin(id){
var page = 'uploadsupp.php?id='+id;
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

<form action="Suppliers_editsave.php" name="frmCust" id="frmCust" method="post" onSubmit="addrowcnt();">
	<fieldset>
    	<legend>Suppliers Details  (<b>Status: <?php echo $Status; ?></b>)</legend>
<table width="100%" border="0">
  <tr>
    <td width='160' rowspan="5">
    <?php 
	if(!file_exists("../imgemps/".$citemno.".jpg")){
		$imgsrc = "../images/emp.jpg";
	}
	else{
		$imgsrc = "../imgemps/".$citemno.".jpg";
	}
	?>
    <img src="<?php echo $imgsrc;?>" width="150" height="150">
    </td>
    <td width="150"><b>Supplier Code</b></td>
    <td style="padding:2px"><div class="col-xs-5"><input type="text" class="form-control input-sm" id="txtcitemno" name="txtcitemno" tabindex="1" placeholder="Input Customer Code.." required value="<?php echo $cCustCode;?>" style="text-transform:uppercase" /></div><span id="user-result"></span></td>
  </tr>
  <tr>
    <td><b>Supplier Name</b></td>
    <td style="padding:2px"><div class="col-xs-8"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Customer Name.." required  value="<?php echo $cCustName;?>" style="text-transform:uppercase" /></div></td>
  </tr>
  <tr>
    <td><b>Grocery Acct</b></td>
    <td style="padding:2px"><div class="col-xs-5">
      <input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="11" placeholder="Search Acct Title.." required  value="<?php echo $GroceryDesc;?>">
    </div>
  &nbsp;&nbsp;
  <input type="text" id="txtsalesacctD" name="txtsalesacctD" style="border:none; height:30px" readonly  value="<?php echo $GroceryID;?>"></td>
    </tr>
  <tr>
    <td><!--<b>Cripples Acct</b>--></td>
    <td style="padding:2px"><div class="col-xs-5">
      <!--
      <input type="text" class="form-control input-sm" id="txtcrippacct" name="txtcrippacct" tabindex="11" placeholder="Search Acct Title.." required  value="<?php //echo $CrippDesc; ?>">
    	-->
    </div>
  &nbsp;&nbsp;
  <!--<input type="text" id="txtcrippacctD" name="txtcrippacctD" style="border:none; height:30px" readonly  value="<?php //echo $CrippID;?>">-->
  
  </td>
  </tr>
  <tr>
    <td><b>Terms</b></td>
    <td style="padding:2px"><div class="col-xs-2">
      <select id="selterms" name="selterms" class="form-control input-sm selectpicker"  tabindex="3">
        <?php
		$sql = "select * from parameters where ccode='TERMS' order by norder";
		$result=mysqli_query($con,$sql);
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
			?>
        <option value="<?php echo $row['cvalue'];?>" <?php if ($row['cvalue']==$Terms) { echo "selected";} ?>><?php echo $row['cvalue']?></option>
        <?php
				}
				

			?>
      </select>
    </div></td>
  </tr>
  <tr>
    <td width='160' align="center"><input type="button" name="btnupload" id="btnupload" value="UPLOAD IMAGE" onClick="popwin('<?php echo $citemno;?>');"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" style="padding:2px">&nbsp;</td>
  </tr>
  <tr>
  
    <td colspan="3" style="padding:2px">
     	Suppliers Products &nbsp; &nbsp;&nbsp;<input type="button" class="btn btn-primary btn-xs" name="btnadditem" id="btnadditem" value="Add Item">
    <br><br>
 	<div class="form-group col-md-7">
        <table width="70%" border="0" class="table table-striped">
          <tr>
            <th scope="col">Item Code</th>
            <th scope="col" width="300">Item Description</th>
            <th scope="col">Cost</th>
            <th scope="col">&nbsp;</th>
          </tr>
          <?php
   		$sqlitms = "select a.*, b.citemdesc from items_suppliers a left join items b on a.cpartno=b.cpartno where ccode='$citemno' order by nident";
		$resultitms=mysqli_query($con,$sqlitms);
			if (!mysqli_query($con, $sqlitms)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			}			

			$cntritms = 0;
			while($rowitms = mysqli_fetch_array($resultitms, MYSQLI_ASSOC))
				{
       
		  			$cntritms = $cntritms + 1;
		  ?>
          <tr>
            <td><input type="text" id="txtitms<?php echo $cntritms;?>" name="txtitms<?php echo $cntritms;?>" readonly  value="<?php echo $rowitms["cpartno"];?>" class="form-control input-sm">
			</td>
            <td><input type="text" id="txtitmdesc<?php echo $cntritms;?>" name="txtitmdesc<?php echo $cntritms;?>" readonly  value="<?php echo $rowitms["citemdesc"];?>" class="form-control input-sm"></td>
            <td><input type="text" id="txtitmcost<?php echo $cntritms;?>" name="txtitmcost<?php echo $cntritms;?>" readonly  value="<?php echo $rowitms["npurchcost"];?>" class="form-control input-sm"></td>
          	<td><input type="button" class="btn btn-danger btn-xs" name="btndel<?php echo $cntritms;?>" id="btndel<?php echo $cntritms;?>" value="Delete"></td>
          </tr>
          <?php
				}
		  ?>
        </table>
	</div>
    
    </td>
  </tr>
</table>

<br>
<br>
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Suppliers.php?f=';" id="btnMain" name="btnMain">
  <table align="center">
    <tr>
      <td><img src="../images/back.gif" width="20" height="20"/></td>
    </tr>
    <tr>
    <td>Back to Main</td>
    </tr>
  </table>
</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='Suppliers_new.php';" id="btnNew" name="btnNew">
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
</body>
</html>