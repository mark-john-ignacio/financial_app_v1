<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SO_new.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];

//echo $_SESSION['chkitmbal']."<br>";
//echo $_SESSION['chkcompvat'];

$ddeldate = date("m/d/Y");
$ddeldate = date("m/d/Y", strtotime($ddeldate . "+1 day"));

//echo $ddeldate;

/*
function listcurrencies(){ //API for currency list
	$apikey = $_SESSION['currapikey'];
  
	//$json = file_get_contents("https://free.currconv.com/api/v7/currencies?&apiKey={$apikey}");
	$json = file_get_contents("https://api.currencyfreaks.com/supported-currencies");
	//$obj = json_decode($json, true);
  
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
<script src="../../Bootstrap/js/jquery.numeric.js"></script>
<script src="../../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../../Bootstrap/js/bootstrap.js"></script>
<script src="../../Bootstrap/js/moment.js"></script>
<script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

</head>

<body style="padding:5px" onLoad="document.getElementById('txtcust').focus();">
<form action="SO_newsave.php" name="frmpos" id="frmpos" method="post">
	<fieldset>
    	<legend>New Sales Order</legend>	
<div class="col-xs-12 nopadwdown"><b>Sales Order Information</b></div>
<ul class="nav nav-tabs">
    <li class="active"><a href="#home">Order Details</a></li>
    <li><a href="#menu1">Delivered To</a></li>
</ul>
 
 <div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;text-align: left;overflow: auto">
 		<div class="tab-content">  

      <div id="home" class="tab-pane fade in active" style="padding-left:5px;">
             
        <table width="100%" border="0">
					<tr>
						<tH width="150">&nbsp;Customer:</tH>
						<td style="padding:2px">
							<div class="col-xs-12 nopadding">
									<div class="col-xs-3 nopadding">
										<input type="text" id="txtcustid" name="txtcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
											<input type="hidden" id="hdnvalid" name="hdnvalid" value="NO">
											<input type="hidden" id="hdnpricever" name="hdnpricever" value="">
									</div>

									<div class="col-xs-8 nopadwleft">
											<input type="text" class="form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
										</div> 
							</div>
						</td>
						<tH width="150">PO No.:</tH>
						<td style="padding:2px;">
						<div class="col-xs-11 nopadding">
							<input type='text' class="form-control input-sm" id="txtcPONo" name="txtcPONo" value="" autocomplete="off" />
						</div>
						</td>
					</tr>
					<tr>
						<tH width="150">&nbsp;Salesman:</tH>
						<td style="padding:2px">
							<div class="col-xs-12 nopadding">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtsalesmanid" name="txtsalesmanid" class="form-control input-sm" placeholder="Salesman Code..." tabindex="1">
								</div>

								<div class="col-xs-8 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtsalesman" name="txtsalesman" width="20px" tabindex="1" placeholder="Search Salesman Name..."  size="60" autocomplete="off">
								</div> 
							</div>
						</td>
						<tH width="150">Delivery Date:</tH>
						<td style="padding:2px;">
							<div class="col-xs-11 nopadding">
								<input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo $ddeldate; ?>" />
							</div>
						</td>
					</tr>
					<tr>
							<tH width="150">&nbsp;Remarks:</tH>
							<td style="padding:2px"><div class="col-xs-11 nopadding"><input type="text" class="form-control input-sm" id="txtremarks" name="txtremarks" width="20px" tabindex="2"></div>
							</td>
							<tH width="150">Sales Type:</th>
							<td style="padding:2px">
								<div class="col-xs-11 nopadding">
									<select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="1">
														<option value="Goods">Goods</option>
														<option value="Services">Services</option>
												</select>
								</div>
							</td>

							
					</tr>
					<tr>
						<tH width="150">&nbsp;Special Instructions:</tH>
						<td rowspan="3" style="padding:2px"><div class="col-xs-11 nopadding">
							<textarea rows="3"  class="form-control input-sm" name="txtSpecIns"  id="txtSpecIns"></textarea>
								</div>
						</td>
						<td style="padding:2px">
						<div class="chklimit"><b>Credit Limit:</b></div>
						</td>
						<td style="padding:2px"  align="right">
							<div class="chklimit col-xs-10 nopadding" id="ncustlimit"></div>
								<input type="hidden" id="hdncustlimit" name="hdncustlimit" value="">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style="padding:2px">
							<div class="chklimit"><b>Balance:</b></div>
						</td>
						<td style="padding:2px"  align="right">
							<div class="chklimit col-xs-10 nopadding" id="ncustbalance"></div>
								<input type="hidden" id="hdncustbalance" name="hdncustbalance" value="">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td style="padding:2px"  align="right" colspan="2">
							<div class="chklimit col-xs-11 nopadwright" id="ncustbalance2"></div>
						</td>							
					</tr>
					<tr>
						<tH width="150">&nbsp;Currency:</tH>
						<td rowspan="7" style="padding:2px">
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
												<option value="<?=$rows['id']?>" <?php if ($nvaluecurrbase==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
									<?php
														}	
													}
									?>
								</select>
									<input type='hidden' id="basecurrvalmain" name="basecurrvalmain" value="<?php echo $nvaluecurrbase; ?>"> 	
									<input type='hidden' id="hidcurrvaldesc" name="hidcurrvaldesc" value="<?php echo $nvaluecurrbasedesc; ?>"> 
							</div>
							<div class="col-xs-2 nopadwleft">
								<input type='text' class="numeric required form-control input-sm text-right" id="basecurrval" name="basecurrval" value="1">	 
							</div>

							<div class="col-xs-4" id="statgetrate" style="padding: 4px !important"> 
										
							</div>
						</td>
					</tr>
				</table>		
      </div>
        
      <div id="menu1" class="tab-pane fade" style="padding-left:5px">
				<table width="100%" border="0">
					<tr>
						<td width="150"><b>Customer</b></td>
						<td width="310" colspan="2" style="padding:2px">
							<div class="col-xs-8 nopadding">
								<div class="col-xs-3 nopadding">
									<input type="text" id="txtdelcustid" name="txtdelcustid" class="form-control input-sm" placeholder="Customer Code..." tabindex="1">
								</div>
								<div class="col-xs-9 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtdelcust" name="txtdelcust" width="20px" tabindex="1" placeholder="Search Customer Name..."  size="60" autocomplete="off">
								</div> 
							</div>						
						</td>
					</tr>
					<tr>
						<td><button type="button" class="btn btn-primary btn-sm" tabindex="6" id="btnNewAdd" name="btnNewAdd">Select Address</button></td>
						<td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  readonly="true" /></div></td>
					</tr>					
					<tr>
						<td>&nbsp;</td>
						<td colspan="2" style="padding:2px">
							<div class="col-xs-8 nopadding">
								<div class="col-xs-6 nopadding">
									<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off"  readonly="true" />
								</div>														
								<div class="col-xs-6 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off"   readonly="true" />
								</div>
							</div>
						</td>
					</tr> 
					<tr>
						<td>&nbsp;</td>
						<td colspan="2" style="padding:2px">
							<div class="col-xs-8 nopadding">
								<div class="col-xs-9 nopadding">
									<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" readonly="true" />
								</div>														
								<div class="col-xs-3 nopadwleft">
									<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off"  readonly="true" />
								</div>
							</div>
						</td>
					</tr>  
				</table>
        </div>
			</div>

		</div><!--tab-content-->

		<hr>
		<div class="col-xs-12 nopadwdown"><b>Details</b></div>
		<div class="col-xs-12 nopadwdown">
						<input type="hidden" name="hdnqty" id="hdnqty">
					<input type="hidden" name="hdnqtyunit" id="hdnqtyunit">
					<input type="hidden" name="hdnunit" id="hdnunit">
					
			<div class="col-xs-3 nopadding"><input type="text" id="txtprodid" name="txtprodid" class="form-control input-sm" placeholder="Search Product Code..." tabindex="4"></div>
				<div class="col-xs-5 nopadwleft"><input type="text" id="txtprodnme" name="txtprodnme" class="form-control input-sm	" placeholder="(CTRL + F) Search Product Name..." size="80" tabindex="5"></div>
		</div>         
            

		<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 1px solid #919b9c;width: 100%;height: 30vh;text-align: left;overflow: auto">
			
								<table id="MyTable" class="MyTable table table-condensed" width="100%">

							<tr>
								<th style="border-bottom:1px solid #999">Code</th>
								<th style="border-bottom:1px solid #999">Description</th>
														<th style="border-bottom:1px solid #999" id='tblAvailable'>Available</th>
														<th style="border-bottom:1px solid #999">UOM</th>
														<th style="border-bottom:1px solid #999">Qty</th>
								<th style="border-bottom:1px solid #999">Price</th>
														<th style="border-bottom:1px solid #999">Amount</th>
								<th style="border-bottom:1px solid #999">Total Amt in <?php echo $nvaluecurrbase; ?></th>
														<th style="border-bottom:1px solid #999">&nbsp;</th>
							</tr>
												
							<tbody class="tbody">
												</tbody>
												
					</table>

		</div>

		<div class="col-xs-12 nopadwtop2x">
			<div class="col-xs-7">
				<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='SO.php';" id="btnMain" name="btnMain">
	Back to Main<br>(ESC)</button>

				<button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="openinv();" id="btnIns" name="btnIns">
	Quote<br>(Insert)</button>	

				<input type="hidden" name="hdnrowcnt" id="hdnrowcnt"> 
    			<button type="button" class="btn btn-success btn-sm" tabindex="6" onClick="return chkform();" id="btnSave" name="btnSave">SAVE<br> (CTRL+S)</button>
			</div>	

			<div class="col-xs-2"  style="padding-top: 14px !important;">
					<b>TOTAL AMOUNT </b>
			</div>
			<div class="col-xs-3"  style="padding-top: 14px !important;">
				<input type="text" id="txtnBaseGross" name="txtnBaseGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
			</div>
		</div>  
		
		<div class="col-xs-12 nopadding">
			<div class="col-xs-7">
					
			</div>	

			<div class="col-xs-2">
					<b>TOTAL AMOUNT IN <?php echo $nvaluecurrbase; ?></b>
			</div>
			<div class="col-xs-3" >
				<input type="text" id="txtnGross" name="txtnGross" readonly value="0" style="text-align:right; border:none; background-color:#FFF; font-size:20px; font-weight:bold; color:#F00;" size="10">
			</div>
		</div>

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
                <h3 class="modal-title" id="InvListHdr">PO List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
       <div class="col-xs-12 nopadding">

                <div class="form-group">
                    <div class="col-xs-4 nopadding pre-scrollable" style="height:37vh">
                          <table name='MyInvTbl' id='MyInvTbl' class="table table-small table-highlight">
                           <thead>
                            <tr>
                              <th>Quote No</th>
                              <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                          </table>
                    </div>

                    <div class="col-xs-8 nopadwleft pre-scrollable" style="height:37vh">
                          <table name='MyInvDetList' id='MyInvDetList' class="table table-small">
                           <thead>
                            <tr>
                              <th align="center"> <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                              <th>Item No</th>
                              <th>Description</th>
                              <th>UOM</th>
                              <th>Qty</th>
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

								<input type="hidden" name="hdncurr" id="hdncurr">
								<input type="hidden" name="hdncurrate" id="hdncurrate">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End FULL INVOICE LIST -->

<!-- Address List -->
<div class="modal fade" id="MyAddModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="invheader"> Address Lists </h3>           
			</div>
    
            <div class="modal-body">
                <table id="MyAddTble" class="table table-condensed" width="100%">
                	<thead>
    				<tr>
                    	<th style="border-bottom:1px solid #999">&nbsp;</th>
						<th style="border-bottom:1px solid #999">House No.</th>
						<th style="border-bottom:1px solid #999">City</th>
                        <th style="border-bottom:1px solid #999">State</th>
						<th style="border-bottom:1px solid #999">Country</th>
                        <th style="border-bottom:1px solid #999">Zip</th>
                        <th style="border-bottom:1px solid #999">&nbsp;</th>
					</tr>
                    </thead>
					<tbody class="tbody">
                    </tbody>
                </table>
    
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


</form>


<!-- FULL PO LIST REFERENCES-->
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



<form method="post" name="frmedit" id="frmedit" action="SO_edit.php">
	<input type="hidden" name="txtctranno" id="txtctranno" value="">
</form>


</body>
</html>

<script type="text/javascript">
var xChkBal = "";
var xChkLimit = "";
var xChkLimitWarn = "";

var xtoday = new Date();
var xdd = xtoday.getDate();
var xmm = xtoday.getMonth()+1; //January is 0!
var xyyyy = xtoday.getFullYear();

xtoday = xmm + '/' + xdd + '/' + xyyyy;

	$(document).ready(function(e) {
			$(".nav-tabs a").click(function(){
    			$(this).tab('show');
			});
	

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
						   
					   });
					}
				});
	
		if(xChkBal==1){
			$("#tblAvailable").hide();
		}
		else{
			$("#tblAvailable").show();
		}


		if(xChkLimit==0){
			$(".chklimit").hide();
		}
		else{
			$(".chklimit").show();
		}
		

	  $('#txtprodnme').attr("disabled", true);
	  $('#txtprodid').attr("disabled", true);

    });


	$(document).keydown(function(e) {	
	
	  if(e.keyCode == 83 && e.ctrlKey) { //Ctrl S
	  	  e.preventDefault();
		 if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		  return chkform();
		 }
	  }
	  else if(e.keyCode == 27){ //ESC
		  e.preventDefault();
		if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
		 window.location.replace("SO.php");
	    }

	  }
	  else if(e.keyCode == 45) { //Insert
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			openinv();
		}
	  }
	  else if(e.keyCode == 70 && e.ctrlKey) { // CTRL + F .. search product code
	   if($('#hdnvalid').val()!="NO"){
		e.preventDefault();
	  	if($('#mySIRef').hasClass('in')==false && $('#AlertModal').hasClass('in')==false){
			$('#txtprodnme').focus();
		}
	   }
      }
	
	});

$(function(){
	    $('#date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY',
				// minDate: new Date(),
        });

		$("#allbox").click(function(){
			$('input:checkbox').not(this).prop('checked', this.checked);
		});

		$("#txtcustid").keydown(function(event){
		if(event.keyCode == 13){
		
		var dInput = this.value;
		
		$.ajax({
        type:'post',
        url:'../get_customerid.php',
        data: 'c_id='+ $(this).val(),                 
        success: function(value){

			if(value!=""){
				var data = value.split(":");
				 
				$('#txtcust').val(data[0]);
				//$('#imgemp').attr("src",data[2]);
				$('#hdnpricever').val(data[1]);
				//deliveredto   
				$('#txtdelcustid').val(dInput);
				$('#txtdelcust').val(data[0]); 
				 
				$('#txtsalesmanid').val(data[10]);
				$('#txtsalesman').val(data[11]);
				
				$('#txtchouseno').val(data[5]);
				$('#txtcCity').val(data[6]);
				$('#txtcState').val(data[7]);
				$('#txtcCountry').val(data[8]);
				$('#txtcZip').val(data[9]);
							
				$('#hdnvalid').val("YES");
				
				$('#txtremarks').focus();
				
				if(xChkLimit==1){

					var limit = data[3];
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}
					//alert(limit)
					limit = Number(limit).toLocaleString('en', { minimumFractionDigits: 4 });
					$('#ncustbalance2').html("");
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(data[3]);
					
					checkcustlimit($(this).val(), data[3]);
				}
				
			}
			else{
				$('#txtcustid').val("");
				$('#txtcust').val("");
				//$('#imgemp').attr("src","../../images/blueX.png");
				$('#hdnpricever').val("");
				
				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");
				
				$('#hdnvalid').val("NO");
			}
		},
		error: function(){
			$('#txtcustid').val("");
			$('#txtcust').val("");
			//$('#imgemp').attr("src","../../images/blueX.png");
			$('#hdnpricever').val("");

				$('#txtdelcustid').val("");
				$('#txtdelcust').val(""); 
				 
				$('#txtsalesmanid').val("");
				$('#txtsalesman').val("");
				
				$('#txtchouseno').val("");
				$('#txtcCity').val("");
				$('#txtcState').val("");
				$('#txtcCountry').val("");
				$('#txtcZip').val("");
							
			$('#hdnvalid').val("NO");
		}
		});

		}
		
	});

	$('#txtcust, #txtcustid').on("blur", function(){
		if($('#hdnvalid').val()=="NO"){
		  $('#txtcust').attr("placeholder", "ENTER A VALID CUSTOMER FIRST...");
		  
		  $('#txtprodnme').attr("disabled", true);
		  $('#txtprodid').attr("disabled", true);
		}else{
			
		  $('#txtprodnme').attr("disabled", false);
		  $('#txtprodid').attr("disabled", false);
		  
		  $('#txtremarks').focus();
	
		}
	});
	//Search Cust name
	$('#txtcust').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_customer.php",
				dataType: "json",
				data: {
					query: $("#txtcust").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtcust').val(item.value).change(); 
			$("#txtcustid").val(item.id);
			//$("#imgemp").attr("src",item.imgsrc);
			$("#hdnpricever").val(item.cver);

				$('#txtdelcustid').val(item.id);
				$('#txtdelcust').val(item.value); 
				 
				$('#txtsalesmanid').val(item.csman);
				$('#txtsalesman').val(item.smaname);
				
				$('#txtchouseno').val(item.chouseno);
				$('#txtcCity').val(item.ccity);
				$('#txtcState').val(item.cstate);
				$('#txtcCountry').val(item.ccountry);
				$('#txtcZip').val(item.czip);
							
			$('#hdnvalid').val("YES");
			
			$('#txtremarks').focus();
			
				if(xChkLimit==1){
					
					var limit = item.nlimit;
					if(limit % 1 == 0){
						limit = parseInt(limit);
					}

					limit = Number(limit).toLocaleString('en', { minimumFractionDigits: 4 });
					$('#ncustbalance2').html("");				
					$('#ncustlimit').html("<b><font size='+1'>"+limit+"</font></b>");
					$('#hdncustlimit').val(item.nlimit);
					
					checkcustlimit(item.id, item.nlimit);

				}
			
			
		}
	
	});
	
	$("#txtsalesmanid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_salesmanid.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
					if(value!=""){				 
						$('#txtsalesman').val(value);
					}
				}
			});
		}
	});
	
	$('#txtsalesman').typeahead({
		autoSelect: true,
		source: function(request, response) {
			$.ajax({
				url: "../th_salesman.php",
				dataType: "json",
				data: {
					query: $("#txtsalesman").val()
				},
				success: function (data) {
					response(data);
				}
			});
		},
		displayText: function (item) {
			return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.value + "</small></div>";
		},
		highlighter: Object,
		afterSelect: function(item) { 					
						
			$('#txtsalesman').val(item.value).change(); 
			$("#txtsalesmanid").val(item.id);
			
			
		}
	
	});
	
	$("#txtdelcustid").keydown(function(event){
		if(event.keyCode == 13){
		
			var dInput = this.value;
			
			$.ajax({
				type:'post',
				url:'../get_customerid.php',
				data: 'c_id='+ $(this).val(),                 
				success: function(value){
					if(value!=""){				 
						var data = value.split(":");

						$('#txtdelcust').val(data[0]); 
						
						$('#txtchouseno').val(data[5]);
						$('#txtcCity').val(data[6]);
						$('#txtcState').val(data[7]);
						$('#txtcCountry').val(data[8]);
						$('#txtcZip').val(data[9]);
					}
				}
			});
		}
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
			$("#hdnqty").val(item.nqty);
			$("#hdnqtyunit").val(item.cqtyunit);
			
			addItemName("","","","","","");
			
			
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
		 }
		 
		if(isItem=="NO"){		

			myFunctionadd("","","","","","");
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
    });
	
	$("#btnNewAdd").on("click", function(){
		if($("#txtdelcustid").val()=="" || $("#txtdelcust").val()==""){
			alert("Select Delivery To Customer!");
		}else{
			$('#MyAddTble tbody').empty();
			//get addressses...
			$.ajax({
				url : "th_addresslist.php?id=" + $("#txtdelcustid").val() ,
				type: "GET",
				dataType: "JSON",
				success: function(data)
				{	
					console.log(data);
                    $.each(data,function(index,item){
						
						$("<tr>").append(
						$("<td>").html("<a onclick=\"trclickable('"+item.chouseno+"','"+item.ccity+"','"+item.cstate+"','"+item.ccountry+"','"+item.czip+"')\" style=\"cursor: pointer;\">Select</a>"),
						$("<td>").html(item.chouseno),
						$("<td>").html(item.ccity),
						$("<td>").html(item.cstate),
						$("<td>").html(item.ccountry),
						$("<td>").html(item.czip)
						).appendTo("#MyAddTble tbody");
											   
					});
						
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(jqXHR.responseText);
				}					
			});
			
		
			 $("#MyAddModal").modal("show");// 
		}
	});

	$("#selbasecurr").on("change", function (){
			
		//convertCurrency($(this).val());

		var dval = $(this).find(':selected').attr('data-val');

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
			$('#txtprodnme').attr("disabled", true);
	  		$('#txtprodid').attr("disabled", true);

		}
	}

}

function addItemName(qty,price,curramt,amt,factr,cref){

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
	 	myFunctionadd(qty,price,curramt,amt,factr,cref);
		
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
		
	 }

}

function myFunctionadd(qty,pricex,curramt,amtx,factr,cref){
	//alert("hello");
	var itmcode = $("#txtprodid").val();
	var itmdesc = $("#txtprodnme").val();
	var itmqtyunit = $("#hdnqtyunit").val();
	var itmqty = $("#hdnqty").val();
	var itmunit = $("#hdnunit").val();
	var itmccode = $("#hdnpricever").val();
	//alert(itmqtyunit);
	if(qty=="" && pricex=="" && amtx=="" && factr==""){
		var itmtotqty = 1;
		var price = chkprice(itmcode,itmunit,itmccode,xtoday);
		var curramtz = price;
		//var amtz = price;
		var factz = 1;
	}
	else{
		var itmtotqty = qty
		var price = pricex;
		var curramtz = curramt;
		//var amtz = amtx;	
		var factz = factr;	
	}

	var baseprice = curramtz * parseFloat($("#basecurrval").val());

	
		if(xChkBal==1){
			var avail = "";
		}
		else{

			if($("#selsityp").val()=="Goods"){
				if(parseFloat(itmqty)>0){
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='"+itmqty+"'> " + itmqty + " " + itmqtyunit +" </td>";
					var qtystat = "";
				}
				else{
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> Unavailable </td>";
					var qtystat = "readonly";
					//itmtotqty = 0;
				}
			}else{
					var avail = "<td> <input type='hidden' name='hdnavailqty' id='hdnavailqty' value='0'> NA </td>";
					var qtystat = "";
					//itmtotqty = 0;
			}


		}
		
	
		var uomoptions = "";
								
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

		
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	if(cref==null){
		cref = ""
	}
	
	var tditmcode = "<td width=\"120\"> <input type='hidden' value='"+itmcode+"' name=\"txtitemcode\" id=\"txtitemcode\">"+itmcode+" <input type='hidden' value='"+cref+"' name=\"txtcreference\" id=\"txtcreference\"></td>";
	var tditmdesc = "<td style=\"white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px;\">"+itmdesc+"</td>";
	var tditmavail = avail;
	var tditmunit = "<td width=\"100\" nowrap> <select class='xseluom form-control input-xs' name=\"seluom\" id=\"seluom"+lastRow+"\">"+uomoptions+"</select> </td>";
	var tditmqty = "<td width=\"100\" nowrap> <input type='text' value='"+itmtotqty+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtnqty\" id=\"txtnqty"+lastRow+"\" autocomplete='off' onFocus='this.select();' "+qtystat+"> <input type='hidden' value='"+itmqtyunit+"' name='hdnmainuom' id='hdnmainuom"+lastRow+"'> <input type='hidden' value='"+factz+"' name='hdnfactor' id='hdnfactor"+lastRow+"'> </td>";
		
	var tditmprice = "<td width=\"100\" nowrap> <input type='text' value='"+price+"' class='numericdec form-control input-xs' style='text-align:right' name=\"txtnprice\" id='txtnprice"+lastRow+"' \"  "+qtystat+"> </td>";

	var tditmbaseamount = "<td width=\"100\" nowrap> <input type='text' value='"+curramtz+"' class='numeric form-control input-xs' style='text-align:right' name=\"txtntranamount\" id='txtntranamount"+lastRow+"' readonly> </td>";
			
	var tditmamount = "<td width=\"100\" nowrap> <input type='text' value='"+baseprice.toFixed(4)+"' class='form-control input-xs' style='text-align:right' name=\"txtnamount\" id='txtnamount"+lastRow+"'  readonly> </td>";
	
	var tditmdel = "<td width=\90\" nowrap> <input class='btn btn-danger btn-xs' type='button' id='del" + itmcode + "' value='delete' onClick=\"deleteRow(this);\"/> &nbsp; <input class='btn btn-primary btn-xs' type='button' id='row_" + lastRow + "_info' value='+' onclick = \"viewhidden('"+itmcode+"','"+itmdesc+"');\"/> </td>";


	$('#MyTable > tbody:last-child').append('<tr>'+tditmcode + tditmdesc + tditmavail + tditmunit + tditmqty + tditmprice + tditmbaseamount + tditmamount + tditmdel + '</tr>');

									$("#del"+itmcode).on('click', function() {
										$(this).closest('tr').remove();
									});


									$("input.numeric").numeric(
										{negative: false}
									);

									$("input.numericdec").numeric(
										{
											negative: false,
											decimalPlaces: 4
										}
									);

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
									
									
									
																		
}

			
		function ComputeAmt(nme){
			var r = nme.replace( /^\D+/g, '');
			var nnet = 0;
			var nqty = 0;
			
			nqty = $("#txtnqty"+r).val();
			nqty = parseFloat(nqty)
			nprc = $("#txtnprice"+r).val();
			nprc = parseFloat(nprc);
			
			namt = nqty * nprc;
			namt = namt.toFixed(4);

			namt2 = namt * parseFloat($("#basecurrval").val());
			namt2 = namt2.toFixed(4);

			
			$("#txtntranamount"+r).val(namt);		

			$("#txtnamount"+r).val(namt2);


		}

		function ComputeGross(){
			var rowCount = $('#MyTable tr').length;
			
			var gross = 0;
			var amt = 0;
			
			if(rowCount>1){
				for (var i = 1; i <= rowCount-1; i++) {
					amt = $("#txtntranamount"+i).val();
					
					gross = gross + parseFloat(amt);
				}
			}

			gross = gross.toFixed(4);

			gross2 = gross * parseFloat($("#basecurrval").val());
			gross2 = gross2.toFixed(4);

			
			$("#txtnBaseGross").val(gross);

			$("#txtnGross").val(gross2);
			
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
			var itmprice = $(this).find("input[name='txtnprice']").val();
			
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
				alert(citmno+"!="+itmcde);
				if(citmno!=itmcde){
					
					$(this).find('input[name="txtinfofld"]').attr("disabled", true);
					$(this).find('input[name="txtinfoval"]').attr("disabled", true);
					$(this).find('input[type="button"][name="delinfo"]').attr("class", "btn btn-danger btn-xs disabled");
					
				}
				else{
					$(this).find('input[name="txtinfofld"]').attr("disabled", false);
					$(this).find('input[name="txtinfoval"]').attr("disabled", false);
					$(this).find('input[type="button"][id="delinfo'+itmcde+'"]').attr("class", "btn btn-danger btn-xs");
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
										$(this).closest('tr').remove();
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
		// alert("?itm="+itmcode+"&cust="+ccode+"&cunit="+itmunit+"&dte="+datez)	
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
			$('#InvListHdr').html("Quote List: " + $('#txtcust').val())

			var xstat = "YES";
			
			//disable escape insert and save button muna
			
			$.ajax({
          url: 'th_qolist.php',
					data: 'x='+x+ "&selsi=" + $("#selsityp").val(),
          dataType: 'json',
          method: 'post',
          success: function (data) {
            // var classRoomsTable = $('#mytable tbody');
					  $("#allbox").prop('checked', false);
					   
            console.log(data);
          	$.each(data,function(index,item){

								
							if(item.cpono=="NONE"){
								$("#AlertMsg").html("No Quotations Available");
								$("#alertbtnOK").show();
								$("#AlertModal").modal('show');

								xstat = "NO";
								
								$("#txtcustid").attr("readonly", false);
								$("#txtcust").attr("readonly", false);

							}
							else{
								$("<tr>").append(
								$("<td id='td"+item.cpono+"' data-curr='"+item.ccurrencycode+"' data-rate='"+item.nexchangerate+"'>").text(item.cpono),
								$("<td>").text(item.ngross)
								).appendTo("#MyInvTbl tbody");
								
								
								$("#td"+item.cpono).on("click", function(){
									checkcurrency($(this).text(),$(this).data("curr"),$(this).data("rate"));
								//	opengetdet($(this).text());
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

function checkcurrency(tranno,currcode,currrate){
	var cnttbl = $('#MyTable tr').length - 1;

	if(cnttbl>0){
		//check if same ng currency
		if(currcode!=$("#selbasecurr").val()){
			var xyz = confirm("Currency of the selected reference is different from the previous reference selected.\nIf you continue, total amount in "+$("#basecurrvalmain").val()+" will be computed base from the current currency selected.");

			if(xyz==true){
				//$("#selbasecurr").val(currcode).change();
				//$("#basecurrval").val(currrate);
				opengetdet(tranno);
			}
		}
	}else{
		$("#hdncurr").val(currcode);
		$("#hdncurrate").val(currrate);
		opengetdet(tranno);
	}
}

function opengetdet(valz){
	var drno = valz;

	$("#txtrefSI").val(drno);

	$('#InvListHdr').html("Quote List: " + $('#txtcust').val() + " | Quote Details: " + drno + "<div id='loadimg'><center><img src='../../images/cusload.gif' style='show:none;'> </center> </div>");
	
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
												$("<td>").html("<input type='checkbox' value='"+item.citemno+"' name='chkSales[]' data-id=\""+drno+"\">"),
												$("<td>").text(item.citemno),
												$("<td>").text(item.cdesc),
												$("<td>").text(item.cunit),
												$("<td>").text(item.nqty)
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

	//checkcurrency muna
	//$("#selbasecurr").val(currcode).change();
				//$("#basecurrval").val(currrate);
	if($("#hdncurr").val()!=""){
		$("#selbasecurr").val($("#hdncurr").val()).change();
		$("#basecurrval").val($("#hdncurrate").val());
	}

	
   $("input[name='chkSales[]']:checked").each( function () {	   
				
				  var tranno = $(this).data("id");
	   			var id = $(this).val();

	   			$.ajax({
					url : "th_qolistput.php?id=" + tranno + "&itm=" + id + "&itmbal=" + xChkBal ,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{	
					    console.log(data);
              $.each(data,function(index,item){
						
							$('#txtprodnme').val(item.desc); 
							$('#txtprodid').val(item.id); 
							$("#hdnunit").val(item.cunit); 
							$("#hdnqty").val(item.nqty);
							$("#hdnqtyunit").val(item.cqtyunit);
							//alert(item.cqtyunit + ":" + item.cunit);
							addItemName(item.totqty,item.nprice,item.nbaseamount,item.namount,item.nfactor,item.xref)
											   
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


function chkform(){
	var ISOK = "YES";
	
	if((document.getElementById("txtcust").value=="" && document.getElementById("txtcustid").value=="") || (document.getElementById("txtdelcust").value=="" && document.getElementById("txtdelcustid").value=="")){

			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Customer Required/Delivered To Customer!");
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

				if($("#selsityp").val()=="Goods"){
					if(parseFloat(myav) < parseFloat(myqtytots)){
						msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Not enough inventory: row " + index;
					}
				}
			}
			
			if(myprice == 0 || myprice == ""){
				msgz = msgz + "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zero amount is not allowed: row " + index;	
			}

		});
		
		if(msgz!=""){
			$("#AlertMsg").html("");
			
			$("#AlertMsg").html("&nbsp;&nbsp;Details Error: "+msgz);
			$("#alertbtnOK").show();
			$("#AlertModal").modal('show');

			return false;
			ISOK = "NO";
		}
	}

	// Check if Credit Limit activated (kung sobra)
	if(xChkLimit==1){
		if(parseFloat($("#txtnGross").val())>parseFloat($("#hdncustbalance").val())){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>ERROR: </b> Available Credit Limit is not enough!");
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
				
				return false;
				ISOK = "NO";


		}
	}
		
	if(ISOK == "YES"){
	var trancode = "";
	var isDone = "True";
	
		//Saving the header
		var ccode = $("#txtcustid").val();
		var crem = $("#txtremarks").val();
		var ddate = $("#date_delivery").val();
		var ngross = $("#txtnGross").val();
		var csitype = $("#selsityp").val(); 
		var custpono = $("#txtcPONo").val();

		var ncurrcode = $("#selbasecurr").val();
		var ncurrdesc = $("#selbasecurr option:selected").text();
		var ncurrrate = $("#basecurrval").val();
		var nbasegross = $("#txtnBaseGross").val();

		$("#hidcurrvaldesc").val($("#selbasecurr option:selected").text());

		var specins = $("#txtSpecIns").val();
		var salesman = $("#txtsalesmanid").val();
		var delcodes = $("#txtdelcustid").val();
		var delhousno = $("#txtchouseno").val();
		var delcity = $("#txtcCity").val();
		var delstate = $("#txtcState").val();
		var delcountry = $("#txtcCountry").val();
		var delzip = $("#txtcZip").val();
		
		//alert("SO_newsavehdr.php?ccode=" + ccode + "&crem="+ crem + "&ddate="+ ddate + "&ngross="+ngross);
		//data: { ccode: ccode, crem: crem, ddate: ddate, ngross: ngross, selsityp: csitype, custpono:custpono, salesman:salesman, delcodes:delcodes, delhousno:delhousno, delcity:delcity, delstate:delstate, delcountry:delcountry, delzip:delzip, specins:specins, ncurrcode:ncurrcode, ncurrdesc:ncurrdesc, ncurrrate:ncurrrate, nbasegross:nbasegross },  frmpos

		var myform = $("#frmpos").serialize();
		$.ajax ({
			url: "SO_newsavehdr.php",
			data: myform,
			async: false,
			beforeSend: function(){
				$("#AlertMsg").html("&nbsp;&nbsp;<b>SAVING NEW ORDER: </b> Please wait a moment...");
				$("#alertbtnOK").hide();
				$("#AlertModal").modal('show');
			},
			success: function( data ) {
				if(data.trim()!="False"){
					trancode = data.trim();
				}
			}
		});
		
		if(trancode!=""){
			//Save Details
			$("#MyTable > tbody > tr").each(function(index) {	
			
				var crefno = $(this).find('input[type="hidden"][name="txtcreference"]').val();
				var citmno = $(this).find('input[type="hidden"][name="txtitemcode"]').val();
				var cuom = $(this).find('select[name="seluom"]').val();
				var nqty = $(this).find('input[name="txtnqty"]').val();
				var nprice = $(this).find('input[name="txtnprice"]').val();
				var namt = $(this).find('input[name="txtnamount"]').val();
				var nbaseamt = $(this).find('input[name="txtntranamount"]').val();
				var mainunit = $(this).find('input[type="hidden"][name="hdnmainuom"]').val();
				var nfactor = $(this).find('input[type="hidden"][name="hdnfactor"]').val();
			
				$.ajax ({
					url: "SO_newsavedet.php",
					data: { trancode: trancode, crefno: crefno, indx: index, citmno: citmno, cuom: cuom, nqty:nqty, nprice: nprice, namt:namt, nbaseamt:nbaseamt, mainunit:mainunit, nfactor:nfactor },
					async: false,
					success: function( data ) {
						if(data.trim()=="False"){
							isDone = "False";
						}
					}
				});
				
			});


			//Save Info
			$("#MyTable2 > tbody > tr").each(function(index) {	
			  
				var citmno = $(this).find('input[type="hidden"][name="txtinfocode"]').val();
				var citmfld = $(this).find('input[name="txtinfofld"]').val();
				var citmvlz = $(this).find('input[name="txtinfoval"]').val();
			
				$.ajax ({
					url: "SO_newsaveinfo.php",
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
				$("#AlertMsg").html("<b>SUCCESFULLY SAVED: </b> Please wait a moment...");
				$("#alertbtnOK").hide();

					setTimeout(function() {
						$("#AlertMsg").html("");
						$('#AlertModal').modal('hide');
			
							$("#txtctranno").val(trancode);
							$("#frmedit").submit();
			
					}, 3000); // milliseconds = 3seconds

				
			}
			
		}
		else{
				$("#AlertMsg").html("<b>ERROR: </b> There's a problem saving your transaction...<br><br>" + trancode);
				$("#alertbtnOK").show();
				$("#AlertModal").modal('show');
		}


	}

}

function trclickable(hsno,ccty,stt,ctry,zip){
	$('#txtchouseno').val(hsno);
	$('#txtcCity').val(ccty);
	$('#txtcState').val(stt);
	$('#txtcCountry').val(ctry);
	$('#txtcZip').val(zip);
	
	$("#MyAddModal").modal("hide");
}

function convertCurrency(fromCurrency) {
  
  toCurrency = $("#basecurrvalmain").val(); //statgetrate

   $.ajax ({
	 url: "../th_convertcurr.php",
	 data: { tocurr: fromCurrency, fromcurr: toCurrency },
	 async: false,
	 beforeSend: function () {
		 $("#statgetrate").html(" <i>Getting exchange rate please wait...</i>");
	 },
	 success: function( data ) {

		 $("#basecurrval").val(data);
		 
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

</script>