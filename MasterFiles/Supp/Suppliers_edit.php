<?php
if(!isset($_SESSION)){
  session_start();
}
$_SESSION['pageid'] = "Suppliers_edit.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');


			if(isset($_REQUEST['txtcitemno'])){
					$citemno = $_REQUEST['txtcitemno'];
			}
			else{
					$citemno = $_REQUEST['txtccode'];
				}
				
				if($citemno <> ""){
					
					$sql = "select suppliers.*, A1.cacctdesc as salescode, A2.cdesc as ewtdesc, A2.nrate as ewtrate, A1.cacctid from suppliers LEFT JOIN accounts A1 ON (suppliers.cacctcode = A1.cacctno) LEFT JOIN wtaxcodes A2 ON (suppliers.newtcode = A2.ctaxcode) where suppliers.compcode='".$_SESSION['companyid']."' and suppliers.ccode='$citemno'";
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
            $cTradeName = $row['ctradename'];
						$GroceryID = $row['cacctcode'];
            $GroceryIDCode = $row['cacctid'];
						$GroceryDesc = $row['salescode'];
						$Status = $row['cstatus'];
						
						$Type = $row['csuppliertype'];
						$Class = $row['csupplierclass'];
						$Terms = $row['cterms'];
            $PROCUREMENT = $row['procurement'];
						
            $VatType = $row['cvattype'];
            $VatRate = $row['nvatrate'];
            $EWTCode = $row['newtcode'];
            $EWTDesc = $row['ewtdesc'];
            $EWTRate = $row['ewtrate'];
			
            $Tin = $row['ctin'];

            $SelCurr = $row['cdefaultcurrency'];

						$HouseNo = $row['chouseno'];
						$City = $row['ccity'];
						$State = $row['cstate'];
						$Country = $row['ccountry'];
						$ZIP = $row['czip'];
					
						//$Contact = $row['ccontactname'];
						//$Desig = $row['cdesignation'];
						//$Email = $row['cemail'];
						//$PhoneNo = $row['cphone'];
						//$Mobile = $row['cmobile'];

					}
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

<body style="padding:5px;">
  <form name="frmSupp" id="frmSupp" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Suppliers Details  (<b>Status: <?php echo $Status; ?></b>)</legend>
          <table width="100%" border="0">
            <tr>
              <td width="150" height="150" rowspan="6"  style="vertical-align:top">
              <?php 
                if(!file_exists("../../imgsupp/".$cCustCode.".jpg") and !file_exists("../../imgsupp/".$cCustCode.".jpeg") and !file_exists("../../imgsupp/".$cCustCode.".png")){
                  $imgsrc = "../../images/emp.jpg";
                }
                else{
                  if(file_exists("../../imgsupp/".$cCustCode.".jpg")){
                    $imgsrc = "../../imgsupp/".$cCustCode.".jpg";
                  }

                  if(file_exists("../../imgsupp/".$cCustCode.".jpeg")){
                    $imgsrc = "../../imgsupp/".$cCustCode.".jpeg";
                  }

                  if(file_exists("../../imgsupp/".$cCustCode.".png")){
                    $imgsrc = "../../imgsupp/".$cCustCode.".png";
                  }
                }
              ?>
              <img src="<?php echo $imgsrc;?>" width="145" height="145" id="previewing">

              </td>
              <td width="200" style="vertical-align:middle"><b>Supplier Code</b></td>
              <td colspan="2" style="padding:2px;">
                <div class="col-xs-12 nopadding">
                  <div class="col-xs-4 nopadding">
                    <input type="text" class="required form-control input-sm" id="txtccode" name="txtccode" tabindex="1" placeholder="Input Supplier Code.."  autocomplete="off" value="<?php echo $cCustCode;?>" onKeyUp="chkSIEnter(event.keyCode,'frmSupp');" />
                  </div>
              
                  <div class="col-xs-4 nopadwleft">		
                    <div id="itmcode_err" style="padding: 5px 10px;"></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td style="vertical-align:middle"><b>Registered Name</b></td>
              <td colspan="2" style="padding:2px;"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Supplier Name.."  autocomplete="off" value="<?php echo $cCustName;?>" /></div></td>
            </tr>
            <tr>
              <td><b>Business/Trade Name</b></td>
              <td colspan="2" style="padding:2px"><div class="col-xs-8 nopadding"><input type="text" class="required form-control input-sm text-uppercase" id="txttradename" name="txttradename" tabindex="2" placeholder="Business/Trade Name.."  autocomplete="off" value="<?php echo $cTradeName;?>" /></div></td> 
            </tr>
            <tr>
              <td><b>Tin No.: </b></td>
              <td colspan="2" style="padding:2px"><div class="required col-xs-8 nopadding"><input type="text" class="form-control input-sm" id="txtTinNo" name="txtTinNo" tabindex="2" placeholder="Input Tin No.."  autocomplete="off" value="<?php echo $Tin;?>" /></div></td>
            </tr>
            <tr>
              <td><b>Address</b></td>
              <td colspan="2" style="padding:2px">
                <div class="col-xs-8 nopadwtop">
                  <input type="text" class="form-control input-sm" id="txtchouseno" name="txtchouseno" placeholder="House/Building No./Street..." autocomplete="off"  tabindex="6" value="<?php echo $HouseNo;?>" />
                </div>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="2" style="padding:2px">
                <div class="col-xs-8 nopadwtop">
                  <div class="col-xs-6 nopadding">
                    <input type="text" class="form-control input-sm" id="txtcCity" name="txtcCity" placeholder="City..." autocomplete="off" tabindex="7" value="<?php echo $City;?>" />
                  </div>
                              
                  <div class="col-xs-6 nopadwleft">
                    <input type="text" class="form-control input-sm" id="txtcState" name="txtcState" placeholder="State..." autocomplete="off" tabindex="8"  value="<?php echo $State;?>" />
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td align="center">                
                <label class="btn btn-warning btn-xs">
                  Browse Image&hellip; <input type="file" name="file" id="file" style="display: none;">
                </label>
              </td>
              <td>&nbsp;</td>
              <td colspan="2" style="padding:2px">
                <div class="col-xs-8 nopadwtop">
                  <div class="col-xs-9 nopadding">
                    <input type="text" class="form-control input-sm" id="txtcCountry" name="txtcCountry" placeholder="Country..." autocomplete="off" tabindex="9" value="<?php echo $Country;?>" />
                  </div>                    
                  <div class="col-xs-3 nopadwleft">
                    <input type="text" class="form-control input-sm" id="txtcZip" name="txtcZip" placeholder="Zip Code..." autocomplete="off" tabindex="10"  value="<?php echo $ZIP;?>" />
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="3" style="vertical-align:top"><div class="err" id="add_err"></div></td>
            </tr>
            <tr>
              <td colspan="3" style="vertical-align:top">&nbsp;
              </td>
              </tr>
          </table>

          <ul class="nav nav-tabs">
            <li class="active"><a href="#menu0">General</a></li>
            <li><a href="#menu1">Contacts List</a></li>
            <li><a href="#menu2">Addresses</a></li>
            <li><a href="#menu3">Groupings</a></li>
            <li><a href="#menu4">Accounting</a></li>
            <!--<li><a href="#menu2">Product Details</a></li>-->
          </ul>
    
          <div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 30vh;text-align: left;overflow: auto">
            <div class="tab-content">
              
              <div id="menu0" class="tab-pane fade in active" style="padding-left:30px">
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
                                      
                            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                          ?>   
                            <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$Type){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
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
                                              <option value="<?php echo $row['ccode'];?>" <?php if($row['ccode']==$Class){ echo "selected"; } ?> ><?php echo $row['cdesc']?></option>
                                              <?php
                                                      }
                                                      
                                      
                                                  ?>
                                            </select>
                                          </div>
                              </div>
                          </div>
                      </p>
                  </div>

                      <div id="menu1" class="tab-pane fade" style="padding-left:10px">
                        <p>
                          <input type="button" value="Add Contact" name="btnNewCont" id="btnNewCont" class="btn btn-primary btn-xs" onClick="addcontlist();">
                          <input name="hdncontlistcnt" id="hdncontlistcnt" type="hidden" value="0">
                          <br>
                          <table width="150%" border="0" cellpadding="2" id="myUnitTable">
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
                            <?php
                              $darrcntcts = array();
                              $qrydcntcts = "Select * From suppliers_contacts_nos where compcode = '$company'";
                              $rowdcntcts = mysqli_query($con, $qrydcntcts) or die(mysqli_error($con));
                              while($row = mysqli_fetch_array($rowdcntcts, MYSQLI_ASSOC))
                              {
                                $darrcntcts[] = array('cid' => $row['cid'], 'contct_id' => $row['customers_contacts_cid'], 'contact_type' => $row['contact_type'], 'cnumber' => $row['cnumber']);
                              }
                              
                              

                              $cntrstrx = 0;
                              $qrycontx = "Select * From suppliers_contacts where ccode = '$citemno' Order by cid";
                              $rowcontx = mysqli_query($con, $qrycontx) or die(mysqli_error($con));
                              while($row = mysqli_fetch_array($rowcontx, MYSQLI_ASSOC))
                              {
                                $cntrstrx = $cntrstrx + 1;
                            ?>
                                <tr>
                                  <td><div class="col-xs-12 nopadtopleft"><input type='text' class='required form-control input-sm' id='txtConNme<?php echo $cntrstrx;?>' name='txtConNme<?php echo $cntrstrx;?>' value='<?php echo $row['cname'];?>' ></div></td>
                                  <td><div class="col-xs-12 nopadtopleft"><input type='text' class='form-control input-sm' id='txtConDes<?php echo $cntrstrx;?>' name='txtConDes<?php echo $cntrstrx;?>' value='<?php echo $row['cdesignation'];?>'> </div></td>
                                  <td><div class="col-xs-12 nopadtopleft"><input type='text' class='form-control input-sm' id='txtConDept<?php echo $cntrstrx;?>' name='txtConDept<?php echo $cntrstrx;?>' value='<?php echo $row['cdept'];?>'> </div></td>
                                  
                                  <?php
                                    foreach($arrcontctsdet as $ckdh){
                                      $dval = "";
                                      foreach($darrcntcts as $zxc){
                                        if($ckdh['cid']==$zxc['contact_type'] && $row['cid']==$zxc['contct_id']){
                                          $dval = $zxc['cnumber'];
                                        }
                                      }
                                  ?>
                                  <td><div class="col-xs-12 nopadtopleft"><input type='text' class='form-control input-sm' id='txtConAdd<?=$ckdh['cid'].$cntrstrx;?>' name='txtConAdd<?=$ckdh['cid'].$cntrstrx;?>' value='<?=$dval?>'> </div></td>
                                  <?php
                                    }
                                  ?>
                                  <td><div class="col-xs-12 nopadtopleft"><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntrstrx;?>_delete' class='delete' value='Delete' onClick="deleteRowconts(this);"/></div></td>
                                </tr>
                            <?php
                              }
                            ?>
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
                            
                            <?php
                    $cntrstrdl = 0;
                    $qrycontdl = "Select * From suppliers_address where ccode = '$citemno' Order by nidentity";
                    $rowcontdl = mysqli_query($con, $qrycontdl) or die(mysqli_error($con));
                    while($rowdl = mysqli_fetch_array($rowcontdl, MYSQLI_ASSOC))
                    {
                      $cntrstrdl = $cntrstrdl + 1;
                  ?>
                    <tr>
                      <td><div class="col-xs-12 nopadtopleft" ><input type='text' class='required form-control input-sm' id='txtdeladdno<?php echo $cntrstrdl;?>' name='txtdeladdno<?php echo $cntrstrdl;?>' value='<?php echo $rowdl['chouseno'];?>' ></div></td>
                      <td><div class="col-xs-12 nopadtopleft" ><input type='text' class='form-control input-sm' id='txtdeladdcity<?php echo $cntrstrdl;?>' name='txtdeladdcity<?php echo $cntrstrdl;?>' value='<?php echo $rowdl['ccity'];?>'> </div></td>
                                <td><div class="col-xs-12 nopadtopleft" ><input type='text' class='form-control input-sm' id='txtdeladdstt<?php echo $cntrstrdl;?>' name='txtdeladdstt<?php echo $cntrstrdl;?>' value='<?php echo $rowdl['cstate'];?>'> </div></td>
                      <td><div class="col-xs-12 nopadtopleft" ><input type='text' class='form-control input-sm' id='txtdeladdcntr<?php echo $cntrstrdl;?>' name='txtdeladdcntr<?php echo $cntrstrdl;?>' value='<?php echo $rowdl['ccountry'];?>'> </div></td>
                      <td><div class="col-xs-12 nopadtopleft" ><input type='text' class='form-control input-sm' id='txtdeladdzip<?php echo $cntrstrdl;?>' name='txtdeladdzip<?php echo $cntrstrdl;?>' value='<?php echo $rowdl['czip'];?>'> </div></td>
                      <td><div class="col-xs-12 nopadtopleft" ><input class='btn btn-danger btn-xs' type='button' id='row_<?php echo $cntrstrdl;?>_delete' class='delete' value='Delete' onClick="deleteRowAddresss(this);"/></div></td>
                    </tr>
                  <?php
                    }
                  ?>
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
                                  <option value="<?php echo $row['ccode'];?>" <?php if($Terms==$row['ccode']){ echo "selected"; }?>><?php echo $row['cdesc']?></option>
                                  <?php
                                    }
                                  ?>
                                </select>
                              </div>
                          </div>
                          </div>

                      <div class="col-xs-7 nopadwtop">
                        <div class="col-xs-3 nopadding">
                          <b>Procurement Type</b>
                        </div>
                        <div class="col-xs-9 nopadwleft">
                          <div class="col-xs-9 nopadding">
                            <select name="procurement" id="procurement" class="form-control input-sm selectpicker" tabindex="3">
                              <option value="Services" <?= $PROCUREMENT == "Services" ? "selected" : null ?>>PURCHASE OF SERVICES</option>
                              <option value="Capital" <?= $PROCUREMENT == "Capital" ? "selected" : null ?>>PURCHASE OF CAPITAL GOODS</option>
                              <option value="Goods" <?= $PROCUREMENT == "Goods" ? "selected" : null ?>>PURCHASE OF GOODS OTHER THAN CAPITAL GOODS</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-7 nopadwtop">
                            <div class="col-xs-3 nopadding">
                              <b>Business Type</b>
                              </div>
                              
                                  <div class="col-xs-9 nopadwleft">

                                  <div class="col-xs-4 nopadding">
                                              <select id="selvattype" name="selvattype" class="form-control input-sm selectpicker"  tabindex="26">
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
                                      
                                          <div class="col-xs-1">
                                            &nbsp;
                                          </div>
                                          
                                          <div class="col-xs-2  nopadwtop">
                                            <b>Tax Rate: </b>
                                          </div>
                                          
                                          <div class="col-xs-2 nopadding">
                                            <input type="text" class="required form-control input-sm text-right" id="txttaxrate" name="txttaxrate"  tabindex="5"  autocomplete="off" value="<?php echo $VatRate;?>" />
                                          </div>
                                          <div class="col-xs-1  nopadwtop"> 
                                            <b>&nbsp;% </b>
                                          </div>
                                          
                                  </div>
                          </div>
                          
                          <div class="col-xs-7 nopadwtop">
                            <div class="col-xs-3 nopadwtop">
                              <b>EWT Code</b>
                              </div>
                              <div class="col-xs-9 nopadwleft">

                                    <div class="col-xs-7 nopadding">
                                      <input type="text" class="form-control input-sm" id="txtewt" name="txtewt"  tabindex="5" placeholder="Search EWT Description.." autocomplete="off"  value="<?php echo $EWTDesc;?>"/>
                                    </div>
                                  
                                      <div class="col-xs-3 nopadwleft">
                                          <input type="text" id="txtewtD" name="txtewtD" class="form-control input-sm" readonly value="<?php echo $EWTCode;?>"> 
                                      </div>	
                                      
                                      <div class="col-xs-2 nopadwleft">
                                          <input type="text" id="txtewtR" name="txtewtR" class="form-control input-sm" readonly value="<?php echo $EWTRate;?>">
                                      </div>	

                                </div>
                              
                        </div>

                          <div class="col-xs-7 nopadwtop">
                            <div class="col-xs-3 nopadding">
                              <b>Account Code</b>
                            </div>
                              
                            <div class="col-xs-9 nopadwleft">

                              <div class="col-xs-7 nopadding">
                                <input type="text" class="required form-control input-sm" id="txtsalesacct" name="txtsalesacct"  tabindex="5" placeholder="Search Acct Title.."  autocomplete="off" value="<?php echo $GroceryDesc;?>" />
                              </div>
                                  
                              <div class="col-xs-3 nopadwleft">
                                <input type="text" id="txtsalesacctD" name="txtsalesacctD" class="form-control input-sm" readonly value="<?php echo $GroceryIDCode;?>">
                                <input type="hidden" id="txtsalesacctDID" name="txtsalesacctDID" value="<?php echo $GroceryID;?>">
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
                                    <option value="<?=$rows['id']?>"  <?php if ($SelCurr==$rows['id']) { echo "selected='true'"; } ?> data-val="<?=$rows['rate']?>"><?=$rows['currencyName']?></option>
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
              <td>		
                <button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Suppliers.php';" id="btnMain" name="btnMain">
                  Back to Main<br>(ESC)
                </button>

                <button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Suppliers_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
            
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

  <form name="frmedit" id="frmedit" action="Suppliers_edit.php" method="POST">
    <input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $citemno; ?>">
  </form>

</body>
</html>


<script type="text/javascript">
  $(document).ready(function() {
    $("#itmcode_err").hide();
    $("#txtccode").focus();

    $(".nav-tabs a").click(function(){
      $(this).tab('show');
    });
    
    loadgroupnmes();
    chkGroupVal();  
    loadgroupvalues(); // load ung value ng group


    disabled();
    

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
											
				addpurchcost(item.id,item.value,"");


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



						$("#frmSupp").on('submit', function (e) {							
							e.preventDefault();
              var form = $("#frmSupp");

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
							  var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
                var lastRow = tbl.length-1;
                document.getElementById('hdncontlistcnt').value = lastRow;

                var tbldl = document.getElementById('myDelAddTable').getElementsByTagName('tr');
                var lastRowdl = tbldl.length-1;
                document.getElementById('hdnaddresscnt').value = lastRowdl;

                var formx = document.getElementById("frmSupp");
								var formData = new FormData(formx);

							  $.ajax({
                  type: 'post',
                  url: 'Suppliers_editsave.php',
                  data: formData,
                  contentType: false,
								  processData: false,
                  async: false,
                  beforeSend: function(){
                    $("#AlertMsg").html("<b>UPDATING SUPPLIER DETAILS: </b> Please wait a moment...");
                    $("#AlertModal").modal('show');
                  },
                  success: function (data) {
                  
                    var x = saveprodz();						
                    
                    //alert(x.trim());	
                                  
                    if(x.trim()=="True"){

                      
                      
                      if(data.trim()=="True" || data.trim()=="Size" || data.trim()=="NO"){
                        if(data.trim()=="True"){
                          $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading supplier details... <br> Please wait!");				
                        }else if(data.trim()=="Size"){
                          $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated<br><br> Invalid Image Type or Size is too big! <br><br> Loading supplier details... <br> Please wait!");				
                        }
                        else if(data.trim()=="NO"){
                          $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated <br><br> NO new image to be uploaded! <br><br> Loading supplier details... <br> Please wait!");				
                        }
                      }
                      else{
                        $("#AlertMsg").html(data);	
                      }


                    }
                    else{
                      $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved!<br><b>ERROR: </b>Product Details saving... <br><br> Loading new supplier... <br> Please wait!");
                    }
                    
                  
                        
                        setTimeout(function() {
                          $("#AlertMsg").html("");
                          $('#AlertModal').modal('hide');
                          
                          $("#txtcitemno").val($("#txtccode").val());
                          $("#frmedit").submit();
                        }, 3000); // milliseconds = 3seconds
                        
                        
                  },
                  error: function(){
                    $("#AlertMsg").html("");
                    $("#AlertModal").modal('hide');
                    
                    $("#AlertMsg").html("<b><font color='red'>ERROR: </font></b> Unable to update supplier!");
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
					data: { query: request },
					success: function (data) {
						response(data);
					}
				});
				},
				displayText: function (item) {
					return '<div style=\'width: 400px\'><span><b>' + item.id + '</b></span><br><small>' + item.name + "</small></div>";
					
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


		//Checking of uploaded file.. must be image
		$("#file").change(function() {
			$("#add_err").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','../../imgusers/preview.jpg');
				
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
							
});

	$(document).keydown(function(e) {	 

		 if(e.keyCode == 112) { //F1
			if($("#btnNew").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Suppliers_new.php';
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
				window.location.href='Suppliers.php';
			}
		  }

	});


function addpurchcost(codez,namez,crem){
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
		
	var crem = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><input type=\"text\" class=\"form-control input-xs\" id=\"txtcremarks\" name=\"txtcremarks\" placeholder=\"Enter Remarks...\" autocomplete=\"off\" value=\""+crem+"\"/></td>";
	
	var cstat = "<td style=\"padding-top: 2px\"><div class=\"col-xs-12 nopadwleft\"><button class=\"form-input btn btn-xs btn-danger\">Remove</button></div></td>";
	
	$('#myPurchTable > tbody:last-child').append('<tr>' + itmcode + itmname + crem + cstat + '</tr>');


	$("#itmerradd").attr("class","");
	$("#itmerradd").html("");
	$("#itmerradd").hide();
	
}
}

function disabled(){

	$("#frmSupp :input, label").attr("disabled", true);
	
	
	$("#txtccode").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmSupp :input, label").attr("disabled", false);
		
			
			$("#txtccode").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Suppliers_edit.php";
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
	


function saveprodz(){
//alert("Hello");

	var custcode = $("#txtccode").val();
	var result = "True"	
		//alert(custcode);
						
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
	
	//alert(result);

}

  function addcontlist(){
    var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
    var lastRow = tbl.length;

    var a=document.getElementById('myUnitTable').insertRow(-1);
    var b=a.insertCell(0);
    var c=a.insertCell(1);
    var d=a.insertCell(2);

    b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='required form-control input-xs' id='txtConNme"+lastRow+"' name='txtConNme"+lastRow+"' value='' ></div>";
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

function loadgroupvalues(){
    $(".txtCustGroup").each(function(i, obj) {
      
       var id = $(this).attr("id");
       var r = id.replace( /^\D+/g, '');

       var nme = "SuppGroup"+r;
       var citmno = $("#txtccode").val();

      $.ajax ({
            url: "../th_loadsgroupvalue.php",
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
	
	b.innerHTML = "<div class=\"col-xs-12 nopadtopleft\" ><input type='text' class='required form-control input-sm' id='txtdeladdno"+lastRow+"' name='txtdeladdno"+lastRow+"' value='' ></div>";
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