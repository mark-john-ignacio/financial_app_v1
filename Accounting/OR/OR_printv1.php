<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "CashBook.php";

    include('../../Connection/connection_string.php');

    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];


    $sql = "select * From company where compcode='$company'";
    $result=mysqli_query($con,$sql);

    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
                        
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $compname =  $row['compname'];
        $compadd = $row['compadd'];
        $comptin = $row['comptin'];
        $compphone = $row['cpnum'];
        $ptucode = $row['ptucode'];
        $ptudate = $row['ptudate'];
        $compemail = $row['email'];
        
    }

    $sql = "select a.*,b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, b.ctin,b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit 
    from receipt  a 
    left join customers b on a.compcode=b.compcode and a.ccode=b.cempid
    where a.ctranno = '$tranno' and a.compcode='$company'";

    $data = [];
    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        $address = $row['chouseno'] . " " . $row['ccity'] . " " . $row['cstate'] . " " . $row['ccountry'];
        // array_push($data, $row);
        $data = $row;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>

    <link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
</head>


<body style="padding-top:0" id='body'>
    <div id='header' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 100%; '>
                <img src='../../images/SLogo.png' alt='Sert technology Logo' width='100%' height="100%">
            </div>
            <div class='col-sm' style='width: 100%; text-align: justify; text-justify: inter-word;'>
                    <h5 class='nopadding'><?= $compadd ?></h5>
                    <!-- <h5 class='nopadding'>Tel/Fax: </h5> -->
                    <h5 class='nopadding'>Mobile No.: <?= $compphone ?></h5>
                    <!-- <h5 class='nopadding'>Manila Line: </h5> -->
                    <h5 class='nopadding'>Email: <?= $compemail ?></h5>
                    <!-- <h5 class='nopadding'>Website: www.serttech.com</h5> -->
                    <h5 class='nopadding'>VAT Reg. TIN: <?= $comptin ?></h5>
            </div>
            <div class='col-sm' style='width: 100%; margin: 5%; text-align: center;'>
                <h2>ACKNOWLEDGEMENT RECEIPT</h2>
                <h2>No. 00001</h2>
            </div>
        </div>
    </div>
    <div id='body' class='container' style='width: 100%;'>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Sold To: </span> <?= $data['cname'] ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;">Date: </span> <?= $data['dcutdate'] ?> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5 class='nopadding'><span style="font-weight: bold;">TIN: </span> <?= $data['ctin'] ?></h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5 class='nopadding'><span style="font-weight: bold;">P.O. Terms: </span> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Address: </span> <?= $address ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;"> Business Style: </span>sample</h5>
            </div>
        </div>
    </div>

    <div class='container' id='detail' style='width: 100%'>
        <div class='row' style='display: flex'>
            <table class='table' border='1' id='list' style='width: 100%;'>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UNIT</th>
                        <th>UNIT PRICE</th>
                        <th>PRICE</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>



    
</body>
</html>

<script type='text/javascript'>
    $(document).ready(function(){
        $.ajax({
            url: 'th_transaction.php',
            data: {
                tranno: <?= $tranno ?>
            },
            async: false,
            dataType: 'json',
            success: function(res){
                if(res.valid){
                    res['data2'].map((item, key) => {
                        $('<tr>').append(
                        $('<td>').text(),
                    ).appendTo('#list > tbody')
                    })
                    
                }
            }
        })
    })
</script>