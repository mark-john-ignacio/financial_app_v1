<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
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
        <div style=' padding: 10px; min-width: 10.5in; height: 4.5in; display: grid;  grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: 5%;'>
            <div id="TRANSACTION_MODULE" style='display: relative; width: 100%; border: 1px solid;'>     
                <div style="display: flex; justify-content: center; justify-items: center; padding: 5px; background-color:#2d5f8b; color: white">
                    <h3>Latest Activity</h3>
                </div>
                <div style="display: relative;  max-height: 3.5in; overflow: auto;">
                    <?php 
                        $sql = "SELECT DISTINCT(ctranno), cmodule FROM glactivity WHERE compcode = '$company' ORDER BY nidentity DESC LIMIT 10";
                        $query = mysqli_query($con, $sql);
                        while($row = $query -> fetch_assoc()):
                            $transaction = $row['ctranno'];
                            $sql = match($row['cmodule']){
                                "APV" => "SELECT cpayee as named, dapvdate as due, cpaymentfor as remarks FROM apv WHERE compcode = '$company' AND ctranno = '$transaction'",
                                "PV" => "SELECT cpayee as named, dtrandate as due, cpaymethod as remarks FROM paybill WHERE compcode = '$company' AND ctranno = '$transaction'",
                                "OR" => "SELECT b.cname as named, dcutdate as due, cpaymethod FROM receipt a LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid WHERE a.compcode = '$company' AND a.ctranno = '$transaction' ",
                                "JE" => "SELECT djdate as due, cmemo as remarks FROM journal WHERE compcode = '$company' AND ctranno = '$transaction'",
                                "SI" => "SELECT b.cname as named, a.dcutdate as due, cremarks as remarks FROM sales a LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid WHERE a.compcode = '$company' AND a.ctranno = '$transaction",
                                "IN" => "SELECT b.cname as named, a.dcutdate as due, cremarks as remarks FROM ntsales a LEFT JOIN customers b ON a.compcode = b.compcode AND a.ccode = b.cempid WHERE a.compcode = '$company' AND a.ctranno = '$transaction'",
                            };
                            $queries = mysqli_query($con, $sql);
                            $list = $queries -> fetch_assoc();
                    ?>
                        <div style="display: relative; border: 1px solid; margin: 2px;" onclick="return false">
                            <div style="display: flex; width: 100%; padding: 5px;">
                                <div style="font-weight: bold; font-size: 18px">
                                    <?= $list['named'] ? $list['named'] : $transaction ?>
                                </div>
                                <div  style="display: flex; justify-content: right; justify-items: right; width: 100%; color: green; font-size: 14px">
                                    <?= $list['due'] ?>
                                </div>
                            </div>
                            <div style="width:75%; max-height: 30px; color: grey; font-size: 14px; overflow: hidden; padding: 5px">
                                <?= $list['remarks'] ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div style='display: flex; justify-content: center; justify-items: center; text-align:center; width: 100%'>
                <div class="display: flex; ">
                    <canvas id="myChart" style="width:500px; max-width:500px; min-height: 200px;"></canvas>
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
        loadTransaction();
        loadlinegraph();

        $("#dateto").on('change', function(){
            let from = $('#datefrom').val();
            let to = $("#dateto").val();
            console.log(from)
            console.log(to)
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
                    } 
                },
                error: function(res){
                    console.log(res)
                }
            })
        })

        $("#datefrom").on('change',function(){
            let from = $('#datefrom').val();
            let to = $("#dateto").val();
            console.log(from)
            console.log(to)
            $.ajax({
                url: 'th_loadheader.php',
                data: { from: from, to: to },
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $('#total').text(res.total)
                        $('#totaltxt').text(res.label)
                        $('#gross').text(res.cost)
                        $('#Highest').text(res.best_rank)
                    } 
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
    });


    function LoadHeader(){

        let from = $('#datefrom').val();
        let to = $("#dateto").val();
        console.log(from)
        console.log(to)
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

    function loadlinegraph(){
        $.ajax({
            url: "th_loadgraphs.php",
            data: {},
            dataType: 'json',
            async: false,
            success: function(res){
                loadchart(res.week, res.values)
            },
            error: function(res){
                console.log(res)
            }
        });
    }

    function loadTransaction(){
        $.ajax({
            url: 'th_loadtransaction.php',
            dataType: 'json',
            async: false,
            success: function(res){
                console.log(res);
                res.map((item, index) => {
                    if(item.valid){
                        $("TRANSACTION_MODULE").append("<div style='display: relative; border: 1px solid; padding: 10px;'>" +
                        "<div style='display: flex; width: 100%;'>"+
                            "<div style='font-weight: bold; font-size: 18px'>"+item.name+"</div>"+
                            "<div  style='display: flex; justify-content: right; justify-items: right; width: 100%; color: green; font-size: 14px'> "+item.date+" </div> </div>" +
                        "<div style='width:75%; max-height: 30px; color: grey; font-size: 14px; overflow: hidden'> Description </div> </div>")
                        // $("<tr>").append(
                        //     $("<td style='text-align: center'>").html(''),
                        //     $("<td style='text-align: center'>").html(item.name),
                        //     $("<td style='text-align: center'>").html(item.date)
                        // ).appendTo(".table tbody")
                    }
                })
            },
            error: function(res){
                console.log(res)
            }
        });
    }

    function loadchart(weeks, values){
        new Chart("myChart", {
            type: "line",
            data: {
                labels: weeks,
                datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "rgba(0,0,255,1.0)",
                borderColor: "rgba(0,0,255,0.1)",
                data: values
                }]
            },
            options: {
                legend: {display: false},
                scales: {
                yAxes: [{ticks: {min: 0}}],
                }
            }
        });
    }
    
</script>