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
	<title>Register</title>
	<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
	<div id="app" class="container p-4">
		<header class="pb-3 d-flex justify-content-between align-items-center">
			<h1>Register a HotSpot Account</h1>
		</header>
		<? if (isset($_GET) && $_GET["res"] === "error") : ?>
			<div class="alert alert-danger" role="alert">
				<h6>Failed to Create Account.</h6>
				<p class="m-0">Possible causes:</p>
				<ul class="m-0">
					<li>Some fields were left empty.</li>
					<li>Username already taken.</li>
					<li>Passwords don't match.</li>
				</ul>
			</div>
		<? elseif (isset($_GET) && $_GET["res"] === "success") : ?>
			<div class="alert alert-success" role="alert">
				<h6 class="m-0">Account was successfully created.</h6>
			</div>
		<? endif; ?>
		<form method="post" action="register-check.php">
			<div class="mb-3">
				<label for="username" class="form-label">Choose a username</label>
				<input type="text" class="form-control" id="username" name="username" autocorrect="off" autocapitalize="none">
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Type your password</label>
				<input type="password" class="form-control" id="password" name="password" autocorrect="off" autocapitalize="none">
			</div>
			<div class="mb-3">
				<label for="re-password" class="form-label">Retype the password</label>
				<input type="password" class="form-control" id="re-password" name="re-password" autocorrect="off" autocapitalize="none">
			</div>
			<button type="submit" class="btn btn-primary">Proceed</button>
		</form>
	</div>
	<script src="bootstrap.bundle.min.js"></script>
</body>

</html>