<?php
    if(!isset($_SEESION)){
        session_start();
    }

    $tranno = $_REQUEST['tranno'];
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
    <style>
        .form-containers{
				position: relative;
				color: #000;
				font-weight: bold;
				text-transform: uppercase;
				width: 9.5in;
				height: 13in;
                font-size: 0.9em;
        }
        #date{
            position: absolute;
            top: 155px;
            left: 720px;

        }
        /* #box {
            position: absolute; 
            top: 395px; 
            border: 6px solid black;
            width: 10px; 
            height: 2px
        } */

        #receive_by {
            position: absolute; 
            top: 185px; 
            left: 415px;
        }
        #receive_address {
            position: absolute; 
            top: 220px; 
            left: 400px;
        }

        #receive_tin {
            position: absolute; 
            top: 240px; 
            left: 650px;
        }
        #businessstyle {
            position: absolute; 
            top: 240px; 
            left: 420px;
        }

        #sumInWords {
            position: absolute; 
            top: 265px; 
            width: 550px;
            left: 305px;
            text-indent: 24%;
            letter-spacing: 4px;
            line-height: 2em;
        }

        #sumInText {
            position: absolute; 
            top: 300px; 
            left: 700px;
        }
        #invoiceTable {
            position: absolute;
            top: 110px;
            left: 30px;
            width: 200px;
            height: 280px;
            
            /* border: 1px solid black; */
        }
        #list{
            text-align: left; 
            width: 100%; 
        }

        #total {
            position: absolute;
            text-align: right;   
            width: 100%; 
            bottom: 0px;
            padding-right: 20px;
            /* border: 1px solid black; */
            
        }

        #amounts {
            width: 100%;
            bottom: .87in;
            position: absolute;
            text-align: right; 
            margin-right: 30px;
            /* border: 1px solid black; */
        }
        
        #totalamount {
            position: absolute; 
            top: 405px; 
            left: 175px;
        }
    </style>
</head>
<body id='body'>
    <div class="form-containers" >

        <div id='date'>asda</div>
        <div id='receive_by'></div>
        <div id='receive_address'></div>
        <div id='businessstyle'></div>
        <div id='receive_tin'></div>
        <div id='sumInWords'></div>
        <div id='sumInText' ></div>
        
        
        <div id='invoiceTable'>
            <table id='list' >
                <tbody>
                </tbody>
            </table>

            <table id='amounts'></table>

            <table id='total'></table>
        </div>
        
        <!-- <div id='box'></div> -->
        <div id='totalamount'></div>
    </div>
</body>
</html>

<script type='text/javascript'>
var totnetvat = 0, totlessvat = 0, totvatable = 0, totvatxmpt= 0, gross=0;
var vatcode = '', vatgross ='', printVATGross = '', printVEGross='', printZRGross='';
   
    $.ajax({
        url: 'th_transaction.php',
        data: {
            tranno: '<?= $tranno ?>'
        },
        async: false,
        dataType: 'json',
        success: function(res){
            if(res.valid){
                var house = (res?.data?.chouseno ? res.data.chouseno : '')
                var state = (res?.data?.cstate ? res.data.cstate : '')
                var city = (res?.data?.ccity ? res.data.ccity : '')
                var address = house + ' ' + state + ' ' + city;

                

                $('#date').text(datenow(new Date()))
                $('#receive_by').text(res.data.cname)
                $('#receive_address').text(address)
                $('#businessstyle').text(res.data.cname)
                $('#receive_tin').text(res.data.ctin)
                $('#sumInWords').text(number_to_text(res.data.namount))
                $('#sumInText').text(toNumber(res.data.namount))

                res['data2'].map((item, key) => {
                    console.log(item)
                    if(item.csalestype === 'Goods'){
                        if(item.namount != 0){
                            totnetvat += parseFloat(item.nnetvat);
                            totlessvat += parseFloat(item.nlessvat);
                            totvatable += parseFloat(item.namount);
                        } else {
                            totvatxmpt = totvatxmpt + parseFloat(item.namount);
                        }

                        var printgross =0;
                        gross = parseFloat(item.ngross);
                        if(item.ctaxcode === 'VT' || item.ctaxcode === 'NV'){
                            printgross = parseFloat(item.ngross)
                            if(parseFloat(totvatxmpt) != 0){
                                printVEGross = parseFloat(totvatxmpt)
                            }

                            totnetvat = parseFloat(totnetvat);
                            totlessvat = parseFloat(totlessvat);
                            totvatable = parseFloat(totvatable);
                        } else if(item.ctaxcode === 'VE') {
                            printVEGross = parseFloat(item.ngross);
                                
                            totnetvat = "";
                            totlessvat = "";
                            totvatable = "";
                        } else if(item.ctaxcode === 'ZR'){
                            printZRGross = parseFloat(item.ngross);
                            totnetvat = "";
                            totlessvat = "";
                            totvatable = "";
                        }


                        $('<tr>').append(
                            $('<td>').text(item.csalesno),
                            $('<td>').text(toNumber(item.nnet))
                        ).appendTo('#list > tbody')
                        console.log(res.data2.length)
                        
                    } else {
                        console.log("No Official Receipt Reference");
                    }
                        
                })

                $('<tr>').append(
                    $("<td>").html("&nbsp;"),
                    $("<td>").html(
                        "<div>"+(totvatable !== ""  ? toNumber(totvatable) : "")+"</div>" +
                        "<div>" + (totlessvat != "" ? toNumber(totlessvat): '') + "</div>" +
                        "<div>" +(totnetvat !== "" ? toNumber(totnetvat) : '') + "</div>" +
                        "<div> &nbsp; </div>" +
                        "<div>" + (totnetvat !== "" ? toNumber(totnetvat) : '') + "</div>" +
                        "<div>" + (totlessvat !== "" ? toNumber(totlessvat) : '')+ "</div>" +
                        "<div>" + (gross !== '' ? toNumber(gross) : '') + "</div>"
                    ),
                ).appendTo('#amounts')


                $("<tr>").append(
                    $("<td>").html("&nbsp;"),
                    $("<td>").html(
                        "<div>" + (totvatable !=="" ? toNumber(totvatable) : "") + "</div>" +
                        "<div>" + (printVEGross !=="" ? toNumber(printVEGross) : "") + "</div>" +
                        "<div>" + (printZRGross !=="" ? toNumber(printZRGross) : "") + "</div>" +
                        "<div>" + (totnetvat !=="" ? toNumber(totnetvat) : "") + "</div>" 
                    )
                ).appendTo('#total')
                
                $('#totalamount').text(toNumber(totvatable))
                
                window.print();
            }
        },
        error: function(res){
            console.log(res)
        }
    })

    function number_to_text (number){
        const units = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];
        const teens = ["", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"];
        const tens = ["", "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];
        const thousands = ["", "thousand", "million", "billion", "trillion"]; // You can extend this array as needed
        
        // Function to convert a three-digit number to words
        function convertThreeDigitNumberToWords(num) {
            let result = "";
            const hundredsDigit = Math.floor(num / 100);
            const tensDigit = Math.floor((num % 100) / 10);
            const onesDigit = num % 10;

            if (hundredsDigit > 0) {
            result += units[hundredsDigit] + " hundred ";
            }

            if (tensDigit === 1 && onesDigit > 0) {
            result += teens[onesDigit] + " ";
            } else {
            if (tensDigit > 0) {
                result += tens[tensDigit] + " ";
            }

            if (onesDigit > 0) {
                result += units[onesDigit] + " ";
            }
            }

            return result;
        }

        // Split the number into integer and decimal parts
        var integerPart = Math.floor(number);
        var decimalPart = Math.round((number - integerPart) * 100); // Convert decimal part to two digits
        // Convert the integer part to words
        let result = "";
        let index = 0;
        while (integerPart > 0) {
            const threeDigitChunk = integerPart % 1000;
            if (threeDigitChunk > 0) {
            result = convertThreeDigitNumberToWords(threeDigitChunk) + thousands[index] + " " + result;
            }
            integerPart = Math.floor(integerPart / 1000)
            index++;
        }

        // Convert the decimal part to words

        let decimal ="";
        let decval = decimalPart
        let i = 0;
        
        while(decval > 0){
            console.log(Math.floor(decimalPart % 100 / 100))
            if (decimalPart > 0) {
                decimal = convertThreeDigitNumberToWords(decimalPart) + tens[i] +  " " + decimal;
            }
            decval = Math.floor(decimalPart % 100 / 100)
            i++;
        }
        if(decimalPart != 0){
            result += "Pesos and " + decimal + "Cents Only.";
            return result.trim();
        }
        result += "Pesos Only.";
        return result.trim(); // Trim any leading/trailing whitespace
    }

    function toNumber(number){
        return parseFloat(number).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
    }


function datenow(d){
    return d.getFullYear() + '-' + d.getMonth() + '-' + d.getDate()
}
</script>