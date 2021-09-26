
function confirmDelete(text) {
	return confirm(text);
}

//-----------------------------------------------------------------------------

function sendRequest(path , body , onFinish) {
	fetch(path , {
		method  : "POST" ,
		headers : {
			"credentials"  : 'include',
			"Content-Type" : "application/json"
		} ,
		body : JSON.stringify(body)
	}).then((res) => {
		return res.json()
	}).then((d) => {
		onFinish(d);
	})

}

//-----------------------------------------------------------------------------

function toggleLike(userID , postID) {
	sendRequest("./api/like.php" , {
		"userID" : userID ,
		"postID" : postID
	} , (d) => {
		if(d["state"] !== 0) return;
		document.getElementById("postLikes").innerText = d["data"]["total"] + " Likes"; 
		document.getElementById("likeBtn").innerText = (d["data"]["action"] === -1) ? "Like" : "Dislike"; 

	});
}

//-----------------------------------------------------------------------------

function removePost(postID , node) {
	if(!confirmDelete("Are you sure to delete this post ?")) return;
	let row   = node.closest("tr");
	
	sendRequest("./api/removePost.php" , {
		"postID" : postID		
	} , (d) => {
		if(d["state"] !== 0) return;
		if(d["data"] === 1 ) row.remove();
	})
}

//-----------------------------------------------------------------------------

function toggleBlock(userID) {
	sendRequest("./api/block.php" , {
		"userID" : userID ,
	} , (d) => {}
	);
}

//-----------------------------------------------------------------------------

function fetchSelect(id) {
	return ([...document.getElementById(id).options].filter(option => option.selected).map(option => +option.value));
}

//-----------------------------------------------------------------------------

function clearSelect(id) {
	let select = document.getElementById(id);
	let L = select.options.length - 1;
   	for(let i = L; i >= 0; i--) {
    	select.remove(i);
   	}
}

//-----------------------------------------------------------------------------

function fillSelectUsers(users) {
	let select = document.getElementById("users");
	users      = users["data"]["users"];
	for(let property in users) {
		let opt = document.createElement("option");
    	opt.value     = users[property]["id"];
    	opt.innerHTML = users[property]["username"] + " ( ID:" + users[property]["id"] + " )";
    	select.appendChild(opt);
    }
}

//-----------------------------------------------------------------------------

function fillSelectComments(comments) {
	let select = document.getElementById("comments");
	comments.forEach((item) => {
		let opt   = document.createElement("option");
    	opt.value = item["id"];
        opt.innerHTML = 
        			 "User: ( " + item["username"] + " ) At: ( " +
					 item["comment_date"] + " ) Comment: ( " +
					 item["comment"] + " ) On Post: ( " +
					 item["title"] + " )";

    	select.appendChild(opt);
	})
}

//-----------------------------------------------------------------------------


function removeUsers() {
	if(!confirmDelete("Are you sure to delete this user(s) ?")) return;
	
	sendRequest("./api/removeUser.php" , {
		"userID" : fetchSelect("users")		
	} , (d) => {
		if(d["state"] !== 0) return;
		clearSelect("users");
		fillSelectUsers(d);
	})
}

//-----------------------------------------------------------------------------

function removeComments() {
	if(!confirmDelete("Are you sure to delete this comment(s) ?")) return;
	sendRequest("./api/removeComments.php" , {
		"commentsID" : fetchSelect("comments")
	} , (d) => {
		if(d["state"] !== 0) return;
		clearSelect("comments");
		fillSelectComments(d["data"]);
	});

}

//-----------------------------------------------------------------------------

function toggleCommentLike(commentID) {
	sendRequest("./api/likeComment.php" , {
		"commentID" : commentID
	} , (d) => {
		if(d["state"] !== 0) return;
		document.getElementById("commentLikes" + commentID).innerText = d["data"]["total"] + " Likes"; 
		document.getElementById("likeCommentBtn" + commentID).innerText = (d["data"]["action"] === -1) ? "Like" : "Dislike"; 
	});
}

//-----------------------------------------------------------------------------

function toggleFollowing(followingID) {
	sendRequest("./api/followUser.php" , {
		"followingID" : followingID
	} , (d) => {
		if(d["state"] !== 0) return;
		document.getElementById("followBtn").innerText = (d["data"] === -1) ? "Follow" : "UnFollow";
	});
}
