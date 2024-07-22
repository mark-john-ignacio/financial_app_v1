<?php
function getBaseURL() {
    $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
    $isLocalhost = isset($_SERVER['HTTP_HOST']) && preg_match('/^localhost(:\d+)?$/', $_SERVER['HTTP_HOST']);
    // Check if 'REQUEST_URI' is set
    $folder_path = isset($_SERVER['REQUEST_URI']) ? explode('/', $_SERVER['REQUEST_URI']) : [];
    $first_part_of_path = $folder_path[1] ?? '';
    // Ensure 'HTTP_HOST' is checked before use
    $baseURL = isset($_SERVER['HTTP_HOST']) ? $protocol . "://" . $_SERVER['HTTP_HOST'] : $protocol . "://localhost";
    $baseURL .= "/" . $first_part_of_path;

    if (getenv('CI_ENVIRONMENT') === 'development' || $isLocalhost) { 
        $second_part_of_path = $folder_path[2] ?? '';
        $baseURL .= "/" . $second_part_of_path;

        if (!preg_match("/public\/?$/", $baseURL)) {
            $baseURL .= '/public';
        }
    }

    $baseURL = rtrim($baseURL, '/') . '/';

    // Check if baseURL ends with 'system_management' and not followed by 'public'
    if (preg_match("/system_management\/?$/i", $baseURL) && !preg_match("/system_management\/public\/?$/i", $baseURL)) {
        $baseURL .= 'public/';
    } else if (!preg_match("/system_management\/public\/?$/i", $baseURL)) {
        // If 'system_management/public' is not already part of the URL, adjust accordingly
        $baseURL = preg_replace("/(public\/?)$/i", "system_management/$1", $baseURL);
    }

    return $baseURL;
}