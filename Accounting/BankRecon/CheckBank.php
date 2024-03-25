<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../../Connection/connection_string.php";
    include "../../Model/helper.php";

    $company = $_SESSION['companyid'];
    $bankcode = $_POST['bank'];
    $from = date("Y-m-d", strtotime($_POST['rangefrom']));
    $to = date("Y-m-d", strtotime($_POST['rangeto']));


    mysqli_query($con, "Delete From bank_recon_temp Where compcode = '$company'");

    $sql = "SELECT * FROM bank WHERE compcode = '$company' AND ccode = '$bankcode'";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_array(MYSQLI_ASSOC);
    $bcode = $row['ccode'];
    $bank = $row['cname'];
    $bankacct = $row['cacctno'];
    $bankacctno = $row['cbankacctno'];

    $nBankBalance = 0;

    $excel = ExcelRead($_FILES);
    $bnkcnt = 0;
    foreach($excel as $row){
        $bnkcnt++;
        if($bnkcnt>1){
            $nforid = $bnkcnt-1;
            $date = $row[0];
            $accountNature = $row[1];
            $checkno = $row[2];
            $debit = str_replace( ',', '', $row[3]);
            $credit = str_replace( ',', '', $row[4]);

            mysqli_query($con, "INSERT into bank_recon_temp(`compcode`, `nid`, `bank_date`, `bank_name`, `bank_reference`, `bank_debit`, `bank_credit`) VALUES ('$company',".$nforid.",STR_TO_DATE('$date', '%m/%d/%Y'),'$accountNature','$checkno','$debit','$credit')");
            
            $nBankBalance = str_replace( ',', '', $row[5]); 
        }
    }

    $query = mysqli_query($con, "Select * From bank_recon_temp Where compcode='$company'");
    $bkrectemp = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $bkrectemp[] = $row;
    }

    //References from PV and OR
    $depositRef = array();
    $date1 = $_POST['rangefrom'];
    $date2 =$_POST['rangeto'];

    $sql = "SELECT 'PV' as cmod, ctranno, dcheckdate as ddate, cpayee as named, CASE WHEN cpaymethod='cheque' THEN ccheckno Else cpayrefno END as refno, npaid, cparticulars as cremarks
    FROM paybill
    WHERE compcode = '$company' and cacctno = '$bankacct' AND lapproved = 1 AND lvoid = 0
    and dcheckdate between '$date1' and '$date2'
    UNION ALL 
    SELECT 'OR' as cmod, a.ctranno, a.dcutdate as ddate, c.cname as named, CASE WHEN a.cpaymethod='cheque' THEN d.ccheckno Else e.crefno END as refno, d.nchkamt as npaid, a.cremarks
    FROM receipt a
    LEFT JOIN customers c ON a.compcode = c.compcode AND a.ccode = c.cempid
    LEFT JOIN receipt_check_t d ON a.compcode = d.compcode AND a.ctranno = d.ctranno
    LEFT JOIN receipt_opay_t e ON a.compcode = e.compcode AND a.ctranno = e.ctranno
    WHERE a.compcode ='$company' AND a.cacctcode ='$bankacct' AND a.lapproved = 1 AND a.lvoid = 0
    and dcutdate between '$date1' and '$date2'
    UNION ALL 
    SELECT 'OR' as cmod, a.ctranno, a.dcutdate as ddate, d.cname as named, b.creference as refno, b.namount as npaid, CONCAT(b.corno,' - ',c.cremarks) as cremarks
    FROM deposit a
    LEFT JOIN deposit_t b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
    LEFT JOIN receipt c ON b.compcode = c.compcode AND b.corno = c.ctranno
    LEFT JOIN customers d ON c.compcode = d.compcode AND c.ccode = d.cempid
    WHERE a.compcode ='$company' AND a.cacctcode ='$bankacct' AND a.lapproved = 1 AND a.lvoid = 0
    and a.dcutdate between '$date1' and '$date2'
    ";

    $query = mysqli_query($con, $sql);
    $bookcnt = 0;
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $depositRef[] = $row;
        $istre = getref($row, $bnkcnt);
        if($istre=="True"){
            $bnkcnt++;
        }

    }

    $BookTotal = 0;

    function getref($row, $latestid){
        global $con;
        global $company;
        global $depositRef;
        global $excel;
        global $bkrectemp;

        $retrefarray = array();
        $ifyes = "True";

        foreach($bkrectemp as $reff){
      
            if($row['cmod']=="PV"){   
                if($row['refno']==$reff['bank_reference'] && $row['npaid']==$reff['bank_debit']){
                    $ifyes = "False";
                    
                    mysqli_query($con, "UPDATE bank_recon_temp set book_date = '".$row['ddate']."', book_trans = '".$row['ctranno']."', book_amount = '".$row['npaid']."', book_module = '".$row['cmod']."', book_remarks = '".$row['cremarks']."' Where `compcode` = '$company' and `nid` = '".$reff['nid']."'");
                }
            }else if($row['cmod']=="OR"){
                if($row['refno']==$reff['bank_reference'] && $row['npaid']==$reff['bank_credit']){
                    $ifyes = "False";
                    
                    mysqli_query($con, "UPDATE bank_recon_temp set book_date = '".$row['ddate']."', book_trans = '".$row['ctranno']."', book_amount = '".$row['npaid']."', book_module = '".$row['cmod']."', book_remarks = '".$row['cremarks']."' Where `compcode` = '$company' and `nid` = '".$reff['nid']."'");
                }
            }

        }

        if($ifyes == "True"){
            $latestid++;

            $crmx =  mysqli_real_escape_string($con, $row['cremarks']);
            mysqli_query($con, "INSERT into bank_recon_temp(`compcode`, `nid`, `book_date`, `book_trans`, `book_amount`, `book_module`, `book_remarks`) VALUES ('$company', ".$latestid.",'".$row['ddate']."','".$row['ctranno']."','".$row['npaid']."','".$row['cmod']."','".$crmx."')");  
        }

        return $ifyes;
    }

    
    $query = mysqli_query($con, "Select * From bank_recon_temp Where compcode='$company'");
    $bkrectemp = array();
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $bkrectemp[] = $row;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

    <link href="../../global/css/components.css?t=<?php echo time();?>" id="style_components" rel="stylesheet" type="text/css"/>
	<link href="../../global/css/plugins.css" rel="stylesheet" type="text/css"/>

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="../../global/scripts/metronic.js" type="text/javascript"></script>
    <title>MyxFinancials</title>
</head>
<body style="padding:5px">
	<section>
        <div class="row nopadding">
        	<div class="col-xs-6 nopadding" style="float:left; width:50%">
				<font size="+2"><u>Bank Reconciliation</u></font>	
          </div>
        </div>

    <div class="container" style="padding: 14px">

        <div style="display: flex; justify-content: center; justify-items: center; width: 100%;">
            <div style="text-decoration: underline; font-weight: bold; font-size: 20px">Reconciliation Summary</div>
        </div>
        <div class="portlet-body" style="margin-top: 10px; font-size: 15px">
            <div class="well well-large">

                <div class="row">
                    <div class="col-xs-1"><b>Bank</b></div>
                    <div class="col-xs-5"><?= $bank ?></div>

                    <div class="col-xs-3"><b>Transactions Period: </b></div>
                    <div class="col-xs-3 text-right" id="book"><?= date("M d, Y",strtotime($from)) ?> to <?= date("M d, Y",strtotime($to)) ?></div>
                </div>
                <div class="row">
                    <div class="col-xs-1">&nbsp; </div>
                    <div class="col-xs-5"><?=$bankacctno?></div>

                </div>
                <div class="row">
                    <div class="col-xs-1">&nbsp; </div>
                    <div class="col-xs-5"><?=$bankacct?></div>
                </div>

                <div class="row" style="margin-top: 10px">
                    <div class="col-xs-3">Bank Balance: </div>
                    <div class="col-xs-3 text-right"><?= number_format($nBankBalance,2) ?></div>

                    <div class="col-xs-3">Book Balance: </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($BookTotal,2) ?></div>
                </div>

            </div>
        </div>
    </div>

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-copy"></i>Matched Items
            </div>
            <div class="tools">
                <a href="javascript:;" class="collapse">
                </a>
               
            </div>
        </div>
        <div class="portlet-body">
            <div class="row"><div class="col-md-12 col-sm-12 col-xs-12">
                <table class="table table-sm" id="TblMatch">
                    <thead>
                        <tr>
                            <th class="success" colspan='3' style='text-align: center' width="50%">Bank Statement</th>
                            <th class="danger" colspan='2' style='text-align: center' width="50%">Myx Transactions</th>
                        </tr>
                        <tr>
                            <th nowrap>Transaction Details</th>
                            <th>Reference</th>
                            <th style='text-align: right'>Amount</th>
                            <th nowrap>Transaction Details</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php
                           // print_r($excel);
                            $cnt = 0;
                            foreach($bkrectemp as $row){    

                                if((floatval($row['bank_debit'])!=0 || floatval($row['bank_credit'])!=0) && floatval($row['book_amount'])!=0){
                                    if($row['bank_debit']!=0){
                                        $xdbtc = "Debit";
                                        $namt = $row['bank_debit'];
                
                                    }else{
                                        $xdbtc = "Credit";
                                        $namt = $row['bank_credit'];
                                    }
                        ?>
                        <tr>
                            <td><?=$row['bank_name']."<br>".$row['bank_date']?></td>
                            <td><?=$row['bank_reference']."<br>".$xdbtc?></td>
                            <td style='text-align: right; vertical-align: middle'><?=number_format($namt,2)?></td>
                            <td><?=$row['book_trans']."<br>".$row['book_date']?></td>
                            <td style='vertical-align: middle'><?=$row['book_remarks']?></td>
                        </tr>
                        <?php
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tasks"></i>Unmatched Bank Statement
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                    
                    </div>
                </div>
                <div class="portlet-body">

                    <table class="table table-sm table-hover" id="TblUnBank">
                        <thead>
                            <tr>
                                <th nowrap>Transaction Details</th>
                                <th>Reference</th>
                                <th style='text-align: right'>Amount</th>                                   
                            </tr>
                        </thead>
                        <tbody> 
                            <?php
                            // print_r($excel);
                                $cnt = 0;
                                foreach($bkrectemp as $row){    

                                    if((floatval($row['bank_debit'])!=0 || floatval($row['bank_credit'])!=0) && floatval($row['book_amount'])==0){
                                        if($row['bank_debit']!=0){
                                            $xdbtc = "Debit";
                                            $namt = $row['bank_debit'];
                    
                                        }else{
                                            $xdbtc = "Credit";
                                            $namt = $row['bank_credit'];
                                        }
                            ?>
                            <tr onclick="checkbank();" style="cursor: pointer;">
                                <td><?=$row['bank_name']."<br>".$row['bank_date']?></td>
                                <td><?=$row['bank_reference']."<br>".$xdbtc?></td>
                                <td style='text-align: right; vertical-align: middle'><?=number_format($namt,2)?></td>
                            </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

            <div class="portlet box purple">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tasks"></i>Unmatched MYX Transactions
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                    
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-sm table-hover" id="TblUnBook">
                        <thead>
                            <tr>
                                <th nowrap>Transaction Details</th>
                                <th>Reference</th>
                                <th style='text-align: right'>Amount</th>                                   
                            </tr>
                        </thead>
                        <tbody> 
                            <?php
                            // print_r($excel);
                                $cnt = 0;
                                foreach($bkrectemp as $row){    

                                    if((floatval($row['bank_debit'])==0 && floatval($row['bank_credit'])==0) && floatval($row['book_amount'])!=0){
                            ?>
                            <tr>
                                <td style="display: none"><?=$row['book_module']?></td>
                                <td style="display: none"><?=$row['book_trans']?></td>

                                <td><?=$row['book_trans']."<br>".$row['book_date']?></td>
                                <td style='vertical-align: middle'><?=$row['book_remarks']?></td>
                                <td style='text-align: right; vertical-align: middle'><?=number_format($row['book_amount'],2)?></td>
                            </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>   
    </div>
    
    <div style="min-width: 10in; width: 100%; padding: 10px;  display: flex; justify-content: center; justify-items: items">
        <button type="button" class="btn btn-primary" onclick="Finalized.call(this)" id="Finalized" disabled>Finalize Bank Reconciliation</button>
    </div>


    <!-- Bootstrap modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            	<div class="modal-header">
                    <h3 class="modal-title" id="invheader">MYX Transactions</h3>
            	</div>
            
            	<div class="modal-body pre-scrollable">
                      	
                    <table name='MyORTbl' id='MyORTbl' class="table">
                   	    <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Trans No</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Remarks</th>
                                <th style='text-align: right'>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                            
                </div>
			
            	<div class="modal-footer">
                
                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Match</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

           	 	</div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- End Bootstrap modal -->

</body>
</html>

<script src="../../global/custom.js"></script>
<script>
    function checkbank(){

        $('#TblUnBook > tbody  > tr').each(function(index, tr) { 
            console.log(index);
            console.log(tr);
        });

        $("#myModal").modal("show");
    }
</script>