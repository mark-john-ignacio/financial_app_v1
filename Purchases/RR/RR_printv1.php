<?php

    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    include('../../include/denied.php');

    $company = $_SESSION['companyid'];

    $sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}

    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.*, c.Fname, c.Lname, c.Minit, IFNULL(c.cusersign,'') as cusersign FROM `receive` a
        left join `suppliers` b on a.compcode = b.compcode and a.ccode = b.ccode
        left join `users` c on a.cpreparedby = c.Userid
        where a.compcode = '$company' and a.ctranno = '$tranno'";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query)){
        while($row = $query -> fetch_assoc()){
            $SupName = $row['cname'];
            $address = $row['chouseno'] . " " . $row['ccity'] . " " . $row['cstate'] . " " . $row['ccountry'];
            $SupZip = $row['czip'];
            $Terms = $row['cterms'];
            $SupTin = $row['ctin'];
            $date = $row['ddate'];

            $delto = $row['Fname'] . " " . $row['Lname'];
            $Remarks = $row['cremarks'];
            $Gross = $row['ngross'];
            $cpreparedBy = $row['Fname']." ".(($row['Minit']!=="" && $row['Minit']!==null) ? " " : $row['Minit']).$row['Lname'];
		    $cpreparedBySign = $row['cusersign'];
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 9pt;
		}
		.tdpadx{
			padding-top: 5px; 
			padding-bottom: 5px
		}
		.tddetz{
			border-left: 1px solid; 
			border-right: 1px solid;
		}
		.tdright{
			padding-right: 10px;
		}
		
	</style>
</head>

<body onLoad="window.print()">

    <table border="0" width="100%" cellpadding="1px"  id="tblMain">
        <tr>
            <td align="center"> 

                    <table border="0" width="100%">
                            <tr align="center">
                                <td><img src="<?php echo "../".$logosrc; ?>" height="68px"></td>
                            </tr>
                            <tr align="center">
                                <td><font style="font-size: 18px;"><?php echo $logonamz; ?></font></td>
                            </tr>
                            <tr align="center">
                                <td style="padding-bottom: 20px"><font><?php echo $logoaddrs; ?></font></td>
                            </tr>
                    </table>

            </td>
        </tr>




        
        <tr>
            <td style="vertical-align: top; padding-top: 10px">

                <table border="0" width="100%" style="border-collapse:collapse">
                    <tr>
                        <td colspan="2" align="center" style="padding-bottom: 20px">
                                <font style="font-size: 24px;">Receive </font>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 10px">
                            <font style="font-size: 14px;"><b>Date:</b> <?=date("F d, Y")?></font>
                        </td>

                        <td align="right" style="padding-bottom: 10px">
                        <font style="font-size: 14px;"><b>No.:</b> <?=$tranno?></font>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td width="150px" style="padding: 10px;">
                                                <b>SUPPLIER'S NAME: </b>
                                        </td>
                                        <td style="padding: 10px;">
                                                <?=$SupName?>
                                        </td>
                                        <td width="100px" style="padding: 10px;" align="right">
                                                <b>TERMS</b>
                                        </td>
                                        <td width="100px" style="padding: 10px;" align="right">
                                                <?=$Terms?>
                                        </td>
                                    </tr>
                                </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td width="150px" style="padding: 10px">
                                            <b>DELIVERED TO: </b>									
                                        </td>
                                        <td style="padding: 10px">
                                            <?=$delto?>
                                        </td>
                                        
                                    </tr> 

                                    <tr>
                                        <td width="150px" style="padding: 10px">
                                            <b>Remarks/Notes: </b>									
                                        </td>
                                        <td style="padding: 10px">
                                            <?=$Remarks?>
                                        </td>
                                        
                                    </tr>
                                </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td style="padding-left: 10px;">
                                            <b> SALES TO: </b> <?=$delto?>
                                        </td>
                                        <td>
                                            <b> DELIVERY DATE: </b> <?=date_format(date_create($date),"F d, Y");?>
                                        </td>
                                    </tr>
                                </table>
                        </td>
                        
                        
                    </tr>
                    
                </table>

                <table border="0" align="center" width="100%" style="border-collapse: collapse;">
        
                    <tr>
                        <th style="border: 1px solid" class="tdpadx">Qty</th>
                        <th style="border: 1px solid" class="tdpadx">Unit</th>
                        <th style="border: 1px solid" class="tdpadx">Product Description/s</th>
                        <th style="border: 1px solid" class="tdpadx"><b>Unit Price</b></th>
                        <th style="border: 1px solid" class="tdpadx"><b>Amount</b></th>
                    </tr>

                    <?php 
                    $sql = "SELECT a.*, b.cpartno, b.citemdesc, b.cunit FROM receive_t a
                        left join items b on a.compcode = b.compcode and b.cpartno = a.citemno
                        where a.compcode = '$company' and a.ctranno = '$tranno'";
                
                    $query = mysqli_query($con, $sql);
                    if(mysqli_num_rows($query) != 0){
                        while($row = $query -> fetch_assoc()){
                            // for items
                            $itemcode = $row['cpartno'];
                            $itemname = $row['citemdesc'];
                            $itemunit = $row['cunit'];
                    
                            //for sales return details
                            $qty = $row['nqty'];
                            $price = $row['nprice'];
                            $amount = $row['namount']; 
                        
                    ?>

                    <tr>
                        <td align="center" class="tdpadx tddetz"><?php echo intval($row['nqty']);?></td>
                        <td align="center" class="tdpadx tddetz"><?php echo $row['cunit'];?></td>					
                        <td align="center" class="tdpadx tddetz"><?php echo $row['citemdesc'];?></td>
                        <td align="right" class="tdpadx tddetz tdright"><?php echo number_format($row['nprice'],2);?></td>
                        <td align="right" class="tdpadx tddetz tdright"><?php echo number_format($row['namount'],2);?></td>					
                    </tr>

                    <?php 
                        } 

                    }
                    ?>

                    <tr>
                        <td colspan="3" class="tdpadx" style="border-top: 1px solid; border-left: 1px solid; border-bottom: 1px solid; padding-right: 10px">
                            <?php
                                echo "<b><i>Note: Price exclusive of VAT</i></b>";
                            ?>
                        </td>
                        <td align="right" class="tdpadx" style="border-top: 1px solid; border-right: 1px solid; border-bottom: 1px solid; padding-right: 10px"><b>TOTAL</b></td>
                        <td align="right"  class="tdpadx" style="border: 1px solid;padding-right: 10px"><?php echo number_format($Gross,2);?></td>
                        
                    </tr>

                </table>
            </td>
        </tr> 
        <tr>
            <td style="vertical-align: bottom;">
                <br><br>	<br><br>		
                <table border="0" width="100%">
                    <tr>
                        <td>
                            <table border=0 width="100%">
                                    <tr>
                                        <td width="25%">
                                            <div style="padding-bottom: 50px; text-align: center">Prepared By</div>
                                            <div style="text-align: center"><?=$cpreparedBy?></div>

                                        </td>
                                    </tr>
                                    
                            </table>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>


</body>
</html>