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
                font-weight: bold;
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
            left: 680px;
        }
        #businessstyle {
            position: absolute; 
            top: 240px; 
            left: 420px;
        }

        #sumInWords {
            position: absolute; 
            top: 268px; 
            width: 550px;
            left: 305px;
            text-indent: 24%;
            letter-spacing: 4px;
            line-height: 3em;
        }

        #sumInText {
            position: absolute; 
            top: 300px; 
            left: 700px;
        }
        #invoiceTable {
            position: absolute;
            top: 110px;
            left: 25px;
            width: 200px;
            height: 280px;
            /* border: 1px solid black; */
        }
        #list{
            text-align: left; 
            width: 100%; 
        }

        #vatlist {
            position: absolute;
            text-align: right; 
            width: 100%; 
            bottom: 0px;
            margin-right: 30px;
        }
        
        #totalamount {
            position: absolute; 
            top: 405px; 
            left: 175px;
        }
        #bankdetails {
            position: absolute;
            top: 430px;
            left: 125px;
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
            <table id='vatlist'>
            </table>
        </div>
        
        <!-- <div id='box'></div> -->
        <div id='totalamount'></div>
        <div id='bankdetails'>
            <div id='bank'></div>
            <div id='chkno'></div>
            <div id='chkdate'></div>
        </div>
    </div>
</body>
</html>

<script type='text/javascript'>
    
var vat = 0;
var ewt = 0;
var total = 0;
   
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

                if(res.data.cpaymethod == 'cash'){
                    $('#box').css('left', '55px')
                } else {
                    $('#box').css('left', '130px')
                }

                res['data2'].map((item, key) => {
                    console.log(item)
                        if(res.data.csalestype === 'Services')
                        vat += parseFloat(item.nvat);
                        ewt += parseFloat(item.newtamt);
                        total += parseFloat(item.namount)

                        $('<tr>').append(
                            $('<td>').text(item.csalesno),
                            $('<td>').text(toNumber(item.nnet))
                        ).appendTo('#list > tbody')
                        console.log(res.data2.length)
                        
                        if(res.data2.length -1 == key){
                            $("<tr>").append(
                                $("<td style='center'>").text('VAT'),
                                $('<td>').text(toNumber(vat))
                            ).appendTo('#vatlist')

                            $("<tr>").append(
                                $("<td style='center'>").text('EWT'),
                                $('<td >').text(toNumber(ewt))
                            ).appendTo('#vatlist')

                            $("<tr>").append(
                                $("<td align='center'>").text(''),
                                $("<td style='width: 100%' align='right'>").text(toNumber(total))
                            ).appendTo('#vatlist')

                            $('#totalamount').text(toNumber(total))
                            $('#bank').text((res.data.cpaymethod === 'cheque' ? res.data.cbank : ''))
                            $('#chkno').text((res.data.cpaymethod === 'cheque' ? res.data.ccheckno : ''))
                            $('#chkdate').text((res.data.cpaymethod === 'cheque' ? res.data.ddate : ''))
                        }
                })
                
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