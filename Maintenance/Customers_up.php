<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Customers.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');

?>
<!DOCTYPE html>
<html>
<head>
<script src="jqs/external/jquery/jquery.js"></script>
<script src="jqs/jquery-ui.js"></script>
<link href="jqs/jquery-ui.css" rel="stylesheet">


	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">

</head>

<body style="padding:5px">
	<div>
		<section>
        <div>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
					  <th>&nbsp;</th>
						<th>Customer Code</th>
						<th>Customer Name</th>
						<th>Customer Type</th>
						<!--<th>Delete</th>-->
					</tr>
				</thead>

				<tbody>
              	<?php
				$company = $_SESSION['companyid'];
				
				if($_REQUEST['f'] == "search"){
					
					$sql = "select * from customers where compcode='$company' and (cempid like '%$_POST[search]%' or clname like '%$_POST[search]%' or cfname like '%$_POST[search]%') order by cempid";
				}else{
					$sql = "select * from customers where compcode='$company' order by cempid";
				}
				
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
 					  <td>
    <?php 
	$citemno = $row['cempid'];
	
	if(!file_exists("../imgemps/".$citemno.".jpg")){
		$imgsrc = "../images/emp.jpg";
		
		echo" <input type=\"button\" name=\"btnupload\" id=\"btnupload\" value=\"UPLOAD IMAGE\" onClick=\"popwin('".$citemno."');\"> ";
	}
	else{
		$imgsrc = "../imgemps/".$citemno.".jpg";
		
		echo "<img src=\"".$imgsrc."\" width=\"50\" height=\"50\">";
	}
	?>
    
                      
                      </td>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['cempid'];?>');" class=info><?php echo $row['cempid'];?></a></td>
						<td><?php echo utf8_encode($row['cname']);?></td>
						<td><?php echo $row['ccustomertype'];?></td>
					   <!--<td align="center"><input class='btn btn-danger btn-xs' type='button' id='row_<?//php echo $row['cempid']; ?>_delete' value='delete' onClick="deleteRow('<?//php echo $row['cempid'];?>');"/></td>-->
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>
			</table>

		</section>
	</div>		

<script>
function popwin(id){
var page = 'uploadpic_up.php?id='+id;
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

</body>
</html>