<?php

require_once "conn.php";
require_once "functions.php";

# Empty username and/or password
if (!exist($_POST["username"]) || !exist($_POST["password"]) || !exist($_POST["re-password"])) {
    header("Location: register.php?res=error");
    exit;
}

$username = check($_POST["username"]);
$password = check($_POST["password"]);
$repassword = check($_POST["re-password"]);

$is_username_taken = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM `radcheck` WHERE `username` = '$username' AND `attribute` = 'Cleartext-Password'"))["count"] >= 1;

if ($is_username_taken || $password !== $repassword) {
    header("Location: register.php?res=error");
    exit;
}

$sqls = [
    "INSERT INTO `radcheck` (`username`, `attribute`, `op`, `value`) VALUES ('$username', 'Cleartext-Password', ':=', '$password');",
    "INSERT INTO `radusergroup` (`username`, `groupname`) VALUES ('$username', 'user');",
    "INSERT INTO `radreply` (`username`, `attribute`, `op`, `value`) VALUES ('$username', 'ChilliSpot-Max-Total-Octets', ':=', '50000000')",
    "INSERT INTO `radreply` (`username`, `attribute`, `op`, `value`) VALUES ('$username', 'Session-Timeout', ':=', '1800')"
];

foreach ($sqls as $sql)
    mysqli_query($conn, $sql);

header("Location: register.php?res=success");
exit;
