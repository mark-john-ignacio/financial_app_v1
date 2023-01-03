<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
    
  <script type="text/javascript">
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

function inserttotbl(){
var checkedValue = null; 
var inputElements = document.getElementsByClassName('messageCheckbox');
for(var i=0; inputElements[i]; ++i){
      if(inputElements[i].checked){
           checkedValue = inputElements[i].value;

	//alert(document.getElementById('tranno'+checkedValue).value);
	var tbl = window.opener.document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=window.opener.document.getElementById('MyTable').insertRow(-1);

	var u=a.insertCell(0);
		u.style.padding = "1px";
		u.style.width = "150px";
	var v=a.insertCell(1);
		v.style.padding = "1px";
		v.style.width = "150px";
	var w=a.insertCell(2);
		w.style.padding = "1px";
	var x=a.insertCell(3);
		x.style.width = "150px";
		x.style.padding = "1px";
	var y=a.insertCell(4);
		y.style.padding = "1px";
	var z=a.insertCell(5);
		z.style.width = "50px";
		z.style.padding = "1px";
		
	u.innerHTML = "<input type='text' name=\"txtrefno"+lastRow+"\" id=\"txtrefno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search RR No...\" style=\"text-transform:uppercase\" onkeyup=\"getRRdetails(this.value,this.name);\" onBlur=\"genAccts(this.value,this.name);\" required value=\""+document.getElementById('tranno'+checkedValue).value+"\" readonly>";
	v.innerHTML = "<input type='text' name=\"txtsuppSI"+lastRow+"\" id=\"txtsuppSI"+lastRow+"\" class=\"form-control input-sm\">";
	w.innerHTML = "<input type='text' name=\"txtrrdesc"+lastRow+"\" id=\"txtrrdesc"+lastRow+"\" class=\"form-control input-sm\">";
	x.innerHTML = "<input type='text' name=\"txtnamount"+lastRow+"\" id=\"txtnamount"+lastRow+"\" class=\"form-control input-sm\" onkeydown=\"return isNumber(event.keyCode)\" onBlur=\"chkdecimal(this.value,this.name,'txtnamount');\" required value=\""+document.getElementById('ngross'+checkedValue).value+"\">";
	y.innerHTML = "<input type='text' name=\"txtremarks"+lastRow+"\" id=\"txtremarks"+lastRow+"\" class=\"form-control input-sm\">";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' class='delete' value='delete' onClick=\"deleteRow1(this);\"/>";


				var amt = window.opener.document.getElementById("txtnGross").value;
				var left = (screen.width/2)-(150/2);
				var top = (screen.height/2)-(150/2);
				var sFeatures="dialogHeight: 150px; dialogWidth: 150px; dialogTop: " + top + "px; dialogLeft: " + left + "px;";
				
				var url = "APV_putAccnt2.php?id="+document.getElementById('tranno'+checkedValue).value+"&amt="+amt;
				
				window.showModalDialog(url, "", sFeatures)
      }
}

window.close();
}
  </script>
  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body style="padding:5px">
<form method="post" name="frm" id="frm" action="Received_POPut.php">
  <table width="100%" cellspacing="0" class="table-condensed">
    <tr bgcolor="#CCCCCC">
      <th align="left"><input name="allbox" type="checkbox" value="Check All" onclick="javascript:checkAll(document.frm)" />
      &nbsp;Invoice No</th>
      <th>Sales Date</th>
      <th>Gross</th>
    </tr>
    <?php 

$salesno = $_REQUEST['x'];
$itmnno = str_replace(",","','",$_REQUEST['itmn']);

if ($itmnno<>""){
	$qry="and ctranno not in ('$itmnno')";
}else{
	$qry="";
}
//and ctranno not in (Select crefno from apv_d)
$sql = "select * from sales where lapproved=1 and ccode='$salesno' ".$qry." order by csalesno desc";

//echo $sql;

				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				$ctr = 0;
				$bg = "#FFFFFF";
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$ctr = $ctr + 1;
						if ($bg == "#FFF7DF"){ 
						  $bg = "#FFFFFF";
						}
						else{
						  $bg = "#FFF7DF";
						}
						

					
?>

    <tr bgcolor="<?php echo $bg; ?>" id="nyca<?php echo $ctr;?>">
      <td><input id="chkitem<?php echo $ctr;?>" name="chkitem[]" type="checkbox" value="<?php echo $ctr; ?>" onclick="javascript:chk('<?php echo $ctr;?>','<?php echo $bg; ?>')" class="messageCheckbox">&nbsp;&nbsp;<?php echo $row['csalesno']; ?>
      
       <input type="hidden" name="chkTranNobg<?php echo $ctr;?>" id="chkTranNobg<?php echo $ctr;?>" value="<?php echo $bg; ?>" />
       
       <input type="hidden" name="ngross<?php echo $ctr;?>" id="ngross<?php echo $ctr;?>" value="<?php echo $row['ngross']; ?>">
       
       <input type="hidden" name="tranno<?php echo $ctr;?>" id="tranno<?php echo $ctr;?>" value="<?php echo $row['csalesno']; ?>">
      </td>
      <td><?php echo $row['dcutdate']; ?></td>
      <td><?php echo $row['ngross']; ?></td>
    </tr>

<?php
				}
				
				mysqli_close($con);

?>

</table>


</form>
</body>
</html>