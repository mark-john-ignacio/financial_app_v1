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

    $deposit = [];
    $bankRecon = [
        'refno' => [],
        'credit' => [],
        'debit' => [],
        'tranno' => [],
        'module' => [], 
    ];
    $EXCEL_TOTAL = 0;
    $totalTransit = 0;
    
    $bookTotal = 0;
    $UNRECORD_DEPOSIT = 0;

    $excel = ExcelRead($_FILES);
    $sql = "SELECT * FROM bank WHERE compcode = '$company' AND cacctno = '$bankcode'";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_array(MYSQLI_ASSOC);
    $bcode = $row['ccode'];
    $bank = $row['cname'];


    //References from PV and OR
    $depositRef = array();
    $sql = "SELECT cpayee as named, ctranno, CASE WHEN cpaymethod='cheque' THEN ccheckno Else cpayrefno END as refno, npaid
    FROM paybill
    WHERE compcode = '$company' and cacctno = '$bankcode' AND lapproved = 1 AND lvoid = 0
    UNION ALL 
    SELECT c.cname as named, ctranno, CASE WHEN a.cpaymethod='cheque' THEN d.ccheckno Else e.crefno END as refno, d.nchkamt as npaid
    FROM receipt a
    LEFT JOIN customers c ON a.compcode = c.compcode AND a.ccode = c.cempid
    LEFT JOIN receipt_check_t d ON a.compcode = d.compcode AND a.ctranno = d.ctranno
    LEFT JOIN receipt_opay_t e ON a.compcode = e.compcode AND a.ctranno = e.ctranno
    WHERE a.compcode ='$company' AND a.cacctcode ='$bankcode' AND a.lapproved = 1 AND a.lvoid = 0";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($depositRef, $row);
    }

    $deposit = array();
    $sql = "SELECT * FROM glactivity WHERE compcode = '$company' AND acctno = '$bankcode' AND (STR_TO_DATE(ddate, '%Y-%m-%d') BETWEEN '$from' AND '$to')";
    $query = mysqli_query($con, $sql);
    // Fetching Data for GL Activity
    while($row = $query -> fetch_assoc()){
        array_push($deposit, $row);
    }

    function getref($cxmod,$cxtran){
        global $depositRef;

        $retrefarray = array();
        foreach($depositRef as $reff){
            if($cxtran==$reff['ctranno']){
                $retrefarray[] = array('cref' => $reff['ctranno'], 'namt' => $reff['npaid']);
            }
        }

        return $retrefarray;
    }


    echo "<pre>";
    print_r($deposit);
    echo "</pre>";
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
                <h4 style="margin-bottom: 0 !important"> Period: <?= date("M d, Y",strtotime($from)) ?> to <?= date("M d, Y",strtotime($to)) ?> </h4>
                <h4 style="margin-top: 0 !important"> Bank: <?= $bank ?> </h4>

                <div class="row">
                    <div class="col-xs-3">Balance per Bank </div>
                    <div class="col-xs-3 text-right"><?= number_format($EXCEL_TOTAL,2) ?></div>

                    <div class="col-xs-3">Balance per Book: </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($bookTotal,2) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-3" style="padding-left: 30px;">Add: Deposit in Transit </div>
                    <div class="col-xs-3 text-right"><?= number_format($totalTransit,2) ?></div>

                    <div class="col-xs-3" style="padding-left: 30px;">Add: Unrecorded Deposit </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($UNRECORD_DEPOSIT,2) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-3">Total </div>
                    <div class="col-xs-3 text-right"><?= number_format($totalBank,2) ?></div>

                    <div class="col-xs-3">Total </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($totalBook,2) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-3" style="padding-left: 30px;">Less: Outstanding Cheques </div>
                    <div class="col-xs-3 text-right"><?= number_format($OUTSTAND_CHEQUE,2) ?></div>

                    <div class="col-xs-3" style="padding-left: 30px; padding-right: 0;">Less: Unrecorded Withdrawal </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($UNRECORD_WITHDRAW,2) ?></div>
                </div>

                <div class="row">
                    <div class="col-xs-3">Adjust Bank Balance: </div>
                    <div class="col-xs-3 text-right"><?= number_format($ADJUST_BANK,2) ?></div>

                    <div class="col-xs-3">Adjust Book Balance: </div>
                    <div class="col-xs-3 text-right" id="book"><?= number_format($ADJUST_BOOK,2) ?></div>
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
                <table class="table table-sm" id="chequeBank">
                    <thead>
                        <tr>
                            <th class="success" colspan='3' style='text-align: center'>Bank Statement</th>
                            <th class="danger" colspan='3' style='text-align: center'>Myx Transactions</th>
                            <th class="warning" rowspan='2' style='text-align: center; vertical-align: middle'>Confirm</th>
                        </tr>
                        <tr>
                            <th>Transaction Details</th>
                            <th>Reference</th>
                            <th style='text-align: right'>Amount</th>
                            <th>Transaction Details</th>
                            <th>Reference</th>
                            <th style='text-align: right'>Amount</th>
                        </tr>
                        <?php
                            $ref = "";
                            $namt = "";
                            foreach($data_excel as $row){
                                $ref = "";
                                $namt = "";
                                $namtLabel = "";
                                if($row[2]==""){
                                    $ref = "<i>No Reference</i>";
                                }else{
                                    $ref = $row[2];
                                }

                                if($row[3]=="" || $row[3]==0){
                                    $namt = $row[4];
                                    $namtLabel = "<i>Credit</i>";
                                }else{
                                    $namt = $row[3];
                                    $namtLabel = "<i>Debit</i>";
                                }

                                $reftran = "";
                                foreach($deposit as $rsx){
                                    $xref = getref($rsx['cmodule'],$rsx['ctranno']);
                                    if($rsx['ddate']==$row[0] && $xref==$ref){
                                        $reftran = $rsx['ctranno'];
                                    }
                                }
                        ?>
                        <tr>
                            <td><?=$row[0]."<br>".$row[1]?></td>
                            <td><?=$ref."<br>".$namtLabel?></td>
                            <td style='text-align: right'><?=number_format($namt,2)?></td>
                            <td>Transaction Details</td>
                            <td>Reference</td>
                            <td style='text-align: right'>Amount</td>
                        </tr>
                        <?php
                            }
                        ?>
                    </thead>
                    <tbody> </tbody>
                </table>
            </div></div>
        </div>
    </div>

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
            <div class="row">
               
            </div>
        </div>
    </div>

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
            <div class="row">
               
            </div>
        </div>
    </div>
    
    <div style="min-width: 10in; width: 100%; padding: 10px;  display: flex; justify-content: center; justify-items: items">
        <button type="button" class="btn btn-primary" onclick="Finalized.call(this)" id="Finalized" disabled>Finalize Bank Reconciliation</button>
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
                <table class="table table-sm" id="chequeBank">
                    <thead>
                        <tr>
                            <th>Check Date</th>
                            <th>Account Nature</th>
                            <th>Check Number</th>
                            <th style='text-align: right'>Debit</th>
                            <th style='text-align: right'>Credit</th>
                            <!-- <th>Amount</th> -->
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody> </tbody>
                </table>
            </div></div>
        </div>
    </div>

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
            <div class="row">
               
            </div>
        </div>
    </div>

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
            <div class="row">
               
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs">
		<li class="active"><a href="#items" data-toggle="tab">Matched Items</a></li>
		<li><a href="#attc" data-toggle="tab">Unmatched Bank Statement</a></li>
        <li><a href="#attc" data-toggle="tab">Unmatched System Transactions</a></li>
	</ul>

    <div style="min-width: 10in; width: 100%; min-height: 3in; max-height: 3in; border: 1px solid; overflow: auto; padding: 5px">
        
    </div>

    <div class="modal fade" id="ReferenceModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="min-width: 650px">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader"> Find Match Cheque </h3>     
                </div>
                <div class="modal-body">
                        <table class="table" id="match" style="padding: 10px;">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Date</th>
                                    <th>Account Name</th>
                                    <th>Reference Number</th>
                                    <!-- <th>Amount</th> -->
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th style="background-color: #2d5f8b; color: white;">Account Name</th>
                                    <th style="background-color: #2d5f8b; color: white;">Reference Number</th>
                                    <!-- <th style="background-color: #2d5f8b; color: white;">Amount</th> -->
                                    <th style="background-color: #2d5f8b; color: white;">Debit</th>
                                    <th style="background-color: #2d5f8b; color: white;">Credit</th>
                                </tr>                               
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
                <div class="modal-footer">
                    <button class='btn btn-success' onclick='matchup.call(this)' >Match</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    Metronic.init(); // init metronic core components

</script>


<script src="../../global/custom.js"></script>