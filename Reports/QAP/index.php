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
            <h2>Quarterly Alphalist of Payees</h2>
        </div>

        <div class="display: flex; justify-content: center; justify-items: center;">
            <div style="display: relative; width: 100%; padding-top: 1in;">
                <table width="100%" border="0" cellpadding="2" >
                    <tr >
                        <th class=" nopadwtop"><button type="button" class="btn btn-danger col-sm-5"><i class="fa fa-search"></i>&nbsp; Search</button></th>
                        <th>Year: </th>
                        <th><div class="col-sm-5"><input type="text" class="yearpicker form-control" ></div></th>
                    </tr>
                    <tr>
                        <th class="nopadwtop"><button type="button" class="btn btn-success col-sm-5"><i class="fa fa-file-excel-o"></i>&nbsp; To Excel</button></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th class="nopadwtop"><button type="button" class="btn btn-primary col-sm-5"><i class="fa fa-file"></i>&nbsp; To DAT</button></th>

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
        } )
    })
</script>