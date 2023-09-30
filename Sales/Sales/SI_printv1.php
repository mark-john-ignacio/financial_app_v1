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
<body>
    <div id='header' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 100%; '>
                <img src='../../images/SLogo.png' alt='Sert technology Logo' width='100%' height="100%">
            </div>
            <div class='col-sm' style='width: 100%; text-align: justify; text-justify: inter-word;'>
                    <h5 class='nopadding'>Block 2 lot 15 tierra Grande Royale, Brgy. Manggahan Gen. Trias Cavite 4107</h5>
                    <h5 class='nopadding'>Tel/Fax: (046) 402-1596</h5>
                    <h5 class='nopadding'>Mobile No.: (0917) 551-3200</h5>
                    <h5 class='nopadding'>Manila Line: (02) 8831-4115 </h5>
                    <h5 class='nopadding'>Email: sales@serttech.com</h5>
                    <h5 class='nopadding'>Website: www.serttech.com</h5>
                    <h5 class='nopadding'>VAT Reg. TIN: 008-586-750-00000</h5>
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


    <div class='container' id='item' style='width: 100%; '>
        <div class='row'  style='display: flex;'>
            <table class='table' border='2' id='salestable'  style=' border: .5 solid black;border-radius: 20%;'>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th width='50%'>ITEM DESCRIPTION</th>
                        <th>QTY</th>
                        <th>UNIT</th>
                        <th>UNIT PRICE</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody height='400px'>
                    
                </tbody>
            </table>
        </div>
        <div class='row' style='display: flex;'>
            <table border='1' style='width: 100%; '>
                <tr>
                    <td>&nbsp;</td>
                    <td>Total Sales</td>
                </tr>
                <tr>
                    <td>VATABLE SALES</td>
                    <td>(VAT INCLUSIVE)</td>
                </tr>
                <tr>
                    <td>VAT-EXEMPT SALES</td>
                    <td>LESS 12% VAT</td>
                </tr><tr>
                    <td>ZERO RATED SALES</td>
                    <td>AMOUNT NET OF VAT</td>
                </tr>
                <tr>
                    <td>VAT AMOUNT</td>
                    <td>LESS: WITHHOLDING TAX</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>AMOUNT DUE</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>ADD: VAT</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>Total AMOUNT DUE</td>
                </tr>
            </table>
        </div>
    </div>

    <div id='footer' class='container' style='width: 100%;'>
        <div class='row' style='display: flex;'>
            <div class='col-sm' style='width: 10%'> logo</div>
            <div class='col-sm' style='width: 25%; border: 1 solid black'>asdasd</div>
            <div class='col-sm' style='width: 25%; border: 1 solid black'></div>
            <div class='col-sm' style='width: 25%; border: 1 solid black'></div>
        </div>
    </div>
    
</body>
</html>

<script type='text/javascript'>

    var totnetvat = 0, totlessvat = 0, totvatable = 0, totvatxmpt= 0;
    var vatcode = '', vatgross ='';
    $(document).ready(function(){
        $.ajax({
            url: 'th_loaddetails.php',
            data: {id: '<?= $tranno ?>' },
            async: false,
		    dataType: "json",
            success: function(res){
                console.log(res);
                res.map((item, index)=> {

                    if(item.namount != 0){
                        totnetvat = parseFloat(totnetvat) + parseFloat(item.nnetvat)
                        totlessvat = parseFloat(totlessvat) + parseFloat(item.nlessvat)
                        totvatable = parseFloat(totvatable) + parseFloat(item.namount)
                    } else {
                        totvatxmpt = parseFloat(totvatxmpt) + parseFloat(item.namount)
                    }
                    $('<tr>').append(
                        $("<td>").text(item.id),
                        $("<td>").text(item.desc),
                        $("<td>").text(parseFloat(item.totqty).toFixed(0)),
                        $("<td>").text(parseFloat(item.nprice).toFixed(2)), 
                        $("<td>").text(parseFloat("no tax yet").toFixed(2)),
                        $("<td>").text(parseFloat(item.namount).toFixed(2)),
                    ).appendTo('#salestable > tbody')
                })
                var printgross =0;
                switch(item.cvatcode){
                    case 'VT':
                        printgross = parseFloat(gross).toFixed(2)
                        if(parseFloat(totvatxmpt) == 0){
                             printVEGross = '';
                        } else {
                            printVEGross = parseFloat(totvatxmpt).toFixed(2)
                        }
                        printZRGross = "";
                        totnetvat = number_format($totnetvat,2);
                        totlessvat = number_format($totlessvat,2);
                        totvatable = number_format($totvatable,2);
                        break;
                    case 'NV':
                        printgross = parseFloat(gross).toFixed(2)
                        if(parseFloat(totvatxmpt) == 0){
                             printVEGross = '';
                        } else {
                            printVEGross = parseFloat(totvatxmpt).toFixed(2)
                        }
                        printZRGross = "";
                        totnetvat = number_format($totnetvat,2);
                        totlessvat = number_format($totlessvat,2);
                        totvatable = number_format($totvatable,2);
                        break;
                    case 'VE': 
                        $printVATGross = "";
                        $printVEGross = number_format($Gross,2);
                        $printZRGross = "";
                        
                        $totnetvat = "";
                        $totlessvat = "";
                        $totvatable = "";
                }

            }
        })
    })


</script>

<!-- if($cvatcode=='VT' || $cvatcode=='NV'){
			$printVATGross = number_format($Gross,2);
			
				if((float)$totvatxmpt==0){
					//echo "A";
					$printVEGross = "";
				}else{
					//echo "AB";
					$printVEGross =  number_format($totvatxmpt,2);
				}

			$printZRGross = "";


				$totnetvat = number_format($totnetvat,2);
				$totlessvat = number_format($totlessvat,2);
				$totvatable = number_format($totvatable,2);
			
		}elseif($cvatcode=='VE'){
			$printVATGross = "";
			$printVEGross = number_format($Gross,2);
			$printZRGross = "";
			
				$totnetvat = "";
				$totlessvat = "";
				$totvatable = "";
			
		}elseif($cvatcode=='ZR'){
			$printVATGross = "";
			$printVEGross = "";
			$printZRGross = number_format($Gross,2);

				$totnetvat = "";
				$totlessvat = "";
				$totvatable = "";
			
		} -->