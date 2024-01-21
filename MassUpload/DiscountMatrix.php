<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap-datetimepicker.css">
        
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>

    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" language="javascript" src="../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
    <title>MyxFinancials</title>
</head>
<body>
    <div style='float: center; padding-top: 1%;'>
        <center>
        <form id="frm" method="POST" enctype="multipart/form-data">
            <div style='width: 50%; border: 1px solid black;'>
                <div style='background-color: #2d5f8b; padding: 10px; color: white; text-align: left; font-weight: bold;'>Discount Matrix Mass Uploading</div>
                <div style='width: 80%; padding-top: 30px'>
                    <div class="row" id='inputs' style='padding-bottom: 30px;'>
                        <div style='padding-bottom: 10px;'>
                            <label for="label">Enter Label</label>
                            <input type="text" name="label" id="label" placeholder="Enter Label..." class="form-control input-sm" require>
                        </div>
                        
                        <div style='padding-bottom: 10px;'>
                            <label for="description">Enter Description</label>
                            <input type="text" name="description" id="description" placeholder="Enter Description..." class="form-control input-sm" require>
                        </div>
                        <div style='padding-bottom: 10px;'>
                            <label for="description">Enter Sales Debit Account</label>

                            <div class="col-xs-12 nopadding">
                                <div class="col-xs-3 nopadding">
                                    <input type="text" class="form-control input-sm" id="salesdracct" name="salesdracct" value='' readonly placeholder="Account Code">
                                </div>
                                <div class="col-xs-9 nopadwleft">
                                    <input type="text" class="form-control input-sm" id="salesdracctnme" name="salesdracctnme" value='' placeholder="Account Title">
                                </div>
                            </div>

                        </div>

                        <div style='display: flex;' class="col-sm-6 nopadwtop">
                            <label for="effectdate" style='width: 100px; padding-top:2%'>Effect Date</label>
                            <input type="text" class="datepicker form-control input-sm" id="effectdate" name="effectdate" value='<?= date("m/d/Y");?>' require>
                        </div>

                        <div style='display: flex;' class="col-sm-6 nopadwtop">
                            <label for="duedate" style='width: 80px; padding-top:2%'>Due Date</label>
                            <input type="text" class="datepicker form-control input-sm" id="duedate" name="duedate" value='<?= date("m/d/Y");?>' require>
                        </div>
                    </div>

                    <div class="row" style='!important; padding-bottom: 30px;'>
                        <select class='form-control input-sm' name="type" id="type">
                            <option value="Preview">Preview</option>
                            <option value="Save">Save</option>
                        </select>
                    </div>

                    <div style='width: 80%; height: 80px; background-color: #519bc9'>
                        <label for="excel_file" class="custom-file-upload" style='padding-top: 10px'>
                            File Upload
                        </label>
                        <input type="file" name="excel_file" accept=".xlsx, .xls" class='btn btn-sm'>
                    </div>
                    
                    <div style='padding-top: 30px; padding-bottom: 30px'>
                        <input type="button" id='submit' value="Submit" class='btn btn-success btn-sm' >
                        <a href="templates/Discount-Matrix-Template.xlsx" download="Discount-Matrix-Template.xlsx" class="btn btn-info btn-sm" id="download" >Download Template</a>
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
        $("#salesdracctnme").typeahead({
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../Sales/th_accounts.php",
					dataType: "json",
					data: {
						query: $("#salesdracctnme").val()
					},
					success: function (data) {
						console.log(data);
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.acct + '</span><br><small>' + item.name + '</small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 

				$('#salesdracctnme').val(item.name).change(); 
				$("#salesdracct").val(item.acct);

			}
		});

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
                        if(res.valid){
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
                        } else {
                            alert(res.msg)
                        }
                    },
                    error: function(res){
                        console.log(res)
                    }
                })
            } else if (type === "Save"){
                $.ajax({
                    url: "th_saveDM.php",
                    type: 'POST',
                    data: formdata,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if(res.valid){
                            alert(res.msg)
                            location.href = "../MasterFiles/Items/discmatrix.php";
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