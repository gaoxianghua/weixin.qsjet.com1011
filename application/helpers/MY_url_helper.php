<?php
if (! function_exists('root_path')) {

    function root_path($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->root_path($uri);
    }
}
if (! function_exists('cas_url')) {

    function cas_url($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->cas_url($uri);
    }
}
if (! function_exists('kkb_api')) {

    function kkb_api($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->kkb_api($uri);
    }
}

if (! function_exists('cas_domain')) {

    function cas_domain($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->cas_domain($uri);
    }
}

if (! function_exists('completeImgUrl')) {

    function completeUrl(&$result, $field_names, $thumb = 'thumb')
    {
        if (! is_array($field_names)) {
            $field_names = array(
                $field_names
            );
        }
        if ($result) {
            if (isset($result[0]) && is_array($result[0])) {
                for ($i = 0; $i < sizeof($result); $i ++) {
                    foreach ($field_names as $field_name) {
                        if (isset($result[$i][$field_name])) {
                            $img_url = $result[$i][$field_name];
                            $result[$i][$field_name] = download_url($img_url);
                            if ($thumb) {
                                $result[$i][$thumb . '_' . $field_name] = download_url($thumb . '/' . $img_url);
                            }
                        }
                    }
                }
            } else {
                foreach ($field_names as $field_name) {
                    $img_url = $result[$field_name];
                    $result[$field_name] = download_url($img_url);
                    if ($thumb) {
                        $result[$thumb . '_' . $field_name] = download_url($thumb . '/' . $img_url);
                    }
                }
            }
        }
        return $result;
    }
}
?>