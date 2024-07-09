<?php

function getBaseURL() {
    $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
    $isLocalhost = preg_match('/^localhost(:\d+)?$/', $_SERVER['HTTP_HOST']);
    $folder_path = explode('/', $_SERVER['REQUEST_URI']);
    $first_part_of_path = $folder_path[1] ?? '';
    $baseURL = $protocol . "://" . $_SERVER['HTTP_HOST'];
    $baseURL .= "/" . $first_part_of_path;
    $second_part_of_path = $folder_path[2] ?? '';
    $baseURL .= "/" . $second_part_of_path;

    if (getenv('CI_ENVIRONMENT') === 'development' || $isLocalhost) { 


        if (!preg_match("/public\/?$/", $baseURL)) {
            $baseURL .= '/public';
        }
    }

    $baseURL = rtrim($baseURL, '/') . '/';

    if (!preg_match("/system_management\/public\/?$/i", $baseURL)) {
        $baseURL = preg_replace("/(public\/?)$/i", "system_management/$1", $baseURL);
    }

    return $baseURL;
}