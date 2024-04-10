<?php

require_once "conn.php";

if (!isset($_SESSION))
    session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
    <div id="app" class="container p-4">
        <? if (!isset($_SESSION["admin"])) : ?>
            <header class="pb-3">
                <h1>Sign in to Admin Panel</h1>
            </header>
            <? if (isset($_GET) && $_GET["res"] === "login-error") : ?>
                <div class="alert alert-danger" role="alert">
                    <h6>Failed to Authenticate.</h6>
                    <p class="m-0">Possible causes:</p>
                    <ul class="m-0">
                        <li>Username or password was mistyped.</li>
                        <li>User did not exist or was not an admin.</li>
                    </ul>
                </div>
            <? endif; ?>
            <form method="post" action="admin-login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" autocorrect="off" autocapitalize="none">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocorrect="off" autocapitalize="none">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        <? else : ?>
            <header class="pb-3 d-flex justify-content-between align-items-center">
                <h1>Admin Panel</h1>
                <a class="btn btn-danger mb-2" href="admin-logout.php" role="button">Logout</a>
            </header>
            <div class="alert alert-primary mb-4" role="alert">
                <h6>NOTICE</h6>
                <ul class="m-0">
                    <li>Users will be forcibly logged out when they exceed their usage limit.</li>
                    <li>Usage is reset each time upon login.</li>
                    <li>Usage information updates once per minute.</li>
                </ul>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Last Session</th>
                        <th scope="col">Bandwidth (bytes)</th>
                        <th scope="col">Time Limit (sec)</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <?php
                $users = array();

                // Get users
                $sql = "SELECT * FROM `radcheck` WHERE `attribute` = 'Cleartext-Password'";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    $users[$row["username"]] = array(
                        "last_session" => null,
                        "bandwidth_quota" => null,
                        "bandwidth_used" => null,
                        "time_quota" => null,
                        "time_used" => null
                    );
                }

                // Get quotas
                $sql = "SELECT * FROM `radreply`";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!isset($users[$row["username"]])) continue;
                    switch ($row["attribute"]) {
                        case "ChilliSpot-Max-Total-Octets":
                            $users[$row["username"]]["bandwidth_quota"] = $row["value"];
                            break;
                        case "Session-Timeout":
                            $users[$row["username"]]["time_quota"] = $row["value"];
                            break;
                        default:
                    }
                }

                // Get usage
                $sql = "SELECT r.* FROM `radacct` r JOIN (SELECT username, MAX(radacctid) AS id FROM `radacct` GROUP BY username) s ON r.username = s.username AND r.radacctid = s.id;";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!isset($users[$row["username"]])) continue;
                    $users[$row["username"]]["last_session"] = array($row["acctstarttime"], $row["acctstoptime"]);
                    $users[$row["username"]]["bandwidth_used"] = $row["acctinputoctets"] + $row["acctoutputoctets"];
                    $users[$row["username"]]["time_used"] = $row["acctsessiontime"];
                }
                ?>
                <tbody class="table-group-divider align-middle">
                    <? foreach ($users as $username => $userdata) : ?>
                        <tr>
                            <td><?= $username ?></td>
                            <td>
                                <? if (is_null($userdata["last_session"])) : ?>
                                    <i>never used</i>
                                <? else : ?>
                                    <span>from <?= $userdata["last_session"][0] ?></span><br>
                                    <span>to <?= $userdata["last_session"][1] ?? "<i>current</i>" ?></span>
                                <? endif; ?>
                            </td>
                            <td>
                                <span class="d-block"><?= $userdata["bandwidth_used"] ?? 0 ?>/<?= $userdata["bandwidth_quota"] ?? "∞" ?></span>
                                <? if ($userdata["bandwidth_used"] && $userdata["bandwidth_quota"]) : ?>
                                    <progress value="<?= $userdata["bandwidth_used"] ?>" max="<?= $userdata["bandwidth_quota"] ?>"></progress>
                                <? else : ?>
                                    <progress value="0" max="100"></progress>
                                <? endif; ?>
                            </td>
                            <td>
                                <span class="d-block"><?= $userdata["time_used"] ?? 0 ?>/<?= $userdata["time_quota"] ?? "∞" ?></span>
                                <? if ($userdata["time_used"] && $userdata["time_quota"]) : ?>
                                    <progress value="<?= $userdata["time_used"] ?>" max="<?= $userdata["time_quota"] ?>"></progress>
                                <? else : ?>
                                    <progress value="0" max="100"></progress>
                                <? endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modal" onclick="handleEditClick('<?= $username ?>', <?= $userdata["bandwidth_quota"] ?? -1 ?>, <?= $userdata["time_quota"] ?? -1 ?>)">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
                <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" action="admin-edit.php">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit User</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bandwidth" class="form-label">Bandwidth (bytes)</label>
                                        <input type="number" class="form-control" id="bandwidth" name="bandwidth">
                                    </div>
                                    <div class="mb-3">
                                        <label for="time" class="form-label">Time Limit (sec)</label>
                                        <input type="number" class="form-control" id="time" name="time">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </table>
            <script>
                function handleEditClick(username = "", bandwidth = -1, time = -1) {
                    document.getElementById("username").value = username;
                    document.getElementById("bandwidth").value = bandwidth;
                    document.getElementById("time").value = time;
                }
            </script>
        <? endif; ?>
    </div>
    <script src="bootstrap.bundle.min.js"></script>
</body>

</html>