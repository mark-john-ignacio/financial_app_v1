<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $sql = "SELECT nidentity, ccode, cdesc FROM groupings WHERE compcode='$company' and ctype = 'ITMPMVER'";
    $query = mysqli_query($con, $sql);
    $pms = [];
    while($row = $query -> fetch_assoc()){
        array_push($pms, $row);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
        
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>

    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
    <title>MyxFinancials</title>
</head>
<body>
    <div style='float: center; padding-top: 3%;'>
        <center>
        <form id="frm" method="POST" enctype="multipart/form-data">
            <div style='width: 50%; border: 1px solid black;'>
                <div style='background-color: #2d5f8b; padding: 10px; color: white; text-align: left; font-weight: bold;'>Sales Price Mass Uploading</div>
                <div style='width: 70%; padding-top: 30px'>
                    <div style='padding-bottom: 30px;'>

                        <div class='nopadwtop'>
                            <label for="matrix">Select a Price Matrix</label>
                            <select name="matrix" id="matrix" class='form-control input-sm'>
                                <?php foreach($pms as $list): ?>
                                    <option value="<?= $list['ccode'] ?>"><?= $list['cdesc'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class='col-sm-12' style='padding-left: 0; padding-top: 10px; display: flex;'>
                            <label for="effectdate" class='col-xs-6'>Effectivity Date</label>
                            <input type="text" id='effectdate' name='effectdate' class='datepicker form-control input-sm' style='padding-left: 20px;' value="<?= date("Y-m-d") ?>">  
                        </div>
                        <br>
                        
                        <div class='nopadwtop'>
                            <label for="description">Description</label>
                            <textarea name="description" id="description" cols="50" rows="5" class='form-control input-sm' style='resize: none'></textarea>
                        </div>
                        <div class='nopadwtop'>
                            <select class='form-control input-sm' name="type" id="type">
                                <option value="Preview">Preview</option>
                                <option value="Save">Save</option>
                            </select>
                        </div>
                    </div>
                    <div style='width: 80%; height: 80px; background-color: #519bc9'>
                        <label for="excel_file" class="custom-file-upload" style='padding-top: 10px'>
                            File Upload
                        </label>
                        <input type="file" name="excel_file" accept=".xlsx, .xls" class='btn btn-sm'>
                    </div>
                    
                    <div style='padding-top: 30px; padding-bottom: 30px'>
                        <input type="button" id='submit' value="Submit" class='btn btn-success btn-sm' >
                        <a href="templates/Sales-Price-List-Template-Mass.xlsx" download="Sales-Price-List-Template-Mass.xlsx" class="btn btn-info btn-sm" id="download" >Download Template</a>
                    </div>
                </div>
            </div>
        </form>
        </center>
    </div>

    <div class='modal fade' id='mymodal' role="dialog">
        <div class="modal-dialog" role="document" style='width: 100%; padding-left: 0;'>
            <div class='modal-content' >
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader">Preview Excel</h3>
                </div>
                <div class='modal-body' style='height: 4in; overflow: auto;'>
                    <table class='table' id='ExcelList' style="width: 100%; ">
                        <thead style='background-color: #019aca'></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>     
        </div>
    </div>
</body>
</html>

<script type='text/javascript'>
    $(function(){
        $('.datepicker').datetimepicker({
            format: 'MM/DD/YYYY',
            minDate: new Date(),
        })
    })
    $(document).ready(function(){
        

        $("#submit").click(function(){
            $("#ExcelList tbody").empty();
            $("#ExcelList thead").empty();

            let formdata = new FormData($("form")[0]);
            let type = $("#type").val();

            if(type == "Preview"){
                $("#mymodal").modal("show")
                $.ajax({
                    url: "mass_excel.php",
                    type: 'POST',
                    data: formdata,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        console.log(res)
                        for (let i = 0; i < res.data.length; i++) {
                            let data = res.data[i];
                            let row = $("<tr>");

                            for (let j = 0; j < data.length; j++) {
                                let cell;
                                if (i === 0) {
                                    cell = $("<th>").text(data[j]);
                                } else {
                                    cell = $("<td>").text(data[j]);
                                }
                                row.append(cell);
                            }

                            if (i === 0) {
                                row.appendTo("#ExcelList > thead");
                            } else {
                                row.appendTo("#ExcelList > tbody");
                            }
                        }
                    },
                    error: function(res){
                        console.log(res)
                    }
                })
            } else if (type === "Save"){
                $.ajax({
                    url: "th_savePM.php",
                    type: 'POST',
                    data: formdata,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if(res.valid){
                            alert(res.msg)
                        } else {
                            alert(res.msg)
                        }
                    },
                    error: function(res){
                        console.log(res)
                    }
                })
            }
        })
    })
</script>