<?php
if(!isset($_SESSION)){
session_start();
}


include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];
    $csalesno = $_REQUEST['x'];
	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");
	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
			$companytin = $rowcomp['comptin'];
            $compphone = $rowcomp['cpnum'];
            $compemail = $rowcomp['email'];
            $ptucode = $rowcomp['ptucode'];
            $ptudate = $rowcomp['ptudate'];
		}

	}	
    $sql = "select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin from ntsales a 
        left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
        where a.compcode='$company' and a.ctranno = '$csalesno'";
	$query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)):
            $CustCode = $row['ccode'];
            $CustName = $row['cname'];
            $Remarks = $row['cremarks'];
            $Date = $row['dcutdate'];
            $address = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
            $cTin = $row['ctin'];
    
            // $SalesType = $row['csalestype'];
            $Gross = $row['ngross'];
            $cTerms = $row['cterms'];
            
            $lCancelled = $row['lcancelled'];
            $lPosted = $row['lapproved'];
            $lPrintPosted = $row['lprintposted'];
        endwhile;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print V1</title>
    <link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>
<body style='padding: 0'>
    <!-- Header -->
    <div id='header' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 100%; '>
                <img src='../../images/SLogo.png' alt='Sert technology Logo' width='100%' height="100%">
            </div>
            <div class='col-sm' style='width: 100%; text-align: justify; text-justify: inter-word;'>
                    <h5 class='nopadding'><?= $companyadd ?></h5>
                    <!-- <h5 class='nopadding'>Tel/Fax: </h5> -->
                    <h5 class='nopadding'>Mobile No.: <?= $compphone ?></h5>
                    <!-- <h5 class='nopadding'>Manila Line: </h5> -->
                    <h5 class='nopadding'>Email: <?= $compemail ?></h5>
                    <!-- <h5 class='nopadding'>Website: www.serttech.com</h5> -->
                    <h5 class='nopadding'>VAT Reg. TIN: <?= $companytin ?></h5>
            </div>
            <div class='col-sm' style='width: 100%; margin: 5%; text-align: center;'>
                <h1>Sales Invoice</h1>
                <h2>No. 00001</h2>
            </div>
        </div>
    </div>

    <!-- Body Customer Detail -->
    <div id='body' class='container' style='width: 100%;'>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Sold To: </span> <?= $CustName ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;">Date: </span> <?= $Date ?> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5 class='nopadding'><span style="font-weight: bold;">TIN: </span> <?= $cTin ?></h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5 class='nopadding'><span style="font-weight: bold;">P.O. Terms: </span></h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Address: </span> <?= $address ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;"> Business Style: </span> <?= $CustName ?></h5>
            </div>
        </div>
    </div>

    <!-- Body Detail of Item list -->
    <div class='container' id='item' style='width: 100%; top: 0; height: 415px;'>
        <div class='row' >
            <table class='table' id='salestable' >
                <thead  style=' border: .5 solid black;border-radius: 20%;'>
                    <tr>
                        <th>No.</th>
                        <th width='50%'>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UNIT</th>
                        <th>UNIT PRICE</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody >
                    <?php 
                        $cntr = 0;
                        $totnetvat = 0;
                        $totlessvat = 0;
                        $totvatxmpt = 0;
                        $totvatable = 0;
            
                        $nnetprice = 0;
                        $sql = "select a.*, b.citemdesc, c.nrate from ntsales_t a 
                        left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
                        left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode 
                        where a.compcode='$company' and a.ctranno = '$csalesno'";
                        $query = mysqli_query($con, $sql);

                        

                        while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)):
                                $cntr = $cntr + 1;
                                $nnetprice = floatval($row['nprice']) - floatval($row['ndiscount']);
                                $cvatcode = $row['cvatcode'];
                                //
                                if((int)$row['nrate']!=0){
                                    //echo "A";
                                    $totnetvat = floatval($totnetvat) + floatval($row['nnetvat']);
                                    $totlessvat = floatval($totlessvat) + floatval($row['nlessvat']);
                                    
                                    $totvatable = floatval($totvatable) + floatval($row['namount']);
                                }
                                else{
                                    //echo "B";
                                    $totvatxmpt = floatval($totvatxmpt) + floatval($row['namount']);
                                }
                    ?>

                    <tr> 
                        <td style="width: 0.4in"  align="center"><?=$cntr;?></td>
                        <td style="text-overflow: ellipsis; width: 0.8in">&nbsp;&nbsp;<?php echo $row['citemno'];?></td>
                        <td style="text-overflow: ellipsis; width: 3.25in"><?php echo $row['citemdesc'];?></td>
                        <td style="width: 0.5in" align="center"><?php echo number_format($row['nqty']);?></td> 
                        <td style="width: 0.5in" align="center"><?php echo $row['cunit'];?></td>
                        <td style="text-overflow: ellipsis; width: 1in" align="right"><?php echo number_format($nnetprice,2);?></td>
                        <td style="padding-right: 0.3in" align="right"><?php echo number_format($row['namount'],2);?></td>    
                    </tr>


                    <?php 
                        endwhile;
                        if($cvatcode=='VT' || $cvatcode=='NV'){
                            $printVATGross = number_format($Gross,2);
                            
                            if(floatval($totvatxmpt)==0){
                                //echo "A";
                                $printVEGross = 0;
                            }else{
                                //echo "AB";
                                $printVEGross =  $totvatxmpt;
                            }
                
                            $printZRGross = 0;
                
                
                            $totnetvat = $totnetvat;
                            $totlessvat = $totlessvat;
                            $totvatable = $totvatable;
                            
                        }elseif($cvatcode=='VE'){
                            $printVATGross = 0;
                            $printVEGross = $Gross;
                            $printZRGross = 0;
                        
                            $totnetvat = 0;
                            $totlessvat = 0;
                            $totvatable = 0;
                        
                        }elseif($cvatcode=='ZR'){
                            $printVATGross = 0;
                            $printVEGross = 0;
                            $printZRGross = $Gross;
            
                            $totnetvat = 0;
                            $totlessvat = 0;
                            $totvatable = 0;
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Detail of Transaction Detail of Amount -->
    <div class='container' id='item' style='width: 100%; top: 0; '>
        <div class='row' style='display: flex;'>
            <table style='width: 100%; '>

                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%'> VATABLE SALES: </div>
                            <div id='vatsales' style='width: 50%; text-align: center'><?= $totvatable ?> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>Total Sales(VAT INCLUSIVE): </div>
                            <div id='totalsales' style='width: 50%; text-align: center'> <?= $totvatable?> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 100%;'>VAT-EXEMPT SALES: 
                            <div id='vatexmptsale' style='width: 100%; text-align: center'> <?= $printVEGross ?> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>LESS 12% VAT: </div>
                            <div id='less12' style='width: 50%; text-align: center'> <?= $totlessvat ?> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>ZERO RATED SALES: </div>
                            <div id='zerorated' style='width: 50%; text-align: center'> <?= $printZRGross ?> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>AMOUNT NET OF VAT: </div>
                            <div id='amtnet' style='width: 50%; text-align: center'> <?= $totnetvat ?> </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'> AMOUNT:</div>
                            <div id='vatamt' style='width: 50%; text-align: center'> &nbsp; </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>LESS: WITHHOLDING TAX: </div>
                            <div id='lesswtax' style='width: 50%; text-align: center'><?//=number_format($Gross,2)?> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>AMOUNT DUE: </div>
                            <div id='amtdue' style='width: 50%; text-align: center'> <?= number_format($totvatable,2) ?> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>ADD: VAT: </div>
                            <div id='addvat' style='width: 50%; text-align: center'> <?=number_format($totlessvat,2)?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>Total AMOUNT DUE: </div>
                            <div id='totaldue' style='width: 50%; text-align: center'><?=number_format($Gross,2)?> </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div id='footer' class='container' style='width: 100%; margin-top: 2px;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 20%; font-size: 9px; font-weight: bold;'>
                <h5>PTU No.: <?= $ptucode ?></h5>
                <h5>Date Issued: <?= $ptudate ?></h5>
                <h5>Inclusive Serial No.: <?= $csalesno ?></h5>
                <h5>Timestamp: <?= date('m-d-Y') ?></h5>
            </div>
            <div class='col-sm' style='width: 40%; '>
                <div style='font-size: 10px; margin-left: 5px; font-weight: bold; width: 100%;'>Issued By:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
            </div>
            <div class='col-sm' style='width: 40%; border: 1 solid black '>
                <div style='font-size: 10px; margin-left: 5px; font-weight: bold; width: 100%; text-align: center;'>Received the merchandise in good order and condition:</div>
                <div style='width: 85%; margin-left:10%; margin-top: 20%; border: 1px solid black;'></div>
                <div style='font-size: 14px; width: 100%; text-align: center;'>Signature over printed name</div>
                
            </div>
        </div>
        <div style='font-size: 12px; font-weight: bold; width: 100%; text-align: right;'>THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAXES</div>
    </div>
</body>
</html>