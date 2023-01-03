<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Customers_edit.php";

include('../Connection/connection_string.php');
include('../include/denied.php');
include('../include/access.php');
?>
              	<?php
			if(isset($_REQUEST["txtcitemno"])){
				$citemno = $_REQUEST['txtcitemno'];
			}else{
				$citemno = $_REQUEST['txtccode'];
			}
				
				
				if($citemno <> ""){
					
					$sql = "select A.*, A1.cacctdesc as salescode, B.cname as cparentname from customers A LEFT JOIN accounts A1 ON (A.cacctcodesales = A1.cacctno) LEFT JOIN customers B ON (A.cparentcode = B.ccode) where A.ccode='$citemno'";
				}else{
					header('Items.php');
					die();
				}
				
				$sqlhead=mysqli_query($con,$sql);
				
					if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
					} 
					
				if (mysqli_num_rows($sqlhead)!=0) {
					while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

						$cCustCode = $row['ccode'];
						$cCustName = $row['cname'];
						$cCustTyp = $row['ccustomertype'];
						$cCustCls = $row['ccustomerclass'];
						$Status = $row['cstatus'];
						$CreditLimit = $row['nlimit'];
						$CreditLimitCR = $row['ncrlimit'];
						$Priceversion = $row['cpricever'];
						$VatType = $row['cvattype'];
						$Terms = $row['cterms'];
						$Tin = $row['ctin'];

						$HouseNo = $row['chouseno'];
						$City = $row['ccity'];
						$State = $row['cstate'];
						$Country = $row['ccountry'];
						$ZIP = $row['czip'];
						
						$cParentCode = $row['cparentcode'];
						$cParentName = $row['cparentname'];
						
						$AcctCodeType = $row['cacctcodetype'];
						$GroceryID = $row['cacctcodesales'];
						$GroceryDesc = $row['salescode'];
						
							if($AcctCodeType=="single"){
								$singlestat = "required";
								$multistat = "";
							}else{
								$singlestat = "";
								$multistat = "required";
							}


					}
				}
				?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Coop Financials</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 


</head>

<body style="padding:5px; height:700px">

<form name="frmCust" id="frmCust" method="post" enctype="multipart/form-data" >
	<fieldset>
    	<legend>Customer Details  (<b>Status: <?php echo $Status; ?></b>)</legend>
<table width="100%" border="1">
  <tr>
    <td width="150" height="150" rowspan="5"  style="vertical-align:top">
    <?php 
	if(!file_exists("../imgcust/".$citemno.".jpg") and !file_exists("../imgsupp/".$citemno.".jpeg") and !file_exists("../imgsupp/".$citemno.".png")){
		$imgsrc = "../images/emp.jpg";
	}
	else{
		if(file_exists("../imgcust/".$citemno.".jpg")){
			$imgsrc = "../imgcust/".$citemno.".jpg";
		}

		if(file_exists("../imgcust/".$citemno.".jpeg")){
			$imgsrc = "../imgcust/".$citemno.".jpeg";
		}

		if(file_exists("../imgcust/".$citemno.".png")){
			$imgsrc = "../imgcust/".$citemno.".png";
		}
	}
	?>

    <img src="<?php echo $imgsrc;?>" width="145" height="145" id="previewing">
    </td>
    <td width="150">&nbsp;<b>Customer Code</b></td>
    <td style="padding:2px"><div class="col-xs-4 nopadding"><input type="text" class="form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Customer Code.." required value="<?php echo $cCustCode;?>" autocomplete="off" onKeyUp="chkSIEnter(event.keyCode,'frmCust');" /></div><span id="user-result"></span></td>
  </tr>
  <tr>
    <td>&nbsp;<b>Customer Name</b></td>
    <td style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Customer Name.." required  value="<?php echo $cCustName;?>" autocomplete="off" /></div></td>
  </tr>
  <tr>
   <td>&nbsp;<b>Tin No.</b></td>
   <td style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtTinNo" name="txtTinNo" tabindex="2" placeholder="Input Tin No.." required autocomplete="off" /></div></td>
  </tr>

  <tr>
    <td style="vertical-align:top">
    <div class="col-xs-6 nopadwtop2x">
            <label class="btn btn-warning btn-xs">
                Browse Image&hellip; <input type="file" name="file" id="file" style="display: none;">
            </label>
    </div>
    </td>
    <td colspan="2" style="padding:2px"></td>
  </tr>

  <tr>
    <td><b>Address</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="3" /></div></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="4" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="5" />
                    </div></div></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="6" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="7" />
                    </div></div></td>
  </tr>
  
   <tr>
    <td colspan="3" style="vertical-align:top"><div class="err" id="add_err"></div></td>
    </tr>
  <tr>
    <td colspan="3" style="vertical-align:top">&nbsp;
		<div class="err" id="add_err"></div>
    </td>
  </tr>
</table>

<p>&nbsp;</p>
  <ul class="nav nav-tabs">
    <li class="active"><a href="#home">General</a></li>
    <li><a href="#menu1">Contact &amp; Address</a></li>
    <li><a href="#menu2">Groupings</a></li>
    <li><a href="#menu3">Accounting</a></li>
  </ul>

<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
    <div class="tab-content">
    
         <div id="home" class="tab-pane fade in active" style="padding-left:30px">
             <p>

				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Type</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-7 nopadding">
                                    <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="3">
										<?php
                                    $company = $_SESSION['companyid'];
                                        
                                    $sql = "select * from groupings where compcode='$company' and ctype='CUSTYP' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>   
                                        <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$cCustTyp){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                                        <?php
                                            }
                                            
                            
                                        ?>     
                                    </select>
                                </div>
                    </div>
                </div>


				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Classification</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-7 nopadding">
                                  <select id="selcls" name="selcls" class="form-control input-sm selectpicker"  tabindex="3">
									<?php
                                    $sql = $sql = "select * from groupings where compcode='$company' and ctype='CUSTCLS' and cstatus='ACTIVE'  order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$cCustCls){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                                </div>
                    </div>
                </div>


				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Grocery Credit Limit</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-4 nopadding">
                                 <input type="text" class="numeric form-control input-sm" id="txtclimit" name="txtclimit" tabindex="11" placeholder="Enter Credit Limit..." required autocomplete="off" value="<?php echo $CreditLimit;?>" />
                                </div>
                              <div class="col-xs-8 nopadwleft">
                             <small>&nbsp;&nbsp; <i>Zero (0) for Unlimited Credit Limit</i></small>
                             </div>
                   </div>
                </div>

				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Cripples Credit Limit</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                            <div class="col-xs-4 nopadding">
                                 <input type="text" class="numeric form-control input-sm" id="txtcriplimit" name="txtcriplimit" tabindex="11" placeholder="Enter Credit Limit..." required autocomplete="off" value="<?php echo $CreditLimitCR;?>" /> 
                      		</div>
                             <div class="col-xs-8 nopadwleft">
                             <small>&nbsp;&nbsp; <i>Zero (0) for Unlimited Credit Limit</i></small>
                             </div>
                    </div>
                </div>

				<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadding">
                		<b>Parent Company</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

                               <div class="col-xs-7 nopadding">
                            <input type="text" class="form-control input-sm" id="txtcparent" name="txtcparent" tabindex="11" placeholder="Search Customer Name.." autocomplete="off" value="<?php echo $cParentName; ?>" />
                               </div>
                            
                                <div class="col-xs-2 nopadwleft">
                                    <input type="text" id="txtcparentD" name="txtcparentD" class="form-control input-sm" readonly value="<?php echo $cParentCode; ?>">
                                </div>	

                    </div>
                </div>


            
             </p>
         </div>

         <div id="menu1" class="tab-pane fade" style="padding-left:30px">
             <p>
   
   				<div class="col-xs-7 nopadding">
					<u><h4>COMPANY ADDRESS</h4></u>
                </div>
                    
             	<div class="col-xs-7 nopadwtop">
                	<input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="6" value="<?php echo $HouseNo;?>" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="7" value="<?php echo $City;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="8" value="<?php echo $State;?>" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" value="<?php echo $Country;?>" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10" value="<?php echo $ZIP;?>" />
                    </div>
                </div>

				<div class="col-xs-7 nopadding">
					<u><h4>CONTACT PERSON</h4></u>
                </div>


                <div class="col-xs-7 nopadding">
                	<input type="text" class="form-control input-sm" id="txtcperson" name="txtcperson" placeholder="Contact Person..." autocomplete="off" tabindex="11" value="<?php echo $Contact;?>" />
                </div>
 
              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcdesig" name="txtcdesig" placeholder="Designation..." autocomplete="off" tabindex="12" value="<?php echo $Desig;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcEmail" name="txtcEmail" placeholder="Email Address..." autocomplete="off" tabindex="13" value="<?php echo $Email;?>" />
                    </div>
                </div>

              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcphone" name="txtcphone" placeholder="Phone No..." autocomplete="off" tabindex="14" value="<?php echo $PhoneNo;?>" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcmobile" name="txtcmobile" placeholder="Mobile No..." autocomplete="off" tabindex="15" value="<?php echo $Mobile;?>" />
                    </div>
                </div>

             </p>
         </div>
         
         
                 <div id="menu2" class="tab-pane fade" style="padding-left:30px">
         <p>
            <div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup1">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup1" name="txtCustGroup1" tabindex="11" placeholder="Search Group 1..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup1D" name="txtCustGroup1D">
                </div>
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup1"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup6">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup6" name="txtCustGroup6" tabindex="11" placeholder="Search Group 6..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>

                    <input type="hidden" id="txtCustGroup6D" name="txtCustGroup6D">
                </div>
        
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button"  id="btnCustGroup6"><i class="fa fa-search"></i></button>
                </div>
        
            </div>
        
            <div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup2">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup2" name="txtCustGroup2" tabindex="11" placeholder="Search Group 2..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                   
                    <input type="hidden" id="txtCustGroup2D" name="txtCustGroup2D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button"  id="btnCustGroup2"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup7">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup7" name="txtCustGroup7" tabindex="11" placeholder="Search Group 7..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup7D" name="txtCustGroup7D">
                </div>
        
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup7"><i class="fa fa-search"></i></button>
                </div>
        
            </div>
        
            <div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup3">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup3" name="txtCustGroup3" tabindex="11" placeholder="Search Group 3..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup3D" name="txtCustGroup3D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup3"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup8">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                  <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup8" name="txtCustGroup8" tabindex="11" placeholder="Search Group 8..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                   
                    <input type="hidden" id="txtCustGroup8D" name="txtCustGroup8D">
                </div>
        
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup8"><i class="fa fa-search"></i></button>
                </div>
        
            </div>
        
            <div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup4">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup4" name="txtCustGroup4" tabindex="11" placeholder="Search Group 4..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup4D" name="txtCustGroup4D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup4"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup9">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                  <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup9" name="txtCustGroup9" tabindex="11" placeholder="Search Group 9..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                   
                    <input type="hidden" id="txtCustGroup9D" name="txtCustGroup9D">
                </div>
        
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup9"><i class="fa fa-search"></i></button>
                </div>
        
            </div>
        
            <div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup5">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup5" name="txtCustGroup5" tabindex="11" placeholder="Search Group 5..">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup5D" name="txtCustGroup5D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup5"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="CustGroup10">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup10" name="txtCustGroup10" tabindex="11" placeholder="Search Group 10...">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup10D" name="txtCustGroup10D">
                </div>
        
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup10"><i class="fa fa-search"></i></button>
                </div>
        
            </div>
            
            
             </p>
         </div>

 
          <div id="menu3" class="tab-pane fade" style="padding-left:30px">
         <p>
           <div class="col-xs-12">
            
			 <div class="col-xs-10 nopadwtop">
                	<div class="col-xs-2 nopadding">
                		<b>Account Code</b>
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    
                    	<select name="selaccttyp" id="selaccttyp" class="form-control input-sm">
                        	<option value="single" <?php if ($AcctCodeType=="single") { echo "selected"; } ?>>Single Account</option>
                            <option value="multiple" <?php if ($AcctCodeType=="multiple") { echo "selected"; } ?>>Per Item Type</option>
                        </select>
                    
                    </div>
                    
                </div>


			 <div class="col-xs-10 nopadwtop">
                	<div class="col-xs-2 nopadding">
                    </div>
                    
                    <div class="col-xs-8 nopadwleft" id="accttypsingle" <?php if ($AcctCodeType=="multiple") { echo "style='display:none'"; } ?>>

                               <div class="col-xs-7 nopadding">
                            <input type="text" class="form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="11" placeholder="Search Acct Title.." <?php echo $singlestat; ?> autocomplete="off" value="<?php echo $GroceryDesc;?>" />
                               </div>
                            
                                <div class="col-xs-2 nopadwleft">
                                    <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly value="<?php echo $GroceryID;?>" />
                                </div>	

                    </div>
   
                    <div class="col-xs-7 nopadwleft" id="accttypmulti" <?php if ($AcctCodeType=="single") { echo "style='display:none'"; } ?>>

                               <table class="table table-condensed table-hover">
                               		<tr>
                                    	<th width="200">Item Type</th>
                                        <th>Account</th>
                                    </tr>
									<?php
                                    $sql = "select A.ccode, A.cdesc, ifnull(B.ccode,'') as custcode, B.cacctno, C.cacctdesc from groupings A left join customers_accts B on A.compcode=B.compcode and A.ccode=B.citemtype and B.ccode='$cCustCode' left join accounts C on B.compcode=C.compcode and B.cacctno=C.cacctno where A.compcode='$company' and A.ctype='ITEMTYP' and A.cstatus='ACTIVE' order by A.cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                    ?>
                                        <tr>
                                            <td><?php echo $row['cdesc'];?></td>
                                            <td>
                                            
                                            <div class="col-xs-9 nopadding">
                            <input type="text" class="selsalesacctz form-control input-sm" id="txtsalesacct<?php echo $row['ccode'];?>" name="txtsalesacct<?php echo $row['ccode'];?>" data-id="<?php echo $row['ccode'];?>" tabindex="11" placeholder="Search Acct Title.." autocomplete="off" <?php echo $multistat; ?> value="<?php echo $row['cacctdesc'];?>"/>
                               				</div>
                            
                                            <div class="col-xs-3 nopadwleft">
                                              <input type="text" id="txtsalesacctD<?php echo $row['ccode'];?>" name="txtsalesacctD<?php echo $row['ccode'];?>" class="form-control input-sm" readonly value="<?php echo $row['cacctno'];?>">
                                            </div>
                                            
                                          </td>
                                        </tr>

									<?php
											}
									?>                                    
                                    
                               </table>

                    </div>
                 
                </div>

			 <div class="col-xs-10 nopadwtop">
                	<div class="col-xs-2 nopadding">
                		<b>Price Version</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

						<div class="col-xs-7 nopadding">
                                  <select id="selpricever" name="selpricever" class="form-control input-sm selectpicker"  tabindex="3">
                                  	<option value="NONE"  <?php if($Priceversion=="NONE"){ echo "selected"; }?>>Base from item markup</option>
									<?php
                                    $sql = $sql = "select * from groupings where compcode='$company' and ctype='ITMPMVER' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$Priceversion){ echo "selected"; }?>><?php echo $row['ccode'] ." - ". $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                          </div>

                    </div>
                </div>

			 <div class="col-xs-10 nopadwtop">
                	<div class="col-xs-2 nopadding">
                		<b>Business Type</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

						<div class="col-xs-7 nopadding">
                                  <select id="selvattype" name="selvattype" class="form-control input-sm selectpicker"  tabindex="3">
									<?php
                                    $sql = "Select * From vatcode where compcode='$company'";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['cvatcode'];?>" <?php if($VatType==$row['cvatcode']){ echo "selected"; }?>><?php echo $row['cvatdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                    ?>

                                  </select>
                         </div>

                    </div>
                </div>
  
  
			 <div class="col-xs-10 nopadwtop">
                	<div class="col-xs-2 nopadding">
                		<b>Terms</b>
                    </div>
                    
                    <div class="col-xs-9 nopadwleft">

						<div class="col-xs-7 nopadding">
                                  <select id="selcterms" name="selcterms" class="form-control input-sm selectpicker"  tabindex="3">
									<?php
                                    $sql = "Select * From groupings where compcode='$company' and ctype='TERMS'";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>" <?php if($Terms==$row['ccode']){ echo "selected"; }?>><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                         </div>

                    </div>
                </div>
		   </div>
        </div>

 
    
	</div>
</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Customers.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Customers_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
 
     <button type="button" class="btn btn-danger btn-sm" onClick="chkSIEnter(13,'frmedit');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
   
        <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit"> Edit<br>(CTRL+E) </button>

    	<button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button>
    
    </td>
  </tr>
</table>

</fieldset>
</form>

<!-- SAVING MODAL -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->		


<form name="frmedit" id="frmedit" action="Customers_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cCustCode;?>">
</form>

</body>
</html>

<script type="text/javascript">
$(document).ready(function(){
	$("#itmcode_err").hide();
	$("#txtccode").focus();

	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

	loadgroupnmes();
	chkGroupVal();	
	loadgroupvalues(); // load ung value ng group

	disabled();
});
	$(function(){

		$(".selsalesacctz").typeahead({						 
			autoSelect: true,
			source: function(request, response) {	
								
					$.ajax({
						url: "th_accounts.php",
						dataType: "json",
						data: { query: request },
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
					var zelectionid = this.$element.attr('data-id');	
					//alert(zelectionid+":"+item.name);
					
					$('#txtsalesacct'+zelectionid).val(item.name).change(); 
					$('#txtsalesacctD'+zelectionid).val(item.id); 
							
				}
		});


		$("#txtsalesacct").typeahead({						 
			autoSelect: true,
			source: function(request, response) {							
				$.ajax({
					url: "th_accounts.php",
					dataType: "json",
					data: { query: request },
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
					$('#txtsalesacct').val(item.name).change(); 
					$('#txtsalesacctD').val(item.id); 
							
				}
		});
		
		$("#txtsalesacct").on("blur", function() {
			if($('#txtsalesacctD').val()==""){
				$('#txtsalesacct').val("").change();
				$('#txtsalesacct').focus();
			}
		});
		
		$("#txtcparent").typeahead({						 
			autoSelect: true,
			source: function(request, response) {							
				$.ajax({
					url: "th_customers.php",
					dataType: "json",
					data: { query: request },
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
					$('#txtcparent').val(item.name).change(); 
					$('#txtcparentD').val(item.id); 
							
				}
		});
		
		$("#txtcparent").on("blur", function() {
			if($('#txtcparentD').val()==""){
				$('#txtcparent').val("").change();
				$('#txtcparent').focus();
			}
		});
		
		
					var $inputgrp = $(".txtCustGroup");
				  	$inputgrp.typeahead({						 
						autoSelect: true,
						source: function(request, response) {	
							$.ajax({								
								url: "th_custgroupdetails.php?id=CustGroup"+$(document.activeElement).attr('id').replace( /^\D+/g, ''),
								dataType: "json",
								data: { query: request },
								success: function (data) {
									response(data);
								}
							});
						},
						highlighter: Object,
						afterSelect: function(item) { 					
									
							var id = $(document.activeElement).attr('id');
							//alert(id);	
							
							$('#'+id).val(item.name).change(); 
							$('#'+id+'D').val(item.id); 
							
						}
					});
					
					$(".btncgroup").on("click", function() {
						var id = $(this).attr("id");
						var r = id.replace( /^\D+/g, '');
						 
						$("#myModalLabel").html("<b>Group "+r+"</b>");
						$("#TblItmGrpDet").html("");
						$("#myGrpModal").modal('show');
						
						
							var nme = "CustGroup"+r;
							
							$.ajax ({
							url: "th_loadcustgrpdetails.php",
							data: { id: nme },
							async: false,
							dataType: 'json',
							success: function( data ) {
								  console.log(data);
								  $.each(data,function(index,item){
									  
									  var divhead = "<div class=\"col-xs-12 nopadding\">";
									  var divcode = "<div class=\"col-xs-2 nopadding\"><a href=\"javascript:;\" onclick=\"setgrpvals('"+item.id+"','"+item.name+"','"+r+"');\">"+item.id+"</a></div>";
									  var divdesc = "<div class=\"col-xs-8 nopadding\">"+item.name+"</div>";
									  var divend = "</div>";
									  
									  $("#TblItmGrpDet").append(divhead + divcode + divdesc + divend);
									  
							
								  });
							}
							});
													 
					});



		$("#txtcEmail").on("blur", function() {
			var sEmail = $(this).val();
			
			var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
			
			if(sEmail!=""){
				if (filter.test(sEmail)) {
					//wlang gagawin
				}
				else {
					$("#txtcEmail").val("").change();
					$("#txtcEmail").attr("placeholder","You entered and invalid email!");
					$("#txtcEmail").focus();
				}
			}
			else{
				$("#txtcEmail").attr("placeholder","Email Address...");
			}

		});

						$("#frmCust").on('submit', function (e) {
							e.preventDefault();
							  	var form = document.getElementById("frmCust");
								var formData = new FormData(form);

							
							  $.ajax({
								type: 'post',
								url: 'Customers_editsave.php',
								data: formData,
								contentType: false,
								processData: false,
								async:false,
								beforeSend: function(){
								  	$("#AlertMsg").html("<b>UPDATING CUSTOMER: </b> Please wait a moment...");
									$("#AlertModal").modal('show');
								},
								success: function(data) {

										if(data.trim()=="True" || data.trim()=="Size" || data.trim()=="NO"){
											if(data.trim()=="True"){
									 			$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading supplier details... <br> Please wait!");				
											}else if(data.trim()=="Size"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated<br><br> Invalid Image Type or Size is too big! <br><br> Loading supplier details... <br> Please wait!");				
											}
											else if(data.trim()=="NO"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated <br><br> NO new image to be uploaded! <br><br> Loading supplier details... <br> Please wait!");				
											}
											
											setTimeout(function() {
											  $("#AlertMsg").html("");
											  $('#AlertModal').modal('hide');
											  
											  $("#txtcitemno").val($("#txtccode").val());
											  $("#frmedit").submit();
											}, 3000); // milliseconds = 3seconds
											
										}
										else{
											$("#AlertMsg").html(data);	
										}
								},
								error: function(){
									$("#AlertMsg").html("");
									$("#AlertModal").modal('hide');
									
							  		$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to update customer!");
									$("#itmcode_err").show();
								  
								}
							  });							

						});

		//Checking of uploaded file.. must be image
		$("#file").change(function() {
			$("#add_err").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','../imgusers/preview.jpg');
				
				$("#add_err").css('display', 'inline', 'important');
				$("#add_err").html("<div class='alert alert-danger nopadwleft' role='alert'>Please Select A valid Image File. <b>Note: </b>Only jpeg, jpg and png Images type allowed</div>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
		
		
		$("#selaccttyp").on("change", function() {
			if($(this).val()=="single"){				
				$("#accttypsingle").show();

				$('#txtsalesacct').prop('required',true);

					$('.selsalesacctz').each(function(i, obj) {
						$(this).prop('required',false);
					});

				$("#accttypmulti").hide();	 			

			}else{
				
				$("#accttypmulti").show();	
			
				$('#txtsalesacct').prop('required',false);
					$('.selsalesacctz').each(function(i, obj) {
						$(this).prop('required',true);
					});
				
				$("#accttypsingle").hide();	 		
			}

		});



	});

	$(document).keydown(function(e) {	 

		 if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Customers_new.php';
			}
		  }
		  else if(e.keyCode == 83 && e.ctrlKey){//F2
			if($("#btnSave").is(":disabled")==false){
				e.preventDefault();
				$("#btnSave").click();
			}
		  }
		  else if(e.keyCode == 69 && e.ctrlKey){//F8
			if($("#btnEdit").is(":disabled")==false){
				e.preventDefault();
				enabled();
			}
		  }
		  else if(e.keyCode == 90 && e.ctrlKey){//F3
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmedit');
			}
		  }
		  else if(e.keyCode == 27){//ESC	  
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Customers.php';
			}
		  }

	});


function disabled(){

	$("#frmCust :input, label").attr("disabled", true);
	
	
	$("#txtccode").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmCust :input, label").attr("disabled", false);
		
			
			$("#txtccode").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Customers_edit.php";
		document.getElementById(frm).submit();
	}
}


	//preview of image
	function imageIsLoaded(e) {
		$("#file").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '145px');
		$('#previewing').attr('height', '145px');
	};



function loadgroupnmes(){

	$('.cgroup').each(function(i, obj) {

		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

			$.ajax ({
            url: "th_loadgroup.php",
			data: { id: id },
			dataType: "text",
            success: function(result) {
				if(result.trim()!="False"){					
					$("#CustGroup"+r).html("<b>" + result + "</b>");
				}
				else {
					$("#CustGroup"+r).html("<b>Group " + r + "</b>");
				}
            }
    		});
	});
}

function chkGroupVal(){
	$(".txtCustGroup").each(function(i, obj) {
		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

			var nme = "CustGroup"+r;
			
			$.ajax ({
            url: "th_checkexistcgroup.php",
			data: { id: nme },
            success: function(result) {
				if(result.trim()=="False"){					
					$("#"+id).attr("readonly", true);					
					$("#btn"+nme).attr("disabled", true);
				}
            }
    		});


	});
}

function loadgroupvalues(){
		$(".txtCustGroup").each(function(i, obj) {
			
		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

		   var nme = "CustGroup"+r;
		   var citmno = $("#txtccode").val();
			
			$.ajax ({
            url: "th_loadcgroupvalue.php",
			data: { id: r, grpno: nme, itm: citmno },
			dataType: 'json',
            success: function(data) {
				console.log(data);
				$.each(data,function(index,item){
					
				  if(item.id!=""){				  
					$("#txtCustGroup"+r).val(item.name);
					$("#txtCustGroup"+r+"D").val(item.id);
				  }
									  
							
				});

            }
    		});

	});

}


</script>
