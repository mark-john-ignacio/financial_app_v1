<?php
  if(!isset($_SESSION)){
    session_start();
  }

  $_SESSION['pageid'] = "Customers_new.php";

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
  <script src="../../Bootstrap/js/jquery.numeric.js"></script>
  <script src="../../Bootstrap/js/bootstrap.js"></script>
    
  <script src="../../Bootstrap/js/moment.js"></script>
    
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; height:700px">
  <form name="frmITEM" id="frmITEM" method="post">
	  <fieldset>
    	<legend>New Customer</legend>
        <table width="100%" border="0">
          <tr>
            <td width="200"><b>Customer Code</b></td>
            <td width="310" colspan="2" style="padding:2px">
              <div class="col-xs-7 nopadding">   
                <div class="col-xs-4 nopadding">
                  <input type="text" class="required form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Customer Code.." required autocomplete="off" />
                </div>   
                <div class="col-xs-5 nopadwleft">		
                  <div id="itmcode_err" style="padding: 5px 10px;"></div>
                </div>   
              </div>    
            </td>
          </tr>
          <tr>
            <td><b>Registered Name: </b></td>
            <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Customer Registered Name.." required autocomplete="off" /></div></td>
          </tr>
          <tr>
            <td><b>Business/Trade Name</b></td>
            <td style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txttradename" name="txttradename" tabindex="2" placeholder="Customer Business/Trade Name.." required  value="" autocomplete="off" /></div></td>
          </tr>
          <tr>
            <td><b>Tin No.: </b></td>
            <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm" id="txtTinNo" name="txtTinNo" tabindex="2" placeholder="Tin No.." required autocomplete="off" /></div></td>
          </tr>
          <tr>
            <td><b>Address</b></td>
            <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="3" /></div></td>
          </tr>  
          <tr>
            <td>&nbsp;</td>
            <td colspan="2" style="padding:2px">
              <div class="col-xs-8 nopadding"><div class="col-xs-6 nopadding">
                <input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="4" />
              </div>                    
              <div class="col-xs-6 nopadwleft">
                <input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="5" />
              </div>
            </td>
          </tr>  
          <tr>
            <td>&nbsp;</td>
            <td colspan="2" style="padding:2px">
              <div class="col-xs-8 nopadding">
                <div class="col-xs-9 nopadding">
                  <input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="6" />
                </div>                    
                <div class="col-xs-3 nopadwleft">
                  <input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="7" />
                </div>
              </div>
            </td>
          </tr> 
        </table>

        <p>&nbsp;</p>
        <ul class="nav nav-tabs">
          <li class="active"><a href="#home">General</a></li>
          <li><a href="#menu1">Contacts List</a></li>
          <li><a href="#menu4">Addresses</a></li>
          <li><a href="#menu2">Groupings</a></li>
          <li><a href="#menu3">Accounting</a></li>
        </ul>

          <div class="tab-content">   

            <div id="home" class="tab-pane fade in active" style="padding-left:20px; padding-top:15px">

              <div class="row nopadwtop">
                <div class="col-xs-1 nopadding">
                  <b>Type</b>
                </div>                    
                <div class="col-xs-3 nopadwleft">
                  <select id="seltyp" name="seltyp" class="form-control input-sm selectpicker"  tabindex="8">
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
                    <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                    <?php
                      }
                    ?>     
                  </select>
                </div>
              </div>

              <div class="row nopadwtop">
                <div class="col-xs-1 nopadding">
                  <b>Classification</b>
                </div>                   
                <div class="col-xs-3 nopadwleft">
                  <select id="selcls" name="selcls" class="form-control input-sm selectpicker"  tabindex="9">
                    <?php
                      $sql = $sql = "select * from groupings where compcode='$company' and ctype='CUSTCLS' and cstatus='ACTIVE'  order by cdesc";
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

              <div class="row nopadwtop">
                <div class="col-xs-1 nopadding">
                  <b>Credit Limit</b>
                </div>                    
                <div class="col-xs-3 nopadwleft">
                  <input type="text" class="numeric required form-control input-sm text-right" id="txtclimit" name="txtclimit" tabindex="10" placeholder="Enter Credit Limit..." required autocomplete="off" value="0"/> 
                </div>
                <div class="col-xs-2 nopadwleft">
                  <small>&nbsp;&nbsp; <i>Zero (0) for Unlimited Credit Limit</i></small>
                </div>
              </div>

              <!--<div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>Parent Company</b>
                </div>                    
                <div class="col-xs-2 nopadwleft">
                  <input type="text" class="form-control input-sm" id="txtcparent" name="txtcparent" tabindex="11" placeholder="Search Customer Name.." autocomplete="off" />
                </div>
                <div class="col-xs-2 nopadwleft">
                  <input type="text" id="txtcparentD" name="txtcparentD" class="form-control input-sm" readonly>                  	
                </div>
              </div>-->

              <div class="row nopadwtop">
                <div class="col-xs-1 nopadding">
                  <b>Salesman</b>
                </div>                    
                <div class="col-xs-3 nopadwleft">
                  <input type="text" class="form-control input-sm" id="txtsman" name="txtsman" tabindex="11" placeholder="Search Salesman Name.." autocomplete="off" />
                </div>
                <div class="col-xs-1 nopadwleft">
                    <input type="text" id="txtsmanD" name="txtsmanD" class="form-control input-sm" readonly>
                </div>	
              </div>

            </div>

            <div id="menu1" class="tab-pane fade" style="padding-left:10px; padding-top:15px; overflow: auto; height: 250px">                    
              <input type="button" value="Add Contact" name="btnNewCont" id="btnNewCont" class="btn btn-primary btn-xs" onClick="addcontlist();">
              <input name="hdncontlistcnt" id="hdncontlistcnt" type="hidden" value="0">
              <br><br>
              <table width="150%" border="0" cellpadding="2" id="myContactDetTable">
                <tr>
                  <th scope="col" width="200">Name</th>
                  <th scope="col" width="180">Designation</th>
                  <th scope="col" width="180">Department</th>
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
            </div>
         
            <div id="menu4" class="tab-pane fade" style="padding-left:10px; padding-top:15px">                
              <input type="button" value="Add Address" name="btnNewAddDel" id="btnNewAddDel" class="btn btn-primary btn-xs" onClick="adddeladdlist();">
              <input name="hdnaddresscnt" id="hdnaddresscnt" type="hidden" value="0">
              <br><br>
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
            </div>
         
            <div id="menu2" class="tab-pane fade" style="padding-left:10px; padding-top:15px"> 

                <div class="row nopadding">
                  <div class="cgroup col-xs-2 nopadwtop" id="CustGroup1">
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
                    &nbsp;<button class="btncgroup btn btn-sm btn-danger" type="button" id="btnCustGroup1"><i class="fa fa-search"></i></button>
                  </div>                    
                  <div class="cgroup col-xs-2 nopadwtop" id="CustGroup6">
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
                    &nbsp;<button class="btncgroup btn btn-sm btn-danger" type="button"  id="btnCustGroup6"><i class="fa fa-search"></i></button>
                  </div>            
                </div>
            
                <div class="row nopadding">
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup2">
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
                    
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup7">
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
            
                <div class="row nopadding">
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup3">
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
                    
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup8">
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
            
                <div class="row nopadding">
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup4">
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
                    
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup9">
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
            
                <div class="row nopadding">
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup5">
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
                    
                    <div class="cgroup col-xs-2 nopadwtop" id="CustGroup10">
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

            </div>

            <div id="menu3" class="tab-pane fade" style="padding-left:10px; padding-top:15px">

              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>AR Code</b>
                </div>                    
                <div class="col-xs-3 nopadwleft">                    
                  <select name="selaccttyp" id="selaccttyp" class="form-control input-sm" tabindex="22">
                    <option value="single" selected>Single Account</option>
                    <option value="multiple">Per Item Type</option>
                  </select>                    
                </div>                   
              </div>

              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">&nbsp; </div>  
                <div class="col-xs-10 nopadwleft" id="accttypsingle">
                  <div class="row nopadding">
                    <div class="col-xs-3 nopadding">
                      <input type="text" class="required form-control input-sm" id="txtsalesacct" name="txtsalesacct" tabindex="23" placeholder="Search Acct Title.." autocomplete="off" required/>
                    </div>          
                    <div class="col-xs-2 nopadwleft">
                      <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly>
                      <input type="hidden" id="txtsalesacctDID" name="txtsalesacctDID">
                    </div>	
                  </div>
                </div>   
                <div class="col-xs-7 nopadwleft" id="accttypmulti" style="display:none">
                  <table class="table table-condensed table-hover">
                    <tr>
                      <th width="200">Item Type</th>
                      <th>Account</th>
                    </tr>
                    <?php
                      $sql = "select * from groupings where compcode='$company' and ctype='ITEMTYP' and cstatus='ACTIVE' order by cdesc";
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
                          <input type="text" class="selsalesacctz form-control input-sm" id="txtsalesacct<?php echo $row['ccode'];?>" name="txtsalesacct<?php echo $row['ccode'];?>" data-id="<?php echo $row['ccode'];?>" tabindex="24" placeholder="Search Acct Title.." autocomplete="off" />
                        </div>         
                        <div class="col-xs-3 nopadwleft">
                          <input type="text" id="txtsalesacctD<?php echo $row['ccode'];?>" name="txtsalesacctD<?php echo $row['ccode'];?>" class="form-control input-sm" readonly>
                          <input type="hidden" id="txtsalesacctDID<?php echo $row['ccode'];?>" name="txtsalesacctDID<?php echo $row['ccode'];?>">
                        </div>
                      </td>
                    </tr>
                    <?php
                        }
                    ?>                 
                  </table>
                </div>                 
              </div>
          
              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>Price Version</b>
                </div>  
                <div class="col-xs-3 nopadwleft">
                  <select id="selpricever" name="selpricever" class="form-control input-sm selectpicker"  tabindex="25">
                    <option value="NONE">Base from item markup</option>
                    <?php
                      $sql = "select * from groupings where compcode='$company' and ctype='ITMPMVER' and cstatus='ACTIVE' order by cdesc";
                      $result=mysqli_query($con,$sql);
                      if (!mysqli_query($con, $sql)) {
                        printf("Errormessage: %s\n", mysqli_error($con));
                      }			
                      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                      {
                    ?>
                      <option value="<?php echo $row['ccode'];?>"><?php echo $row['ccode'] . " - " . $row['cdesc']?></option>
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div>          
        
              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>Terms</b>
                </div>
                <div class="col-xs-3 nopadwleft">
                  <select id="selcterms" name="selcterms" class="form-control input-sm selectpicker"  tabindex="27">
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
              
              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>Default Currency</b>
                </div>
                <div class="col-xs-3 nopadwleft">
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

              <div class="row nopadwtop">
                <div class="col-xs-2 nopadding">
                  <b>Default Sales Tax Type</b>
                </div>  
                <div class="col-xs-3 nopadwleft">
                  <select id="selvattype" name="selvattype" class="form-control input-sm selectpicker"  tabindex="26">
                    <option value="">N/A</option>
                  <?php
                      $sql = "Select * From vatcode where compcode='$company' and ctype='Sales' and cstatus='ACTIVE'";
                      $result=mysqli_query($con,$sql);
                      if (!mysqli_query($con, $sql)) {
                        printf("Errormessage: %s\n", mysqli_error($con));
                      }			          
                      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                      {
                    ?>
                      <option value="<?php echo $row['cvatcode'];?>"><?php echo $row['cvatdesc']?></option>
                    <?php
                      }
                    ?>
                  </select>
                </div>
                <div class="col-xs-7 nopadwleft">
                  <div class="nopadwtop"><small><i> N/A means that the Item's Sales Tax Type will be read by the system in Sales Transactions</i></small></div>
                </div>
              </div>  

            </div>

          </div><!-- tab-content -->


          <div class="row nopadwtop2x">
            <div class="col-xs-12 nopadwtop2x">
              <button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (F2)</button>
            </div>
          </div>

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

  <form name="frmedit" id="frmedit" action="Customers_edit.php" method="POST">
    <input type="hidden" name="txtcitemno" id="txtcitemno" value="">
  </form>

</body>
</html>


<script type="text/javascript">

  $(document).ready(function() {
    $(".nav-tabs a").click(function(){
          $(this).tab('show');
      });
    
      $("#addGrp_err").hide();
    $("#itmcode_err").hide();
    $("#txtccode").focus();
    $("input.numeric").numeric();


    loadgroupnmes();
    chkGroupVal();	
        
      $(".selsalesacctz").typeahead({						 
        autoSelect: true,
        source: function(request, response) {	
                  
            $.ajax({
              url: "../th_accounts.php",
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
            $('#txtsalesacctDID'+zelectionid).val(item.idcode);
                
          }
      });
    
      $("#txtsalesacct").typeahead({						 
        autoSelect: true,
        source: function(request, response) {							
          $.ajax({
            url: "../th_accounts.php",
            dataType: "json",
            data: { query: request, typ: "ASSETS" },
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
            $('#txtsalesacctDID').val(item.idcode);
                
          }
      });
      
      $("#txtsalesacct").on("blur", function() {
        if($('#txtsalesacctD').val()==""){
          $('#txtsalesacct').val("").change();
          $('#txtsalesacctDID').val()==""
          $('#txtsalesacct').focus();
        }
      });

      $("#txtsalesacctCR").typeahead({						 
        autoSelect: true,
        source: function(request, response) {							
          $.ajax({
            url: "../th_accounts.php",
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
            $('#txtsalesacctCR').val(item.name).change(); 
            $('#txtsalesacctCRD').val(item.id); 
            $('#txtsalesacctDIDCR').val(item.idcode);
                
          }
      });
      
      $("#txtsalesacctCR").on("blur", function() {
        if($('#txtsalesacctCRD').val()==""){
          $('#txtsalesacctCR').val("").change();
          $('#txtsalesacctDIDCR').val()==""
          $('#txtsalesacctCR').focus();
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
      
      $("#txtsman").typeahead({						 
        autoSelect: true,
        source: function(request, response) {							
          $.ajax({
            url: "th_salesman.php",
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
            $('#txtsman').val(item.name).change(); 
            $('#txtsmanD').val(item.id); 
                
          }
      });
      
      $("#txtsman").on("blur", function() {
        if($('#txtsmanD').val()==""){
          $('#txtsman').val("").change();
          $('#txtsman').focus();
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

      $("#frmITEM").on('submit', function (e) {
        e.preventDefault();
        
        var tbl = document.getElementById('myContactDetTable').getElementsByTagName('tr');
        var lastRow = tbl.length-1;											
        document.getElementById('hdncontlistcnt').value = lastRow;


        var tbldl = document.getElementById('myDelAddTable').getElementsByTagName('tr');
        var lastRowdl = tbldl.length-1;
        document.getElementById('hdnaddresscnt').value = lastRowdl;	
                                                  
        var form = $("#frmITEM");
        var formdata = form.serialize();
        $.ajax({
        url: 'Customers_newsave.php',
        type: 'POST',
        async: false,
        data: formdata,
        beforeSend: function(){
          $("#AlertMsg").html("<b>SAVING NEW CUSTOMER: </b> Please wait a moment...");
          $("#AlertModal").modal('show');
        },
        success: function(data) {
          if(data.trim()=="True"){
            $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new customer... <br> Please wait!");
                          
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
                    
          $("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new customer!");
          $("#itmcode_err").show();
                    
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

      
              $("#txtccode").on("keyup", function() {
              
              //	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
              if($(this).val()!=""){
                $.ajax ({
                url: "customers_codechecker.php",
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
                url: "customers_codechecker.php",
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
    if(e.keyCode == 113){//F2
        if(document.getElementById("btnSave").className=="btn btn-success btn-sm"){
          $("#btnSave").click();
        }
    }
  });

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

  function addcontlist(){
      var tbl = document.getElementById('myContactDetTable').getElementsByTagName('tr');
      var lastRow = tbl.length;

      var a=document.getElementById('myContactDetTable').insertRow(-1);
      var b=a.insertCell(0);
      var c=a.insertCell(1);
      var d=a.insertCell(2);

      b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConNme"+lastRow+"' name='txtConNme"+lastRow+"' value='' required></div>";
      c.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConDes"+lastRow+"' name='txtConDes"+lastRow+"' value=''> </div>";
      d.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConDept"+lastRow+"' name='txtConDept"+lastRow+"' value=''> </div>";

      $cntng = 2;
      var xz = $("#conctsadddet").val();
        $.each(jQuery.parseJSON(xz), function() { 
          $cntng = $cntng + 1;
          var e=a.insertCell($cntng);

          e.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-xs' id='txtConAdd"+this['cid']+lastRow+"' name='txtConAdd"+this['cid']+lastRow+"' value=''> </div>";

        });

      $cntng = $cntng + 1
      var h=a.insertCell($cntng);
      h.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input class='btn btn-danger btn-block btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRowconts(this);\"/></div>";
      
  }

  function deleteRowconts(r) {
    var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
    var lastRow = tbl.length;
    var i=r.parentNode.parentNode.parentNode.rowIndex;
    //alert(i)
    document.getElementById('myUnitTable').deleteRow(i);
    var lastRow = tbl.length;
    var z; //for loop counter changing textboxes ID;
    
      for (z=i+1; z<=lastRow; z++){
        var tempconnme = document.getElementById('txtConNme' + z);
        var tempcondes = document.getElementById('txtConDes' + z);
        var tempcondept = document.getElementById('txtConDept' + z);
        var tempconeml = document.getElementById('txtConeml' + z);
        var tempcontel = document.getElementById('txtContel' + z);
        var tempconmob = document.getElementById('txtConmob' + z);
        
        var x = z-1;
        tempconnme.id = "txtConNme" + x;
        tempconnme.name = "txtConNme" + x;
        tempcondes.id = "txtConDes" + x;
        tempcondes.name = "txtConDes" + x;
          tempcondept.id = "txtConDept" + x;
            tempcondept.name = "txtConDept" + x;
        tempconeml.id = "txtConeml" + x;
        tempconeml.name = "txtConeml" + x;
        tempcontel.id = "txtContel" + x;
        tempcontel.name = "txtContel" + x;
        tempconmob.id = "txtConmob" + x;
        tempconmob.name = "txtConmob" + x;
      }
  }

  function setgrpvals(code,desc,r){
    $("#txtCustGroup"+r).val(desc);
    $("#txtCustGroup"+r+"D").val(code);
    
    $("#myGrpModal").modal('hide');
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
    
    b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='form-control input-sm' id='txtdeladdno"+lastRow+"' name='txtdeladdno"+lastRow+"' value='' required></div>";
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