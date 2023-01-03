<?php

if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "System_Set";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


?><html>
<head>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min34.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
<script language="javascript" type="text/javascript" src="../js/datetimepicker.js"></script>

<script>
function setval(valz){
	
if(valz!=""){

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
			document.getElementById("divmsg").innerHTML=xmlhttp.responseText;
			
			if(xmlhttp.responseText=="POS Credit Limit Reset changed to Cutoff Posting"){
				document.getElementById("rowcut").style.display = "table-row-group";
			}
			else{
				document.getElementById("rowcut").style.display = "none";
			}
	}
	}
	xmlhttp.open("GET","put_poscutval.php?code="+valz,true);
	xmlhttp.send();
}
	
}

function setautopost(valz){
	
if(valz!=""){

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
			document.getElementById("divautopost").innerHTML=xmlhttp.responseText;
	}
	}
	xmlhttp.open("GET","put_autopost.php?code="+valz,true);
	xmlhttp.send();
}
	
}

function setcutdate(){
	var dte1 = document.getElementById("date1").value;
	var dte2 = document.getElementById("date2").value;
	
	if (Date.parse(dte1) <= Date.parse(dte2)) {
		
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
				document.getElementById("divmsg2").innerHTML=xmlhttp.responseText;
		}
		}
		xmlhttp.open("GET","put_posdteval.php?dte1="+dte1+"&dte2="+dte2,true);
		xmlhttp.send();
	
	}
	else{
		alert("Invalid Date Range!\nStart Date cannot be after End Date!");
	}
} 

function setCLAllow(valz){
	
if(valz!=""){

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
			document.getElementById("divmsgallow").innerHTML=xmlhttp.responseText;
			
	}
	}
	xmlhttp.open("GET","put_posclallow.php?code="+valz,true);
	xmlhttp.send();
}
	
}

function setcominfo(){
	var nme = document.getElementById("txtcom").value;
	var desc = document.getElementById("txtdesc").value;
	var add = document.getElementById("txtadd").value;
	var tin = document.getElementById("txttin").value;
	
if(nme!=""){

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
			document.getElementById("divmsgcompany").innerHTML=xmlhttp.responseText;
			
	}
	}
	//alert("putcominfo.php?nme="+nme+"&desc="+desc+"&add="+add+"&tin="+tin);
	xmlhttp.open("GET","putcominfo.php?nme="+nme+"&desc="+desc+"&add="+add+"&tin="+tin,true);
	xmlhttp.send();
}
	
}


function popwin(id){
var page = 'uploadpic.php';
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

<body style="padding:10px; height:450px">
<fieldset>
  <legend> COMPANY INFO <button type="submit" class="btn btn-danger btn-sm" id="btnsales" onClick="setcominfo();">
    	<span class="glyphicon glyphicon-ok"></span> <b>Update Company Info</b>
    </button> 
    
    
  <div style="display:inline" id="divmsgcompany">
    	
    </div>

    </legend>

        <?php
	$ccomp = $_SESSION['companyid'];
	
     $result = mysqli_query($con,"SELECT * FROM `company` WHERE compcode='$ccomp'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $cnme = $all_course_data['compname']; 
		 $cdesc = $all_course_data['compdesc']; 
		 $cadd= $all_course_data['compadd']; 
		 $ctin = $all_course_data['comptin']; 
		
	 }
	 else{
		 
		 $cnme = ""; 
		 $cdesc = ""; 
		 $cadd= ""; 
		 $ctin = ""; 
		 
	 }

		?>

<table width="100%" border="0" cellpadding="0">
  <tr>
    <td width="150" rowspan="3" align="center">
    <?php 
		$imgsrc = "../images/COMPLOGO.png";
	?>
    <img src="<?php echo $imgsrc;?>" width="100" height="100">
    
    </td>
    <td width="150"><b>Company Name:</b></td>
    <td style="padding:2px"><div class="col-xs-7"><input type="text" name="txtcom" id="txtcom" class="form-control input-sm" placeholder="Company Name..." value="<?php echo $cnme;?>" maxlength="90"></div></td>
  </tr>
  <tr>
    <td><b>Description:</b></td>
    <td style="padding:2px"><div class="col-xs-7">
      <input type="text" name="txtdesc" id="txtdesc" class="form-control input-sm" placeholder="Company Description..." value="<?php echo $cdesc;?>"  maxlength="90">
    </div></td>
  </tr>
  <tr>
    <td><b>Address:</b></td>
    <td style="padding:2px"><div class="col-xs-7">
      <input type="text" name="txtadd" id="txtadd" class="form-control input-sm" placeholder="Address..." value="<?php echo $cadd;?>"  maxlength="90">
    </div></td>
  </tr>
  <tr>
    <td align="center"><input type="button" name="btnupload" id="btnupload" value="UPLOAD IMAGE" onClick="popwin();"></td>
    <td><b>Tin No.:</b></td>
    <td style="padding:2px"><div class="col-xs-7">
      <input type="text" name="txttin" id="txttin" class="form-control input-sm" placeholder="TIN No..."  value="<?php echo $ctin;?>">
    </div></td>
  </tr>
</table>
</fieldset>
<br>
<fieldset>
  <legend> SALES </legend>
    <table width="100%" border="0" cellpadding="0">
  <tr>
    <td><b>Auto Post Upon Saving:</b></td>
    <td style="padding:2px">
          <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSPOST'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>

    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selcut" id	="selcut" onChange="setautopost(this.value)">
        	<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
            <option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
        </select>
    </div>
    <div style="display:inline" id="divautopost">
    	
    </div>
    
    </td>
  </tr>
  <tr>
    <td width="250"><b>POS CREDIT LIMIT RESET :</b></td>
    <td style="padding:2px">
        <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSCLMT'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>
        
    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selcut" id	="selcut" onChange="setval(this.value)">
        	<option value="Daily" <?php if ($nvalue=="Daily") { echo "selected"; } ?>> DAILY </option>
            <option value="Cutoff" <?php if ($nvalue=="Cutoff") { echo "selected"; } ?>> CUTOFF POSTING </option>
        </select>
    </div>
    <div style="display:inline" id="divmsg">
    	
    </div>
    </td>
  </tr>
<?php

if($nvalue=="Cutoff"){
	$styleval = "style=\"display:table-row-group;\"";
}
else{
	$styleval = "style=\"display:none;\"";
}

?>
<tbody <?php echo $styleval; ?> id="rowcut">
  <tr>
    <td style="padding:5px"><b>Current Cutoff :</b></td>
    <td style="padding:5px">
    <div style="display:inline" id="divmsg2">
    <?php
     $result = mysqli_query($con,"SELECT DATE_FORMAT(ddatefrom,'%m/%d/%Y') as ddatefrom, DATE_FORMAT(ddateto,'%m/%d/%Y') as ddateto FROM `pos_cutoff` Order By postdate Desc"); 
	 
	 if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
	 $c_datefr = $all_course_data['ddatefrom']; 
	 $c_dateto = $all_course_data['ddateto']; 
	 
		
	 echo $c_datefr." TO ".$c_dateto;
	 }
	 else{
		 echo "";
	 }

	?>
    </div>
    </td>
  </tr>
  <tr>
    <td>
    <button type="submit" class="btn btn-danger btn-sm" id="btnsales" onClick="setcutdate();">
    	<span class="glyphicon glyphicon-ok"></span> <b>START NEW CUTOFF</b>
    </button>
</td>
    <td style="padding:2px">
    <div class="col-xs-5">
    <div class="control-group">
        <div class="controls form-inline">
     <a href="javascript:NewCal('date1','mmddyyyy')">
		<input type='text' class="form-control input-sm" id="date1" name="date1" value="<?php echo date("m/d/Y"); ?>" readonly/>
     </a>
            <label for="inputValue">TO</label>
     <a href="javascript:NewCal('date2','mmddyyyy')">
		<input type='text' class="form-control input-sm" id="date2" name="date2" value="<?php echo date("m/d/Y"); ?>" readonly/>
     </a>
        </div>
    </div>
    </div>
    
    </td>
  </tr>
</tbody>


  <tr>
    <td colspan="2">&nbsp;</td>
    </tr>
  <tr>
    <td><b>Above Credit Limit:</b></td>
    <td style="padding:2px"> 
         <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ABOVECL'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>

    
    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selallow" id="selallow" onChange="setCLAllow(this.value)">
        	<option value="Deny" <?php if ($nvalue=="Deny") { echo "selected"; } ?>> DON'T ALLOW </option>
            <option value="Allow" <?php if ($nvalue=="Allow") { echo "selected"; } ?>> ALLOW PAYMENT </option>
        </select>
    </div>
    <div style="display:inline" id="divmsgallow">
    	
    </div>

    
    </td>
  </tr>

  </table>

</fieldset>

</body>
</html>