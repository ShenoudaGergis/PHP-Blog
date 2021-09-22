<?php
	require_once "./initializer.php";
	// require_once "./utils/validate.php";
	require_once "./utils/misc.php";
	require_once "./templates/pagination-section.php";

?>

<?php
	function getPostCard($post) {
		$tags = "";
		foreach ($post["tags"] as $tag) {
			$tags .= sprintf('<a><li> %s </li> ' , $tag);
		}
?>

	<div class="col-lg-6">
		<div class="blog-post">
			<div class="blog-thumb">
				<img src="./assets/images/banner-item-01.jpg" alt="">
			</div>
			<div class="down-content">
				<span> <?php echo $post["category"]; ?> </span>
				<a href="post-details.php?id=<?php echo $post["id"]; ?>">
					<h4> <?php echo $post["post_title"]; ?> </h4>
				</a>
				<ul class="post-info">
					<li><i class="badge rounded-pill bg-primary text-light"><?php echo $post["user_name"]; ?></i></li>
					<li><i class="badge rounded-pill bg-secondary text-light"><?php echo count($post["comments"]); ?> comments</i></li>
					<li><i class="badge rounded-pill bg-warning text-dark"><?php echo date('M-j-Y',strtotime($post["publish_date"]));?></i></li>
				</ul>
				<p><?php  echo (strlen($s = $post["content"]) > 40) ? substr($s, 0, 37) . "..." : $s ?></p>
				<div class="post-options">
					<div class="row">
						<div class="col-lg-12">
							<ul class="post-tags">
								<li><i class="fa fa-tags"></i></li>
								<?php echo $tags; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php 
	}
?>


<?php
	function getPostsSection($userID , $file) {

		global      $connection;
	
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

		$ppp    = 4;

        $from   = ($page * $ppp) - $ppp;
        $result = $connection->getPost($userID , 
        							   $from , 
        							   $ppp , 
        							   $query , 
        							   $category , 
        							   $tag);

        if($result["state"] === 0) $posts = $result["data"];
        else $posts = [];

?>

        <?php
            if(count($posts) !== 0) {
        ?>

        <div class="all-blog-posts">
            <div class="row">

        <?php 
        	foreach ($posts as $post) {
	            getPostCard($post);
	        }
        ?>
            </div>
        </div>
        <div class="row">
        	<div class="col-lg-12">        		
			<?php
				paginate(
					$page , 
					$file , 
					$ppp , 
					$userID ,
					$query ,
					$category ,
					$tag
				);
			?>
			</div>
		</div>

		<?php
				}
			}
		?>