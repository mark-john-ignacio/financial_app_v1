<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Accounts.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$result = mysqli_query ($con, "select DISTINCT ccategory from accounts WHERE compcode = '".$company."' and cstatus='ACTIVE'"); 
	$row = $result->fetch_all(MYSQLI_ASSOC);

	$cats = [];
	foreach ($row as $r) {
		$cats[] = $r['ccategory'];
	}


	$query = mysqli_query($con,"SELECT (CASE WHEN A.mainacct='0' OR ctype='General' THEN A.cacctid ELSE A.mainacct END) as 'main', A.cacctno, A.cacctid, A.cacctdesc, A.ctype, A.ccategory, A.mainacct, A.cFinGroup, A.lcontra, A.nlevel, A.nbalance FROM `accounts` A where A.compcode='".$_SESSION['companyid']."' ORDER BY ccategory, nlevel, cacctid");
	$resallaccts = $query->fetch_all(MYSQLI_ASSOC);

	function getchild($acctcode, $nlevel){
		global $resallaccts;

		foreach($resallaccts as $rsz){
			if($rsz['mainacct']==$acctcode){
				 echo "<tr><td>".$rsz['cacctid']."</td> <td>".$rsz['cacctdesc']."</td> <td>".$rsz['ctype']."</td> <td> </td> </tr>";

				if($rsz['ctype']=="General"){
					getchild($rsz['cacctid'], $rsz['nlevel']);
				}
			}
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
	<link href="../../Bootstrap/css/jquery.bootstrap.treeselect.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">


	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

</head>

<body style="padding:5px">

	<div>
		<section>
        <div>
        	<div style="float:left; width:50%">
							<font size="+2"><u>Chart of Accounts</u></font>	
          </div>
        </div>       
				<br><br><br>
				<ul class="nav nav-tabs">
					<?php
						$cnt = 0;
						foreach($cats as $rs){
							$cnt++;

							if($cnt==1){
								$setact = "active";
							}else{
								$setact = "";
							}
					?>
          <li class="<?=$setact?>" id="li<?=$rs?>"><a href="#<?=$rs?>"><?=$rs?></a></li>
					<?php
						}
						?>
          <!--<li id="licos"><a href="#cos">COST OF SALES</a></li>-->
          
        </ul>

			<br><br>
			<div class="tab-content">

				<?php
					$cnt = 0;
					foreach($cats as $rs){
						$cnt++;

						if($cnt==1){
							$setact = " active";
						}else{
							$setact = "";
						}
				?>

        <div id="<?=$rs?>" class="tab-pane fade in<?=$setact?>" style="padding-left:10px">
			
					<table class="table table-hover" role="grid" id="MyTable<?=$rs?>">
						<thead>
							<tr>
								<th width="150px">Acct No</th>
								<th>Description</th>
								<th width="150px">Type</th>
								<th width="200px" style="text-align: right">Beg Balance</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($resallaccts as $row)
								{
									if(intval($row['nlevel'])==1 && $row['ccategory']==$rs){
							?>
									<tr>
										<td><?=$row['cacctid']?></td>
										<td><?=$row['cacctdesc']?></td>
										<td><?=$row['ctype']?></td>
										<td>Beg Balance</td>
									</tr>
							<?php
										if($row['ctype']=="General"){
											getchild($row['cacctid'], $row['nlevel']);
										}
										
									}
								}
							?>
						</tbody>
					</table>

				</div> 
				<?php
					}
				?>
			</div>

		</section>
	</div>		

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
	</div>



<?php

mysqli_close($con);
?>
</body>
</html>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	
		$(document).ready(function() {

			$(".nav-tabs a").click(function(){
        $(this).tab('show');
      });

			$("#frmnew").on('submit', function (e) {
				e.preventDefault();

				var form = $("#frmnew");
				var formdata = form.serialize();
					$.ajax({
					url: 'Accounts_add.php',
					type: 'POST',
					async: false,
					data: formdata,
					success: function(data) {
						if(data.trim()!="False"){
							$('#myModal').modal('hide');

							alert(data);
							location.reload();
						}else{
							alert("Error saving new account!");	
						}
					}
				});							

			});


		});

		$(document).keydown(function(e) {	
			
			if(e.keyCode == 112) { //F1
				e.preventDefault();
				$("#btnadd").click();
			}
		});

	</script>
