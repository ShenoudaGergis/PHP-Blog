<?php
	require_once "./templates/pagination-section.php";
?>

<?php
	function listPosts($posts , $userID , $file , $page , $ppp , $query , $category , $tag) {
		global $connection;
?>
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
						<th>Updated At</th>
   						<th>Post Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(count($posts) > 0) {
							$j = 0;
							foreach($posts as $post) {
					?>
    					<tr>
    						<td><a href="./post-details.php?id=<?php echo $post["id"]; ?>"><?php echo ++$j?></a></td>
    						<td><?php echo $post["post_title"];?></td>
    						<td><?php echo $post["category_name"];?></td>
    						<td><?php echo join(", " , $post["tags"])?></td>
    						<td><img style="width: 100px; height: 100px;" src="./post-images/<?php echo $post["image"];?>"></td>
    						<td><i class="badge rounded-pill bg-light text-dark"><?php echo date('M-j-Y',strtotime($post["publish_date"]));?></i></td>
    						<td><i class="badge rounded-pill bg-light text-dark"><?php if($post["updated_at"]) echo date('M-j-Y',strtotime($post["updated_at"])); else echo "None"?></i></td>
       						<td>
       							<button onclick="window.location.href = './editPost.php?id=' + <?php echo $post["id"];?>" class="btn btn-primary">Edit</button>
       							<button onclick="removePost(<?php echo $post["id"] . "," . "this"; ?>);" class="btn btn-warning">Remove</button>
       						</td>
    					</tr>
					<?php
							}
						}
					?>

				</tbody>
			</table>
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
?>