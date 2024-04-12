<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Suppliers_new.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

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
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?v=<?php echo time();?>"> 
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; min-height:700px">
<form name="frmITEM" id="frmITEM" method="post">
	<fieldset>
    	<legend>New Supplier</legend>
<table width="100%" border="0">
  <tr>
    <td width="200"><b>Supplier Code</b></td>
    <td width="310" colspan="2" style="padding:2px">
			<div class="col-xs-12 nopadding">
				<div class="col-xs-4 nopadding">
					<input type="text" class="required form-control input-sm has-error" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Supplier Code.." autocomplete="off" />
				</div>
			
				<div class="col-xs-4 nopadwleft">		
					<div id="itmcode_err" style="padding: 5px 10px;"></div>
				</div>
			</div>
    </td>
  </tr>
  <tr>
    <td><b>Registered Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Registered Name.." autocomplete="off" /></div></td>
  </tr>
	<tr>
    <td><b>Business/Trade Name</b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txttradename" name="txttradename" tabindex="2" placeholder="Business/Trade Name.." autocomplete="off" /></div></td>
  </tr>
  <tr>
    <td><b>Tin No.: </b></td>
    <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm" id="txtTinNo" name="txtTinNo" tabindex="2" placeholder="Tin No.."  autocomplete="off" /></div></td>
  </tr>
	<tr>
    <td><b>Address</b></td>
    <td colspan="2" style="padding:2px">
			<div class="col-xs-7 nopadwtop">
         <input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="6" />
      </div>
		</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px">
			          <div class="col-xs-7 nopadwtop">
                	<div class="col-xs-6 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="7" />
                    </div>
                    
                    <div class="col-xs-6 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="8" />
                    </div>
                </div>
		</td>
  </tr>
<tr>
    <td>&nbsp;</td>
    <td colspan="2" style="padding:2px">


              	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-9 nopadding">
                		<input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" />
                    </div>
                    
                    <div class="col-xs-3 nopadwleft">
                    	<input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10" />
                    </div>
                </div>
		</td>
  </tr>
</table>

<p>&nbsp;</p>
  <ul class="nav nav-tabs">
    <li class="ulist active"><a href="#menu0">General</a></li>
    <li class="ulist"><a href="#menu1">Contacts List</a></li>
    <li class="ulist"><a href="#menu2">Addresses</a></li>
    <li class="ulist"><a href="#menu3">Groupings</a></li>
    <li class="ulist"><a href="#menu4">Accounting</a></li>
	<!--<li><a href="#menu2">Product Details</a></li>-->
  </ul>
  
<div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
    <div class="tab-content">
    
         <div id="menu0" class="tab-pane fade in active" style="padding-left:10px">
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
                                        
                                    $sql = "select * from groupings where compcode='$company' and ctype='SUPTYP' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>   
                                        <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
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
                                    $sql = $sql = "select * from groupings where compcode='$company' and ctype='SUPCLS' and cstatus='ACTIVE' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                        ?>
                                    <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                                </div>
                    </div>
                </div>


				
        		</p>       
				 </div>

         <div id="menu1" class="tab-pane fade" style="padding-left:10px; padding-top:10px;">
             <p>

                <input type="button" value="Add Contact" name="btnNewCont" id="btnNewCont" class="btn btn-primary btn-xs" onClick="addcontlist();">
            
	            <input name="hdncontlistcnt" id="hdncontlistcnt" type="hidden" value="0">
	            <br>
				<table width="150%" border="0" cellpadding="2" id="myContactDetTable">
                  <tr>
                    <th scope="col" width="200">Name</th>
                    <th scope="col" width="180">Designation</th>
                    <th scope="col" width="180">Department</th>
					<th scope="col" width="180">Email Add.</th>
					<th scope="col" width="180">Mobile No.</th>
					<th scope="col" width="180">Phone No.</th>
					<th scope="col" width="180">Fax No.</th>
                      <?php
                          $arrcontctsdet = array();
                          $sql = "Select * From contacts_types where compcode='$company'";
                          $result=mysqli_query($con,$sql);
                          if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                          }			
                                      
                          while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                          {
                            $arrcontctsdet[] = array('cid' => $row['cid'], 'cdesc' => $row['cdesc']);
                        ?>
                            <th scope="col" width="180"><?=$row['cdesc']?></th>
                        <?php
                          }
                      ?>
                    <th scope="col" width="80"><input type='hidden' id='conctsadddet' value='<?=json_encode($arrcontctsdet)?>'></th>
                  </tr>
                </table>

             </p>
         </div>
         
         <div id="menu2" class="tab-pane fade" style="padding-left:30px">
             <p>
             
             <input type="button" value="Add Address" name="btnNewAddDel" id="btnNewAddDel" class="btn btn-primary btn-xs" onClick="adddeladdlist();">
            
            <input name="hdnaddresscnt" id="hdnaddresscnt" type="hidden" value="0">
            <br>
                <table width="100%" border="0" cellpadding="2" id="myDelAddTable"> 
                  <tr>
                    <th scope="col">House No./Bldg./Street/Subd.</th>
                    <th scope="col" width="180">City</th>
                    <th scope="col" width="180">State</th>
                    <th scope="col" width="180">Country</th>
                    <th scope="col" width="100">Zip Code.</th>
                    <th scope="col" width="50">&nbsp;</th>
                  </tr>
            	</table>

             </p>
         </div>

          <div id="menu3" class="tab-pane fade" style="padding-left:10px">
             <p>
             	<div class="col-xs-12">
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup1">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup1" name="txtCustGroup1" tabindex="12" placeholder="Search Group 1.." autocomplete="off">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup1D" name="txtCustGroup1D">
                </div>
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup1"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup6">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup6" name="txtCustGroup6" tabindex="13" placeholder="Search Group 6.." autocomplete="off">
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
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup2">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup2" name="txtCustGroup2" tabindex="14" placeholder="Search Group 2.." autocomplete="off">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                   
                    <input type="hidden" id="txtCustGroup2D" name="txtCustGroup2D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button"  id="btnCustGroup2"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup7">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup7" name="txtCustGroup7" tabindex="15" placeholder="Search Group 7.." autocomplete="off">
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
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup3">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup3" name="txtCustGroup3" tabindex="16" placeholder="Search Group 3.." autocomplete="off">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup3D" name="txtCustGroup3D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup3"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup8">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                  <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup8" name="txtCustGroup8" tabindex="17" placeholder="Search Group 8.." autocomplete="off">
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
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup4">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup4" name="txtCustGroup4" tabindex="18" placeholder="Search Group 4.." autocomplete="off">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup4D" name="txtCustGroup4D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup4"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup9">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                  <div class="btn-group btn-group-justified nopadding">
                    <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup9" name="txtCustGroup9" tabindex="19" placeholder="Search Group 9.." autocomplete="off">
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
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup5">
                    <b>Cost of Goods</b>
                </div>
                
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup5" name="txtCustGroup5" tabindex="20" placeholder="Search Group 5.." autocomplete="off">
                   <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                 </div>
                    
                    <input type="hidden" id="txtCustGroup5D" name="txtCustGroup5D">
                </div>
         
         
                <div class="col-xs-1 nopadwtop">
                    &nbsp;
                    <button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup5"><i class="fa fa-search"></i></button>
                </div>
                
                <div class="cgroup col-xs-2 nopadwtop" id="SuppGroup10">
                    <b>Cost of Goods</b>
                </div>
        
        
                <div class="col-xs-3 nopadwtop">
                 <div class="btn-group btn-group-justified nopadding">
                   <input type="text" class="txtCustGroup form-control input-sm" id="txtCustGroup10" name="txtCustGroup10" tabindex="21" placeholder="Search Group 10..." autocomplete="off">
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

         <div id="menu4" class="tab-pane fade" style="padding-left:10px">
             <p>
             
             <div class="col-xs-7 nopadwtop">
                		<div class="col-xs-3 nopadding">
                			<b>Terms</b>
                    	</div>
                    
                   		<div class="col-xs-9 nopadwleft">

                            	<div class="col-xs-4 nopadding">
                                  <select id="selterms" name="selterms" class="form-control input-sm selectpicker"  tabindex="3">
																	<?php
                                    $sql = "Select * From groupings where compcode='$company' and ctype='TERMS'";
                                    $result=mysqli_query($con,$sql);
                                        if (!mysqli_query($con, $sql)) {
                                            printf("Errormessage: %s\n", mysqli_error($con));
                                        }			
                            
                                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                            {
                                   ?>
                                    <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                                    <?php
                                            }
                                            
                            
                                        ?>
                                  </select>
                                </div>
                   		</div>
                </div>

				<input type="hidden" id="selvattype" name="selvattype" value="VT">
				<input type="hidden" id="procurement" name="procurement" value="">

				<!--<div class="col-xs-7 nopadwtop">
					<div class="col-xs-3 nopadding">
						<b>Procurement Type</b>
					</div>
					<div class="col-xs-9 nopadwleft">
						<div class="col-xs-9 nopadding">
						<select name="procurement" id="procurement" class="form-control input-sm selectpicker" tabindex="3">
							<option value="Services">PURCHASE OF SERVICES</option>
							<option value="Capital">PURCHASE OF CAPITAL GOODS</option>
							<option value="Goods">PURCHASE OF GOODS OTHER THAN CAPITAL GOODS</option>
						</select>
						</div>
					</div>
				</div>
                

             	<div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadwtop">
                		<b>Business Type</b>
                    </div>
                    
                    		<div class="col-xs-9 nopadwleft">

						        <div class="col-xs-4 nopadding">
	                                  <select id="selvattype" name="selvattype" class="form-control input-sm selectpicker"  tabindex="26">
										<?php
	                                    //$sql = "Select * From vatcode where compcode='$company'";
	                                   // $result=mysqli_query($con,$sql);
	                                     //   if (!mysqli_query($con, $sql)) {
	                                    //        printf("Errormessage: %s\n", mysqli_error($con));
	                                   //     }			
	                            
	                                    //    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	                                   //         {
	                                        ?>
	                                    <option value="<?//php echo $row['cvatcode'];?>"><?//php echo $row['cvatdesc']?></option>
	                                    <?php
	                                         //   }
	                                            
	                            
	                                        ?>
	                                  </select>
                       			</div>
                                <div class="col-xs-1">
                                	&nbsp;
                                </div>
                                
                                <div class="col-xs-2  nopadwtop">
                                	<b>Tax Rate: </b>
                                </div>
                                
                                <div class="col-xs-2 nopadding">
                                	<input type="text" class="form-control input-sm text-right required" id="txttaxrate" name="txttaxrate"  tabindex="5"  autocomplete="off" value="0.00" />
                                </div>
                                <div class="col-xs-1  nopadwtop"> 
                                	<b>&nbsp;% </b>
                                </div>
                   			</div>
                </div>-->
                
              <div class="col-xs-7 nopadwtop">
                	<div class="col-xs-3 nopadwtop">
                		<b>EWT Code</b>
                    </div>
                    <div class="col-xs-9 nopadwleft">

                           <div class="col-xs-7 nopadding">
                        		<input type="text" class="form-control input-sm" id="txtewt" name="txtewt"  tabindex="5" placeholder="Search EWT Description.." autocomplete="off" />
                           </div>
                        
                            <div class="col-xs-3 nopadwleft">
                                <input type="text" id="txtewtD" name="txtewtD" class="form-control input-sm" readonly>
                            </div>	
                            
                            <div class="col-xs-2 nopadwleft">
                                <input type="text" id="txtewtR" name="txtewtR" class="form-control input-sm" readonly>
                            </div>	

                    	</div>
                    
              </div>


         	

				<div class="col-xs-7 nopadwtop">
                		<div class="col-xs-3 nopadding">
                			<b>Liability Code</b>
                    	</div>
                    
                   		<div class="col-xs-9 nopadwleft">

                           <div class="col-xs-7 nopadding">
                        		<input type="text" class="required form-control input-sm" id="txtsalesacct" name="txtsalesacct"  tabindex="5" placeholder="Search Acct Title.." autocomplete="off" />
                           </div>
                        
                            <div class="col-xs-3 nopadwleft">
                                <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly>
																<input type="hidden" id="txtsalesacctDID" name="txtsalesacctDID">
                            </div>	

                    	</div>
				</div>

				<div class="col-xs-7 nopadwtop">
          <div class="col-xs-3 nopadding">
            <b>Default Currency</b>
          </div>
                    
          <div class="col-xs-9 nopadwleft">

            <div class="col-xs-7 nopadding">
							<select id="selcurrncy" name="selcurrncy" class="form-control input-sm selectpicker"  tabindex="27">
                <?php
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
            </div>
                      
          </div>
				</div>
			</p>	
         </div>

         
             
	</div>
</div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td><button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
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

<!-- Group Selection -->
<div class="modal fade" id="myGrpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Select Group Detail</b></h5>        
      </div>

    <div class="modal-body" style="height: 40vh">
    
         <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop" id="cGroup5">
                <b>Code</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
               <b>Description</b>
            </div>
        </div> 
          

        <div class="col-xs-12 nopadding pre-scrollable" id="TblItmGrpDet">
           
        </div> 
      
        <div class="alert alert-danger nopadwtop2x" id="addGrp_err"></div>         

  </div>
    
  <div class="modal-footer">
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
  </div>
    
    </div>
  </div>
</div>
<!-- Modal -->  

<form name="frmedit" id="frmedit" action="Suppliers_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="">
</form>

</body>
</html>


<script type="text/javascript">
$(document).ready(function() {
	$("#addGrp_err").hide();
	$("#itmcode_err").hide();
	$("#txtccode").focus();

	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });

    loadgroupnmes();
	chkGroupVal();
	
});

$(function() {

		$('#txtcitmdesc').typeahead({
			autoSelect: true,
			source: function(request, response) {
				$.ajax({
					url: "th_product.php",
					dataType: "json",
					data: {
						query: $("#txtcitmdesc").val()
					},
					success: function (data) {
						response(data);
					}
				});
			},
			displayText: function (item) {
				return item.value;
			},
			highlighter: Object,
			afterSelect: function(item) { 					
											
				addpurchcost(item.id,item.value);


									$("#txtcitmdesc").val("").change();
									$("#txtcitmdesc").focus();

			}
		
		});
	
		$('#txtcitmno').on("keypress", function(event) {
				if(event.keyCode == 13){
							$.ajax({
								url:'get_productid.php',
								data: 'c_id='+ $(this).val(),                 
								success: function(value){
									var data = value.split(",");
									//$('#txtcitmno').val(data[0]);
									//$('#txtcitmdesc').val(data[1]);

									addpurchcost(data[0],data[1],"");
								}
							});

							$('#txtcitmno').val("");
				}
			});

		$("#frmITEM").on('submit', function (e) {
			e.preventDefault();
			var form = $("#frmITEM");

			var dis = form.find('.required').filter(function(){ return this.value === '' });

			if (dis.length > 0) {
				e.preventDefault();
						
				$.each(dis, function( index, value ) {
					parentId = $("#"+this.id).parents("div[id*='menu']").attr("id");
					tabIndex = $("li a[href='#"+parentId+"']").parents("li");

					
					if(parentId!==undefined){

						$(".tab-pane").attr("class", "tab-pane fade");

						$("#"+parentId).attr("class", "tab-pane fade in active");
						tabIndex.attr("class", "active");

						$("#"+this.id).addClass("with-error");

					}

					return false;
				});

				return false;
												
			}else{

				var tbl = document.getElementById('myContactDetTable').getElementsByTagName('tr');
				var lastRow = tbl.length-1;
												
				document.getElementById('hdncontlistcnt').value = lastRow;

				var tbldl = document.getElementById('myDelAddTable').getElementsByTagName('tr');
				var lastRowdl = tbldl.length-1;
				document.getElementById('hdnaddresscnt').value = lastRowdl;

				var formdata = form.serialize();

				$.ajax({
					url: 'Suppliers_newsave.php',
					type: 'POST',
					async: false,
					data: formdata,
					beforeSend: function(){
						$("#AlertMsg").html("<b>SAVING NEW SUPPLIER: </b> Please wait a moment...");
						$("#AlertModal").modal('show');
					},
					success: function(data) {

						if(data.trim()=="True"){
							
							var x = saveprodz();						
							
							if(x.trim()=="True"){
								
								$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new supplier... <br> Please wait!");
							}
							else{
								$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved!<br><b>ERROR: </b>Supplier Details saving... <br><br> Loading new supplier... <br> Please wait!");
							}
							
								setTimeout(function() {
									$("#AlertMsg").html("");
									$('#AlertModal').modal('hide');
														
									$("#txtcitemno").val($("#txtccode").val());
											$("#frmedit").submit();
								}, 2000); // milliseconds = 2seconds
													
						}

						else{
							$("#AlertMsg").html(data);	
						}
					},
					error: function(){
						$("#AlertMsg").html("");
						$("#AlertModal").modal('hide');
										
						$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new supplier!");
						$("#itmcode_err").show();
										
					}
				});		
			
			}
								

		});
			
		$("#txtewt").typeahead({						 
			autoSelect: true,
			source: function(request, response) {							
				$.ajax({
					url: "../th_ewtcodes.php",
					dataType: "json",
					data: { query: request },
					success: function (data) {
						response(data);
					}
				});
				},
				displayText: function (item) {
					return '<div style=\'width: 400px\'><span><b>' + item.id +': ' + item.rate + '%</b></span><br><small>' + item.desc + "</small></div>";
				},
				highlighter: Object,
				afterSelect: function(item) { 					
					$('#txtewt').val(item.desc).change(); 
					$('#txtewtD').val(item.id); 
					$('#txtewtR').val(item.rate); 
							
				}
		});
						
		$("#txtsalesacct").typeahead({						 
			autoSelect: true,
			source: function(request, response) {							
				$.ajax({
					url: "../th_accounts.php",
					dataType: "json",
					data: { query: request, typ: "LIABILITIES" },
					success: function (data) {
						response(data);
					}
				});
				},
				displayText: function (item) {
					return '<div style=\'width: 400px\'><span><b>' + item.id + '</b></span><br><small>' + item.name + "</small></div>";
					
					return item.id + " : " + item.name;
				},
				highlighter: Object,
				afterSelect: function(item) { 					
					$('#txtsalesacct').val(item.name).change(); 
					$('#txtsalesacctD').val(item.id); 
					$('#txtsalesacctDID').val(item.idcode); 
							
				}
		});
		
		$("#txtsalesacct").on("blur", function() {
			if($('#txtsalesacctD').val()==""){
				$('#txtsalesacctDID').val("");
				$('#txtsalesacct').val("").change();
				$('#txtsalesacct').focus();
			}
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

					$("#txtccode").on("keyup", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
						if($(this).val()!=""){
							$.ajax ({
							url: "suppcode_checker.php",
							data: { id: $(this).val() },
							async: false,
							dataType: 'text',
							success: function( data ) {

								if(data.trim()=="True"){

							  		$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Code Already In Use!");
									
									$("#itmcode_err").show();
								}
								else if(data.trim()=="False") {

							  		$("#itmcode_err").html("<b><font color='green'>VALID: </font></b> Valid Code!");
									
									$("#itmcode_err").show();
								}
							}
							});
						}
						else{
							$("#itmcode_err").html("");
							$("#itmcode_err").hide();
						}

					});


					$("#txtccode").on("blur", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
							
							$.ajax ({
							url: "suppcode_checker.php",
							data: { id: $(this).val() },
							async: false,
							success: function( data ) {
								if(data.trim()=="True"){
									$("#txtccode").val("").change();
									$("#txtccode").focus();
								}
							}
							});
							
							$("#itmcode_err").html("");
							$("#itmcode_err").hide();


					});

					var $inputgrp = $(".txtCustGroup");
				  	$inputgrp.typeahead({						 
						autoSelect: true,
						source: function(request, response) {	
							$.ajax({								
								url: "th_custgroupdetails.php?id=SuppGroup"+$(document.activeElement).attr('id').replace( /^\D+/g, ''),
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
						
						
							var nme = "SuppGroup"+r;
							
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

							
});

$(document).keydown(function(e) {

	 if(e.keyCode == 83 && e.ctrlKey) { //CTRL S
	  	  e.preventDefault();
		 if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
		  $("#btnSave").click();
		 }
	  }

});

function addpurchcost(codez,namez){
var isMERON = "";	
//CHECK IF CODE EXIST IN TABLE
$("#myPurchTable > tbody > tr").each(function() {
	var txtcitm = $(this).find("input[name='txtitmcode']").val();
	
	if(txtcitm==codez){
		isMERON = "TRUE";
	}
	
});

if(isMERON=="TRUE"){
	$("#itmerradd").attr("class","alert alert-danger nopadding");
	$("#itmerradd").html("<b>ERROR: </b> Item already added!");
	$("#itmerradd").show();

}
else{

	var tbl = document.getElementById('myPurchTable').getElementsByTagName('tr');
	var count = tbl.length;
	
	var itmcode = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadding\"><input type=\"hidden\" id=\"txtitmcode\" name=\"txtitmcode\" value=\""+codez+"\" />"+codez+"</div></td>";
	
	var itmname = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"hidden\" id=\"txtitmname\" name=\"txtitmname\" value=\""+namez+"\" />"+namez+"</div></td>";
		
	var crem = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtcremarks\" name=\"txtcremarks\" placeholder=\"Enter Remarks...\" autocomplete=\"off\" /></td>";
	
	var cstat = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><button class=\"form-input btn btn-xs btn-danger\">Remove</button></div></td>";
	
	$('#myPurchTable > tbody:last-child').append('<tr>' + itmcode + itmname + crem + cstat + '</tr>');


	$("#itmerradd").attr("class","");
	$("#itmerradd").html("");
	$("#itmerradd").hide();
	
}
}

function saveprodz(){

	var custcode = $("#txtccode").val();
	var result = "True";
						
	$("#myPurchTable > tbody > tr").each(function() {
		
		var txtcitm = $(this).find("input[name='txtitmcode']").val();
		var txtcremarks = $(this).find("input[name='txtcremarks']").val();
							

			$.ajax ({
				url: "Suppliers_prodsave.php",
				data: { id: custcode, itm: txtcitm, rem: txtcremarks },
				async: false,
				success: function( data ) {
					 result = data;
				}
			});
														
	});
	
	return result;

}

function loadgroupnmes(){

	$('.cgroup').each(function(i, obj) {

		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

			$.ajax ({
        url: "../th_loadgroup.php",
				data: { id: id },
				dataType: "text",
        success: function(result) {
					if(result.trim()!="False"){					
						$("#SuppGroup"+r).html("<b>" + result + "</b>");
					}
					else {
						$("#SuppGroup"+r).html("<b>Group " + r + "</b>");
					}
        }
    	});
	});
}

function chkGroupVal(){
	$(".txtCustGroup").each(function(i, obj) {
		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

			var nme = "SuppGroup"+r;
			
			$.ajax ({
            url: "../th_checkexistcgroup.php",
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

function setgrpvals(code,desc,r){
  $("#txtCustGroup"+r).val(desc);
  $("#txtCustGroup"+r+"D").val(code);
  
  $("#myGrpModal").modal('hide');
}

	function addcontlist(){
    var tbl = document.getElementById('myContactDetTable').getElementsByTagName('tr');
    var lastRow = tbl.length;

    var a=document.getElementById('myContactDetTable').insertRow(-1);
    var b=a.insertCell(0);
    var c=a.insertCell(1);
    var d=a.insertCell(2);
	var d1=a.insertCell(3);
	var d2=a.insertCell(4);
	var d3=a.insertCell(5);
	var d4=a.insertCell(6);

    b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='required form-control input-xs' id='txtConNme"+lastRow+"' name='txtConNme"+lastRow+"' value=''></div>";
    c.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConDes"+lastRow+"' name='txtConDes"+lastRow+"' value=''> </div>";
    d.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConDept"+lastRow+"' name='txtConDept"+lastRow+"' value=''> </div>";
	d1.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConEmail"+lastRow+"' name='txtConEmail"+lastRow+"' value=''> </div>";
	d2.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConMob"+lastRow+"' name='txtConMob"+lastRow+"' value=''> </div>";
	d3.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConPhone"+lastRow+"' name='txtConPhone"+lastRow+"' value=''> </div>";
	d4.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConFax"+lastRow+"' name='txtConFax"+lastRow+"' value=''> </div>";

    $cntng = 6;
    var xz = $("#conctsadddet").val();
	$.each(jQuery.parseJSON(xz), function() { 
		$cntng = $cntng + 1;
		var e=a.insertCell($cntng);

		e.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConAdd"+this['cid']+lastRow+"' name='txtConAdd"+this['cid']+lastRow+"' value=''> </div>";
	});

    $cntng = $cntng + 1
    var h=a.insertCell($cntng);
    h.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input class='btn btn-danger btn-block btn-xs' type='button' id='rowCn_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRowconts(this);\"/></div>";
    
  }

  function deleteRowconts(r) {
	var tbl = document.getElementById('myContactDetTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.parentNode.rowIndex;
	document.getElementById('myContactDetTable').deleteRow(i);
	var lastRow = tbl.length;
	var z; //for loop counter changing textboxes ID;
	var xz = $("#conctsadddet").val();

	for (z=i+1; z<=lastRow; z++){
		var tempconname = document.getElementById('txtConNme' + z);
		var tempcondes = document.getElementById('txtConDes' + z);
		var tempcondept = document.getElementById('txtConDept' + z);
		var tempconemail = document.getElementById('txtConEmail' + z);
		var tempconmob = document.getElementById('txtConMob' + z);
		var tempconphone = document.getElementById('txtConPhone' + z);
		var tempconfax = document.getElementById('txtConFax' + z);
		var tempdelbtn = document.getElementById('rowCn_' + z + '_delete');

		$.each(jQuery.parseJSON(xz), function() { 
			var tempx = document.getElementById('txtConAdd'+this['cid'] + z);
			m = z - 1
			tempx.id = "txtConAdd" + this['cid'] + m;
			tempx.name = "txtConAdd" + this['cid'] + m;
		})
		
		var x = z-1;
		tempconname.id = "txtConNme" + x;
		tempconname.name = "txtConNme" + x;
		tempcondes.id = "txtConDes" + x;
		tempcondes.name = "txtConDes" + x;
		tempcondept.id = "txtConDept" + x;
		tempcondept.name = "txtConDept" + x;
		tempconemail.id = "txtConEmail" + x;
		tempconemail.name = "txtConEmail" + x;
		tempconmob.id = "txtConMob" + x;
		tempconmob.name = "txtConMob" + x;
		tempconphone.id = "txtConPhone" + x;
		tempconphone.name = "txtConPhone" + x;
		tempconfax.id = "txtConFax" + x;
		tempconfax.name = "txtConFax" + x;
		tempdelbtn.id = "rowCn_" + x + "_delete";
		tempdelbtn.name = "rowCn_" + x + "_delete";
	}
  }

function adddeladdlist(){
	var tbl = document.getElementById('myDelAddTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('myDelAddTable').insertRow(-1);
	var b=a.insertCell(0);
	var c=a.insertCell(1);
	var d=a.insertCell(2);
	var e=a.insertCell(3);
	var f=a.insertCell(4);
  	var h=a.insertCell(5);
	
	b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='required form-control input-sm' id='txtdeladdno"+lastRow+"' name='txtdeladdno"+lastRow+"' value=''></div>";
	c.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-sm' id='txtdeladdcity"+lastRow+"' name='txtdeladdcity"+lastRow+"' value=''> </div>";
  	d.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-sm' id='txtdeladdstt"+lastRow+"' name='txtdeladdstt"+lastRow+"' value=''> </div>";
	e.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-sm' id='txtdeladdcntr"+lastRow+"' name='txtdeladdcntr"+lastRow+"' value=''> </div>";
	f.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-sm' id='txtdeladdzip"+lastRow+"' name='txtdeladdzip"+lastRow+"' value=''> </div>";
	
	h.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRowAddresss(this);\"/></div>";
	
}

function deleteRowAddresss(r) {
	var tbl = document.getElementById('myDelAddTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.parentNode.rowIndex;
	//alert(i)
	 document.getElementById('myDelAddTable').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempdeladdno = document.getElementById('txtdeladdno' + z);
			var tempdeladdcity = document.getElementById('txtdeladdcity' + z);
      		var tempdeladdstt = document.getElementById('txtdeladdstt' + z);
			var tempdeladdntr = document.getElementById('txtdeladdcntr' + z);
			var tempdeladdzip = document.getElementById('txtdeladdzip' + z);
			var tempdeladddelt = document.getElementById('row_' + z + '_delete');
			
			var x = z-1;
			tempdeladdno.id = "txtdeladdno" + x;
			tempdeladdno.name = "txtdeladdno" + x;
			tempdeladdcity.id = "txtdeladdcity" + x;
			tempdeladdcity.name = "txtdeladdcity" + x;
     		tempdeladdstt.id = "txtdeladdstt" + x;
      		tempdeladdstt.name = "txtdeladdstt" + x;
			tempdeladdntr.id = "txtdeladdcntr" + x;
			tempdeladdntr.name = "txtdeladdcntr" + x;
			tempdeladdzip.id = "txtdeladdzip" + x;
			tempdeladdzip.name = "txtdeladdzip" + x;
			tempdeladddelt.id = "row_" + x + "_delete";
		}
}

</script>