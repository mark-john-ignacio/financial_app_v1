<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
  
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">

function checkAll(field)
{
for (var i=0;i<field.length;i++){
var e = field[i];
if (e.name == 'chkTranNo[]')
e.checked = field.allbox.checked;
}

var products = document.getElementsByName( 'chkTranNo[]' );
for( var n = 1; n <= products.length; n++ )
   {
		if(document.getElementById("chkTranNo"+n).checked == true){
		document.getElementById("nyca"+n).style.backgroundColor="#FFCC99";
		}
		
		else if(document.getElementById("chkTranNo"+n).checked == false){
		document.getElementById("nyca"+n).style.backgroundColor=document.getElementById("chkTranNobg"+n).value;
		}
   }

}


function chk(id,bgcolor){
	if(document.getElementById("chkTranNo"+id).checked == true){
		document.getElementById("nyca"+id).style.backgroundColor="#FFCC99";
	}else if(document.getElementById("chkTranNo"+id).checked == false){
		document.getElementById("nyca"+id).style.backgroundColor=bgcolor;
	}
}

function openpop(x){
	
	var page = 'view.php?x=' + x ;
	var name = 'popup';
	var w = 710;
	var h = 500;
	var myleft = (screen.width)?(screen.width-w)/2:100;
	var mytop = (screen.height)?(screen.height-h)/2:100;
	var setting = "width=" + w + ",height=" + h + ",top=" + mytop + ",left=" + myleft + ",scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no";
	myPopup = window.open(page, name, setting);

	
}
</script>

<style>
.right{ text-align: right;
}

.left{
  text-align: left;
}

.center{
  text-align: center;
}

.dataTables_filter input {width:250px}
</style>
</head>

<body style="padding:5px; height:600px">
	<form name="frmTran" id="frmTran" method="post" action="POS.php">

			<button type="submit" class="btn btn-primary btn-md"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;POST TRANSACTIONS</button>
            
			<h3>Pending Invoices</h3>
			<table class="table table striped" cellspacing="0" width="100%">
				<thead>
					<tr>
                        <th width="24"><input name="allbox" type="checkbox" value="Check All" onClick="javascript:checkAll(document.frmTran)"></th>
						<th>Sales No</th>
						<th>Customer</th>
						<th>SI Date</th>
						<th>Remarks</th>
                        <th>Gross</th>
					</tr>
				</thead>
			
            
            <tbody>
              	<?php
				$sql = "select a.*,b.cname from sales a left join customers b on a.ccode=b.cempid where a.compcode='001' and lcancelled=0 and lapproved=0 and dcutdate between '2022-01-01' and '2022-12-31' and ctranno not in (Select ctranno from glactivity) order by ctranno";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
				$nums = 0;
				$bg = "";	
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$nums = $nums + 1;
					
						if ($bg == "#FFF7DF"){
						  $bg = "#FFFFFF";
						}
						else{
						  $bg = "#FFF7DF";
						}
						
				?>
 					<tr bgcolor="<?php echo $bg;?>" id="nyca<?php echo $nums;?>">
                    	<td> <input type="checkbox" name="chkTranNo[]" id="chkTranNo<?php echo $nums;?>" value="<?php echo $row['ctranno'];?>" onClick="chk('<?php echo $nums;?>','<?php echo $bg;?>')"/> <input type="hidden" name="chkTranNobg<?php echo $nums;?>" id="chkTranNobg<?php echo $nums;?>" value="<?php echo $bg?>" /></td>
						<td><a href="javascript:;" onClick="openpop('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
						<td><?php echo $row['ccode'];?> - <?php echo utf8_encode($row['cname']);?> </td>
                        <td><?php echo $row['dcutdate'];?></td>
						<td><?php echo $row['cremarks'];?></td>
                        <td align="right"><?php echo $row['ngross'];?></td>
					</tr>
                <?php 
				}
				
				mysqli_close($con);
				
				?>
               
				</tbody>

            </table>
    
</form>		

</body>
</html>