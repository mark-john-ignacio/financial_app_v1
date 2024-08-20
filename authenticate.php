<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    include('Connection/connection_string.php');
    include('Model/helper.php');

    if(isset($_POST['login'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $con->prepare("SELECT * FROM users WHERE Userid = BINARY ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $xp = ($row['Userid']) ?? "";
        
        if($xp != ""){

            if($row['cstatus']=="Blocked"){

                $GOYes="False";
                $xmessage = "User Blocked! Contact Administrator or Change your Password.";

            }elseif($row['cstatus']=="Active"){


                $GOYes = "True";
                $xmessage = "";

                if(password_verify($password, $row['password'])){

                    $xsessionid = session_id();
                    $dateNow = date('Y-m-d h:i:s');
                    $hashedIP = getMyIP();

                    $MAC = exec('getmac');
                    $MAC = strtok($MAC, ' ');

                    $hashedMAC = $MAC;

                    //check if user is currently logged sa ibang PC
                    if($row['session_ID']!="0"){
                        //check if sa same PC nakalogin (or naiwan ang login) 
                        if($row['machine_last_log']==$hashedIP){ // pag same PC go with login || $row['mac_last_log']==$hashedMAC
                            $GOYes="True";
                            $xmessage = "";
                        }else{ // pag nde check if more than 2hrs na ung last log... pag 24hrs or more na go with log na
                            $date1 = date("Y-m-d H:i:s");
                            $date2 = Date("Y-m-d H:i:s", strtotime($row['date_last_log']));;

                            $timestamp1 = strtotime($date1);
                            $timestamp2 = strtotime($date2);

                            $xhrs = abs($timestamp2 - $timestamp1)/(60*60);
                            if($xhrs >= 2){
                                $GOYes="True";
                                $xmessage = ""; 
                            }else{
                                $GOYes="False";
                                $xmessage = "Username currently logged in another PC!"; 
                            }

                        }

                    }else{

                        $GOYes="True";
                        $xmessage = ""; 

                    }
                    

                }else{
                    $GOYes="False";

                    if(isset($_SESSION['attempt'])){
                        $_SESSION['attempt'] = intval($_SESSION['attempt']) + 1;
                    }else{
                        $_SESSION['attempt'] = 1;
                    }

                    if(intval($_SESSION['attempt']) >= 5){

                        $dateNow = NULL;
                        $hashedIP = NULL;

                        $stmtlog = $con->prepare("UPDATE users set cstatus = 'Blocked', `session_ID` = 0, `date_last_log` = ?, `machine_last_log` = ? WHERE `userid` = ?");
                        $stmtlog->bind_param("sss", $dateNow, $hashedIP, $username);
                        $stmtlog->execute();
                        $stmtlog->close();
                       
                        $xmessage = "User Blocked! Contact Administrator or Change your Password.";
                        
                    }else{
                        $_SESSION['employeeid'] = $username;

                        $xmessage = "Invalid Password!";
                    }
                }   
                
            }else{
                $GOYes="False";
                $xmessage = "Username does not exist!"; 
            }

        }else{
            $GOYes="False";
            $xmessage = "Username does not exist!";     
        }
       
        $stmt->close();	

        if($GOYes=="True"){

            $stmtlog = $con->prepare("UPDATE users set `session_ID` = ?, `date_last_log` = ?, `machine_last_log` = ?, `machine_last_log` = ? WHERE `userid` = ?");
            $stmtlog->bind_param("sssss", $xsessionid, $dateNow, $hashedIP, $hashedMAC, $username);
            $stmtlog->execute();
            $stmtlog->close();

            $_SESSION['employeeid'] = $username;
            $_SESSION['session_id'] = $xsessionid;
            $_SESSION['employeename'] = $row['Fname'];
            $_SESSION['employeefull'] = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
            $_SESSION['status'] = $row['cstatus'];
            $_SESSION['companyid'] = getDefaultCompany($username);
            $_SESSION['timestamp']=time();


            $now = time();
            $your_date = strtotime($row['modify']);
            $datediff = $now - $your_date;
            $days = round($datediff / (60 * 60 * 24));

            $_SESSION['modify_pass'] = $days;

            $dateNow = date('Y-m-d h:i:s');
            $stmtlog = $con->prepare("INSERT INTO `users_log` (`Userid`, `status`, `machine`, `logged_date`) values (?, 'Online', ?, ?)");
            $stmtlog->bind_param("sss", $username, $hashedIP, $dateNow);
            $stmtlog->execute();
            $stmtlog->close();


            //if REMEBER ME is CLICKED
                if(isset($_POST['remember'])){
                    /**
                    * Store Login Credential
                    */
                    setcookie('username', $_POST['username'], (time()+60*60*24*30));
                    setcookie('password', $_POST['password'], (time()+60*60*24*30));
                }else{
                    /**
                    * Delete Login Credential
                    */
                    setcookie('username', $_POST['username'], (time()-3600));
                    setcookie('password', $_POST['password'], (time()-3600));
                }
            //END REMEBER

            if($row['usertype']=="ADMIN"){
                header("Location: main.php");
            }elseif($row['usertype']=="CASHIER"){
                header("Location: POS/index.php");
            }


        }else{

            $_SESSION['xmessage'] = $xmessage;
            header("Location: index.php");

        }

    }else{
        header("Location: index.php");
    }


    function getDefaultCompany($userid){
        global $con;

        $stmt = $con->prepare("SELECT * FROM users_company WHERE UserID = BINARY ? ORDER BY compcode LIMIT 1");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['compcode'];
    }
?>