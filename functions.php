<?php
function save_cookie()
{
    $filename = 'cookie.txt';
    if (file_exists($filename) && filemtime($filename) > time() - 12 * 60 * 60)
        return true;

    $ch = curl_init('http://www.nic.ir/Just_Released');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    if (isset($matches[1][0])) {
        parse_str($matches[1][0], $cookie);
        if (!isset($cookie['IRNIC']))
            return false;
        $file = fopen($filename, "w") or die("Unable to open file!");
        fwrite($file, $cookie['IRNIC']);
        fclose($file);
        return true;
    }
    return false;
}

function get_cookie()
{
    $file = fopen("cookie.txt", "r") or die("Unable to open file!");
    $cookie = fread($file, filesize("cookie.txt"));
    fclose($file);
    return !empty($cookie) ? $cookie : false;
}