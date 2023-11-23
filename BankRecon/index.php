<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <title>MyxFinancials</title>
</head>
<body>
    <div class='container' style='padding-top: 2%'>
        <div class='container-fluid' style='display: flex; justify-content: center; justify-items: center;'>
            <h3 style="text-decoration: underline">BANK RECONCILATION</h3>
        </div>
        <div style=' width: 100%;'>
            <div class='container' style='padding: 50px; display: flex; justify-content: center; justify-items: center; border: 1px solid;'>
                <form action="Preview.php" method="POST" enctype="multipart/form-data">
                    <table>
                        <tr valign="top">
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Date Range:</th>
                            <th style="width: 100px">
                                <div class="col-xs-13 nopadding">
                                    <input type="date" id='range' name='range' class='form-control input-sm'>
                                </div>
                            </th>
                        </tr>
                        <tr valign="top" class='nopadwtop'>
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Select Bank:</th>
                            <th style="width: 300px">
                                <div class="col-xs-13 nopadding">
                                    <select name="bank" id="bank" class="form-control input-sm"></select>
                                </div>
                            </th>
                        </tr>
                        <tr valign="top">
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Sample Template:</th>
                            <th style="width: 300px">
                                <div class="col-xs-13 nopadding">
                                    <a href="template/Bank-Reconciliation-template.xlsx" download="Bank-Reconciliation-template.xlsx" class="btn btn-primary btn-sm"> Download Here </a>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th style='display: flex; justify-items: center; justify-content: center; padding: 10px;'>Select to import file:</th>
                            <th style="width: 300px">
                                <div class="col-xs-13 nopadding">
                                    <!-- <label class="btn btn-sm btn-primary" for="excel_file">Browse...</label> -->
                                    <input type="file" name="excel_file" id="excel_file" value="Browse..."  accept=".xlsx, .xls">
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="2" style="padding-top: 10px">
                                <select name="select" id="select" class='form-control input-sm'>
                                    <option value="Preview">Preview</option>
                                    <option value="Check">Check</option>
                                </select>
                            </th>
                        </tr>
                        <tr >
                            <th colspan="2" style="padding-top: 10px">
                                <button type="submit" class='btn btn-danger btn-block' id="btnSubmit"><i class='fa fa-search'></i>&nbsp;&nbsp;View Report</button>
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
        loadbank()

        $("#btnSubmit").click(function(){
            let range = $("#range").val();
            let bank = $("#bank").val();
            let type = $("#select").val();
            
            $.ajax({
                url: "Preview.php",
                data: {
                    range: range,
                    bank: bank,
                },
                dataType: 'json',
                async: false,
                success: function(res){
                    console.log(res)
                },
                error: function(res){
                    console.log(res)
                }
            });
        })
    })

    function loadbank(){
        $.ajax({
            url: 'th_loadbank.php',
            dataType: 'json',
            async: false,
            success: function(res){
                res.map((item, index) =>{
                    let bank = document.getElementById("bank");
                    let option = document.createElement("option");
                    option.text = item.cname;
                    option.value = item.ccode;
                    bank.appendChild(option);
                })
            },
            error: function(res){
                console.log(res)
            }
        })
    }
</script>