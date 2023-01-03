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
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap-select.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css">
    
  <script type="text/javascript" src="../js/jquery.js"></script>
  
  <script type="text/javascript" src="lib/js/bootstrap-select.js"></script>
  <script src="../js/bootstrap.min.js"></script>
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

  </script>
  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body style="padding:5px">
<form method="post" name="frm" id="frm" action="Received_POPut.php">
<table width="100%">
<tr>
	<td align="left"><b>Items Listing</b><input type="hidden" name="hdnSI" id="hdnSI" value="<?php echo $_REQUEST['id']?>" /></td>
    <td align="right"><input type="submit" value="OK">
<input type="button" value="Cancel" onClick="window.close();"></td>
</tr>
</table>

<div class="alt2" dir="ltr" style="
	 				background-image:url(images/body1.jpg);
	  				background-attachment:fixed;
					margin: 0px;
					padding: 0px;
					border: 0px outset;
					width: 100%;
					height: 300px;
					text-align: left;
					overflow: auto">
  <table width="100%" cellspacing="0" class="table-condensed">
    <tr bgcolor="#CCCCCC">
      <th align="left"><input name="allbox" type="checkbox" value="Check All" onclick="javascript:checkAll(document.frm)" />&nbsp;Product ID</th>
      <th>Description</th>
      <th>Unit</th>
      <th>Qty</th>
      <th>Price</th>
      <th>Amount</th>
    </tr>
    <?php 

$salesno = $_REQUEST['id'];
$itmnno = $_REQUEST['itmn'];

if ($itmnno<>""){
	$qry="and a.nident not in ($itmnno)";
}else{
	$qry="";
}

$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc 
from purchase_t a 
left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
left join
	(Select x.creference,x.citemno,sum(x.nqty) as nqty
     From receive_t x
     left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno
     Where x.compcode='$company' and x.creference='$salesno' and y.lcancelled=0
     group by x.creference,x.citemno
     ) c on a.cpono=c.creference and a.citemno=c.citemno
where a.compcode='$company' and a.cpono='$salesno' $qry
order by a.nident";

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
						
	if(($row['nqty']-$row['nqty2'])<>0) {

					
?>

    <tr bgcolor="<?php echo $bg; ?>" id="nyca<?php echo $ctr;?>">
      <td><input id="chkitem<?php echo $ctr;?>" name="chkitem[]" type="checkbox" value="<?php echo $row['nident']; ?>" onclick="javascript:chk('<?php echo $ctr;?>','<?php echo $bg; ?>')">&nbsp;&nbsp;<?php echo $row['citemno']; ?>
       <input type="hidden" name="chkTranNobg<?php echo $ctr;?>" id="chkTranNobg<?php echo $ctr;?>" value="<?php echo $bg; ?>" />
      </td>
      <td><?php echo $row['citemdesc']; ?></td>
      <td><?php echo $row['cunit']; ?></td>
      <td align="right"><?php echo ($row['nqty']-$row['nqty2']); ?></td>
      <td align="right"><?php echo $row['nprice']; ?></td>
      <td align="right"><?php echo $row['namount']; ?></td>
    </tr>

<?php
	}
				}
				
				mysqli_close($con);

?>

</table>

</div>
</form>
</body>
</html>