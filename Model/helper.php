<?php 

    /**
     * @param Array $file name of the File input type {$_FILES['upload']}
     * @param string $dir store directory 
     */

    function upload_image($file, $dir) {
        
         /**
         * @var string $files (return file name)
         * @var int $total_count (return count of the files)
         * @var string $tmpFilePath (return the file temporary Path)
         * @var string $newFilePath (new File Path)
         * @return move_uploaded_file (transfer a file to a new file path)
         */

        $files = array_filter($file);
        $total_count = count($file);

        if($total_count >= 1){
            /**
             * @param {0777} for read, write, & execute for owner, group and others
             * @return is_dir return if directory path location was already existed
             */
            if(!is_dir($dir)){
                mkdir($dir, 0777);
            }
        }

        for($i = 0; $i < $total_count; $i++){
            $tmpFilePath = $file['file-'.$i]['tmp_name'];

            /**
             * See if temp file path is existed
             */

            if($tmpFilePath != ""){
                $newFilePath = $dir.$file['file-'.$i]['name'];
                move_uploaded_file($tmpFilePath, $newFilePath);
            }
        }
    }

    function NA_file_upload($file, $dir){
        $files = array_filter($file);
        $total_count = count($file);

        if($total_count >= 1){
            if(!is_dir($dir)){
                mkdir($dir, 0777);
            }
        }
        for($i = 0; $i <$total_count; $i++){
            $tmpFilePath = $file['upload']['tmp_name'][$i];
            if($tmpFilePath != ""){
                $newFilePath = $dir.$file['upload']['name'][$i];
                move_uploaded_file($tmpFilePath, $newFilePath);
            }
        }
    }

    function file_checker($dir){
        
        $all_files = scandir($dir);
        $files = array_diff($all_files, array('.','..'));
        foreach($files as $file){
            $fileNameParts = explode('.', $file);
            $ext = end($fileNameParts);
    
            $file_array[] = array("name" => $file, "ext" => $ext);
        }
        return $file_array;
    }


    // function delete_file($code, $path){
    //     if( unlink($path) ){
    //         return "Successfully Delete!: " . $code;
    //     } else {
    //         return "Error! File has not been deleted!: " . $code;
    //     }
    // }

    function better_crypt($input, $rounds = 10) { 

        $crypt_options = array( 'cost' => $rounds ); 
        return password_hash($input, PASSWORD_BCRYPT, $crypt_options); 
    
    }

    function password_restriction($password){
        return  strlen($password) >= 8 && preg_match("/^(?=.*[a-zA-Z])(?=.*[0-9])/", $password);
    }

    function match_password($new, $confirm){
        return $new == $confirm;
    }

    function validStatus($status){
        return match($status){
            'Offline' => true,
            'Online' => false,
            default => true
        };
    }
    
    function statusAccount($status){
        return match($status){
            'Active' => true,
            'Deactivate' => false,
            default => false
        };
    
    }
    
    function failedAttempt($attempt){
        return $attempt == 5;
    }
    
    function validIP($IP){
        return $IP == gethostbyaddr($_SERVER['REMOTE_ADDR']) || $IP == null;
    }
    
    function valid30Days($date, $user){
        $dateNow = date('Y-m-d');
        if($dateNow > date('Y-m-d', strtotime($date.'+30days' ) )){
            return [
                'valid' => true,
                'msg' => 'Need To Change Password',
                'proceed' => false,
                'userType' => $user
            ];
        } else {
            return [
                'valid' => true,
                'msg' => 'Login Successful',
                'proceed' => true,
                'userType' => $user
            ];
        }
    }
     

    function CustomerNames($module, $ctranno, $company){
        return match($module){

            'DR' => "select b.cname from dr_t a
                    left join
                    (
                            select a.*,b.cname, b.cpricever,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nlimit)AS char)))) as nlimit, c.cname as cdelname, d.cname as csalesmaname 
                            from dr a 
                            left join customers b on a.compcode=b.compcode and a.ccode=b.cempid 
                            left join customers c on a.compcode=c.compcode and a.cdelcode=c.cempid 
                            left join salesman d on a.compcode=d.compcode and a.csalesman=d.ccode 
                            where a.ctranno = '$ctranno' and a.compcode='$company'
                    ) b on a.ctranno = b.ctranno and a.compcode = b.compcode
    
                    left join items c on a.compcode = c.compcode and a.citemno = c.cpartno
                    where a.ctranno = '$ctranno' and a.compcode='$company'",
    
            'SI' => "select c.cname from sales_t a
                    left join sales b on a.compcode = b.compcode and a.ctranno = b.ctranno
                    left join customers c on a.compcode = c.compcode and b.ccode = c.cempid
                    left join items d on a.compcode = d.compcode and a.citemno = d.cpartno
                    where a.compcode = '$company' and a.ctranno='$ctranno'",
    
            'IN' => "select C.cname
                    from ntsales_t X
                    left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
                    left join ntsales B on X.compcode = B.compcode and X.ctranno = B.ctranno
                    left join customers C on B.compcode = C.compcode and B.ccode = C.cempid 
                    where X.compcode='$company' and X.ctranno = '$ctranno' Order By X.nident",
    
            'JE' => "select a.*, b.* from journal_t a
                    left join journal b on a.compcode = b.compcode and a.ctranno = b.ctranno
                    where a.compcode='$company' and a.ctranno = '$ctranno' order by a.nident",
    
            'ARADJ' => "select c.cname From aradjustment_t a 
                    left join aradjustment b on a.compcode = b.compcode and a.ctranno = b.ctranno
                    left join customers c on b.compcode = c.compcode and b.ccode = c.cempid
                    left join groupings d on c.compcode = d.compcode and a.nident = d.nidentity
                    where a.compcode='$company' and a.ctranno = '$ctranno'",
    
            'OR' => "select e.cname from receipt_sales_t a 
                    left join sales b on a.csalesno=b.ctranno and a.compcode=b.compcode 
                    left join accounts c on a.cacctno=c.cacctid and a.compcode=c.compcode 
                    left join groupings d on a.compcode = d.compcode and a.nidentity = d.nidentity
                    left join (
                            SELECT a.compcode, a.ctranno, a.ddate, b.cname
                            from receipt a
                            left join customers b on a.compcode = b.compcode and a.ccode = b.cempid
                            where a.compcode ='$company' and a.ctranno = '$ctranno'
                    ) e on a.compcode = e.compcode and a.ctranno = e.ctranno
                    where a.compcode='$company' and a.ctranno = '$ctranno' order by a.nidentity",
    
            'BD' => "select a.*, b.cornumber, b.dcutdate, b.cremarks as remarks_t, b.cpaymethod, b.namount, c.cacctdesc, c.ddate, c.namount
            from deposit_t a 
            left join receipt b on a.compcode=b.compcode and a.corno=b.ctranno and a.compcode=b.compcode 
            left join (
                    SELECT a.compcode, a.ctranno, b.cacctdesc, a.ddate, a.namount
                    from deposit a
                    left join accounts b on a.compcode = b.compcode and a.cacctcode = b.cacctid
                    where a.compcode = '$company' and a.ctranno='$ctranno'
            ) c on a.compcode = c.compcode and a.ctranno = c.ctranno
            where a.compcode='$company' and a.ctranno = '$ctranno' ",
    
            'PV' => "Select A.cacctno, b.ctranno, b.bankname, b.cpayrefno, b.ddate, A.crefrr, a.capvno, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.namount, a.nowed, a.napplied, IFNULL(b.npayed,0) as npayed, c.cacctdesc, a.newtamt, d.cname
            From paybill_t a
            left join
                (
                    select x.capvno, y.ccode, y.ctranno, y.cpayrefno, y.ddate, z.cname as bankname, sum(x.napplied) as npayed
                    from paybill_t x 
                                    left join paybill y on x.compcode=y.compcode and x.ctranno=y.ctranno
                                    left join bank z on x.compcode=z.compcode and y.cbankcode=z.ccode
                    where x.compcode = '$company' and x.ctranno = '$ctranno'
                    group by x.capvno
                ) b on a.capvno=b.capvno
            left join accounts c on a.compcode=c.compcode and a.cacctno=c.cacctid 
                    left join suppliers d on a.compcode = d.compcode and b.ccode = d.ccode
            where a.compcode='$company' and a.ctranno='$ctranno' order by a.nident",        
        
            'APV' => "select a.*, b.*, c.cname from apv_d a
                    left join apv b on a.compcode = b.compcode and a.ctranno = b.ctranno
                    left join suppliers c on a.compcode = c.compcode and b.ccode = c.ccode
                    where a.compcode = '$company' and a.ctranno = '$ctranno'",
            
            'APADJ' => "select a.*, b.cacctno, b.ctitle, b.ndebit, b.ncredit, b.cremarks as remark_t, c.* from apadjustment a
                    left join apadjustment_t b on a.compcode=b.compcode and a.ctranno=b.ctranno
                    left join suppliers c on a.compcode=c.compcode and a.ccode=c.ccode
                    where a.compcode='$company' and a.ctranno='$ctranno'",
            default => [
                'errCode' => 'ERR_DATA',
                'errMsg' => 'Data no referrence'
            ]
        };
    }