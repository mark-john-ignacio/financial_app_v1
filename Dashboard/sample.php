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
    <div style="display: relative;  max-height: 2.5in; overflow: auto;" id="summary">

    </div>
    
</body>
</html>

<script>
    $(document).ready(function(){
        loadsummary();
    })
    function loadsummary() {
        $.ajax({
            url: "th_loadsummary.php",
            type: "post",
            dataType: "json",
            async: false,
            success: function (res) {
                console.log(res);
                if(res.valid){
                    res.data.map((item, index) => {
                        $("<div style='display: relative; border: 1px solid; margin: 2px;' onclick='return false'>").append(
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
</script>