<?php
require_once "../utils/initializer.php";
if(!isset($_SESSION["user"])) echo json_encode([
	"state" => -1,
	"data"  => []
]);

header("Content-Type: application/json");
$_POST = json_decode(file_get_contents('php://input'), true);
echo json_encode(
	$connection->toggleUserPostLike($_POST["userID"] , $_POST["postID"])
);
