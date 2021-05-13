<?php
    include 'auth.php';
    include 'tools.php';
    if (isset($_GET["hash"])){
        $_GET["url"] = hash_to_url($_GET["hash"]);
    }
    if (isset($_GET["redirect"])){
        header('Location: ' . get_url($_GET["url"]));
        exit();
    }
    header('Content-Type: ' . get_mime(get_loc($_GET["url"])));
    header('Content-Length: ' . filesize(get_loc($_GET["url"])));
    header('Accept-Range: bytes');
    echo load_cache($_GET["url"]);