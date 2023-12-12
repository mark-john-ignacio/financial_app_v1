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

    $sql = "SELECT * FROM glactivity WHERE compcode = '$company' AND acctno = '$bankcode' AND (STR_TO_DATE(ddate, '%Y-%m-%d') BETWEEN '$from' AND '$to')";
    $query = mysqli_query($con, $sql);
    // Fetching Data for GL Activity
    while($row = $query -> fetch_assoc()){
        array_push($deposit, $row);
    }
    
    // READ Excel file row
    for($i = 1; $i < count($excel); $i++){
        $data = $excel[$i];

        $date = $data[0];
        $refno = $data[2];
        $excel_debit = floatval($data[3]);
        $excel_credit = floatval($data[4]);
        
        $EXCEL_TOTAL += floatval($excel_credit) + floatval($excel_debit);
        
        foreach($deposit as $list){
            $tranno = $list['ctranno'];
            $module = $list['cmodule'];
            $credit = $list['ncredit'];
            $debit = $list['ndebit'];

            if($module != "JE"){
                $bookTotal += round($credit,2) + round($debit,2);
            } else {
                $UNRECORD_DEPOSIT += round($credit,2) + round($debit,2);
            }
        
            // Check if module is PV or OR
            $sql = match($module){
                "PV" => "SELECT cpayee as named, cpayrefno as refno FROM paybill WHERE compcode = '$company' AND cpayrefno = '$refno' AND ctranno = '$tranno' AND cbankcode = '$bcode' AND STR_TO_DATE(dcheckdate, '%Y-%m-%d') = '$date' AND lapproved = 1 AND lvoid = 0",
                "OR" => "SELECT c.cname as named, a.ccheckno as refno FROM receipt_check_t a
                        LEFT JOIN receipt b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
                        LEFT JOIN customers c ON a.compcode = c.compcode AND b.ccode = c.cempid
                        WHERE a.compcode ='$company' AND a.ctranno = '$tranno' AND a.cbank ='$bcode' AND a.ccheckno = '$refno' AND (a.ddate, '%Y-%m-%d') = '$date' AND b.lapproved = 1 AND b.lvoid = 0",
            };
    
            // Validation for Inserting for Paycheck Logs
            $queries = mysqli_query($con, $sql);
            $rows = $queries -> fetch_assoc();
            if(mysqli_num_rows($queries) != 0){

                array_push($bankRecon['refno'], $refno);
                array_push($bankRecon['credit'], $excel_credit);
                array_push($bankRecon['debit'], $excel_debit);
                array_push($bankRecon['tranno'], $tranno);
                array_push($bankRecon['module'], $module);
            }
        }        
    }

    //Excel Transactions
    $totalBank = floatval($EXCEL_TOTAL) + $totalTransit;
    $OUTSTAND_CHEQUE = 0;
    $ADJUST_BANK = $totalBank + $OUTSTAND_CHEQUE;

    //Read Database for Reference Transaction
    $totalBook = floatval($bookTotal) + $UNRECORD_DEPOSIT;
    $UNRECORD_WITHDRAW = 0; 
    $ADJUST_BOOK = $totalBook + $UNRECORD_WITHDRAW;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <title>MyxFinancials</title>
</head>
<body> 
    <div class="container" style="padding: 14px">
        <div style="display: flex; justify-content: center; justify-items: center; width: 100%;">
            <div style="text-decoration: underline; font-weight: bold; font-size: 20px">Summary of Bank Reconciliation</div>
        </div>
        <div style="display: flex; min-width: 10in; padding-top: 40px; font-size: 16px ">
            <!-- header summary -->
            <div style="width: 100%; padding: 10px">
                <div>
                    Period: <?= date("M d, Y",strtotime($from)) ?> to <?= date("M d, Y",strtotime($to)) ?>
                </div>
                <div>
                    Bank: <?= $bank ?>
                </div>
                <div style="display: flex; width: 100%; padding-right: 10px;">
                    <div style="width: 100%;">Balance per Bank </div>
                    <div style="width: 100%; text-align: right;"><?= number_format($EXCEL_TOTAL,2) ?></div>
                </div>
                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Add: Deposit in Transit </div>
                    <div style="width: 100%; text-align: right;"><?= number_format($totalTransit,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Total: </div>
                    <div style="width: 100%; text-align: right;"><?= number_format($totalBank,2) ?></div>
                </div>
                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Less: Outstanding Cheques </div>
                    <div style="width: 100%; text-align: right;"><?= number_format($OUTSTAND_CHEQUE,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Adjust Bank Balance: </div>
                    <div style="width: 100%; text-align: right;"><?= number_format($ADJUST_BANK,2) ?></div>
                </div>
            </div>


            <div style="width: 100%; padding: 10px;">
                <div style="display: flex; width: 100%; padding-top: 45px; padding-right: 10px;">
                    <div style="width: 100%">Balance per Book: </div>
                    <div style="width: 100%; text-align: right;" id="book"><?= number_format($bookTotal,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Add: Unrecorded Deposit </div>
                    <div style="width: 100%; text-align: right;" id="unrecordedbook"><?= number_format($UNRECORD_DEPOSIT,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Total: </div>
                    <div style="width: 100%; text-align: right;" id="booktotal"><?= number_format($totalBook,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Less: Unrecorded Withdrawal </div>
                    <div style="width: 100%; text-align: right;" id="lesswithdrawal"><?= number_format($UNRECORD_WITHDRAW,2) ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Adjust Book Balance: </div>
                    <div style="width: 100%; text-align: right;" id="adjustment"><?= number_format($ADJUST_BOOK,2) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="min-width: 10in; width: 100%; padding: 10px;  display: flex; justify-content: center; justify-items: items">
        <button type="button" class="btn btn-primary" onclick="Finalized.call(this)" id="Finalized" style="display: none">Finalized</button>
    </div>
    <div style="min-width: 10in; width: 100%; min-height: 3in; max-height: 3in; border: 1px solid; overflow: auto;">
        <table class="table" id="chequeBank" style="min-width: 10in; overflow: auto;">
            <thead>
                <tr>
                    <th>Check Date</th>
                    <th>Account Nature</th>
                    <th>Check Number</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <!-- <th>Amount</th> -->
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody> </tbody>
        </table>
    </div>

    <div class="modal fade" id="ReferenceModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="min-width: 650px">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader"> Find Match Cheque </h3>     
                </div>
                <div class="modal-body" >
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
    var transactions = [];
    //PHP Array Converting to JS Array
    var Reconciliation = <?= json_encode($bankRecon) ?>;
    var ChequeExcel = <?= json_encode($excel) ?>

    $(document).ready(function(){
        ViewCheque();
        ViewFinalized();
    })
    function isCheck(){
        let row = $(this).closest("tr");
        let modules = row.find("td:eq(0)").text();
        let date = row.find("td:eq(2)").text();

        let book_debit = row.find("td:eq(5)").text();
        let book_credit = row.find("td:eq(6)").text();
        
        let reference = row.find("td:eq(8)").text();
        
        let debit = row.find("td:eq(9)").text();
        let credit = row.find("td:eq(10)").text();
        let tranno = $(this).val();
        
        if ($(this).is(":checked")) {
            transactions.push({
                tranno: tranno,
                module: modules,
                refno: reference,

                book_debit: book_debit,
                book_credit: book_credit,

                date: date,

                credit: credit,
                debit: debit
            });
        } else {
            const indexToRemove = transactions.findIndex(transaction => transaction.tranno === tranno);

            if (indexToRemove !== -1) {
                transactions.splice(indexToRemove, 1);
            }
            
            $(this).prop("checked", false)
        }
        
    }

    function LoadMatchCheque(){
        let row = $(this).closest("tr");
        let reference = row.find("td:eq(2)").text();
        let name = row.find("td:eq(1)").text();
        let date = row.find("td:eq(0)").text();
        // let amount = row.find("td:eq(3)").text();
        let debit = row.find("td:eq(3)").text();
        let credit = row.find("td:eq(4)").text();
        let bank = <?= $bankcode ?>;
        let tranno = <?= json_encode($bankRecon['tranno']) ?>;
        console.log(tranno)

        $("#match > tbody").empty();
        $.ajax({
            url: "th_checkref.php",
            type: 'post',
            data: { 
                refno: reference,  
                name: name, 
                date: date,
                bank: bank,
                tranno: JSON.stringify(tranno)
            },
            dataType: "json",
            async: false,
            success: function(res){
                if(res.valid){
                    $("#ReferenceModal").modal("show")
                    res.data.map((item, index) => {
                        $("<tr>").append(
                            $("<td style='display: none'>").text(item.module),
                            $("<td>").html("<input type='checkbox' id='isCheck' name='isCheck' value='"+ item.tranno+"' onclick='isCheck.call(this)'>"),
                            $("<td>").text(item.date),
                            $("<td>").text(item.name),
                            $("<td>").text(item.refno),
                            $("<td>").text(item.debit),
                            $("<td>").text(item.credit),
                            $("<td>").text(name),
                            $("<td>").text(reference),
                            $("<td>").text(debit),
                            $("<td>").text(credit),
                            // $("<td>").text(amount),
                            // $("<td>").html("<button class='btn btn-sm btn-success' onclick='matchup.call(this)' value='" + item.tranno + "'>Match</button>")
                        ).appendTo("#match > tbody")
                    })
                } else {
                    alert(res.msg)
                }
                
            }, 
            error: function(res){
                console.log(res)
            }
        });
    }

    function matchup(){
        if(transactions.length == 0){
            return alert("Empty Transaction");
        }

        var TOTAL_DEBIT = 0;
        var TOTAL_CREDIT = 0;
        var book_credit = 0;
        var book_debit = 0;

        transactions.map((item, index) => {
            TOTAL_DEBIT = parseFloat(item.debit);
            TOTAL_CREDIT = parseFloat(item.credit);
            book_credit += parseFloat(item.book_credit);
            book_debit += parseFloat(item.book_debit);
        })
        // console.log(transactions)

        var GROSS_DEBIT = parseFloat(book_credit) - parseFloat(TOTAL_DEBIT);
        var GROSS_CREDIT = parseFloat(book_debit) - parseFloat(TOTAL_CREDIT);

        if( isEqualsZero(GROSS_DEBIT) && isEqualsZero(GROSS_CREDIT) ){
            transactions.map((item, index) => {
                Reconciliation['refno'].push(item.refno);
                Reconciliation['debit'].push(parseFloat(item.debit));
                Reconciliation['credit'].push(parseFloat(item.credit));
                Reconciliation['module'].push(item.module);
                Reconciliation['tranno'].push(item.tranno);
            })
        } else {
            alert("Amount has a balance!\n Amount must be zero")
        }
        
        transactions = [];
        transactions.length = 0;
        $("#ReferenceModal").modal("hide")
        ViewCheque();
        ViewFinalized();
    }

    function Finalized(){
        let bank = "<?= $bcode; ?>";
        let tranno = Reconciliation['tranno'];
        let refno = Reconciliation['refno'];
        let modules = Reconciliation['module'];
        let credit = Reconciliation['credit'];
        let debit = Reconciliation['debit'];

        $.ajax({
            url: 'th_checkbank.php',
            data: {
                tranno: JSON.stringify(tranno),
                refno: JSON.stringify(refno),
                module: JSON.stringify(modules),
                credit: JSON.stringify(credit),
                debit: JSON.stringify(debit),
                bank: bank
            },
            dataType: 'json',
            async: false,
            success: function(res){
                if(res.valid){
                    alert(res.msg)
                } else {
                    alert(res.msg)
                }
                location.reload();
            },
            error: function(res){
                console.log(res)
            }
        })
    }

    function ViewCheque(){
        var proceed = false;
        $("#chequeBank tbody").empty()
        ChequeExcel.map((item, index) => {
            if(index == 0){
                for(let i = 0; i < item.length; i++){
                    switch(item[i]){
                        case "DATE *":  
                            proceed = true;
                            break;
                        case "Name *": 
                            proceed = true;
                            break;
                        case "Reference No. *": 
                            proceed = true;
                            break;
                        case "DEBIT":;
                            proceed = true;
                            break;
                        case "CREDIT": 
                            proceed = true;
                            break;
                        case "BALANCE":
                            proceed = true;
                            break;
                        default: break;
                    }
                    if(!proceed) break;
                }
            } else {
                if(!proceed) return;
                
                let date = item[0];
                let accountNature = item[1];
                let checkno = item[2];
                let debit = item[3] ? parseFloat(item[3]) : 0;
                let credit = item[4] ? parseFloat(item[4]) : 0;

                console.log(Reconciliation)
                if( !CheckStore(checkno, debit, credit) ){
                    $("<tr>").append(
                        $("<td>").text(date),
                        $("<td>").text(accountNature),
                        $("<td>").text(checkno),
                        $("<td>").text(debit),
                        $("<td>").text(credit),
                        $("<td>").html("<button type='button' onclick='LoadMatchCheque.call(this)' class='btn btn-sm btn-primary'>Find Match</button>")
                    ).appendTo("#chequeBank tbody")
                }
            }
           
        })
    }

    function ViewFinalized(){
        let ExcelLength = ChequeExcel.length -1;
        let ReconLength = Reconciliation['refno'].length;

        if(ExcelLength === ReconLength){
            $("#Finalized").css("display", "inline");
        }
    }

    function CheckStore(checkno, debit, credit) {
        return (
            Reconciliation['refno'].includes(checkno) &&    
            Reconciliation['debit'].includes(debit) &&
            Reconciliation['credit'].includes(credit)
        );
    }
    
    function isEqualsZero(data){
        return data == 0;
    }
</script>