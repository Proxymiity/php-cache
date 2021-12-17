<?php
    include 'auth.php';
    include 'tools.php';
    if (isset($_GET["hash"])){
        $_GET["url"] = hash_to_url($_GET["hash"]);
    }
    if (isset($_GET["live"])){
        echo pass_proxy($_GET["url"], $_GET["referer"], $_GET["user"], $_GET["pass"], $_GET["bearer"], true);
        exit();
    }
    if (isset($_GET["refresh"])){
        rem_cache($_GET["url"]);
    }
    if (!cached($_GET["url"])) {
        cache($_GET["url"], $_GET["referer"], $_GET["user"], $_GET["pass"], $_GET["bearer"]);
    }
    if (isset($_GET["redirect"])){
        header('Location: ' . get_url($_GET["url"]));
        exit();
    }
    header('Content-Type: ' . get_mime(get_url($_GET["url"])));
    header('Content-Length: ' . filesize("./data/" . md5($_GET["url"]) . "/" . get_file_str($_GET["url"])));
    header('Accept-Range: bytes');
    echo load_cache($_GET["url"]);