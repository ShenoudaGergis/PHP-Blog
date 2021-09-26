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

	public function isUserAdmin($userID) {
		$result = mysqli_query($this->conn , "SELECT id as sum FROM users WHERE id=$userID AND type=1");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
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
			"SELECT 
					comments.id ,
					comments.comment ,
			    	comments.comment_date ,
			    	users.username
			   
				FROM comments
				JOIN posts on posts.id = comments.post_id
				JOIN users on comments.user_id = users.id
				WHERE comments.post_id = $postID
				ORDER BY comments.comment_date;"
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
						posts.user_id ,
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
		$r = $this->isUserAdmin($userID);
		if($r["state"] !== 0) return $r;
		if($r["data"]) $query = "DELETE FROM posts WHERE id = $postID";
		else $query = "DELETE FROM posts WHERE id = $postID AND user_id = $userID";
		
		$result = mysqli_query($this->conn ,  $query);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_affected_rows($this->conn)
		];

	}

	//---------------------------------------------------------------------

	public function isUserBlocked($userID) {
		$result = mysqli_query($this->conn , "SELECT blocked FROM users WHERE id=$userID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$r = mysqli_fetch_assoc($result);
		if($r === null) return [
			"state" => 0 ,
			"data"  => -1
		];

		return [
			"state" => 0 ,
			"data"  => intval($r["blocked"]) === 1
		];
	}

	//---------------------------------------------------------------------

	public function toggleUserBlocking($userID) {
		$state = $this->isUserBlocked($userID);

		if($state["state"] !== 0) return $state;
		$query = "UPDATE users SET blocked=";
		if($state["data"]) $query .= "0 WHERE id=$userID";
		else $query .= "1 WHERE id=$userID";

		$result = mysqli_query($this->conn , $query);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		
		return [
			"state" => 0 ,
			"data"  => !$state["data"]
		];	
	}

	//---------------------------------------------------------------------

	public function getUserIDFromPostID($postID) {
		$result = mysqli_query($this->conn , "SELECT user_id FROM posts WHERE id=$postID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result)["user_id"]
		];
	}
	
	//---------------------------------------------------------------------

	public function getAllUsers() {
		$result = mysqli_query($this->conn , "SELECT id , username FROM users");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$data = [];
		while(($user = mysqli_fetch_assoc($result)) !== null) {
			array_push($data , $user);
		}
		return [
			"state" => 0 ,
			"data"  => $data
		];
	}

	//---------------------------------------------------------------------

	public function deleteUser($usersID , $filter) {
		$query = "DELETE FROM users WHERE id=*";
		$done  = 0;
		foreach ($usersID as $id) {
			if(in_array($id , $filter)) continue;
			$result = mysqli_query($this->conn , str_replace("*" , $id , $query));
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
			if(mysqli_affected_rows($this->conn) > 0) $done++;
		}
		$r = $this->getAllUsers();
		if($r["state"] !== 0) $r = ["data" => []];
		$r["data"] = array_filter($r["data"] , function($element) use ($filter) {
			return !in_array($element["id"] , $filter);
		});
		return [
			"state" => 0 ,
			"data"  => ["deleted" => $done , "users" => $r["data"]]
		];

	}

	//-------------------------------------------------------------------------

	public function getPostTagsIDs($postID) {
		$result = mysqli_query($this->conn , "SELECT tag_id FROM post_tags WHERE post_id = $postID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$ids = [];

		while (($id = mysqli_fetch_assoc($result)) !== null) {
			array_push($ids , intval($id["tag_id"]));
		}

		return [
			"state" => 0 ,
			"data"  => $ids
		];	
	}

	//-------------------------------------------------------------------------
	
	public function getPostCategoryID($postID) {
		$result = mysqli_query($this->conn , "SELECT category_id FROM posts WHERE id = $postID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => intval(mysqli_fetch_assoc($result)["category_id"])

		];	
	}

	//-------------------------------------------------------------------------

	public function removePostTags($postID) {
		$result = mysqli_query($this->conn , "DELETE FROM post_tags WHERE post_id = $postID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_affected_rows($this->conn)

		];	
	}

	//-------------------------------------------------------------------------

	public function attachTagsToPost($postID , $tagsIDs) {
		$count = 0;
		foreach ($tagsIDs as $id) {
			$query = "INSERT INTO post_tags (post_id , tag_id) VALUES ($postID , $id)";
			$result = mysqli_query($this->conn , $query);
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
			$count++;
		}
		return [
			"state" => 0 ,
			"data"  => $count
		];	
	}

	//-------------------------------------------------------------------------

	public function updatePost($postID , $title , $content , $image , $categoryID , $tagsIDs) {
		$query = "UPDATE posts SET title='$title', content='$content', category_id=$categoryID , updated_at=CURRENT_TIMESTAMP";
		if($image !== null) $query .= ", image='$image'";
		$query .= " WHERE id=$postID";		

		$result = mysqli_query($this->conn , $query);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		
		if($this->removePostTags($postID)["state"] === -1) return ["state" => -1 , "error" => mysqli_error($this->conn)];

		if($this->attachTagsToPost($postID , $tagsIDs)["state"] === -1) return ["state" => -1 , "error" => mysqli_error($this->conn)];

		return [
			"state" => 0 ,
			"data"  => mysqli_affected_rows($this->conn)

		];
	}

	//-------------------------------------------------------------------------

	public function doesUserOwnPost($userID , $postID) {
		$result = mysqli_query($this->conn , "SELECT id FROM posts WHERE id=$postID AND user_id=$userID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => (mysqli_fetch_assoc($result) !== null)

		];	
	}

	//-------------------------------------------------------------------------

	public function getUserPostsComments($userID) {
		$result = mysqli_query($this->conn , "SELECT users.username , comments.id as id , comment , title , comment_date FROM comments JOIN posts on posts.id = comments.post_id JOIN users ON comments.user_id = users.id WHERE posts.user_id = $userID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$comments = [];

		while (($comment = mysqli_fetch_assoc($result)) !== null) {
			array_push($comments , $comment);
		}	

		return [
			"state" => 0 ,
			"data"  => $comments
		];	
	}

	//-------------------------------------------------------------------------

	public function isCommentOnUserPost($userID , $commentID) {
		# the comment of that ID is on post that was posted by user with ID is that ?
		$query = "SELECT posts.user_id FROM comments JOIN posts ON posts.id = comments.post_id WHERE comments.id = $commentID AND posts.user_id = $userID";
		
		$result = mysqli_query($this->conn , $query);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//-------------------------------------------------------------------------

	public function deleteComment($userID , $commentID) {
		$r = $this->isCommentOnUserPost($userID , $commentID);
		if($r["state"] !== 0) return $r;
		if($r["data"]) {
			$result = mysqli_query($this->conn , "DELETE FROM comments WHERE id=$commentID");
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];																																																																							
			return [
				"state" => 0 ,
				"data"  => mysqli_affected_rows($this->conn)
			];
		}
		return [
			"state" => 0 ,
			"data"  => 0
		];
	}

	//---------------------------------------------------------------------

	public function userLikesComment($userID , $commentID) {
		$result = mysqli_query($this->conn , "SELECT id FROM comment_likes WHERE comment_id=$commentID AND user_id=$userID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//-------------------------------------------------------------------------

	public function getCommentLikes($commentID) {
		$result = mysqli_query($this->conn , "SELECT count(id) as sum FROM comment_likes WHERE comment_id=$commentID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result)["sum"]
		];
	}

	//---------------------------------------------------------------------

	public function toggleUserCommentLike($userID , $commentID) {
		$r = $this->userLikesComment($userID , $commentID);
		if($r["state"] === 0) {
			if($r["data"] === true) {
				//remove like
				$result = mysqli_query($this->conn , "DELETE FROM comment_likes WHERE user_id = $userID AND comment_id = $commentID");
				if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
				return [
					"state" => 0 ,
					"data"  => [
						"action" => -1 ,
						"total"  => ($t = $this->getCommentLikes($commentID))["state"] === 0 ? $t["data"] : -1
					]
				];

			} else {
				//add like
				$result = mysqli_query($this->conn , "INSERT INTO comment_likes (id , comment_id , user_id , like_date) VALUES (NULL , $commentID , $userID , CURRENT_TIMESTAMP)");
				if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
				return [
					"state" => 0 ,
					"data"  => [
						"action" => 1 ,
						"total"  => ($t = $this->getCommentLikes($commentID))["state"] === 0 ? $t["data"] : -1
					]
				];

			}
		} else return $r;
		
	}

	//-------------------------------------------------------------------------

	public function doesUserFollow($followerID , $followingID) {
		$result = mysqli_query($this->conn , "SELECT id FROM follows WHERE follower_id=$followerID AND following_id=$followingID");
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => mysqli_fetch_assoc($result) !== null
		];
	}

	//-------------------------------------------------------------------------

	public function toggleFollowUser($followerID , $followingID) {
		$r = $this->doesUserFollow($followerID , $followingID);
		if($r["state"] !== 0) return $r;
		if($r["data"]) {
			# remove following
			$query = "DELETE FROM follows WHERE follower_id=$followerID AND following_id=$followingID";
			$result = mysqli_query($this->conn , $query);
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
			return [
				"state" => 0 ,
				"data"  => -1
			];

			
		} else {
			# add following
			$query = "INSERT INTO follows (id , follower_id , following_id , follow_date) VALUES (NULL , $followerID , $followingID , CURRENT_TIMESTAMP)";
			$result = mysqli_query($this->conn , $query);
			if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
			return [
				"state" => 0 ,
				"data"  => 1
			];

		}

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
// print_r($connection->deletePost(5 , 33));
// print_r($connection->isUserAdmin(5));
// print_r($connection->isUserBlocked(6));
// print_r($connection->toggleUserBlocking(5));
// print_r($connection->getUserIDFromPostID(320));
// print_r($connection->getAllUsers());
// print_r($connection->deleteUser([22] , [5]));
// print_r($connection->userAdd("ccc" , "bbb" , "jesusCHRIST7#" , "asdsad" , null , null));
// print_r($connection->getPostCategoryID(37));
// print_r($connection->removePostTags(37));
// print_r($connection->updatePost(37 , "AAA" , "BBB" , 222 , 1 , [2 , 4]));
// print_r($connection->doesUserOwnPost(5 , 37));
// print_r($connection->getUserPostsComments(5));
// print_r($connection->isCommentOnUserPost(5 , 3));
// print_r($connection->getPostComments(37));
// print_r($connection->getCommentLikes(22));
// print_r($connection->toggleFollowUser(5 , 42));