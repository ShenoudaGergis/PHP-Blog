<?php
	require_once "./initializer.php";
	require_once "./utils/misc.php";
	require      "./templates/header.php";
	require_once "./templates/banner.php";
	require_once "./templates/category-select-section.php";
	require_once "./templates/tags-select-section.php";
	require_once "./templates/post-list.php";

?>

<?php
	if(!(isset($_SESSION["user"]) && ($connection->isUserAdmin($_SESSION["user"]["id"])))) {
		header("Location: ./index.php");
		die();
	}
	getBanner("MANAGING SITE" , "CONTROL YOUR PREFERENCE");
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
					[null , false] , 
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
		    <div class="col-lg-12 sidebar-item search">
		        <form id="search_form" name="gs" method="GET" action="./admin.php">    
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
		</div>
		<div class="row">
			<div class="col-lg-2">
				<span>Blocked User</span>			
			</div>
			<div class="col-lg-10">
				<select id="usersBlk" multiple size="1">
				<?php
					foreach ($connection->getAllUsers()["data"] as $user) {
						$id = intval($user["id"]);
						if($id == $_SESSION["user"]["id"]) continue;
						$username = $user["username"];
						$r  = $connection->isUserBlocked($user["id"]);
						if($r["state"] === 0) $userBlocked = $r["data"];
						else break; 
				?>
		            <option onclick="toggleBlock(<?php echo $id; ?>)" <?php echo ($userBlocked) ? "selected" : ""; ?> value="<?php echo $id ?>"><?php echo $username . " ( ID: " . $id . " )" ?></option>
		        <?php
		    		}
		    	?>
		        </select>
		        <script type="text/javascript">
		        	new vanillaSelectBox("#usersBlk", {
					    	"placeHolder"  :"Block User",
					    	"disableSelectAll" : true ,
					    	"translations": {"items": "Users"} ,
					    	"search" : true ,
					    }
					);
		        </script>
			</div>
		</div>
		<br />

		<div class="row">
			<div class="col-lg-2">
				<span>Remove User</span>			
			</div>
			<div class="col-lg-8">
				<select class="form-control form-select-lg" id="users" multiple size="3">
				<?php
					foreach ($connection->getAllUsers()["data"] as $user) {
						$id = intval($user["id"]);
						if($id == $_SESSION["user"]["id"]) continue;
						$username = $user["username"];
				?>
		            <option value="<?php echo $id ?>"><?php echo $username . " ( ID: " . $id . " )" ?></option>
		        <?php
		    		}
		    	?>
		    	</select>
			</div>
			<div class="col-lg-2">
                <button onclick="removeUsers();" class="btn btn-block btn-md btn-outline-warning" type="submit">Remove</button>
			</div>
		</div>
		<br />


		<?php
			listPosts($posts , null , "./admin.php" , $page , $ppp , $query , $category , $tag);
		?>

    </div>
</section>

<?php
	require "./templates/footer.php";
?>