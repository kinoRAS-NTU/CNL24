<?php

$uamsecret = "tauam";
$baseurl = "http://" . $_GET["uamip"] . ":" . $_GET["uamport"];
$loginpath = "hotspotlogin.php";

$debug = true;
$form = false;

$result = -1;
$title = "";
$meta = array();
$content = array();

function check($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function exist($data)
{
    if (!isset($data) || empty($data))
        return false;
    if (check($data) == "")
        return false;
    return true;
}
