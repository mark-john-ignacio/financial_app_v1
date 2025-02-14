<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "POS_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

if(isset($_REQUEST['txtctranno'])){
		$txtctranno = $_REQUEST['txtctranno'];
}
else{
		$txtctranno = $_REQUEST['txtcsalesno'];
	}
	
$company = $_SESSION['companyid'];


$sqlhead = mysqli_query($con,"select a.*,b.cname,b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit from sales a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.ctranno = '$txtctranno' and a.compcode='$company'");	

/*
function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	//$obj = json_decode($json, true);

	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
  
	return $json;
}
*/
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
   	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    
<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../../include/autoNumeric.js"></script>
<!--
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>
-->

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcsalesno').focus(); ">
<?php


if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];
		$Remarks = $row['cremarks'];
		$Date = $row['dcutdate'];
		$Gross = $row['ngross'];
		$cpricever = $row['cpricever'];
		$nlimit = $row['nlimit'];
		$creinv = $row['creinvoice'];
    $cslstypz = $row['csalestype'];
		$cpaytypz = $row['cpaytype'];
		$selsiseries = $row['csiprintno'];
		$nnetvat = $row['nnet'];
		$nvat = $row['nvat'];

		$nbasegross = $row['nbasegross'];
		$ccurrcode = $row['ccurrencycode']; 
		$ccurrdesc = $row['ccurrencydesc']; 
		$ccurrrate = $row['nexchangerate']; 

		if($_SESSION['employeeid']=='CoopAdmin'){
			$lCancelled = 0;
			$lPosted = 0;
		}else{
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
		}
	}
	
	
	if(!file_exists("../../imgcust/".$CustCode .".jpg")){
		$imgsrc = "../../images/blueX.png";
	}
	else{
		$imgsrc = "../../imgcust/".$CustCode .".jpg";
	}

?>

<form action="SI_edit.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
    	<legend>
        <div class="col-xs-6 nopadding">Sales Invoice Details
      </div><div id="salesstat" class="text-right nopadding">
        <?php
        if($lCancelled==1){
            echo "<font color='#FF0000'><b>CANCELLED</b></font>";
        }
        
        if($lPosted==1){
            echo "<font color='#FF0000'><b>POSTED</b></font>";
        }
        ?>
        </div>
        </div> 
        </legend>	
        <table width="100%" border="0">
			<tr>
				<tH>&nbsp;TRANS NO.:</tH>
				<td style="padding:2px">
					<div class="col-xs-4 nopadding">
					
					<input type="text" class="form-control input-sm" id="txtcsalesno" name="txtcsalesno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos');"></div>
					
					<input type="hidden" name="hdnposted" id="hdnposted" value="<?php echo $lPosted;?>">
					<input type="hidden" name="hdncancel" id="hdncancel" value="<?php echo $lCancelled;?>">
					<input type="hidden" name="hdncsalesno" id="hdncsalesno" value="<?php echo $txtctranno;?>">
					&nbsp;&nbsp;
					<div id="statmsgz" style="display:inline"></div>
				</td>
				<tH width="150">Invoice Date:</tH>
				<td style="padding:2px;">
					<div class="col-xs-11 nopadding">
							<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($Date),'m/d/Y'); ?>" />
					</div>
				</td>
			</tr>
			
			<tr>
				<tH>&nbsp;SI Series No.</tH>
				<td style="padding:2px;">
					<div class="col-xs-4 nopadding">
						<input type='text' class="form-control input-sm" id="csiprintno" name="csiprintno" value="<?php echo $selsiseries;?>" autocomplete="off"/>
					</div>
				</td>
				<td><b>Sales Type:</b></td>
				<td style="padding:2px">
									
					<div class="col-xs-11 nopadding">
						<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
							<option value="Goods" <?php if($cslstypz=="Goods"){ echo "selected"; } ?>>Goods</option>
							<option value="Services" <?php if($cslstypz=="Services"){ echo "selected"; } ?>>Services</option> 
						</select>
					</div>

					<div class="col-xs-1 nopadding">
						&nbsp;
					</div>
				</td>
			</tr>


  <tr>
    <tH width="100">&nbsp;Customer:</tH>
    <td style="padding:2px">
    	<div class="col-xs-12 nopadding">
        <div class="col-xs-3 nopadding">
        	<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1" value="<?php echo $CustCode; ?>" readonly>
            <input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
            <input type="hidden" id="hdnpricever" name="hdnpricever" value="<?php echo $cpricever;?>">
        </div>

    		<div class="col-xs-8 nopadwleft">
        	<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" value="<?php echo $CustName; ?>" readonly>
        </div> 
      </div>
    </td>
		<tH width="100"><b>Payment Type:</b></tH>
		<td style="padding:2px">
			<div class="col-xs-11 nopadding">
				<select id="selpaytyp" name="selpaytyp" class="form-control input-sm selectpicker"  tabindex="1">
					<option value="Credit" <?php if($cpaytypz=="Credit"){ echo "selected"; } ?>>Credit</option> 
					<option value="Cash" <?php if($cpaytypz=="Cash"){ echo "selected"; } ?>>Cash</option>
				</select>
			</div>
		</td>
  </tr>

	<tr>
            <td>&nbsp;<b>Currency:</b></td>
            <td style="padding:2px">
              
							<div class="col-xs-4 nopadding">
								<select class="form-control input-sm" name="selbasecurr" id="selbasecurr"> 						
									<?php
											$nvaluecurrbase = "";	
											$nvaluecurrbasedesc = "";	
											$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='DEF_CURRENCY'"); 
											
												if (mysqli_num_rows($result)!=0) {
													$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
													
													$nvaluecurrbase = $all_course_data['cvalue']; 
														
												}
												else{
													$nvaluecurrbase = "";
												}
						
												/*
													$objcurrs = listcurrencies();
													$objrows = json_decode($objcurrs, true);
														
											foreach($objrows as $rows){
												if ($nvaluecurrbase==$rows['currencyCode']) {
													$nvaluecurrbasedesc = $rows['currencyName'];
												}

												if($rows['countryCode']!=="Crypto" && $rows['currencyName']!==null){

													*/

													$sqlhead=mysqli_query($con,"Select symbol as id, CONCAT(symbol,\" - \",country,\" \",unit) as currencyName, rate from currency_rate");
													if (mysqli_num_rows($sqlhead)!=0) {
														while($rows = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
									?>
												<option value="<?=$rows['id']?>" <?php if ($ccurrcode==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=strtoupper($rows['currencyName'])?></option>
									<?php

												}
											}
									?>
								</select>
								<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
								<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $ccurrdesc; ?>"> 
							</div>
							<div class="col-xs-2 nopadwleft">
								<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="<?php echo $ccurrrate; ?>">	 
							</div>

							<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
										
							</div>
            </td>
            
						<td><b><div class="chklimit">Credit Limit:</div></b></td>
						<td style="padding:2px;" align="right">
							<div class="chklimit col-xs-11 nopadding" id="ncustlimit"><b><font size='+1'><?php echo number_format($nlimit,4);?></font></b></div>
							<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">
						</td>

          </tr>


		<tr>

  <tr>
    <tH width="100">&nbsp;Remarks:</tH>
    <td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2" value="<?php echo $Remarks; ?>"></div></td>
						           
            <th><div class="chklimit">Balance:</div></th>
            <td style="padding:2px;"  align="right">                  
                      <div class="chklimit col-xs-11 nopadding" id="ncustbalance"></div>
                    <input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
            </td>

          </tr>


		<td colspan="2"><div class="col-xs-12 nopadwtop2x">
			<div class="chkitmsadd col-xs-3 nopadwdown">
			<input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4">
			</div>
			<div class="chkitmsadd col-xs-8 nopadwleft">
			<input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="Search Product Name..." size="80" tabindex="5">
			</div>
			</div>
			<input type="hidden" name="hdnqty" id="hdnqty">
			<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
			<input type="hidden" name="hdnunit" id="hdnunit"> 
			<input type="hidden" name="hdnctype" id="hdnctype">
			<input type="hidden" name="hdncvat" id="hdncvat"> 
			
			</td>
			<td>&nbsp;</td>
			<td style="padding:2px;" align="right"><div class="chklimit col-xs-11 nopadwright" id="ncustbalance2"></div></td>
		</tr>
</table>
         <div class="alt2" dir="ltr" style="
					margin: 0px;
					padding: 3px;
					border: 1px solid #919b9c;
					width: 100%;
					height: 250px;
					text-align: left;
					overflow: auto">
	
            <table id="MyTable" class="MyTable table table-condensed" width="100%">

					<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
						<th style="border-bottom:1px solid #999" class="chkVATClass">VAT</th>            
						<th style="border-bottom:1px solid #999">UOM</th>
						<th style="border-bottom:1px solid #999">Qty</th>
						<th style="border-bottom:1px solid #999">Price</th>
						<th style="border-bottom:1px solid #999">Discount</th>
						<th style="border-bottom:1px solid #999">Amount</th>
						<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
						<th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
                    
					<tbody class="tbody">
                    </tbody>
                    
			</table>

</div>
<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
    <input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
 
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SI.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='SI_new.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
DR<br>(Insert)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmpos');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>

<?php
	$sql = mysqli_query($con,"select * from users_access where userid = '".$_SESSION['employeeid']."' and pageid = 'POS_print'");

	if(mysqli_num_rows($sql) == 1){
	
?>
    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $txtctranno;?>');" id="btnPrint" name="btnPrint">
Print<br>(CTRL+P)
    </button>

<?php		
	}

?>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)    </button>
    
    <button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">
Save<br>(CTRL+S)    </button>
    
    
    
    </td>    <td align="right" valign="top">
    

     <table width="90%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="110px" align="right"><b>Net of VAT </b>&nbsp;&nbsp;</td>
        <td width="150px"> <input type="text" id="txtnNetVAT" name="txtnNetVAT" readonly value="<?php echo $nnetvat; ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>
      </tr>
      <tr>
        <td width="110px" align="right"><b>VAT </b>&nbsp;&nbsp;</td>
        <td width="150px"> <input type="text" id="txtnVAT" name="txtnVAT" readonly value="<?php echo $nvat; ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>
      </tr>
	  <tr>
        <td width="110px" align="right"><b>Gross Amt </b>&nbsp;&nbsp;</td>
        <td width="150px"> <input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="<?php echo  number_format($nbasegross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>
      </tr>
	  <tr>
        <td width="110px" align="right"><b>Gross Amt in <?php echo $nvaluecurrbase; ?></b>&nbsp;&nbsp;</td>
        <td width="150px"> <input type="text" id="txtnGross" name="txtnGross" readonly value="<?php echo  number_format($Gross,4); ?>" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10"></td>
      </tr>
    </table>

    </table> 
   
      </td>
    </tr>
</table>

    </fieldset>
    
   
  <div class="modal fade" id="MyDetModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  aria-label="Close"  onclick="chkCloseInfo();"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader"> Additional Details Info</h3>           
			</div>
    
            <div class="modal-body">
                <input type="hidden" name="hdnrowcnt2" id="hdnrowcnt2">
                <table id="MyTable2" class="MyTable table table-condensed" width="100%">
    				<tr>
						<th style="border-bottom:1px solid #999">Code</th>
						<th style="border-bottom:1px solid #999">Description</th>
                        <th style="border-bottom:1px solid #999">Field Name</th>
						<th style="border-bottom:1px solid #999">Value</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
					<tbody class="tbody">
                    </tbody>
                </table>
    
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


<!-- FULL PO LIST REFERENCES-->

<div class="modal fade" id="mySIRef" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="InvListHdr">DR List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       				<div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-3 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>DR No</th>
                              <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>

                    <div class="col-xs-9 nopadwleft pre-scrollable" style="height:37vh">
                          <table name='MyInvDetList' id='MyInvDetList' class="table table-small small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
							  <th>SO No.</th> 
							  <th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
							  <th>Price</th>
							  <th>Amount</th>
							  <th>Cur</th>
                            </tr>
                            </thead>
                            <tbody>
                            	
                            </tbody>
                          </table>
                    </div>
               </div>

        </div>
         	            
			</div>
			
            <div class="modal-footer">
                <button type="button" id="btnInsDet" onClick="InsertSI()" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->

</form>

<?php
}
else{
?>
<form action="SI_edit.php" name="frmpos2" id="frmpos2">
  <fieldset>
   	<legend>Sales Invoice</legend>	
<table width="100%" border="0">
  <tr>
    <tH width="100">TRANS NO.:</tH>
    <td colspan="3" style="padding:2px" align="left"><div class="col-xs-2"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $txtctranno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmpos2');"></div></td>
    </tr>
  <tr>
    <tH colspan="4" align="center" style="padding:10px"><font color="#FF0000"><b>SI No. DID NOT EXIST!</b></font></tH>
    </tr>
</table>
</fieldset>
</form>
<?php
}
?>


<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- PRINT OUT MODAL-->
<div class="modal fade" id="PrintModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-contnorad">   
            <div class="modal-bodylong">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        
        
               <iframe id="myprintframe" name="myprintframe" scrolling="no" style="width:100%; height:8.5in; display:block; margin:0px; padding:0px; border:0px"></iframe>
    
            	
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


</body>
</html>

<script type="text/javascript">
var xChkBal = "";
var xChkLimit = "";
var xChkLimitWarn = "";
var xChkVatableStatus = "";

var slctqryvatc = "";
var slctvatsoption = "";


var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;

	$(document).keydown(function(e) {	 
	  if(e.keyCode == 112) { //F1
		if($("#btnNew").is(":disabled")==false){
			e.preventDefault();
			window.location.href='SI_new.php';
		}
	  }
	  else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
		if($("#btnSave").is(":disabled")==false){
			e.preventDefault();
			return chkform();
		}
	  }
	  else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
		if($("#btnEdit").is(":disabled")==false){
			e.preventDefault();
			enabled();
		}
	  }
	  else if(e.keyCode == 80 && e.ctrlKey){//CTRL P
		if($("#btnPrint").is(":disabled")==false){
			e.preventDefault();
			printchk('<?php echo $txtctranno;?>');
		}
	  }
	  else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
		if($("#btnUndo").is(":disabled")==false){
			e.preventDefault();
			chkSIEnter(13,'frmpos');
		}
	  }
	  else if(e.keyCode == 27){//ESC
		if($("#btnMain").is(":disabled")==false){
			e.preventDefault();
			window.location.href='SI.php';
		}
	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false && $("#btnIns").is(":disabled")==false){
			openinv();
		}
	  }

	});


	$(document).ready(function(e) {	
	

		$("#txtnBaseGross").autoNumeric('init',{mDec:4});
		$("#txtnGross").autoNumeric('init',{mDec:4});  
		$("#txtnVAT").autoNumeric('init',{mDec:4});
		$("#txtnNetVAT").autoNumeric('init',{mDec:4});

		   			$.ajax({
					url : "../../include/th_xtrasessions.php",
					type: "Post",
					async:false,
					dataType: "json",
					success: function(data)
					{	
					 console.log(data);
            $.each(data,function(index,item){
						  xChkBal = item.chkinv; //0 = Check ; 1 = Dont Check
						  xChkLimit = item.chkcustlmt; //0 = Disable ; 1 = Enable
						  xChkLimitWarn = item.chklmtwarn; //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
              xChkVatableStatus = item.chkcompvat;

						   
					 });
					}
				});
				
	//	if(xChkBal==1){
	//		$("#tblAvailable").hide();
	//	}
	//	else{
	//		$("#tblAvailable").show();
	//	}

    if(xChkVatableStatus==1){
      $(".chkVATClass").show(); 
    }
    else{
      $(".chkVATClass").hide();
    }

		if(xChkLimit==0){
			$(".chklimit").hide();
		}
		else{
			$(".chklimit").show();
		}


		loaddetails();
		loaddetinfo();
    	ComputeGross();

    if($("#selsityp").val()=="Goods"){
      $(".chkitmsadd").hide();
    }else{
      $(".chkitmsadd").show();

      $('#txtprodnme').attr("disabled", true);
      $('#txtprodid').attr("disabled", true);
    }
	  
	  disabled();

    });


$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				 //minDate: new Date(),
        });
		
		$('#date_delivery').on('dp.change', function(e){ 
			if($("#txtcustid").val()!=""){
				$.ajax({
				type:'post',
				url:'../get_customerid.php',
				data: 'c_id='+ $("#txtcustid").val(),                 
				success: function(value){
					//alert(value);
					if(value.trim()!=""){
						var data = value.split(":");
						
            if(xChkLimit==1){
              
              limit = Number(data[3]).toLocaleString('en', { minimumFractionDigits: 4 }); 

              $('#ncustbalance2').html("");
              $('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
              $('#hdncustlimit').val(data[3]);
              
              checkcustlimit(dInput, data[3]);

            }
					}
				}
				});
			}

		
		});

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});
	
	$('#txtprodnme').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_product.php",
				dataType: "json",
				data: { query: $("#txtprodnme").val(), itmbal: xChkBal, styp: $("#selsityp").val() },
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span >'+item.desc+'</span</div>';
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtprodnme').val(item.desc).change(); 
			$('#txtprodid').val(item.id); 
			$("#hdnunit").val(item.cunit); 
			$("#hdnctype").val(item.citmcls);
			$("#hdnqty").val(item.nqty);
			$("#hdnqtyunit").val(item.cqtyunit);
      		$("#hdncvat").val(item.ctaxcode);
			
	  		addItemName("","","","","","","","","");
			
			
		}
	
	});


	$("#txtprodid").keypress(function(event){
		if(event.keyCode == 13){

		$.ajax({
        url:'../get_productid.php',
        data: 'c_id='+ $(this).val() + "&itmbal="+xChkBal+"&styp="+ $("#selsityp").val(),                 
        success: function(value){
            var data = value.split(",");
            $('#txtprodid').val(data[0]);
            $('#txtprodnme').val(data[1]);
			      $('#hdnunit').val(data[2]);
		      	$("#hdnqty").val(data[3]);
		      	$("#hdnqtyunit").val(data[4]);
		      	$("#hdnctype").val(data[5]);
            $("#hdncvat").val(data[6]);

		if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){
			var isItem = "NO";
			var disID = "";
			
			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();

				if($("#txtprodid").val()==disID){
					
					isItem = "YES";

				}
			});	

		//if value is not blank
		 }else{
			 alert("ITEM BARCODE NOT EXISTING!");
			 $('#txtprodnme').focus();
		 }
		 
		if(isItem=="NO"){		

			myFunctionadd("","","","","","","","","");
			ComputeGross();	
			
	    }
	    else{
			
			addqty();
		}
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		$("#hdnctype").val("");
    	$("#hdncvat").val("");

	    //closing for success: function(value){
	    }
        }); 

	
		 
		//if enter is clicked
		}
		
	});
	

  $("#selsityp").on("change", function(){

      var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
    var lastRow = tbl.length-1;

    if(lastRow > 0){
      var x = confirm("Changing this will erase all details!");
      if (x == true) {
        $("#MyTable").find("tr:gt(0)").remove();
      }
    }
    else{
      $("#MyTable").find("tr:gt(0)").remove();
    }

    if($(this).val()=="Goods"){

      $(".chkitmsadd").hide();

    }else{
      $(".chkitmsadd").show();
    }
  });

  $("#selbasecurr").on("change", function (){
			
		//	convertCurrency($(this).val());

		$("#basecurrval").val(dval);
		$("#statgetrate").html("");
		recomputeCurr();
			
	});
			
		$("#basecurrval").on("keyup", function () {
			recomputeCurr();
		});

});

function checkcustlimit(id,xcred){
  //Check Credit Limit BALNCE here

  var xBalance = 0;
  var xinvs = 0;
  var xors = 0;
  var dte = $("#date_delivery").val();
  
    $.ajax ({
      url: "../th_creditlimit.php",
      data: { id: id },
      async: false,
      dataType: "json",
      success: function( data ) {
                      
        console.log(data);
        $.each(data,function(index,item){
          if(item.invs!=null){
            xinvs = item.invs;
          }
          
          if(item.ors!=null){
            xors = item.ors;
          }
          
        });
      }
    });
  
  //alert("("+parseFloat(xcred) +"-"+ parseFloat(xinvs)+") + "+parseFloat(xors));
    
  xBalance = (parseFloat(xcred) - parseFloat(xinvs)) + parseFloat(xors);
  $("#hdncustbalance").val(xBalance);
  
  if(xBalance > 0){
    xBalance = Number(xBalance).toLocaleString('en', { minimumFractionDigits: 4 });
    $("#ncustbalance").html("<b><font size='+1'>"+xBalance+"</font></b>");
  }
  else{

    if(xChkLimitWarn==0) { //0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
      $("#ncustbalance").html("<b><i><font color='red'>Max Limit Reached</font></i></b>");
    }
    else if(xChkLimitWarn==1) {
      $("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
      $("#ncustbalance2").html("<b><i><font color='red' size='-1'>Delivery is blocked</font></i></b>");
    }
    else if(xChkLimitWarn==2) {
      $("#ncustbalance").html("<b><i><font color='red' size='-1'>Max Limit Reached</font></i></b>");
      $("#ncustbalance").html("<b><i><font color='red' size='-1'>ORDERS BLOCKED</font></i></b>");
      $("#btnSave").attr("disabled", true);
      $("#btnIns").attr("disabled", true);
      //$('#txtprodnme').attr("disabled", true);
        //$('#txtprodid').attr("disabled", true);

    }
    
  }

}

function addItemName(qty,price,ndisc,curramt,amt,factr,cref,nrefident,citmcls){

	 if($("#txtprodid").val() != "" && $("#txtprodnme").val() !="" ){

		var isItem = "NO";
		var disID = "";

			$("#MyTable > tbody > tr").each(function() {	
				disID =  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				disref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				
				if($("#txtprodid").val()==disID && cref==disref){
					
					isItem = "YES";

				}
			});	

	 if(isItem=="NO"){	
		myFunctionadd(qty,price,ndisc,curramt,amt,factr,cref,nrefident,citmcls);
		//myFunctionadd(item.totqty,item.nprice,item.ndisc,item.namount,item.nfactor,item.xref,item.citmtyp)
		
		ComputeGross();	

	 }
	 else{

		addqty();	
			
	 }
		
		$("#txtprodid").val("");
		$("#txtprodnme").val("");
		$("#hdnunit").val("");
		$("#hdnqty").val("");
		$("#hdnqtyunit").val("");
		$("#hdnctype").val("");
		
	 }

}

function myFunctionadd(qty,pricex,ndisc,curramt,amtx,factr,cref,nrefident,citmcls){

	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = chkprice(itmcode,itmunit,itmccode,$("#date_delivery").val());
		var curramtz = price;
		//var amtz = price;
		var itmctype= $("#hdnctype").val();
		var factz = 1;
		var xdisc = 0;
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var curramtz = curramt;
		//var amtz = amtx;	
		var factz = factr;
		var itmctype= citmcls;
		var xdisc = ndisc;
	}	

	var baseprice = curramtz * parseFloat($("#basecurrval").val());
	
	var uomoptions = "";

	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	if(cref==null || cref==""){
		cref = ""
		qtystat = "";
							
		 $.ajax ({
			url: "../th_loaduomperitm.php",
			data: { id: itmcode },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){
					if(item.id==itmunit){
						isselctd = "selected";
					}
					else{
						isselctd = "";
					}
					
					uomoptions = uomoptions + '<option value='+item.id+' '+isselctd+'>'+item.name+'</option>';
				});
						
											 
			}
		});
		
		uomoptions = " <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">" + uomoptions + "</select>";
		
	}else{
		uomoptions = "<input type='hidden' value='"+itmunit+"' name=\"seluom\" id=\"seluom"+lastRow+"\">"+itmunit;
		qtystat = "readonly";
	}

		
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"> <input type='hidden' value='"+nrefident+"' name=\"txtcrefident\" id=\"txtcrefident\"> <input type='hidden' value='"+itmctype+"' name=\"hdncitmtype\" id=\"hdncitmtype"+lastRow+"\"> </td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmunit = "<td width=\"100\" nowrap>"+uomoptions+"</td>";

  var tditmvats = "";
  var itsvats = "";
    if(xChkVatableStatus==1){ 

      $.ajax ({
        url: "../../System/th_loadtax.php",
        async: false,
        dataType: "json",
        success: function( data ) {
                            
          console.log(data);
          $.each(data,function(index,item){

            if($("#hdncvat").val()==item.ctaxcode){
              itsvats = item.ctaxdesc+"<input type='hidden' name='selitmvatyp' id='selitmvatyp"+lastRow+"' data-id=\""+item.nrate+"\" value=\""+item.ctaxcode+"\">";
            }
                
          });
        }
      });

      tditmvats = "<td width=\"100\" nowrap>" + itsvats + "</td>";

    }

	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";

	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' > </td>";
	
	var tditmdisc = "<td width=\"100\" nowrap> <input type='text' value='"+xdisc+"' class='numeric form-control input-xs' style='text-align:right; cursor: pointer' name=\"txtndisc\" id='txtndisc"+lastRow+"' readonly onclick='getdiscount(this.id);'> </td>";

	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";
			
	var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice.toFixed(4)+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"' readonly> </td>";
	
	var tditmdel = "<td width=\"90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del"+ lastRow +"' value='delete' data-var='"+lastRow+"'/> &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> </td>";

	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmvats + tditmunit + tditmqty + tditmprice + tditmdisc + tditmbaseamount + tditmamount + tditmdel + '</tr>');

									$("#del"+lastRow).on('click', function() {
										
											var xy = $(this).data('var');
											
											$(this).attr("data-var",parseInt(xy)-1);
											
											$(this).closest('tr').remove();
											
											ReIdentity(xy);
											ComputeGross();									
										
									});

									$("input.numeric").autoNumeric('init',{mDec:2});


									/*
                  $("input.numeric").numeric(
                    {negative: false}
                  );

                  $("input.numericdec").numeric(
                    {
                      negative: false,
                      decimalPlaces: 4
                    }
                  );
									*/

									$("input.numeric, input.numericdec").on("click", function () {
									   $(this).select();
									});
									
									$("input.numeric, input.numericdec").on("keyup", function () {
									   ComputeAmt($(this).attr('id'));
									   ComputeGross();
									});
									
									$(".xseluom").on('change', function() {

										var xyz = chkprice(itmcode,$(this).val(),itmccode,xtoday);
										
										$('#txtnprice'+lastRow).val(xyz.trim());
										//alert($(this).attr('id'));
										ComputeAmt($(this).attr('id'));
										ComputeGross();
										
										var fact = setfactor($(this).val(), itmcode);
										//alert(fact);
										$('#hdnfactor'+lastRow).val(fact.trim());
										
									});
									
									ComputeGross();
									
									
}

			
	function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val().replace(/,/g,'');
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val().replace(/,/g,'');
			nprc = parseFloat(nprc);
			
			ndsc = $("#txtndisc"+r).val().replace(/,/g,'');
			ndsc = parseFloat(ndsc);
			
			if (parseFloat(ndsc) != 0) {
				nprcdisc = parseFloat(nprc) * (parseFloat(ndsc) / 100);
				nprc = parseFloat(nprc) - nprcdisc;

			}
			
			namt = nqty * nprc;
			namt2 = namt * parseFloat($("#basecurrval").val().replace(/,/g,''));
						
			$("#txtnamount"+r).val(namt2);

			$("#txtntranamount"+r).val(namt);	

			$("#txtntranamount"+r).autoNumeric('destroy');
			$("#txtnamount"+r).autoNumeric('destroy');

			$("#txtntranamount"+r).autoNumeric('init',{mDec:2});
			$("#txtnamount"+r).autoNumeric('init',{mDec:2});

	}

	function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var nnet = 0;
			var vatz = 0;

			var nnetTot = 0;
			var vatzTot = 0;

			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					
					//if(document.getElementById("hdncitmtype"+i).value=="GROCERY"){
				//		amtgro = parseFloat(amtgro) + parseFloat($("#txtnamount"+i).val());
				////	}
				////	else if(document.getElementById("hdncitmtype"+i).value=="CRIPPLES"){
				///		amtcrp = parseFloat(amtcrp) + parseFloat($("#txtnamount"+i).val());
			//		}
					if(xChkVatableStatus==1){  
						var slctdval = $("#selitmvatyp"+i).data('id');

						if(slctdval!=0){
							if(parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) > 0 ){

								//alert($("#txtnamount"+i).val() + "/ (1+" + slctdval + ")/100)))");

								nnet = parseFloat($("#txtntranamount"+i).val().replace(/,/g,'')) / parseFloat(1 + (parseInt(slctdval)/100));
								vatz = nnet * (parseInt(slctdval)/100);

								nnetTot = nnetTot + nnet;
								vatzTot = vatzTot + vatz;
							}
						}
					}

					gross = gross + parseFloat($("#txtntranamount"+i).val().replace(/,/g,''));
				}
			}

			gross2 = gross * parseFloat($("#basecurrval").val().replace(/,/g,''));

			$("#txtnNetVAT").val(nnetTot);
			$("#txtnVAT").val(vatzTot);
			$("#txtnGross").val(gross2);
			$("#txtnBaseGross").val(gross);
			//$("#txtnGroAmt").val(Number(amtgro).toLocaleString('en', { minimumFractionDigits: 4 }));
			//$("#txtnCrpAmt").val(Number(amtcrp).toLocaleString('en', { minimumFractionDigits: 4 }));


			$("#txtnNetVAT").autoNumeric('destroy');
			$("#txtnVAT").autoNumeric('destroy');			
			$("#txtnGross").autoNumeric('destroy');
			$("#txtnBaseGross").autoNumeric('destroy');

			$("#txtnNetVAT").autoNumeric('init',{mDec:2});
			$("#txtnVAT").autoNumeric('init',{mDec:2});
			$("#txtnGross").autoNumeric('init',{mDec:2});
			$("#txtnBaseGross").autoNumeric('init',{mDec:2});
			
			
	}


		function ReIdentity(xy){

			
			var rowCount = $('#MyTable tr').length;
						
			if(rowCount>1){
				for (var i = xy+1; i <= rowCount; i++) {
					//alert(i);
					var SelUOM = document.getElementById('seluom' + i); 
					var ItmTyp = document.getElementById('hdncitmtype' + i);
					var nQty = document.getElementById('txtnqty' + i);
					var MainUom = document.getElementById('hdnmainuom' + i);
					var nFactor = document.getElementById('hdnfactor' + i);
					var nPrice = document.getElementById('txtnprice' + i);
					var nDisc = document.getElementById('txtndisc' + i);
					var nAmount = document.getElementById('txtnamount' + i);
					var RowInfo = document.getElementById('row_' + i + '_info');					
					
					var za = i - 1;
					
					//alert(za);
					
					SelUOM.id = "seluom" + za;
					ItmTyp.id = "hdncitmtype" + za;
					nQty.id = "txtnqty" + za;
					MainUom.id = "hdnmainuom" + za;
					nFactor.id = "hdnfactor" + za;
					nPrice.id = "txtnprice" + za;
					nDisc.id = "txtndisc" + za;
					nAmount.id = "txtnamount" + za;
					RowInfo.id = "row_" + za + "_info";
					
				}
			}
		}
		
function addqty(){

	var itmcode = document.getElementById("txtprodid").value;

	var TotQty = 0;
	var TotAmt = 0;
	
	$("#MyTable > tbody > tr").each(function() {	
	var disID = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
	
	//alert(disID);
		if(disID==itmcode){
			
			var itmqty = $(this).find("input[name='txtnqty']").val();
			var itmprice = $(this).find("input[name='txtnprice']").val().replace(/,/g,'');
			
			//alert(itmqty +" : "+ itmprice);
			
			TotQty = parseFloat(itmqty) + 1;
			$(this).find("input[name='txtnqty']").val(TotQty);
			
			TotAmt = TotQty * parseFloat(itmprice);
			$(this).find("input[name='txtnamount']").val(TotAmt);
		}

	});
	
	ComputeGross();

}


function viewhidden(itmcde,itmnme){
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow2 = tbl.length-1;
	
	if(lastRow2>=1){
			$("#MyTable2 > tbody > tr").each(function() {	
			
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				//alert(citmno+"!="+itmcde);
				if(citmno!=itmcde){
					
					$(this).find('input[name="txtinfofld"]').attr("disabled", true);
					$(this).find('input[name="txtinfoval"]').attr("disabled", true);
					//$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled", true);
					
				}
				else{
					$(this).find('input[name="txtinfofld"]').attr("disabled", false);
					$(this).find('input[name="txtinfoval"]').attr("disabled", false);
				//	$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
					$(this).find('input[type="button"][name="delinfo"]').prop("disabled", false);
				}
				
			});
	}			
			
	addinfo(itmcde,itmnme);
	
	$('#MyDetModal').modal('show');
}

function addinfo(itmcde,itmnme){
	//alert(itmcde+","+itmnme);
	var tbl = document.getElementById('MyTable2').getElementsByTagName('tr');
	var lastRow = tbl.length;

	
	var tdinfocode = "<td><input type='hidden' value='"+itmcde+"' name='txtinfocode' id='txtinfocode"+lastRow+"'>"+itmcde+"</td>";
	var tdinfodesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmnme+"</td>"
	var tdinfofld = "<td><input type='text' name='txtinfofld' id='txtinfofld"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfoval = "<td><input type='text' name='txtinfoval' id='txtinfoval"+lastRow+"' class='form-control input-xs'></td>";
	var tdinfodel = "<td><input class='btn btn-danger btn-xs' type='button' name='delinfo' id='delinfo" + lastRow + itmcde + "' value='delete' /></td>";

	//alert(tdinfocode + "\n" + tdinfodesc + "\n" + tdinfofld + "\n" + tdinfoval + "\n" + tdinfodel);
	
	$('#MyTable2 > tbody:last-child').append('<tr>'+tdinfocode + tdinfodesc + tdinfofld + tdinfoval + tdinfodel + '</tr>');

									$("#delinfo"+lastRow+itmcde).on('click', function() {
										//alert($(this).prop('disabled'));
										//if($(this).prop('disabled')!==false){
											$(this).closest('tr').remove();
										//}
									});

}

function chkCloseInfo(){
	var isInfo = "TRUE";
	
	$("#MyTable > tbody > tr").each(function(index) {	
			
		var citmfld = $(this).find('input[name="txtinfofld"]');
		var citmval = $(this).find('input[name="txtinfoval"]');
		
		if(citmfld=="" || citmval==""){
			isInfo = "FALSE";
		}
				
	});

	
	if(isInfo == "TRUE"){
		$('#MyDetModal').modal('hide');	}
	else{
		alert("Incomplete info values!");
	}
}


function chkprice(itmcode,itmunit,ccode,datez){
	var result;
			
	$.ajax ({
		url: "../th_checkitmprice.php",
		data: { itm: itmcode, cust: ccode, cunit: itmunit, dte: datez },
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}

function setfactor(itmunit, itmcode){
	var result;
			
	$.ajax ({
		url: "../th_checkitmfactor.php",
		data: { itm: itmcode, cunit: itmunit },
		async: false,
		success: function( data ) {
			 result = data;
		}
	});
			
	return result;
	
}


function openinv(){
		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			$("#txtcustid").attr("readonly", true);
			$("#txtcust").attr("readonly", true);

			//clear table body if may laman
			$('#MyInvTbl tbody').empty(); 
			$('#MyInvDetList tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#InvListHdr').html("SO List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			
			$.ajax({
                    url: 'th_qolist.php',
					data: 'x='+x,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					   $("#allbox").prop('checked', false);
					   
                       console.log(data);
                       $.each(data,function(index,item){

								
						if(item.cpono=="NONE"){
						$("#AlertMsg").html("No Deliver Receipt Available");
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');

							xstat = "NO";
							
										$("#txtcustid").attr("readonly", false);
										$("#txtcust").attr("readonly", false);

						}
						else{
							$("<tr>").append(
							$("<td id='td"+item.cpono+"'>").text(item.cpono),
							$("<td>").text(item.ngross)
							).appendTo("#MyInvTbl tbody");
							
							
							$("#td"+item.cpono).on("click", function(){
								opengetdet($(this).text());
							});
							
							$("#td"+item.cpono).on("mouseover", function(){
								$(this).css('cursor','pointer');
							});
					   	}

                       });
					   

					   if(xstat=="YES"){
						   $('#mySIRef').modal('show');
					   }
                    },
                    error: function (req, status, err) {
						//alert();
						console.log('Something went wrong', status, err);
						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
					}
                });
			
			
			
		}

}

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("DR List: " + $('#txtcust').val() + " | DR Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
	$('#MyInvDetList tbody').empty();
	$('#MyDRDetList tbody').empty();
		
	$('#loadimg').show();
	
			var salesnos = "";
			var cnt = 0;
			
			$("#MyTable > tbody > tr").each(function() {
				myxref = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				
				if(myxref == drno){
					cnt = cnt + 1;
					
				  if(cnt>1){
					  salesnos = salesnos + ",";
				  }
							  
					salesnos = salesnos +  $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				}
				
			});

					//alert('th_sinumdet.php?x='+drno+"&y="+salesnos);
					$.ajax({
                    url: 'th_qolistdet.php',
					data: 'x='+drno+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false); 
					   
                      console.log(data);
					  $.each(data,function(index,item){
						  if(item.citemno==""){
							  alert("NO more items to add!")
						  }
						  else{
						  
							if (item.nqty>=1){
								$("<tr>").append(
								$("<td>").html("<input type='checkbox' value='"+item.citemno+"' name='chkSales[]' data-id=\""+drno+"\" data-curr=\""+item.ccurrencycode+"\">"),
								$("<td>").text(item.creference),
								$("<td>").text(item.citemno),
								$("<td>").text(item.cdesc),
								$("<td>").text(item.cunit),
								$("<td>").text(item.nqty),
								$("<td>").text(item.nprice),
								$("<td>").text(item.nbaseamount),
								$("<td>").text(item.ccurrencycode)
								).appendTo("#MyInvDetList tbody");
							}
					 	 }
					 });
                    },
					complete: function(){
						$('#loadimg').hide();
					},
                    error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
						console.log('Something went wrong', status, err);
 						$("#AlertMsg").html("Something went wrong<br>Status: "+status +"<br>Error: "+err);
						$("#alertbtnOK").show();
						$("#AlertModal").modal('show');
                   }
                });

}

function InsertSI(){	
	//check muna if pareparehas ng currency
	
	//get defsult curr if may laman na ang details
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var tblrowcnt = tbl.length;

	var trannocurr = "";
	var trannocurrcnt = 0;

		if(tblrowcnt>1){
			trannocurr = $("#selbasecurr").val();
			trannocurrcnt = 1;
		}

		$("input[name='chkSales[]']:checked").each( function () {

			if(trannocurr != $(this).data("curr")){
				trannocurr = $(this).data("curr");
				trannocurrcnt++;
			}
		});

	if(trannocurrcnt>1){
		alert("Multi currency in one invoice is not allowed!");
	}
	else{

	
   		$("input[name='chkSales[]']:checked").each( function () {
	   
	
				var tranno = $(this).data("id");
	   			var id = $(this).val();
	
				//alert("th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal);
	
	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id,
					type: "GET",
					dataType: "JSON",
					async: false,
					success: function(data)
					{	
					   console.log(data);
                       $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.desc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 
							$("#hdnqty").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							$("#hdncvat").val(item.ctaxcode);


							//alert(item.cqtyunit + ":" + item.cunit);
							//addItemName(item.totqty,item.nprice,"0",item.namount,item.nfactor,item.xref,item.citmcls)
							addItemName(item.totqty,item.nprice,"0",item.nbaseamount,item.namount,item.nfactor,item.xref,item.crefident,item.citmcls);
											   
					   });
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(jqXHR.responseText);
					}
					
				});

   		});
   		//alert($("#hdnQuoteNo").val());
   
  		 $('#mySIModal').modal('hide');
   		$('#mySIRef').modal('hide');
	}

}


function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "SI_edit.php";
		document.getElementById(frm).submit();
	}
}

function disabled(){

	$("#frmpos :input").attr("disabled", true);
	
	$("#txtcsalesno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnPrint").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){
	if(document.getElementById("hdnposted").value==1 || document.getElementById("hdncancel").value==1){
		if(document.getElementById("hdnposted").value==1){
			var msgsx = "POSTED"
		}
		
		if(document.getElementById("hdncancel").value==1){
			var msgsx = "CANCELLED"
		}
		
		document.getElementById("statmsgz").innerHTML = "<font style=\"font-size: x-small\">TRANSACTION IS ALREADY "+msgsx+", EDITING IS NOT ALLOWED!</font>";
		document.getElementById("statmsgz").style.color = "#FF0000";
		
	}
	else{
		
		$("#frmpos :input").attr("disabled", false);
		
			$("#txtcsalesno").val($("#hdncsalesno").val());
			$("#txtcsalesno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnPrint").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
					
		ComputeGross();
		
		
		
			if(xChkLimit==1){
					
					$.ajax({
						type:'post',
						url:'../get_customerid.php',
						data: 'c_id='+ $('#txtcustid').val(),                 
						success: function(value){
							//alert(value);
							if(value.trim()!=""){
								var data = value.split(":");

                limit = Number(data[3]).toLocaleString('en', { minimumFractionDigits: 4 }); 

                $('#ncustbalance2').html("");
                $('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
                $('#hdncustlimit').val(data[3]);
          
                checkcustlimit($('#txtcustid').val(), data[3]);

							}
						}
					});
								

				}
		

	}
}

function printchk(x){
	if(document.getElementById("hdncancel").value==1){	
		document.getElementById("statmsgz").innerHTML = "CANCELLED TRANSACTION CANNOT BE PRINTED!";
		document.getElementById("statmsgz").style.color = "#FF0000";
	}
	else{

		  var url = "SI_confirmprint.php?x="+x;
		  
		  $("#myprintframe").attr('src',url);


		  $("#PrintModal").modal('show');

	}
}


function loaddetails(){
	//alert($("#txtcsalesno").val());
	$.ajax ({
		url: "th_loaddetails.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				$('#txtprodnme').val(item.desc); 
				$('#txtprodid').val(item.id); 
				$("#hdnunit").val(item.cunit); 
				$("#hdnqty").val(item.nqty);
				$("#hdnqtyunit").val(item.cqtyunit);
       			$("#hdncvat").val(item.ctaxcode);
				
				//alert(item.citmtyp); myFunctionadd(qty,pricex,curramt,amtx,factr,cref,nrefident,citmcls)
				//alert(item.ndisc);
				myFunctionadd(item.totqty,item.nprice,item.ndisc,item.nbaseamount,item.namount,item.nfactor,item.xref,item.nrefident,item.citmtyp);
				//addItemName(item.totqty,item.nprice,item.namount,item.nfactor,item.xref);
			});
			
				$("#txtprodid").val("");
				$("#txtprodnme").val("");
				$("#hdnunit").val("");
				$("#hdnqty").val("");
				$("#hdnqtyunit").val("");
         		$("#hdncvat").val("");


		}
	});

}

function loaddetinfo(){
	$.ajax ({
		url: "th_loaddetinfo.php",
		data: { id: $("#txtcsalesno").val() },
		async: false,
		dataType: "json",
		success: function( data ) {
											
			console.log(data);
			$.each(data,function(index,item){

				addinfo(item.id,item.desc,item.fldnme,item.cvalue);

			});

		}
	});

}

function chkform(){
	var ISOK = "YES";
	
	if(document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value==""){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Customer Required!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		document.getElementById("txtcust").focus();
		return false;
		
		ISOK = "NO";
	}
	// ACTIVATE MUNA LAHAT NG INFO
	
	$("#MyTable2 > tbody > tr").each(function() {				

		var itmcde = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
		
		$(this).find('input[name="txtinfofld"]').attr("disabled", false);
		$(this).find('input[name="txtinfoval"]').attr("disabled", false);
		$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");

	});

	// Check pag meron wla Qty na Order
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;

	if(lastRow == 0){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;NO details found!");
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		return false;
		ISOK = "NO";
	}
	else{
		var msgz = "";
		var myqty = "";
		var myav = "";
		var myfacx = "";
		var myprice = "";

		$("#MyTable > tbody > tr").each(function(index) {
			
			myqty = $(this).find('input[name="txtnqty"]').val();
			myav = $(this).find('input[type="hidden"][name="hdnavailqty"]').val();
			myfacx = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
			
			myprice = $(this).find('input[name="txtnamount"]').val();
			
			if(myqty == 0 || myqty == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero or blank qty is not allowed: row " + index;	
			}else{
				var myqtytots = parseFloat(myqty) * parseFloat(myfacx);

				if(parseFloat(myav) < parseFloat(myqtytots)){
					msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
				}
			}
			
		//	if(myprice == 0 || myprice == ""){
		//		msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
		//	}

		});

		// Check if Credit Limit activated (kung sobra)
		if(xChkLimit==1){
			
			if(parseFloat($("#txtnGross").val().replace(/,/g, ''))>parseFloat($("#hdncustbalance").val().replace(/,/g, ''))){
					ISOK = "NO";
					msgz = "Available Credit Limit is not enough!";
					//$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Available Credit Limit is not enough!");
					//$("#alertbtnOK").show();
					//$("#AlertModal").modal('show');
					
					//return false;
					


			}
		}
		
		if(msgz!=""){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}
	
	if(ISOK == "YES"){
	var trancode = "";
	var isDone = "True";
	var VARHDRSTAT = "";
	var VARHDRERR = "";
	
		//Saving the header
		var trancode = $("#txtcsalesno").val();
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val();
		var selreinv = $("#selreinv").val();
    	var selsitypz = $("#selsityp").val();
		var siprintno = $("#csiprintno").val();
		var nnetvat = $("#txtnNetVAT").val(); 
		var nvat = $("#txtnVAT").val();
				
		//alert("SO_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		var myform = $("#frmpos").serialize();
		//alert(myform);
		$.ajax ({
			url: "SI_updatehdr.php",
			data: myform,
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>UPDATING SALES INVOICE: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				//alert(data.trim());
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			},
            error: function (req, status, err) {
						//alert('Something went wrong\nStatus: '+status +"\nError: "+err);
				console.log('Something went wrong', status, err);

				VARHDRSTAT = status;
				VARHDRERR = err;

            }
			
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
				if(index>0){
				
					var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
					var crefident = $(this).find('input[type="hidden"][name="txtcrefident"]').val();
					var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
					var cuom = $(this).find('select[name="seluom"]').val();
					
						if(cuom=="" || cuom==null){
							var cuom = $(this).find('input[type="hidden"][name="seluom"]').val();
						}
						
					var nqty = $(this).find('input[name="txtnqty"]').val();
					var nprice = $(this).find('input[name="txtnprice"]').val();
					var ndiscount = $(this).find('input[name="txtndisc"]').val();
					var ntranamt = $(this).find('input[name="txtntranamount"]').val();
					var namt = $(this).find('input[name="txtnamount"]').val();
					var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
					var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
				
					//alert("trancode="+trancode+"&crefno="+crefno+"&crefident="+crefident+"&indx="+index+"&citmno="+citmno+"&cuom="+cuom+"&nqty="+nqty+"&nprice="+nprice+"&ndiscount="+ndiscount+"&ntranamt="+ntranamt+"&namt="+namt+"&mainunit="+mainunit+"&nfactor="+nfactor+"&ccode="+ccode);

					if(nqty!==undefined){
							nqty = nqty.replace(/,/g,'');
							ndiscount = ndiscount.replace(/,/g,'');
							nprice = nprice.replace(/,/g,'');
							namt = namt.replace(/,/g,'');
							ntranamt = ntranamt.replace(/,/g,'');
						}
						
					$.ajax ({
						url: "SI_newsavedet.php",
						data: { trancode: trancode, crefno: crefno, crefident:crefident, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, ndiscount:ndiscount, ntranamt:ntranamt, namt:namt, mainunit:mainunit, nfactor:nfactor, ccode:ccode },
						async: false,
						success: function( data ) {
							if(data.trim()=="False"){
								isDone = "False";
							}
						}
					});
				}
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "SI_newsaveinfo.php",
					data: { trancode: trancode, indx: index, citmno: citmno, citmfld: citmfld, citmvlz:citmvlz },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});
			
			if(isDone=="True"){
				$("#AlertMsg").html("<b>SUCCESFULLY UPDATED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							//$("#txtcsalesno").val(trancode);
							$("#frmpos").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}
			
		}
		else{
			$("#AlertMsg").html("Something went wrong<br>Status: "+VARHDRSTAT +"<br>Error: "+VARHDRERR);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

		}


	}
		
		

}


function convertCurrency(fromCurrency) {
  
  toCurrency = $("#basecurrvalmain").val(); //statgetrate
   $.ajax ({
	 url: "../th_convertcurr.php",
	 data: { fromcurr: fromCurrency, tocurr: toCurrency },
	 async: false,
	 beforeSend: function () {
		 $("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
	 },
	 success: function( data ) {

		 $("#basecurrval").val(data);
		 $("#hidcurrvaldesc").val($( "#selbasecurr option:selected" ).text()); 
		 
	 },
	 complete: function(){
		 $("#statgetrate").html("");
		 recomputeCurr();
	 }
 });

}


function recomputeCurr(){

 var newcurate = $("#basecurrval").val();
 var rowCount = $('#MyTable tr').length;
		 
 var gross = 0;
 var amt = 0;

 if(rowCount>1){
	 for (var i = 1; i <= rowCount-1; i++) {
		 amt = $("#txtntranamount"+i).val();			
		 recurr = parseFloat(newcurate) * parseFloat(amt);

		 $("#txtnamount"+i).val(recurr.toFixed(4));
	 }
 }


 ComputeGross();


}

function getdiscount(xyz){
	alert(xyz);
}

</script>