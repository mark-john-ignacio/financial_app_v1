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

  <style type='text/css'>

.deleterow{cursor:pointer}
  </style>

</head>

<body>
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
$citmno = str_replace(",","','",$_REQUEST['itms']);

$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc 
from receive_t a 
left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
left join
	(Select x.creference,x.citemno,sum(x.nqty) as nqty
     From purchreturn_t x
     left join purchreturn y on x.compcode=y.compcode and x.ctranno=y.ctranno
     Where x.compcode='$company' and x.creference='$salesno' and y.lcancelled=0
     group by x.creference,x.citemno
     ) c on a.ctranno=c.creference and a.citemno=c.citemno
where a.compcode='$company' and a.ctranno='$salesno' and a.citemno NOT IN ('".$citmno."')
order by a.nident";

//echo $sql ;
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
						

	if(($row['nqty']-$row['nqty2'])>0) {
					
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

</body>
</html>