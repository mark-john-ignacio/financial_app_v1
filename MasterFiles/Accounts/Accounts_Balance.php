<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Accounts";

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

	$date = strtotime("-1 year", time());
	@$begbaldate = "12/31/".date("Y", $date);
	@$begbalrems = "";

	$getbegbaldet = mysqli_query($con,"SELECT * FROM `accounts_beg` WHERE compcode='$company'"); 
	if (mysqli_num_rows($getbegbaldet)!=0) {
		while($rows = mysqli_fetch_array($getbegbaldet, MYSQLI_ASSOC)){
			@$begbaldate = date_format(date_create($rows['begbaldate']), "m/d/Y");
			@$begbalrems = $rows['cremarks'];
		}
	}


	$query = mysqli_query($con,"SELECT (CASE WHEN A.mainacct='0' OR ctype='General' THEN A.cacctid ELSE A.mainacct END) as 'main', A.cacctno, A.cacctid, A.cacctdesc, A.ctype, A.ccategory, A.mainacct, A.cFinGroup, A.lcontra, A.nlevel, A.nbalance FROM `accounts` A where A.compcode='".$_SESSION['companyid']."' ORDER BY ccategory, nlevel, cacctid");
	$resallaccts = $query->fetch_all(MYSQLI_ASSOC);

	function getchild($acctcode, $nlevel){
		global $resallaccts;

		foreach($resallaccts as $rsz){
			if($rsz['mainacct']==$acctcode){

				$isexcv = "";
				if($rsz['ctype']=="General"){
					$isexcv = "readonly";
				}

				$style=setTabsLevel($rsz['nlevel']);

				 echo "<tr><td style=\"padding-left:".$style."px\" valign=\"middle\"> ".$rsz['cacctid']."</td> <td style=\"padding-left:".$style."px\" valign=\"middle\">".setIcons($style) . " ".$rsz['cacctdesc']."</td> <td>".$rsz['ctype']."</td> <td><input type=\"text\" class=\"numeric form-control input-xs text-right\" id=\"txt".$rsz['cacctid']."\" name=\"txt".$rsz['cacctid']."\" tabindex=\"1\" placeholder=\"0.0000\" value=\"".$rsz['nbalance']."\" autocomplete=\"off\" maxlength=\"255\" data-hdr=\"".$rsz['mainacct']."\" ".$isexcv ."/></td> </tr>";

				if($rsz['ctype']=="General"){
					getchild($rsz['cacctid'], $rsz['nlevel']);
				}
			}
		}
	}

	function setTabsLevel($nlevel){
		$GENxyz = intval($nlevel);
						
		$GENxyz0 = 0;
		if($GENxyz>1){
			$GENxyz0 = (5 * $GENxyz) + ($GENxyz * 2);
		}

		return $GENxyz0;
	}

	function setIcons($GENxyz0){
		$symxcol = "";
		if($GENxyz0==14){
			$symxcol = "&#8226; ";
		}else if($GENxyz0==21){
			$symxcol = "&#10022; ";
		}else if($GENxyz0==28){
			$symxcol = "&#10070; ";
		}else if($GENxyz0==35){
			$symxcol = "&#10148; ";
		}

		return $symxcol;
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
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">


	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../include/autoNumeric.js"></script>

	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<script src="../../Bootstrap/js/moment.js"></script>
	<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px">
	<form action="Accounts_Balance_Save.php" name="frmacct" id="frmacct" method="post">
		<fieldset>
    	<legend>Chart of Accounts Beginning Balance</legend>	

				<table width="100%" border="0">
					<tr>
						<tH nowrap width="50px">&nbsp;As Of: &nbsp;</tH>
						<td style="padding:2px"> 
							<div class="col-xs-2">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?=@$begbaldate; ?>" />
							</div>
						
						</td>
					</tr>
					<tr>
						<tH nowrap width="50px">&nbsp;Remarks: &nbsp;</tH>
						<td style="padding:2px"> 
							<div class="col-xs-8">
								<input type='text' class="form-control input-sm" id="xremarkshdr" name="xremarkshdr" value="<?=@$begbalrems; ?>" />
							</div>
						
						</td>
					</tr>
				</table>


					<br>
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

					<br>
					<div class="tab-content" style="margin: 0px;padding: 3px;width: 100%;height: 300px;text-align: left;overflow: auto">

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
					
							<table class="table table-hover table-sm" role="grid" id="MyTable<?=$rs?>">
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
												$xcgen = setTabsLevel($row['nlevel']);
									?>
											<tr>
												<td style="padding-left:<?=$xcgen;?>px" valign="middle"><?=$row['cacctid']?></td>
												<td style="padding-left:<?=$xcgen;?>px" valign="middle"><?=setIcons($xcgen). " ". $row['cacctdesc']?></td>
												<td valign="middle"><?=$row['ctype']?></td>
												<td><input type="text" class="numeric form-control input-xs text-right" id="txt<?=$row['cacctid']?>" name="txt<?=$row['cacctid']?>" tabindex="1" placeholder="0.0000" value="<?=$row['nbalance']?>" autocomplete="off" maxlength="18" data-hdr="<?=$row['mainacct']?>" <?=($row['ctype']=="General") ? "readonly" : "";?>/></td>
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


					<div class="row nopadwtop2x">
						<div class="col-xs-7">
								<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='Accounts.php?f=';" id="btnMain" name="btnMain">
						Back to Main<br>(ESC)</button>

								
						<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();">SAVE<br> (F2)</button>
						</div>	

					</div>  
			

		</fieldset>
	</form>

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

			$(".numeric").autoNumeric('init',{mDec:4});
			$("input.numeric").on("click focus", function () {
				$(this).select();
			});

			$("input.numeric").on("keyup", function (e) {
				if (e.which === 13) {
					console.log(e.which);
					$(this).closest("tr").next().find("input.numeric").focus();
				}
				
				computehdr($(this).data("hdr"), $(this).attr("id"));
			});

			$("input.numeric").on("blur", function () {
				if($(this).val()==""){
					$(this).val(0);
					$(this).autoNumeric('destroy');
					$(this).autoNumeric('init',{mDec:4});

				}
			});

			$('#date_delivery').datetimepicker({
				format: 'MM/DD/YYYY',
				// onChangeDateTime:changelimits,
				//minDate: new Date(),
			});


		});

		$(document).keydown(function(e) {	
			
			if(e.keyCode == 112) { //F1
				e.preventDefault();
				$("#btnadd").click();
			}
		});

		function computehdr($dhdr,$did){

			if($dhdr != "0"){
				$currint = $("#txt"+$dhdr).val().replace(/,/g,'');
				$xtot = 0;

				jQuery('.numeric').each(function() {

					if($(this).data("hdr")==$dhdr){
						$xtot = parseFloat($xtot) + parseFloat($(this).val().replace(/,/g,''));
					}

				});


				$("#txt"+$dhdr).val($xtot);
				$("#txt"+$dhdr).autoNumeric('destroy');
				$("#txt"+$dhdr).autoNumeric('init',{mDec:4});
			}
		}

		function chkform(){
			$iswuth = 0;
			jQuery('.numeric').each(function() {

				if(parseFloat($(this).val().replace(/,/g,''))>=1){
					$iswuth++;
				}

			});

			if($iswuth == 0){
				$("#AlertMsg").html("<center><b>NO VALUE TO BE SAVED!</b></center>");
			 	$("#AlertModal").modal('show');
			}else{
				$("#frmacct").submit();
			}
		}

	</script>
