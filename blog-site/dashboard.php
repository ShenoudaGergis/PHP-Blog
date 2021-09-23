<?php
	require_once "./initializer.php";
	require_once "./utils/misc.php";
	require      "./templates/header.php";
	require_once "./templates/banner.php";
	require_once "./templates/category-select-section.php";
	require_once "./templates/tags-select-section.php";
	require_once "./templates/pagination-section.php";

?>

<?php
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

	if(isset($_SESSION["user"])) {
		$results  = $connection->getPost(
						$_SESSION["user"]["id"] , 
						$page * $ppp - $ppp , 
						$ppp ,
						$query ,
						$category ,
						$tag
					);

		$posts    = ($results["state"] === 0) ? $results["data"] : [];
	} else $posts = [];

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
    		<div class="col-lg-12">
    			<table class="table">
    				<thead>
    					<tr>
    						<th>#</th>
    						<th>Title</th>
    						<th>Category</th>
    						<th>Tags</th>
    						<th>Image</th>
    						<th>Publish Date</th>
       						<th>Actions</th>
    					</tr>
    				</thead>
    				<tbody>
    					<?php
    						if(count($posts) > 0) {
    							$j = 0;
    							foreach($posts as $post) {
    					?>
	    					<tr>
	    						<td><?php echo ++$j?></td>
	    						<td><?php echo $post["post_title"];?></td>
	    						<td><?php echo $post["category_name"];?></td>
	    						<td><?php echo join(", " , $post["tags"])?></td>
	    						<td><img style="width: 100px; height: 100px;" src="./post-images/<?php echo $post["image"];?>"></td>
	    						<td><i class="badge rounded-pill bg-light text-dark"><?php echo date('M-j-Y',strtotime($post["publish_date"]));?></i></td>
	       						<td>
	       							<button onclick="window.location.href = './edit.php?id=' + <?php echo $post["id"];?>" class="btn btn-primary">Edit</button>
	       							<button onclick="removePost(<?php echo $_SESSION["user"]["id"] . " , " . $post["id"] . " , this"; ?>);" class="btn btn-warning">Remove</button>
	       						</td>    						
	    					</tr>
    					<?php
    							}
    					?>

    					<?php

							}
    					?>

    				</tbody>
    			</table>
    			<?php
					paginate(
						$page , 
						"dashboard.php" , 
						$ppp , 
						$_SESSION["user"]["id"] ,
						$query ,
						$category ,
						$tag
					);
    			?>    		
    		</div>
    	</div>
    </div>
</section>

<?php
	require "./templates/footer.php";
?>