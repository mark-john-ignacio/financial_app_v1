<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "OR_new.php";

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

	<title>Coop Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css?h=<?php echo time();?>" rel="stylesheet" type="text/css"/>

<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap3-typeahead.min.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../include/jquery-maskmoney.js" type="text/javascript"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px; height:700px" onLoad="document.getElementById('txtctranno').focus(); disabled();">

<?php

    	$sqlchk = mysqli_query($con,"Select a.cacctcode, a.ccode, a.namount, a.cpaymethod, a.cpaytype, DATE_FORMAT(a.dcutdate,'%m/%d/%Y') as dcutdate, a.namount, a.lapproved, a.lcancelled, a.lprintposted, a.cornumber, a.cremarks, b.cname, d.cname as csuppname, c.cacctdesc, c.nbalance From receipt a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join accounts c on a.compcode=c.compcode and a.cacctcode=c.cacctno left join suppliers d on a.compcode=d.compcode and a.ccode=d.ccode where a.compcode='$company' and a.ctranno='$corno'");
if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cacctcode'];
			$nDebitDesc = $row['cacctdesc'];
			$nBalance = $row['nbalance'];
			
			
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
    <div class="col-xs-12 nopadding">
    <div class="col-xs-2 nopadding"><input type="text" class="form-control input-sm" id="txtctranno" name="txtctranno" width="20px" tabindex="1" value="<?php echo $corno;?>" onKeyUp="chkSIEnter(event.keyCode,'frmOR');"></div>
      
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
    	Deposit To Account
    
    </tH>
    <td style="padding:2px;" width="500">
  <div class="col-xs-12 nopadding">
    <div class="col-xs-6 nopadding">
        	<input type="text" class="form-control input-sm" id="txtcacct" name="txtcacct" width="20px" tabindex="1" placeholder="Search Account Description..." required value="<?php echo $nDebitDesc;?>">
    </div> 
	<div class="col-xs-6 nopadding">
        	<input type="text" id="txtcacctid" name="txtcacctid" style="border:none; height:30px;" readonly  value="<?php echo $nDebitDef;?>">
   	</div>
   </div>     
    </td>
    <tH width="150">Balance:</tH>
    <td style="padding:2px;">
    <div class="col-xs-8 nopadding">
    <input type="text" id="txtacctbal" name="txtacctbal" class="form-control input-sm" readonly value="<?php echo $nBalance;?>"  style="text-align:right">
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
    <tH width="210" valign="top">Payor:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-12 nopadding">
        <div class="col-xs-6 nopadding">
        	<input type="text" class="typeahead form-control input-sm" id="txtcust" name="txtcust" width="20px" tabindex="2" placeholder="Search Customer Name..." required autocomplete="off" value="<?php echo $cName ;?>"  />
		</div> 
		<div class="col-xs-3 nopadwleft">
        	<input type="text" id="txtcustid" name="txtcustid" style="border:none; height:30px;" readonly value="<?php echo $cCode ;?>">
        </div>
    </div>        
    </td>
    <th valign="top" style="padding:2px">Date:</th>
    <td valign="top" style="padding:2px"><div class="col-xs-8 nopadding">
      <input type='text' class="form-control input-sm" id="date_delivery" name="date_delivery" value="<?php echo date_format(date_create($dDate),'m/d/Y'); ?>" />
    </div></td>
    <!--
    <tH width="150" style="padding:2px">Payment Type:</tH>
    <td style="padding:2px">
    <div class="col-xs-8 nopadding">

     <select id="selpaytype" name="selpaytype" class="form-control input-sm selectpicker">
       <option value="None" <?php //if($cPaytype=="None") { echo "selected"; } ?>>Others</option>
       <option value="Sales" <?php //if($cPaytype=="Sales") { echo "selected"; } ?>>Sales</option>
       <option value="Loans" <?php  //if($cPaytype=="Loans") { echo "selected"; } ?>>Loans</option>
       <option value="Shares" <?php //if($cPaytype=="Shares") { echo "selected"; } ?>>Shares</option>
       <option value="Fee" <?php //if($cPaytype=="Fee") { echo "selected"; } ?>>Membership Fee</option>

     </select>

    </div></td>
    -->
  </tr>
  <tr>
    <tH width="210" valign="top">Payment Method:</tH>
    <td valign="top" style="padding:2px">
    <div class="col-xs-12 nopadding">
     <div class="col-xs-6 nopadding">
      <select id="selpayment" name="selpayment" class="form-control input-sm selectpicker">
          <option value="Cash" <?php if($cPayMeth=="Cash") { echo "selected"; } ?>>Cash</option>
          <option value="Cheque" <?php if($cPayMeth=="Cheque") { echo "selected"; } ?>>Cheque</option>
        </select>
     </div>
     
     <div class="col-xs-4 nopadwleft">
       <button type="button" class="btn btn-primary btn-sm" tabindex="6" style="width:100%" name="btnDet" id="btnDet">Details</button>
     </div>
    </div>
    
    
    </td>
    <tH style="padding:2px">OR No.:</tH>
    <td style="padding:2px">
      <div class="col-xs-8 nopadding">
      <input type="text" class="form-control input-sm" id="txtORNo" name="txtORNo" width="20px" required value="<?php echo $cORNo;?>" readonly>
    </div></td>
  </tr>
  <tr>
    <tH width="210" rowspan="2" valign="top">Memo:</tH>

    <td rowspan="2" valign="top" style="padding:2px">
    <div class="col-xs-12 nopadding">
      <div class="col-xs-10 nopadding">
        <textarea class="form-control" rows="2" id="txtremarks" name="txtremarks"><?php echo $cRemarks;?></textarea>
      </div>
    </div>
    </td>
    <th valign="top" style="padding:2px">&nbsp;</th>
    <td valign="top" style="padding:2px">&nbsp;</td>
    </tr>
  <tr>
    <th valign="top" style="padding:2px">Amount Received:</th>
    <td valign="top" style="padding:2px">
    <div class="col-xs-8 nopadding">
    <?php
    	if($cPaytype=="Sales" or $cPaytype=="Loans") {
			$vargrossstat = "readonly";
		}
		else{
			$vargrossstat = "";
		}
	?>
      <input type="text" id="txtnGross" name="txtnGross" class="form-control" value="<?php echo $nAmount;?>" style="text-align:right" <?php echo $vargrossstat; ?>>
    </div></td>
  </tr>
      </table>
      
      
  <ul class="nav nav-tabs">
    <li class="active"><a href="#divSales">Sales Invoice</a></li>
    <li><a href="#divLoans">Loans</a></li>
     <li><a href="#divOthers">Others</a></li>
  </ul>


<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">

    <div class="tab-content">
    
        <div id="divSales" class="tab-pane fade in active">
        	<div class="col-xs-12 nopadwdown">
             <button type="button" class="btn btn-xs btn-info" onClick="getInvs();">
            	<i class="fa fa-search"></i>&nbsp; Find Invoice
             </button>
			</div>
                  <div id="tableContainer" class="alt2" dir="ltr" style="
                                    margin: 0px;
                                    padding: 3px;
                                    border: 1px solid #919b9c;
                                    width: 1500px;
                                    height: 260px;
                                    text-align: left;
                                    overflow: auto">
                    <table width="100%" border="0" cellpadding="3" id="MyTable">
                    <thead>
                      <tr>
                        <th scope="col" width="10%" nowrap>Invoice No</th>
                        <th scope="col" width="110px" class="text-center" nowrap>Date</th>
                        <th scope="col" width="110px" class="text-right" nowrap>Amount</th>
                        <th scope="col" width="110px" class="text-right" nowrap>DM</th>
                        <th scope="col" width="110px" class="text-right" nowrap>CM</th>
                        <th scope="col" width="110px" class="text-right" nowrap>Payments</th>
                        <th scope="col" width="110px" class="text-right" nowrap>Total Due</th>
                        <th scope="col" width="110px" class="text-right" nowrap>Amt Applied&nbsp;</th>
                        <th scope="col" width="100px">&nbsp;Acct No</th>
                        <th scope="col" width="500px" nowrap>Acct Desc</th>
                         <th scope="col">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
            
                <?php
                        
                        $sqlbody = mysqli_query($con,"select a.*,b.dcutdate, b.ngross, c.cacctdesc from receipt_sales_t a left join sales b on a.csalesno=b.ctranno and a.compcode=b.compcode left join accounts c on a.cacctno=c.cacctno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
            
                        if (mysqli_num_rows($sqlbody)!=0) {
                            $cntr = 0;
                            while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                                            $cntr = $cntr + 1;
                ?>
                           <tr>
                            <td><div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo<?php echo $cntr;?>' id='txtcSalesNo<?php echo $cntr;?>' value='<?php echo $rowbody['csalesno'];?>'  /><?php echo $rowbody['csalesno'];?></div></td>
                            <td align='center'><?php echo $rowbody['dcutdate'];?></td>
                            <td align='right'><div class='col-xs-12 nopadwleft'><input type='hidden' name='txtAmt<?php echo $cntr;?>' id='txtAmt<?php echo $cntr;?>' value='<?php echo $rowbody['ngross'];?>' /><?php echo $rowbody['ngross'];?></div></td>
             
                             <td align='right'><div class='col-xs-12 nopadwleft'><input type='hidden' name='txtndebit<?php echo $cntr;?>' id='txtndebit<?php echo $cntr;?>' value='<?php echo $rowbody['ndm'];?>' /><?php echo $rowbody['ndm'];?></div></td>
            
                            <td align='right'><div class='col-xs-12 nopadwleft'><input type='hidden' name='txtncredit<?php echo $cntr;?>' id='txtncredit<?php echo $cntr;?>' value='<?php echo $rowbody['ncm'];?>' /><?php echo $rowbody['ncm'];?></div></td>
            
                            <td align='right'><div class='col-xs-12 nopadwleft'><input type='hidden' name='txtnpayments<?php echo $cntr;?>' id='txtnpayments<?php echo $cntr;?>' value='<?php echo $rowbody['npayment'];?>' /><?php echo $rowbody['npayment'];?></div></td>
            
                            <td align='right'><div class='col-xs-12 nopadwleft'><input type='hidden' name='txtDue<?php echo $cntr;?>' id='txtDue<?php echo $cntr;?>' value="<?php echo $rowbody['ndue'];?>"/><?php echo $rowbody['ndue'];?></div></td>
                            
                            <td style='padding: 1px;'><div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs' name='txtApplied<?php echo $cntr;?>' id='txtApplied<?php echo $cntr;?>' value="<?php echo $rowbody['namount'];?>" style="text-align:right" autocomplete="off" /></div></td>
                             <td style='padding: 1px;'><div class='col-xs-12 nopadding'><input type='text' name='txtcSalesAcctNo<?php echo $cntr;?>' id='txtcSalesAcctNo<?php echo $cntr;?>' value='<?php echo $rowbody['cacctno'];?>' class='form-control input-xs' autocomplete="off" /></td>
                             
                            <td style='padding: 1px;'><div class='col-xs-12 nopadding'><input type='text' name='txtcSalesAcctTitle<?php echo $cntr;?>' id='txtcSalesAcctTitle<?php echo $cntr;?>' value='<?php echo $rowbody['cacctdesc'];?>' class='form-control input-xs' /></td>

                            <td><div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow(this);' autocomplete="off" /></div></td>
                          </tr>
                          
                          <script>
						  			$("#txtcSalesAcctNo<?php echo $cntr;?>, #txtcSalesAcctTitle<?php echo $cntr;?>").on("click focus", function(event) {
										$(this).select();
									});
									
									$("#txtcSalesAcctNo<?php echo $cntr;?>").on("keyup", function(event) {
										if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
										
											if(event.keyCode==13 ){	
											var dInput = this.value;
									
												$.ajax({
													type:'post',
													url:'getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														if(value.trim()!=""){
															$("#txtcSalesAcctTitle<?php echo $cntr;?>").val(value.trim());
														}
													}
												});
											}
											
											setPosi("txtcSalesAcctNo<?php echo $cntr;?>",event.keyCode,'MyTable');
											
										}
										
									});
									
									$("#txtcSalesAcctTitle<?php echo $cntr;?>").typeahead({
								
									items: 10,
									source: function(request, response) {
										$.ajax({
											url: "th_accounts.php",
											dataType: "json",
											data: {
												query: $("#txtcSalesAcctTitle<?php echo $cntr;?>").val()
											},
											success: function (data) {
												response(data);
												
											}
										});
									},
									autoSelect: true,
									displayText: function (item) {
										 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
									},
									highlighter: Object,
									afterSelect: function(item, event) { 
										$("#txtcSalesAcctTitle<?php echo $cntr;?>").val(item.name).change(); 
										$("#txtcSalesAcctNo<?php echo $cntr;?>").val(item.id);
										
										setPosi("txtcSalesAcctTitle<?php echo $cntr;?>",13,'MyTable');
										
									}
									});
						  </script>
                
                <?php
                                        }
                                    }
                ?>
            </tbody>
            </table>
            <input type="hidden" name="hdnrowcnt" id="hdnrowcnt" value="0">
            </div>

		</div>


		<div id="divLoans" class="tab-pane fade">
        	<div class="col-xs-12 nopadwdown">
             <button type="button" class="btn btn-xs btn-info" onClick="getLoans();">
            	<i class="fa fa-search"></i>&nbsp; Find Loan Reference
             </button>
			</div>
                  <div id="tblLoContainer" class="alt2" dir="ltr" style="
                                    margin: 0px;
                                    padding: 3px;
                                    border: 1px solid #919b9c;
                                   width: 1300px;
                                    height: 200px;
                                    text-align: left;
                                    overflow: auto">
                                <table width="100%" border="0" cellpadding="3" id="MyTbl">
                                <thead>
                                  <tr>
                                    <th scope="col" width="10%" nowrap>Loan No</th>
                                    <th scope="col" width="110px" class="text-center" nowrap>Date</th>
                                    <th scope="col" width="110px" class="text-center" nowrap>Date</th>
                                    <th scope="col" width="110px" class="text-right">Loan Amt</th>
                                    <th scope="col" width="110px" class="text-right">Deduction</th>
                                    <th scope="col" width="110px" class="text-right">Balance</th>
                                    <th scope="col" width="110px" class="text-right" nowrap>Amt Paid&nbsp;</th>
                                    <th scope="col" width="100px">&nbsp;Acct No</th>
                                    <th scope="col" width="500px" nowrap>&nbsp;Acct Desc</th>
                                  </tr>
                                </thead>
            <tbody>
                <?php                        
                  $sqlbody = mysqli_query($con,"select a.*,b.dbegin, b.dend from receipt_loans_t a left join loans b on a.cloanno=b.ctranno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
            
                  if (mysqli_num_rows($sqlbody)!=0) {
                    $cntr = 0;
                  while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                     $cntr = $cntr + 1;
                ?>
            
              <tr>
                <td>
                    <div class='col-xs-12 nopadding'>
                        <input type='hidden' name='txtcLoanNo<?php echo $cntr;?>' id='txtcLoanNo<?php echo $cntr;?>' value='<?php echo $rowbody['cloanno'];?>' /><?php echo $rowbody['cloanno'];?>
                    </div>
                </td>
                <td><?php echo $rowbody['dbegin'];?></td>
                <td><?php echo $rowbody['dend'];?></td>
                <td>
                    <div class='col-xs-12 nopadwleft'>
                        <input type='hidden' name='txtLoTotal<?php echo $cntr;?>' id='txtLoTotal<?php echo $cntr;?>' value='<?php echo $rowbody['ntotal'];?>' />
                            <?php echo $rowbody['ntotal'];?>
                    </div>
                </td>
                <td>
                    <div class='col-xs-12 nopadwleft'>
                            <input type='hidden' name='txtLoDedct<?php echo $cntr;?>' id='txtLoDedct<?php echo $cntr;?>' value='<?php echo $rowbody['ndeduction'];?>' />
                            <?php echo $rowbody['ndeduction'];?>
                     </div>
                </td>
                <td>
                    <div class='col-xs-12 nopadwleft'>
                            <input type='hidden' name='txtLoBalnc<?php echo $cntr;?>' id='txtLoBalnc<?php echo $cntr;?>' value='<?php echo $rowbody['nbalance'];?>' />
                            <?php echo $rowbody['nbalance'];?>
                    </div>
                </td>
                <td>
                    <div class='col-xs-12 nopadwleft'>
                        <input type='text' class='numeric form-control input-xs' name='txtLoApplied<?php echo $cntr;?>' id='txtLoApplied<?php echo $cntr;?>' value='<?php echo $rowbody['namount'];?>' style="text-align:right" autocomplete="false" />
                    </div>
                </td>
                <td style="padding:1px">
                    <div class='col-xs-12 nopadwleft'>
                        <input type='text' class='form-control input-xs' name='txtcLoanAcctNo<?php echo $cntr;?>' id='txtcLoanAcctNo<?php echo $cntr;?>' value='<?php echo $rowbody['cacctno'];?>' autocomplete="false" />
                    </div>
                </td>

                <td style="padding:1px">
                    <div class='col-xs-12 nopadwleft'>
                        <input type='text' class='form-control input-xs' name='txtcLoanAcctTitle<?php echo $cntr;?>' id='txtcLoanAcctTitle<?php echo $cntr;?>' value='<?php echo $rowbody['cacctno'];?>' autocomplete="false" />
                    </div>
                </td>

                <td>
                    <input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntr;?>_delete' value='delete' onClick='deleteRow2(this);' />
                </td>
              </tr>
              				<script>
							
									$("#txtcLoanAcctNo<?php echo $cntr;?>, #txtcLoanAcctTitle<?php echo $cntr;?>").on("click focus", function(event) {
										$(this).select();
									});
									
									$("#txtcLoanAcctNo<?php echo $cntr;?>").on("keyup", function(event) {
										if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
										
											if(event.keyCode==13 ){	
											var dInput = this.value;
									
												$.ajax({
													type:'post',
													url:'getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														if(value.trim()!=""){
															$("#txtcLoanAcctTitle<?php echo $cntr;?>").val(value.trim());
														}
													}
												});
											}
											
											setPosi("txtcLoanAcctNo<?php echo $cntr;?>",event.keyCode,'MyTbl');
											
										}
										
									});
									
									$("#txtcLoanAcctTitle<?php echo $cntr;?>").typeahead({
								
									items: 10,
									source: function(request, response) {
										$.ajax({
											url: "th_accounts.php",
											dataType: "json",
											data: {
												query: $("#txtcLoanAcctTitle<?php echo $cntr;?>").val()
											},
											success: function (data) {
												response(data);
												
											}
										});
									},
									autoSelect: true,
									displayText: function (item) {
										 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
									},
									highlighter: Object,
									afterSelect: function(item, event) { 
										$("#txtcLoanAcctTitle<?php echo $cntr;?>").val(item.name).change(); 
										$("#txtcLoanAcctNo<?php echo $cntr;?>").val(item.id);
										
										setPosi("txtcLoanAcctTitle<?php echo $cntr;?>",13,'MyTbl');
										
									}
									});
									
							</script>
            
                <?php
                                        }
                                    }
                ?>
            
            </tbody>
            </table>
            <input type="hidden" name="hdnLocnt" id="hdnLocnt" value="0">
            </div>
		</div>

        <div id="divOthers" class="tab-pane fade">
          <div class="col-xs-12 nopadwdown">
            <button type="button" class="btn btn-xs btn-info" onClick="addacct();">
            	<i class="fa fa-plus"></i>&nbsp; Add New Line
            </button>
          </div>

                  <div id="tblOtContainer" class="alt2" dir="ltr" style="
                                    margin: 0px;
                                    padding: 3px;
                                    border: 1px solid #919b9c;
                                    width: 100%;
                                    height: 200px;
                                    text-align: left;
                                    overflow: auto">
                    <table width="100%" border="0" cellpadding="3" id="MyTblOthers">
                    <thead>
                      <tr>
                        <th scope="col">Account No.</th>
                        <th scope="col">Account Title</th>
                        <th scope="col">Amount</th>
                        <th scope="col">&nbsp;</th>
                      </tr>
					 </thead>
                     <tbody>
					<?php
                            
                            $sqlbody = mysqli_query($con,"select a.*,b.cacctdesc from receipt_others_t a left join accounts b on a.cacctno=b.cacctno and a.compcode=b.compcode where a.compcode='$company' and a.ctranno = '$corno' order by a.nidentity");
                
                            if (mysqli_num_rows($sqlbody)!=0) {
                                $cntr = 0;
                                while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
                                                $cntr = $cntr + 1;
                    ?>
                      <tr>
                        <td width="150px" style="padding:1px"><input type='text' name="txtacctno<?php echo $cntr;?>" id="txtacctno<?php echo $cntr;?>" class="form-control input-sm" placeholder="Enter Acct Code..." style="text-transform:uppercase" autocomplete="off" value="<?php echo $rowbody['cacctno'];?>"></td>
                        <td style="padding:1px"><input type='text' name="txtacctitle<?php echo $cntr;?>" id="txtacctitle<?php echo $cntr;?>" class="form-control input-sm" placeholder="Search Acct Desc..." style="text-transform:uppercase" autocomplete="off" value="<?php echo $rowbody['cacctdesc'];?>"></td>
                        <td width="100px" style="padding:1px"><input type='text' name="txtnotamt<?php echo $cntr;?>" id="txtnotamt<?php echo $cntr;?>" class="numeric form-control input-sm" style="text-align:right" value="<?php echo $rowbody['namount'];?>" onkeydown="return isNumber(event.keyCode)" onkeyup="compgross();" required autocomplete="off"></td>
                        <td width="50px" style="padding:1px"><input class='btn btn-danger btn-xs' type='button' id='row3_<?php echo $cntr;?>_delete' value='delete' onClick="deleteRow3(this);"/></td>
                      </tr>                 
                    
                    <?php
                                }
                            }
                    ?>


                   </tbody>
                    </table>
                    <input type="hidden" name="hdnOthcnt" id="hdnOthcnt" value="0">
                   </div>


        </div>

	</div>
</div>

<table width="100%" border="0" cellpadding="3">
  <tr>
    <td width="50%">
<button type="button" class="btn btn-primary btn-sm" tabindex="6" onClick="window.location.href='OR.php';" id="btnMain" name="btnMain">
Back to Main<br>(ESC)</button>
   
    <button type="button" class="btn btn-default btn-sm" tabindex="6" onClick="window.location.href='OR_new2.php';" id="btnNew" name="btnNew">
New<br>(F1)</button>

    <button type="button" class="btn btn-danger btn-sm" tabindex="6" onClick="chkSIEnter(13,'frmOR');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>

    <button type="button" class="btn btn-info btn-sm" tabindex="6" onClick="printchk('<?php echo $corno;?>');" id="btnPrint" name="btnPrint">
Print<br>(CTRL+P)
    </button>
    
    <button type="button" class="btn btn-warning btn-sm" tabindex="6" onClick="enabled();" id="btnEdit" name="btnEdit">
Edit<br>(CTRL+E)    </button>
    
    <button type="submit" class="btn btn-success btn-sm" tabindex="6" id="btnSave" name="btnSave">
Save<br>(CTRL+S)    </button>

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
            
            <div class="modal-body" style="height:40vh">
            
            	<div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
                  <table name='MyORTbl' id='MyORTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allbox" id="allbox" type="checkbox" value="Check All" /></th>
                      <th>Invoice No</th>
                      <th>Sales Date</th>
                      <th>Gross</th>
                      <th>DM</th>
                      <th>CM</th>
                      <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
               </div> 
            
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnInsert" onclick="save();" class="btn btn-primary">Insert</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<!-- Bootstrap modal LOANS -->
<div class="modal fade" id="myLoMod" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="loanheader">Loans List</h3>
            </div>
            
            <div class="modal-body" style="height:40vh">
            
            	<div class="col-xs-12 nopadding pre-scrollable" style="height:37vh">
                  <table name='MyLOTbl' id='MyLOTbl' class="table table-scroll table-striped">
                   <thead>
                    <tr>
                      <th align="center">
                      <input name="allboxLO" id="allboxLO" type="checkbox" value="Check All" /></th>
                      <th>Loan No</th>
                      <th>Deducion Start</th>
                      <th>Deduction End</th>
                      <th>Amount</th>
                      <th>Deduction Amt</th>
                      <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
				  </table>
                
             </div>
			</div>
			
            <div class="modal-footer">
                
                <button type="button" id="btnInsLo" onclick="saveLO();" class="btn btn-primary">Insert</button>
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
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1000' id='txtDenom1000' value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>" /></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1000' id='txtAmt1000' readonly value="<?php if($Pcs1000<>0){ echo $Pcs1000; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">500</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom500' id='txtDenom500' value="<?php if($Pcs500<>0){ echo $Pcs500; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt500' id='txtAmt500' readonly value="<?php if($Amt500<>0){ echo $Amt500; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">200</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom200' id='txtDenom200' value="<?php if($Pcs200<>0){ echo $Pcs200; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt200' id='txtAmt200' readonly value="<?php if($Amt200<>0){ echo $Amt200; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">100</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom100' id='txtDenom100' value="<?php if($Pcs100<>0){ echo $Pcs100; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt100' id='txtAmt100' readonly value="<?php if($Amt100<>0){ echo $Amt100; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">50</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom50' id='txtDenom50' value="<?php if($Pcs50<>0){ echo $Pcs50; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt50' id='txtAmt50' readonly value="<?php if($Amt50<>0){ echo $Amt50; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">20</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom20' id='txtDenom20' value="<?php if($Pcs20<>0){ echo $Pcs20; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt20' id='txtAmt20' readonly value="<?php if($Amt20<>0){ echo $Amt20; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">10</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom10' id='txtDenom10' value="<?php if($Pcs10<>0){ echo $Pcs10; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt10' id='txtAmt10' readonly value="<?php if($Amt10<>0){ echo $Amt10; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">5</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom5' id='txtDenom5' value="<?php if($Pcs5<>0){ echo $Pcs5; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt5' id='txtAmt5' readonly value="<?php if($Amt5<>0){ echo $Amt5; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">1</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom1' id='txtDenom1' value="<?php if($Pcs1<>0){ echo $Pcs1; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt1' id='txtAmt1' readonly value="<?php if($Amt1<>0){ echo $Amt1; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.25</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom025' id='txtDenom025' value="<?php if($Pcs025<>0){ echo $Pcs025; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt025' id='txtAmt025' readonly value="<?php if($Amt025<>0){ echo $Amt025; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.10</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom010' id='txtDenom010' value="<?php if($Pcs010<>0){ echo $Pcs010; } ?>"/></div></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtAmt010' id='txtAmt010' readonly value="<?php if($Amt010<>0){ echo $Amt010; } ?>"/></div></td>
                      </tr>
                      <tr>
                        <td align="center">0.05</td>
                        <td><div class='col-xs-12'><input type='text' class='numericint form-control input-sm' name='txtDenom005' id='txtDenom005' value="<?php if($Pcs005<>0){ echo $Pcs005; } ?>"/></div></td>
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
                            <input type='text' class="form-control input-sm" placeholder="Pick a Date" name="txtChekDate" id="txtChekDate"  value="<?php echo date_format(date_create($dDateCheck),'m/d/Y'); ?>"/>

                        </div>
                        </td>
                      </tr>
                      <tr>
                        <td><b>Cheque Number</b></td>
                        <td><div class='col-xs-12'><input type='text' class='form-control input-sm' name='txtCheckNo' id='txtCheckNo' placeholder="Input Cheque Number"  value="<?php echo $cCheckNo; ?>"/></div></td>
                      </tr>
                       <tr>
                        <td><b>Cheque Amount</b></td>
                        <td><div class='col-xs-12'><input type='text' class='numericchkamt form-control input-sm' name='txtCheckAmt' id='txtCheckAmt' placeholder="Input Cheque Amount"  value="<?php echo $nCheckAmt; ?>" /></div></td>
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

</body>
</html>

<script type="text/javascript">
	$(document).keydown(function(e) {	 
	
		if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='OR_new2.php';
			}
		}
		else if(e.keyCode == 83 && e.ctrlKey){//CTRL S
			if($("#btnSave").is(":disabled")==false){ 
				e.preventDefault();
				$("#btnSave").click();
			}
		}
		else if(e.keyCode == 69 && e.ctrlKey){//CTRL E
			if($("#btnEdit").is(":disabled")==false){
				e.preventDefault();
				enabled();
			}
		}
		else if(e.keyCode == 80 && e.ctrlKey){//CTRL+P
			if($("#btnPrint").is(":disabled")==false){
				e.preventDefault();
				printchk('<?php echo $corno;?>');
			}
		}
		else if(e.keyCode == 90 && e.ctrlKey){//CTRL Z
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmOR');
			}
		}
		else if(e.keyCode == 27){//ESC
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				$("#btnMain").click();
			}
		}
		else if(e.keyCode == 45) { //Insert    
			if($('#myModal').hasClass('in')==false && $('#CashModal').hasClass('in')==false && $('#ChequeModal').hasClass('in')==false && $('#OthersModal').hasClass('in')==false && $('#MyAdjustmentModal').hasClass('in')==false){
				getInvs();
			}
		}
	});
	
	$(document).ready(function(){
		$(".nav-tabs a").click(function(){
			$(this).tab('show');
		});
		
		$("input.numeric").numeric({decimalPlaces: 4});
        $("input.numeric").on("click focus", function () {
              $(this).select();
        });

		
		$("input.numeric").on("keyup", function (e) {
			var nme = $(this).attr('name');
			var x = nme.replace(/[0-9]/g, '');
			
			if(x=="txtApplied"){
				var tblnme = "MyTable";
			}else if(x=="txtLoApplied"){
				var tblnme = "MyTbl";
			}
			
			setPosi($(this).attr('name'),e.keyCode,tblnme);
			computeGross();
		});

	
	});


$(function() {   

	$('#frmOR').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
		e.preventDefault();
		return false;
	  }
	});           
           // Bootstrap DateTimePicker v4
           $('#datetimepicker4, #txtChekDate, #date_delivery').datetimepicker({
                 format: 'MM/DD/YYYY'
           });
		   
	$("input.numericchkamt").numeric({decimalPlaces: 4});
	$("input.numericchkamt").on("click focus", function () {
		$(this).select();
	});
	
	$("input.numericint").numeric( {decimalPlaces: false, negative: false} );
	$("input.numericint").on("click focus", function () {
		$(this).select();
	});
											
	$('.numericint').keydown(function (e) {
		
        if (e.which == 39) { // right arrow
          $(this).closest('td').next().find('input').focus();
 
        } else if (e.which == 37) { // left arrow
          $(this).closest('td').prev().find('input').focus();
 
        } else if (e.which == 40) { // down arrow
          $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
 
        } else if (e.which == 38) { // up arrow
          $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
        }
	});
	
	$('.numericint').keyup(function (e) {
		
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

	});
	
	$("#txtCheckAmt").on('keyup', function() {
		if($("#selpaytype").val() == "None"){
			$('#txtnGross').val($(this).val());
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
				url: "th_csall.php",
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
			 return '<div style="border-top:1px solid gray; width: 300px"><span><b>' + item.typ + ": </b>"+ item.id + '</span><br><small>' + item.value + "</small></div>";
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
	
	$("#selpayment").on("change", function(){
		$('#txtnGross').val('0.00');
		
		 if ($(this).val() == "Cheque"){
			$('#txtnGross').attr('readonly', true);
		 }
		 else{
			$('#txtnGross').attr('readonly', false);
		 }


		var valz = $(this).val();
		var codez = "";
		
		if(valz=="Cash"){
			codez = "ORDEBCASH";
		}
		else if(valz=="Cheque"){
			codez = "ORDEBCHK";
		}
		//alert(valz);
		
		 $.ajax ({
			url: "th_parameter.php",
			data: { id: codez },
			async: false,
			dataType: "json",
			success: function( data ) {
											
				console.log(data);
				$.each(data,function(index,item){

					$('#txtcacct').val(item.name);
					$('#txtcacctid').val(item.id);
					$('#txtacctbal').val(item.balance);
				});
						
											 
			}
		});

	});
	

	$("#btnDet").on('click', function() {
		if($('#selpayment').val() == "Cash"){
			$('#CashModal').modal('show');
		}
		if($('#selpayment').val() == "Cheque"){
			$('#ChequeModal').modal('show');
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
		
			var tbl1 = document.getElementById('MyTable').getElementsByTagName('tr');
			var lastRow1 = tbl1.length-1;
		
			if(lastRow1!=0){
				$("#hdnrowcnt").val(lastRow1);				
			}
				
			var tbl2 = document.getElementById('MyTbl').getElementsByTagName('tr');
			var lastRow2 = tbl2.length-1;
					
			if(lastRow2!=0){
				$("#hdnLocnt").val(lastRow2);	
			}
	
			var tbl3 = document.getElementById('MyTblOthers').getElementsByTagName('tr');
			lastRow3 = tbl3.length-1;
					
			if(lastRow3!=0){
				$("#hdnOthcnt").val(lastRow3);	
			}
	
		if(lastRow1==0 && lastRow2==0 && lastRow3==0){
				alert("Details Required!");
				subz = "NO";
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
			var tempdue= document.getElementById('txtDue' + z);
			var tempapplies = document.getElementById('txtApplied' + z);
			
			var x = z-1;
			tempsalesno.id = "txtcSalesNo" + x;
			tempsalesno.name = "txtcSalesNo" + x;
			tempamt.id = "txtAmt" + x;
			tempamt.name = "txtAmt" + x;
			tempdue.id = "txtDue" + x;
			tempdue.name = "txtDue" + x;
			tempapplies.id = "txtApplied" + x;
			tempapplies.name = "txtApplied" + x;
			
			//tempnqty.onkeyup = function(){ computeamt(this.value,x,event.keyCode); };

		}

computeGross();

}
function deleteRow2(r) {
	var tbl = document.getElementById('MyTbl').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTbl').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempsalesno = document.getElementById('txtcLoanNo' + z);
			var tempamt = document.getElementById('txtLoTotal' + z);
			var tempdisc= document.getElementById('txtLoDedct' + z);
			var tempdue= document.getElementById('txtLoBalnc' + z);
			var tempapplies = document.getElementById('txtLoApplied' + z);
			
			var x = z-1;
			tempsalesno.id = "txtcLoanNo" + x;
			tempsalesno.name = "txtcLoanNo" + x;
			tempamt.id = "txtLoTotal" + x;
			tempamt.name = "txtLoTotal" + x;
			tempdisc.id = "txtLoDedct" + x;
			tempdisc.name = "txtLoDedct" + x;
			tempdue.id = "txtLoBalnc" + x;
			tempdue.name = "txtLoBalnc" + x;
			tempapplies.id = "txtLoApplied" + x;
			tempapplies.name = "txtLoApplied" + x;
			
		}

computeGross();

}

function computeGross(){
	//alert("Hello";)
	var tot = 0;
	var tot2 = 0;
	var tot3 = 0;
	
	var tbl = document.getElementById('MyTable').getElementsByTagName('tr');
	var lastRow = tbl.length-1;
	if(lastRow!=0){
		var x = 0;
		
		for (z=1; z<=lastRow; z++){
			x = document.getElementById('txtApplied' + z).value;
			
			x = x.replace(",","");
			if(x!=0 && x!=""){
				tot = parseFloat(x) + parseFloat(tot);	
			}
		}
	}
	
	//alert(parseFloat(tot));
	
	var tbl2 = document.getElementById('MyTbl').getElementsByTagName('tr');
	var lastRow2 = tbl2.length-1;
	if(lastRow2!=0){
		var x2 = 0;
		for (z2=1; z2<=lastRow2; z2++){
			x2 = document.getElementById('txtLoApplied' + z2).value;
			
			x2 = x2.replace(",","");
			if(x2!=0 && x2!=""){
				tot2 = parseFloat(x2) + parseFloat(tot2);	
			}
		}
	}
	
	//alert(parseFloat(tot2));

	var tbl3 = document.getElementById('MyTblOthers').getElementsByTagName('tr');
	var lastRow3 = tbl3.length-1;

	if(lastRow3!=0){
		var x3 = 0;
		for (z3=1; z3<=lastRow3; z3++){
			x3 = document.getElementById('txtnotamt' + z3).value;
			
			x3 = x3.replace(",","");
			if(x3!=0 && x3!=""){
				tot3 = parseFloat(x3) + parseFloat(tot3);	
			}
		}
	}
	
	
	var XTOTGross = parseFloat(tot) + parseFloat(tot2) + parseFloat(tot3);
	
	document.getElementById('txtnGross').value = XTOTGross.toFixed(2);

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
			//alert(rc);
			for(y=1;y<=rc-1;y++){ 
			  if(y>1){
				  salesnos = salesnos + ",";
			  }
				salesnos = salesnos + $('#txtcSalesNo'+y).val();
				//alert(salesnos)
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#invheader').html("Invoice List: " + $('#txtcust').val())
			
			//alert('x='+x+"&y="+salesnos)
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
						$("<td>").text(item.ngross),
						$("<td>").text(item.ndebit),
						$("<td>").text(item.ncredit)
                        ).appendTo("#MyORTbl tbody");

                       });
                    },
                    error: function (jqXHR, textStatus, errorThrown)
					{
						if(errorThrown!="Unexpected end of JSON input"){
						}
					}
                });
			
			$('#myModal').modal('show');
			
		}


}

function getLoans(){

		if($('#txtcustid').val() == ""){
			alert("Please pick a valid customer!");
		}
		else{
			
			//clear table body if may laman
			

			$('#MyLOTbl tbody').empty();
			
			//get salesno na selected na
			var y;
			var salesnos = "";
			
			var rc = $('#MyTbl tr').length;
			for(y=1;y<=rc-1;y++){ 
			  if(y>1){
				  salesnos = salesnos + ",";
			  }
				salesnos = salesnos + $('#txtcLoanNo'+y).val();
			}

			//ajax lagay table details sa modal body
			var x = $('#txtcustid').val();
			$('#loanheader').html("Loans List: " + $('#txtcust').val())
			
			$.ajax({
                    url: 'th_loanlist.php',
					data: 'x='+x+"&y="+salesnos,
                    dataType: 'json',
                    method: 'post',
                    success: function (data) {
                       
                        console.log(data);
                        $.each(data,function(index,item){
							//alert(item.ctranno);
                        $("<tr>").append(
						$("<td>").html("<input type='checkbox' value='"+item.ctranno+"' name='chkLoans[]'>"),
                        $("<td>").text(item.ctranno),
                        $("<td>").text(item.dbegin),
						$("<td>").text(item.dend),
						$("<td>").text(item.npaymnt),
						$("<td>").text(item.ndedamt)
                        ).appendTo("#MyLOTbl tbody");
						
						//alert(item.ctranno + "-" + item.dbegin + "-" + item.dend + "-" + item.npaymnt + "-" + item.ndedamt);
                       });
                    },
                    error: function (jqXHR, textStatus, errorThrown)
					{
						if(errorThrown!="Unexpected end of JSON input"){
						}
					}
                });
			
			$('#myLoMod').modal('show');
			
		}


}


function save(){

	var i = 0;
	var rcnt = 0;
	
	//var rowCount = $('#MyTable tr').length-1;
	//var rcnt = rowCount - 1;	
   $("input[name='chkSales[]']:checked").each( function () {
	   i += 1;
	  // rcnt += 1;
	  var tbl = document.getElementById('MyTable').getElementsByTagName('tbody')[0];
	 // alert(tbl.rows.length);
	  		
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getsalesdetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{				
					
					   console.log(data);
                       $.each(data,function(index,item){
						   
						   var ngross = item.ngross;
						   var ndebit = item.ndebit;
						   var ncredit = item.ncredit;
						   var npayment = item.npayment;
						   var ndue = 0;
						   
						   	ndue = ((parseFloat(ngross) + parseFloat(ndebit)) - parseFloat(ncredit)) - parseFloat(npayment);
							
							if(parseFloat(npayment)==0){
								npayment = "0.0000"
							}
							
							var lastRow = tbl.rows.length + 1;
							
							var z=tbl.insertRow(-1);

							var a=z.insertCell(-1);
								a.innerHTML ="<div class='col-xs-12 nopadding'><input type='hidden' name='txtcSalesNo"+lastRow+"' id='txtcSalesNo"+lastRow+"' value='"+item.csalesno+"' />"+item.csalesno+"</div>";
							
							var b=z.insertCell(-1);
								b.align = "center";
								b.innerHTML = item.dcutdate;
								
							var c=z.insertCell(-1);
								c.align = "right";
								c.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtAmt"+lastRow+"' id='txtAmt"+lastRow+"' value='"+item.ngross+"' />"+item.ngross+"</div>";
								
							var d=z.insertCell(-1);
								d.align = "right";
								d.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtndebit"+lastRow+"' id='txtndebit"+lastRow+"' value='"+item.ndebit+"' />"+item.ndebit+"</div>";
								
							var e=z.insertCell(-1);
								e.align = "right";
								e.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtncredit"+lastRow+"' id='txtncredit"+lastRow+"' value='"+item.ncredit+"' />"+item.ncredit+"</div>";
								
							var f=z.insertCell(-1);
								f.align = "right";
								f.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtnpayments"+lastRow+"' id='txtnpayments"+lastRow+"' value='"+item.npayment+"' />"+npayment+"</div>";
								
							var g=z.insertCell(-1);
								g.align = "right";
								g.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtDue"+lastRow+"' id='txtDue"+lastRow+"' value='"+ndue.toFixed(4)+"' />"+ndue.toFixed(4)+"</div>";
								
							var h=z.insertCell(-1);
								h.style.padding = "1px";
								h.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs' name='txtApplied"+lastRow+"' id='txtApplied"+lastRow+"' value='0.0000' style='text-align:right' autocomplete=\"off\" /></div>";
								
							var i=z.insertCell(-1);
								i.style.padding = "1px";
								i.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='form-control input-xs' name='txtcSalesAcctNo"+lastRow+"' id='txtcSalesAcctNo"+lastRow+"' value='"+item.cacctno+"' autocomplete=\"off\" /></div>";
								
							var j=z.insertCell(-1);
								j.style.padding = "1px";
								j.innerHTML = "<div class='col-xs-12 nopadding'><input type='text' class='form-control input-xs' name='txtcSalesAcctTitle"+lastRow+"' id='txtcSalesAcctTitle"+lastRow+"' value='txtcSalesAcctTitle"+lastRow+"' autocomplete=\"off\" /></div>";
								
							var k=z.insertCell(-1);
								k.innerHTML = "<div class='col-xs-12 nopadwleft'><input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' value='delete' onClick='deleteRow(this);' /></div>";
							
													   
									$("input.numeric").numeric({decimalPlaces: 4});
									$("input.numeric").on("click focus", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function (e) {
										setPosi($(this).attr('name'),e.keyCode,'MyTable');
										computeGross();
									});
									
									$("#txtcSalesAcctNo"+lastRow+", #txtcSalesAcctTitle"+lastRow).on("click focus", function(event) {
										$(this).select();
									});
									
									$("#txtcSalesAcctNo"+lastRow).on("keyup", function(event) {
										if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
										
											if(event.keyCode==13 ){	
											var dInput = this.value;
									
												$.ajax({
													type:'post',
													url:'getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														if(value.trim()!=""){
															$("#txtcSalesAcctTitle"+lastRow).val(value.trim());
														}
													}
												});
											}
											
											setPosi("txtcSalesAcctNo"+lastRow,event.keyCode,'MyTable');
											
										}
										
									});
									
									$("#txtcSalesAcctTitle"+lastRow).typeahead({
								
									items: 10,
									source: function(request, response) {
										$.ajax({
											url: "th_accounts.php",
											dataType: "json",
											data: {
												query: $("#txtcSalesAcctTitle"+lastRow).val()
											},
											success: function (data) {
												response(data);
												
											}
										});
									},
									autoSelect: true,
									displayText: function (item) {
										 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
									},
									highlighter: Object,
									afterSelect: function(item, event) { 
										$("#txtcSalesAcctTitle"+lastRow).val(item.name).change(); 
										$("#txtcSalesAcctNo"+lastRow).val(item.id);
										
										setPosi("txtcSalesAcctTitle"+lastRow,13,'MyTable');
										
									}
									});
							
					   
					   	
					   });

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

function setPosi(nme,keyCode,tbl){
		var r = nme.replace(/\D/g,'');
		var namez = nme.replace(/[0-9]/g, '');
		
		//alert(nme+";"+keyCode);
		var tbl = document.getElementById(tbl).getElementsByTagName('tr');
		var lastRow = tbl.length-1;
		
//
		//if(namez=="txtApplied"){
			//alert(keyCode);
			if(keyCode==38 && r!=1){//Up
				var z = parseInt(r) - parseInt(1);
				document.getElementById(namez+z).focus();
			}
			
			if((keyCode==40 || keyCode==13) && r!=lastRow){//Down or ENTER
				var z = parseInt(r) + parseInt(1);
				document.getElementById(namez+z).focus();
			}
			
		//}

}


function saveLO(){

	var i = 0;
	//var rowCount = $('#MyTbl tr').length;
   $("input[name='chkLoans[]']:checked").each( function () {
	   i += 1;
      // alert( $(this).val() );
	   var tbl = document.getElementById('MyTbl').getElementsByTagName('tbody')[0];
	  			
	   			var id = $(this).val();
	   			$.ajax({
					url : "th_getloansdetails.php?id=" + id,
					type: "GET",
					dataType: "JSON",
					success: function(data)
					{				
					
					   console.log(data);
                       $.each(data,function(index,item){
						   
						    var lastRow = tbl.rows.length + 1;
							
							var z=tbl.insertRow(-1);

							var a=z.insertCell(-1);
								a.innerHTML ="<div class='col-xs-12 nopadding'><input type='hidden' name='txtcLoanNo"+lastRow+"' id='txtcLoanNo"+lastRow+"' value='"+item.ctranno+"' />"+item.ctranno+"</div>";
							
							var b=z.insertCell(-1);
								b.align = "center";
								b.innerHTML = item.dbegin;
								
							var c=z.insertCell(-1);
								c.align = "center";
								c.innerHTML = item.dend;
								
							var d=z.insertCell(-1);
								d.align = "right";
								d.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtLoTotal"+lastRow+"' id='txtLoTotal"+lastRow+"' value='"+item.namount+"' />"+item.namount+"</div>";
								
							var e=z.insertCell(-1);
								e.align = "right";
								e.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtLoDedct"+lastRow+"' id='txtLoDedct"+lastRow+"' value='"+item.ndeduct+"' />"+item.ndeduct+"</div>";
								
							var f=z.insertCell(-1);
								f.align = "right";
								f.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='hidden' name='txtLoBalnc"+lastRow+"' id='txtLoBalnc"+lastRow+"' value='"+item.nbalance+"' />"+item.nbalance+"</div>";
								
							var g=z.insertCell(-1);
								g.align = "right";
								g.innerHTML = "<div class='col-xs-12 nopadwleft'><input type='text' class='numeric form-control input-xs' name='txtLoApplied"+lastRow+"' id='txtLoApplied"+lastRow+"' style='text-align:right' value='0.00' /></div>";
								
							var h=z.insertCell(-1);
								h.style.padding = "1px";
								h.innerHTML = "<input type='text' class='form-control input-xs' name='txtcLoanAcctNo"+lastRow+"' id='txtcLoanAcctNo"+lastRow+"' value='"+item.cacctno+"' autocomplete=\"false\" />";
								
							var i=z.insertCell(-1);
								i.style.padding = "1px";
								i.innerHTML = "<input type='text' class='form-control input-xs' name='txtcLoanAcctTitle"+lastRow+"' id='txtcLoanAcctTitle"+lastRow+"' value='"+item.ctitle+"' autocomplete=\"false\" />";
								
							var j=z.insertCell(-1);
								j.style.padding = "1px";
								j.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_"+lastRow+"_delete' value='delete' onClick='deleteRow2(this);' />";
										
														   
									$("input.numeric").numeric({decimalPlaces: 4});
									$("input.numeric").on("click focus", function () {
									   $(this).select();
									});
									
									$("input.numeric").on("keyup", function (e) {
										setPosi($(this).attr('name'),e.keyCode,'MyTbl');
										computeGross();
									});
									
									$("#txtcLoanAcctNo"+lastRow+", #txtcLoanAcctTitle"+lastRow).on("click focus", function(event) {
										$(this).select();
									});
									
									$("#txtcLoanAcctNo"+lastRow).on("keyup", function(event) {
										if(event.keyCode == 13 || event.keyCode== 38 || event.keyCode==40){
										
											if(event.keyCode==13 ){	
											var dInput = this.value;
									
												$.ajax({
													type:'post',
													url:'getaccountid.php',
													data: 'c_id='+ $(this).val(),                 
													success: function(value){
														if(value.trim()!=""){
															$("#txtcLoanAcctTitle"+lastRow).val(value.trim());
														}
													}
												});
											}
											
											setPosi("txtcLoanAcctNo"+lastRow,event.keyCode,'MyTbl');
											
										}
										
									});
									
									$("#txtcLoanAcctTitle"+lastRow).typeahead({
								
									items: 10,
									source: function(request, response) {
										$.ajax({
											url: "th_accounts.php",
											dataType: "json",
											data: {
												query: $("#txtcLoanAcctTitle"+lastRow).val()
											},
											success: function (data) {
												response(data);
												
											}
										});
									},
									autoSelect: true,
									displayText: function (item) {
										 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
									},
									highlighter: Object,
									afterSelect: function(item, event) { 
										$("#txtcLoanAcctTitle"+lastRow).val(item.name).change(); 
										$("#txtcLoanAcctNo"+lastRow).val(item.id);
										
										setPosi("txtcLoanAcctTitle"+lastRow,13,'MyTbl');
										
									}
									});
					   
						   
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
	   alert("No Loan is selected!")
   }
   
   $('#myLoMod').modal('hide');
   
}

function addacct(){

	var tbl = document.getElementById('MyTblOthers').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('MyTblOthers').insertRow(-1);
	
	var v=a.insertCell(0);
		v.style.width = "150px";
		v.style.padding = "1px";
	var w=a.insertCell(1);
		w.style.padding = "1px";
	var x=a.insertCell(2);
		x.style.width = "100px";
		x.style.padding = "1px";
	var y=a.insertCell(3);
		y.style.width = "50px";
		y.style.padding = "1px";

	v.innerHTML = "<input type='text' name=\"txtacctno"+lastRow+"\" id=\"txtacctno"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Enter Acct Code...\" style=\"text-transform:uppercase\" autocomplete=\"off\">";
	w.innerHTML = "<input type='text' name=\"txtacctitle"+lastRow+"\" id=\"txtacctitle"+lastRow+"\" class=\"form-control input-sm\" placeholder=\"Search Acct Desc...\" style=\"text-transform:uppercase\" autocomplete=\"off\">";
	x.innerHTML = "<input type='text' name=\"txtnotamt"+lastRow+"\" id=\"txtnotamt"+lastRow+"\" class=\"numeric form-control input-sm\" style=\"text-align:right\" value=\"0.0000\" onkeydown=\"return isNumber(event.keyCode)\" onkeyup=\"compgross();\" required autocomplete=\"off\">";
	y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row3_"+lastRow+"_delete' value='delete' onClick=\"deleteRow3(this);\"/>";

	//alert(lastRow);
		$("#txtacctitle"+lastRow).focus();

		$("input.numeric").numeric();
		$("input.numeric").on("focus", function () {
			$(this).select();
		});
										
		$("#txtacctno"+lastRow).on("keyup", function(event) {
			if(event.keyCode == 13){
				
				var dInput = this.value;
		
					$.ajax({
					type:'post',
					url:'getaccountid.php',
					data: 'c_id='+ $(this).val(),                 
					success: function(value){
						//alert(value);
						if(value.trim()!=""){
							$("#txtacctitle"+lastRow).val(value.trim());
							$("#selacctpaytyp"+lastRow).val(value.trim());
						}
					}
					});
				
			}
		});
		
		$("#txtacctitle"+lastRow).typeahead({
		
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "th_accounts.php",
					dataType: "json",
					data: {
						query: $("#txtacctitle"+lastRow).val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				 return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.id + '</span><br><small>' + item.name + "</small></div>";
			},
			highlighter: Object,
			afterSelect: function(item) { 
				$("#txtacctitle"+lastRow).val(item.name).change(); 
				$("#txtacctno"+lastRow).val(item.id);
				$("#txtdebit"+lastRow).focus();
			}
		});


}

function deleteRow3(r) {
	var tbl = document.getElementById('MyTblOthers').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('MyTblOthers').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempOaccttyp = document.getElementById('selacctortyp' + z);
			var tempOacctno = document.getElementById('txtacctno' + z);
			var tempOctitle = document.getElementById('txtacctitle' + z);
			var tempOamt= document.getElementById('txtnotamt' + z);
			var tempOdelbtn = document.getElementById('row3_'+z+'_delete');
			
			var x = z-1;
			tempOaccttyp.id = "selacctortyp" + x;
			tempOaccttyp.name = "selacctortyp" + x;
			tempOacctno.id = "txtacctno" + x;
			tempOacctno.name = "txtacctno" + x;
			tempOctitle.id = "txtacctitle" + x;
			tempOctitle.name = "txtacctitle" + x;
			tempOamt.id = "txtnotamt" + x;
			tempOamt.name = "txtnotamt" + x;
			tempOdelbtn.id = "row3_"+x+"_delete";
			tempOdelbtn.name = "row3_"+x+"_delete";
			
		}

computeGross();

}

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
