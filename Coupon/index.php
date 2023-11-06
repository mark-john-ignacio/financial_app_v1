<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include ('../Connection/connection_string.php');

    $company = $_SESSION['companyid'];
    $company = [];
    $query = mysqli_query($con,"SELECT * FROM company");
    if(mysqli_num_rows($query) != 0){
        while($row = $query -> fetch_assoc()){
            array_push($company, $row);
        }
    }
    // $_SESSION['pageid'] = "Coupon.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?v=<?php echo time();?>">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/DigiClock.css"> 

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../include/select2/select2.min.css?x=<?=time()?>"> 
    <script src="../include/select2/select2.full.min.js"></script>
    <title>Coupon</title>
</head>
<body>
    <div class='row nopadwtop2x' id='header' style="background-color: #2d5f8b; height:65px; margin-bottom: 5px !important">
        <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
            <img src="../images/LOGOTOP.png" width="150" height="50"/>
        </div>
    </div>
    <div class='container'>
        
        <div style="padding: 20%">
            <p class='input-sm' style="color: #098; font-weight: bold">Coupon Activation</p>
            <select name="company" id="company" class="form-control input-sm">
                <?php foreach($company as $list):?>
                    <option value="<?= $list['compcode'] ?>"><?= $list['compname'] ?></option>
                <?php endforeach; ?>
            </select>
            <div class='input-group margin-bottom-sm nopadwtop' >
                <input type="text" id="coupon" name="coupon" placeholder="Enter your coupon code ..." class='form-control input-sm' />
                <span class='input-group-addon nopadding'><button class="btn btn-info btn-xs " id='activateBtn'>Activate</button></span>
            </div>
            <p id='msg'></p>
        </div>
    </div>
</body>
</html>

<script type='text/javascript'>
    $(document).ready(function(){
        $('#activateBtn').on('click', function(){
            let coupon = $("#coupon").val();
            $.ajax({
                url: "th_coupon.php",
                data: {coupon: coupon.trim()},
                dataType: 'json',
                async: false,
                success: function(res){
                    if(res.valid){
                        $('#msg').text(res.msg)
                        $('#msg').css('color', 'Green')
                    } else {
                        $('#msg').text(res.msg)
                        $('#msg').css('color', 'RED')
                    }
                    setTimeout(function() {
                        location.reload()
                    }, 3000)
                },
                error: function(res){
                    console.log(res)
                }
            })
        })
    })
</script>