<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


$company = $_SESSION['companyid'];
$corno = $_REQUEST['txtctranno'];


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>COOPERATIVE SYSTEM</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../include/jquery-maskmoney.js" type="text/javascript"></script>


<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtctranno').focus(); disabled();">
<?php

    	$sqlchk = mysqli_query($con,"Select a.cacctcode, a.ccode, a.namount, a.cpaymethod, a.cpaytype, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.lapproved, a.lcancelled, a.lprintposted, a.cornumber, a.cremarks, a.caccttype, b.cname, d.cname as csuppname, c.cacctdesc From receipt a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join accounts c on a.compcode=c.compcode and a.cacctcode=c.cacctno left join suppliers d on a.compcode=d.compcode and a.ccode=d.ccode where a.compcode='$company' and a.ctranno='$corno'");
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctcode'];
			$nDebitDesc = $row['cacctdesc'];
			$cCode = $row['ccode'];
			$cName = $row['cname'];
			 if($cName==""){
				 $cName = $row['csuppname'];
			 }
			 
			$cPaytype = $row['cpaytype'];
			$cPayMeth = $row['cpaymethod'];
			$cORNo = $row['cornumber'];
			$dDate = $row['dcutdate'];
			$nAmount = $row['namount'];
			$cAcctType = $row['caccttype'];
			$cRemarks = $row['cremarks'];
			
			$lPosted = $row['lapproved'];
			$lCancelled = $row['lcancelled'];
			$lPrintPost = $row['lprintposted'];
		}

?>
<form action="OR_editsave2.php" name="frmOR" id="frmOR" method="post">
	<fieldset>
    	<legend>Receive Payment</legend>	
        <table width="100%" border="0">
  <tr>
    <tH>OR No.:</tH>
    <td colspan="3" style="padding:2px;">
    <div class="col-xs-12">
    <div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmOR');"></div>
      
      <input type="hidden" name="hdnorigNo" id="hdnorigNo" value="<?php echo $corno;?>">
      
      <input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
      <input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
      <input type="hidden" name="hdnprintpost" id="hdnprintpost" value="<?php echo $lPrintPost;?>">
      &nbsp;&nbsp;
      <div id="statmsgz" style="display:inline"></div>
      </div>
      
    </td>
    </tr>
  <tr>
    <tH width="210">
    	<select id="selDepAcct" name="selDepAcct" class="form-control input-sm selectpicker" tabindex="3">
        <option value="Deposit" <?php if($cAcctType=="Deposit") { echo "selected"; } ?>>Deposit To Account</option>
          <option value="Group" <?php if($cAcctType=="Group") { echo "selected"; } ?>>Group w/ Undeposited Funds</option>
        </select>
    
    </tH>
    <td style="padding:2px;" width="500">
    <?php
    $sqlchk = mysqli_query($con,"Select a.cvalue, b.cacctdesc, IFNULL(b.nbalance,0) as nbalance From parameters a left join accounts b on a.compcode=b.compcode and  a.cvalue=b.cacctno where a.compcode='$company' and ccode='ORDEBIT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			//$nDebitDef = $row['cvalue'];
			//$nDebitDesc = $row['cacctdesc'];
			$nBalance = $row['nbalance'];
		}
	}else{
		//$nDebitDef = "";
		//$nDebitDesc =  "";
		$nBalance = 0.000;
	}
	?>
  <div class="col-xs-12">
    <div class="col-xs-8">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
    </div> 

        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
   </div>     
    </td>
    <tH width="150">Balance:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8">
    <input type="text" id="txtacctbal" name="txtacctbal" class="form-control input-sm" readonly value="<?php echo $nBalance;?>">
    </div>
    </td>
  </tr>
  <tr>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
    <tH>&nbsp;</tH>
    <td style="padding:2px;">&nbsp;</td>
  </tr>
  <tr>
    <tH width="210" valign="top">PAYOR:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-12">
        <div class="col-xs-8">
        	<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off" value="<?php echo $cName ;?>"  />
		</div> 
		<div class="col-xs-4">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode ;?>">
        </div>
    </div>        
    </td>
    <tH width="150" style="padding:2px">Payment Type:</tH>
    <td style="padding:2px">
    <div class="col-xs-7">

     <select id="selpaytype" name="selpaytype" class="form-control input-sm selectpicker">
       <option value="None" <?php if($cPaytype=="None") { echo "selected"; } ?>>No Reference</option>
       <option value="Sales" <?php if($cPaytype=="Sales") { echo "selected"; } ?>>Sales</option>
<!--
       <option value="Grocery" <?php //if($cPaytype=="Grocery") { echo "selected"; } ?>>Grocery</option>
       <option value="Cripples" <?php //if($cPaytype=="Cripples") { echo "selected"; } ?>>Cripples</option>
       <option value="Shares" <?php //if($cPaytype=="Shares") { echo "selected"; } ?>>Shares</option>
       <option value="Savings" <?php // if($cPaytype=="Savings") { echo "selected"; } ?>>Savings</option>
       <option value="Loan" <?php // if($cPaytype=="Loan") { echo "selected"; } ?>>Coop Loan</option>
       <option value="StPeter" <?php // if($cPaytype=="StPeter") { echo "selected"; } ?>>St. Peter Loan</option>
       <option value="Fee" <?php //if($cPaytype=="Fee") { echo "selected"; } ?>>Membership Fee</option>
 -->
     </select>

    </div></td>
  </tr>
  <tr>
    <tH width="210" valign="top">PAYMENT METHOD:</tH>
    <td valign="top" style="padding:2px">
    
    
    <div class="col-xs-12">
     <div class="col-xs-6">
      <select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
          <option value="Cash" <?php if($cPayMeth=="Cash") { echo "selected"; } ?>>Cash</option>
          <option value="Cheque" <?php if($cPayMeth=="Cheque") { echo "selected"; } ?>>Cheque</option>
        </select>
     </div>
     
     <div class="col-xs-4">
       <button type="button" class="btn btn-primary btn-sm" tabindex="6" style="width:100%" name="btnDet" id="btnDet">Details</button>
   </div>
    </div>
    
    
    </td>
    <tH style="padding:2px">OR NO.:</tH>
    <td style="padding:2px">
      <div class="col-xs-8">
      <input type="text" class="form-control input-sm" id="txtORNo" name="txtORNo" width="20px" required value="<?php echo $cORNo;?>" readonly>
    </div></td>
  </tr>
  <tr>
    <tH width="210" rowspan="2" valign="top">MEMO:</tH>
    <td rowspan="2" valign="top" style="padding:2px"><div class="col-xs-12">
      <div class="col-xs-10">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cRemarks;?></textarea>
      </div>
    </div></td>
    <th valign="top" style="padding:2px">DATE:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8"> <!--<a href="javascript:NewCal('date_delivery','mmddyyyy')">-->
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($dDate),'m/d/Y'); ?>" />
    <!--</a>--></div></td>
    </tr>
  <tr>
    <th valign="top" style="padding:2px">AMOUNT RECEIVED:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8">
      <input type="text" id="txtnGross" name="txtnGross" class="form-control" value="<?php echo $nAmount;?>">
    </div></td>
  </tr>
      </table>
<br>

<div id='divSales' <?php if($cPaytype<>"Sales"){ echo "style='display:none'" ; } ?>>
<button type="button" class="btn btn-xs btn-info" onClick="getInvs();">

	<!--<button type="button" class="btn btn-xs btn-primary" onClick="popup('add_asset.asp?types=asset');" name="openBtn" id="openBtn">-->
  	<table border="0">
    <tr>
      <td valign="top"><img src="../images/Find.png" border="0" height="20" width="20" />&nbsp;</td>
      <td>Find Invoice</td>
    </tr>
  	</table>
    </button>

    <br><br>
	  <div id="tableContainer" class="alt2" dir="ltr" style="
                        margin: 0px;
                        padding: 3px;
                        border: 1px solid #919b9c;
                        width: 100%;
                        height: 200px;
                        text-align: left;
                        overflow: auto">
<table width="100%" border="0" cellpadding="3" id="MyTable">
<thead>
  <tr>
    <th scope="col" width="15%">Invoice No</th>
    <!--<th scope="col">Status</th>-->
    <th scope="col">Date</th>
    <th scope="col" >Amount</th>
    <th scope="col" width="15%">Discount</th>
    <th scope="col" width="15%">Total Due</th>
    <th scope="col" width="15%">Amount Applied</th>
    <th scope="col">&nbsp;</th>
  </tr>
</thead>
<tbody>

	<?php
    	if($cPaytype=="Sales"){
			
			$sqlbody = mysqli_query($con,"select a.*,b.dcutdate, b.ngross from receipt_sales_t a left join sales b on a.csalesno=b.csalesno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");

						if (mysqli_num_rows($sqlbody)!=0) {
							$cntr = 0;
							while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								$cntr = $cntr + 1;
	?>
               <tr>
                <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtcSalesNo<?php echo $cntr;?>' id='txtcSalesNo<?php echo $cntr;?>' value='<?php echo $rowbody['csalesno'];?>' readonly /></div></td>
                <td align='center'><?php echo $rowbody['dcutdate'];?></td>
                <td align='right'><div class='col-xs-12'><div class='col-xs-6'><input type='hidden' name='txtAmt<?php echo $cntr;?>' id='txtAmt<?php echo $cntr;?>' value='<?php echo $rowbody['ngross'];?>' /><?php echo $rowbody['ngross'];?></div></div></td>
                <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtDiscount<?php echo $cntr;?>' id='txtDiscount<?php echo $cntr;?>' value="<?php echo $rowbody['ndiscount'];?>" placeholder='value in %' onKeyup='computeAmt(this.name,this.value);' /></div></td>
                <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtDue<?php echo $cntr;?>' id='txtDue<?php echo $cntr;?>' value="<?php echo $rowbody['ndue'];?>" readonly /></div></td>
                <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtApplied<?php echo $cntr;?>' id='txtApplied<?php echo $cntr;?>' value="<?php echo $rowbody['namount'];?>" onKeyup='computeGross();' /></div></td>
                <td><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow(this);' /></td>
              </tr>
   <script type="text/javascript">
   						   $("#txtDiscount<?php echo $cntr;?>").on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 
							 
							 $("#txtDue<?php echo $cntr;?>").on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });

							$("#txtApplied<?php echo $cntr;?>").on("keypress keyup blur",function (event) { 
  
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 $("#txtApplied<?php echo $cntr;?>").maskMoney({precision:4});
							 $("#txtDiscount<?php echo $cntr;?>").maskMoney({precision:0,thousands:'',allowEmpty:true});

   </script>
    
    <?php
							}
						}
		}
	?>
</tbody>
</table>
<input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
</div>

</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='OR.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='OR_new2.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmOR');" id="btnUndo" name="btnUndo">
Undo Edit<br>(F3)
    </button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $corno;?>');" id="btnPrint" name="btnPrint">
Print<br>(F4)
    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(F8)    </button>
    
    <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
Save<br>(F2)    </button>

</td>
    <td align="right">&nbsp;</td>
  </tr>
</table>

    </fieldset>



<!-- Bootstrap modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">Invoice List</h3>
            </div>
            
            <div class="modal-body">
            
            	
                  <table name='MyORTbl' id='MyORTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Invoice No</th>
                      <th>Sales Date</th>
                      <th>Gross</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
                
            
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<!--CASH DETAILS DENOMINATIONS -->
<div class="modal fade" id="CashModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CASH DENOMINATION</h3>
            </div>
            <div class="modal-body">
            
                  <table width="100%" border="0" class="table table-scroll table-condensed">
                  <thead>
                      <tr>
                        <td align="center"><b>Denomination</b></td>
                        <td align="center"><b>Pieces</b></td>
                        <td align="center"><b>Amount</b></td>
                      </tr>
                  </thead>
                  	<?php
											$cntr = 0;
											$Pcs1000 = 0;
											$Pcs500 = 0;
											$Pcs200 = 0;
											$Pcs100 = 0;
											$Pcs50 = 0;
											$Pcs20 = 0;
											$Pcs10 = 0;
											$Pcs5 = 0;
											$Pcs1 = 0;
											$Pcs025 = 0;
											$Pcs010 = 0;
											$Pcs005 = 0;
											$Amt1000 = 0;
											$Amt500 = 0;
											$Amt200 = 0;
											$Amt100 = 0;
											$Amt50 = 0;
											$Amt20 = 0;
											$Amt10 = 0;
											$Amt5 = 0;
											$Amt1 = 0;
											$Amt025 = 0;
											$Amt010 = 0;
											$Amt005 = 0;


						if($cPayMeth=="Cash"){
							
							$sqlbody = mysqli_query($con,"select a.* from receipt_cash_t a where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
				
										if (mysqli_num_rows($sqlbody)!=0) {
											while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
												if($rowbody['ndenomination']==1000){
													$Pcs1000 = $rowbody['npieces'];
													$Amt1000 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==500){
													$Pcs500 = $rowbody['npieces'];
													$Amt500 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==200){
													$Pcs200 = $rowbody['npieces'];
													$Amt200 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==100){
													$Pcs100 = $rowbody['npieces'];
													$Amt100 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==50){
													$Pcs50 = $rowbody['npieces'];
													$Amt50 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==20){
													$Pcs20 = $rowbody['npieces'];
													$Amt20 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==10){
													$Pcs10 = $rowbody['npieces'];
													$Amt10 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==5){
													$Pcs5 = $rowbody['npieces'];
													$Amt5 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==1){
													$Pcs1 = $rowbody['npieces'];
													$Amt1 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.25){
													$Pcs025 = $rowbody['npieces'];
													$Amt025 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.10){
													$Pcs010 = $rowbody['npieces'];
													$Amt010 = $rowbody['namount'];
												}
												elseif($rowbody['ndenomination']==0.05){
													$Pcs005 = $rowbody['npieces'];
													$Amt005 = $rowbody['namount'];
												}
											}
										}
						}
					?>

                  <tbody>
                      <tr>
                        <td align="center">1000</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom1000' id='txtDenom1000' value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>" /></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1000' id='txtAmt1000' readonly value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">500</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom500' id='txtDenom500' value="<?php if($Pcs500<>0){ echo $Pcs500; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt500' id='txtAmt500' readonly value="<?php if($Amt500<>0){ echo $Amt500; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">200</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom200' id='txtDenom200' value="<?php if($Pcs200<>0){ echo $Pcs200; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt200' id='txtAmt200' readonly value="<?php if($Amt200<>0){ echo $Amt200; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">100</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom100' id='txtDenom100' value="<?php if($Pcs100<>0){ echo $Pcs100; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt100' id='txtAmt100' readonly value="<?php if($Amt100<>0){ echo $Amt100; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">50</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom50' id='txtDenom50' value="<?php if($Pcs50<>0){ echo $Pcs50; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt50' id='txtAmt50' readonly value="<?php if($Amt50<>0){ echo $Amt50; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">20</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom20' id='txtDenom20' value="<?php if($Pcs20<>0){ echo $Pcs20; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt20' id='txtAmt20' readonly value="<?php if($Amt20<>0){ echo $Amt20; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">10</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom10' id='txtDenom10' value="<?php if($Pcs10<>0){ echo $Pcs10; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt10' id='txtAmt10' readonly value="<?php if($Amt10<>0){ echo $Amt10; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">5</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom5' id='txtDenom5' value="<?php if($Pcs5<>0){ echo $Pcs5; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt5' id='txtAmt5' readonly value="<?php if($Amt5<>0){ echo $Amt5; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">1</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom1' id='txtDenom1' value="<?php if($Pcs1<>0){ echo $Pcs1; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1' id='txtAmt1' readonly value="<?php if($Amt1<>0){ echo $Amt1; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.25</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom025' id='txtDenom025' value="<?php if($Pcs025<>0){ echo $Pcs025; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt025' id='txtAmt025' readonly value="<?php if($Amt025<>0){ echo $Amt025; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.10</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom010' id='txtDenom010' value="<?php if($Pcs010<>0){ echo $Pcs010; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt010' id='txtAmt010' readonly value="<?php if($Amt010<>0){ echo $Amt010; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.05</td>
                        <td><div class='col-xs-12'><input type='text' class='allownumericwithoutdecimal form-control input-sm' name='txtDenom005' id='txtDenom005' value="<?php if($Pcs005<>0){ echo $Pcs005; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt005' id='txtAmt005' readonly value="<?php if($Amt005<>0){ echo $Amt005; } ?>"/></div></td>
                      </tr>
                    </tbody>
                    </table>
            
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<div class="modal fade" id="ChequeModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader">CHEQUE DETAILS</h3>
            </div>
            <div class="modal-body">
            	<?php
											$cBank = "";
											$cCheckNo = "";
											$dDateCheck = "";
											$nCheckAmt = "";
											
					if($cPayMeth=="Cheque"){
						
						$sqlbody = mysqli_query($con,"select a.* from receipt_check_t a where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
			
									if (mysqli_num_rows($sqlbody)!=0) {
										$cntr = 0;
										while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
											$cBank = $rowbody['cbank'];
											$cCheckNo = $rowbody['ccheckno'];
											$dDateCheck = $rowbody['ddate'];
											$nCheckAmt = $rowbody['nchkamt'];
										}
									}
					}
				?>

                  <table width="100%" border="0" class="table table-condensed">
                      <tr>
                        <td><b>Bank Name</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtBankName' id='txtBankName' placeholder="Input Bank Name" value="<?php echo $cBank; ?>"/></div></td>
                      </tr>
                      <tr>
                        <td><b>Cheque Date</b></td>
                        <td>
                        <div class='col-sm-12'>
                            <input type='text' class="form-control input-sm" id='datetimepicker4' placeholder="Pick a Date" name="txtChekDate" id="txtChekDate"  value="<?php echo date_format(date_create($dDateCheck),'m/d/Y'); ?>"/>

                        </div>
                        </td>
                      </tr>
                      <tr>
                        <td><b>Cheque Number</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' placeholder="Input Cheque Number"  value="<?php echo $cCheckNo; ?>"/></div></td>
                      </tr>
                       <tr>
                        <td><b>Cheque Amount</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckAmt' id='txtCheckAmt' placeholder="Input Cheque Amount"  value="<?php echo $nCheckAmt; ?>" /></div></td>
                      </tr>
                    </table>
            
            </div>
            <div class="modal-footer">
                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


</form>

<?php
}
else{
?>

<form action="OR_edit2.php" name="frmpos2" id="frmpos2" method="post">
  <fieldset>
   	<legend>Receive Payment</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">OR No.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>OR No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>


<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 112) { //F1
		if(document.getElementById("btnNew").className=="btn btn-default btn-sm"){
			window.location.href='OR_new2.php';
		}
	  }
	  else if(e.keyCode == 113){//F2
		if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
			return chkform();
		}
	  }
	  else if(e.keyCode == 119){//F8
		if(document.getElementById("btnEdit").className=="btn btn-warning btn-sm"){
			enabled();
		}
	  }
	  else if(e.keyCode == 115){//F4
		if(document.getElementById("btnPrint").className=="btn btn-info btn-sm"){
			printchk('<?php echo $corno;?>');
		}
	  }
	  else if(e.keyCode == 114){//F3
		if(document.getElementById("btnUndo").className=="btn btn-danger btn-sm"){
			e.preventDefault();
			chkSIEnter(13,'frmOR');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if(document.getElementById("btnMain").className=="btn btn-primary btn-sm"){
			e.preventDefault();
			window.location.href='OR.php';
		}
	  }
	});


$(function() {              
           // Bootstrap DateTimePicker v4
           $('#datetimepicker4').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
		   
		   $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
           });

		   $('#txtCheckAmt').maskMoney({prefix:'\u20B1 '});
});
		
$('.allownumericwithoutdecimal').keyup(function (e) {
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input').focus();
 
        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input').focus();
 
        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
 
        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        }
		else{
			var str = $(this).attr('name');
			var res = str.substring(0, 8);
			var valz = str.substring(8);
			
			if(valz=="025"){
				var val2=0.25;
			}
			else if(valz=="010"){
				var val2=0.10;
			}
			else if(valz=="005"){
				var val2=0.05;
			}
			else{
				var val2 = valz;
			}
			
			var value = $(this).val();
			if(res=="txtDenom"){
				
				var x = parseFloat(val2) * parseFloat(value);	
				//alert("#txtAmt"+valz+" = "+x);	
				if(value!=""){		
					$("#txtAmt"+valz).val(x.toFixed(2));
				}
				else{
					$("#txtAmt"+valz).val("");
				}
				
			}

		}
});
	
$('#txtcacct').typeahead({

    source: function (query, process) {
        return $.getJSON(
            'th_accounts.php',
            { query: query },
            function (data) {
				newData = [];
				map = {};
				
				$.each(data, function(i, object) {
					map[object.name] = object;
					newData.push(object.name);
				});
                
				process(newData);
            });
    },
	updater: function (item) {	
		  	
			$('#txtcacctid').val(map[item].id);
			$('#txtacctbal').val(map[item].balance);
			return item;
	}

});
	
$('#txtcust').typeahead({

	items: 10,
    source: function(request, response) {
        $.ajax({
            url: "th_customer.php",
            dataType: "json",
            data: {
                query: $("#txtcust").val()
            },
            success: function (data) {
                response(data);
            }
        });
    },
    autoSelect: true,
    displayText: function (item) {
        return '<span class="dropdown-item-extra">' + item.typ + " " + item.id + '</span><br>' + item.value;
    },
	highlighter: Object,
	afterSelect: function(item) { 
	 	$('#txtcust').val(item.value).change(); 
		$("#txtcustid").val(item.id);
	}
});

$("#txtcust").on('blur', function() {
	if($('#txtcustid').val() != "" && $('#txtcustid').val() != ""){
		$('#txtcust').attr('readonly', true);
	}
}); 


$("#selpayment").on('change', function() {
	$('#txtnGross').val('0.00');
	
	 if ($(this).val() == "Cheque"){
		$('#txtnGross').attr('readonly', true);
	 }
	 else{
		$('#txtnGross').attr('readonly', false);
	 }
});


$("#selpaytype").on('change', function() {
	
	 if ($(this).val() == "Sales"){
		$('#txtnGross').val('0.00');
		$('#txtnGross').attr('readonly', true);
	 }
	 else{
		$('#txtnGross').attr('readonly', false);
	 }
});

 
$("#txtCheckAmt").on('keyup', function() {
	var x = $('#txtCheckAmt').maskMoney('unmasked')[0];
	
		$('#txtnGross').val(x);
});



$("#selpaytype").change(function() {
var rc = $('#MyTable tr').length;


  if ($(this).val() != "Sales"){
	 if(rc > 1) {
		 var j = confirm("Changing Payment Type will clear the Sales Details.\n Are you sure you want to change type?");
		 if(j==true){
			 $('#MyTable tbody').empty();
		 }
	 }

	  $("#divSales").hide();

  }
  else{
  	  $("#divSales").show();
  }
 // alert($(this).val());
});


$("#btnDet").on('click', function() {
	if($('#selpayment').val() == "Cash"){
		$('#CashModal').modal('show');
	}
	if($('#selpayment').val() == "Cheque"){
		$('#ChequeModal').modal('show');
	}
});

$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
     $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
});

 $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
           $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which >= 48 && event.which <= 57) || event.which == 8 || event.which == 46) {
				return true;
			}
			else {
				event.preventDefault();
			}
 });
 
 $("#allbox").click(function () {
        if ($("#allbox").is(':checked')) {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='chkSales[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
    });


function computeAmt(str,valz){

	var numberPattern = /\d+/g;
	var r = str.match(numberPattern);
		
	var rwcnt = parseInt(r);
	
	var amtz = $("#txtAmt"+rwcnt).val();

									
	if(valz!=""){							
		var thisvalz = parseFloat(valz);
											
		var Totdicnt = (thisvalz/100) * amtz;
		var TotDue = amtz - Totdicnt;
											
		$("#txtDue"+rwcnt).val(TotDue.toFixed(4));
	}
	else{
		$("#txtDue"+rwcnt).val(amtz);
	}

}

function deleteRow(r) {
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTable').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempsalesno = document.getElementById('txtcSalesNo' + z);
			var tempamt = document.getElementById('txtAmt' + z);
			var tempdisc= document.getElementById('txtDiscount' + z);
			var tempdue= document.getElementById('txtDue' + z);
			var tempapplies = document.getElementById('txtApplied' + z);
			
			var x = z-1;
			tempsalesno.id = "txtcSalesNo" + x;
			tempsalesno.name = "txtcSalesNo" + x;
			tempamt.id = "txtAmt" + x;
			tempamt.name = "txtAmt" + x;
			tempdisc.id = "txtDiscount" + x;
			tempdisc.name = "txtDiscount" + x;
			tempdue.id = "txtDue" + x;
			tempdue.name = "txtDue" + x;
			tempapplies.id = "txtApplied" + x;
			tempapplies.name = "txtApplied" + x;
			
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}

computeGross();

}

function computeGross(){
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var x = 0;
	var tot = 0;
	for (z=1; z<=lastRow-1; z++){
		x = $('#txtApplied' + z).maskMoney('unmasked')[0];
		if(x!=0 && x!=""){
		var tot = parseFloat(x) + parseFloat(tot);	
		}
	}
	
	document.getElementById('txtnGross').value = tot.toFixed(2);

}

function getInvs(){
	
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			//clear table body if may laman
			

			$('#MyORTbl tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";
			var rc = $('#MyTable tr').length;
			for(y=1;y<=rc-1;y++){ 
			  if(y>1){
				  salesnos = salesnos + ",";
			  }
				salesnos = salesnos + $('#txtcSalesNo'+y).val();
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#invheader').html("Invoice List: " + $('#txtcust').val())
			
			$.ajax({
                    url: 'th_orlist.php',
					data: 'x='+x+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
                        console.log(data);
                       $.each(data,function(index,item){
                        $("<tr>").append(
						$("<td>").html("<input type='checkbox' value='"+item.csalesno+"' name='chkSales[]'>"),
                        $("<td>").text(item.csalesno),
                        $("<td>").text(item.dcutdate),
						$("<td>").text(item.ngross)
                        ).appendTo("#MyORTbl tbody");

                       });
                    },
                    error: function (err) {
                        alert(err);
                    }
                });
			
			$('#myModal').modal('show');
			
		}


}

function save(){

	var i = 0;
	var rowCount = $('#MyTable tr').length;
   $("input[name='chkSales[]']:checked").each( function () {
	   i += 1;
      // alert( $(this).val() );
	  			
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getsalesdetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{				
					
					   console.log(data);
                       $.each(data,function(index,item){
						   $("<tr>").append(
							$("<td>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtcSalesNo"+rowCount+"' id='txtcSalesNo"+rowCount+"' value='"+item.csalesno+"' readonly /></div>"),
							//$("<td>").text(""),
							$("<td align='center'>").text(item.dcutdate),
							$("<td align='right'>").html("<div class='col-xs-12'><div class='col-xs-6'><input type='hidden' name='txtAmt"+rowCount+"' id='txtAmt"+rowCount+"' value='"+item.ngross+"' />"+item.ngross+"</div></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='InvDet form-control input-sm' name='txtDiscount"+rowCount+"' id='txtDiscount"+rowCount+"' placeholder='value in %' onKeyup='computeAmt(this.name,this.value);' /></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtDue"+rowCount+"' id='txtDue"+rowCount+"' readonly value='"+item.ngross+"' /></div>"),
							$("<td style='padding: 1px;'>").html("<div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtApplied"+rowCount+"' id='txtApplied"+rowCount+"' onKeyup='computeGross();' /></div>"),
							$("<td align='center'>").html("<input class='btn btn-danger btn-xs' type='button' id='row_"+rowCount+"_delete' value='delete' onClick='deleteRow(this);' />")
						   ).appendTo("#MyTable tbody");
						   
						   
						   $("#txtDiscount"+rowCount).on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 
							 
							 $("#txtDue"+rowCount).on("keypress keyup blur",function (event) {    
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });

							$("#txtApplied"+rowCount).on("keypress keyup blur",function (event) { 
  
									  if (event.which == 39) { // right arrow
										 $(this).closest('td').next().find('input').focus();
								 
										} else if (event.which == 37) { // left arrow
										  $(this).closest('td').prev().find('input').focus();
								 
										} else if (event.which == 40) { // down arrow
										  $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
								 
										} else if (event.which == 38) { // up arrow
										  $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
										}
							 });
							 
							 $("#txtApplied"+rowCount).maskMoney({precision:4});
							 $("#txtDiscount"+rowCount).maskMoney({precision:0,thousands:'',allowEmpty:true});
					   
						   
						});
					rowCount = rowCount + 1;

					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

	   
	   
	   
   });
   
   if(i==0){
	   alert("No Invoice is selected!")
   }
   
   $('#myModal').modal('hide');
   
}


$('#frmOR').submit(function() {
	var subz = "YES";

  	if($('#txtcustid').val() == "" || $('#txtcustid').val() == ""){
		alert("You Need a Valid Customer.");
		subz = "NO";
	}


  	if($('#txtnGross').val() == "" || $('#txtnGross').val() == 0){
		alert("Zero or Blank AMOUNT RECEIVED is not allowed!");
		subz = "NO";
	}

  	if($('#txtORNo').val() == ""){
		alert("Please input your OR NUMBER!");
		subz = "NO";
	}
	    
	if($('#selpayment').val() == "Cheque"){
		if($('#txtBankName').val() == "" || $('#txtChekDate').val() == "" || $('#txtCheckNo').val() == "" || $('#txtCheckAmt').val() == ""){
			alert("Please complete your cheque details!");
			subz = "NO";
		}
	}
	
	if($('#selpaytype').val() == "Sales"){
		
			var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow = tbl.length-1;
			
			if(lastRow==0){
				alert("Sales Details Required!");
				subz = "NO";
			}
			else{
					var tot = 0;
					
					for (z=1; z<=lastRow; z++){
						x = document.getElementById('txtApplied' + z).value;
						if(x!=0 && x!=""){
							var tot = tot + 1;	
						}
					}
					
					if(tot == 0){
						alert("Your details has no amount");
						subz = "NO";
					}
					else if(tot < lastRow){
						alert("Note: Only details with applied amount will be saved.");
					}
					
					$("#hdnrowcnt").val(lastRow);

			}

	}
	
	if(subz=="NO"){
		return false;
	}
	else{
		if($('#selpayment').val() == "Cheque"){
			$('#txtCheckAmt').val($('#txtCheckAmt').maskMoney('unmasked')[0]);
		}
		
		$("#frmOR").submit();
	}
	
});



function disabled(){

	$("#frmOR :input").attr("disabled", true);
	
	
	$("#txtctranno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
$("#frmOR :input").attr("disabled", false);

	if($("#selpayment").val()=="Cheque" || $("#selpaytype").val()=="Sales"){
		$("#txtnGross").attr("readonly", true);
	}
	
	$("#txtctranno").attr("readonly", true);
	$("#btnMain").attr("disabled", true);
	$("#btnNew").attr("disabled", true);
	$("#btnPrint").attr("disabled", true);
	$("#btnEdit").attr("disabled", true);

}

function chkSIEnter(keyCode,frm){

	if(keyCode==13){			
		document.getElementById(frm).action = "OR_edit2.php";
		document.getElementById(frm).submit();
	}
}


</script>


</body>
</html>
