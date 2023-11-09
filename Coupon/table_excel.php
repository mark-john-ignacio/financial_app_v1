<?php
if(!isset($_SESSION)){
    session_start();
}

include("../Connection/connection_string.php");
$type = $_POST['type'];
$company = $_SESSION['companyid'];
$proceed = true;
$duplicate = false;

$excel_data = [];
if (isset($_FILES['excel_file']) || !empty($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] === 0) {
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (in_array($fileExt, ['xlsx', 'xls'])) {
            $uploadDir = './';
            $uploadedFile = $uploadDir . $file['name'];
            move_uploaded_file($file['tmp_name'], $uploadedFile);

            require '../vendor2/autoload.php';
            
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadedFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $i = 0;
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $rowdata = [];
            
                $hasNonNullValue = false;
                $i++;
                // if($i > 1){
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                
                        if (!is_null($cellValue)) {
                            $hasNonNullValue = true;
                        }
    
                        $rowdata[] = trim($cellValue);
                    }
                    if ($hasNonNullValue) {
                        // echo json_encode($rowdata);
                        array_push($excel_data, $rowdata);
                    }
                // }
            }

            unlink($uploadedFile);
        } else {
            echo "Please upload a valid Excel file (XLSX or XLS format).";
        }
    } else {
        echo "Error uploading the file. Please try again.";
        $proceed = false;
    }
} 

if($type === "Preview" && $proceed) {
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap2.css?v=<?php echo time();?>">
	    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <title>Document</title>
    </head>
    <body>
        <div class='container'>
            <div style='padding-top: 30px'>
                <a href="javascript:;" onclick='back()'><i class='fa fa-backward'></i> back</a>
            </div>
            <div style='padding-top: 30px;'>
                <table class="table">
                    <thead>
                        <tr>
                            <?php for($i = 0; $i < sizeof($excel_data[0]); $i++ ): ?>
                                <th><?= $excel_data[0][$i] ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 1; $i < sizeof($excel_data); $i++):?>
                            <tr> 
                            <?php $data = $excel_data[$i];
                                for($j = 0; $j < sizeof($data); $j++):?>
                                <td><?= $data[$j]?></td>
                            <?php endfor; ?>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>


<?php } else if($type === "Save" && $proceed) { 
    
    for($i = 1; $i < sizeof($excel_data); $i++){
        $data = $excel_data[$i];
        $sql = "SELECT * FROM `coupon` WHERE `compcode` = '$company' AND `CouponNo` = '{$data[0]}'";
        $query = mysqli_query($con, $sql);
        if(mysqli_num_rows($query) != 0){
            
        } else {
            $sql = "INSERT INTO `coupon`(`compcode`, `CouponNo`, `label`, `remarks`, `barcode`, `price`, `days`, `status`, `ddate`) 
            VALUES ('$company', '{$data[0]}', '{$data[1]}', '{$data[2]}', '{$data[3]}', '{$data[4]}', '{$data[5]}', 'INACTIVE', NOW())";
            if(mysqli_query($con, $sql)){
                ?>
                    <script>
                        alert("Successfully inserted");
                        location.href="mass_upload.php"
                    </script>
                <?php
            } else {
                ?>
                <script>
                    alert("Unsuccesfully inserted");
                    location.href="mass_upload.php"
                </script>
                <?php
            }
        }
        
    }

} else { ?>
    <script>
        alert("File not found! or File did not match the recommended File Template");
        location.href="mass_upload.php"
    </script>
<?php } 

// function deleteInserted($coupon){
//     global $con;
//     global $company;
    
//     for($i = 1; $i < sizeof($coupon); $i++){
//         $sql = "DELETE FROM coupon WHERE compcode = '$company' AND CouponNo = '{$coupon[$i][1]}' AND ddate = NOW()";
//         mysqli_query($con, $sql);
//     }
//      ? > 
//         < script>
//             alert("Coupon has an duplicate! Mass Upload Failed");
//             location.href="mass_upload.php"
//         < /script>
//     < ?php
// }
?>
<script>
    function back(){
        window.location = 'mass_upload.php'
    }
</script>