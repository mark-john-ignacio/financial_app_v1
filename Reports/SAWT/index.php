<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../include/autoNumeric.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: center; justify-items: center">
            <h2>Summary Alphalist of Withholding Tax</h2>
        </div>

        <div class="display: flex; justify-content: center; justify-items: center;">
            <div style="display: relative; width: 100%; padding-top: 1in;">
                <table width="100%" border="0" cellpadding="2" >
                    <tr>
                        <th rowspan="3">
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-danger col-sm-5"><i class="fa fa-search"></i>&nbsp; Search</button><br><br>
                            </div>
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-success col-sm-5"><i class="fa fa-file-excel-o"></i>&nbsp; To Excel</button><br><br>
                            </div>
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-primary col-sm-5"><i class="fa fa-file"></i>&nbsp; To DAT</button>
                            </div>
                        </th>
                        <th>
                            <div class="col-xs-8">  
                                
                                <label for="months">Months: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="months" name="months" class="monthpicker form-control input-sm" value="<?= date("MM") ?>">
                                </div>
                            </div>
                        </th>
                        <th >
                            <div class="col-xs-4">
                                <label for="years">Years: </label>
                                
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="years" name="years" class="yearpicker form-control input-sm col-xs-2" value="<?= date("Y") ?>">
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th class="col-xs-3">
                            <div class="cold-xs-2">
                                <label for="rdo">Enter RDO:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-icon"></i></span>
                                    <input type="text" id="rdo" name="rdo" class="form-control input-sm" placeholder="Enter RDO..." required>
                                </div>
                            </div>
                        </th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<script type="text/javascript">
    $(document).ready(function(){
        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        })

        $(".monthpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'MMMM'
        })

        // FetchAPV();
    })
</script>