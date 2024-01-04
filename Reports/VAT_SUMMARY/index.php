<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['pageid'] = "VATSummary.php";
    
    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    include('../../include/access2.php');
    
    $company = $_SESSION['companyid'];

    @$arrtaxSI = array();
    @$arrtaxPR = array();

    $gettaxcd = mysqli_query($con,"SELECT * FROM `vatcode` where compcode='$company' and cstatus='ACTIVE' order By cvatdesc"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){

            if($row['ctype']=="Sales" || $row['ctype']=="Both"){
			    @$arrtaxSI[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => $row['nrate']); 
            }

            if($row['ctype']=="Purchase" || $row['ctype']=="Both"){
			    @$arrtaxPR[] = array('ctaxcode' => $row['cvatcode'], 'ctaxdesc' => $row['cvatdesc'], 'nrate' => $row['nrate']); 
            }

		}
	}

    //print_r(@$arrtaxSI);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/autoNumeric.js"></script>

    <script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   
    <title>MyxFinancials</title>
</head>

<body style="padding-left:50px; padding-right:10px;">
    <center>
    <b><u><font size="+1">VAT Summary Report</font></u></b>

    </center>
    <br>

    <form action="" method="post" id="FORM_VATSUM" enctype="multipart/form-data" target="_blank">
       
        <table width="100%" border="0" cellpadding="2">
            <tr>
                <th style="padding:2px" width="50">
                    <button type="button" class='btn btn-danger btn-block' id="btnView" onclick="btnonclick.call(this)" value="VIEW"><i class='fa fa-search'></i>&nbsp;&nbsp;View Report</button>
                </th>
                <td width="90" style="padding-left:10px"><b>Date Range:</td>
                <td style="padding:2px">
                    <div class="form-group nopadding">
                        <div class="col-xs-8">
                            <div class="input-group input-large date-picker input-daterange">
                                <input type="text" class="datepick form-control input-sm" id="from" name="from" value="<?php echo date("m/d/Y"); ?>">
                                <span class="input-group-addon">to </span>
                                <input type="text" class="datepick form-control input-sm" id="to" name="to" value="<?php echo date("m/d/Y"); ?>">
                            </div>
                        </div>	
                    </div>
                </td>

                
            </tr>
            <tr width="50">
                <th style="padding:2px"><button type="button" class="btn btn-success btn-block" id="btnExcel" onclick="btnonclick.call(this)" value="CSV"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;To Excel</button></th>
                <td width="90" style="padding-left:10px"><b>Type: </td>
                <td style="padding:2px">
                    <div class="form-group nopadding">
                        <div class="col-xs-8">
                            <select id='seltyp' name='seltyp' class='form-control input-sm'>
                                <option value="Sales.php">Sales</option>
                                <option value="Purchases.php">Purchases</option>
                            </select> 
                        </div>
                    </div>
                </th>


            </tr>
            
        </table>
        

    </form>
</body>
</html>

<script>
    $(document).ready(function() {

        $(".selewt").select2();

        $(".datepick").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'MM/DD/YYYY'
        })

        fetch_vatables();

    })

    function fetch_vatables() {
        $.ajax({
            url: "./SUMMARY",
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    /**
                     * Sales Tax Codes
                     */
                    $("#zero").val(res.zero);
                    $("#vatgov").val(res.gov);
                    $("#vatexempt").val(res.exempt);
                    $("#vatable").val(res.vatable);

                    /**
                     * Purchase Tax Code
                     */
                    $("#capital").val(res.capital);
                    $("#services").val(res.service);
                    $("#other_goods").val(res.others)
                } else {
                    console.log("error")
                }
            },
            error: function(msg) {
                console.log(msg)
            }
        })
    }

    function btnonclick() {
        let button = $(this).val();
        var form = document.getElementById('FORM_VATSUM');
        var newAction = "";

        var seltyp = $("#seltyp").val();
        
        switch (button) {
            case "VIEW":
                newAction = "./TO_VIEW/"+seltyp;
                break;
        }
        
        form.action = newAction;
        form.submit();
    }
</script>