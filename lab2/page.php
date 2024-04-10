<?php

global $meta, $title, $loginpath, $debug, $form;

$uamip = $_GET["uamip"];
$uamport = $_GET["uamport"];
$challenge = $_GET['challenge'];
$userurl = $_GET['userurl'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $title ?></title>
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Pragma" content="no-cache">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= implode("", $meta) ?>
    <link rel="stylesheet" href="bootstrap.min.css">
    <script>
        var blur = 0;
        var starttime = new Date();
        var startclock = starttime.getTime();
        var mytimeleft = 0;

        function doTime() {
            window.setTimeout("doTime()", 1000);
            t = new Date();
            time = Math.round((t.getTime() - starttime.getTime()) / 1000);
            if (mytimeleft) {
                time = mytimeleft - time;
                if (time <= 0) {
                    window.location = "<?= $loginpath ?>?res=popup3&uamip=<?= $uamip ?>&uamport=<?= $uamport ?>";
                }
            }
            if (time < 0) time = 0;
            hours = (time - (time % 3600)) / 3600;
            time = time - (hours * 3600);
            mins = (time - (time % 60)) / 60;
            secs = time - (mins * 60);
            if (hours < 10) hours = "0" + hours;
            if (mins < 10) mins = "0" + mins;
            if (secs < 10) secs = "0" + secs;
            title = "Online time: " + hours + ":" + mins + ":" + secs;
            if (mytimeleft) {
                title = "Remaining time: " + hours + ":" + mins + ":" + secs;
            }
            if (document.all || document.getElementById) {
                document.title = title;
            } else {
                self.status = title;
            }
        }

        function popUp(URL) {
            if (self.name != "chillispot_popup") {
                chillispot_popup = window.open(URL, 'chillispot_popup', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=500,height=375');
            }
        }

        function doOnLoad(result, URL, userurl, redirurl, timeleft) {
            if (timeleft) {
                mytimeleft = timeleft;
            }
            if ((result == 1) && (self.name == "chillispot_popup")) {
                doTime();
            }
            if ((result == 1) && (self.name != "chillispot_popup")) {
                chillispot_popup = window.open(URL, 'chillispot_popup', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=500,height=375');
            }
            if ((result == 2) || result == 5) {
                document.form1.username.focus()
            }
            if ((result == 2) && (self.name != "chillispot_popup")) {
                chillispot_popup = window.open('', 'chillispot_popup', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=400,height=200');
                chillispot_popup.close();
            }
            if ((result == 12) && (self.name == "chillispot_popup")) {
                doTime();
                if (redirurl) {
                    opener.location = redirurl;
                } else if (opener.home) {
                    opener.home();
                } else {
                    opener.location = "about:home";
                }
                self.focus();
                blur = 0;
            }
            if ((result == 13) && (self.name == "chillispot_popup")) {
                self.focus();
                blur = 1;
            }
        }

        function doOnBlur(result) {
            if ((result == 12) && (self.name == "chillispot_popup")) {
                if (blur == 0) {
                    blur = 1;
                    self.focus();
                }
            }
        }
    </script>
</head>

<body onLoad="javascript:doOnLoad(<?= $result ?>, '<?= $loginpath ?>?res=popup2&uamip=<?= $uamip ?>&uamport=<?= $uamport ?>&userurl=<?= $userurl ?>&redirurl=<?= $redirurl ?>&timeleft=<?= $timeleft ?>' ,'<?= $userurldecode ?>', '<?= $redirurldecode ?>' , '<?= $timeleft ?>' )" onBlur="javascript:doOnBlur(<?= $result ?>)">
    <div id="app" class="container p-4">
        <h1 class="pb-3"><?= $title ?></h1>
        <? if (isset($content["popup"])) : ?>
            <div class="alert alert-<?= $content["popup"]["type"] ?>" role="alert">
                <?= $content["popup"]["message"] ?>
            </div>
        <? endif; ?>
        <? if (isset($content["actions"])) : ?>
            <? foreach ($content["actions"] as $action) : ?>
                <a type="button" class="btn btn-<?= $action["type"] ?>" href="<?= $action["link"] ?>">
                    <?= $action["text"] ?>
                </a>
            <? endforeach; ?>
        <? endif; ?>
        <? if ($form) : ?>
            <form name="form1" method="get" action="<?= $loginpath ?>?">
                <div>
                    <input type="hidden" name="chal" value="<?= $challenge ?>">
                    <input type="hidden" name="uamip" value="<?= $uamip ?>">
                    <input type="hidden" name="uamport" value="<?= $uamport ?>">
                    <input type="hidden" name="userurl" value="<?= $userurl ?>">
                    <input type="hidden" name="login" value="login">
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" autocorrect="off" autocapitalize="none">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocorrect="off" autocapitalize="none">
                </div>
                <p>Don't have an account? <a href="register.php">Register</a>.</p>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        <? endif; ?>
        <? if ($debug) : ?>
            <div class="alert alert-light" role="alert">
                <h6>Debug Message ($_GET):</h6>
                <ul>
                    <? foreach ($_GET as $key => $value) : ?>
                        <li><?= $key ?> = <?= $value ?></li>
                    <? endforeach; ?>
                </ul>
            </div>
        <? endif; ?>
        <div class="mb-4">
            <a href="admin.php">Admin Panel</a>
        </div>
    </div>
    <script src="bootstrap.bundle.min.js"></script>
</body>

</html>