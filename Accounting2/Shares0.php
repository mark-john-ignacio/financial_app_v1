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

	<title>COOPERATIVE SYSTEM</title>
	<link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="lib/css/jquery.dataTables.min.css">
	<script src="lib/js/bootstrap.min.js"></script>

<script type="text/javascript">
function editfrm(x){
	document.getElementById("txtctranno").value = x;
	document.getElementById("frmedit").submit();
}

function trans(x,num){
	var r = confirm("Are you sure you want to "+x+" Sales Return No.: "+num);
	if(r==true){
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
	<div >
		<section>
			<font size="+2"><u>Shares &amp; Savings</u></font>
			<br><br>
            
              <ul class="nav nav-tabs">
                <li class="active"><a href="#home">Lists</a></li>
                <li><a href="#menu1">Salary Deductions</a></li>
              </ul>

  <div class="tab-content">
   
   
    <div id="home" class="tab-pane fade in active">
		<br>
			<button type="button" class="btn btn-primary" onClick="location.href='Shares_new.php'">Create New Transaction</button>
            <br><br>
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>Trans No</th>
                        <th>Type</th>
						<th>Cutoff Date</th>
						<th>Trans Date</th>
						<th>Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php
				$sql = "select a.*,b.dfrom, b.dto from savingshares a left join cutcodes b on a.cutcode=b.num order by a.cutcode, a.ctranno";
				$result=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
				?>
 					<tr>
						<td><a href="javascript:;" onClick="editfrm('<?php echo $row['ctranno'];?>');"><?php echo $row['ctranno'];?></a></td>
 						<td><?php echo $row['ctype'];?></td>
                       <td><?php echo $row['dfrom'];?> To <?php echo $row['dto'];?></td>
                        <td><?php echo $row['ddate'];?></td>
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
                </div>
                
                <div id="menu1" class="tab-pane fade">
                	
                </div>
                
          </div>

		</section>
	</div>		
    
<form name="frmedit" id="frmedit" method="post" action="Shares_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" />
</form>		

	<script type="text/javascript" language="javascript" src="lib/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="dist/bootstrapPager.min.js"></script>
	
	<script>
	$('#example').DataTable({
	   pagingType: "bootstrapPager",
	   pagerSettings: {
			searchOnEnter: true,
			language:  "Page ~ of ~ pages"
	   }
	});
	
	$(document).ready(function(){
    $(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
    $('.nav-tabs a').on('shown.bs.tab', function(event){
        var x = $(event.target).text();         // active tab
        var y = $(event.relatedTarget).text();  // previous tab
       // $(".act span").text(x);
        //$(".prev span").text(y);
    });
});

	</script>


</body>
</html>