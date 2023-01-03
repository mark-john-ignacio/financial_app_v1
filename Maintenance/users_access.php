<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "users_access.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Case</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
  
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
    
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">Maintenance</a></li>
    <li><a href="#menu1">Sales</a></li>
    <li><a href="#menu2">Purchases</a></li>
    <li><a href="#menu3">Accounting</a></li>
    <li><a href="#menu4">Inventory</a></li>
    <li><a href="#menu5">Reports</a></li>
  </ul>

</fieldset>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 68vh;text-align: left;overflow: auto">

  <div class="tab-content">
   
   
     <div id="home" class="tab-pane fade in active" style="padding-left:10px;">
      
      <br>
        <b><u><i>Chart of Accounts</i></u></b>
      	<div style="padding-left:10px;">
          <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts.php" id="chkBox1">&nbsp;View List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts_New.php" id="chkBox2">&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Accounts_Edit.php" id="chkBox3">&nbsp;Edit</label>
            </div>
         </div>
      	</div>
        
		<br>
          <b><u><i>Items Master List</i></u></b>
		<div style="padding-left:10px;">
          <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items.php" id="chkBox4">&nbsp;View List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items_New.php" id="chkBox5">&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Items_Edit.php" id="chkBox6">&nbsp;Edit</label>
            </div>
         </div>
		</div>
        
       <br>
         <b><u><i>Items Sub Menu</i></u></b>
        <div style="padding-left:10px;">         
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM.php" id="chkBox83">&nbsp;UOM List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM_New.php" id="chkBox84">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UOM_Edit.php" id="chkBox85">&nbsp;Edit</label>
            </div>
		 </div>

         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE.php" id="chkBox86">&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE_New.php" id="chkBox87">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="TYPE_Edit.php" id="chkBox88">&nbsp;Edit</label>
            </div>
		 </div>

         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	 <label><input type="checkbox" name="chkAcc[]" value="CLASS.php" id="chkBox89">&nbsp;Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CLASS_New.php" id="chkBox90">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CLASS_Edit.php" id="chkBox91">&nbsp;Edit</label>
            </div>
		 </div>

         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	 <label><input type="checkbox" name="chkAcc[]" value="Groupings.php" id="chkBox92">&nbsp;Group Details List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Groupings_New.php" id="chkBox93">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Groupings_Edit.php" id="chkBox94">&nbsp;Edit</label>
            </div>
		 </div>
       </div>
 
        <br>
       <b><u><i>Price List</i></u></b>
        <div style="padding-left:10px;">

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM.php" id="chkBox107">&nbsp;Sales Price</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_New.php" id="chkBox108">&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_Edit.php" id="chkBox124">&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_post" id="chkBox125">&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PM_cancel" id="chkBox126">&nbsp;Cancel</label>
            </div>
         </div>

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP.php" id="chkBox139">&nbsp;Purchase Price</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_New.php" id="chkBox140">&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_Edit.php" id="chkBox141">&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_post" id="chkBox142">&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PP_cancel" id="chkBox143">&nbsp;Cancel</label>
            </div>
         </div>

        <div class="col-xs-12 nopadwleft">
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC.php" id="chkBox144">&nbsp;Discounts List</label>
            </div>
           	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_New" id="chkBox145">&nbsp;Add New</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_Edit" id="chkBox146">&nbsp;Edit</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_post" id="chkBox147">&nbsp;Posting</label>
            </div>
          	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DISC_cancel" id="chkBox148">&nbsp;Cancel</label>
            </div>
         </div>

		</div>
       
       <br>
       <b><u><i>Customers Master List</i></u></b>
        <div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers.php" id="chkBox7">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers_New.php" id="chkBox8">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Customers_Edit.php" id="chkBox9">&nbsp;Edit</label>
            </div>
		 </div>
		</div>

        <br>
         <b><u><i>Customers Sub Menu</i></u></b>
         <div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE.php" id="chkBox95">&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE_New.php" id="chkBox96">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSTYPE_Edit.php" id="chkBox97">&nbsp;Edit</label>
            </div>
		 </div>

         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS.php" id="chkBox98">&nbsp; Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS_New.php" id="chkBox99">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSCLASS_Edit.php" id="chkBox100">&nbsp;Edit</label>
            </div>
		 </div>
 
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS.php" id="chkBox154">&nbsp; Group Details Listt</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS_New.php" id="chkBox155">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="CUSGROUPS_Edit.php" id="chkBox156">&nbsp;Edit</label>
            </div>
		 </div>
        </div>
       
        <br>
        <b><u><i>Suppliers Master List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers.php" id="chkBox10">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers_New.php" id="chkBox11">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Suppliers_Edit.php" id="chkBox12">&nbsp;Edit</label>
            </div>
		 </div>
		</div>
        
		<br> 
        <b><u><i>Suppliers Sub Menu</i></u></b>
 		<div style="padding-left:10px;">
          
          <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE.php" id="chkBox101">&nbsp;Types List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE_New.php" id="chkBox102">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPTYPE_Edit.php" id="chkBox103">&nbsp;Edit</label>
            </div>
		 </div>

          <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS.php" id="chkBox104">&nbsp;Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS_New.php" id="chkBox105">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SUPCLASS_Edit.php" id="chkBox106">&nbsp;Edit</label>
            </div>
		 </div>
         
		</div>

        <br>
        <b><u><i>Bank Master List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank.php" id="chkBox157">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank_New.php" id="chkBox158">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Bank_Edit.php" id="chkBox159">&nbsp;Edit</label>
            </div>
		 </div>
		</div>

        
        <br> 
        <b><u><i>Users List</i></u></b>
		<div style="padding-left:10px;">
         <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="users.php" id="chkBox13">&nbsp;Classification List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="users_add.php" id="chkBox14">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="users_access.php" id="chkBox15">&nbsp;Edit</label>
            </div>
		 </div>
		</div>
        
		<br> 
        <b><u><i>System Settings</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="System_Set" id="chkBox82">&nbsp;Update System Setting</label>
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
            	<label><input type="checkbox" name="chkAcc[]" value="Quote.php" id="chkBox52">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_new.php" id="chkBox53">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_edit.php" id="chkBox54">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_post" id="chkBox55">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_cancel" id="chkBox56">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Quote_print" id="chkBox110">&nbsp;Print</label>
            </div>
		</div>
		</div>


		<br>
       <b><u><i>Sales Order</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO.php" id="chkBox127">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_new.php" id="chkBox128">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_edit.php" id="chkBox129">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_post" id="chkBox130">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_cancel" id="chkBox131">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SO_print" id="chkBox132">&nbsp;Print</label>
            </div>
		</div>
		</div>
        
       	<br>
       <b><u><i>Delivery Receipt</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR.php" id="chkBox133">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_new.php" id="chkBox134">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_edit.php" id="chkBox135">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_post" id="chkBox136">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_cancel" id="chkBox137">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="DR_print" id="chkBox138">&nbsp;Print</label>
            </div>
		</div>
		</div>


	<br>
    <b><u><i>Sales Invoice</i></u></b>

        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS.php" id="chkBox16">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_new.php" id="chkBox17">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_edit.php" id="chkBox18">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_post" id="chkBox19">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_cancel" id="chkBox20">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="POS_print" id="chkBox111">&nbsp;Print</label>
            </div>
		</div>
		</div>
          
		<br>
       <b><u><i>Sales Return</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet.php" id="chkBox21">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_new.php" id="chkBox22">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_edit.php" id="chkBox23">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_post" id="chkBox24">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_cancel" id="chkBox25">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="SalesRet_print" id="chkBox112">&nbsp;Print</label>
            </div>
		</div>
		</div>

    </div>
    
     <div id="menu2" class="tab-pane fade">
	 <br>
      <b><u><i>Purchase Order</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch.php" id="chkBox26">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_new.php" id="chkBox27">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_edit.php" id="chkBox28">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_post" id="chkBox29">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_cancel" id="chkBox30">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Purch_print" id="chkBox113">&nbsp;Print</label>
            </div>
		</div>
		</div>

	   <br>
       <b><u><i>Receiving</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive.php" id="chkBox31">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_new.php" id="chkBox32">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_edit.php" id="chkBox33">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_post" id="chkBox34">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_cancel" id="chkBox35">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Receive_print" id="chkBox114">&nbsp;Print</label>
            </div>
		</div>
		</div>
        
        <br>
       <b><u><i>Receiving Updating Posted Transactions</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UpdateRRQty" id="chkBox170">&nbsp;Update Quantity</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="UpdateRRAmt" id="chkBox171">&nbsp;Update Price/Amount</label>
            </div>
		</div>
		</div>
       
        <br>
        <b><u><i>Purchase Return</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet.php" id="chkBox36">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_new.php" id="chkBox37">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_edit.php" id="chkBox38">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_post" id="chkBox39">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_cancel" id="chkBox40">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PurchRet_print" id="chkBox115">&nbsp;Print</label>
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
            	<label><input type="checkbox" name="chkAcc[]" value="Journal.php" id="chkBox77">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_new.php" id="chkBox78">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_edit.php" id="chkBox79">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_post" id="chkBox80">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Journal_cancel" id="chkBox81">&nbsp;Cancel</label>
            </div>
		</div>
		</div>

       <br>
       <b><u><i>AP Invoices</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV.php" id="chkBox57">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_new.php" id="chkBox58">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_edit.php" id="chkBox59">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_post" id="chkBox60">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_cancel" id="chkBox61">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="APV_print" id="chkBox116">&nbsp;Print</label>
            </div>
		</div>
		</div>
          
       <br>
       <b><u><i>AP Payments</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill.php" id="chkBox62">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_new.php" id="chkBox63">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_edit.php" id="chkBox64">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_post" id="chkBox65">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_cancel" id="chkBox66">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="PayBill_print" id="chkBox117">&nbsp;Print</label>
            </div>
		</div>
		</div>

	   <br>
       <b><u><i>AR Adjustment</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR.php" id="chkBox149">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR_new.php" id="chkBox149">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR_edit.php" id="chkBox150">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR_post" id="chkBox151">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR_cancel" id="chkBox152">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="AR_print" id="chkBox153">&nbsp;Print</label>
            </div>
		</div>
		</div>

	   <br>
       <b><u><i>AR Payments</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR.php" id="chkBox67">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_new.php" id="chkBox68">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_edit.php" id="chkBox69">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_post" id="chkBox70">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_cancel" id="chkBox71">&nbsp;Cancel</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="OR_print" id="chkBox118">&nbsp;Print</label>
            </div>
		</div>
		</div>

		<br>
       <b><u><i>Prepare Bank Deposit</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit.php" id="chkBox72">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_new.php" id="chkBox73">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_edit.php" id="chkBox74">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_post" id="chkBox75">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="Deposit_cancel" id="chkBox76">&nbsp;Cancel</label>
            </div>
		</div>
		</div>

    </div>

     <div id="menu4" class="tab-pane fade">
	   
       <br>
       <b><u><i>Inventory Count</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt.php" id="chkBox160">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_new.php" id="chkBox161">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_edit.php" id="chkBox162">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_post" id="chkBox163">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvCnt_cancel" id="chkBox164">&nbsp;Cancel</label>
            </div>
		</div>
		</div>

	   <br>
       <b><u><i>Inventory Adjustment</i></u></b>
        <div style="padding-left:10px;"> 
        <div class="col-xs-12 nopadwleft">
         	<div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj.php" id="chkBox119">&nbsp;View List</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_new.php" id="chkBox120">&nbsp;Add New</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_edit.php" id="chkBox121">&nbsp;Edit</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_post" id="chkBox122">&nbsp;Post</label>
            </div>
            <div class="col-xs-2 nopadding">
            	<label><input type="checkbox" name="chkAcc[]" value="InvAdj_cancel" id="chkBox123">&nbsp;Cancel</label>
            </div>
		</div>
		</div>
        
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


	 </div>
     
     <div id="menu5" class="tab-pane fade">
	  
      <br>
      <b><u><i>Sales</i></u></b><br>
      <div style="padding-left:10px;"> 
         <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="SalesPerItem.php" id="chkBox42">
          Sales Per Item</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="SalesPerCust.php" id="chkBox43">
          Sales Per Customer</label>
           <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="SalesPerSupp.php" id="chkBox51">
          Sales Per Supplier</label>
           <br>
          <label>
          <input type="checkbox" name="chkAcc[]" value="SalesSummary.php" id="chkBox44">
          Sales Summary</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="SalesDetailed.php" id="chkBox45">
          Sales Detailed</label>
       </div>
       
       <br>
       <b><u><i>Purchases</i></u></b><br>
       <div style="padding-left:10px;"> 
        <label>
          <input type="checkbox" name="chkAcc[]" value="PurchReg.php" id="chkBox46">
          Purchase Register</label>
         <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchperItem.php" id="chkBox47">
          Purchases Per Item</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="PurchPerSupp.php" id="chkBox48">
          Purchases Per Supplier</label>
       </div>
 
        <br>
       <b><u><i>Finance</i></u></b><br>
       <div style="padding-left:10px;"> 
        <label>
          <input type="checkbox" name="chkAcc[]" value="SalesReg.php" id="chkBox41">
          Sales Register</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="CashBook.php" id="chkBox172">
          Cash Receipts</label>
          <br>
        <label>
          <input type="checkbox" name="chkAcc[]" value="SalesBook.php" id="chkBox173">
          Sales Book</label>
          <br>
       <label>
          <input type="checkbox" name="chkAcc[]" value="PurchReg.php" id="chkBox46">
          Purchase Register</label>
         <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="APJ.php" id="chkBox174">
          Accounts Payable Ledger</label>
          <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="CDJ.php" id="chkBox175">
          Cash Disbursement Journal</label>
          <br>
       <label>
          <input type="checkbox" name="chkAcc[]" value="TBal.php" id="chkBox176">
          Trial Balance</label>
       </div>
      
       <br>
       <b><u><i>Inventory</i></u></b><br>
       <div style="padding-left:10px;"> 
        <label>
          <input type="checkbox" name="chkAcc[]" value="InvSum.php" id="chkBox49">
          Inventory Summary</label>
         <br>
         <label>
          <input type="checkbox" name="chkAcc[]" value="StockLedger.php" id="chkBox50">
          Stock Ledger</label>
       </div>
       
  </div>
 
  
  </div>

</div>

  <button type="submit" class="btn btn-success btn-sm">Save<br> (F2)</button>
    
  <input type="hidden" name="userid" id="userid" value="<?php echo $_REQUEST['empedit'];?>">


<script>
$(document).ready(function(){
    $(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
	
	$("input[name='chkAcc[]']").each(function () {
		var val = $(this).val();
		var id = $("#userid").val();
		
		var idchk = $(this).attr("id");
		
			$.ajax ({
				url: "users_getval.php",
				data: { id: id, val: val },
				async: false,
				dataType: "text",
				success: function( data ) {

					if(data.trim()=="True"){
						$("#"+idchk).prop('checked', true);
					}
				}
			});

	});


});
</script>


<!-- LAST NUMBER chkBox176 -->
</form>
</body>
</html>
