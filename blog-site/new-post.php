<?php

require_once "./initializer.php";
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
		$r = fetchParams([
			"title"    => ["string" , null] ,
			"content"  => ["string" , null] ,
			"category" => ["number" , null] , 
			"tag"      => ["number" , null]
		]);

		if($r["title"]    === null) $errors[] = "title";
		if($r["content"]  === null) $errors[] = "content";
		if($r["category"] === null) $errors[] = "category";
		if($r["tag"]      === null) $errors[] = "tag";
		if(isset($_FILES["image"]) && ($_FILES["image"]["error"] == 0)) {
			if(!\validation\isFileImage($_FILES["image"]["tmp_name"]))
				$errors[] = "image";
			else {
				$imageName = time();
				if(!move_uploaded_file($_FILES["image"]["tmp_name"] , "./post-images/" . $imageName . "jpeg"))
					$errors[] = "image";
			}
		} else {
			$errors[] = "image";
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
			$r = $connection->addPost(
				$_SESSION["user"]["id"] , 
				$r["title"] ,
				$r["content"] ,
				$imageName ,
				$r["category"] ,
				$r["tag"]
			);
			if($r["state"] === -1) $errors[] = "process";
			else {
				header("Location: ./index.php");
				die();
			}
		}

	}
?>


<?php
	getBanner("NEW POST" , "PUBLISH NEW POST");
?>

<br />
<div class="row" style="padding-left: 40px;padding-top: 20px">
	<div class="col-md-12 text-danger">
    	<?php
    		echo (in_array("process" , $errors)) ? "Error In Creating Post" : "";
    	?>
	</div>			
</div>

<div style="margin-top: 20px;padding: 40px;">
	<h4>Create New Post</h4><br /><br />
	<form action="./new-post.php" method="post" enctype="multipart/form-data">
		<div class="row">
			<label class="col-md-1 form-label">Title</label>
			<div class="col-md-9">
				<input name="title" type="text" class="form-control" placeholder="Post Title...">
			</div>
		    <div class="col-md-2 text-danger">
		    	<?php
		    		echo (in_array("title" , $errors)) ? "Invalid Title" : "";
		    	?>
			</div>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 form-label">Content</label>
			<div class="col-md-9">
				<textarea name="content" class="form-control" rows="4" placeholder="Post Content..."></textarea>
			</div>
		    <div class="col-md-2 text-danger">
		    	<?php
		    		echo (in_array("content" , $errors)) ? "Invalid Content" : "";
		    	?>
			</div>
		</div>


		<br />
		<div class="row">
			<label class="col-md-1 form-label">Categories</label>
			<div class="col-md-9">
				<?php
					getCategorySelect(false);
				?>
			</div>
		    <div class="col-md-2 text-danger">
		    	<?php
		    		echo (in_array("category" , $errors)) ? "Invalid Category" : "";
		    	?>
			</div>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 form-label">Tags</label>
			<div class="col-md-9">
				<?php
					getTagsSelect(false);
				?>
			</div>
		    <div class="col-md-2 text-danger">
				<?php
		    		echo (in_array("tag" , $errors)) ? "Invalid Tag" : "";
		    	?>
			</div>
		</div>

		<br />
		<div class="row">
			<label class="col-md-1 form-label">Image</label>
			<div class="col-md-9">
				<input name="image" type="file" accept="image/*" class="form-control"/>			
			</div>
		    <div class="col-md-2 text-danger">
				<?php
		    		echo (in_array("image" , $errors)) ? "Invalid Image" : "";
		    	?>
			</div>
		</div>

		<br />
		<div class="col-md-10">
			<button class="btn btn-primary" style="float: right;" type="submit">Create New Post</button>		
		</div>

	</form>
</div>


<?php
	require "./templates/footer.php";
?>