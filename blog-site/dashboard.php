<?php
	require_once "./initializer.php";
	require      "./check-block.php";
	require_once "./utils/misc.php";
	require      "./templates/header.php";
	require_once "./templates/banner.php";
	require_once "./templates/category-select-section.php";
	require_once "./templates/tags-select-section.php";
	require_once "./templates/pagination-section.php";
	require_once "./templates/post-list.php";

?>

<?php
	if(!isset($_SESSION["user"])) {
		header("Location: ./index.php");
		die();
	}
	getBanner("MANAGE YOUR POSTS" , "YOUR POSTS");
?>

<?php
	$p = fetchParams([
		"query"    => ["string" , null] ,
		"page"     => ["number" , 1] ,
		"category" => ["number" , null] ,
		"tag"      => ["number" , null] ,
	]);

	$page     = $p["page"];
	$query    = $p["query"];
	$category = $p["category"];
	$tag      = $p["tag"];

	$ppp   = 4;

	$results  = $connection->getPost(
					[$_SESSION["user"]["id"] , false] , 
					$page * $ppp - $ppp , 
					$ppp ,
					$query ,
					$category ,
					$tag
				);

	$posts    = ($results["state"] === 0) ? $results["data"] : [];

?>

<section class="blog-posts">
    <div class="container">
		<div class="row">
		    <div class="col-lg-11 sidebar-item search">
		        <form id="search_form" name="gs" method="GET" action="./dashboard.php">    
		            <div class="row">

		                <div class="col-lg-4" style="padding-bottom: 20px">
		                    <input type="text" name="query" class="form-control" placeholder="type to search..." autocomplete="on">                            
		                </div>

		                <div class="col-lg-3" style="padding-bottom: 20px">
		                    <?php
		                        getCategorySelect(true , null);
		                    ?>                                
		                </div>

		                <div class="col-lg-3">                                
		                    <?php
		                        getTagsSelect(true , []);
		                    ?>                                
		                </div>

		                <div class="col-lg-2">
		                    <button class="btn btn-block btn-md btn-outline-primary" type="submit">SEARCH</button>
		                </div>
		            </div>
		        </form>
		    </div>
		    <div class="col-lg-1">
				<button onclick="window.location.href = './new-post.php'" class="btn btn-outline-primary btn-block"><b>+</b></button>	    	
		    </div>
		</div>
		<br />

		<div class="row">
			<div class="col-lg-2">
				<span>User Comments</span>
			</div>
			<div class="col-lg-8">
				<select id="comments" class="form-control" multiple size="3">
					<?php
						$comments = $connection->getUserPostsComments($_SESSION["user"]["id"]);
						if($comments["state"] === 0) {
							foreach ($comments["data"] as $comment) {
					?>

					<option value="<?php echo $comment["id"];?>">
						<?php 
							echo "User: ( " . $comment["username"] . " ) At: ( " .
								 $comment["comment_date"] . " ) Comment: ( " .
								 $comment["comment"] . " ) On Post: ( " .
								 $comment["title"] . " )."

						?>	
					</option>

					<?php
							}
						}
					?>
				</select>
			</div>
			<div class="col-lg-2">
				<button onclick="removeComments()" class="btn btn-outline-warning btn-block">Delete</button>
			</div>
		</div>
		<br />

		<?php
			listPosts($posts , [$_SESSION["user"]["id"] , false] , "./dashboard.php" , $page , $ppp , $query , $category , $tag);
		?>

    </div>
</section>

<?php
	require "./templates/footer.php";
?>