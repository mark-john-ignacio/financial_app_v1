<?php 
    if(!isset($_SESION)){
        session_start();
    }
    include "../../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $bank = array();

    $sql = "SELECT * FROM bank WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        $bank[] = $row;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/select2/css/select2.css?h=<?php echo time();?>">

    <link rel="stylesheet" type="text/css" href="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
    <link href="../../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../../global/css/plugins.css" rel="stylesheet" type="text/css"/>

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../Bootstrap/select2/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <title>MyxFinancials</title>
</head>
<body>
    <section>
        <div class="row nopadding">
        	<div class="col-xs-6 nopadding" style="float:left; width:50%">
				<font size="+2"><u>Bank Reconciliation</u></font>	
          </div>
        </div>


    <div class='container' style='padding-top: 2%'>
        <div class='container-fluid' style='display: flex; justify-content: center; justify-items: center;'>
            <h4 style="text-decoration: underline">Import Bank Statement</h4>
        </div>
        <div style=' width: 100%;'>
            <div class='container' style='padding: 50px; display: flex; justify-content: center; justify-items: center; border: 1px solid;'>
                <form action="CheckBank.php" method="POST" enctype="multipart/form-data">
                    <table border="0">
                        <tr valign="top">
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Date Range From:</th>
                            <th style="width: 100px">
                                <div class="col-xs-13 nopadding">
                                    <input type="date" id='rangefrom' name='rangefrom' class='form-control input-sm' required>
                                </div>
                            </th>
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Date Range To:</th>
                            <th style="width: 100px">
                                <div class="col-xs-13 nopadding">
                                    <input type="date" id='rangeto' name='rangeto' class='form-control input-sm' required>
                                </div>
                            </th>
                        </tr>
                        <tr valign="top" class='nopadwtop'>
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Select Bank:</th>
                            <th colspan="3" style="width: 300px">
                                <div class="col-xs-12 nopadding">
                                    <select name="bank" id="bank" class="form-control input-sm" required>
                                        <option value=""></option>
                                        <?php
                                            foreach($bank as $rs){
                                                echo "<option value=\"".$rs['ccode']."\">".$rs['cname']." - ".$rs['cbankacctno']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Select to import file:</th>
                            <th colspan="3" style="width: 300px">
                                <div class="form-group">
                                    <div class="col-md-12 nopadding">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group">
                                                
                                                <span class="input-group-addon btn btn-success default btn-file">
                                                <span class="fileinput-new">
                                                Select file </span>
                                                <span class="fileinput-exists">
                                                Change </span>
                                                <input type="file" type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls" required> 
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                Remove </a>
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <!-- <tr>
                            <th colspan="2" style="padding-top: 10px">
                                <select name="select" id="select" class='form-control input-sm'>
                                    <option value="Preview">Preview</option>
                                    <option value="Check">Check</option>
                                </select>
                            </th>
                        </tr> -->
                        <tr >
                            <th colspan="2" style="padding-top: 10px">
                                <button type="submit" class='btn btn-danger btn-block' id="btnSubmit"><i class='fa fa-cloud-upload'></i>&nbsp;&nbsp;Import Statement</button>
                            </th>
                            <th colspan="2" style="padding-top: 10px">
                                <a href="template/Bank-Reconciliation-template.xlsx" download="Bank-Reconciliation-template.xlsx" class="btn btn-primary btn-block"><i class='fa fa-cloud-download'></i>&nbsp;&nbsp;Download Template </a>
                            </th>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    $(function(){

        $("#bank").select2({
			placeholder: "Select Bank...",
			allowClear: true
		});

    });

</script>