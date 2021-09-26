<?php
require_once "../utils/initializer.php";
if(!isset($_SESSION["user"])) {
	echo json_encode([
		"state" => -1,
		"data"  => []
	]);
	die();
}

header("Content-Type: application/json");
$_POST = json_decode(file_get_contents('php://input'), true);

if(!is_array($_POST["commentsID"])) {
	echo json_encode([
		"state" => -1,
		"data"  => []
	]);
	die();
}

foreach($_POST["commentsID"] as $cID) {
	$r = $connection->deleteComment($_SESSION["user"]["id"] , $cID);
	if($r["state"] !== 0) {
		echo json_encode($r);
		die();
	}
}

$comments = $connection->getUserPostsComments($_SESSION["user"]["id"]);
if($comments["state"] !== 0) {
	echo json_encode($comments);
	die();
}

echo json_encode([
	"state" => 0,
	"data"  => $comments["data"]
]);
