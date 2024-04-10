<?php

require_once "functions.php";

if ($_GET['login'] == "login") {
    // Hashing
    $hexchal = pack("H32", $_GET['chal']);
    $newchal = pack("H*", md5($hexchal . $uamsecret));
    $response = md5("\0" . $_GET['password'] . $newchal);
    $newpwd = pack("a32", $_GET['password']);
    $pappassword = implode("", unpack("H32", ($newpwd ^ $newchal)));
    // UI
    $title = 'Logging into HotSpot';
    $meta = array('<meta http-equiv="refresh" content="0;url=' . $baseurl . '/logon?username=' . $_GET['username'] . '&password=' . $pappassword . '">');
} else {
    switch ($_GET['res']) {
        case "success":
            $result = 1;
            $title = 'Welcome to HotSpot';
            $content["popup"] = ["type" => "success", "message" => "Logged in successfully."];
            $content["actions"] = array(["type" => "danger", "text" => "Logout", "link" => $baseurl . "/logoff"]);
            break;

        case "failed":
            $result = 2;
            $reply = $_GET["reply"];
            $title = 'Log in to HotSpot';
            $content["popup"] = ["type" => "danger", "message" => $reply ? $reply : "Failed to authenticate."];
            $form = true;
            break;

        case "logoff":
            $result = 3;
            $title = "Login to HotSpot";
            $content["popup"] = ["type" => "success", "message" => "Logged out successfully."];
            $form = true;
            break;

        case "already":
            $result = 4;
            $title = "Already logged in.";
            $content["actions"] = array(["type" => "danger", "text" => "Logout", "link" => $baseurl . "/logoff"]);
            break;

        case "notyet":
            $result = 5;
            $title = "Log in to HotSpot";
            $form = true;
            break;

        case "popup1":
            $result = 11;
            $title = 'Logging into HotSpot';
            break;

        case "popup2":
            $result = 12;
            $title = 'Welcome to HotSpot';
            $content["popup"] = ["type" => "success", "message" => "Logged in successfully."];
            $content["actions"] = array(["type" => "danger", "text" => "Logout", "link" => $baseurl . "/logoff"]);
            break;

        case "popup3":
            $result = 13;
            $title = "Login to HotSpot";
            $content["popup"] = ["type" => "success", "message" => "Logged out successfully."];
            $form = true;
            break;

        default:
            $result = 0;
            $title = 'HotSpot';
    }
}

require_once "page.php";

exit(0);
