<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
    <div style="text-align: center; font-weight: bold; text-decoration: underline;">
        <font size="+1">Quarterly Alphalist of Payees</font>
    </div>
    <form action="" method="post" id="FORM_VATSUM" enctype="multipart/form-data" target="_blank">
        <div class='container' style='padding-top: 50px'>
            
                <table>
                    <tr valign="top">
                        <th><button type="button" class='btn btn-danger btn-block' id="btnView" onclick="btnonclick.call(this)" value="VIEW"><i class='fa fa-search'></i>&nbsp;&nbsp;View Report</button></th>
                        <th width='100px'>Month of:</th>
                        <th>
                            <div class="col-xs-10 nopadding">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="from" name="from" class="monthpicker form-control input-sm" value="<?= date("Y-m-d") ?>">
                                </div>
                            </div>
                        </th>
                        <th>Year:</th>
                        <th>
                            <div class="col-xs-10 nopadding">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id='to' name='to' class='yearpicker form-control input-sm' value="<?= date("Y-m-d"); ?>">
                                </div>
                                
                            </div>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th><button type="button" class="btn btn-success btn-block" id="btnExcel" onclick="btnonclick.call(this)" value="CSV"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;To Excel</button></th>
                        <!-- <th>RDO Type: </th> -->
                        <!-- <th><input type="text" id='rdo' name="rdo" class='form-control input-sm' placeholder="RDO TYPE...." required></th> -->
                        <th colspan='4'>&nbsp;</th>
                    </tr>
                    <tr>
                        <!-- <th><button type="button" class="btn btn-info btn-block" id="btnDat" onclick="btnonclick.call(this)" value="DAT"><i class="fa fa-file"></i>&nbsp;&nbsp;To DAT</button></th> -->
                        <th colspan='4'>&nbsp;</th>
                    </tr>
                </table>
        </div>

        <div style="display: flex; justify-content: center; justify-items: center; width: 100%; padding-top: 50px;">
            <div style="display: flex; justify-content: center; justify-items: center; width: 50%;">
                <table style="border: 1px solid; border-radius: 10px; ">
                    <tr>
                        <th><div>Zero Rated</div></th>
                        <th>
                            <div class="col-sm-10">
                                <input type="text" id="zero" name="zero" class="form-control input-sm">
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><div>VAT to Government</div></th>
                        <th>
                            <div class="col-sm-10">
                                <input type="text" id="vatgov" name="vatgov" class="form-control input-sm">
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><div>VAT EXEMPT SALES</div></th>
                        <th>
                            <div class="col-sm-10">
                                <input type="text" id="vatexempt" name="vatexempt" class="form-control input-sm">
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th><div>VATABLE SALES</div></th>
                        <th>
                            <div class="col-sm-10">
                                <input type="text" id="vatable" name="vatable" class="form-control input-sm">
                            </div>
                        </th>
                    </tr>
                </table>
            </div>
            
        </div>
    </form>
</body>
</html>

<script>
    $(document).ready(function() {
        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'YYYY-MM-DD'
        })

        $(".monthpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'YYYY-MM-DD'
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
                    console.log(res)
                    $("#zero").val(res.zero);
                    $("#vatgov").val(res.gov);
                    $("#vatexempt").val(res.exempt);
                    $("#vatable").val(res.vatable);
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
        let button = $(this).val()
        var form = document.getElementById('FORM_VATSUM');
        var newAction = "";
        
        switch (button) {
            case "VIEW":
                newAction = "./TO_VIEW/";
                break;
        }
        
        form.action = newAction;
        form.submit();
    }
</script>