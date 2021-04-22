<?php
    include 'tools.php';

    if (isset($_GET["delete"])){
        rem_cache($_GET["url"]);
    }
    if (isset($_GET["create"])){
        cache($_GET["url"], $_GET["referer"], $_GET["user"], $_GET["pass"]);
    }
    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
    echo get_file_json($_GET["url"]);