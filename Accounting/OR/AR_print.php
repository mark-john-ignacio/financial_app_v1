<?php
    if(!isset($_SESSION)) {
        session_start();
    }

    include('../../Connection/connection_string.php');
    include('../../include/denied.php');

	$company = $_SESSION['companyid'];
    $sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$companyid = $rowcomp['compcode'];
			$companyname = $rowcomp['compname'];
			$companydesc = $rowcomp['compdesc'];
			$companyadd = $rowcomp['compadd'];
			$companytin = $rowcomp['comptin'];
		}

	}

    $csalesno = $_REQUEST['tranno'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname,b.chouseno,b.ccity,b.cstate,b.ctin from ntsales a 
        left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
        where a.compcode='$company' and a.ctranno = '$csalesno'");

    if (mysqli_num_rows($sqlhead)!=0) {
        while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
            $CustCode = $row['ccode'];
            $CustName = $row['cname'];
            $Remarks = $row['cremarks'];
            $Date = $row['dcutdate'];
            $Adds = $row['chouseno']." ". $row['ccity']." ". $row['cstate'];
            $cTin = $row['ctin'];
            $cTerms = $row['cterms'];

            $cvatcode = $row['cvatcode'];

            $SalesType = $row['csalestype'];
            $PayType = $row['cpaytype'];
            $Gross = $row['ngross'];
            
            $lCancelled = $row['lcancelled'];
            $lPosted = $row['lapproved'];
            $lPrintPosted = $row['lprintposted'];
        }
    } // onLoad="window.print()"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../css/cssSM.css">
</head>
<body style="position:relative; padding-top:.70in" onLoad="window.print()" >
    <div class='container' width='100%'>
        <table border="0" cellpadding="1" style="width: 100%; border-collapse:collapse;" id="tblMain">
            <tr>
                <td colspan='2' style="padding-right: 0.25in;" align="right">
                    <table width="100%" border="0" cellpadding="2" style=" margin-top: 0.18in !important">
                        <tr>
                            <td style="padding-right: 0.1in; padding-top: 5px" align="right" colspan="2"><?= date('m-d-Y') ?></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 1.0in; padding-top: 5px" colspan="2"><?= $CustName ?></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 1.0in; padding-top: 5px" colspan="2"> <?= $Adds ?></td>
                        </tr>
                        <tr>
                            <td style="padding-left: 1.0in; padding-top: 5px" width='50%'><?= $cTin ?></td>
                            <td style="padding-right: 0.1in; padding-top: 5px" align="right">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan='2' style="height: 5.5in; padding-top: 13px;" VALIGN="TOP">
                        <table width="100%" border="0" cellpadding="3" >
                            <tr>
                                <td>&nbsp;</td>
                                <td width='50px'>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php 
                                $sqlbody = mysqli_query($con,"select a.*, d.ngross, b.citemdesc, c.nrate from ntsales_t a 
                                left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
                                left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode 
                                left join sales d on a.compcode = d.compcode and a.ctranno = d.ctranno
                                where a.compcode='$company' and a.ctranno = '$csalesno'");
                                $amount = 0;
                                if(mysqli_num_rows($sqlbody) != 0){
                                    while($row = $sqlbody -> fetch_assoc()):
                                        $amount += $row['namount'];
                            ?>
                            <tr>
                                <td align='center'><?= number_format($row['nqty']) ?></td>
                                <td width='50%' style='padding-left: 10px;'><?= $row['citemdesc'] ?></td>
                                <td align='center'><?= number_format($row['nprice'],2) ?></td>
                                <td align='center'><?= number_format($row['namount'],2) ?></td>
                            </tr>
                            <?php endwhile; } ?>
                        </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-right: 0.1in; padding-top: 10px" align="right"> <?= $amount ?></td>
            </tr>
            <tr>
                <td width='70%'>
                    <table width="100%" border="0" cellpadding="1" style="border-collapse:collapse;">
                        <tr>
                            <td align="center"> <?= $cTerms ?> </td>
                        </tr>
                        <tr>
                            <td align="center"><?= $Date ?> </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" border="0" cellpadding="1" style="border-collapse:collapse;">
                        <tr>
                            <!-- Deposit -->
                            <td align="center"></td>
                        </tr>
                        <tr>
                            <td align='center'><?= $amount ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
