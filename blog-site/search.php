<?php
require_once "./initializer.php";
require_once "./utils/validate.php";
require      "./templates/header.php";
require_once "./templates/banner.php";
require_once "./templates/post-card.php";

?>

<?php 
	getBanner("SEARCH BLOGS" , "SEARCH RESULTS");
?>

<section class="blog-posts grid-system">
    <div class="container">
        <div class="row">
        	<div class="col-lg-12">

        		<?php 
					getPostsSection(null , "search.php");
				?>
				        				
        	</div>    	
  		</div>
	</div>
</section>
	

<?php
require "./templates/footer.php";
?>
