<?php
    if(!isset($_SEESION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    
    $company = $_SESSION['companyid'];

    function numberTowords($num)
    {
        $ones = array(
            0 =>"ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "014" => "FOURTEEN"
        );
        $tens = array( 
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY", 
            4 => "FORTY", 
            5 => "FIFTY", 
            6 => "SIXTY", 
            7 => "SEVENTY", 
            8 => "EIGHTY", 
            9 => "NINETY" 
        ); 
        $hundreds = array( 
            "HUNDRED", 
            "THOUSAND", 
            "MILLION", 
            "BILLION", 
            "TRILLION", 
            "QUARDRILLION"
        ); /*limit t quadrillion */
        $num = number_format($num,2,".",","); 
        $num_arr = explode(".",$num); 
        $wholenum = $num_arr[0]; 
        $decnum = $num_arr[1]; 
        $whole_arr = array_reverse(explode(",",$wholenum)); 
        krsort($whole_arr,1); 
        $rettxt = ""; 

        foreach($whole_arr as $key => $i){
        
            while(substr($i,0,1)=="0")
                $i=substr($i,1,5);
                if($i!=="") {

                    if($i < 20){ 
                        /* echo "getting:".$i; */
                        $rettxt .= $ones[$i]; 
                    }elseif($i < 100){ 
                        if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)] . "-"; 
                        if(substr($i,1,1)!="0") $rettxt .= "".$ones[substr($i,1,1)]; 
                    }else{ 
                        if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 

                        if(substr($i,1,1)==1){
                            if(substr($i,2,1)==0){
                                $rettxt .= " ".$tens[substr($i,1,1)];
                            }else{
                                $rettxt .= " ".$ones[substr($i,1,2)];
                            }
                        }else{
                            if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
                            if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
                        }

                    } 

                }
                
                if($key > 0){ 
                    $rettxt .= " ".$hundreds[$key]." "; 
                }
            } 

            if($decnum > 0){
                $rettxt .= " PESOS AND ";
            
                if($decnum < 20){
                    //$rettxt .= $ones[$decnum];
                }elseif($decnum < 100){
                //	$rettxt .= $tens[substr($decnum,0,1)];
                //	$rettxt .= " ".$ones[substr($decnum,1,1)];
                }

                $rettxt .= $decnum ."/100";

            }else{
                $rettxt .= " PESOS ONLY";
            }
        return $rettxt;
    }

    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.cname,b.ctradename, b.chouseno,b.ccity,b.cstate,b.ctin, c.Fname, c.Lname, c.Minit, d.cbank, d.ccheckno, d.ddate as chekddate
        FROM receipt a 
        left join customers b on a.compcode = b.compcode and a.ccode = b.cempid
        left join users c on a.compcode = b.compcode and a.cpreparedby = c.Userid
        left join receipt_check_t d on a.compcode = d.compcode and a.ctranno = d.ctranno
        WHERE a.compcode = '$company' and a.ctranno = '$tranno'";
     $query = mysqli_query($con, $sql);
     $data = [];
     while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
         $data = $row;
     }    

     //getInvoicedetails
     $sqlD = "SELECT a.csalesno, b.nexempt, b.nzerorated, b.nnet, b.nvat, a.ndue, a.namount, a.newtamt from receipt_sales_t a
     left join sales b on a.compcode = b.compcode and a.csalesno = b.ctranno
     WHERE a.compcode = '$company' and a.ctranno = '$tranno'";

    $queryD = mysqli_query($con, $sqlD);
    $rowcount=mysqli_num_rows($queryD);

    $xnvatable = 0;
    $xnexempt = 0;
    $xnzero = 0;
    $xnvat = 0;

    $xnewts = 0;

    $totamt = 0;
    $dataD = array();
    while($row = mysqli_fetch_array($queryD, MYSQLI_ASSOC)){
        $dataD[] = $row;
        $totamt = $totamt + floatval($row['namount']);
        $xnvatable = $xnvatable + floatval($row['nnet']);
        $xnexempt = $xnexempt + floatval($row['nexempt']);
        $xnzero = $xnzero + floatval($row['nzerorated']);
        $xnvat = $xnvat + floatval($row['nvat']);

        $xnewts = $xnewts + floatval($row['newtamt']);

        $xnetz = $xnvat + floatval($row['nvat']);
    } 

    //print_r($dataD);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        body{
            font-family: 'Courier New', monospace !important;
			font-weight: 900 !important;
            font-size: 12px;
		}

        #date{
            position: absolute;
            top: 100px;
            right: 2px;
            width: 1.25in;
            float: right;
            

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
            top: 125px; 
            left: 420px;
        }
        #receive_address {
            position: absolute; 
            top: 150px; 
            left: 400px;
            line-height: 12px;
        }

        #receive_tin {
            position: absolute; 
            top: 170px; 
            right: 10px;
            float: right;
            width: 1.75in;
        }
        #businessstyle {
            position: absolute; 
            top: 170px; 
            left: 420px;
            width: 1.5in;
            overflow: hidden;
            line-height: 12px;
        }

        #sumInWords {
            position: absolute; 
            top: 190px; 
            width: 550px;
            left: 320px;
            text-indent: 1.30in;
            letter-spacing: 1px;
            line-height: 2em;
            width: 4.5in;
            
        }
        #hwo:second-line {
            width: 3in;
            border: 1px solid #000;
        }

        #sumInText {
            position: absolute; 
            top: 220px; 
            right: 10px;
            float: right;
            width: 1.25in;
        } 

        #xcmemo {
            position: absolute; 
            top: 243px; 
            float: right;
            right: 20px;
            width: 3in;
        }  

        #xcprepby {
            position: absolute; 
            top: 290px; 
            float: right;
            text-align: right;
            right: 40px;
            width: 2in;
            font-size: 11px;
        } 

        #nvatable {
            position: absolute; 
            top: 125px; 
            left: 220px;
            width: 1.25in;
        } 
        #nexmpt  {
            position: absolute; 
            top: 138px; 
            left: 220px;
            width: 1.25in;
        } 
        #nzero  {
            position: absolute; 
            top: 151px; 
            left: 220px;
            width: 1.25in;
        } 
        #nvat {
            position: absolute; 
            top: 164px; 
            left: 220px;
            width: 1.25in;
        }           

        #nvatinc {
            position: absolute; 
            top: 193px; 
            left: 220px;
            width: 1.25in;
        } 

        #nlessvatlbl {
            position: absolute; 
            top: 206px; 
            left: 140px;
            width: 1.25in;
        }

        #nlessvat {
            position: absolute; 
            top: 206px; 
            left: 220px;
            width: 1.25in;
        }
        #nnetvat {
            position: absolute; 
            top: 219px; 
            left: 220px;
            width: 1.25in;
        }
        #ndiscount {
            position: absolute; 
            top: 232px; 
            left: 220px;
            width: 1.25in;
        }
        #namtdue {
            position: absolute; 
            top: 245px; 
            left: 220px;
            width: 1.25in;
        }
        #naddvat {
            position: absolute; 
            top: 258px; 
            left: 220px;
            width: 1.25in;
        }
        #ntotdue {
            position: absolute; 
            top: 271px; 
            left: 220px;
            width: 1.25in;
        }

        #ngrossss {
            position: absolute; 
            top: 298px; 
            left: 249px;
            width: 1.25in;
        }
        
        #nformcash {
            position: absolute; 
            top: 298px; 
            left: 100px;
            width: 1.25in;
        }

        #nformcheck {
            position: absolute; 
            top: 298px; 
            left: 168px;
            width: 1.25in;
        }
         
       
        .RowCont{
            position: absolute;
            top: 60px !important;
            display: table;
            left: 100px; /*Optional*/
            table-layout: fixed; /*Optional*/
            /*border: 1px solid #000; 
            color: blue*/
        }

        .Row{    
            display: block;
            left: 48px; /*Optional*/  
            /*border: 1px solid #000; 
            letter-spacing: 11px;
            border: 1px solid #000;*/
        }

        .Column{
            display: table-cell; 
            /*border: 1px solid #000;
            letter-spacing: 11px;*/
        }

        #bank {
            position: absolute; 
            top: 311px; 
            left: 160px;
            width: 1.25in;
        }
        #chkno {   
            position: absolute; 
            top: 324px; 
            left: 160px;
            width: 1.25in;
        }
        #chkdate {
            position: absolute; 
            top: 337px; 
            left: 160px;
            width: 1.25in;
        }
        
    </style>
</head>
<body id='body' onload="print();">


        <div id='date'><?=date_format(date_create($data['ddate']),"M d, Y")?></div>
        <div id='receive_by'><?=$data['cname']?></div>
        <div id='receive_address'><?=$data['chouseno']." ,".$data['ccity']." ,".$data['cstate']?></div>
        <div id='businessstyle'><?=$data['ctradename']?></div>
        <div id='receive_tin'><?=$data['ctin']?></div>
        <div id='sumInWords'><span id="hwo"><?=numberTowords($data['napplied'])?></span></div>
        <div id='sumInText' ><?=number_format($data['napplied'],2)?></div>
        <div id='xcmemo' ><?=$data['cremarks']?></div>

        <div id='xcprepby' ><?=$data['Fname']." ".$data['Minit'].(($data['Minit']!=="" && $data['Minit']!==null) ? " " : "").$data['Lname']?></div>
        
        
        
        <div class="RowCont">
            <?php
                if($rowcount>4){
            ?>
                <div class="Row">
                    <div class="Column" style="width: 119px; text-align: left;">Various</div>
                    <div class="Column" style="width: 121px; text-align: left"> <?=number_format($xrow['namount'],2);?></div>
                </div>
            <?php
                }else{
                    foreach($dataD as $xrow){
            ?>
                <div class="Row">
                    <div class="Column" style="width: 119px; text-align: left;"><?=$xrow['csalesno']?></div>
		            <div class="Column" style="width: 121px; text-align: left"> <?=number_format($xrow['namount'],2);?></div>
                </div>
            <?php
                    }
                }
            ?>          
        </div>  

        <div id='nvatable'><?=(floatval($xnvatable)>0) ? number_format($xnvatable,2) : " ";?></div>
        <div id='nexmpt'><?=(floatval($xnexempt)>0) ? number_format($xnexempt,2) : " ";?></div>
        <div id='nzero'><?=(floatval($xnzero)>0) ? number_format($xnzero,2) : " ";?></div>
        <div id='nvat'><?=(floatval($xnvat)>0) ? number_format($xnvat,2) : " ";?></div>

        <?php
            //+ floatval($xnexempt) + floatval($xnzero)
            $xvatinc = floatval($xnvatable) + floatval($xnvat);
            $xnetvatz = floatval($xvatinc) - floatval($xnvat);

            $xdiscount = 0;

            $xnmtdue = floatval($xnetvatz) - floatval($xdiscount);
            $xntotue = floatval($xnmtdue) + floatval($xnvat);

            if($xnvatable > 0) {
        ?>
        <div id='nvatinc'><?=number_format($xvatinc,2);?></div> 
        <div id='nlessvatlbl'><?=(floatval($xnewts)>0) ? "/ EWT" : " ";?></div>
        <div id='nlessvat'><?=(floatval($xnewts)>0) ? number_format($xnewts,2) : " ";?></div>
        <div id='nnetvat'><?=number_format($xnetvatz,2);?></div>
        <div id='ndiscount'><?=(floatval($xdiscount)>0) ? number_format($xdiscount,2) : " ";?></div>
        <div id='namtdue'><?=number_format($xnmtdue,2);?></div>
        <div id='naddvat'><?=(floatval($xnvat)>0) ? number_format($xnvat,2) : " ";?></div>
        <div id='ntotdue'><?=number_format($xntotue,2);?></div>
        <?php
            }
        ?>
        <div id='ngrossss' ><?=number_format($data['napplied'],2)?></div>

        <?php 
            if($data['cpaymethod']=="cash"){
                echo " <div id='nformcash' >/</div>";
            }else if($data['cpaymethod']=="cheque"){
                echo " <div id='nformcheck' >/</div>";
            }
        ?>
        <div id='bank'><?=$data['cbank']?></div>

        <div id='bank'><?=$data['cbank']?></div>

        <div id='bank'><?=$data['cbank']?></div>
        <div id='chkno'><?=$data['ccheckno']?></div>
        <div id='chkdate'><?=$data['chekddate']?></div>

       
</body>
</html>
