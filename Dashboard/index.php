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
    <title>MyxFinancials</title>
</head>
<body>
    <div class='container-fluid' style=' padding-top: 2%;'>

    <!-- Header -->
        <div style='position: relative; min-width: 5.5in; height: .5in; background-color: #2d5f8b; '>
            <div style='position: absolute; left:0; padding: 10px; font-size: 20px; color: white;'>
                Dashboard
            </div>
            <div style='position: absolute; width: 350px; right: 0; padding: 8px; font-size: 15px;'>
                <div style='display: flex'>
                    <div class='col-xs-8'>
                        <input type="date" class='form-control input-sm' id='datefrom' value="<?= date('Y-m-d',strtotime(date('m/d/y'))) ?>">
                    </div>
                    <div class='col-xs-8'>
                        <input type="date" class='form-control input-sm' id='dateto' value="<?= date('Y-m-d',strtotime(date('m/d/y'))) ?>">
                    </div>
                    
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
                        Average Contracts
                    </div>
                </div>
                <a href="javascript:;" style='color: white;'>
                    <div style='position: absolute; width: 100%; bottom: 0; background-color: #229c38; padding: 3px;'>View More</div>
                    <div style='position: absolute; bottom: 0; right: 0; padding: 3px;'><i class='fa fa-forward'></i></div>
                </a>
            </div>

            <div style='position: relative; min-height: 100%; background-color: #c25834'>
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
            </div>
        </div>

        <!-- Logs -->
        <div style='margin-top: 10px; padding: 10px; min-width: 5.5in; height: 1.5in; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-gap: 5%;'>
            <div>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th style="text-align: center; width: 50%">Description</th>
                            <th style="text-align: center">Transaction</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style='display: flex; justify-content: center; justify-items: center; text-align:center; border: 1px solid'>
                hello
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
</script>