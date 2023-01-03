<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css">
    
  <script type="text/javascript" src="../js/jquery.js"></script>
  
  <script type="text/javascript" src="lib/js/bootstrap-select.js"></script>
  <script src="../js/bootstrap.min.js"></script>


<script type='text/javascript' src='../js/jquery.autocomplete.js'></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />

<script type="text/javascript">
function getData(refNo)
{
	self.frames['mainframe1'].location.href = 'req_by.php?id=' + refNo;
	product_listing(refNo);
}


function chkhdr(id){
	num = document.getElementById("hdrcnt").value;
	for(i=1; i<=num; i++){
		document.getElementById("hdr"+i).style.backgroundColor="#FFFFFF";
	}
	
	document.getElementById("hdr"+id).style.backgroundColor="#FFCC99";
}

function product_listing(ref_no){
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
    document.getElementById("shoutbox").innerHTML=xmlhttp.responseText;
	document.getElementById("hdnSI").value = ref_no;
    }
  }
  
  citemno2 = "";
  
 var tbl = window.opener.document.getElementById('MyTable').getElementsByTagName('tr');
 var num = tbl.length-1;
 var citemno2 = "";
 var i; 
 
  if (num>=1){
	 // alert("hello");
	for(i=1; i<=num; i++){
		if(window.opener.document.getElementById("txtcreference" + i).value==ref_no){
			
			if(i>1){
				citemno2 = citemno2 + ",";
			}
			
			citemno2 = citemno2 + window.opener.document.getElementById("txtitemcode" + i).value;
		}
		//alert(citemno2);
	}
  }

//alert("SalesRet_InvDet.php?id="+ref_no+"&itms="+citemno2);
xmlhttp.open("GET","PurchRet_RRDet.php?id="+ref_no+"&itms="+citemno2,true);
xmlhttp.send();
}


function checkAll(field)
{
for (var i=0;i<field.length;i++){
var e = field[i];
if (e.name == 'chkitem[]')
	if (e.disabled != true){
		e.checked = field.allbox.checked;
	}
}

var products = document.getElementsByName('chkitem[]');
for( var n = 1; n <= products.length; n++ )
   {
		if(document.getElementById("chkitem"+n).checked == true){
		document.getElementById("nyca"+n).style.backgroundColor="#FFCC99";
		}
		
		else if(document.getElementById("chkitem"+n).checked == false){
		document.getElementById("nyca"+n).style.backgroundColor=document.getElementById("chkTranNobg"+n).value;
		}
   }

}

function chk(id,bgcolor){
	if(document.getElementById("chkitem"+id).checked == true){
		document.getElementById("nyca"+id).style.backgroundColor="#FFCC99";
	}else if(document.getElementById("chkitem"+id).checked == false){
		document.getElementById("nyca"+id).style.backgroundColor=bgcolor;
	}
}

</script>
  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<fieldset>
<table width="100%" cellpadding="3" border="0">
<tr>
	<td width="31%">
     <div class="alt2" dir="ltr" style="
	 				background-image:url(images/body1.jpg);
	  				background-attachment:fixed;
					margin: 0px;
					padding: 0px;
					border: 0px outset;
					width: 100%;
					height: 150px;
					text-align: left;
					overflow: auto">
    	<table width="100%" cellpadding="2" cellspacing="2" border="1" style="border-width:1px; border-color:#999999; border-style:solid; border-collapse:collapse" class="sortable" id="anyid">
		<tr bgcolor="#CCCCCC">
			<th>Reference No.</th>	
		</tr>
          <?php
		  $cCustID = $_REQUEST['id'];
		  $company = $_SESSION['companyid'];
		  
				$sql = "select a.*,b.cname from receive a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.lapproved=1 and a.ccode='$cCustID' order by ctranno";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				$f1 = 0;
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$f1 = $f1 + 1;
		  ?>
          
        <tr onClick="getData('<?php echo $row['ctranno']; ?>'); chkhdr('<?php echo $f1; ?>')" id="hdr<?php echo $f1; ?>">
        	<td style="cursor:pointer;"><?php echo $row['ctranno']; ?></td>
        </tr>

          <?php 
				}
				
				mysqli_close($con);
				
		  ?>
          
		</table>
        </div>
    </td>
    <td width="69%" valign="top">
   <iframe hspace="0" name="mainframe1" height="100%" width="100%" src="req_by.php?id=" frameborder="0" scrolling="no"></iframe>
    </td>
</tr>
</table><input type="hidden" name="hdrcnt" id="hdrcnt" value="<?php echo $f1;?>" />
</fieldset>

<form method="post" name="frm" id="frm" action="PurchRet_RRPut.php">
<fieldset>
<table width="100%">
<tr>
	<td align="left"><b>Items Listing</b></td>
    <td align="right"><input type="submit" value="OK">
<input type="button" value="Cancel" onClick="window.close();"></td>
</tr>
</table>
</legend>

<input type="hidden" name="hdnSI" id="hdnSI" value="" />

<div id="shoutbox" align="center">
<br><br><br><center>Select Reference No.</center><br><br><br>
</div>
</form>



<!-- <iframe hspace="0" name="mainframe2" height="100%" width="100%" src="dr_prod_list.asp" frameborder="0" scrolling="no"></iframe>-->


</body>
</html>