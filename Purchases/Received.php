<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Receive.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F2
		window.location = "Received_new.php";
	  }
	});
function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	var r = confirm("Are you sure you want to "+x+" Receiving No.: "+num);
	if(r==true){
		//alert('Received_Tran.php?x='+num+'&typ='+x);
	var page = 'Received_Tran.php?x='+num+'&typ='+x;
	var name = 'popwin';
	var w = 100;
	var h = 100;
	var myleft = (screen.width)?(screen.width-w)/2:100;
	var mytop = (screen.height)?(screen.height-h)/2:100;
	var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
	myPopup = window.open(page, name, setting);
	}
}
</script>
</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
				<font size="+2"><u>Receiving List</u></font>	
            </div>
        </div>
			<br><br>
			<button type="button" class="btn btn-primary" onClick="location.href='Received_new.php'"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Receive No</th>
						<th>Customer</th>
						<th>Receive Date</th>
						<th>Gross</th>
						<th>Receive Type</th>
                        <th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
				$sql = "select a.*,b.cname from receive a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' order by a.ddate DESC";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
						<td><?php echo $row['ccode'];?> - <?php echo $row['cname'];?> </td>
                        <td><?php echo $row['dreceived'];?></td>
						<td align="right"><?php echo $row['ngross'];?></td>
						<td><?php echo $row['creceivetype'];?></td>
                        <td align="center">
                        <div id="msg<?php echo $row['ctranno'];?>">
                        	<?php 
							if(intval($row['lcancelled'])==intval(0) && intval($row['lapproved'])==intval(0)){
							?>
								<a href="javascript:;" onClick="trans('POST','<?php echo $row['ctranno'];?>')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['ctranno'];?>')">CANCEL</a>
							<?php
                            }
							else{
								if(intval($row['lcancelled'])==intval(1)){
									echo "Cancelled";
								}
								if(intval($row['lapproved'])==intval(1)){
									echo "Posted";
								}
							}
							
							?>
                            </div>
                        </td>
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="Received_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

    <link rel="stylesheet" type="text/css" href="../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$('#example').DataTable({bSort:false});
	</script>

</body>
</html>