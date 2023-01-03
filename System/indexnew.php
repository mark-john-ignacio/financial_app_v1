<?php

if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "System_Set";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');


?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Coop Financials</title>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
   <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
   <link rel="stylesheet" type="text/css" href="../Bootstrap/css/DigiClock.css"> 
    
<script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
<script src="../Bootstrap/js/jquery.numeric.js"></script>
<script src="../Bootstrap/js/jquery.inputlimiter.min.js"></script>

<script src="../Bootstrap/js/bootstrap.js"></script>
<script src="../Bootstrap/js/moment.js"></script>
<script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
</head>

<body style="padding:5px">
	<fieldset>
    	<legend>System Setup</legend>	


            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#home">Company Info</a></li>
              <li><a data-toggle="tab" href="#param">Parameters</a></li>
              <li><a data-toggle="tab" href="#sales">Sales &amp; Delivery</a></li>
              <li><a data-toggle="tab" href="#acct">Accounting</a></li>
              <li><a data-toggle="tab" href="#rpts">Reports</a></li>
            </ul>
            
            <div class="tab-content col-lg-12 nopadwtop2x">
			
           
			<!-- COMPANY INFO -->
              <div id="home" class="tab-pane fade in active">
              
                   <div class="col-xs-12 nopadwdown">   
                        <div style="display:inline" class="col-xs-3">
                        <button class="btn btn-xs btn-success" name="btncompsave" id="btncompsave"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Company Details</button>
   						</div>
                        
   						<div style="display:inline" class="col-xs-5"> 
                        	<div class="alert alert-danger nopadding" id="CompanyAlertMsg">
                              
                            </div>
                         	<div class="alert alert-success nopadding" id="CompanyAlertDone">
                              
                            </div>
                       </div>                 
 					</div>
                    <div class="col-xs-12 nopadwtop">
				<table width="100%" border="0" cellpadding="0">
                      <tr>
                        <td width="150" rowspan="3" align="center">
                        <?php 
                            $imgsrc = "../images/COMPLOGO.png";
                        ?>
                        <img src="<?php echo $imgsrc;?>" width="100" height="100">
                        
                        </td>
                        <td width="150"><b>Company Name:</b></td>
                        <td style="padding:2px"><div class="col-xs-7"><input type="text" name="txtcompanycom" id="txtcompanycom" class="form-control input-sm" placeholder="Company Name..." maxlength="90"></div></td>
                      </tr>
                      <tr>
                        <td><b>Description:</b></td>
                        <td style="padding:2px"><div class="col-xs-7">
                          <input type="text" name="txtcompanydesc" id="txtcompanydesc" class="form-control input-sm" placeholder="Company Description..." maxlength="90" >
                        </div></td>
                      </tr>
                      <tr>
                        <td><b>Address:</b></td>
                        <td style="padding:2px"><div class="col-xs-7">
                          <input type="text" name="txtcompanyadd" id="txtcompanyadd" class="form-control input-sm" placeholder="Address..." maxlength="90">
                        </div></td>
                      </tr>
                      <tr>
                        <td align="center"><input type="button" class="btn btn-primary btn-sm" name="btnupload" id="btnupload" value="Upload Logo" onClick="popwin();"></td>
                        <td><b>Tin No.:</b></td>
                        <td style="padding:2px">
                        <div class="col-xs-7">
                          <input type="text" name="txtcompanytin" id="txtcompanytin" class="form-control input-sm" placeholder="TIN No..." maxlength="50">
                        </div></td>
                      </tr>
                       <tr>
                        <td align="center">&nbsp;</td>
                        <td><b>VAT Exempt:</b></td>
                        <td style="padding:2px">
                        <div class="col-xs-7">
                          <select class="form-control input-xs" name="selcompanyvat" id="selcompanyvat">
                          </select>
                        </div></td>
                      </tr>
                   </table>
				</div>

              </div>
              
              <!-- SALES SETUP -->
              <div id="sales" class="tab-pane fade in">
 
<table width="100%" border="0" cellpadding="0">
   <tr>
    <td><b>Inventory Checking:</b></td>
    <td style="padding:2px">
          <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='INVPOST'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>

    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selcut" id	="selcut" onChange="setinvcheck(this.value)">
        	<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> ALLOW ALL ITEMS </option>
            <option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> CHECK AVAILABLE </option>
        </select>
    </div>
    <div style="display:inline" id="divnvcheck">
    	
    </div>
    
    </td>
  </tr>

  <tr>
    <td><b>Auto Post Upon Saving:</b></td>
    <td style="padding:2px">
          <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSPOST'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>

    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selcut" id	="selcut" onChange="setautopost(this.value)">
        	<option value="1" <?php if ($nvalue==1) { echo "selected"; } ?>> YES </option>
            <option value="0" <?php if ($nvalue==0) { echo "selected"; } ?>> NO </option>
        </select>
    </div>
    <div style="display:inline" id="divautopost">
    	
    </div>
    
    </td>
  </tr>
  <tr>
    <td width="250"><b>POS CREDIT LIMIT RESET :</b></td>
    <td style="padding:2px">
        <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='POSCLMT'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>
        
    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selcut" id	="selcut" onChange="setval(this.value)">
        	<option value="Daily" <?php if ($nvalue=="Daily") { echo "selected"; } ?>> DAILY </option>
            <option value="Cutoff" <?php if ($nvalue=="Cutoff") { echo "selected"; } ?>> CUTOFF POSTING </option>
        </select>
    </div>
    <div style="display:inline" id="divmsg">
    	
    </div>
    </td>
  </tr>
<?php

if($nvalue=="Cutoff"){
	$styleval = "style=\"display:table-row-group;\"";
}
else{
	$styleval = "style=\"display:none;\"";
}

?>
<tbody <?php echo $styleval; ?> id="rowcut">
  <tr>
    <td>    <button type="submit" class="btn btn-danger btn-sm" id="btnsales" onClick="setcutdate();">
    	<span class="glyphicon glyphicon-remove"></span> <b>CLOSE CURRENT CUTOFF</b>
    </button>
</td>
    <td style="padding:5px">
    <div style="display:inline" id="divmsg2">
    <?php
	function validateDate($date, $format = 'Y-m-d H:i:s')
		{
			$d = DateTime::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}


     $result = mysqli_query($con,"SELECT DATE_FORMAT(ddatefrom,'%m/%d/%Y') as ddatefrom, DATE_FORMAT(ddateto,'%m/%d/%Y') as ddateto FROM `pos_cutoff` Order By postdate Desc"); 
	 
	 if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
 $c_datefr = $all_course_data['ddatefrom']; 
 $c_dateto = $all_course_data['ddateto']; 

	// $c_datefr = "06/01/2016"; 
	// $c_dateto = "06/15/2016"; 
	 
		
	 echo $c_datefr." TO ".$c_dateto;
	 }
	 else{
		 echo "";
	 }
	 
	// $date1 = str_replace('-', '/', $c_dateto);
	 $c_datenxttfr = date('m/d/Y',strtotime($c_dateto . "+1 days"));
	 $c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+15 days"));
	 
	 
if(date('d',strtotime($c_datenxttfr))=="01"){
	$c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+14 days"));
}
else{
	 $m1 = date('m',strtotime($c_datenxttfr));
	 $m2 = date('m',strtotime($c_datenxttto));

	//echo "<br>".$m1."-".$m2."<br>";
	if ($m1!=$m2){
		$c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+14 days"));
		
	        $m2 = date('m',strtotime($c_datenxttto));


			if ($m1!=$m2){
				$c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+13 days"));
				
					$m2 = date('m',strtotime($c_datenxttto));


					if ($m1!=$m2){
						$c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+12 days"));
						
							$m2 = date('m',strtotime($c_datenxttto));

							if ($m1!=$m2){
								$c_datenxttto = date('m/d/Y',strtotime($c_datenxttfr . "+11 days"));
								
									 $m2 = date('m',strtotime($c_datenxttto));
							}

					}
			}
	}
	
	//echo $m1."-".$m2;
}
	?>
    </div>
    </td>
  </tr>
  <tr>
    <td>
    <button type="submit" class="btn btn-success btn-sm" id="btnsales" onClick="setcutdate();">
    	<span class="glyphicon glyphicon-ok"></span> <b>START NEW CUTOFF</b>
    </button>
</td>
    <td style="padding:2px">
    <div class="col-xs-5">
    <div class="control-group">
        <div class="controls form-inline">
     
		<input type='text' class="datepick form-control input-sm" id="date1" name="date1" value="<?php echo $c_datenxttfr; ?>"/>
     
            <label for="inputValue">TO</label>
     
		<input type='text' class="datepick form-control input-sm" id="date2" name="date2" value="<?php echo $c_datenxttto; ?>"/>
     
        </div>
    </div>
    </div>
    
    </td>
  </tr>
</tbody>


  <tr>
    <td colspan="2">&nbsp;</td>
    </tr>
  <tr>
    <td><b>Above Credit Limit:</b></td>
    <td style="padding:2px"> 
         <?php
     $result = mysqli_query($con,"SELECT * FROM `parameters` WHERE ccode='ABOVECL'"); 

	  if (mysqli_num_rows($result)!=0) {
	 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
		 $nvalue = $all_course_data['cvalue']; 
		
	 }
	 else{
		 $nvalue = "";
	 }

	 
		?>

    
    <div class="col-xs-3">
    	<select class="form-control input-sm selectpicker" name="selallow" id="selallow" onChange="setCLAllow(this.value)">
        	<option value="Deny" <?php if ($nvalue=="Deny") { echo "selected"; } ?>> DON'T ALLOW </option>
            <option value="Allow" <?php if ($nvalue=="Allow") { echo "selected"; } ?>> ALLOW PAYMENT </option>
        </select>
    </div>
    <div style="display:inline" id="divmsgallow">
    	
    </div>

    
    </td>
  </tr>
  <tr>
    <td><b>Default Credit Limit:</b></td>
    <td style="padding:2px">
    <div class="col-xs-5">
    <input type="text" class="form-control input-sm" id="txtclimit" name="txtclimit" tabindex="11" placeholder="Enter Credit Limit..." required>
    </div>
    </td>
  </tr>
   <tr>
    <td><b>Default Sales Account:</b></td>
    <td style="padding:2px"> 
    <div class="col-xs-5">
    <input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="11" placeholder="Enter Description...">
    </div> &nbsp;&nbsp;
    <input type="text" id="txtsalesacctD" name="txtsalesacctD" style="border:none; height:30px" readonly>
    </td>
  </tr>
 
  </table>

 
              
              </div>
               
               
              <div id="param" class="tab-pane fade in">
             <p data-toggle="collapse" data-target="#itmgrpcollapse"><i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Items Groupings</b></u> <i>**Note: Press ENTER after you enter your description to save...</i></p>
              
              <div class="collapse in" id="itmgrpcollapse">
                <div class="col-xs-12">
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 1</b>
                        <div id="divcGroup1" style="display:inline; padding-left:5px"></div>
                    </div>
                    
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup1" name="txtGroup1" placeholder="Enter Description..." data-content="cGroup1">
                    </div>
             
             
                    <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    </div>
                    
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 6</b>
                        <div id="divcGroup6" style="display:inline; padding-left:5px"></div>
                    </div>
            
            
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup6" name="txtGroup6" tabindex="11" placeholder="Enter Description..." data-content="cGroup6">
                    </div>
            
                </div>
            
                <div class="col-xs-12">
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 2</b>
                        <div id="divcGroup2" style="display:inline; padding-left:5px"></div>
                    </div>
                    
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup2" name="txtGroup2" tabindex="11" placeholder="Enter Description..." data-content="cGroup2">
                    </div>
             
             
                    <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    </div>
                    
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 7</b>
                        <div id="divcGroup7" style="display:inline; padding-left:5px"></div>
                    </div>
            
            
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup7" name="txtGroup7" tabindex="11" placeholder="Enter Description..." data-content="cGroup7">
                    </div>
            
                </div>
            
                <div class="col-xs-12">
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 3</b>
                        <div id="divcGroup3" style="display:inline; padding-left:5px"></div>
                    </div>
                    
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup3" name="txtGroup3" tabindex="11" placeholder="Enter Description..." data-content="cGroup3">
                    </div>
             
             
                    <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    </div>
                    
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 8</b>
                        <div id="divcGroup8" style="display:inline; padding-left:5px"></div>
                    </div>
            
            
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup8" name="txtGroup8" tabindex="11" placeholder="Enter Description..." data-content="cGroup8">
                    </div>
            
                </div>
            
                <div class="col-xs-12">
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 4</b>
                        <div id="divcGroup4" style="display:inline; padding-left:5px"></div>
                    </div>
                    
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup4" name="txtGroup4" tabindex="11" placeholder="Enter Description..." data-content="cGroup4">
                    </div>
             
             
                    <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    </div>
                    
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 9</b>
                        <div id="divcGroup9" style="display:inline; padding-left:5px"></div>
                    </div>
            
            
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup9" name="txtGroup9" tabindex="11" placeholder="Enter Description..."data-content="cGroup9">
                    </div>
            
                </div>
            
                <div class="col-xs-12">
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 5</b>
                        <div id="divcGroup5" style="display:inline; padding-left:5px"></div>
                    </div>
                    
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup5" name="txtGroup5" tabindex="11" placeholder="Enter Description..." data-content="cGroup5">
                    </div>
             
             
                    <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    </div>
                    
                    <div class="col-xs-2 nopadwtop">
                        <b>Group 10</b>
                        <div id="divcGroup10" style="display:inline; padding-left:5px"></div>
                    </div>
            
            
                    <div class="col-xs-3 nopadwtop">
                        <input type="text" class="cgroup form-control input-sm" id="txtGroup10" name="txtGroup10" tabindex="11" placeholder="Enter Description..." data-content="cGroup10">
                    </div>
            
                </div>
              </div>
              
            
            <p data-toggle="collapse" data-target="#taxcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Items VAT Codes</b></u></p>
              <div class="collapse" id="taxcodecollapse">
                   <div class="col-xs-12 nopadwdown">   
                        <div style="display:inline" class="col-xs-3">
                        <button class="btn btn-xs btn-primary" name="btnaddtax" id="btnaddtax"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
                        <button class="btn btn-xs btn-success" name="btntax" id="btntax"><i class="fa fa-save"></i>&nbsp; &nbsp;Save VAT Codes</button>
   						</div>
                        
   						<div style="display:inline" class="col-xs-5"> 
                        	<div class="alert alert-danger nopadding" id="TAXAlertMsg">
                              
                            </div>
                         	<div class="alert alert-success nopadding" id="TAXAlertDone">
                              
                            </div>
                       </div>                 
 					</div>
                	<div class="col-xs-12 nopadding">
                        <div class="col-xs-2">
                           <b>Tax Code</b> 
                        </div>
                        
                        <div class="col-xs-4">
                          <b>Description</b>  
                        </div>
                        
                        <div class="col-xs-2">
                          <b>Rate %</b> 
                        </div>
  
                        <div class="col-xs-3">
                          <b>Status</b> 
                        </div>
                      
                    </div>

                    <div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblTax">
                      
                    </div>
                    
                 </div>
                 
                 <p data-toggle="collapse" data-target="#vatcodecollapse"> <i class="fa fa-caret-down" style="cursor: pointer"></i>&nbsp;&nbsp;<u><b>Customers VAT Exemptions Code</b></u></p>
                 <div class="collapse" id="vatcodecollapse">
                   <div class="col-xs-12 nopadwdown">   
                        <div style="display:inline" class="col-xs-3">
                        <button class="btn btn-xs btn-primary" name="btnaddvat" id="btnaddvat"><i class="fa fa-plus"></i>&nbsp; &nbsp;Add</button>
                        <button class="btn btn-xs btn-success" name="btnvat" id="btnvat"><i class="fa fa-save"></i>&nbsp; &nbsp;Save Vat Exempt Codes</button>
   						</div>
                        
   						<div style="display:inline" class="col-xs-5"> 
                        	<div class="alert alert-danger nopadding" id="VATAlertMsg">
                              
                            </div>
                         	<div class="alert alert-success nopadding" id="VATAlertDone">
                              
                            </div>
                       </div>                 
 					</div>
                	<div class="col-xs-12 nopadding">
                        <div class="col-xs-1">
                           <b>Code</b> 
                        </div>
                        
                        <div class="col-xs-4">
                          <b>Description</b>  
                        </div>
                        
                        <div class="col-xs-3">
                          <b>Remarks</b> 
                        </div>
 
                        <div class="col-xs-1">
                          <b>Compute</b> 
                        </div>
                         
                        <div class="col-xs-2">
                          <b>Status</b> 
                        </div>
                      
                    </div>

                    <div style="height:20vh; border:1px solid #CCC" class="col-lg-12 nopadding pre-scrollable" id="TblVAT">
                      
                    </div>
                 
                 </div>

             
              </div> 
 
               
              <div id="acct" class="tab-pane fade in">
              acct
              </div>
              
              <div id="rpts" class="tab-pane fade in">
              rpts
              </div> 
              
            </div>
            
     </fieldset>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(e) {
	loadcompany();
	
	loadgroups();
	
	loadtax();
	
	loadvat();
});

$(function() {              
           // Bootstrap DateTimePicker v4
	        $('.datepick').datetimepicker({
                 format: 'MM/DD/YYYY'
            });
			
			$('#popoverData1, #popoverData2').popover({ trigger: "hover" });
			$("#TAXAlertMsg").hide();
			$("#TAXAlertDone").hide();
			$("#VATAlertMsg").hide();
			$("#VATAlertDone").hide();
			$("#CompanyAlertMsg").hide();
			$("#CompanyAlertDone").hide();



				  	$("#txtsalesacct").typeahead({
						autoSelect: true,
						source: function(request, response) {
							$.ajax({
								url: "th_accounts.php",
								dataType: "json",
								data: {
									query: $("#txtsalesacct").val()
								},
								success: function (data) {
									response(data);
								}
							});
						},
						displayText: function (item) {
							return item.id + " : " + item.name;
						},
						highlighter: Object,
						afterSelect: function(item) { 					
										
							$('#txtsalesacct'+rowCount).val(item.name).change(); 
							$('#txtsalesacctD'+rowCount).val(item.id); 
							
						}
					});


	$(".cgroup").on("keyup", function(e) {
	   if(e.keyCode==13){
		   var x = $(this).val();
		   var y = $(this).attr("data-content") 
		   var nme = $(this).attr("name");
		   var r = nme.replace( /^\D+/g, '');
		   
			if(r<=10){
				r = parseInt(r) + 1;
			}
		   
			$.ajax ({
            url: "th_updategroup.php",
            data: { val: x,  nme: y},
            success: function( result ) {
				
					if(result.trim()=="True"){
						$("#div"+y).html("<i class=\"fa fa-check\" style=\"color:green;\"></i>");
						
						$("#txtGroup"+r).focus();
					}
					else{
						alert(result);
					}
            }
    		});

	   }
	});
	
	$("#btnaddtax").on("click", function(){
		var xy = 1;
			$.ajax ({
				url: "th_getlasttax.php",
				async: false,
				success: function( data ) {
					if(data!="False"){
						xy = parseInt(data) + 1;
						
					}
				}
			
			});
						var divhead = "<div class=\"taxdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
						var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxcode[]\" id=\"txtctaxcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Code...\" /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctaxdesc[]\" id=\"txtctaxdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" /></div>";
						var divrate = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxrate[]\" id=\"txtctaxrate"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Rate...\" /></div>";                                                 
						var divstat = "<div class=\"col-xs-3\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
						var divend = "</div>";

						$("#TblTax").append(divhead + divcode + divdesc + divrate + divstat + divend);

							$("#txtctaxcode"+xy).on("keyup", function() {
								var valz = $(this).val();

								$.post('th_chktaxcode.php',{'q':valz },function( data ){ //send value to post request in ajax to the php page
									if(data!="True"){ 
										$("#TAXAlertMsg").html("<b>Error: </b>"+ data);
										$("#TAXAlertMsg").show();

										$("#TAXAlertDone").html("");
										$("#TAXAlertDone").hide();
									}
									else {
										$("#TAXAlertMsg").html("");
										$("#TAXAlertMsg").hide();

										$("#TAXAlertDone").html("");
										$("#TAXAlertDone").hide();
									}
								});
							});
							$("#txtctaxcode"+xy).on("blur", function() {
								 if ($("#TAXAlertMsg").text().length > 0) {
									 
									 $("#txtctaxcode"+xy).val("").change();
									 $("#txtctaxcode"+xy).focus();
									 
								 }
							});
						
						
		
	});
	
	
	$("#btntax").on("click", function() {
		var isOk = "YES";
		
		$('.taxdetail').each(function(i, obj) {
			
			divid = $(this).attr("id");
			varcode = $(this).find('input[name="txtctaxcode[]"]').val();
			vardesc = $(this).find('input[name="txtctaxdesc[]"]').val();
			varrate = $(this).find('input[name="txtctaxrate[]"]').val();
			

			$.ajax ({
				url: "th_savetax.php",
				data: { code: varcode,  desc: vardesc, rate: varrate },
				async: false,
				success: function( data ) {
					if(data!="True"){
						isOk = data;
					}
				}
			
			});
						
			
		});	
		
			if(isOk == "YES"){
				$("#TblTax").html("");
				loadtax();
				
				$("#TAXAlertDone").html("<b>SUCCESS: </b> Tax table successfully saved!");
				$("#TAXAlertDone").show();

						$("#TAXAlertMsg").html("");
						$("#TAXAlertMsg").hide();
				
			}
			else{
				$("#TAXAlertMsg").html("<b>Error Saving:</b>"+isOk);
				$("#TAXAlertMsg").show();

						$("#TAXAlertDone").html("");
						$("#TAXAlertDone").hide();

			}
		
	});	
	
	
	$("#btnaddvat").on("click", function(){
		var xy = 1;
			$.ajax ({
				url: "th_getlastvat.php",
				async: false,
				success: function( data ) {
					if(data!="False"){
						xy = parseInt(data) + 1;
						
					}
				}
			
			});
						var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+xy+"\">";
						var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Code...\" /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Description...\" /></div>";
						var divrem = "<div class=\"col-xs-3\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+xy+"\" data-citmno=\""+xy+"\" class=\"form-control input-xs\" placeholder=\"Enter Remarks...\" /></div>";                                                 
						var divcomp = "<div class=\"col-xs-1\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+xy+"\" ><option value=\"1\">YES</option><option value=\"0\">NO</option></select></div>";                                                 
						var divstat = "<div class=\"col-xs-2\">&nbsp;<span class='label label-success'>Active</span></div>";                                                 
						var divend = "</div>";

						$("#TblVAT").append(divhead + divcode + divdesc + divrem + divcomp + divstat + divend);

							$("#txtcvatcode"+xy).on("keyup", function() {
								var valz = $(this).val();

								$.post('th_chkvatcode.php',{'q':valz },function( data ){ //send value to post request in ajax to the php page
									if(data.trim()!="True"){ 
										$("#VATAlertMsg").html("<b>Error: </b>"+ data.trim());
										$("#VATAlertMsg").show();

										$("#VATAlertDone").html("");
										$("#VATAlertDone").hide();
									}
									else {
										$("#VATAlertMsg").html("");
										$("#VATAlertMsg").hide();

										$("#VATAlertDone").html("");
										$("#VATAlertDone").hide();
									}
								});
							});
							$("#txtcvatcode"+xy).on("blur", function() {
								 if ($("#VATAlertMsg").text().length > 0) {
									 
									 $("#txtcvatcode"+xy).val("").change();
									 $("#txtcvatcode"+xy).focus();
									 
								 }
							});
						
						
		
	});
	
	
	$("#btnvat").on("click", function() {
		var isOk = "YES";

		$('.vatdetail').each(function(i, obj) {
			//alert("una");
			divid = $(this).attr("id");
			varcode = $(this).find('input[name="txtcvatcode[]"]').val();
			//alert(varcode);
			vardesc = $(this).find('input[name="txtcvatdesc[]"]').val();
			//alert(vardesc);
			varrem = $(this).find('input[name="txtcvatrem[]"]').val();
			//alert(varrem);
			varcomp = $(this).find('select[name="selcomp[]"]').val();
			//alert(varcomp);

			$.ajax ({
				url: "th_savevat.php",
				data: { code: varcode,  desc: vardesc, rem: varrem, lcomp: varcomp },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						isOk = data;
					}
				}
			
			});
						
			
		});	
		
			if(isOk == "YES"){
				$("#TblVAT").html("");
				loadvat();
				
				$("#VATAlertDone").html("<b>SUCCESS: </b> VAT Exempt table successfully saved!");
				$("#VATAlertDone").show();

						$("#VATAlertMsg").html("");
						$("#VATAlertMsg").hide();
				
			}
			else{
				$("#VATAlertMsg").html("<b>Error Saving:</b>"+isOk);
				$("#VATAlertMsg").show();

						$("#VATAlertDone").html("");
						$("#VATAlertDone").hide();

			}
		
	});	
	
	$("#btncompsave").on("click", function() {
		var nme = $("#txtcompanycom").val();
		var desc = $("#txtcompanydesc").val();
		var add = $("#txtcompanyadd").val();
		var tin = $("#txtcompanytin").val();
		var vatz = $("#selcompanyvat").val();
		
			$.ajax ({
				url: "th_savecompany.php",
				data: { nme: nme,  desc: desc, add: add, tin: tin, vatz: vatz },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#CompanyAlertMsg").html("<b>Error Saving:</b>"+data.trim());
						$("#CompanyAlertMsg").show();
		
								$("#CompanyAlertDone").html("");
								$("#CompanyAlertDone").hide();
					}
					else{
						$("#CompanyAlertMsg").html("");
						$("#CompanyAlertMsg").hide();
		
								$("#CompanyAlertDone").html("<b>SUCCESS: </b> Company details successfully updated!");
								$("#CompanyAlertDone").show();
					}
				},
				error: function (req, status, err) {
						console.log('Something went wrong', status, err)
						alert('Something went wrong\n'+status+"\n"+err);	
						$("#CompanyAlertMsg").html("<b>Something went wrong: </b>"+status+ " " + err);
						$("#CompanyAlertMsg").show();
				}
			
			});

	});

	   
});

function setval(valz){
	
if(valz!=""){

	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
			document.getElementById("divmsg").innerHTML=xmlhttp.responseText;
			
			if(xmlhttp.responseText=="POS Credit Limit Reset changed to Cutoff Posting"){
				document.getElementById("rowcut").style.display = "table-row-group";
			}
			else{
				document.getElementById("rowcut").style.display = "none";
			}
	}
	}
	xmlhttp.open("GET","put_poscutval.php?code="+valz,true);
	xmlhttp.send();
}
	
}

function loadcompany(){
		$.ajax ({
            url: "th_loadcompany.php",
			dataType: 'json',
			async:false,
            success: function( result ) {

               console.log(result);
			   $.each(result,function(index,item){
				   	$("#txtcompanycom").val(item.cname);
					$("#txtcompanydesc").val(item.cdesc);
					$("#txtcompanyadd").val(item.cadd);
					$("#txtcompanytin").val(item.ctin);
					var vatcompcode = item.lvat;
					
							    $.ajax ({
								url: "th_loadvat.php",
								dataType: 'json',
								async:false,
								success: function( result ) {
								   var isselctd = "";
								   
								   console.log(result);
								   $.each(result,function(index,item){
									   
									   if(item.cvatcode==vatcompcode){
										isselctd = "selected";
									   }else{
									   	isselctd = "";
									   }
									   $("#selcompanyvat").append("<option value=\""+item.cvatcode+"\" "+isselctd+">"+item.cvatdesc+"</option>");
								   });
								}
								});

				   
			   });
			}
		});
			
}

function loadgroups(){
	$('.cgroup').each(function(i, obj) {
		   var y = $(this).attr("data-content"); 
		   var nme = $(this).attr("name");
		   var r = nme.replace( /^\D+/g, '');
		   		   
		
		    $.ajax ({
            url: "th_loadgroup.php",
            data: { nme: y},
            success: function( result ) {
				
					if(result.trim()!="False"){						
						$("#txtGroup"+r).val(result);
					}
					else{
						$("#txtGroup"+r).val("");
					}
            }
    		});

	});
}

function loadtax(){
	
		    $.ajax ({
            url: "th_loadtax.php",
			dataType: 'json',
			async:false,
            success: function( result ) {

               console.log(result);
			   $.each(result,function(index,item){

					if(item.ctaxcode!=""){	
						 if(item.cstat == "ACTIVE"){ 
							var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setTaxStat('"+item.ctaxcode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						 } else{
							var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setTaxStat('"+item.ctaxcode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						 }
										
						var divhead = "<div class=\"taxdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
						var divcode = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxcode[]\" id=\"txtctaxcode"+item.nident+"\" value=\""+item.ctaxcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtctaxdesc[]\" id=\"txtctaxdesc"+item.nident+"\" value=\""+item.ctaxdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>";						
						var divrate = "<div class=\"col-xs-2\"><input type=\"text\" name=\"txtctaxrate[]\" id=\"txtctaxrate"+item.nident+"\" value=\""+item.nrate+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" /></div>"; 
						var divstat = "<div class=\"col-xs-3\">&nbsp;"+spanstat+"</div>";                                               
						var divend = "</div>";
						
							
						$("#TblTax").append(divhead + divcode + divdesc + divrate + divstat + divend);
						//$("#TblTax").html("Hello String");

					}
					
			   });
            }
    		});
	
	
}

function loadvat(){
	
		    $.ajax ({
            url: "th_loadvat.php",
			dataType: 'json',
			async:false,
            success: function( result ) {

               console.log(result);
			   $.each(result,function(index,item){

					if(item.cvatcode!=""){	
						 if(item.cstat == "ACTIVE"){ 
							var spanstat = "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setVATStat('"+item.cvatcode+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
						 } else{
							var spanstat = "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setVATStat('"+item.cvatcode+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
						 }

						 if(item.lcomp == 1){ 
							var isYes = "selected";
							var isNo = "";
						 } else{
							var isNo = "selected";
							var isYes = "";
						 }
										
						var divhead = "<div class=\"vatdetail col-xs-12 nopadwtop\" id=\""+item.nident+"\">";
						var divcode = "<div class=\"col-xs-1\"><input type=\"text\" name=\"txtcvatcode[]\" id=\"txtcvatcode"+item.nident+"\" value=\""+item.cvatcode+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\" readonly /></div>";
						var divdesc = "<div class=\"col-xs-4\"><input type=\"text\" name=\"txtcvatdesc[]\" id=\"txtcvatdesc"+item.nident+"\" value=\""+item.cvatdesc+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Description...\" /></div>";						
						var divrem = "<div class=\"col-xs-3\"><input type=\"text\" name=\"txtcvatrem[]\" id=\"txtcvatrem"+item.nident+"\" value=\""+item.nrem+"\" data-citmno=\""+item.nident+"\" class=\"form-control input-xs\"  placeholder=\"Enter Remarks...\" /></div>"; 
						var divcomp = "<div class=\"col-xs-1\"><select class=\"form-control input-xs\" name=\"selcomp[]\" id=\"selcomp"+item.nident+"\"><option value=\"1\" "+isYes+">YES</option><option value=\"0\" "+isNo+">NO</option></select></div>";                                                 
						var divstat = "<div class=\"col-xs-2\">&nbsp;"+spanstat+"</div>";                                               
						var divend = "</div>";
						
							
						$("#TblVAT").append(divhead + divcode + divdesc + divrem + divcomp + divstat + divend);
						//$("#TblTax").html("Hello String");

					}
					
			   });
            }
    		});
	
	
}


function setTaxStat(code,stat){
			$.ajax ({
				url: "th_settaxstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#TAXAlertMsg").html("<b>Error: </b>"+ data);
						$("#TAXAlertMsg").show();
						
						$("#TAXAlertDone").html("");
						$("#TAXAlertDone").hide();

					}
					else{
						$("#TblTax").html("");
						loadtax();
						
						$("#TAXAlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);
						$("#TAXAlertDone").show();

						$("#TAXAlertMsg").html("");
						$("#TAXAlertMsg").hide();

					}
				}
			
			});

}


function setVATStat(code,stat){
			$.ajax ({
				url: "th_setvatstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#VATAlertMsg").html("<b>Error: </b>"+ data);
						$("#VATAlertMsg").show();
						
						$("#VATAlertDone").html("");
						$("#VATAlertDone").hide();

					}
					else{
						$("#TblVAT").html("");
						loadvat();
						
						$("#VATAlertDone").html("<b>SUCCESS: </b> "+code+" status changed to "+ stat);
						$("#VATAlertDone").show();

						$("#VATAlertMsg").html("");
						$("#VATAlertMsg").hide();

					}
				}
			
			});

}


</script>