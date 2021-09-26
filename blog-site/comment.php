<?php
require_once "./initializer.php";
require      "./check-block.php";


if(!isset($_SESSION["user"])) header("Location: ./login.php");

$r = $connection->addComment($_SESSION["user"]["id"] , $_POST["id"] , $_POST["message"]);
if($r["state"] === 0) {
	header("Location: ./post-details.php?id=" . $_POST["id"]);
} else {
	header("Location: ./index.php");
}