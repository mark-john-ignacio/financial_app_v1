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
        return $IP == getHostByName(getHostName()) || $IP == null;
    }
    
    function valid30Days($date){
        $dateNow = date('Y-m-d');
        if($dateNow > date('Y-m-d', strtotime($date.'+30days' ) )){
            return [
                'valid' => true,
                'msg' => 'Need To Change Password',
                'proceed' => false
            ];
        } else {
            return [
                'valid' => true,
                'msg' => 'Login Successful',
                'proceed' => true
            ];
        }
        return $date > date('Y-m-d', strtotime($date.'+30days'));
    }
    