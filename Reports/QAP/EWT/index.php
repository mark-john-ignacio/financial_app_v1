<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../../global/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../../Bootstrap/css/bootstrap-datetimepicker.css">


    <script src="../../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../../js/bootstrap3-typeahead.min.js"></script>
    <script src="../../../include/autoNumeric.js"></script>

    <script src="../../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../../Bootstrap/js/moment.js"></script>
    <script src="../../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MyxFinancials</title>
</head>
<body >
        <div  style="padding-top: 20px;">
            <form action="" method="post" id="formexport" onsubmit="return false" enctype="multipart/form-data">
                <div style="display: flex; padding: 10px">
                    <div class="col-xs-2">
                        <label for="years">Years: </label>
                        <input type="text" id="years" class="yearpicker form-control input-sm" value="<?= date("Y") ?>">
                    </div>
                    <div class="col-xs-2">
                        <label for="months">Month: </label>
                        <input type="text" id="months" class="monthpicker form-control input-sm" value="<?= date("MM") ?>" >
                    </div>
                    <div class="col-xs-2" style="display: flex; min-width: 200px;">
                        <button class="btn btn-success btn-sm col-xs-4" style="margin: 5px;" onclick="export_file.call(this)" value="CSV">CSV</button>
                        <button class="btn btn-primary btn-sm col-xs-4" style="margin: 5px;" onclick="export_file.call(this)" value="DAT">DAT</button>
                    </div class="col-xs-2">
                    
                </div>
            </form>
            
        </div>
        <div style="display: grid; grid-template-columns: repeat(2, minmax(100px, .2fr)); width: 100%; padding: 10px;">
            <h5>TAX PAYER TRADE NAME:</h5> <h4 id='trade'>Acme Corp.</h4>
            <h5>TAX PAYER NAME:</h5> <h4 id='company'>Acme Corp.</h4>
            <h5>TIN:</h5> <h4 id='tin'>Acme Corp.</h4>
            <h5>TAX PAYER ADDRESS:</h5> <h4 id='address'>Acme Corp.</h4>
        </div>
        <div style="display: flex; height: 350px; overflow: auto; border: 1px solid grey; margin-top: 10px">
            <table class="table">
                <thead>
                    <tr>
                        <th>TIN</th>
                        <th>CUSTOMER</th>
                        <th>ATC</th>
                        <th>PERCENT</th>
                        <th>EWT</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
</body>
</html>
<script>
    var apv = [];
    
    $(document).ready(function(){
        
        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        }).on('dp.change', function (e) {
            FetchAPV();
        });

        $(".monthpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'MMMM'
        }).on('dp.change', function (e) {
            FetchAPV();
        });

        FetchAPV();
    })
    function FetchAPV() {
        let year = $("#years").val();
        let month = $("#months").val();
        let msg = "";
        $.ajax({
            url: "../APV_EWT",
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
                    msg = res.msg
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

        if(apv.length === 0){
            alert(msg)
            return {
                valid: false
            };
        } 

        DisplayCode();
        return {
            valid: true
        };
    }
    
    function export_file () {
        let type = $(this).val();
        let fetch = FetchAPV();
        if(fetch.valid) {
            switch(type) {
                case "CSV": 
                    $("#formexport").prop("action", "CSV").change();
                    break;
                case "DAT":
                    $("#formexport").prop("action", "DAT").change();
                    break;
            }
        }
        let sample = $("#formexport").prop("action")
        console.log(sample)
    }

    function DisplayCode() {
        $("table tbody").empty();
        apv.map((item, index) => {
            $("<tr>").append(
                $("<td>").text(item.tin),
                $("<td>").text(item.name),
                $("<td>").text(item.ewt),
                $("<td>").text(item.rate + "%"),
                $("<td>").text(parseFloat(item.credit).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')),
            ).appendTo("table tbody");
        });
    }
</script>