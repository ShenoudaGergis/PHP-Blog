<?php

require_once "./initializer.php";
require      "./check-block.php";
require      "./templates/header.php";
require_once "./utils/misc.php";
require_once "./utils/validate.php";
require_once "./templates/banner.php";
require_once "./templates/category-select-section.php";
require_once "./templates/tags-select-section.php";

?>
<?php
	$errors = [];
	if(!isset($_SESSION["user"])) {
		header("Location: ./index.php");
		die();	
	}

	if($_SERVER["REQUEST_METHOD"] === "POST") {
		$oldImage = false;
		$r = fetchParams([
			"id"       => ["number" , null] ,
			"title"    => ["string" , null] ,
			"content"  => ["string" , null] ,
			"category" => ["number" , null] , 
			"tag"      => ["number" , null]
		]);

		if($r["title"]    === null) $errors[] = "title";
		if($r["content"]  === null) $errors[] = "content";
		if($r["category"] === null) $errors[] = "category";
		if($r["tag"]      === null) $errors[] = "tag";
		
		//if post id not belongs to the current user then redirect
		if($r["id"] !== null) {
			$belongs = $connection->doesUserOwnPost($_SESSION["user"]["id"] , $r["id"]);
			if(!(($belongs["state"] === 0) && ($belongs["data"]))) {
				header("Location: ./index.php");
				die();			
			}  
		} else {
			header("Location: ./index.php");
			die();			
		}

		if(isset($_FILES["image"]) && ($_FILES["image"]["error"] == 0)) {
			if(!\validation\isFileImage($_FILES["image"]["tmp_name"]))
				$errors[] = "image";
			else {
				$imageName = time();
				$move = move_uploaded_file($_FILES["image"]["tmp_name"] , "./post-images/" . $imageName);
				if(!$move) $errors[] = "image";
			}
		} else {
			$oldImage = true;
		}

		if(count($errors) === 0) {
			if(!$connection->isCategoryFound($r["category"])) $errors[] = "category";
			foreach ($r["tag"] as $tag) {
				if(!$connection->isTagFound($r["tag"])) {
					$errors[] = "tag";
					break;
				}
			}
		}

		if(count($errors) === 0) {
			$r = $connection->updatePost($r["id"] , $r["title"] , $r["content"] , ($oldImage) ? null : $imageName , $r["category"] , $r["tag"]);

			print_r($r);
			if($r["state"] === -1) $errors[] = "process";
			else {
				header("Location: ./index.php");
				die();
			}
		}

	} else if($_SERVER["REQUEST_METHOD"] === "GET") {
		$id = fetchParams([
			"id" => ["number" , null]
		])["id"];
		if($id === null) {
			header("Location: ./index.php");
			die();
		}

		$post = $connection->getPost($id);
		if(($post["state"] === -1) || (count($post["data"]) === 0)) {
			header("Location: ./index.php");
			die();			
		}

		$r = [];
		$r["id"]       = $id;
		$r["title"]    = $post["data"][0]["post_title"];
		$r["content"]  = $post["data"][0]["content"];
		$r["tag"]      = $connection->getPostTagsIDs($id)["data"];
		$r["category"] = $connection->getPostCategoryID($id)["data"];

	}
?>


<?php
	getBanner("EDIT POST" , "CHANGE YOUR POST ENTRIES");
?>

<br />
<div class="row" style="padding-left: 40px;padding-top: 20px">
	<?php
		if(in_array("process" , $errors)) {
	?>
	<div class="col-md-12 alert alert-danger" role="alert">
		Cannot Edit Post
	</div>
	<?php
	}
	?>		
</div>


<div style="padding: 30px;">
	<h4>Edit Post</h4><br /><br />
	

	<form action="./editPost.php" method="post" enctype="multipart/form-data">
		<input hidden name="id" value="<?php echo $r["id"]; ?>">
		<div class="row">
			<label class="col-md-1 form-label">Title</label>
			<div class="col-md-8">
				<input name="title" value="<?php if(!empty($r["title"])) echo $r["title"];?>" type="text" class="form-control" placeholder="Post Title...">
			</div>
				<?php
					if(in_array("title" , $errors)) {
		    	?>
				<div class="col-md-3 alert alert-danger" role="alert">
		    		Invalid Title
		    	</div>
		    	<?php
		    	}
		    	?>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 label-info">Content</label>
			<div class="col-md-8">
				<textarea name="content" class="form-control" rows="4" placeholder="Post Content..."><?php if(!empty($r["content"])) echo $r["content"];?></textarea>
			</div>
				<?php
					if(in_array("content" , $errors)) {
		    	?>
				<div class="col-md-3 alert alert-danger" role="alert">
		    		Invalid Content
		    	</div>
		    	<?php
		    	}
		    	?>
		</div>


		<br />
		<div class="row">
			<label class="col-md-1 form-label">Categories</label>
			<div class="col-md-8">
				<?php
					getCategorySelect(false , (!empty($r["category"])) ? $r["category"] : null);
				?>
			</div>
				<?php
					if(in_array("category" , $errors)) {
		    	?>
				<div class="col-md-3 alert alert-danger" role="alert">
		    		Invalid Category
		    	</div>
		    	<?php
		    	}
		    	?>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 form-label">Tags</label>
			<div class="col-md-8">
				<?php
					getTagsSelect(false , (!empty($r["tag"])) ? $r["tag"] : []);
				?>
			</div>
				<?php
					if(in_array("tag" , $errors)) {
		    	?>
				<div class="col-md-3 alert alert-danger" role="alert">
		    		Invalid Tag
		    	</div>
		    	<?php
		    	}
		    	?>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 form-label">Image</label>
			<div class="col-md-8">
				<input name="image" type="file" accept="image/*" class="form-control"/>			
			</div>
				<?php
					if(in_array("image" , $errors)) {
		    	?>
				<div class="col-md-3 alert alert-danger" role="alert">
		    		Invalid Image
		    	</div>
		    	<?php
		    	}
		    	?>
		</div>

		<br /><br />
		<div class="col-md-9">
			<button class="btn btn-primary btn-block" type="submit">Edit Post</button>		
		</div>

	</form>
</div>


<?php
	require "./templates/footer.php";
?>