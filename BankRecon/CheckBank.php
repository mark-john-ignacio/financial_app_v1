<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    include "../Model/helper.php";
    $company = $_SESSION['companyid'];
    $bankcode = $_POST['bank'];
    $range = date("Y-m-d", strtotime($_POST['range']));

    $deposit = [];
    $EXCEL_TOTAL = 0;
    $totalTransit = 0;
    
    $bookTotal = 0;
    $UNRECORD_DEPOSIT = 0;

    $excel = ExcelRead($_FILES);

    $sql = "SELECT a.* FROM glactivity a WHERE a.compcode = '$company' AND a.acctno = '$bankcode' AND STR_TO_DATE(ddate, '%Y-%m-%d') = $range";

    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($deposit, $row);
        $bookTotal += round($row['ncredit'],2);
    }


    $sql = "SELECT * FROM bank WHERE compcode = '$company' AND cacctno = '$bankcode'";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_array(MYSQLI_ASSOC);
    $bank = $row['cname'];

    for($i = 1; $i < count($excel); $i++){
        $data = $excel[$i];
        $EXCEL_TOTAL += floatval($data[4]) + floatval($data[3]);
    }

    $totalBank = floatval($EXCEL_TOTAL) + $totalTransit;
    $OUTSTAND_CHEQUE = 0;
    $ADJUST_BANK = $totalBank + $OUTSTAND_CHEQUE;

    $totalBook = floatval($bookTotal) + $UNRECORD_DEPOSIT;
    $UNRECORD_WITHDRAW = 0;
    $ADJUST_BOOK = $totalBook + $UNRECORD_WITHDRAW;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
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
                    Period: <?= date("M d, Y",strtotime($range)) ?>
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
    <div style="width: 100%; min-height: 3in; max-height: 3in; border: 1px solid; overflow: auto;">
        <table class="table" style="min-width: 10in; overflow: auto;">
            <thead>
                <tr>
                    <th>Check Date</th>
                    <th>Account Nature</th>
                    <th>Check Number</th>
                    <th>Amount</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody">
                <?php 
                    for($i = 0; $i < count($excel); $i++):
                        $data = $excel[$i];
                        if($i == 0){
                            //Excel Checker Header
                            for($j = 0; $j < count($data); $j++){
                                $proceed = match(onlyString($data[$j])){
                                    "DATE" => true,
                                    "ReferenceNo" => true,
                                    "DEBIT" => true,
                                    "CREDIT" => true,
                                    "BALANCE" => true,
                                    "Name" => true,
                                    default => false
                                };
                                
                                if($proceed == false) break;
                            }
                        } else {
                            if(!$proceed) break;
                                $date = $data[0];
                                $accountNature = $data[1];
                                $checkno = $data[2];
                                $balance = floatval($data[4]) + floatval($data[3]);
                ?>
                    <tr>
                        <td><?= $date ?></td>
                        <td><?= $accountNature ?></td>
                        <td><?= $checkno ?></td>
                        <td><?= $balance ?></td>
                        <th style="display: flex; justify-items: center; justify-content: center;">
                            <button type='button' onclick="LoadMatchCheque.call(this)" class="btn btn-sm btn-primary">Match</button>
                        </th>
                    </tr>
                <?php } endfor; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script>
    function LoadMatchCheque(){
        let row = $(this).closest("tr");
        let reference = row.find("td:eq(2)").text();
        let name = row.find("td:eq(1)").text();
        let date = row.find("td:eq(0)").text();
        let bank = <?= $bankcode ?>

        $.ajax({
            url: "th_checkref.php",
            type: 'post',
            data: { 
                refno: reference,  
                name: name, 
                date: date,
                bank: bank
            },
            dataType: "json",
            async: false,
            success: function(res){
                console.log(res)
            }, 
            error: function(res){

            }
        });
    }
</script>