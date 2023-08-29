<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "Items_edit.php";

    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    include('../../include/access2.php');

				$company = $_SESSION['companyid'];
				//$citemno = $_REQUEST['txtcitemno'];
				//echo $citemno;
				if(isset($_REQUEST['txtcitemno'])){
						$citemno = $_REQUEST['txtcitemno'];
				}
				else{
						$citemno = $_REQUEST['txtcpartno'];
					}

				if($citemno <> ""){
					
					$sql = "select items.*, 
						A1.cacctdesc as salescode, 
						A2.cacctdesc as wrrcode, 
						A3.cacctdesc as drcode, 
						A4.cacctdesc as retcode, 
						A5.cacctdesc as cogcode,
                        A1.cacctid as salescodeid, 
						A2.cacctid as wrrcodeid, 
						A3.cacctid as drcodeid, 
						A4.cacctid as retcodeid, 
						A5.cacctid as cogcodeid
					from items 
					LEFT JOIN accounts A1 ON (items.cacctcodesales = A1.cacctno) 
					LEFT JOIN accounts A2 ON (items.cacctcodewrr = A2.cacctno) 
					LEFT JOIN accounts A3 ON (items.cacctcodedr = A3.cacctno)
					LEFT JOIN accounts A4 ON (items.cacctcoderet = A4.cacctno)
					LEFT JOIN accounts A5 ON (items.cacctcodecog = A5.cacctno) 
					where items.compcode='$company' and items.cpartno='$citemno'";
					
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

						$cItemNo = $row['cpartno']; 
                        $cSKUCode = $row['cskucode'];
						$cItemDesc = $row['citemdesc'];
						$cNotes = $row['cnotes'];
                        $cUserPic = $row['cuserpic'];

						$cUnit = $row['cunit'];
						$cClass = $row['cclass'];
						$cType = $row['ctype']; 
						$Seltax = $row['ctaxcode'];
						$cPriceType = $row['cpricetype'];
						$MarkUp = $row['nmarkup'];
                        $cTradeType = $row['ctradetype']; 
						
						$SalesCode = $row['cacctcodesales'];
						$WRRCode = $row['cacctcodewrr'];
						$DRCode = $row['cacctcodedr'];
						$SRetCode = $row['cacctcoderet']; 
						$COGCode = $row['cacctcodecog'];
						//$PRetCode = $row['cacctcodepret']; 

						$SalesCodeDesc = $row['salescode'];
						$WRRCodeDesc = $row['wrrcode'];
						$DRCodeDesc = $row['drcode'];
						$SRetCodeDesc = $row['retcode']; 
						$COGCodeDesc = $row['cogcode'];
						//$PRetCodeDesc = $row['pretcode'];
						$ChkSer = $row['lSerial'];
                        $Chkbcode = $row['lbarcode'];
                        $Chkpkgs = $row['lpack'];

                        $InvMin = $row['ninvmin'];
                        $InvMax = $row['ninvmax'];
                        $InvRePt = $row['ninvordpt'];

                        $SITyp = $row['csalestype'];

                        $SalesCodeID = $row['salescodeid'];
						$WRRCodeID = $row['wrrcodeid'];
						$DRCodeID = $row['drcodeid'];
						$SRetCodeID = $row['retcodeid']; 
						$COGCodeID = $row['cogcodeid'];
						
						//echo $ChkSer;

					}
				}

    $arrprocess = array();
    $sqlprocess = mysqli_query($con,"SELECT * FROM `items_process` WHERE compcode='$company' and cstatus='ACTIVE'"); 
    if (mysqli_num_rows($sqlprocess)!=0) {
        while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
            $arrprocess[] = $row;
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
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css"> 
    
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    
     <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/modal-center.css?v=<?php echo time();?>"> 

</head>

<body style="padding:5px; height:700px">
<input type="hidden" value='<?=json_encode(@$arrprocess)?>' id="hdnprocess">  

<form name="frmITEM" id="frmITEM"  method="post" enctype="multipart/form-data">
	<fieldset>
    	<legend>Item Details <i><font size="-1">Note: Fields with (*) are mandatory fields...</font></i></legend>

        <ul class="nav nav-tabs">
            <li class="active" id="liacc"><a href="#menu0">General</a></li>
            <li id="liacc"><a href="#menu1">Financial</a></li>
            <li id="licon"><a href="#menu2">Inventory</a></li>
            <li id="ligrp"><a href="#menu3">Groupings</a></li>
            <li id="liproc"><a href="#menu4">Processes</a></li>
        </ul>


        <div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 60vh;text-align: left;overflow: auto">
            <div class="tab-content">

                <div id="menu0" class="tab-pane fade in active" style="padding-left:10px">

                    <div class="col-xs-12" style="margin-top:10px !important">
                        <div class="col-xs-2 nopadding" align="left">
                            <?php 
                            if(!file_exists($cUserPic)){
                                $imgsrc = "../../images/emp.jpg";
                            }
                            else{
                                $imgsrc = $cUserPic;
                            }
                            ?>
                            <img src="<?php echo $imgsrc;?>" width="145" height="145" id="previewing">
                        </div>

                        <div class="col-xs-10 nopadwleft">

                            <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                    <b>Item Code*</b>
                                </div>
                                
                                <div class="col-xs-3 nopadwtop">
                                    <div class="col-xs-8 nopadwtop">
                                        <input type="text" class="form-control input-sm" id="txtcpartno" name="txtcpartno" tabindex="1" placeholder="Input Item Code.." required autocomplete="off" value="<?php echo $cItemNo;?>" maxlength="40" onKeyUp="chkSIEnter(event.keyCode,'frmITEM');" maxlength="50"/>
                                        
                                        <input type="hidden" name="hdncpartno" id="hdncpartno" value="<?php echo $cItemNo;?>">
                                    </div>
                                </div>

                                <div class="col-xs-4 nopadwtop">		
                                    <div id="itmcode_err" style="padding: 5px 10px;"></div>
                                </div>
                                
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                    <b>SKU Code</b>
                                </div>
                            
                                <div class="col-xs-6 nopadwtop">
                                    <input type="text" class="form-control input-sm" id="txtcSKU" name="txtcSKU" tabindex="1" placeholder="Input Item SKU.." autocomplete="off" value="<?php echo $cSKUCode;?>" maxlength="255"/> 
                                </div>
                                    
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                    <b>Description*</b>
                                </div>
                                
                                <div class="col-xs-6 nopadwtop">
                                    <!--<input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" value="" placeholder="Input Item Description.." required autocomplete="off" maxlength="90"/>-->

                                    <textarea class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Item Description.." required autocomplete="off" maxlength="500"><?php echo htmlentities($cItemDesc, ENT_QUOTES); ?></textarea>

                                </div>
                            
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                    <b>Notes</b>
                                </div>
                                
                                <div class="col-xs-6 nopadwtop">
                                    <!--<input type="text" class="form-control input-sm" id="txtcnotes" name="txtcnotes" tabindex="3" placeholder="Enter some notes.." autocomplete="off" value="" maxlength="90" />-->

                                    <textarea class="form-control input-sm" id="txtcnotes" name="txtcnotes" tabindex="3" placeholder="Enter some notes.." autocomplete="off"><?php echo htmlentities($cNotes, ENT_QUOTES); ?></textarea>

                                </div>
                            
                            </div>

                            <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                    <label class="btn btn-warning btn-xs">
                                        Browse Image&hellip; <input type="file" name="file" id="file" style="display: none;">
                                    </label>
                                </div>

                                <!-- <div class="col-xs-2 nopadwtop">
                                    <b>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" value="" name="chkInvn" id="chkInvn">Inventoriable
                                    </label>
                                    </b>
                                </div>-->
                                    
                            </div>

                        </div>

                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Unit of Measure</b>
                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                            <select id="seluom" name="seluom" class="form-control input-sm selectpicker"  tabindex="3">
                            <?php
                        $sql = "select * from groupings where ctype='ITMUNIT' and compcode='$company' order by cdesc";
                        $result=mysqli_query($con,$sql);
                            if (!mysqli_query($con, $sql)) {
                                printf("Errormessage: %s\n", mysqli_error($con));
                            }			
                
                            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                            ?>   
                            <option value="<?php echo $row['ccode'];?>" <?php if($cUnit==$row['ccode']){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                            <?php
                                }
                                
                
                            ?>     
                        </select>
                        </div>
                    </div>
              
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Classification</b>
                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                        <select id="selclass" name="selclass" class="form-control input-sm selectpicker"  tabindex="4">
                            <?php
                        $sql = "select * from groupings where ctype='ITEMCLS' and compcode='$company' order by cdesc";
                        $result=mysqli_query($con,$sql);
                            if (!mysqli_query($con, $sql)) {
                                printf("Errormessage: %s\n", mysqli_error($con));
                            }			
                
                            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                            ?>   
                            <option value="<?php echo $row['ccode'];?>" <?php if($cClass==$row['ccode']){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                            <?php
                                }
                                
                                
                            ?>     
                        </select>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Type</b>
                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                            <select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
                                <?php
                                    $sql = "select * from groupings where ctype='ITEMTYP' and compcode='$company' order by cdesc";
                                    $result=mysqli_query($con,$sql);
                                    if (!mysqli_query($con, $sql)) {
                                        printf("Errormessage: %s\n", mysqli_error($con));
                                    }			
                        
                                    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                    {
                                ?>   
                                    <option value="<?php echo $row['ccode'];?>" <?php if($cType==$row['ccode']){ echo "selected"; } ?>><?php echo $row['cdesc']?></option>
                                <?php
                                    }                               
                                ?>     
                            </select>
                        </div> 
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Trade Type</b>
                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                            <select id="seltradetype" name="seltradetype" class="form-control input-sm selectpicker"  tabindex="4">
                                <option value="Trade" <?=($cTradeType=="Trade") ? "selected" : ""?>>Trade</option>
                                <option value="Non-Trade" <?=($cTradeType=="Non-Trade") ? "selected" : ""?>>Non-Trade</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div id="menu1" class="tab-pane fade" style="padding-left:10px">
                    <p>
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Sales Type</b>
                        </div>
                        <div class="col-xs-4 nopadwtop">
                            <select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="4">
                                <option value="Goods" <?php if($SITyp=="Goods") { echo "selected"; } ?> >Goods</option>
                                <option value="Services" <?php if($SITyp=="Services") { echo "selected"; } ?>>Services</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Tax Code</b>
                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                        <select id="seltax" name="seltax" class="form-control input-sm selectpicker"  tabindex="4">
                            <?php
                        $sql = "select * from taxcode where compcode='$company' order by nidentity";
                        $result=mysqli_query($con,$sql);
                            if (!mysqli_query($con, $sql)) {
                                printf("Errormessage: %s\n", mysqli_error($con));
                            }           
                
                            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                            ?>   
                            <option value="<?php echo $row['ctaxcode'];?>" <?php if($Seltax==$row['ctaxcode']){ echo "selected"; } ?>> <?php echo $row['ctaxdesc'];?> - <?php echo $row['nrate']."%";?>
                            </option>
                            <?php
                                }
                                
                                
                            ?>     
                        </select>
                        </div>
                    </div>
        
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Item Pricing</b>                         

                        </div>
                        
                        <div class="col-xs-4 nopadwtop">
                            <select id="selitmpricing" name="selitmpricing" class="form-control input-sm selectpicker"  tabindex="4">
                                <option value="MU" <?php if($cPriceType=="MU") { echo "selected"; } ?>>Mark-Up %</option>
                                <option value="MUFIX" <?php if($cPriceType=="MUFIX") { echo "selected"; } ?>>Fix Mark-Up</option>
                                <option value="PM" <?php if($cPriceType=="PM") { echo "selected"; } ?>>Price Matrix</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12" id="divItmMarkUp" <?php if($cPriceType!="MU") { echo "style=\"display:none\""; } ?>>
                        <div class="col-xs-2 nopadwtop">
                        <b>Mark Up %</b>
                        </div>
                        
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="numeric form-control input-sm" id="txtcmarkUp" name="txtcmarkUp" required value="<?php echo $MarkUp;?>" autocomplete="off"> 
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <div style=" padding: 5px 10px;">
                                %
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="divItmMarkUpFix" <?php if($cPriceType!="MUFIX") { echo "style=\"display:none\""; } ?>>
                        <div class="col-xs-2 nopadwtop">
                        <b>Fix Mark Up</b>
                        </div>
                        
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="numeric form-control input-sm" id="txtcmarkUp" name="txtcmarkUp" required value="<?php echo $MarkUp;?>" autocomplete="off"> 
                        </div>
                    </div>


                    <div class="col-xs-12" style="padding-top: 10px !important">
                        <b><u>ACCOUNT CODES</u></b>
                    </div>
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Sales (AR)</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                            <input type="text" class="acctcontrol form-control input-sm" id="txtsalesacct" name="txtsalesacct" placeholder="Search Acct Title.." value="<?php echo $SalesCodeDesc; ?>" autocomplete="off">
                        
                        </div>
                        
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm" id="txtsalesacctID" name="txtsalesacctID"  readonly value="<?php echo $SalesCodeID; ?>">
                            <input type="hidden" id="txtsalesacctD" name="txtsalesacctD" value="<?php echo $SalesCode; ?>">
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Sales Return</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                            <input type="text" class="acctcontrol form-control input-sm" id="txtretacct" name="txtretacct" placeholder="Search Acct Title.." value="<?php echo $SRetCodeDesc; ?>" autocomplete="off">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm" id="txtretacctID" name="txtretacctID" value="<?php echo $SRetCodeID; ?>" readonly>
                            <input type="hidden" id="txtretacctD" name="txtretacctD" value="<?php echo $SRetCode; ?>">
                        </div>
                    </div>        
                
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Receiving (AP)</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                            <input type="text" class="acctcontrol form-control input-sm" id="txtrracct" name="txtrracct" placeholder="Search Acct Title.." value="<?php echo $WRRCodeDesc; ?>" autocomplete="off">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm" id="txtrracctID" name="txtrracctID" value="<?php echo $WRRCodeID;  ?>" readonly>
                            <input type="hidden" id="txtrracctD" name="txtrracctD" value="<?php echo $WRRCode;  ?>">
                        </div>
                    </div>
                
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>DR</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                            <input type="text" class="acctcontrol form-control input-sm" id="txtdracct" name="txtdracct" placeholder="Search Acct Title.." autocomplete="off" value="<?php echo $DRCodeDesc; ?>">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm" id="txtdracctID" name="txtdracctID" value="<?php echo $DRCodeID; ?>" readonly>
                            <input type="hidden" id="txtdracctD" name="txtdracctD" value="<?php echo $DRCode; ?>">
                        </div>
                    </div>
                        
                    <div class="col-xs-12">
                        <div class="col-xs-2 nopadwtop">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                            <input type="text" class="acctcontrol form-control input-sm" id="txtcogacct" name="txtcogacct" placeholder="Search Acct Title.." autocomplete="off" value="<?php echo $COGCodeDesc; ?>">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm" id="txtcogacctID" name="txtcogacctID" value="<?php echo $COGCodeID; ?>" readonly>
                            <input type="hidden" id="txtcogacctD" name="txtcogacctD" value="<?php echo $COGCode; ?>">
                        </div>
                    </div>
                
                    </p>
                </div>
                
                <div id="menu2" class="tab-pane fade" style="padding-left:10px">
                    <p style="padding-top:10px">

                    <div class="col-xs-12">
                        <div class="col-xs-1 nopadwtop">
                            <b>Serial No.:</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="checkbox" value="" name="chkSer" id="chkSer"  <?php if($ChkSer==1) { echo "checked"; } ?>>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                        
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <b>Barcoded.:</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="checkbox" value="" name="chkbarcoded" id="chkbarcoded"  <?php if($Chkbcode==1) { echo "checked"; } ?>>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                        
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <b>Packed</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="checkbox" value="" name="chkPack" id="chkPack"  <?php if($Chkpkgs==1) { echo "checked"; } ?>>
                        </div>
                    </div>

                    <div class="col-xs-12" style="padding-top: 10px !important">
                            <b><u>INVENTORY LEVEL</u></b>
                    </div>

                    <div class="col-xs-12">
                        <div class="col-xs-1 nopadwtop">
                            <b>Minimum:</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm text-right" id="txtInvMin" name="txtInvMin" value="<?php echo $InvMin; ?>">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                        </div>


                        <div class="col-xs-1 nopadwtop">
                        <b>Maximum:</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm text-right" id="txtInvMax" name="txtInvMax" value="<?php echo $InvMax; ?>" >
                        </div>
                        <div class="col-xs-1 nopadwtop">
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <b>ReOrder Pt.:</b>
                        </div>
                        <div class="col-xs-1 nopadwtop">
                            <input type="text" class="form-control input-sm text-right" id="txtInvRO" name="txtInvRO" value="<?php echo $InvRePt; ?>" >
                        </div>
                    </div>
                    
                    <div class="col-xs-12" style="padding-top: 10px !important">
                            <b><u>CONVERTION FACTOR</u></b>
                    </div>

                    <input type="button" value="Add Convertion" name="btnaddunit" id="btnaddunit" class="btn btn-primary btn-xs" onClick="addunitconv();">
                    
                    <input name="hdnunitrowcnt" id="hdnunitrowcnt" type="hidden" value="0">
                    <br>
                        <table width="50%" border="0" cellpadding="2" id="myUnitTable">
                        <tr>
                            <th scope="col" width="120">UNIT</th>
                            <th scope="col">FACTOR<br><i>(qty/smallest unit)</i></th>
                            <th scope="col" width="80">STATUS</th>
                        </tr>
                        </table>
                    </p>        
                </div>
                    
                <div id="menu3" class="tab-pane fade" style="padding-left:10px">
                    <p>
                    <div class="col-xs-12">
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup1">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                        <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup1" name="txtcGroup1" tabindex="11" placeholder="Search Group 1..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup1D" name="txtcGroup1D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup1"><i class="fa fa-search"></i></button>
                        </div>
                        
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup6">
                            <b>Cost of Goods</b>
                        </div>
                
                
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                        <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup6" name="txtcGroup6" tabindex="11" placeholder="Search Group 6..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>

                            <input type="hidden" id="txtcGroup6D" name="txtcGroup6D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button"  id="btncGroup6"><i class="fa fa-search"></i></button>
                        </div>
                
                    </div>
                
                    <div class="col-xs-12">
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup2">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup2" name="txtcGroup2" tabindex="11" placeholder="Search Group 2..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                        
                            <input type="hidden" id="txtcGroup2D" name="txtcGroup2D">
                        </div>
                
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button"  id="btncGroup2"><i class="fa fa-search"></i></button>
                        </div>
                        
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup7">
                            <b>Cost of Goods</b>
                        </div>
                
                
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup7" name="txtcGroup7" tabindex="11" placeholder="Search Group 7..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup7D" name="txtcGroup7D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup7"><i class="fa fa-search"></i></button>
                        </div>
                
                    </div>
                
                    <div class="col-xs-12">
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup3">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup3" name="txtcGroup3" tabindex="11" placeholder="Search Group 3..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup3D" name="txtcGroup3D">
                        </div>
                
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup3"><i class="fa fa-search"></i></button>
                        </div>
                        
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup8">
                            <b>Cost of Goods</b>
                        </div>
                
                
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup8" name="txtcGroup8" tabindex="11" placeholder="Search Group 8..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                        
                            <input type="hidden" id="txtcGroup8D" name="txtcGroup8D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup8"><i class="fa fa-search"></i></button>
                        </div>
                
                    </div>
                
                    <div class="col-xs-12">
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup4">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup4" name="txtcGroup4" tabindex="11" placeholder="Search Group 4..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup4D" name="txtcGroup4D">
                        </div>
                
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup4"><i class="fa fa-search"></i></button>
                        </div>
                        
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup9">
                            <b>Cost of Goods</b>
                        </div>
                
                
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup9" name="txtcGroup9" tabindex="11" placeholder="Search Group 9..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                        
                            <input type="hidden" id="txtcGroup9D" name="txtcGroup9D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup9"><i class="fa fa-search"></i></button>
                        </div>
                
                    </div>
                
                    <div class="col-xs-12">
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup5">
                            <b>Cost of Goods</b>
                        </div>
                        
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                        <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup5" name="txtcGroup5" tabindex="11" placeholder="Search Group 5..">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup5D" name="txtcGroup5D">
                        </div>
                
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup5"><i class="fa fa-search"></i></button>
                        </div>
                        
                        <div class="cgroup col-xs-2 nopadwtop" id="cGroup10">
                            <b>Cost of Goods</b>
                        </div>
                
                
                        <div class="col-xs-3 nopadwtop">
                        <div class="btn-group btn-group-justified nopadding">
                        <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup10" name="txtcGroup10" tabindex="11" placeholder="Search Group 10...">
                        <span class="searchclear glyphicon glyphicon-remove-circle"></span>
                        </div>
                            
                            <input type="hidden" id="txtcGroup10D" name="txtcGroup10D">
                        </div>
                
                        <div class="col-xs-1 nopadwtop">
                            &nbsp;
                            <button class="btncgroup btn btn-sm btn-danger" type="button" id="btncGroup10"><i class="fa fa-search"></i></button>
                        </div>
                
                    </div>
                
                
                    </p>
                </div>

                <div id="menu4" class="tab-pane fade" style="padding-left:2 0px">
                    <p>

                        <div class="col-xs-12">
                            <input type="button" value="Add Process" name="btnaddprocess" id="btnaddprocess" class="btn btn-primary btn-xs" onClick="addprocess();">
                                
                            <input name="hdnprocesslist" id="hdnprocesslist" type="hidden" value="0">

                            <table width="50%" border="0" cellpadding="2" id="myProcessTable" style="margin-top: 10px;">
                                <tr>
                                    <th scope="col">PROCESS</th>
                                    <th scope="col" width="80">STATUS</th>
                                </tr>

                                <?php
                                    $cbtr = 0;
                                    $sqlprocess = mysqli_query($con,"SELECT * FROM `items_process_t` WHERE compcode='$company' and citemno='$citemno'"); 
                                    if (mysqli_num_rows($sqlprocess)!=0) {
                                        while($row = mysqli_fetch_array($sqlprocess, MYSQLI_ASSOC)){
                                            $cbtr++;
                                ?>

                                    <tr>
                                            <td>
                                                <div id='divselproc<?=$cbtr?>' class="col-xs-12 nopadwright">
                                                    <select name='selproc<?=$cbtr?>' id='selproc<?=$cbtr?>' class='form-control input-sm selectpicker'>
                                                    <?php
                                                        foreach(@$arrprocess as $xcv){
                                                            $xselec = "";
                                                            if($xcv['nid']==$row['items_process_id']){
                                                                $xselec = " selected";
                                                            }

                                                            echo "<option value='".$xcv['nid']."'".$xselec."> ".$xcv['cdesc']." </option>";
                                                        }
                                                    ?>
                                                    </select>
                                                </div>  

                                            </td>
                                            <td>
                                                <input class='btn btn-danger btn-xs' type='button' id='row_<?=$cbtr?>_delete' class='delete' value='Delete' onClick="delProcRow(this);"/>
                                            </td>
                                    </tr>

                                <?php
                                        }
                                    }
                                ?>
                            </table>
                        </div>
                        
                    </p>
                </div>

            </div>
        </div>

<br>
<table width="100%" border="0" cellpadding="3">
  <tr>
    <td>
		<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Items.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>

    	<button type="button" class="btn btn-default btn-sm" onClick="window.location.href='Items_new.php';" id="btnNew" name="btnNew">New<br>(F1)</button>
 
     <button type="button" class="btn btn-danger btn-sm" onClick="chkSIEnter(13,'frmITEM');" id="btnUndo" name="btnUndo">
Undo Edit<br>(CTRL+Z)
    </button>
   
        <button type="button" class="btn btn-warning btn-sm" onClick="enabled();" id="btnEdit" name="btnEdit"> Edit<br>(CTRL+E) </button>

    	<button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button>
    
    </td>
  </tr>
</table>
</fieldset>
</form>


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


<form name="frmedit" id="frmedit" action="Items_edit.php" method="POST">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cItemNo;?>">
</form>
</body>
</html>



<script type="text/javascript">
$(document).ready(function(){
	loadgroupnmes();
	chkGroupVal();	
	loadgroupvalues(); // load ung value ng group
	loaditmfactor(); // load ung item factor TAB
	
	disabled();
	
	$(".nav-tabs a").click(function(){
        $(this).tab('show');
    });
	
	$("input.numeric").numeric({decimalPlaces: 2});
	$("input.numeric").on("click", function () {
		$(this).select();
	});


});


$(function(){
	$("#addGrp_err").hide();
	$("#itmcode_err").hide();
	$("#txtcpartno").focus();
	
					var $input = $(".acctcontrol");
					
				  	$input.typeahead({						 
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
									
							var id = $(document.activeElement).attr('id');
							//alert(id);	
							
							$('#'+id).val(item.name).change(); 
							$('#'+id+'D').val(item.idcode);
                            $('#'+id+'ID').val(item.id);
							
						}
					});
					
					$(".acctcontrol, .txtcgroup").on("blur", function(){
						var x = $(this).attr("id");
						
						if( $("#"+x+"ID").val()==""){
							$(this).val("").change();
                            $("#"+x+"D").val("");
						}
						
					});
					
					
					var $inputgrp = $(".txtcgroup");
				  	$inputgrp.typeahead({						 
						autoSelect: true,
						source: function(request, response) {	
							$.ajax({								
								url: "th_groupdetails.php?id=cGroup"+$(document.activeElement).attr('id').replace( /^\D+/g, ''),
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
						
						
							var nme = "cGroup"+r;
							
							$.ajax ({
							url: "th_loadgrpdetails.php",
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
					var form0 = $("#frmITEM");	
					if (form0.find('.required').filter(function(){ return this.value === '' }).length > 0) {
						e.preventDefault();
						

						  $("#home").attr("class", "tab-pane fade"); 
						  $("#menu1").attr("class", "tab-pane fade in active");
						  $("#menu2").attr("class", "tab-pane fade");
						  $("#menu3").attr("class", "tab-pane fade");

						  $("#ligen").attr("class", ""); 
						  $("#liacc").attr("class", "active");
						  $("#licon").attr("class", ""); 
						  $("#ligrp").attr("class", ""); 
						
						alert("Check Account Codes...");
						return false;
						
						
					}else{
						var submit = true;
                            var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
                            var lastRow = tbl.length-1;
											
						    document.getElementById('hdnunitrowcnt').value = lastRow;

                            var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
                            var lastRow = tbl.length-1;
                                                
                            document.getElementById('hdnprocesslist').value = lastRow;

							e.preventDefault();							
							//submit form objects to ajax:
							
							  var form = document.getElementById("frmITEM");
								var formData = new FormData(form);

							  $.ajax({
								type: 'post',
								url: 'items_editsave.php',
								data: formData,
								contentType: false,
								processData: false,
								async:false,
								beforeSend: function(){
								  	$("#AlertMsg").html("<b>UPDATING ITEM: </b> Please wait a moment...");
										$("#AlertModal").modal('show');
								},
								success: function(data) {

										if(data.trim()=="True" || data.trim()=="Size" || data.trim()=="NO"){
	
											if(data.trim()=="True"){
									 			$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated! <br><br> Loading item details... <br> Please wait!");				
											}else if(data.trim()=="Size"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated<br><br> Invalid Image Type or Size is too big! <br><br> Loading item details... <br> Please wait!");				
											}
											else if(data.trim()=="NO"){
												$("#AlertMsg").html("<b>SUCCESS: </b>Succesfully updated <br><br> NO new image to be uploaded! <br><br> Loading item details... <br> Please wait!");				
											}
											
											setTimeout(function() {
											  $("#AlertMsg").html("");
											  $('#AlertModal').modal('hide');
											  
											  $("#txtcitemno").val($("#txtcpartno").val());
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
									
							  		$("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to updateitem!");
									$("#itmcode_err").show();
								  
								}
							  });	
					}
					});										



					$(".searchclear").on("click", function() {
						var cid = $(this).prev('input').attr('id');
						
						$("#"+cid).val('');
						$("#"+cid+"D").val('');
					});
					
					
					$("#selitmpricing").on("change", function() {
						var xy = $(this).val();

						if(xy=="PM"){
							$("#divItmMarkUp").hide();
                            $("#divItmMarkUpFix").hide();
						}else if(xy=="MU"){
							$("#divItmMarkUp").show();
                            $("#divItmMarkUpFix").hide();						
						}else if(xy=="MUFIX"){
							$("#divItmMarkUpFix").show();	
                            $("#divItmMarkUp").hide();						
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
				window.location.href='Items_new.php';
			}
		  }
		  else if(e.keyCode == 83 && e.ctrlKey){//CTRL+S
			if($("#btnSave").is(":disabled")==false){
				e.preventDefault();
				$("#btnSave").click();
			}
		  }
		  else if(e.keyCode == 69 && e.ctrlKey){//CTRL+E
			if($("#btnEdit").is(":disabled")==false){
				e.preventDefault();
				enabled();
			}
		  }
		  else if(e.keyCode == 90 && e.ctrlKey){//CTRL+Z
			if($("#btnUndo").is(":disabled")==false){
				e.preventDefault();
				chkSIEnter(13,'frmedit');
			}
		  }
		  else if(e.keyCode == 27){//ESC	  
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Items.php';
			}
		  }

	});

function setgrpvals(code,desc,r){
	$("#txtcGroup"+r).val(desc);
	$("#txtcGroup"+r+"D").val(code);
	
	$("#myGrpModal").modal('hide');
}

function addunitconv(){
	var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
	var lastRow = tbl.length;

	var a=document.getElementById('myUnitTable').insertRow(-1);
	var u=a.insertCell(0);
	var v=a.insertCell(1);
	v.align = "left";
	v.style.padding = "1px";
	var y=a.insertCell(2);
	
	u.innerHTML = "<div id='divselunit"+lastRow+"' class=\"col-xs-12 nopadwright\"></div>";
	v.innerHTML = "<div class=\"col-xs-10 nopadwleft\" ><input type='text' class='form-control input-sm' id='txtfactor"+lastRow+"' name='txtfactor"+lastRow+"' value='1' required style=\"text-align: right\"> </div>";
	y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRow(this);\"/>";
	
	addselect(lastRow);
									$("input.numeric").numeric({decimalPlaces: 2});
									$("input.numeric").on("click", function () {
									   $(this).select();
									});

}

function addselect(nme){
        var xmlhttp;
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
            var res=xmlhttp.responseText;
            document.getElementById("divselunit"+nme).innerHTML=res;
            }
          }
        xmlhttp.open("GET","get_uom.php?x="+nme,true);
        xmlhttp.send();
        }
		
		
function deleteRow(r) {
	var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
	var lastRow = tbl.length;
	var i=r.parentNode.parentNode.rowIndex;
	 document.getElementById('myUnitTable').deleteRow(i);
	 var lastRow = tbl.length;
	 var z; //for loop counter changing textboxes ID;
	 
		for (z=i+1; z<=lastRow; z++){
			var tempcitemno = document.getElementById('selunit' + z);
			var tempcdesc = document.getElementById('txtfactor' + z);
			var tempnqty= document.getElementById('txtpurch' + z);
			var tempcunit= document.getElementById('txtretail' + z);
			
			var x = z-1;
			tempcitemno.id = "selunit" + x;
			tempcitemno.name = "selunit" + x;
			tempcdesc.id = "txtfactor" + x;
			tempcdesc.name = "txtfactor" + x;
			tempnqty.id = "txtpurch" + x;
			tempnqty.name = "txtpurch" + x;
			tempcunit.id = "txtretail" + x;
			tempcunit.name = "txtretail" + x;

		}
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
					$("#cGroup"+r).html("<b>" + result + "</b>");
				}
				else {
					$("#cGroup"+r).html("<b>Group " + r + "</b>");
				}
            }
    		});
	});
}

function chkGroupVal(){
	$(".txtcgroup").each(function(i, obj) {
		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

			var nme = "cGroup"+r;
			
			$.ajax ({
            url: "../th_checkexistgroup.php",
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
		$(".txtcgroup").each(function(i, obj) {
			
		   var id = $(this).attr("id");
		   var r = id.replace( /^\D+/g, '');

		   var nme = "cGroup"+r;
		   var citmno = $("#txtcpartno").val();
			
			$.ajax ({
            url: "../th_loadgroupvalue.php",
			data: { id: r, grpno: nme, itm: citmno },
			dataType: 'json',
            success: function(data) {
				console.log(data);
				$.each(data,function(index,item){
					
				  if(item.id!=""){				  
					$("#txtcGroup"+r).val(item.name);
					$("#txtcGroup"+r+"D").val(item.id);
				  }
									  
							
				});

            }
    		});

	});

}

function loaditmfactor(){
	var itmno = $("#txtcpartno").val();
	
			$.ajax ({
            url: "th_loaditemfactor.php",
			data: { id: itmno },
			dataType: 'json',
			async: false,
            success: function(data) {
				
				console.log(data);
				$.each(data,function(index,item){
					
					  if(item.cstatus=="ACTIVE"){
						 var varstat = "<input class='setStat btn btn-success btn-xs' type='button' value='Active' id=\"setStat"+item.cunit+"\" name= \"setStat"+item.cunit+"\" />";
					  }else{
						 var varstat = "<input class='btn btn-warning btn-xs' type='button' value='Inactive' id=\"setStat"+item.cunit+"\" name= \"setStat"+item.cunit+"\" />";
					  }


						var tbl = document.getElementById('myUnitTable').getElementsByTagName('tr');
						var lastRow = tbl.length;
					
						var a=document.getElementById('myUnitTable').insertRow(-1);
						var u=a.insertCell(0);
						var v=a.insertCell(1);
						v.align = "left";
						v.style.padding = "1px";
						var y=a.insertCell(2);
						var z=a.insertCell(3);
						
						u.innerHTML = "<div class=\"col-xs-12 nopadwright\" ><input type=\"text\" class='form-control input-sm' name=\"selunit"+lastRow+"\" id=\"selunit"+lastRow+"\" value=\""+item.cunit+"\" readonly></div>";
						v.innerHTML = "<div class=\"col-xs-10 nopadwleft\" > <input type='text' class='form-control input-sm' id='txtfactor"+lastRow+"' name='txtfactor"+lastRow+"' value='"+item.nfactor+"' required style=\"text-align: right\"> </div>";
						
						y.innerHTML = varstat;
						z.innerHTML = "<div class=\"alert alert-danger nopadding\" id=\"itmunitmsg"+item.cunit+"\" style=\"display: inline\"></div>";
						
						//addselect(lastRow, item.cunit);
						
						$("#itmunitmsg"+item.cunit).hide();
						
						$("#setStat"+item.cunit).on("click", function() {
																				
							var stat = $("#setStat"+item.cunit).val();
							var unitz = item.cunit;
							
								$.ajax ({
								url: "th_itmsetuomstat.php",
								data: { code: itmno, stat: stat.toUpperCase(), uom: unitz },
								async: false,
								dataType: "text",
								success: function( data ) {
				
									if(data.trim() != "True"){
										$("#itmunitmsg"+unitz).html("<b>Error: </b>"+ data);
										$("#itmunitmsg"+unitz).attr("class", "itmalert alert alert-danger nopadding")
										$("#itmunitmsg"+unitz).show();
									}
									else{
									  if(stat.toUpperCase()=="ACTIVE"){
											$("#setStat"+item.cunit).attr("class","btn btn-warning btn-xs"); 
											$("#setStat"+item.cunit).val("Inactive");
											
											$("#itmunitmsg"+unitz).html("<b>SUCCESS: </b> Status changed to INACTIVE");
										
									  }else{
											$("#setStat"+item.cunit).attr("class","btn btn-success btn-xs"); 
											$("#setStat"+item.cunit).val("Active");
											
											$("#itmunitmsg"+unitz).html("<b>SUCCESS: </b> Status changed to ACTIVE");
										 
									  }
										
										$("#itmunitmsg"+unitz).attr("class", "itmalert alert alert-success nopadding")
										$("#itmunitmsg"+unitz).show();
									}
								}
							
							});

						});
							
				});

            }
    		});
	
}

function chkSIEnter(keyCode,frm){
	if(keyCode==13){
		document.getElementById(frm).action = "Items_edit.php";
		document.getElementById(frm).submit();
	}
}


function disabled(){

	$("#frmITEM :input, label").attr("disabled", true);
	
	
	$("#txtcpartno").attr("disabled", false);
	$("#btnMain").attr("disabled", false);
	$("#btnNew").attr("disabled", false);
	$("#btnEdit").attr("disabled", false);

}

function enabled(){

		$("#frmITEM :input, label").attr("disabled", false);
		
			
			$("#txtcpartno").val($("#hdncpartno").val());
			$("#txtcpartno").attr("readonly", true);
			$("#btnMain").attr("disabled", true);
			$("#btnNew").attr("disabled", true);
			$("#btnEdit").attr("disabled", true);
			
			$("#txtcdesc").focus();

}

	//preview of image
	function imageIsLoaded(e) {
		$("#file").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '145px');
		$('#previewing').attr('height', '145px');
	};

    function addprocess(){
        var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
        var lastRow = tbl.length;

        var a=document.getElementById('myProcessTable').insertRow(-1);
        var u=a.insertCell(0);
        var y=a.insertCell(1);

        var xz = $("#hdnprocess").val();
		prooptions = "";
		$.each(jQuery.parseJSON(xz), function() { 
			prooptions = prooptions + "<option value='"+this['nid']+"'>"+this['cdesc']+"</option>";
		});
        
        u.innerHTML = "<div id='divselproc"+lastRow+"' class=\"col-xs-12 nopadwright\"><select name='selproc"+lastRow+"' id='selproc"+lastRow+"' class='form-control input-sm selectpicker'>"+prooptions+"</select></div>";
        y.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"delProcRow(this);\"/>";

    }

    function delProcRow(r) {
        var tbl = document.getElementById('myProcessTable').getElementsByTagName('tr');
        var lastRow = tbl.length;
        var i=r.parentNode.parentNode.rowIndex;
        document.getElementById('myProcessTable').deleteRow(i);
        var lastRow = tbl.length;
        var z; //for loop counter changing textboxes ID;
        
            for (z=i+1; z<=lastRow; z++){
                var tempcitemno = document.getElementById('selproc' + z);
                
                var x = z-1;
                tempcitemno.id = "selproc" + x;
                tempcitemno.name = "selproc" + x;

            }
    }


</script>
