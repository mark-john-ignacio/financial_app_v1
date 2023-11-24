<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    include "../Model/helper.php";
    $company = $_SESSION['companyid'];
    $bank = $_POST['bank'];
    $range = $_POST['range'];

    $sql = "SELECT a.namount FROM deposit a 
        LEFT JOIN receipt b on a.compcode = b.compcode 
        WHERE a.compcode = '$company' AND a.cbankcode = '$bank'";
    $query = mysqli_query($con, $sql);
    $deposit = [];
    $total = 0;
    $bookTotal = 0;
    $totalTransit = 0;
    $EXCEL_TOTAL = 0;
    $UNRECORD_DEPOSIT = 0;
    $UNRECORD_WITHDRAW = 0;
    $OUTSTAND_CHEQUE = 0;
    $ADJUST_BANK = 0;
    $ADJUST_BOOK = 0;

    $excel = ExcelRead($_FILES);

    while($row = $query -> fetch_assoc()){
        array_push($deposit, $row);
        $book += round($row['namount'],2);
    }


    for($i = 1; $i < count($excel); $i++){
        $data = $excel[$i];
        $EXCEL_TOTAL += round($data[4],2);
    }

   
    $totalBank = floatval($EXCEL_TOTAL) + $totalTransit;
    $totalBook = floatval($bookTotal) + $UNRECORD_DEPOSIT;
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
                    Period: March 01, 2023 - March 23, 2023
                </div>
                <div>
                    Bank: CIP BANK
                </div>
                <div style="display: flex; width: 100%; padding-right: 10px;">
                    <div style="width: 100%;">Balance per Bank </div>
                    <div style="width: 100%; text-align: right;"><?= $total ?></div>
                </div>
                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Add: Deposit in Transit </div>
                    <div style="width: 100%; text-align: right;"><?= $totalTransit ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Total: </div>
                    <div style="width: 100%; text-align: right;"><?= $totalBank ?></div>
                </div>
                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Less: Outstanding Cheques </div>
                    <div style="width: 100%; text-align: right;"><?= $OUTSTAND_CHEQUE ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Adjust Bank Balance: </div>
                    <div style="width: 100%; text-align: right;"><?= $ADJUST_BANK ?></div>
                </div>
            </div>


            <div style="width: 100%; padding: 10px;">
                <div style="display: flex; width: 100%; padding-top: 45px; padding-right: 10px;">
                    <div style="width: 100%">Balance per Book: </div>
                    <div style="width: 100%; text-align: right;"><?= $EXCEL_TOTAL ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Add: Unrecorded Deposit </div>
                    <div style="width: 100%; text-align: right;"><?= $UNRECORD_DEPOSIT ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Total: </div>
                    <div style="width: 100%; text-align: right;"><?= $totalBook ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px;">
                    <div style="width: 100%; padding-left: 30px;">Less: Unrecorded Withdrawal </div>
                    <div style="width: 100%; text-align: right;"><?= $UNRECORD_WITHDRAW ?></div>
                </div>

                <div style="display: flex; width: 100%; padding-top: 20px; padding-right: 10px">
                    <div style="width: 100%">Adjust Book Balance: </div>
                    <div style="width: 100%; text-align: right;"><?= $ADJUST_BOOK ?></div>
                </div>
            </div>
        </div>
    </div>
    <div style="width: 100%; max-height: 3in; border: 1px solid">
        <table  class="table" style="display: block; height: 3in; overflow: auto;">
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
                                    default => false
                                };
                                
                                if($proceed == false) break;
                            }
                        } else {
                            if(!$proceed) break;
                        $data = $excel[$i];
                        $date = $data[0];
                        $checkno = $data[1];
                        $balance = $data[4];
                ?>
                    <tr>
                        <td><?= $date ?></td>
                        <td><?= $accountNature ?></td>
                        <td><?= $checkno ?></td>
                        <td><?= $balance ?></td>
                        <th><button type='button' onclick="LoadMatchCheque.call(this)" value="<?= $checkno ?>">Match</button></th>
                    </tr>
                <?php }endfor; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<script>
    function LoadMatchCheque(){
        let reference = $(this).val();

        $.ajax({
            url: "th_checkref.php",
            data: { refno: reference },
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