<?php
$r = $connection->isUserBlocked($_SESSION["user"]["id"]);
if($r["state"] === 0) {
	if($r["data"]) {
		header("Location: ./index.php");
		die();
	}
} 