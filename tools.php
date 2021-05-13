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

    function get_file_json($url) {
        $dt = array(
            "cached" => cached($url),
            "url" => get_url($url),
            "original_url" => $url,
            "url_hash" => md5(md_get_persistent_string($url)),
            "file" => basename(md_get_persistent_string($url)),
            "type" => get_mime(get_loc(md_get_persistent_string($url)))
        );
        return json_encode($dt);
    }

    function get_mime($p) {
        $m = mime_content_type($p);
        if ($m==""){
            $m = get_mime_type(basename($p));
        }
        return $m;
    }

    function pass_proxy($url, $referer, $user, $pass, $set_headers=false) {
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36';
        $proxy = curl_init($url);
        $rt_headers = [];
        curl_setopt($proxy, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($proxy, CURLOPT_HEADER, 0);
        curl_setopt($proxy, CURLOPT_USERAGENT, $useragent);
        curl_setopt($proxy, CURLOPT_REFERER, $referer);
        curl_setopt($proxy, CURLOPT_USERPWD, $user . ":" . $pass);
        curl_setopt($proxy, CURLOPT_TIMEOUT, 30);
        curl_setopt($proxy, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($proxy, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($proxy, CURLOPT_URL, $url);
        curl_setopt($proxy, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$rt_headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2)
                    return $len;
                $rt_headers[strtolower(trim($header[0]))][] = trim($header[1]);
                return $len;
            }
        );
        $rt_content = curl_exec($proxy);
        if ($set_headers){
            $contentType = curl_getinfo($proxy, CURLINFO_CONTENT_TYPE);
            header('Content-Type: ' . $contentType);
        }
        $time = curl_getinfo($proxy, CURLINFO_TOTAL_TIME_T);
        $status = curl_getinfo($proxy, CURLINFO_HTTP_CODE);
        curl_close($proxy);
        return Array(
            "content" => $rt_content,
            "headers" => $rt_headers,
            "time" => $time,
            "status" => $status
        );
    }

    function cache($url, $referer, $user, $pass) {
        $md_url = md_get_persistent_string($url);
        $pp = md5($md_url);
        mkdir("./data/" . $pp);
        $ph = get_loc($md_url);
        $fp = fopen($ph, 'wb');
        $content = pass_proxy($url, $referer, $user, $pass);
        fwrite($fp, $content["content"]);
        fclose($fp);
        $f = fopen("./data/" . $pp . "/url", 'wb'); fwrite($f, $url); fclose($f);
        return md_get_response_json($url, $content["headers"], $content["time"], $content["status"], $ph);
    }

    function load_cache($url) {
        $ph = get_loc(md_get_persistent_string($url));
        $fp = fopen($ph, 'rb');
        return fread($fp, filesize($ph));
    }

    function get_url($url) {
        $url = md_get_persistent_string($url);
        return get_base_folder() . "/data/" . md5($url) . "/" . basename($url);
    }

    function get_loc($url) {
        return "./data/" . md5($url) . "/" . basename($url);
    }

    function cached($url) {
        return file_exists(get_loc(md_get_persistent_string($url)));
    }

    function rem_cache($url) {
        $url = md_get_persistent_string($url);
        $pp = md5($url);
        unlink(get_loc($url));
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

    function md_get_persistent_string($url) {
        $a = explode('/', $url);
        $a = array_slice($a, -2);
        return($a[0] . "/" . $a[1]);
    }

    function md_get_response_json($url, $headers, $time, $status, $path) {
        if(strval($status) == "200") {$s = true;} else {$s = false;}
        if($headers["x-cache"][0] == "HIT") {$c = true;} else {$c = false;}
        $json =  Array(
            "url" => $url,
            "success" => $s,
            "cached" => $c,
            "time" => intval($time/1000),
            "bytes" => filesize($path),
            "api_response" => Array(
                "cached" => cached($url),
                "url" => get_url($url),
                "original_url" => $url,
                "url_hash" => md5(md_get_persistent_string($url)),
                "file" => basename(md_get_persistent_string($url)),
                "type" => get_mime(get_loc(md_get_persistent_string($url)))
            )
        );
        return json_encode($json);
    }