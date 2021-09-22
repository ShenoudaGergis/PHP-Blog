<?php
	require_once "./utils/misc.php";
?>

<?php
function paginate($page , $file , $ppp , $userID , $query , $category , $tag) {
?>

<ul class="page-numbers">	

<?php
	global $connection;

	if($page > 1) echo sprintf('<li><a href="./%s?%s"><<</a></li>' , $file , generateParams([
		"page"  => $page - 1 ,
		"query" => $query , 
		"category" => $category , 
		"tag"      => $tag
	]));

	$finalEnd = false;	
	for($i = $page - 2;$i <= $page + 2;$i++) {
		if($i <= 0) continue;
		if($i <= $page) {
			echo sprintf('<li %s><a href="./%s?%s">%d</a></li>' , ($i === $page) ? 'class="active"' : ''  , $file , generateParams([
					"page"  => $i ,
					"query" => $query , 
					"category" => $category , 
					"tag"      => $tag
				]) , $i);			
			continue;
		}
		$r = $connection->getPost(
				$userID , 
				$i * $ppp - $ppp , 
				$ppp ,
				$query ,
				$category ,
				$tag
			);

		if($r["state"] === 0) {
			if(count($r["data"]) !== 0) {
				$finalEnd = true;
				echo sprintf('<li><a href="./%s?%s">%d</a></li>' , $file , generateParams([
					"page"  => $i ,
					"query" => $query , 
					"category" => $category , 
					"tag"      => $tag
				]) , $i);

			} else break;
		} else break;
	}
	if($finalEnd) echo sprintf('<li><a href="./%s?%s"> >> </a></li>' , $file , generateParams([
		"page"  => $page + 1,
		"query" => $query , 
		"category" => $category , 
		"tag"      => $tag
	]));
?>

</ul>

<?php
}
?>
