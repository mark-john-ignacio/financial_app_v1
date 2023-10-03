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
    from sales a 
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
    <title>Print V1</title>
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
                <h1>Sales Invoice</h1>
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
                <h5 class='nopadding'><span style="font-weight: bold;">P.O. Terms: </span><?= $data['cremarks'] ?> </h5>
            </div>
        </div>
        <div class='row' style="display: flex;">
            <div class='col-sm' style='width: 100%'>
                <h5><span style="font-weight: bold;">Address: </span> <?= $address ?> </h5>
            </div>
            <div class='col-sm' style='width: 75%'>
                <h5><span style="font-weight: bold;"> Business Style: </span> <?= $data['cname'] ?></h5>
            </div>
        </div>
    </div>


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
                    
                </tbody>
            </table>
        </div>
    </div>
    <div class='container' id='item' style='width: 100%; top: 0; '>
        <div class='row' style='display: flex;'>
            <table style='width: 100%; '>

                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%'> VATABLE SALES: </div>
                            <div id='vatsales' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>Total Sales(VAT INCLUSIVE): </div>
                            <div id='totalsales' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 100%;'>VAT-EXEMPT SALES: 
                            <div id='vatexmptsale' style='width: 100%; text-align: center'> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>LESS 12% VAT: </div>
                            <div id='less12' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>ZERO RATED SALES: </div>
                            <div id='zerorated' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>AMOUNT NET OF VAT: </div>
                            <div id='amtnet' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'> AMOUNT:</div>
                            <div id='vatamt' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>LESS: WITHHOLDING TAX: </div>
                            <div id='lesswtax' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>AMOUNT DUE: </div>
                            <div id='amtdue' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>ADD: VAT: </div>
                            <div id='addvat' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <div style='display: flex'>
                            <div style='width: 50%;'>Total AMOUNT DUE: </div>
                            <div id='totaldue' style='width: 50%; text-align: center'> </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div id='footer' class='container' style='width: 100%; margin-top: 2px;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 20%; font-size: 9px; font-weight: bold;'>
                PTU No.: <?= $ptucode ?><br>
                Date Issued: <?= $ptudate ?><br>
                Inclusive Serial No.: <?= $tranno ?><br><br>

                Timestamp: <?= date('m-d-Y') ?>
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

<script type='text/javascript'>

    var totnetvat = 0, totlessvat = 0, totvatable = 0, totvatxmpt= 0;
    var vatcode = '', vatgross ='';
    $(document).ready(function(){

        $.ajax({
            // url: 'th_loaddetails.php',
            url: 'th_loadtransaction.php',
            data: {tranno: '<?= $tranno ?>' },
            dataType: "json",
            async: false,
		    
            success: function(res){
                console.log(res.data);
                if(res.valid){
                    res['data'].map((item, index)=> {

                        if(item.namount != 0){
                            totnetvat = totnetvat + parseFloat(item.nnetvat);
                            totlessvat = totlessvat + parseFloat(item.nlessvat);
                            totvatable = totvatable + parseFloat(item.namount);
                        } else {
                            totvatxmpt = totvatxmpt + parseFloat(item.namount);
                        }
                        $("<tr class='spacer'>").append(
                            $("<td>").text(item.citemno),
                            $("<td>").text(item.citemdesc),
                            $("<td>").text(parseFloat(item.nqty).toFixed(0)),
                            $("<td>").text(item.cunit),
                            $("<td>").text(toNumber(item.nprice)), 
                            $("<td>").text(toNumber(item.namount)),
                        ).appendTo('#salestable > tbody')

                        var printgross =0;
                        var printVATGross = '', printVEGross='', printZRGross='';
                        var gross = parseFloat(item.ngross);
                        if(item.ctaxcode === 'VT' || item.ctaxcode === 'NV'){
                            printgross = parseFloat(item.ngross)
                            if(parseFloat(totvatxmpt) != 0){
                                printVEGross = parseFloat(totvatxmpt)
                            }

                            totnetvat = parseFloat(totnetvat);
                            totlessvat = parseFloat(totlessvat);
                            totvatable = parseFloat(totvatable);
                        } else if(item.ctaxcode === 'VE') {
                            $printVEGross = parseFloat(item.ngross);
                                
                            $totnetvat = "";
                            $totlessvat = "";
                            $totvatable = "";
                        } else if(item.ctaxcode === 'ZR'){
                            printZRGross = parseFloat(item.ngross);
                            $totnetvat = "";
                            $totlessvat = "";
                            $totvatable = "";
                        }

                        $('#vatsales').text((totvatable !=="" ? toNumber(totvatable) : ""))
                        $('#vatexmptsale').text((printVEGross !=="" ? toNumber(printVEGross) : ""));
                        $('#zerorated').text((printZRGross !=="" ? toNumber(printZRGross) : ""));
                        $('#vatamt').text((totnetvat !=="" ? toNumber(totnetvat) : ""));

                        $('#totalsales').text((totvatable !== ""  ? toNumber(totvatable) : ""))
                        $('#less12').text((totlessvat != "" ? toNumber(totlessvat): ''))
                        $('#amtnet').text((totnetvat !== "" ? toNumber(totnetvat) : ''))
                        $('#lesswtax').text()
                        $('#amtdue').text((totnetvat !== "" ? toNumber(totnetvat) : ''))
                        $('#addvat').text((totlessvat !== "" ? toNumber(totlessvat) : ''))
                        $('#totaldue').text((gross !== '' ? toNumber(gross) : ''))
                    })

                    window.print();
                }
            },
            error: function(res){
                console.log(res)
            }
        })

    })
    function toNumber(number){
        return parseFloat(number).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
    }
</script>