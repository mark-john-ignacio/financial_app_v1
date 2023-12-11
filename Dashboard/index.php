<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];

    $sql = "SELECT pageid FROM users_access WHERE userid = '$employee'";
    $query = mysqli_query($con, $sql);

    $page = [];
    while($row = $query -> fetch_assoc()){
        array_push($page, $row['pageid']);
    }
?>

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
    <style>
        #DivNavigation {
            display: relative; 
            border: 1px solid #a6a6a6; 
            margin: 2px;
        }
        
        #DivNavigation:hover {
            box-shadow: 1px 3px #a6a6a6;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class='container-fluid' style=' padding-top: 5x;'>
    <!-- Header -->
        <div style='position: relative; min-width: 5.5in; height: .5in; background-color: #2d5f8b; '>
            <div style='position: absolute; left:0; padding: 10px; font-size: 20px; color: white;'>
                Dashboard
            </div>
            <div style='position: absolute; width: 350px; right: 0; padding: 8px; font-size: 15px;'>
                <div style='display: flex'>
                    <!-- <div class='col-xs-8'>
                        <input type="date" class='form-control input-sm' id='datefrom' value="< ?= date('Y-m-d',strtotime(date('m/d/y'))) ?>">
                    </div>
                    <div class='col-xs-8'>
                        <input type="date" class='form-control input-sm' id='dateto' value="< ?= date('Y-m-d',strtotime(date('m/d/y'))) ?>">
                    </div> -->
                    
                </div>
            </div>
        </div>
    <!-- Header Count Reports -->
        <div class='col-sm-12' style='margin-top: 10px; min-width: 5.5in;  padding: 10px; height: 1.5in; display: grid; grid-template-columns: repeat(auto-fit, minmax(0, 1fr)); grid-gap: 5%'>
            <div style='position: relative; min-height: 100%; background-color: #d65151;'>
                <div style='position: absolute; width: 100%; right:0; color: white; text-align: right; padding: 5px;'>
                    <div style='font-size: 30px;' id='total'>
                        185M+
                    </div>
                    <div style='font-size: 20px;'>
                        Number of <span id='totaltxt'>Sales</span>
                    </div>
                </div>
                <a href="javascript:;" style='color: white;'>
                    <div style='position: absolute; width: 100%; bottom: 0; background-color: #a33636; padding: 3px;'>View More</div>
                    <div style='position: absolute; bottom: 0; right: 0; padding: 3px;'><i class='fa fa-forward'></i></div>
                </a>
            </div>
            
            <div style='position: relative; min-height: 100%; background-color: #96B0BC;'>
                <div style='position: absolute; width: 100%; right:0; color: white; text-align: right;  padding: 5px;'>
                    <div style='font-size: 20px; font-weight: bold' id='Highest'>
                        Sample Customer
                    </div>
                    <div style='font-size: 20px;'>
                        Highest Transaction
                    </div>
                </div>
                <a href="javascript:;" style='color: white;'>
                    <div style='position: absolute; width: 100%; bottom: 0; background-color: #96b0ff; padding: 3px;'>View More</div>
                    <div style='position: absolute; bottom: 0; right: 0; padding: 3px;'><i class='fa fa-forward'></i></div>
                </a>
            </div>

            <div style='position: relative; min-height: 100%; background-color: #5dcf72;'>
                <div style='position: absolute; width: 100%; right:0; color: white; text-align: right;  padding: 5px;'>
                    <div style='font-size: 30px;'>
                        ₱<span id='gross'>185M+</span>
                    </div>
                    <div style='font-size: 20px;'>
                        Total Contracts Value
                    </div>
                </div>
                <a href="javascript:;" style='color: white;'>
                    <div style='position: absolute; width: 100%; bottom: 0; background-color: #229c38; padding: 3px;'>View More</div>
                    <div style='position: absolute; bottom: 0; right: 0; padding: 3px;'><i class='fa fa-forward'></i></div>
                </a>
            </div>

            <!-- <div style='position: relative; min-height: 100%; background-color: #c25834'>
                <div style='position: absolute; width: 100%; right:0; color: white; text-align: right;  padding: 5px;'>
                    <div style='font-size: 30px;' id='users'>
                        135K+
                    </div>
                    <div style='font-size: 20px;'>
                        Number of Employee
                    </div>
                </div>

                <a href="javascript:;" style='color: white;'>
                    <div style='position: absolute; width: 100%; bottom: 0; background-color: #c24217; padding: 3px;'>View More</div>
                    <div style='position: absolute; bottom: 0; right: 0; padding: 3px;'><i class='fa fa-forward'></i></div>
                </a>
            </div> -->
        </div>

        <!-- Logs -->
        
        <div style=' padding: 10px; min-width: 10.5in; height: 3in; display: grid;  grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: 5%;'>
            <div style='width: 100%;'>
                <div style="display: flex; justify-content: right; justify-items: right;">
                    <label for="Periodicals" style="padding: 2%">Periodicals: </label>
                    <select name="Periodicals" id="Periodicals" style="width: 100px" class="col-xs-1 form-control">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: center; justify-items: center;">
                    <canvas id="myChart" style="width:100%; max-width:500px; min-height: 200px;"></canvas>
                </div>
            </div>
            
            <div>&nbsp;</div>
    
            <div id="TRANSACTION_MODULE" style='display: relative; width: 100%; border: 1px solid; border-radius: 20px 20px 0 0;'>     
                <div style="display: flex; justify-content: center; justify-items: center; background-color:#2d5f8b; color: white; border-radius: 20px 20px 0 0;">
                    <h4><?php 
                            if(in_array("DashboardSales.php", $page)){
                                echo "Recent Sales Invoice";
                            } else if(in_array("DashboardPurchase.php", $page)){
                                echo "Recent Purchase Order";
                            }
                        ?></h4>
                </div>
                <div>
                    <label for="Approved" class="btn btn-sm btn-success" style="margin: 2px"> Approved </label>
                    <input type="radio" name="status" id="Approved" value="Approved" style="display: none">

                    <label for="Pending" class="btn btn-sm btn-warning" style="margin: 2px"> Pending </label>
                    <input type="radio" name="status" id="Pending" value="Pending" style="display: none">
                </div>
                <div style="display: relative;  max-height: 2.5in; overflow: auto;" id="summary">
                </div>
            </div>
            <div id="RecentLog" style="display: relative; width: 100%; border: 1px solid; border-radius: 20px 20px 0 0;">
                <div style="display: flex; justify-content: center; justify-items: center; background-color:#2d5f8b; color: white; border-radius: 20px 20px 0 0;">
                    <h4>Recent Logs</h4>
                </div>
                <div style="display: relative;  max-height: 2.5in; overflow: auto;" id="logs">
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>

<script type='text/javascript'>
    $(document).ready(function(){
        $(".datepicker").datetimepicker({
            format: "MM/DD/YYYY",
        });

        LoadHeader();
        loadlinegraph();
        loadsummary();
        loadlogs();

        $("#Periodicals").change(function(){
            loadlinegraph($(this).val());
        })

        $("input[name='status']").change(function(){
            if($(this).is(":checked")){
                loadsummary($(this).val())
            }
        })
    });


    function LoadHeader(){

        let from = $('#datefrom').val();
        let to = $("#dateto").val();
        $.ajax({
            url: 'th_loadheader.php',
            data: { from: from, to: to },
            dataType: 'json',
            async: false,
            success: function(res){
                console.log(res)
                if(res.valid){
                    $('#total').text(res.total)
                    $('#totaltxt').text(res.label)
                    $('#gross').text(res.cost)
                    $('#Highest').text(res.best_rank)
                    // $('#purchase').text(res.purchase)
                    // $('#profit').text(res.cost)
                    // $('#users').text(res.user)
                    
                } 
            },
            error: function(res){
                console.log(res)
            }
        })
    }

    function loadlinegraph(Periodicals = $("#Periodicals").val()){
        $.ajax({
            url: "th_loadgraphs.php",
            type: "post",
            data: {
                Periodicals: Periodicals
            },
            dataType: 'json',
            async: false,
            success: function(res){
                loadchart(res.Periodicals, res.values)
            },
            error: function(res){
                console.log(res)
            }
        });
    }

    function navigate(link){
        location.href = link.toString();
    }

    function loadsummary(status = "Approved") {
        $.ajax({
            url: "th_loadsummary.php",
            type: "post",
            data: { status: status },
            dataType: "json",
            async: false,
            success: function (res) {
                console.log(res);
                $("#summary").empty();
                if(res.valid){
                    res.data.map((item, index) => {
                        let link = res.link.toString() + "?txtctranno=" + item.tranno.toString();
                        let DivNavigation = $("<div id='DivNavigation'>").click( function(){
                            navigate(link);
                        })
                        $(DivNavigation).append(
                            $("<div style='display: flex; width: 100%; padding: 5px;'>").append(
                                $("<div style='font-weight: bold; font-size: 14px; width: 75%;' id='title'>").text(item.names),
                                $("<div style='flex-grow: 1; display: flex; justify-content: flex-end; align-items: center; color: green; font-size: 12px' id='date'>").text(item.dates)
                            ),
                            $("<div style='width:100%; max-height: 30px; color: grey; font-size: 12px; overflow: hidden; padding: 5px' id='remarks'>").text(item.remarks)
                        ).appendTo("#summary");
                    });
                } else {
                    console.log(res.msg)
                }
            },
            error: function (msg) {
                console.log(msg);
            }
        });
    }

    function loadchart(months, values){
        new Chart("myChart", {
            type: "bar",
            data: {
                labels: months,
                datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "rgba(100,65,255,1.0)",
                borderColor: "rgba(0,0,255,0.1)",
                data: values
                }]
            },
            options: {
                legend: { display: false },
                scales: {
                yAxes: [{
                    ticks: {
                    beginAtZero: true,
                    }
                }]
                }
            }
        });
    }

    function loadlogs(){
        $.ajax({
            url: "th_loadlogs.php",
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid){
                    res.data.map((item, index) => {
                        $("<div style='display: relative; border: 1px solid #a6a6a6; margin: 2px;'>").append(
                            $("<div style='display: flex; width: 100%; padding: 5px;'>").append(
                                $("<div style='font-weight: bold; font-size: 14px; width: 75%;' id='title'>").text(item.module),
                                $("<div style='flex-grow: 1; display: flex; justify-content: flex-end; align-items: center; color: green; font-size: 12px' id='date'>").text(item.ddate)
                            ),
                            $("<div style='width:100%; max-height: 30px; color: grey; font-size: 12px; overflow: hidden; padding: 5px' id='remarks'>").text(item.cremarks)
                        ).appendTo("#logs");
                    })
                } else {
                    console.log(res.msg)
                }
            }, 
            error: function(msg){
                console.log(msg)
            }
        })
    }
    
</script>