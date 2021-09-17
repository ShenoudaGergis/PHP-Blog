<?php
	require_once "./utils/validate.php";
	require_once "./initializer.php";
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
	function generateParameters($page , $query , $category , $tag) {
		$builder = "";
		if($page     !== null) $builder .= "page=$page&";
		if($query    !== null) $builder .= "query=$query&";
		if($category !== null) $builder .= "category=$category&";
		if($tag      !== null) $builder .= "tag=$tag&";
		return $builder;
	}
?>


<?php
	function getPostsSection($userID , $file) {

		global      $connection;
		$page     = $_GET["page"];
		$query    = $_GET["query"];
		$category = $_GET["category"];
		$tag      = $_GET["tag"];


		if(trim($query) === "") $query = null;

		if(\validation\validate("number" , $category)) $category = intval($category);
		else $category = null;

		if(\validation\validate("number" , $page)) {
			$page = intval($page);
			if($page === 0) $page = 1;
		}
		else $page = 1;

		if(\validation\validate("number" , $tag)) $tag = intval($tag);
		else $tag = null;


		$ppp    = 2;
        $from   = ($page - 1) * $ppp;
        $result = $connection->getPost($userID , $from , $ppp , $query , $category , $tag);
        if($result["state"] === 0) $posts = $result["data"];
        else $posts = [];

		?>

        <div class="all-blog-posts">
            <div class="row">

        <?php
	        foreach ($posts as $post) {
	            getPostCard($post);
	        }
        ?>

        <div class="col-lg-12">
            <ul class="page-numbers">

        <?php
            if(count($posts) !== 0) {

                if($page === 2) {
                    echo sprintf('<li><a href="./%s?%s"><<</a></li>' , $file , generateParameters(1 , $query , $category , $tag));
                    echo sprintf('<li><a href="./%s?%s">1</a></li>'  , $file , generateParameters(1 , $query , $category , $tag));
                }
                if($page > 2) {
                    echo sprintf('<li><a href="./%s?%s"><<</a></li>' , $file , generateParameters($page - 1 , $query , $category , $tag));
                    echo sprintf('<li><a href="./%s?%s">%d</a></li>' , $file , generateParameters($page - 2 , $query , $category , $tag) , $page - 2);
                    echo sprintf('<li><a href="./%s?%s">%d</a></li>' , $file , generateParameters($page - 1 , $query , $category , $tag) , $page - 1);
                }
                echo sprintf('<li class="active"><a href="#">%d</a></li>' , $page);

				$result = $connection->getPost($userID , $from + $ppp * 1 , $ppp , $query , $category , $tag);
                if($result["state"] === 0) $posts = $result["data"];
                else $posts = [];
                if(count($posts) !== 0) {
                    echo sprintf('<li><a href="./%s?%s">%d</a></li>' , $file , generateParameters($page + 1 , $query , $category , $tag) , $page + 1);

					$result = $connection->getPost($userID , $from + $ppp * 2 , $ppp , $query , $category , $tag);
                    if($result["state"] === 0) $posts = $result["data"];
                    else $posts = [];
                    if(count($posts) !== 0) {
                    	echo sprintf('<li><a href="./%s?%s">%d</a></li>' , $file , generateParameters($page + 2 , $query , $category , $tag) , $page + 2);
                    	echo sprintf('<li><a href="./%s?%s">>></a></li>' , $file , generateParameters($page + 1 , $query , $category , $tag) , $page + 1);
                    } else {
                    	echo sprintf('<li><a href="./%s?%s">>></a></li>' , $file , generateParameters($page + 1 , $query , $category , $tag) , $page + 1);
                    }
                }


            } else if($page !== 1) {
        ?>
            <script type="text/javascript">
                window.location.href = "./" . <?php echo $file ?> . "?page=1";
            </script>
        <?php
            }
        ?>

                	</ul>
                </div>              
            </div>
        </div>



<?php

	}
?>