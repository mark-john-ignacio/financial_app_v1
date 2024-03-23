<?php
if (!isset($_SESSION)) {
    session_start();
}
if (basename($_SERVER['PHP_SELF']) === 'historylog.php') {
    setcookie('historylog', 'active', time() + 15, "/"); // Cookie expires in 15 seconds
}
$_SESSION['pageid'] = "UOM.php";

include('Connection/connection_string.php');
include('include/accessinner.php');
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="Bootstrap/css/bootstrap.css">
    <link href="global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="Bootstrap/js/bootstrap.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

    <title>MYX Financials</title>
</head>
<body style="padding:5px">
    <div>
        <section>
            <div>
                <div style="float:left; width:50%">
                    <font size="+2"><u>History Logs</u></font>   
                </div>            
            </div>
            <br><br>
            <br><br>
            <table id="example" class="display styles" cellspacing="0">
                <thead>
                    <tr>
                        <th width="100">LOG ID</th>
                        <th width="100">USER ID</th>
                        <th width="80">DATE</th>
                        <th width="80">STATUS</th>
                        <th width="80">TIME</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM users_log WHERE Userid = '$employeeid' ORDER BY logged_date DESC";
                        $result = mysqli_query($con, $sql);
                        if (!$result) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        } 
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    ?>
                        <tr>
                            <td><?php echo $row['logid']; ?></td>
                            <td><?php echo $row['Userid']; ?></td>
                            <td><?php echo $logged_date = date('Y-m-d', strtotime($row['logged_date'])); ?></td>
                            <td>
                                <div id="itmstat<?php echo $row['status']; ?>">
                                    <?php 
                                        if ($row['status'] == "Online") {
                                            echo "<span class='label label-success'>ONLINE</span>";
                                        } else if ($row['status'] == "Offline"){
                                            echo "<span class='label label-danger'>OFFLINE</span>";
                                        } else{
                                            echo "<span class='label label-warning'>AUTO-LOGOUT</span>";
                                        }
                                    ?>
                                </div>
                            </td>
                            <td><?php echo $logged_time = date('H:i:s', strtotime($row['logged_date'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>

    <?php mysqli_close($con); ?>
    <script src="autologout.js"></script> <!-- Include the autologout.js script here -->
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
</body>
</html>
