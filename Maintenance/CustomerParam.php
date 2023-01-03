<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "CustomerParam.php";

include('../Connection/connection_string.php');
//include('../include/denied.php');
//include('../include/access.php');

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Coop Financials</title>

    <!-- Bootstrap core CSS -->
    <link href="lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="lib/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="lib/css/theme.css" rel="stylesheet">
    
    
  </head>

  <body style="padding:5px" style="height:90vh">
  <?php
  if($_REQUEST["msg"]==""){
	  $msg = "";
  }
  else
  {
	  $msg = " (".$_REQUEST["msg"].")";
  }
  
  ?>
<form class="form-inline" role="form" method="post" action="CustomerParam_Save.php" id="frmparam" name="frmparam">
<input type="hidden" name="txtcnt" id="txtcnt" value="">
<input type="hidden" name="txtccode" id="txtccode" value="<?php echo $_REQUEST['x'];?>">
<fieldset>
	<legend><?php echo $_REQUEST['x'].$msg;?> </legend>
    
      <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="addval();" id="btnSave" name="btnSave">
      Add Value
      </button>
      &nbsp;&nbsp;
      <button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnopen" name="btnopen" onClick="return chkform();">Save</button>
<br> <br>
             <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 5px; padding-bottom:10px;
					border: 0px;
					width: 100%;
					height: 80vh;
					text-align: left;
					overflow: auto;">

<table width="80%" class="table table-striped" id="MyTable2">
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">Value(s)</th>
    <th scope="col">&nbsp;</th>
  </tr>
  <?php
$typ = $_REQUEST['x'];
$company = $_SESSION['companyid'];
  
  $sql = "Select * from parameters where compcode='$company' and ccode='$typ' Order By norder";
  	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
		$cntr = 0	;		
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cntr = $cntr + 1;
  ?>
<tr>
    <td width="20%">
    <div class="col-xs-10">
    <input type="text" readonly name="nord<?php echo $cntr?>" id="nord<?php echo $cntr?>" value="<?php echo $row['norder'];?>" class="form-control input-group-sm">
    </div>
    </td>
    <td><input type="text" name="nvalz<?php echo $cntr?>" id="nvalz<?php echo $cntr?>" value="<?php echo $row['cvalue'];?>" class="form-control input-group-sm"></td>
    <td width="10%">
    <button type="button" class="btn btn-danger btn-sm" tabindex="6" id="btndel" name="btndel" onClick="delInfo(this);" />delete</button>
    </td>
  </tr>
  <?php
	}
  ?>
</table>

</div>


</fieldset>
</form>
<?php
	
				mysqli_close($con);
?>

<script>
function chkform(){
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	var withval = "YES";
	
	if(lastRow==0){
		alert("Cannot save parameter without value(s);");
		return false;
	}
	else{
		for (z=1; z<=lastRow; z++){
			if(document.getElementById("nvalz"+z).value==""){
				withval = "NO";
			}
		}
	}
	
	if(withval=="NO"){
		alert("Cannot Save Blank Value!");
		return false;
	}
	else{
		document.getElementById("txtcnt").value=lastRow;
		document.getElementById("frmparam").submit();
	}
}
function addval(){
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var a=document.getElementById('MyTable2').insertRow(-1);
	var u=a.insertCell(0);
	u.style.width = "20%";
	var v=a.insertCell(1);
	var w=a.insertCell(2);
	w.style.width = "10%";
	u.innerHTML = "<div class=\"col-xs-10\"><input type='text' readonly name='nord"+lastRow+"' id='nord"+lastRow+"' value='"+lastRow+"' class='form-control input-group-sm'> </div>";
	v.innerHTML = "<input type='text' value='' name='nvalz"+lastRow+"' id='nvalz"+lastRow+"' class='form-control input-group-sm'>";
	w.innerHTML = "<button type=\"button\" class=\"btn btn-danger btn-sm\" tabindex=\"6\" id=\"btndel\" name=\"btndel\" onClick=\"delInfo(this);\" />delete</button>";

}
function delInfo(r) {
		if(isNaN(r)==true){
			var i=r.parentNode.parentNode.rowIndex;
		}
		else{
			var i = r;
		}
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;
	 document.getElementById('MyTable2').deleteRow(i);
	 document.getElementById('txtcnt').value = lastRow - 2;
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
		for (z=i+1; z<=lastRow; z++){
			var nord = document.getElementById('nord' + z);
			var nvalz = document.getElementById('nvalz' + z);
			
			var x = z-1;
			nord.id = "nord" + x;
			nord.name = "nord" + x;
			
			nvalz.id = "nvalz" + x;
			nvalz.name = "nvalz" + x;
			
			document.getElementById("nord" + x).value = x;
		}
}
</script>
    
  </body>
</html>
