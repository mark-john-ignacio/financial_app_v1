<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvTrans_post";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access.php');

	$company = $_SESSION['companyid'];

	$arrtrans = array();
	$sqlloc = mysqli_query($con,"Select A.*, B.citemdesc From invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$_REQUEST['x']."'");
	$rowdetloc = $sqlloc->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrtrans[] = $row0;
	}

	$arravails= array();
	$sql = "select a.citemno, a.nsection_id, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
	From tblinventory a where a.compcode='$company' group by a.citemno, a.nsection_id";
	$sqltblinv= mysqli_query($con,$sql);
	$rowTemplate = $sqltblinv->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arravails[] = array('citemno' => $row0['citemno'], 'nsection_id' => $row0['nsection_id'], 'nqty' => $row0['nqty']);
	}

	$sqlcp = "select * from invtransfer where compcode='$company' and ctranno='".$_REQUEST['x']."'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$invtype = $rowcp['ctrantype'];
			if($invtype=="request"){
				$cwhsefin = $rowcp['csection2'];
			}else{
				$cwhsefin = $rowcp['csection1'];
			}
						
		}
	}

	$arrinvlvs = array();
	$sqlcp = "select * from items_invlvl where compcode='$company'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$arrinvlvs[] = array('section_nid' => $rowcp['section_nid'], 'cpartno' => $rowcp['cpartno'], 'nmin' => $rowcp['nmin'], 'nmax' => $rowcp['nmax'], 'nreorderpt' => $rowcp['nreorderpt']);
			
		}
	}

	$proceddtoapp = 1;

?>

	<input type="hidden" id="hdntemplist" value='<?=json_encode($arrtemplates)?>'>

				<table name='MyTbl' id='MyTbl' class="table table-scroll table-striped table-condensed">
					<thead>
						<tr>
							<th width="150">Item Code</th>
							<th>Item Description</th>
							<th width="70" class="text-center">Available</th>
							<th width="70" class="text-center">Needed</th>
							<th width="50" class="text-center">Minimum</td>
							<th width="50" class="text-center">Maximum</td>
							<th width="80" class="text-center">Reorder pt.</td>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($arrtrans as $myrow){

								$navail = 0;
								foreach($arravails as $rschkav){

									if($rschkav['citemno']==$myrow['citemno'] && $rschkav['nsection_id']==$cwhsefin){
										$navail = $rschkav['nqty'];
									}
								}

								$nnum = 0;
								$nmaxi = 0;
								$nreord = 0;
								foreach($arrinvlvs as $rschklvl){

									if($rschklvl['cpartno']==$myrow['citemno'] && $rschklvl['section_nid']==$cwhsefin){
										$nminum = $rschklvl['nmin'];
										$nmaxi = $rschklvl['nmax'];
										$nreord = $rschklvl['nreorderpt'];
									}
								}

								$bgval = "";
								if(floatval($navail)<=floatval($nminum)){
									$bgval = "bg-danger";
									$proceddtoapp = 0;
								}else{
									if(floatval($nminum)>=(floatval($navail)-floatval($myrow['nqty2']))){
										$bgval = "bg-danger";
										$proceddtoapp = 0;
									}else{
										if(floatval($nreord)>=(floatval($navail)-floatval($myrow['nqty2']))){
											$bgval = "bg-check";
										}
									}
								}


						?>
							<tr>
								<td class="<?=$bgval?>"><?=$myrow['citemno']?></td>
								<td class="<?=$bgval?>"><?=$myrow['citemdesc']?></td>
								<td class="text-center <?=$bgval?>"><?=number_format($navail);?></td>
								<td class="text-center <?=$bgval?>"><?=number_format($myrow['nqty2'])?></td>
								<td class="text-center <?=$bgval?>"><?=number_format($nminum);?></td>
								<td class="text-center <?=$bgval?>"><?=number_format($nmaxi)?></td>
								<td class="text-center <?=$bgval?>"><?=number_format($nreord)?></td>
							</tr>
						<?php
							}
						?>
					</tbody>
					<tfoot>
						<tr> 
							<td colspan="7" class="text-right">
								<button type="button" class="btn btn-success btn-sm" name="btnApp" id="btnApp" <?=($proceddtoapp == 0) ? "disabled" : ""?> onclick="proceed();">
									<i class="fa fa-check" aria-hidden="true"></i> Proceed to Approval
								</button>
							</td>
						</tr>
					</tfoot>
				</table>

