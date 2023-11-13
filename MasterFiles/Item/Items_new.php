<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "Items_new.php";

    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    include('../../include/access2.php');


    $company = $_SESSION['companyid'];

	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='DEFMRKUP'"); 
					
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$nvalue = $all_course_data['cvalue']; 							
	}
	else{
		$nvalue = "";
	}

    @$arrsecslist = array();
    $getfctrs = mysqli_query($con,"SELECT * FROM `locations` where compcode='$company' and cstatus='ACTIVE' order By nid"); 
	if (mysqli_num_rows($getfctrs)!=0) {
		while($row = mysqli_fetch_array($getfctrs, MYSQLI_ASSOC)){
			@$arrsecslist[] = array('nid' => $row['nid'], 'cdesc' => $row['cdesc']); 
		}
	}

    $arruoms = array();
    $resuom = mysqli_query($con,"SELECT * FROM `groupings` WHERE compcode='$company' and cstatus='ACTIVE' and ctype='ITMUNIT'"); 					
	$rowdetsecs = $resuom->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetsecs as $row0){
		$arruoms[] = array('ccode' => $row0['ccode'], 'cdesc' => $row0['cdesc']);
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

<body style="padding:5px; height:700px"> 

    <input type="hidden" value='<?=json_encode(@$arrsecslist)?>' id="hdnsecslist"> 
    <input type="hidden" id="hdnuomlist" value='<?=json_encode($arruoms)?>'>

    <form name="frmITEM" id="frmITEM" method="post">
        <fieldset>
            <legend>New Item <i><font size="-1">Note: Fields with (*) are mandatory fields...</font></i></legend>
            

        
            <ul class="nav nav-tabs">
                <li class="active" id="liacc"><a href="#menu0">General</a></li>
                <li id="liacc"><a href="#menu1">Financial</a></li>
                <li id="licon"><a href="#menu2">Inventory</a></li>
                <li id="ligrp"><a href="#menu3">Groupings</a></li>
            </ul>
    

            <div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 60vh;text-align: left;overflow: auto">
                <div class="tab-content">

                    <div id="menu0" class="tab-pane fade in active" style="padding-left:10px">

                        <div class="col-xs-12" style="margin-top:10px !important">
                            <div class="col-xs-2 nopadwtop">
                                <b>Item Code*</b>
                            </div>
                        
                            <div class="col-xs-3 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtcpartno" name="txtcpartno" tabindex="1" placeholder="Input Item Code.." required autocomplete="off" maxlength="50"/>     
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
                                <input type="text" class="form-control input-sm" id="txtcSKU" name="txtcSKU" tabindex="1" placeholder="Input Item SKU.." autocomplete="off" maxlength="255" /> 
                            </div>
                                
                        </div>

                        <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                <b>Description*</b>
                            </div>
                            
                            <div class="col-xs-6 nopadwtop">
                            <!-- <input type="text" class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Item Description.." required autocomplete="off" />-->

                                <textarea class="form-control input-sm" id="txtcdesc" name="txtcdesc" tabindex="2" placeholder="Input Item Description.." required autocomplete="off" maxlength="500"></textarea>
                            </div>        
                        </div>

                        <div class="col-xs-12">
                                <div class="col-xs-2 nopadwtop">
                                <b>Notes</b>
                            </div>
                            
                            <div class="col-xs-6 nopadwtop">
                                <!--<input type="text" class="form-control input-sm" id="txtcnotes" name="txtcnotes" tabindex="3" placeholder="Enter some notes.." autocomplete="off" />-->

                                        <textarea class="form-control input-sm" id="txtcnotes" name="txtcnotes" tabindex="3" placeholder="Enter some notes.." autocomplete="off"></textarea>
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
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
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
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
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
                                <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
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
                                    <option value="Trade">Trade</option>
                                    <option value="Non-Trade">Non-Trade</option>
                                </select>
                            </div>
                        </div>
                    
                    </div>              

                    <div id="menu1" class="tab-pane fade in" style="padding-left:10px">
                        <p>
                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>Sales Type</b>
                            </div>
                            <div class="col-xs-4 nopadwtop">
                                <select id="selsityp" name="selsityp" class="form-control input-sm selectpicker"  tabindex="4">
                                    <option value="Goods">Goods</option>
                                    <option value="Services">Services</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>VAT Code</b>
                            </div>
                            
                            <div class="col-xs-4 nopadwtop">
                            <select id="seltax" name="seltax" class="form-control input-sm selectpicker"  tabindex="4">
                                <?php
                                    $sql = "select * from vatcode where compcode='$company' and cstatus='ACTIVE' order by nidentity";
                                    $result=mysqli_query($con,$sql);
                                if (!mysqli_query($con, $sql)) {
                                    printf("Errormessage: %s\n", mysqli_error($con));
                                }           
                    
                                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                    {
                                ?>   
                                <option value="<?php echo $row['cvatcode'];?>"> <?php echo $row['cvatdesc'];?> - <?php echo number_format($row['nrate'])."%";?>
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
                                    <option value="MU">Mark-Up %</option>
                                    <option value="MUFIX">Fix Mark-Up</option>                    
                                    <option value="PM">Price Matrix</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-xs-12" id="divItmMarkUp">
                            <div class="col-xs-2 nopadwtop">
                            <b>Mark Up %</b>
                            </div>
                            
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="numeric form-control input-sm" id="txtcmarkUp" name="txtcmarkUp" required value="<?php echo $nvalue;?>" autocomplete="off"> 
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <div style=" padding: 5px 10px;">
                                    %
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" id="divItmMarkUpFix" style="display:none">
                            <div class="col-xs-2 nopadwtop">
                            <b>Fix Mark Up</b>
                            </div>
                            
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="numeric form-control input-sm" id="txtcmarkUp" name="txtcmarkUp" required value="<?php echo $nvalue;?>" autocomplete="off"> 
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
                                <input type="text" class="acctcontrol form-control input-sm" id="txtsalesacct" name="txtsalesacct" placeholder="Search Acct Title.." autocomplete="off">
                            
                            </div>
                            
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtsalesacctID" name="txtsalesacctID"  readonly>
                                <input type="hidden" id="txtsalesacctD" name="txtsalesacctD">
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>Sales Return</b>
                            </div>
                            
                            <div class="col-xs-3 nopadwtop">
                                <input type="text" class="acctcontrol form-control input-sm" id="txtretacct" name="txtretacct" placeholder="Search Acct Title.." autocomplete="off">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtretacctID" name="txtretacctID"  readonly>
                                <input type="hidden" id="txtretacctD" name="txtretacctD">
                            </div>
                        </div>        
                    
                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>Receiving (AP)</b>
                            </div>
                            
                            <div class="col-xs-3 nopadwtop">
                                <input type="text" class="acctcontrol form-control input-sm" id="txtrracct" name="txtrracct" placeholder="Search Acct Title.." autocomplete="off">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtrracctID" name="txtrracctID"  readonly>
                                <input type="hidden" id="txtrracctD" name="txtrracctD">
                            </div>
                        </div>
                    
                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>DR</b>
                            </div>
                            
                            <div class="col-xs-3 nopadwtop">
                                <input type="text" class="acctcontrol form-control input-sm" id="txtdracct" name="txtdracct" placeholder="Search Acct Title.." autocomplete="off">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtdracctID" name="txtdracctID"  readonly>
                                <input type="hidden" id="txtdracctD" name="txtdracctD">
                            </div>
                        </div>
                            
                        <div class="col-xs-12">
                            <div class="col-xs-2 nopadwtop">
                                <b>Cost of Goods</b>
                            </div>
                            
                            <div class="col-xs-3 nopadwtop">
                                <input type="text" class="acctcontrol form-control input-sm" id="txtcogacct" name="txtcogacct" placeholder="Search Acct Title.." autocomplete="off">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <input type="text" class="form-control input-sm" id="txtcogacctID" name="txtcogacctID"  readonly>
                                <input type="hidden" id="txtcogacctD" name="txtcogacctD"  readonly>
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
                                <input type="checkbox" value="" name="chkSer" id="chkSer">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                            
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <b>Barcoded.:</b>
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <input type="checkbox" value="" name="chkbarcoded" id="chkbarcoded">
                            </div>
                            <div class="col-xs-1 nopadwtop">
                            
                            </div>
                            <div class="col-xs-2 nopadwtop">
                                <b>Non-Inventoriable</b>
                            </div>
                            <div class="col-xs-1 nopadwtop">
                                <!--<input type="checkbox" value="" name="chkPack" id="chkPack">-->
                                <input type="checkbox" value="" name="chkInventoriable" id="chkInventoriable">
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-top: 10px !important">
                            <b><u>INVENTORY LEVEL</u></b>
                        </div>

                        <div class="col-xs-10">
                            <input type="button" value="Add Section" name="btnaddunit" id="btnaddunit" class="btn btn-primary btn-xs" onClick="addseclevel();">
                            
                            <input name="hdnseclvlrowcnt" id="hdnseclvlrowcnt" type="hidden" value="0">
                            <br>
                            <table width="80%" class="table table-sm table-bordered" id="mySecLevelTbl" style="margin-top:5px !important"> 
                                <tr>
                                    <th scope="col">Section</th>
                                    <th scope="col" width="120">Minimum</th>
                                    <th scope="col" width="120">Maximum</th>
                                    <th scope="col" width="120">Reorder Pt.</th>
                                    <th scope="col" width="80">&nbsp;</th>
                                </tr>
                            </table>
                        </div>

                        <div class="col-xs-12" style="padding-top: 10px !important">
                                <b><u>CONVERTION FACTOR</u></b>
                        </div>

                        <div class="col-xs-6">
                            <input type="button" value="Add Convertion" name="btnaddunit" id="btnaddunit" class="btn btn-primary btn-xs" onClick="addunitconv();">
                                    
                            <input name="hdnunitrowcnt" id="hdnunitrowcnt" type="hidden" value="0">
                            <br>
                            <table width="50%" class="table table-sm table-bordered" id="myUnitTable" style="margin-top:5px !important">
                                <tr>
                                    <th scope="col" width="120">UNIT</th>
                                    <th scope="col" width="80">RULE<br></th>
                                    <th scope="col">FACTOR<br></th>
                                    <th scope="col" class="text-center">PO UOM<br></th>
                                    <th scope="col" class="text-center">SALES UOM<br></th>
                                    <th scope="col" class="text-center" width="80">STATUS</th>
                                </tr>
                            </table>
                        </div>

                        </p>        
                    </div>
                        
                    <div id="menu3" class="tab-pane fade" style="padding-left:30px">
                        <p>
                        <div class="col-xs-12">
                            <div class="cgroup col-xs-2 nopadwtop" id="cGroup1">
                                <b>Cost of Goods</b>
                            </div>
                            
                            <div class="col-xs-3 nopadwtop">
                            <div class="btn-group btn-group-justified nopadding">
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup1" name="txtcGroup1" tabindex="11" placeholder="Search Group 1.." autocomplete="off">
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
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup6" name="txtcGroup6" tabindex="11" placeholder="Search Group 6.."  autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup2" name="txtcGroup2" tabindex="11" placeholder="Search Group 2.." autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup7" name="txtcGroup7" tabindex="11" placeholder="Search Group 7.." autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup3" name="txtcGroup3" tabindex="11" placeholder="Search Group 3.." autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup8" name="txtcGroup8" tabindex="11" placeholder="Search Group 8.." autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup4" name="txtcGroup4" tabindex="11" placeholder="Search Group 4.." autocomplete="off">
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
                                <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup9" name="txtcGroup9" tabindex="11" placeholder="Search Group 9.." autocomplete="off">
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
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup5" name="txtcGroup5" tabindex="11" placeholder="Search Group 5.." autocomplete="off">
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
                            <input type="text" class="txtcgroup form-control input-sm" id="txtcGroup10" name="txtcGroup10" tabindex="11" placeholder="Search Group 10..." autocomplete="off">
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

                    

                </div>
            </div>

            <br>

            <table width="100%" border="0" cellpadding="3">
            <tr>
                <td>
                <button type="button" class="btn btn-primary btn-sm" onClick="window.location.href='Items.php';" id="btnMain" name="btnMain">Back to Main<br>(ESC)</button>
                
                <button type="submit" class="btn btn-success btn-sm" name="btnSave" id="btnSave">Save<br> (CTRL+S)</button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
        <input type="hidden" name="txtcitemno" id="txtcitemno" value="">
    </form>

</body>
</html>



<script type="text/javascript">

    $(document).ready(function(){
        loadgroupnmes();
        chkGroupVal();	
        
        $(".nav-tabs a").click(function(){
            $(this).tab('show');
        });
        
        $("input.numeric").numeric({decimalPlaces: 2});
        $("input.numeric").on("click", function () {
            $(this).select();
        });


    });

    $(document).keydown(function(e) {	 

		 if(e.keyCode == 83 && e.ctrlKey){//CTRL+S
			if($("#btnSave").is(":disabled")==false){
				e.preventDefault();
				$("#btnSave").click();
			}
		  }
		  
		  else if(e.keyCode == 27){//ESC	  
			if($("#btnMain").is(":disabled")==false){
				e.preventDefault();
				window.location.href='Items.php';
			}
		  }

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
                                
                $('#'+id).val(item.name).change(); 
                $('#'+id+'D').val(item.idcode); 
                $('#'+id+'ID').val(item.id); 
                                
            }
        });
					
		$(".acctcontrol, .txtcgroup").on("blur", function(){
			var x = $(this).attr("id");
						
			if( $("#"+x+"D").val()==""){
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
					
					$("#txtcpartno").on("keyup", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
						if($(this).val()!=""){
							$.ajax ({
							url: "itemcode_checker.php",
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


					$("#txtcpartno").on("blur", function() {
						
						//	$.post('itemcode_checker.php', {'id': $(this).val() }, function(data) {
							
							$.ajax ({
							url: "itemcode_checker.php",
							data: { id: $(this).val() },
							async: false,
							success: function( data ) {
								if(data.trim()=="True"){
									$("#txtcpartno").val("").change();
									$("#txtcpartno").focus();
								}
							}
							});
							
							$("#itmcode_err").html("");
							$("#itmcode_err").hide();


					});
					
					$("#frmITEM").on('submit', function (e) {
                        var form = $("#frmITEM");
                            
                        if (form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
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

                            var tbl = document.getElementById('mySecLevelTbl').getElementsByTagName('tr');
                            var lastRow = tbl.length-1;
                                                        
                            document.getElementById('hdnseclvlrowcnt').value = lastRow;

                            e.preventDefault();							
                                //submit form objects to ajax:
                                
                                
                                var formdata = form.serialize();
                                
                                $.ajax({
                                    url: 'items_newsave.php',
                                    type: 'POST',
                                    async: false,
                                    data: formdata,
                                    beforeSend: function(){
                                        $("#AlertMsg").html("<b>SAVING NEW ITEM: </b> Please wait a moment...");
                                        $("#AlertModal").modal('show');
                                    },
                                    success: function(data) {

                                            if(data.trim()=="True"){
                                                $("#AlertMsg").html("<b>SUCCESS: </b>Succesfully saved! <br><br> Loading new item... <br> Please wait!");
                                                
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
                                        
                                        $("#itmcode_err").html("<b><font color='red'>ERROR: </font></b> Unable to save new item!");
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

					$("#selitmpricing").on("change", function(){

						var xid = $(this).val();

						if(xid=="PM"){
							$("#divItmMarkUp").hide();
                            $("#divItmMarkUpFix").hide();
						}else if(xid=="MU"){
							$("#divItmMarkUp").show();
                            $("#divItmMarkUpFix").hide();						
						}else if(xid=="MUFIX"){
							$("#divItmMarkUpFix").show();	
                            $("#divItmMarkUp").hide();						
						}
					});
					
					
					

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
            v.style.padding = "1px";
        var w=a.insertCell(2);
            w.style.padding = "1px";
        var x=a.insertCell(3);
            x.style.padding = "1px"; 
            x.align = "center"; 
            x.style.verticalAlign = "middle";       
        var y=a.insertCell(4);
            y.style.padding = "1px"; 
            y.align = "center";  
            y.style.verticalAlign = "middle";     
        var z=a.insertCell(5);
            z.align = "center";
            z.style.padding = "1px";        

            var xz = $("#hdnuomlist").val();
            secxoptions = "";
            $.each(jQuery.parseJSON(xz), function() { 
                secxoptions = secxoptions + "<option value='"+this['ccode']+"'>"+this['cdesc']+"</option>";
            });
        
        u.innerHTML = "<div class=\"col-xs-12 nopadwright\"><select class='form-control input-xs' name=\"selunit"+lastRow+"\" id=\"selunit"+lastRow+"\">" + secxoptions + "</select></div>";
        v.innerHTML = "<div class=\"nopadwright\" ><select id='selrule"+lastRow+"' name='selrule"+lastRow+"' class='form-control input-xs'> <option value='mul'>&gt;</option> <option value='div'>&lt;</option> </select></div>";
        w.innerHTML = "<div class=\"nopadwright\" ><input type='text' class='form-control input-xs' id='txtfactor"+lastRow+"' name='txtfactor"+lastRow+"' value='1' required style=\"text-align: right\"> </div>";
        x.innerHTML = "<div class=\"nopadwright\" ><input type='checkbox' id='txtchkPO"+lastRow+"' name='txtchkPO"+lastRow+"' value='1'> </div>";
        y.innerHTML = "<div class=\"nopadwright\" ><input type='checkbox' id='txtchkSI"+lastRow+"' name='txtchkSI"+lastRow+"' value='1'> </div>";
        z.innerHTML = "<input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteRow(this);\"/>";
        
        $("#txtfactor"+lastRow).autoNumeric('init',{mDec:2});
		$("#txtfactor"+lastRow).on("focus", function () {
			$(this).select();
		});

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

    function addseclevel(){
        var tbl = document.getElementById('mySecLevelTbl').getElementsByTagName('tr');
        var lastRow = tbl.length;

        var a=document.getElementById('mySecLevelTbl').insertRow(-1);
        var b=a.insertCell(0);
        var c=a.insertCell(1);
        c.align = "left";
        c.style.padding = "1px";
        var d=a.insertCell(2);
        d.align = "left";
        d.style.padding = "1px";
        var e=a.insertCell(3);
        e.align = "left";
        e.style.padding = "1px";
        var g=a.insertCell(4);

        var xz = $("#hdnsecslist").val();
        secxoptions = "";
        $.each(jQuery.parseJSON(xz), function() { 
            secxoptions = secxoptions + "<option value='"+this['nid']+"'>"+this['cdesc']+"</option>";
        });
        
        b.innerHTML = "<div class=\"col-xs-12 nopadwright\"><select class='form-control input-xs' name=\"selitmsec"+lastRow+"\" id=\"selitmsec"+lastRow+"\">" + secxoptions + "</select></div>";
        c.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='numeric form-control input-sm' id='txtwhmin"+lastRow+"' name='txtwhmin"+lastRow+"' value='0' required style=\"text-align: right\"> </div>";
        d.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='numeric form-control input-sm' id='txtwhmax"+lastRow+"' name='txtwhmax"+lastRow+"' value='0' required style=\"text-align: right\"> </div>";
        e.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input type='text' class='numeric form-control input-sm' id='txtwhreor"+lastRow+"' name='txtwhreor"+lastRow+"' value='0' required style=\"text-align: right\"> </div>";

        g.innerHTML = "<div class=\"col-xs-12 nopadwleft\" ><input class='btn btn-danger btn-xs' type='button' id='row_" + lastRow + "_delete' class='delete' value='Delete' onClick=\"deleteSeclevl(this);\"/></div>";
        
        $("input.numeric").numeric({decimalPlaces: 2});
        $("input.numeric").on("click", function () {
            $(this).select();
        });

    }
   		
    function deleteSeclevl(r) {
        var tbl = document.getElementById('mySecLevelTbl').getElementsByTagName('tr');
        var lastRow = tbl.length;
        var i=r.parentNode.parentNode.rowIndex;
        document.getElementById('mySecLevelTbl').deleteRow(i);
        var lastRow = tbl.length;
        var z; //for loop counter changing textboxes ID;
        
            for (z=i+1; z<=lastRow; z++){
                var tempcitemno = document.getElementById('selitmsec' + z);
                var tempcdesc = document.getElementById('txtwhmin' + z);
                var tempnqty= document.getElementById('txtwhmax' + z);
                var tempcunit= document.getElementById('txtwhreor' + z);

                var tempddel= document.getElementById('row_' + z + '_delete');
                
                var x = z-1;
                tempcitemno.id = "selitmsec" + x;
                tempcitemno.name = "selitmsec" + x;
                tempcdesc.id = "txtwhmin" + x;
                tempcdesc.name = "txtwhmin" + x;
                tempnqty.id = "txtwhmax" + x;
                tempnqty.name = "txtwhmax" + x;
                tempcunit.id = "txtwhreor" + x;
                tempcunit.name = "txtwhreor" + x;

                tempcunit.id = "row_" + x + "_delete";

            }
    }

    
</script>
