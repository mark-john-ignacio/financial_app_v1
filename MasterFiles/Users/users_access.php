<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "users_access.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
$employeeid = $_REQUEST['empedit'];
@$arrpgist = array();
  $sql = mysqli_query($con,"select * from users_access where userid = '$employeeid'");
	if (mysqli_num_rows($sql)!=0) {
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			@$arrpgist[] = $row['pageid']; 
		}
	}

  @$arrseclist[] = "";
  $sql = mysqli_query($con,"select * from users_sections where userid = '$employeeid'");
	if (mysqli_num_rows($sql)!=0) {
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			@$arrseclist[] = $row['section_nid']; 
		}
	}

  $lallowMRP = 0;
	$result=mysqli_query($con,"select * From company");								
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row['compcode'] == $company){
				$lallowMRP =  $row['lmrpmodules'];
			}
		} 
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Myx Financials</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
  
<script type="text/javascript">
function checkAll(field)
{
for (var i=0;i<field.length;i++){
var e = field[i];
	if (e.name == 'chkAcc[]'){
		if (e.disabled != true){
			e.checked = field.allbox.checked;
		}
	}
}
}

function atleast_onecheckbox(e) {
  if ($("input[type=checkbox]:checked").length === 0) {
      e.preventDefault();
      alert('Atleast one checkbox or access is required!');
      return false;
  }
}


function loadvalues(){
	
	var empid= document.getElementById("userid").value;
	var inputs = document.getElementsByTagName('input');
	var count = 0;
	var hdntxt = "";
	for(var cpt = 0; cpt < inputs.length; cpt++){
		if (inputs[cpt].type == 'checkbox'){ 
			count++;
		}
	}
	alert(count);
	
	for (z=1; z<=count; z++){
		hdntxt = document.getElementById("chkBox"+z).value;
		getval(empid,z,hdntxt);
	}
}


function getval(idz,namez,val) {
	//alert(idz+":"+namez+":"+val);
if (window.XMLHttpRequest)
  {
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
		if(xmlhttp.responseText=="true"){
    		document.getElementById("chkBox"+namez).checked=xmlhttp.responseText;
		}
    }
  }
xmlhttp.open("GET","users_getval.php?emp="+idz+"&page="+val,false);
xmlhttp.send();
}

</script>
</head>
<body>
<form action="users_access_save.php" name="frmuser" id="frmuser" method="post" onsubmit="return atleast_onecheckbox(event)">
	<fieldset>
    	<legend>User's Access (<?php echo $_REQUEST['empedit'];?>)    <input name="allbox" type="checkbox" value="Check All" onclick="javascript:checkAll(document.frmuser)" /> CHECK ALL
</legend>	

<div class="col-xs-12 nopadding"><button type="submit" class="btn btn-block btn-success btn-sm">Save (F2)</button></div>
    <br><br>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">Maintenance</a></li>
    <li><a href="#menu1">Sales</a></li>
    <li><a href="#menu2">Purchases</a></li>
    <li><a href="#menu3">Accounting</a></li>
    <li><a href="#menu4"><?=($lallowMRP==1) ? "MES & Inventory" : "Inventory";?></a></li>
    <li><a href="#menu5">Reports</a></li>
  </ul>

</fieldset>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 90vh;text-align: left;overflow: auto">

  <div class="tab-content">
   
   
    <div id="home" class="tab-pane fade in active" style="padding-left:10px;">
      
       <br>
        <b><u><i>Chart of Accounts</i></u></b>
      	<div style="padding-left:10px;">
          <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts.php" id="chkBox1" <?=(in_array("Accounts.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts_New.php" id="chkBox2" <?=(in_array("Accounts_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts_Edit.php" id="chkBox3" <?=(in_array("Accounts_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
         </div>
      	</div>
        
		    <br>
        <b><u><i>Items Master List</i></u></b>
		    <div style="padding-left:10px;">
          <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items.php" id="chkBox4" <?=(in_array("Items.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items_New.php" id="chkBox5" <?=(in_array("Items_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items_Edit.php" id="chkBox6" <?=(in_array("Items_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
          </div>
		    </div>
        
        <br>
        <b><u><i>Items Sub Menu</i></u></b>

        <div style="padding-left:10px;">  

          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM.php" id="chkBox83" <?=(in_array("UOM.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;UOM List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM_New.php" id="chkBox84" <?=(in_array("UOM_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM_Edit.php" id="chkBox85" <?=(in_array("UOM_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>

          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE.php" id="chkBox86" <?=(in_array("TYPE.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE_New.php" id="chkBox87" <?=(in_array("TYPE_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE_Edit.php" id="chkBox88" <?=(in_array("TYPE_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>

          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	 <label><input type="checkbox" name="chkAcc[]" value="CLASS.php" id="chkBox89" <?=(in_array("CLASS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CLASS_New.php" id="chkBox90" <?=(in_array("CLASS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CLASS_Edit.php" id="chkBox91" <?=(in_array("CLASS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>

          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	 <label><input type="checkbox" name="chkAcc[]" value="Groupings.php" id="chkBox92" <?=(in_array("Groupings.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Group Details List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Groupings_New.php" id="chkBox93" <?=(in_array("Groupings_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Groupings_Edit.php" id="chkBox94" <?=(in_array("Groupings_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>

          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	 <label><input type="checkbox" name="chkAcc[]" value="Process.php" <?=(in_array("Process.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Processes List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Process_New.php" <?=(in_array("Process_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Process_Edit.php" <?=(in_array("Process_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>

        </div>
 
        <br>
       <b><u><i>Price List</i></u></b>
        <div style="padding-left:10px;">

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM.php" id="chkBox107" <?=(in_array("Groupings_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Sales Price</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_New.php" id="chkBox108" <?=(in_array("PM_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_Edit.php" id="chkBox124" <?=(in_array("PM_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_post" id="chkBox125" <?=(in_array("PM_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_cancel" id="chkBox126" <?=(in_array("PM_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
         </div>

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP.php" id="chkBox139" <?=(in_array("PP.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Purchase Price</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_New.php" id="chkBox140" <?=(in_array("PP_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_Edit.php" id="chkBox141" <?=(in_array("PP_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_post" id="chkBox142" <?=(in_array("PP_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_cancel" id="chkBox143" <?=(in_array("PP_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
         </div>

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC.php" id="chkBox144" <?=(in_array("DISC.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Discounts List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_New" id="chkBox145" <?=(in_array("DISC_New",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_Edit" id="chkBox146" <?=(in_array("DISC_Edit",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_post" id="chkBox147" <?=(in_array("DISC_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_cancel" id="chkBox148" <?=(in_array("DISC_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
         </div>

		</div>
       
       <br>
       <b><u><i>Customers Master List</i></u></b>
        <div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers.php" id="chkBox7" <?=(in_array("Customers.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers_New.php" id="chkBox8" <?=(in_array("Customers_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers_Edit.php" id="chkBox9" <?=(in_array("Customers_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
		</div>

        <br>
         <b><u><i>Customers Sub Menu</i></u></b>
         <div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE.php" id="chkBox95" <?=(in_array("CUSTYPE.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE_New.php" id="chkBox96" <?=(in_array("CUSTYPE_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE_Edit.php" id="chkBox97" <?=(in_array("CUSTYPE_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>

         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS.php" id="chkBox98" <?=(in_array("CUSCLASS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp; Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS_New.php" id="chkBox99" <?=(in_array("CUSCLASS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS_Edit.php" id="chkBox100" <?=(in_array("CUSCLASS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
 
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS.php" id="chkBox154" <?=(in_array("CUSGROUPS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp; Group Details List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS_New.php" id="chkBox155" <?=(in_array("CUSGROUPS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS_Edit.php" id="chkBox156" <?=(in_array("CUSGROUPS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
        </div>
         </div>
       
        <br>
        <b><u><i>Suppliers Master List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers.php" id="chkBox10" <?=(in_array("Suppliers.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers_New.php" id="chkBox11" <?=(in_array("Suppliers_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers_Edit.php" id="chkBox12" <?=(in_array("Suppliers_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
     <!--
         <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS.php" id="chkBox154" <?//=(in_array("SUPPGROUPS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp; Group Details List</label>
            </div>
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS_New.php" id="chkBox155" <?//=(in_array("SUPPGROUPS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS_Edit.php" id="chkBox156" <?//=(in_array("SUPPGROUPS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
         </div>
-->
		</div>
        
		<br> 
        <b><u><i>Suppliers Sub Menu</i></u></b>
 		<div style="padding-left:10px;">
          
          <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE.php" id="chkBox101" <?=(in_array("SUPTYPE.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE_New.php" id="chkBox102" <?=(in_array("SUPTYPE_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE_Edit.php" id="chkBox103" <?=(in_array("SUPTYPE_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>

          <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS.php" id="chkBox104" <?=(in_array("SUPCLASS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS_New.php" id="chkBox105" <?=(in_array("SUPCLASS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS_Edit.php" id="chkBox106" <?=(in_array("SUPCLASS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
         </div>
          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS.php" id="chkBox172" <?=(in_array("SUPPGROUPS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp; Group Details List</label>
            </div>
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS_New.php" id="chkBox173" <?=(in_array("SUPPGROUPS_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="SUPPGROUPS_Edit.php" id="chkBox174" <?=(in_array("SUPPGROUPS_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
         </div>
		 </div>

		<br>
		<b><u><i>Salesman Master List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Salesman.php" id="chkBox176" <?=(in_array("Salesman.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Salesman_New.php" id="chkBox177" <?=(in_array("Salesman_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Salesman_Edit.php" id="chkBox178" <?=(in_array("Salesman_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
        </div>
         
    <br>
    <b><u><i>Bank Master List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank.php" id="chkBox157" <?=(in_array("Bank.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank_New.php" id="chkBox158" <?=(in_array("Bank_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank_Edit.php" id="chkBox159" <?=(in_array("Bank_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
		</div>

    <br>
    <b><u><i>Sections List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Locations.php" id="chkBox157" <?=(in_array("Locations.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Locations_New.php" id="chkBox158" <?=(in_array("Locations_New.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Locations_Edit.php" id="chkBox159" <?=(in_array("Locations_Edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		 </div>
		</div>
        
    <br> 
    <b><u><i>Users List</i></u></b>
		<div style="padding-left:10px;">
      <div class="col-xs-12 nopadwleft">
        <div class="col-xs-2 nopadding">
          <label><input type="checkbox" name="chkAcc[]" value="users.php" id="chkBox13" <?=(in_array("users.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Classification List</label>
        </div>
        <div class="col-xs-2 nopadding">
          <label><input type="checkbox" name="chkAcc[]" value="users_add.php" id="chkBox14" <?=(in_array("users_add.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
        </div>
        <div class="col-xs-2 nopadding">
          <label><input type="checkbox" name="chkAcc[]" value="users_access.php" id="chkBox15" <?=(in_array("users_access.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
        </div>
		  </div>
		</div>
        
		<br> 
        <b><u><i>System Settings</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="System_Set" id="chkBox82" <?=(in_array("System_Set",@$arrpgist)) ? "checked" : "";?>>&nbsp;Update System Setting</label>
            </div>
		</div>
		</div>

    </div>
    
     <div id="menu1" class="tab-pane fade">
		<br>
       <b><u><i>Sales Quotation</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote.php" id="chkBox52" <?=(in_array("Quote.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_new.php" id="chkBox53" <?=(in_array("Quote_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_edit.php" id="chkBox54" <?=(in_array("Quote_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_post" id="chkBox55" <?=(in_array("Quote_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_cancel" id="chkBox56" <?=(in_array("Quote_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_print" id="chkBox110" <?=(in_array("Quote_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_unpost.php" id="chkBox111" <?=(in_array("Quote_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="qotype_billing" id="chkBox111" <?=(in_array("qotype_billing",@$arrpgist)) ? "checked" : "";?>>&nbsp;Billing</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="qotype_quote" id="chkBox111" <?=(in_array("qotype_quote",@$arrpgist)) ? "checked" : "";?>>&nbsp;Quotation</label>
            </div>
		</div>
		</div>


		<br>
       <b><u><i>Sales Order</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO.php" id="chkBox127" <?=(in_array("SO.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_new.php" id="chkBox128" <?=(in_array("SO_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_edit.php" id="chkBox129" <?=(in_array("SO_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_post" id="chkBox130" <?=(in_array("SO_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_cancel" id="chkBox131" <?=(in_array("SO_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_print" id="chkBox132" <?=(in_array("SO_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_unpost.php" id="chkBox111" <?=(in_array("SO_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>
        
       	<br>
       <b><u><i>Delivery Receipt</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR.php" id="chkBox133" <?=(in_array("DR.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_new.php" id="chkBox134" <?=(in_array("DR_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_edit.php" id="chkBox135" <?=(in_array("DR_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_post" id="chkBox136" <?=(in_array("DR_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_cancel" id="chkBox137" <?=(in_array("DR_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_print" id="chkBox138" <?=(in_array("DR_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_unpost.php" id="chkBox111" <?=(in_array("DR_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>


	<br>
    <b><u><i>Sales Invoice</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS.php" id="chkBox16" <?=(in_array("POS.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_new.php" id="chkBox17" <?=(in_array("POS_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_edit.php" id="chkBox18" <?=(in_array("POS_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_post" id="chkBox19" <?=(in_array("POS_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_cancel" id="chkBox20" <?=(in_array("POS_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_print" id="chkBox111" <?=(in_array("POS_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SI_unpost.php" id="chkBox111" <?=(in_array("SI_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>
          
		<br>
       <b><u><i>Sales Return</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet.php" id="chkBox21" <?=(in_array("SalesRet.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_new.php" id="chkBox22" <?=(in_array("SalesRet_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_edit.php" id="chkBox23" <?=(in_array("SalesRet_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_post" id="chkBox24" <?=(in_array("SalesRet_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_cancel" id="chkBox25" <?=(in_array("SalesRet_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_print" id="chkBox112" <?=(in_array("SalesRet_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_unpost.php" id="chkBox111" <?=(in_array("SalesRet_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

    </div>
    
     <div id="menu2" class="tab-pane fade">
     <br>
       <b><u><i>Purchase Request</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR.php" id="chkBox31" <?=(in_array("PR.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_new.php" id="chkBox32" <?=(in_array("PR_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_edit.php" id="chkBox33" <?=(in_array("PR_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_post" id="chkBox34" <?=(in_array("PR_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_cancel" id="chkBox35" <?=(in_array("PR_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_print" id="chkBox114" <?=(in_array("PR_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PR_unpost.php" id="chkBox114" <?=(in_array("PR_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

	    <br>
      <b><u><i>Purchase Order</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch.php" id="chkBox26" <?=(in_array("Purch.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_new.php" id="chkBox27" <?=(in_array("Purch_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_edit.php" id="chkBox28" <?=(in_array("Purch_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_post" id="chkBox29" <?=(in_array("Purch_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
          
            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="Purch_cancel" id="chkBox30" <?=(in_array("Purch_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>

            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="Purch_print" id="chkBox113" <?=(in_array("Purch_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
              <label><input type="checkbox" name="chkAcc[]" value="Purch_unpost.php" id="chkBox188" <?=(in_array("Purch_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
          </div>
		    </div>

	   <br>
       <b><u><i>Receiving</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive.php" id="chkBox31" <?=(in_array("Receive.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_new.php" id="chkBox32" <?=(in_array("Receive_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_edit.php" id="chkBox33" <?=(in_array("Receive_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_post" id="chkBox34" <?=(in_array("Receive_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_cancel" id="chkBox35" <?=(in_array("Receive_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_print" id="chkBox114" <?=(in_array("Receive_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_unpost.php" id="chkBox111" <?=(in_array("Receive_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>
  <!--
  	   <br>
       <b><u><i>Receiving (AMOUNT)</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_amt_edit.php" id="chkBox160">&nbsp;Edit</label>
                <input type="hidden" name="chkAcc[]" value="" id="chkBox161">
                <input type="hidden" name="chkAcc[]" value="" id="chkBox162">
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_amt_post" id="chkBox161">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_amt_cancel" id="chkBox162">&nbsp;Cancel</label>
            </div>
</div>
		</div>>-->
     
        <br>
        <b><u><i>Purchase Return</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet.php" id="chkBox36" <?=(in_array("PurchRet.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_new.php" id="chkBox37" <?=(in_array("PurchRet_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_edit.php" id="chkBox38" <?=(in_array("PurchRet_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_post" id="chkBox39" <?=(in_array("PurchRet_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_cancel" id="chkBox40" <?=(in_array("PurchRet_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_print" id="chkBox115" <?=(in_array("PurchRet_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_unpost.php" id="chkBox111" <?=(in_array("PurchRet_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

    </div>
    
     <div id="menu3" class="tab-pane fade">
		
       <br>
       <b><u><i>Journal Entry</i></u></b>
       <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal.php" id="chkBox77" <?=(in_array("Journal.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_new.php" id="chkBox78" <?=(in_array("Journal_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_edit.php" id="chkBox79" <?=(in_array("Journal_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_post" id="chkBox80" <?=(in_array("Journal_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_cancel" id="chkBox81" <?=(in_array("Journal_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_unpost.php" id="chkBox111" <?=(in_array("Journal_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

    <br><br><br>
       <b><u><i>AP Suppliers Invoice</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv" id="chkBox189" <?=(in_array("SuppInv",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_new.php" id="chkBox190" <?=(in_array("SuppInv_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_edit.php" id="chkBox191" <?=(in_array("SuppInv_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_post" id="chkBox192" <?=(in_array("SuppInv_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_cancel" id="chkBox193" <?=(in_array("SuppInv_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_print" id="chkBox194" <?=(in_array("SuppInv_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv2" id="SuppInv2" <?=(in_array("SuppInv2",@$arrpgist)) ? "checked" : "";?>>&nbsp;Per Items Entry</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_unpost.php" id="chkBox111" <?=(in_array("SuppInv_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

    <br>
       <b><u><i>AP Adjustments</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj.php" id="chkBox189" <?=(in_array("APAdj.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj_new.php" id="chkBox190" <?=(in_array("APAdj_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj_edit.php" id="chkBox191" <?=(in_array("APAdj_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj_post" id="chkBox192" <?=(in_array("APAdj_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj_cancel" id="chkBox193" <?=(in_array("APAdj_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APAdj_unpost.php" id="chkBox111" <?=(in_array("APAdj_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
            <!--
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv_print" id="chkBox194" <?//=(in_array("SuppInv_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SuppInv2" id="SuppInv2" <?//=(in_array("SuppInv2",@$arrpgist)) ? "checked" : "";?>>&nbsp;Per Items Entry</label>
            </div>
            -->
		</div>
		</div>

       <br>
       <b><u><i>AP Voucher</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV.php" id="chkBox57" <?=(in_array("APV.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_new.php" id="chkBox58" <?=(in_array("APV_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_edit.php" id="chkBox59" <?=(in_array("APV_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_post" id="chkBox60" <?=(in_array("APV_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_cancel" id="chkBox61" <?=(in_array("APV_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_print" id="chkBox116" <?=(in_array("APV_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_unpost.php" id="chkBox111" <?=(in_array("APV_unpost.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

    <br>
       <b><u><i>Request For Payment</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP.php" id="chkBox62" <?=(in_array("RFP.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_new.php" id="chkBox63" <?=(in_array("RFP_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_edit.php" id="chkBox64" <?=(in_array("RFP_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_post" id="chkBox65" <?=(in_array("RFP_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_cancel" id="chkBox66" <?=(in_array("RFP_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_print" id="chkBox117" <?=(in_array("RFP_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="RFP_unpost" id="chkBox117" <?=(in_array("RFP_unpost",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
            
          </div>
		    </div>
          
       <br>
       <b><u><i>AP Bills Payment</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
         	  <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill.php" id="chkBox62" <?=(in_array("PayBill.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_new.php" id="chkBox63" <?=(in_array("PayBill_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_edit.php" id="chkBox64" <?=(in_array("PayBill_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_post" id="chkBox65" <?=(in_array("PayBill_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_cancel" id="chkBox66" <?=(in_array("PayBill_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_print" id="chkBox117" <?=(in_array("PayBill_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_unpost" id="chkBox117" <?=(in_array("PayBill_unpost",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="check_override" id="chkBox117" <?=(in_array("check_override",@$arrpgist)) ? "checked" : "";?>>&nbsp;Check Override</label>
            </div>
          </div>
		    </div>

	   <br><br><br>
       <b><u><i>AR Adjustments</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj.php" id="chkBox149" <?=(in_array("ARAdj.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_new.php" id="chkBox149" <?=(in_array("ARAdj_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_edit.php" id="chkBox150" <?=(in_array("ARAdj_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_post" id="chkBox151" <?=(in_array("ARAdj_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_cancel" id="chkBox152" <?=(in_array("ARAdj_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_unpost" id="chkBox118" <?=(in_array("ARAdj_unpost",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>

            <!--
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ARAdj_print" id="chkBox153" <?=(in_array("ARAdj_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>
-->
		</div>
		</div>

	   <br>
       <b><u><i>AR Payments</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR.php" id="chkBox67" <?=(in_array("OR.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_new.php" id="chkBox68" <?=(in_array("OR_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_edit.php" id="chkBox69" <?=(in_array("OR_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_post" id="chkBox70" <?=(in_array("OR_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_cancel" id="chkBox71" <?=(in_array("OR_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_print" id="chkBox118" <?=(in_array("OR_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>

            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_unpost" id="chkBox118" <?=(in_array("OR_unpost",@$arrpgist)) ? "checked" : "";?>>&nbsp;Un-Post</label>
            </div>
		</div>
		</div>

		<br><br><br>
       <b><u><i>Prepare Bank Deposit</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit.php" id="chkBox72" <?=(in_array("Deposit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_new.php" id="chkBox73" <?=(in_array("Deposit_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_edit.php" id="chkBox74" <?=(in_array("Deposit_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_post" id="chkBox75" <?=(in_array("Deposit_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_cancel" id="chkBox76" <?=(in_array("Deposit_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
		</div>
		</div>

    </div>

     <div id="menu4" class="tab-pane fade">
  
        <?php
          if($lallowMRP==1){
        ?>

        <br>
        <b><u><i>Material BOM</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="MaterialBOM" <?=(in_array("MaterialBOM",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="MaterialBOM_new" <?=(in_array("MaterialBOM_new",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="MaterialBOM_edit" <?=(in_array("MaterialBOM_edit",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>
		    </div>

        <br>
        <b><u><i>Production Processes</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ProdProcess" <?=(in_array("ProdProcess",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ProdProcess_new" <?=(in_array("ProdProcess_new",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="ProdProcess_edit" <?=(in_array("ProdProcess_edit",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
		      </div>
		    </div>

        <br>
        <b><u><i>Job Orders</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders" <?=(in_array("JobOrders",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders_new" <?=(in_array("JobOrders_new",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders_edit" <?=(in_array("JobOrders_edit",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders_post" <?=(in_array("JobOrders_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders_cancel" <?=(in_array("JobOrders_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="JobOrders_print" <?=(in_array("JobOrders_print",@$arrpgist)) ? "checked" : "";?>>&nbsp;Print</label>
            </div>
		      </div>
		    </div>


        <?php
          }
        ?>
        <br>
        <b><u><i>Inventory Count</i></u></b>
        <div style="padding-left:10px;"> 
          <div class="col-xs-12 nopadwleft">
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt.php" id="chkBox179" <?=(in_array("InvCnt.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_new.php" id="chkBox180" <?=(in_array("InvCnt_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_edit.php" id="chkBox181" <?=(in_array("InvCnt_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_post" id="chkBox182" <?=(in_array("InvCnt_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_cancel" id="chkBox182" <?=(in_array("InvCnt_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
		      </div>
		    </div>

		 <br>
       <b><u><i>Inventory Transfer</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvTrans.php" id="chkBox183" <?=(in_array("InvTrans.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvTrans_new.php" id="chkBox184" <?=(in_array("InvTrans_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvTrans_edit.php" id="chkBox185" <?=(in_array("InvTrans_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvTrans_post" id="chkBox186" <?=(in_array("InvTrans_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvTrans_cancel" id="chkBox187" <?=(in_array("InvTrans_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
		</div>
		</div>
		 
	    <br>
      <b><u><i>Inventory Adjustment</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj.php" id="chkBox119" <?=(in_array("InvAdj.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_new.php" id="chkBox120" <?=(in_array("InvAdj_new.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_edit.php" id="chkBox121" <?=(in_array("InvAdj_edit.php",@$arrpgist)) ? "checked" : "";?>>&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_post" id="chkBox122" <?=(in_array("InvAdj_post",@$arrpgist)) ? "checked" : "";?>>&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_cancel" id="chkBox123" <?=(in_array("InvAdj_cancel",@$arrpgist)) ? "checked" : "";?>>&nbsp;Cancel</label>
            </div>
		      </div>
		    </div>

        <br><br><br>

					<h4><i>Sections Access</i></h4> 

					<?php
          $company = $_SESSION['companyid'];
					$sqloc = mysqli_query($con,"select * from locations where compcode = '$company'");
					if (mysqli_num_rows($sqloc)!=0) {
						while($rowloc = mysqli_fetch_array($sqloc, MYSQLI_ASSOC)){
					?>
						<div class="col-xs-12 nopadwleft">
							<label><input type="checkbox" name="chkSections[]" value="<?=$rowloc['nid']?>" <?=(in_array($rowloc['nid'],@$arrseclist)) ? "checked" : "";?>>&nbsp;<?=$rowloc['cdesc']?></label>
						</div>

					<?php
						}
					}

					?>

      <!--  
      <br>
       <b><u><i>Inventory Receiving</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvRec.php" id="chkBox165">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvRec_new.php" id="chkBox166">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvRec_edit.php" id="chkBox167">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvRec_post" id="chkBox168">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvRec_cancel" id="chkBox169">&nbsp;Cancel</label>
            </div>
		</div>
		</div>
	-->

	 </div>
     
     <div id="menu5" class="tab-pane fade">
	  
      <br>
      <b><u><i>Sales</i></u></b><br>
      <div style="padding-left:10px;"> 
          <label><input type="checkbox" name="chkAcc[]" value="SalesOrders.php" id="chkBox43" <?=(in_array("SalesOrders.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Orders</label>
           <br>
         <label><input type="checkbox" name="chkAcc[]" value="SalesPerItem.php" id="chkBox42" <?=(in_array("SalesPerItem.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Per Item</label>
          <br>
         <label><input type="checkbox" name="chkAcc[]" value="SalesPerCust.php" id="chkBox43" <?=(in_array("SalesPerCust.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Per Customer</label>
           <br>
         <label><input type="checkbox" name="chkAcc[]" value="SalesPerSupp.php" id="chkBox51" <?=(in_array("SalesPerSupp.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Per Supplier</label>
           <br>
         <label><input type="checkbox" name="chkAcc[]" value="SalesSummary.php" id="chkBox44" <?=(in_array("SalesSummary.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Summary</label>
          <br>
         <label><input type="checkbox" name="chkAcc[]" value="SalesDetailed.php" id="chkBox45" <?=(in_array("SalesDetailed.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Detailed</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="SalesDisc.php" id="chkBox45" <?=(in_array("SalesDisc.php",@$arrpgist)) ? "checked" : "";?>>
          SO vs DR vs SI</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="SODRDisc.php" id="chkBox45" <?=(in_array("SODRDisc.php",@$arrpgist)) ? "checked" : "";?>>
          Discrepancy Report - SO vs DR</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="ARAgeing.php" id="chkBox164" <?=(in_array("ARAgeing.php",@$arrpgist)) ? "checked" : "";?>>
          AR Ageing</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="ARMonitoring.php" <?=(in_array("ARMonitoring.php",@$arrpgist)) ? "checked" : "";?>>
          AR Monitoring</label>
          <br>
       </div>
       
       <br>
       <b><u><i>Purchases</i></u></b><br>
       <div style="padding-left:10px;"> 
        <label>
          <input type="checkbox" name="chkAcc[]" value="PurchReg.php" id="chkBox46" <?=(in_array("PurchReg.php",@$arrpgist)) ? "checked" : "";?>>
          Purchase Register</label>
         <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchperItem.php" id="chkBox47" <?=(in_array("PurchperItem.php",@$arrpgist)) ? "checked" : "";?>>
          Purchases Per Item</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchPerSupp.php" id="chkBox48" <?=(in_array("PurchPerSupp.php",@$arrpgist)) ? "checked" : "";?>>
          Purchases Per Supplier</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchSummary.php" id="chkBox170" <?=(in_array("PurchSummary.php",@$arrpgist)) ? "checked" : "";?>>
          Purchase Summary</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchDetailed.php" id="chkBox171" <?=(in_array("PurchDetailed.php",@$arrpgist)) ? "checked" : "";?>>
          Purchase Detailed</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="PurchBalances.php" id="chkBox45" <?=(in_array("PurchBalances.php",@$arrpgist)) ? "checked" : "";?>>
          PO Balances</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="PurchMonitoring.php" id="chkBox45" <?=(in_array("PurchMonitoring.php",@$arrpgist)) ? "checked" : "";?>>
          PO Price Monitoring</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="APAgeing.php" id="chkBox168" <?=(in_array("APAgeing.php",@$arrpgist)) ? "checked" : "";?>>
          AP Ageing Report</label>
          <br>
       </div>

       <br>
       <b><u><i>GL Reports</i></u></b><br>
        <div style="padding-left:10px;"> 
        <label><input type="checkbox" name="chkAcc[]" value="SalesReg.php" id="chkBox41" <?=(in_array("SalesReg.php",@$arrpgist)) ? "checked" : "";?>>
          Sales Register</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="CashBook.php" id="chkBox163" <?=(in_array("CashBook.php",@$arrpgist)) ? "checked" : "";?>>
          Cash Receipts Journal</label>
          <br>         
          <label><input type="checkbox" name="chkAcc[]" value="PurchJourn.php" id="chkBox166" <?=(in_array("PurchJourn.php",@$arrpgist)) ? "checked" : "";?>>
          Purchase Journal</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="APJ.php" id="chkBox167" <?=(in_array("APJ.php",@$arrpgist)) ? "checked" : "";?>>
          Accounts Payable Ledger</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="CDJ.php" id="chkBox168" <?=(in_array("CDJ.php",@$arrpgist)) ? "checked" : "";?>>
          Cash Disbursement Journal</label>
          <br>         
          <label><input type="checkbox" name="chkAcc[]" value="GJournal.php" id="chkBox169" <?=(in_array("GJournal.php",@$arrpgist)) ? "checked" : "";?>>
          General Journal</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="GLedger.php" id="chkBox169" <?=(in_array("GLedger.php",@$arrpgist)) ? "checked" : "";?>>
          General Ledger</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="TBal.php" id="chkBox169" <?=(in_array("TBal.php",@$arrpgist)) ? "checked" : "";?>>
          Trial Balance</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="BalSheet.php" id="chkBox169" <?=(in_array("BalSheet.php",@$arrpgist)) ? "checked" : "";?>>
          Balance Sheet</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="IncomeStatement.php" id="chkBox169" <?=(in_array("IncomeStatement.php",@$arrpgist)) ? "checked" : "";?>>
          Income Statement</label>
          
        </div>

        <br>
        <b><u><i>BIR Reports</i></u></b><br>
        <div style="padding-left:10px;"> 
          <label><input type="checkbox" name="chkAcc[]" value="MonthlyVAT.php" id="chkBox163" <?=(in_array("MonthlyVAT.php",@$arrpgist)) ? "checked" : "";?>>
          BIR - Monthly Output VAT</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="Monthly_IVAT.php" id="chkBox168" <?=(in_array("Monthly_IVAT.php",@$arrpgist)) ? "checked" : "";?>>
          Monthly Input VAT and W/Tax Report</label>
          <br>
          <label><input type="checkbox" name="chkAcc[]" value="bir2307.php" id="chkBox168" <?=(in_array("bir2307.php",@$arrpgist)) ? "checked" : "";?>>
          BIR Form - 2307</label>
         <br>
        </div>
        
         <b><u><i>Inventory</i></u></b><br>
         <div style="padding-left:10px;"> 
          <label>
            <input type="checkbox" name="chkAcc[]" value="InvSum.php" id="chkBox49" <?=(in_array("InvSum.php",@$arrpgist)) ? "checked" : "";?>>
            Inventory Summary</label>
           <br>
           <label>
            <input type="checkbox" name="chkAcc[]" value="StockLedger.php" id="chkBox50" <?=(in_array("StockLedger.php",@$arrpgist)) ? "checked" : "";?>>
            Stock Ledger</label>
         </div>
       
  </div>
 
  
  </div>

</div>

  
    
  <input type="hidden" name="userid" id="userid" value="<?php echo $employeeid;?>">


<script>
$(document).ready(function(){
    $(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
	
    /*
	$("input[name='chkAcc[]']").each(function () {
		var val = $(this).val();
		var id = $("#userid").val();
		
		var idchk = $(this);
		
			$.ajax ({
				url: "users_getval.php",
				data: { id: id, val: val },
				async: false,
				dataType: "text",
				success: function( data ) {

					if(data.trim()=="True"){
						idchk.prop('checked', true);
					}
				}
			});

	});
  */


});
</script>


<!-- LAST NUMBER chkBox194 -->
</form>
</body>
</html>
