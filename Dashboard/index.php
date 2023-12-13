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
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

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
                    <div style='font-size: 30px;'>
                        ₱ <span id='total'> 185M+</span>
                    </div>
                    <div style='font-size: 20px;'>
                        <?php 
                            if(in_array("DashboardSales.php", $page)){
                                echo "Total Invoice Amount";
                            } else if(in_array("DashboardPurchase.php", $page)){
                                echo "Total Payable Amount";
                            }
                        ?>
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
                        ₱ <span id='gross'>185M+</span>
                    </div>
                    <div style='font-size: 20px;'>
                        <?php 
                            if(in_array("DashboardSales.php", $page)){
                                echo "Total Collected Amount";
                            } else if(in_array("DashboardPurchase.php", $page)){
                                echo "Total Paid Amount";
                            }
                        ?>
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
                <div style="display: flex; justify-content: center; justify-items: center; background-color:#2d5f8b; color: white; border-radius: 20px 20px 0 0;">
                    <h4 style="padding: 3px"><?php 
                        if(in_array("DashboardSales.php", $page)){
                            echo "Sales Invoice Bar Chart";
                        } else if(in_array("DashboardPurchase.php", $page)){
                            echo "Purchase Order Bar Chart";
                        }
                    ?></h4>
                </div>
                <div style="display: flex; justify-content: right; justify-items: right; padding: 5px; border-left: 1px solid grey; border-right: 1px solid grey;">
                    <div class="dropdown">
                        <button class="dropdown-toggle btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Filter
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="width: 250px;">
                            <div class="dropdown-item" style="display: flex; padding: 2%;">
                                <label for="Periodicals" style="width: 100%;">Periodicals: </label>
                                <select name="Periodicals" id="Periodicals" style="width: 100%;" class="col-xs-1 form-control" onclick="event.stopPropagation();">
                                    <option value="monthly">Monthly</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                            </div>
                            <div style="display: none; padding: 2%;" id="dates">
                                <label for="Day" style="width: 100%;">Date of:</label>
                                <input type="text" id="Day" name="Day" style="width: 100%;" class="datepick col-xs-1 form-control" value="<?= date("m-d") ?>">
                            </div>
                            <div style="display: flex; padding: 2%;">
                                <label for="Year" style="width: 100%;">Year of: </label>
                                <input type="text" id="Year" name="Year" style="width: 100%;" class="yearpick col-xs-1 form-control" value="<?= date("Y") ?>">
                            </div>
                            <div class="dropdown-item" style="display: flex; justify-content: right; justify-items: right; padding: 2%;">
                                <button id="graphfilter" class="btn btn-sm btn-primary" onclick="loadbargraph();">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display: flex; justify-content: center; justify-items: center; max-height: 600px; border: 1px solid grey; border-top: 0px solid; ">
                    <canvas id="myChart" style="width:100%; max-width:500px; min-height: 200px;"></canvas>
                </div>
            </div>
            
            <div style='width: 100%;'>
                <div style="display: flex; justify-content: center; justify-items: center; background-color:#2d5f8b; color: white; border-radius: 20px 20px 0 0;">
                    <!-- <label for="Periodicals" style="padding: 2%">Periodicals: </label>
                    <select name="Periodicals" id="Periodicals" style="width: 100px" class="col-xs-1 form-control">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select> -->
                    <h4 style="padding: 3px"><?php 
                            if(in_array("DashboardSales.php", $page)){
                                echo "Sales Invoice Classification Chart";
                            } else if(in_array("DashboardPurchase.php", $page)){
                                echo "Purchase Order Classification Chart";
                            }
                        ?></h4>
                </div>
                <div style="display: flex;  border: 1px solid grey; padding: 2%; max-height: 600px; overflow: auto;">
                    <!-- <div style="display: flex; width:100%; min-height: 200px; overflow: auto;"> -->
                    <canvas id="PieLegends" style="position: relative; width: 100%;  min-height: 300px; overflow: auto"></canvas>
                    <!-- </div> -->
                </div>
            </div>
    
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
        $('.yearpick').datetimepicker({
            defaultDate: moment(),
            viewMode: 'years',
            format: 'YYYY'
        });

        $('.datepick').datetimepicker({
            defaultDate: moment(),
            format: 'MM-DD'
        });


        LoadHeader();
        loadbargraph();
        loadpiechart();
        loadsummary();
        loadlogs();

        $("#Periodicals").change(function(){
        //     loadbargraph($(this).val());
            if($(this).val() === "weekly"){
                $("#dates").css("display", "flex");
            } else {
                $("#dates").css("display", "none");
            }
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
                if(res.valid){
                    let ranker = res.best_rank;

                    if(ranker.trim().length > 30){
                        ranker = ranker.substr(0,30) + "...";
                    }

                    $('#total').text(res.total)
                    $('#totaltxt').text(res.label)
                    $('#gross').text(res.cost)
                    
                    $('#Highest').text(ranker)
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

    function loadbargraph(Periodicals = $("#Periodicals").val()){
        let year = $("#Year").val();
        let days = $("#Day").val();
        $.ajax({
            url: "th_loadgraphs.php",
            type: "post",
            data: {
                Periodicals: Periodicals,
                year: year,
                days: days,
            },
            dataType: 'json',
            async: false,
            success: function(res){
                barchart(res.Periodicals, res.values)
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

    function barchart(months, values){
        var colors = [];
        for (var i = 0; i < months.length; i++) {
            colors.push(getRandomColor());
        }
        new Chart("myChart", {
            type: "bar",
            data: {
                labels: months,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    // backgroundColor: "rgba(100,65,255,1.0)",
                    backgroundColor: colors,
                    borderColor: "rgba(0,0,255,0.1)",
                    borderWidth: 1,
                    data: values
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        display: false // Set to false to hide data labels
                    }
                },
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                },
            }
        });
    }
    function PieChart(label, values) {
        // Sort values and labels based on values
        const sortedData = values.map((value, index) => ({ value, label: label[index] })).sort((a, b) => b.value - a.value);
        
        // Get the top 5 values and labels
        const top5Values = sortedData.slice(0, 5).map(entry => entry.value);
        const top5Labels = sortedData.slice(0, 5).map(entry => entry.label);

        // Generate random colors for the top 5 values
        const colors = top5Labels.map(() => getRandomColor());

        var options = {
            tooltips: {
                enabled: true
            },
            plugins: {
                datalabels: {
                formatter: (value, context) => {
                    // collecting sum for all data 
                    const sum = context.dataset.data.reduce((acc, data) => acc + data, 0);
                    // Converting to Percentage
                    let percentage = (value * 100 / sum).toFixed(2) + "%";
                    return percentage;
                },
                color: '#fff',
                }
            }
        };
        
        new Chart("PieLegends", {
            type: "pie",
            data: {
                labels: top5Labels,
                datasets: [{
                    fill: false,
                    backgroundColor: colors,
                    lineTension: 0,
                    borderColor: "rgba(255,255,255,0.8)",
                    borderWidth: 1,
                    data: top5Values,
                }]
            },
            options: options
        });
    }

    function loadpiechart() {
        
        $.ajax({
            url: "th_loadpiechart.php",
            type: "post",
            dataType: "json",
            async: false,
            success: function(res) {
                if(res.valid) {
                    PieChart(res.label, res.data);
                } else {
                    console.log(res.msg)
                }
            },
            error: function (msg) {
                console.log(msg)
            }
        })
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
                                $("<div style='font-weight: bold; font-size: 14px; width: 75%;' id='title'>").text(item.ctranno),
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
    
    function getRandomColor() {
        var letters = "0123456789ABCDEF";
        var color = "#";
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

</script>