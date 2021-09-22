<?php
namespace database;

class DB {
	private $conn = null;
	
	public function __construct($host="127.0.0.1" , $user="root" , $pass="" , $db="blog") {
		if($conn = mysqli_connect($host , $user , $pass , $db)) {
			$this->conn = $conn;
		}
	}

	//-----------------------------------------------------------------------------------

	public function checkConnection() {
		return ($this->conn === null) ? false : true;
	}

	//-----------------------------------------------------------------------------------

	public function userLogin($email , $password) {
		$result = mysqli_query($this->conn , 
			sprintf("SELECT id , username , type FROM users WHERE email='%s' and password='%s'" ,
					$email , md5($password)
			)
		);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => ($r = mysqli_fetch_assoc($result)) ? $r : []
		];
	}

	//-----------------------------------------------------------------------------------

	public function userAdd(
		$name ,
		$username ,
		$password ,
		$email ,
		$phone ,
		$type
	) {
		$result = mysqli_query($this->conn ,
			sprintf("INSERT INTO users (id , name , username , password , email , phone , type) VALUES (null , '%s' , '%s' , '%s' , '%s' , %d , %d);" , 
					$name , $username , md5($password) , $email , $phone , $type
		));

		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => $this->userLogin($email , $password)["data"]
		];
	}

	//-----------------------------------------------------------------------------------

	public function addPost($userID , $title , $content , $image , $cID , $tIDs) {
		$result = 
			mysqli_query(
				$this->conn ,
				sprintf('INSERT INTO posts 
					(id , 
				     title , 
				     content , 
				     image , 
				     publish_date , 
				     created_at , 
				     category_id , 
				     user_id)
					 VALUES (NULL , "%s" , "%s" , "%s" , CURRENT_TIMESTAMP , CURRENT_TIMESTAMP , %d , %d)' , 
				$title , 
				$content ,
				$image ,
				$cID ,
				$userID)
			);

		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];

		$newPostID = mysqli_insert_id($this->conn);
		foreach ($tIDs as $id) {
			$result = mysqli_query($this->conn , sprintf(
					  		"INSERT INTO post_tags (post_id , tag_id) 
					  		values (%d , %d)" , $newPostID ,  $id));
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		}

		return [
			"state" => 0 ,
			"data"  => true
		];	
	}

	//-----------------------------------------------------------------------------------

	// public function getUserPosts($id) {
	// 	$result = mysqli_query($this->conn ,
	// 		sprintf("
	// 			SELECT 
	// 				posts.id ,
	// 				title ,
	// 	    		content ,
	// 	    		publish_date ,
	// 	    		updated_at ,
	// 	    		categories.name AS category_name
	// 			FROM posts
	// 			JOIN categories ON posts.category_id = categories.id
	// 			WHERE posts.user_id = %d" ,
	// 			$id)
	// 	);
	// 	if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
	// 	$posts = [];
	// 	while(($post = mysqli_fetch_assoc($result)) !== null) {
	// 		array_push($posts , $post);
	// 	}
	// 	return [
	// 		"state" => 0 ,
	// 		"data"  => $posts
	// 	];
	// }

	//-------------------------------------------------------------------------

	public function getPostCommentsCount($id) {
		$result = mysqli_query($this->conn , 
			"SELECT count(1) AS comment_count
			 FROM comments
			 WHERE post_id = $id  
			"
		);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0,
			"data"  => mysqli_fetch_assoc($result)["comment_count"]
		];

	}

	//-------------------------------------------------------------------------

	public function getPostComments($postID) {
		$result = mysqli_query($this->conn , 
			"	SELECT 
					comments.comment ,
			    	comments.comment_date ,
			    	users.username
			   
				FROM comments
				LEFT JOIN comment_likes on comments.id = comment_likes.comment_id
				JOIN users on comments.user_id = users.id
				WHERE comments.post_id = $postID 
			"
		);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$comments = [];
		while(($comment = mysqli_fetch_assoc($result)) !== null) {
			array_push($comments , $comment);
		}
		return [
			"state" => 0 ,
			"data"  => $comments
		];
	}

	//-------------------------------------------------------------------------

	public function getPostTags($postID) {
		$result = mysqli_query($this->conn , 
			"
			SELECT 
				tags.name
			FROM tags
			JOIN post_tags on post_tags.tag_id = tags.id
			WHERE post_tags.post_id = $postID
			"
		);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$tags = [];
		while(($tag = mysqli_fetch_assoc($result)) !== null) {
			array_push($tags , $tag["name"]);
		}
		return [
			"state" => 0 ,
			"data"  => $tags
		];		
	}

	//-------------------------------------------------------------------------

	public function getPost(...$args) {
		# 1 : postID
		# 2 : from   | to
		# 3 : userID | from | to
		# 4 : userID | from | to | query
		# 5 : userID | from | to | query | category
		# 6 : userID | from | to | query | category | tag

		// print_r(gettype($args));
		// var_dump($args);
		// die();
		$builder = "WHERE 1=1";
		switch (count($args)) {

			case 6 :
				if($args[5] !== null) {
					$tagClause = [];					
					for($i = 0;$i < count($args[5]);$i++) {
						$tagClause[$i] = "tag_id = " . $args[5][$i];
					}
					$builder .= sprintf(" AND posts.id IN (SELECT post_id FROM post_tags WHERE %s)" , join(" OR " , $tagClause));
				}

			case 5 :
				if($args[4] !== null)
					$builder .= " AND posts.category_id = $args[4]";

			case 4 :
				if($args[3] !== null)
					$builder .= str_replace("#" , $args[3] , " AND posts.content LIKE '%#%'");

			case 3:
				if($args[0] !== null) 
					$builder  .= " AND users.id = $args[0]";
				$builder .= " ORDER BY posts.publish_date DESC LIMIT $args[1] , $args[2]";
				break;

			case 2:
				$builder .= " ORDER BY posts.publish_date DESC LIMIT $args[0] , $args[1]";
				break;

			case 1:
				$builder .= " AND posts.id = $args[0]";
				break;
		}
		// print($builder);
		// return;

		$result = mysqli_query($this->conn , 
			sprintf("SELECT 
						posts.id ,
						posts.image ,
				    	categories.name as category_name ,
				    	posts.title AS post_title ,
				    	posts.content , 
				    	users.name AS user_name , 
				    	posts.publish_date
					FROM posts
					JOIN users on users.id = posts.user_id
					JOIN categories on categories.id = posts.category_id
					%s" , $builder)
			);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$posts = [];
		while(($post = mysqli_fetch_assoc($result)) !== null) {
			$r = $this->getPostComments($post["id"]);
			if($r["state"] === 0) $post["comments"] = $r["data"];
			$r = $this->getPostTags($post["id"]);
			if($r["state"] === 0) $post["tags"] = $r["data"];
			array_push($posts , $post);
		}
		return [
			"state" => 0 ,
			"data"  => $posts
		];

	}

	//-------------------------------------------------------------------------

	public function addComment($userID , $postID , $message) {
		if(strlen($message = trim($message)) !== 0) {
			$result = mysqli_query($this->conn ,
				sprintf('INSERT INTO comments 
						(comment , post_id , user_id)
						VALUES ("%s" , %d , %d)

				' , $message , $postID , $userID));
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
			return [
				"state" => 0 ,
				"data"  => true
			];
		}
	}

	//---------------------------------------------------------------------

	public function getCategories() {
		$result = mysqli_query($this->conn , "SELECT id , name FROM categories");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$categories = [];
		while(($category = mysqli_fetch_assoc($result)) !== null) {
			array_push($categories , $category);
		}
		return [
			"state" => 0 ,
			"data"  => $categories
		];

	}

	//---------------------------------------------------------------------

	public function getTags() {
		$result = mysqli_query($this->conn , "SELECT id , name FROM tags");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$tags = [];
		while(($tag = mysqli_fetch_assoc($result)) !== null) {
			array_push($tags , $tag);
		}
		return [
			"state" => 0 ,
			"data"  => $tags
		];

	}

	//---------------------------------------------------------------------

	public function isTagFound($id) {
		$result = mysqli_query($this->conn , "SELECT id FROM tags WHERE id=$id");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//---------------------------------------------------------------------

	public function isCategoryFound($id) {
		$result = mysqli_query($this->conn , "SELECT id FROM categories WHERE id=$id");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//---------------------------------------------------------------------

	public function userLikesPost($userID , $postID) {
		$result = mysqli_query($this->conn , "SELECT id FROM likes WHERE post_id=$postID AND user_id=$userID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//---------------------------------------------------------------------

	public function getPostLikes($postID) {
		$result = mysqli_query($this->conn , "SELECT count(id) as sum FROM likes WHERE post_id=$postID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result)["sum"]
		];
	}

	//---------------------------------------------------------------------

	public function toggleUserPostLike($userID , $postID) {
		$r = $this->userLikesPost($userID , $postID);
		if($r["state"] === 0) {
			if($r["data"] === true) {
				//remove like
				$result = mysqli_query($this->conn , "DELETE FROM likes WHERE user_id = $userID AND post_id = $postID");
				if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
				return [
					"state" => 0 ,
					"data"  => [
						"action" => -1 ,
						"total"  => ($t = $this->getPostLikes($postID))["state"] === 0 ? $t["data"] : -1
					]
				];

			} else {
				//add like
				$result = mysqli_query($this->conn , "INSERT INTO likes (id , like_date , post_id , user_id) VALUES (NULL , CURRENT_TIMESTAMP , $postID , $userID)");
				if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
				return [
					"state" => 0 ,
					"data"  => [
						"action" => 1 ,
						"total"  => ($t = $this->getPostLikes($postID))["state"] === 0 ? $t["data"] : -1
					]
				];

			}
		} else return $r;
		
	}

	//---------------------------------------------------------------------

	public function deletePost($userID , $postID) {
		$query = "DELETE FROM posts WHERE id = $postID " . (($userID) ? "AND user_id = $userID" : "");
		$result = mysqli_query($this->conn ,  $query);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => true
		];

	}
}

$connection = new DB();
// print_r($connection->getRecentPosts(0 , 4));
// print_r($connection->getPost(5 , 0 , 10 , null , null , [1 , 2]));
// print_r($connection->addComment(10 , 6 , "by the name of jesus christ"));
// print_r($connection->getUserPosts(10));
// print_r($connection->getTags());
// print_r($connection->isCategoryFound(1));
// print_r($connection->getPostLikes(32));
// print_r($connection->toggleUserPostLike(5 , 29));
// print_r($connection->deletePost(5 , 7));