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
    <style>
        th, td {
            padding-top: 2px;
            padding-left: 15px;
            padding-right: 15px;
            padding-bottom: 2px;
        }
    </style>
    <title>MyxFinancials</title>
</head>
<body>
    <div style="padding: 10px;">
        <h4>Quality Assurance Plan</h4>
    </div>
    <div style="padding-top: 10px;">
        <div style="display: flex; justify-content: center; justify-items: center">
            <h2>Quality Assurance Plan</h2>
        </div>

        <div class="container"style="display: relative;  width: 50%; padding-top: 50px; min-width: 500px">
            <form action="" method="post" id="QAPForm" enctype="multipart/form-data" target="_blank">
                <table>
                    <tr>
                        <th rowspan="3">
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-danger col-sm-12" onclick="btnonclick.call(this)" value="VIEW"><i class="fa fa-search"></i>&nbsp; Search</button><br><br>
                            </div>
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-success col-sm-12" onclick="btnonclick.call(this)" value="CSV"><i class="fa fa-file-excel-o"></i>&nbsp; To Excel</button><br><br>
                            </div>
                            <div class="nopadwtop">
                                <button type="button" class="btn btn-primary col-sm-12" onclick="btnonclick.call(this)" value="DAT"><i class="fa fa-file"></i>&nbsp; To DAT</button>
                            </div>
                        </th>
                        <th>
                            <div class="col-xs-12">  
                                <label for="months">Months: </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="months" name="months" class="monthpicker form-control input-sm" value="<?= date("MM") ?>">
                                </div>
                            </div>
                        </th>
                        <th >
                            <div class="col-xs-12">
                                <label for="years">Years: </label>
                                
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="years" name="years" class="yearpicker form-control input-sm col-xs-2" value="<?= date("Y") ?>">
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="col-xs-6">
                                <label for="rdo">Enter RDO:</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-icon"></i></span>
                                    <input type="text" id="rdo" name="rdo" class="form-control input-sm" placeholder="Enter RDO..." required>
                                </div>
                            </div>
                        </th>
                    </tr>
                </table>
            </form>
            
        </div>
    </div>
</body>
</html>

<script type="text/javascript">
    var apv = [];
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

        FetchAPV();
    })

    function FetchAPV() {
        let year = $("#years").val();
        let month = $("#months").val();
        $.ajax({
            url: "./LIST_EWT/",
            data: {
                years: year,
                months: month
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    apv = res.data
                } else {
                    apv.length = 0;
                    apv = [];
                    console.log(res.msg)
                }
                $("#trade").text(res.company.trade);
                $("#company").text(res.company.name);
                $("#tin").text(res.company.tin);
                $("#address").text(res.company.address);
            },
            error: function(msg){
                console.log(msg)
            }
        })
    }

    function btnonclick() {
        let type = $(this).val();
        var form = document.getElementById('QAPForm');
        var formData = new FormData(form);
        FetchAPV();

        let rdo = $("#rdo").val();
        var newAction = "";

        if (apv.length === 0) {
            return alert("No Referrence found!");
        } 

        if(rdo == ""){ 
            return alert("No RDO found please! Fill this detail!");
        }
        
        switch (type) {
            case "CSV":
                newAction = "./TO_CSV/";
                break;
            case "DAT":
                newAction = "./TO_DAT/";
                break;
            case "VIEW":
                newAction = "./TO_VIEW/";
                break;
        }
        form.action = newAction;
        // console.log(form)
        form.submit();
    }
</script>