<?php

require_once "conn.php";
require_once "functions.php";

if (!isset($_SESSION))
    session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
    exit;
}

# Empty field
if (!exist($_POST["username"]) || !exist($_POST["bandwidth"]) || !exist($_POST["time"])) {
    header("Location: admin.php?res=edit-error");
    exit;
}

$username = check($_POST["username"]);
$bandwidth = intval(check($_POST["bandwidth"]));
$time = intval(check($_POST["time"]));

$has_bandwidth_limit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM `radreply` WHERE `username` = '$username' AND `attribute` = 'ChilliSpot-Max-Total-Octets'"))["count"] >= 1;
$has_time_limit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM `radreply` WHERE `username` = '$username' AND `attribute` = 'Session-Timeout'"))["count"] >= 1;

# Update Bandwidth
$sql = ($bandwidth <= 0)
    ? "DELETE FROM `radreply` WHERE `username` = '$username' AND `attribute` = 'ChilliSpot-Max-Total-Octets'"
    : (($has_bandwidth_limit)
        ? "UPDATE `radreply` SET `value` = '$bandwidth' WHERE `username` = '$username' AND `attribute` = 'ChilliSpot-Max-Total-Octets'"
        : "INSERT INTO `radreply` (`username`, `attribute`, `op`, `value`) VALUES ('$username', 'ChilliSpot-Max-Total-Octets', ':=', '$bandwidth')");
mysqli_query($conn, $sql);

# Update Time
$sql = ($time <= 0)
    ? "DELETE FROM `radreply` WHERE `username` = '$username' AND `attribute` = 'Session-Timeout'"
    : (($has_time_limit)
        ? "UPDATE `radreply` SET `value` = '$time' WHERE `username` = '$username' AND `attribute` = 'Session-Timeout'"
        : "INSERT INTO `radreply` (`username`, `attribute`, `op`, `value`) VALUES ('$username', 'Session-Timeout', ':=', '$time')");
mysqli_query($conn, $sql);

header("Location: admin.php");
