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

    $sql = "SELECT a.*, b.*, c.Fname, c.Lname, c.Minit, IFNULL(c.cusersign,'') as cusersign, IFNULL(d.cusersign,'') as cchecksign FROM `receive` a
        left join `suppliers` b on a.compcode = b.compcode and a.ccode = b.ccode
        left join `users` c on a.cpreparedby = c.Userid
        left join `users` d on a.lappbyid = d.Userid
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

            $lposted = $row['lapproved'];

            $delto = $row['Fname'] . " " . $row['Lname'];
            $Remarks = $row['cremarks'];
            $Gross = $row['ngross'];
            $cpreparedBy = $row['Fname']." ".(($row['Minit']!=="" && $row['Minit']!==null) ? " " : $row['Minit']).$row['Lname'];
		    $cpreparedBySign = $row['cusersign'];

            $cCheckedBy = $row['lappby'];
		    $cCheckedBySign = $row['cchecksign'];
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Arial, sans-serif;
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
		#imgcontent {
        	position: relative;
		}
		#imgcontent img {
			position: absolute;
			top: 2px;
			left: 3px;
		}
	</style>
</head>
<!--"-->
<body onLoad="window.print()">
    <div id="imgcontent">
		<img src="../<?=$logosrc?>" class="ribbon" alt="" width="150px"/>
	</div>

    <table border="0" width="100%" style="border-collapse:collapse">
        <tr>
            <td colspan="2" align="center" style="padding-bottom: 20px">
                <font style="font-size: 18px;">PURCHASED STOCK-IN SLIP  </font><br>
                <font style="font-size: 18px;">(RECEIVED FROM SUPPLIER/SOURCE)  </font>
            </td>
        </tr>
        <tr> 
            <td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid; border-bottom: 1px solid;">
                <table border="0" width="100%" cellspacing="0">
                    <tr>
                        <td width="50%" style="border-right: 1px solid #000; padding: 5px">
                            Supplier/Source:
                        </td>
                        <td width="30%" style="border-right: 1px solid #000; padding: 5px">
                            Reference No.:
                        </td>
                        <td style=" padding: 5px">
                            Date: <?=$delto?>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="border-right: 1px solid #000; padding: 5px">
                            <?=$SupName?>
                        </td>
                        <td width="30%" style="border-right: 1px solid #000; padding: 5px">
                            &nbsp;
                        </td>
                        <td style="padding: 5px">
                            PSS No.: <?=$tranno?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

    <table border="1" align="center" width="100%" style="border-collapse: collapse; margin-top: 5px" cellpadding="5px">

        <tr>
            <th style="width: 5%;">No</th>
            <th>Code</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Size/Spec</th>
            <th>Unit</th>
            <th>POR</th>
            <th>Cost Center</th>
            <th>Remarks</th>
        </tr>

        <?php 
            $cnt = 0;
            $sql = "SELECT a.*, b.cpartno, b.citemdesc, b.cunit FROM receive_t a
            left join items b on a.compcode = b.compcode and b.cpartno = a.citemno
            where a.compcode = '$company' and a.ctranno = '$tranno'";
    
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0){
                while($row = $query -> fetch_assoc()){

                    $cnt++;
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
            <td align="center" class="tdpadx tddetz"><?=$cnt?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['citemno'];?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['cskucode'];?></td>
            <td align="center" class="tdpadx tddetz"><?=number_format($row['nqty']);?></td>
            <td align="center" class="tdpadx tddetz"><?php echo $row['citemdesc'];?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['cunit'];?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['creference'];?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['ncostcenterdesc'];?></td>
            <td align="center" class="tdpadx tddetz"><?=$row['cremarks'];?></td>
        </tr>

        <?php 
            } 

        }
        ?>

    </table>

    <table border="0" width="100%" style="border-collapse:collapse; margin-top: 5px">
        <tr> 
            <td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid; border-bottom: 1px solid;">
                <table border="0" width="100%" cellspacing="0">
                    <tr>
                        <td width="33%" style="border-right: 1px solid #000; padding: 5px">
                            PSS Prepared By/Date:
                        </td>
                        <td width="34%" style="border-right: 1px solid #000; padding: 5px">
                            Checked By: (Name/Sign/Dept/Date)
                        </td>
                        <td width="33%" style="padding: 5px">
                            Acknowledged By: (Purchasing/Date)
                        </td>
                    </tr>
                    <tr>
                        <td style="border-right: 1px solid #000; padding: 5px">
                            <?php
                                if($cpreparedBySign!=""){                          
                            ?>
                                    <div style="text-align: center"><div><img src = '<?=$cpreparedBySign?>?x=<?=time()?>' height="80px"></div> 
									<div style="text-align: center"><?=$cpreparedBy?></div> 
                            <?php
                                }else{
                            ?>
                                    <div style="text-align: center"><div style="height:80px">&nbsp;</div></div>
									<div style="text-align: center"><?=$cpreparedBy?></div>
                            <?php
                                }
                            ?>
                        </td>
                        <td style="border-right: 1px solid #000; padding: 5px">
                            <?php
                                if($lposted==1 && $cCheckedBySign!=""){                          
                            ?>
                                    <div style="text-align: center"><div><img src = '<?=$cCheckedBySign?>?x=<?=time()?>' height="80px"></div> 
									<div style="text-align: center"><?=$cCheckedBy?></div> 
                            <?php
                                }else{
                            ?>
                                    <div style="text-align: center"><div style="height:80px">&nbsp;</div></div>
									<div style="text-align: center"><?=$cCheckedBy?></div>
                            <?php
                                }                              
                            ?>
                        </td>
                        <td style="padding: 5px">
                           &nbsp;
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
            
</body>
</html>