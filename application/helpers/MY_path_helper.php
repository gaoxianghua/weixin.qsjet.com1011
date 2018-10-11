<?php
if (! function_exists('upload_base')) {

    function upload_base($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->upload_base($uri);
    }
}

if (! function_exists('getUploadPath')) {

    function getUploadPath($path)
    {
        $relative_path = $path . '/' . date('Y') . '/' . date('m') . '/';
        createPaths(upload_base() . $relative_path);
        return $relative_path;
    }
}
?>