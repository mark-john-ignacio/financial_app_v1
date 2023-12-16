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
    <style>
        table, th, td {
            font-size: 10px
        }
        
    </style>
</head>
<body>
    <div style="padding: 10px;">
        <h4>Summary Alphalist of Withholding Tax at Source</h4>
    </div>
    <div style="padding-top: 10px;">
            <form action="" method="post" id="SAWTForm" enctype="multipart/form-data">
                <div style="display: flex; padding: 10px">
                    
                    <div class="col-xs-2">
                        <label for="months">Month: </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <!-- <input type="text" id="months" name="months" class="monthpicker form-control input-sm" value="< ?= date("MM", strtotime($_POST['months'])) ?>"> -->
                            <input type="text" id="months" name="months" class="monthpicker form-control input-sm" value="<?= date("MM", strtotime($_POST['months'])) ?>">
                        </div>
                    </div>
                    <div class="col-xs-2">
                        <label for="years">Years: </label>
                        
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" id="years" name="years" class="yearpicker form-control input-sm col-xs-2" value="<?= date("Y", strtotime($_POST['years'])) ?>">
                        </div>
                    </div>
                    <div class="cold-xs-2">
                        <label for="rdo">Enter RDO:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-icon"></i></span>
                            <input type="text" id="rdo" name="rdo" class="form-control input-sm" placeholder="Enter RDO..." required>
                        </div>
                    </div>
                    
                    <div class="col-xs-2" style="display: flex; min-width: 200px;">
                        <button type="button" class="btn btn-success btn-sm col-xs-4" style="margin: 5px;" onclick="export_file.call(this)" value="CSV">CSV</button>
                        <button type="button" class="btn btn-primary btn-sm col-xs-4" style="margin: 5px;" onclick="export_file.call(this)" value="DAT">DAT</button>
                    </div>
                </div>
            </form>
        </div>

        <div style="display: flex;">
            <div style="display: grid; grid-template-columns: repeat(2, minmax(25%, .2fr)); width: 100%; padding: 10px;">
                <h5>TAX PAYER TRADE NAME:</h5> <h5 id='trade'>Acme Corp.</h5>
                <h5>TAX PAYER NAME:</h5> <h5 id='company'>Acme Corp.</h5>
                <h5>TIN:</h5> <h5 id='tin'>Acme Corp.</h5>
                <h5>TAX PAYER ADDRESS:</h5> <h5 id='address'>Acme Corp.</h5>
            </div>
            <!-- <div style="display: flex; justify-content: left; justify-items: left;">
                <div style="display: grid; grid-template-columns: repeat(2, minmax(100px, .2fr)); padding: 10px;">
                    <h5>TOTAL GROSS: </h5>
                    <h5>₱ <span id="TOTAL_GROSS">00.00</span></h5>
                    <h5>TOTAL CREDIT: </h5>
                    <h5>₱ <span id="TOTAL_CREDIT">00.00</span></h5>
                </div>
            </div> -->
            
        </div>

        <div style="display: flex; height: 350px; overflow: auto; border: 1px solid grey; border-radius: 20px;  margin: 10px">
            <table class="table" id="List">
                <thead>
                    <tr>
                        <th>TRANSACTION DATE</th>
                        <th>CV REFERENCE NO.</th>
                        <th>VENDOR TIN</th>
                        <th>VENDOR NAME</th>
                        <th>VENDOR ADDRESS</th>
                        <th>W/TAX CODE</th>
                        <th>W/TAX RATE</th>
                        <th>W/TAX BASE AMOUNT</th>
                        <th>W/TAX AMOUNT</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
</body>
</html>

<script>
    var sawt = [];
     $(document).ready(function(){
        $(".yearpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        }).on('dp.change', function (e) {
            fetchSAWT();
        });

        $(".monthpicker").datetimepicker({
            defaultDate: moment(),
            viewMode: 'months',
            format: 'MMMM'
        }).on('dp.change', function (e) {
            fetchSAWT();
        });

        fetchSAWT();
    })

    function export_file() {
        let type = $(this).val();
        let rdo = $("#rdo").val();
        var form = document.getElementById('SAWTForm');
        var formData = new FormData(form);

        if(sawt.length != 0) {
            return alert("No Reference found");
        }
        
        if(rdo == ""){ 
            return alert("No RDO found please! Fill this detail!");
        }

        switch (type) {
            case "CSV":
                newAction = "../TO_CSV/";
                break;
            case "DAT":
                newAction = "../TO_DAT/";
                break;
        }
        form.action = newAction;
        // console.log(form)
        form.submit();

    }
    
    function fetchSAWT(){
        let month = $("#months").val();
        let year = $("#years").val();

        $.ajax({
            url: "../SAWT_LIST/",
            type: "post",
            data: {
                months: month,
                years: year,
            },
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    sawt = res.data;
                } else {
                    sawt.length = 0;
                    sawt = [];
                    console.log(res.msg)
                }
                $("#trade").text(res.company.trade);
                $("#company").text(res.company.name);
                $("#tin").text(res.company.tin);
                $("#address").text(res.company.address);
            },
            error: function(msg) {
                console.log(msg)
            }
        })
        display()
    }

    function display () {
        $("#List tbody").empty();

        let TOTAL_CREDIT = 0;
        let TOTAL_GROSS = 0;
        sawt.map((item, index) => { 
            TOTAL_CREDIT += parseFloat(item.credit);
            TOTAL_GROSS += parseFloat(item.gross);

            $("<tr>").append(
                $("<td>").text(item.date),
                $("<td>").text(item.tranno),
                $("<td>").text(item.tin),
                $("<td>").text(item.name),
                $("<td>").text(item.address),
                $("<td>").text(item.ewt),
                $("<td>").text((item.rate / 100) + "%"),
                $("<td>").text(parseFloat(item.gross).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')),
                $("<td>").text(parseFloat(item.credit).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')),
            ).appendTo("#List tbody")
        })

    }
</script>