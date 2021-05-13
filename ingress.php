<?php
    include 'auth.php';
    include 'tools.php';
    if (isset($_GET["hash"])){
        $_GET["url"] = hash_to_url($_GET["hash"]);
    }
    if (isset($_GET["live"])){
        $p = pass_proxy($_GET["url"], $_GET["referer"], $_GET["user"], $_GET["pass"], true);
        echo $p["content"];
        exit();
    }
    if (cached($_GET["url"])) { rem_cache($_GET["url"]); }
    header("Content-Type: application/json");
    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
    echo cache($_GET["url"], $_GET["referer"], $_GET["user"], $_GET["pass"]);
