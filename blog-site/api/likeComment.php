<?php
require_once "../utils/initializer.php";
header("Content-Type: application/json");

if(!isset($_SESSION["user"])) echo json_encode([
	"state" => -1,
	"data"  => []
]);

$_POST = json_decode(file_get_contents('php://input'), true);
echo json_encode(
	$connection->toggleUserCommentLike($_SESSION["user"]["id"] , $_POST["commentID"])
);
