<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$salesno = $_REQUEST['hdnSI'];

$name = $_POST['chkitem'];
$ctr = 0;
$itms = "";

foreach ($name as $id){
$ctr = $ctr + 1;

if($ctr>1){
	$itms = $itms."','";	
}

$itms = $itms.$id;

}

//echo $itms;

$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc,b.cunit as mainuom 
from receive_t a 
left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
left join
	(Select x.creference,x.citemno,sum(x.nqty) as nqty
     From purchreturn_t x
     left join purchreturn y on x.compcode=y.compcode and x.ctranno=y.ctranno
     Where x.compcode='$company' and x.creference='$salesno' and y.lcancelled=0 and y.lapproved=1
     group by x.creference,x.citemno
     ) c on a.ctranno=c.creference and a.citemno=c.citemno
where a.compcode='$company' and a.ctranno='$salesno' and a.nident in ('$itms')
order by a.nident";

//echo $sql;

$result=mysqli_query($con,$sql);
				
if (!mysqli_query($con, $sql)) {
	printf("Errormessage: %s\n", mysqli_error($con));
} 
	
$totamt = 0;				
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$totamt = $totamt + $row['namount'];
?>

<script type="text/javascript">
{
	var tbl = window.opener.document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=window.opener.document.getElementById('MyTable').insertRow(-1);
	var u=a.insertCell(0);
	var v=a.insertCell(1);
	var v2=a.insertCell(2);
	var w=a.insertCell(3);
		w.style.width = "100px";
		w.style.padding = "1px";
	var x=a.insertCell(4);
		x.style.width = "100px";
		x.style.padding = "1px";
	var y=a.insertCell(5);
		y.style.width = "100px";
		y.style.padding = "1px";
	var w2=a.insertCell(6);
		w2.style.width = "100px";
		w2.style.padding = "1px";
		w2.style.paddingLeft = "10px";
	var z=a.insertCell(7);
		z.style.width = "80px";
		z.align = "right";
	
	u.innerHTML = "<input type='hidden' value='<?php echo $salesno;?>' name='txtcreference"+lastRow+"' id='txtcreference"+lastRow+"'> <input type='hidden' value='<?php echo $row['nident'];?>' name='txtnrefident"+lastRow+"' id='txtnrefident"+lastRow+"'> <input type='hidden' value='<?php echo $row['citemno'];?>' name='txtitemcode"+lastRow+"' id='txtitemcode"+lastRow+"'><?php echo $row['citemno'];?>";
	v.innerHTML = "<input type='hidden' value='<?php echo $row['citemdesc'];?>' name='txtitemdesc"+lastRow+"' id='txtitemdesc"+lastRow+"'><?php echo $row['citemdesc']." (".$row['nfactor']." ".$row['mainuom']."/".$row['cunit'].")";?>";
	v2.innerHTML = "<input type='hidden' value='<?php echo $row['cunit'];?>' name='txtcunit"+lastRow+"' id='txtcunit"+lastRow+"'><?php echo $row['cunit'];?>";
	w.innerHTML = "<div class='col-xs-12'><input type='text' value='<?php echo ($row['nqty']-$row['nqty2']);?>' class='nqty form-control input-xs' style='text-align:right' name='txtnqty"+lastRow+"' id='txtnqty"+lastRow+"' onKeyup=\"computeamt(this.value,this.name,event.keyCode);\" onkeydown=\"return isNumber(event.keyCode)\" onBlur=\"chkdecimal(this.value,"+lastRow+");\" > <input type='hidden' value='<?php echo ($row['nqty']-$row['nqty2']);?>' name='txtnqtyOrig"+lastRow+"' id='txtnqtyOrig"+lastRow+"'></div>";
	x.innerHTML = "<div class='col-xs-12'><input type='text' value='<?php echo $row['nprice'];?>' class='form-control input-xs' style='text-align:right' name='txtnprice"+lastRow+"' id='txtnprice"+lastRow+"' onKeyup=\"computeamt(this.value,this.name,event.keyCode);\" onkeydown=\"return isNumber(event.keyCode)\" onBlur=\"chkdecimal(this.value,"+lastRow+");\"></div>";
	y.innerHTML = "<div class='col-xs-12'><input type='text' value='<?php echo $row['namount'];?>' class='form-control input-xs' style='text-align:right' name='txtnamount"+lastRow+"' id='txtnamount"+lastRow+"' readonly></div>";
	w2.innerHTML = "<div class='col-xs-12'><input type='text' value='<?php echo $row['nfactor'];?>' name='txtnfactor"+lastRow+"' id='txtnfactor"+lastRow+"' class='nqty form-control input-xs' style='text-align:right'></div>";
	z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>";

}

window.opener.document.getElementById('txtnGross').value = '<?php echo $totamt; ?>';
</script>

<?php					
				}
				
				mysqli_close($con);

//close this window
echo "<script language='Javascript'>" ;
echo "window.opener.document.getElementById('txtcust').readOnly=true;";
echo "window.close();" ;
echo "</script>" ;

?>
