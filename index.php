<?php
    #include 'auth.php';
    include 'tools.php';
    if (isset($_GET["hash"])){
        $_GET["url"] = hash_to_url($_GET["hash"]);
    }
    if (cached($_GET["url"])) {
	    if (isset($_GET["redirect"])){
	        header('Location: ' . get_url($_GET["url"]));
	        exit();
	    }
        header('Content-Type: ' . get_mime("./data/" . md5($_GET["url"]) . "/" . basename($_GET["url"])));
        header('Content-Length: ' . filesize("./data/" . md5($_GET["url"]) . "/" . basename($_GET["url"])));
        header('Accept-Range: bytes');
        echo load_cache($_GET["url"]);
    } else {
    	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        echo "app ready";
    	die();
    }
