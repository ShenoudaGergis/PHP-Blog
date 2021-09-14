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
					$email , $password
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
					$name , $username , $password , $email , $phone , $type
		));

		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		return [
			"state" => 0 ,
			"data"  => $this->userLogin($email , $password)["data"]
		];
	}

	//-----------------------------------------------------------------------------------

	public function getUserPosts($id) {
		$result = mysqli_query($this->conn ,
			sprintf("
				SELECT 
					title ,
		    		content ,
		    		publish_date ,
		    		updated_at ,
		    		categories.name AS category_name
				FROM posts
				JOIN categories ON posts.category_id = categories.id
				WHERE posts.user_id = %d" ,
				$id)
		);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$posts = [];
		while(($post = mysqli_fetch_assoc($result)) !== null) {
			array_push($posts , $post);
		}
		return [
			"state" => 0 ,
			"data"  => $posts
		];
	}

	//-------------------------------------------------------------------------php

	public function getRecentPosts($from , $to) {
		$result = mysqli_query($this->conn , 
			sprintf("SELECT 
						posts.id ,
				    	categories.name as category_name ,
				    	posts.title AS post_title ,
				    	posts.content , 
				    	users.name AS user_name , 
				    	posts.publish_date
					FROM posts
					JOIN users on users.id = posts.user_id
					JOIN categories on categories.id = posts.category_id
					ORDER BY posts.publish_date DESC
					LIMIT %d , %d",

					$from , $to
				)
			);
		if($result === false) return ["state" => -1 , "error" => mysqli_error($this->conn)];
		$posts = [];
		while(($post = mysqli_fetch_assoc($result)) !== null) {
			$r = $this->getPostCommentsCount($post["id"]);
			if($r["state"] === 0) $post["comment_count"] = $r["data"];
			array_push($posts , $post);
		}
		return [
			"state" => 0 ,
			"data"  => $posts
		];
	}

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
}

$connection = new DB();
// print_r($connection->getPostCommentsCount(6));
// print_r($connection->getRecentPosts(0 , 3));
