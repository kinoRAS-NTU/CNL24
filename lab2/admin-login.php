<?php

require_once "conn.php";
require_once "functions.php";

if (!isset($_SESSION))
    session_start();

if (isset($_SESSION["admin"])) {
    header("Location: admin.php?res=error");
    exit;
}

# Empty username and/or password
if (!exist($_POST["username"]) || !exist($_POST["password"])) {
    header("Location: admin.php?res=error");
    exit;
}

$username = check($_POST["username"]);
$password = check($_POST["password"]);

$sql = "SELECT * FROM `radusergroup` WHERE `username` = '$username' AND `groupname` = 'admin'";
$result = mysqli_query($conn, $sql);

# No such admin
if (mysqli_num_rows($result) !== 1) {
    header("Location: admin.php?res=error");
    exit;
}

$sql = "SELECT * FROM `radcheck` WHERE `username` = '$username' AND `attribute` = 'Cleartext-Password'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

# Incorrect password
if ($row["value"] !== $password) {
    header("Location: admin.php?res=error");
    exit;
}

$_SESSION["admin"] = $username;
header("Location: admin.php");
exit;
