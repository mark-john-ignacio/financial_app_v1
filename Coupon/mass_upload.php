<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>
    
	<link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap.js"></script>

</head>
<body>
    <div style='float: center; padding-top: 10%;'>
        <center>
        <form action="table_excel.php" id="frm" method="POST"  enctype="multipart/form-data">
            <div style='width: 50%; border: 1px solid black;'>
                <div style='background-color: #2d5f8b; padding: 10px; color: white; text-align: left; font-weight: bold;'> Mass Uploading</div>
                <div style='width: 70%; padding-top: 30px'>
                    <div style='padding-bottom: 30px;'>
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
                        <input type="submit" id='submit' value="Upload Excel File" class='btn btn-success btn-sm' >
                    </div>
                </div>
            </div>
        </form>
        </center>
    </div>
    
</body>
</html>

<script type='text/javascript'>
    // $(document).ready(function(){
    //     $("#submit").click(function(e){
    //         e.preventDefault();
    //         let formdata = new FormData($("form")[0]);
            
    //         for (var pair of formdata.entries()) {
    //             console.log(pair[0] + ': ' + pair[1]);
    //         }


    //         // $.ajax({
    //         //     url: 'mass_excel.php',
    //         //     type: 'POST',
    //         //     data: formdata,
    //         //     dataType: 'json',
    //         //     processData: false,
    //         //     contentType: false,
    //         //     success: function (res) {
    //         //         var responseData = JSON.parse(res);

    //         //         for (let i = 0; i < responseData.length; i++) {
    //         //             var rowData = responseData[i];
    //         //             var newRow = $("<tr>");

    //         //             for (let j = 0; j < rowData.length; j++) {
    //         //                 var newCell = $("<td>").text(rowData[j]);
    //         //                 newRow.append(newCell);
    //         //             }

    //         //             $("table > tbody").append(newRow);
    //         //         }
    //         //     },
    //         //     error: function (xhr, status, error) {
    //         //         console.log(error);
    //         //     }
    //         // });

    //         $.ajax({
    //             url: 'mass_excel.php',
    //             type: 'POST',
    //             data: formdata,
    //             processData: false,
    //             contentType: false,
    //             dataType: 'json',
    //             success: function (res) {
    //                 if (Array.isArray(res)) {
    //                     // Assuming res is an array of arrays
    //                     var tableBody = $("table > tbody");

    //                     res.forEach(function (rowData, i) {
    //                         if (i !== 0) {
    //                             var newRow = $("<tr>");

    //                             rowData.forEach(function (cellData) {
    //                                 var newCell = $("<td>").text(cellData);
    //                                 newRow.append(newCell);
    //                             });

    //                             tableBody.append(newRow);
    //                         }
    //                     });
    //                 } else {
    //                     console.error("Invalid data format received from the server");
    //                 }
    //             },
    //             error: function (jqXHR, textStatus, errorThrown) {
    //                 console.log("AJAX request failed:", textStatus, errorThrown);
    //             }
    //         });
    //     })
    // })
</script>