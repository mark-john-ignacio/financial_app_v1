<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$rrno = $_REQUEST['id'];
$TotalGross = floatval($_REQUEST['amt']);
$nDebit = 0;
$nCredit = 0;

$sql = "select * from glactivity where ctranno='$rrno'";


//echo "select * from glactivity where ctranno='$rrno'";

$result=mysqli_query($con,$sql);
				
if (!mysqli_query($con, $sql)) {
	printf("Errormessage: %s\n", mysqli_error($con));
} 
					
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))

{
?>

<script type="text/javascript">
{
	var tbl = window.opener.opener.document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=window.opener.opener.document.getElementById('MyTable2').insertRow(-1);
	var u=a.insertCell(0);
		u.style.padding = "1px";
		u.style.width = "100px";
	var v=a.insertCell(1);
		v.style.padding = "1px";
	var w=a.insertCell(2);
		w.style.padding = "1px";
		w.style.width = "150px";
	var x=a.insertCell(3);
		x.style.width = "150px";
		x.style.padding = "1px";
	var y=a.insertCell(4);
		y.style.width = "200px";
		y.style.padding = "1px";
	var z=a.insertCell(5);
		z.style.padding = "1px";
	var b=a.insertCell(6);
		b.style.width = "50px";
		b.style.padding = "1px";


	u.innerHTML = "<input type='hidden' name=\"txtcrefrr"+lastRow+"\" id=\"txtcrefrr"+lastRow+"\" value=\"<?php echo $rrno;?>\"><input type='text' name=\"txtacctno"+lastRow+"\" id=\"txtacctno"+lastRow+"\" class=\"form-control input-sm\" value=\"<?php echo $row["acctno"];?>\" style=\"text-transform:uppercase\" readOnly>";
	v.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"form-control input-sm\" readonly value=\"<?php echo $row["ctitle"];?>\">";
	w.innerHTML = "<input type='text' name=\"txtdebit"+lastRow+"\" id=\"txtdebit"+lastRow+"\" class=\"form-control input-sm\" value=\"<?php echo $row["ndebit"];?>\" onkeydown=\"return isNumber(event.keyCode)\" onkeyup=\"compgross();\" required>";
	x.innerHTML = "<input type='text' name=\"txtcredit"+lastRow+"\" id=\"txtcredit"+lastRow+"\" class=\"form-control input-sm\" value=\"<?php echo $row["ncredit"];?>\" onkeydown=\"return isNumber(event.keyCode)\" onkeyup=\"compgross();\" required>";
	y.innerHTML = "<input type='text' name=\"txtsubs"+lastRow+"\" id=\"txtsubs"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Name...\" onkeyup=\"searchSUBS(this.name);\"> <input type='hidden' name=\"txtsubsid"+lastRow+"\" id=\"txtsubsid"+lastRow+"\">";
	z.innerHTML = "<input type='text' name=\"txtacctrem"+lastRow+"\" id=\"txtacctrem"+lastRow+"\" class=\"form-control input-sm\">";
	b.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row2_"+lastRow+"_delete' class='delete' value='delete' onClick=\"deleteRow(this);\"/>";

}

</script>

<?php					

				$nDebit = $nDebit + floatval($row["ndebit"]);
				$nCredit = $nCredit + floatval($row["ncredit"]);

				}
				
				if($nDebit = $nCredit){
					$TotalGross = $TotalGross + $nDebit;
				}
				
				mysqli_close($con);


				
echo $rrno.": DONE!";
//close this window
echo "<script language='Javascript'>" ;
echo "window.opener.opener.document.getElementById('txtcust').readOnly=true;";
echo "window.opener.opener.document.getElementById('txtcustchkr').value='';";
echo "window.opener.opener.document.getElementById('txtnGross').value='".$TotalGross."';";
echo "window.close();" ;
echo "</script>" ;

?>
