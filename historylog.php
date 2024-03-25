<?php
if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['pageid'] = "historylog.php";

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
                        //get the user logs based on who is log in
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
                            <!--extract the date to the database -->
                            <td><?php echo $logged_date = date('Y-m-d', strtotime($row['logged_date'])); ?></td>
                            <td>
                                <div id="itmstat<?php echo $row['status']; ?>">
                                    <?php //checking the status and set color green, red, orange
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
                            <!--extract the time to the database -->
                            <td><?php echo $logged_time = date('H:i:s', strtotime($row['logged_date'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>

    <?php mysqli_close($con); ?>

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
            login(); // call the login function
            
            
        });

        //login function for autologout
        function login() {
            
            // Simulate successful login
            lastActivityTime = Date.now();
            console.log("User logged in at: " + formatTime(lastActivityTime));
            
            // Start monitoring for activity
            document.addEventListener("mousemove", updateActivityTime);
            document.addEventListener("keypress", updateActivityTime);
            document.addEventListener("input", updateActivityTime);
            document.addEventListener("click", updateActivityTime);
            
            // Start the auto-logout timer
            startLogoutTimer();
        }
        // converting the time to hh mm ss
        function formatTime(milliseconds) {
            let totalSeconds = Math.floor(milliseconds / 1000);
            let hours = Math.floor(totalSeconds / 3600); // Calculate total hours
            let remainingSeconds = totalSeconds % 3600; // Remaining seconds after calculating hours
            let minutes = Math.floor(remainingSeconds / 60); // Calculate minutes from remaining seconds
            let seconds = remainingSeconds % 60; // Calculate remaining seconds after calculating minutes
            
            return `${hours}h ${minutes}m ${seconds}s`;
        }

        //update the actiivity time from historylog.php and main.php
        function updateActivityTime() {
            parent.updateActivityTime();
            lastActivityTime = Date.now();
            console.log("Last activity time: " + formatTime(lastActivityTime));
        }

        function startLogoutTimer() {
            logoutTimer = setInterval(function() {
                checkLogoutTime();
            }, 10000); // Check every 10 seconds
        }

        //compare the time to last activity to time now
        function checkLogoutTime() {
            let currentTime = Date.now();
            let timeDifferenceInSeconds = (currentTime - lastActivityTime) / 1000; // Convert milliseconds to seconds
            console.log(timeDifferenceInSeconds);
            if (timeDifferenceInSeconds >= 10) { // 10 seconds
                logout(); // Pass currentPage as an argument
            }
        }
        // add to prevent multiple alert
        window.addEventListener('message', function(event) {
            if (event.data && event.data.logoutInitiated) {
                
                handleLogout();
            }
        });

        function handleLogout() {
            // Perform logout actions for the logout frame
            clearInterval(logoutTimer);
            alert("Auto logout due to inactivity." );
            window.location.href = "logout.php?logout_reason=inactivity";
        }

    </script>
</body>
</html>
