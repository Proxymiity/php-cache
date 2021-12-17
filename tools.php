<?php
    function get_base_folder() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$p = 'https';} else {$p = 'http';}
        return $p . '://' . $_SERVER['SERVER_NAME'] . substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
    }

    // Sourced from: https://stackoverflow.com/questions/35299457/getting-mime-type-from-file-name-in-php
    function get_mime_type($filename) {
        $idx = explode( '.', $filename );
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode-1]);
        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        if (isset( $mimet[$idx] )) {
            return $mimet[$idx];
        } else {
            return 'application/octet-stream';
        }
    }

    function get_file_str($file) {
        return explode('?', basename($file), 2)[0];
    }

    function get_file_json($url) {
        $dt = array(
            "cached" => cached($url),
            "url" => get_url($url),
            "original_url" => $url,
            "url_hash" => md5($url),
            "file" => get_file_str($url),
            "type" => get_mime(get_url($url))
        );
        return json_encode($dt);
    }

    function get_mime($p) {
        $m = mime_content_type($p);
        if ($m==""){
            $m = get_mime_type(get_file_str($p));
        }
        return $m;
    }

    function pass_proxy($url, $referer, $user, $pass, $set_headers=false) {
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36';
        $proxy = curl_init($url);
        curl_setopt($proxy, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($proxy, CURLOPT_HEADER, 0);
        curl_setopt($proxy, CURLOPT_USERAGENT, $useragent);
        curl_setopt($proxy, CURLOPT_REFERER, $referer);
        curl_setopt($proxy, CURLOPT_USERPWD, $user . ":" . $pass);
        curl_setopt($proxy, CURLOPT_TIMEOUT, 30);
        curl_setopt($proxy, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($proxy, CURLOPT_FOLLOWLOCATION, 1);
        $return = curl_exec($proxy);
        if ($set_headers){
            $contentType = curl_getinfo($proxy, CURLINFO_CONTENT_TYPE);
            header('Content-Type: ' . $contentType);
        }
        curl_close($proxy);
        return $return;
    }

    function cache($url, $referer, $user, $pass) {
        $fn = get_file_str($url);
        $pp = md5($url);
        mkdir("./data/" . $pp);
        $ph = "./data/" . $pp . "/" . $fn;
        $fp = fopen($ph, 'wb');
        $content = pass_proxy($url, $referer, $user, $pass);
        fwrite($fp, $content);
        fclose($fp);
        $f = fopen("./data/" . $pp . "/url", 'wb'); fwrite($f, $url); fclose($f);
    }

    function load_cache($url) {
        $fn = get_file_str($url);
        $pp = md5($url);
        $ph = "./data/" . $pp . "/" . $fn;
        $fp = fopen($ph, 'rb');
        return fread($fp, filesize($ph));
    }

    function get_url($url) {
        return get_base_folder() . "/data/" . md5($url) . "/" . get_file_str($url);
    }

    function cached($url) {
        $fn = get_file_str($url);
        $pp = md5($url);
        return file_exists("./data/" . $pp . "/" .$fn);
    }

    function rem_cache($url) {
        $fn = get_file_str($url);
        $pp = md5($url);
        unlink("./data/" . $pp . "/" . $fn);
        unlink("./data/" . $pp . "/url");
        rmdir("./data/" . $pp);
    }

    function hash_to_url($hash) {
        $ph = "./data/" . $hash . "/url";
        if (is_file($ph)) {
            $f = fopen($ph, 'rb');
            return fread($f, filesize($ph));
        } else {return "";}
    }