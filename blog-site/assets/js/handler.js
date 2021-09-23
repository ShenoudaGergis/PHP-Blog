
function confirmDelete(id) {
	return confirm("Are you sure to delete this post ?");
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

function removePost(userID , postID , node) {
	if(!confirmDelete()) return;
	let row   = node.closest("tr");
	
	sendRequest("./api/removePost.php" , {
		"userID" : userID ,
		"postID" : postID		
	} , (d) => {
		if(d["state"] !== 0) return;
		row.remove();
	})
}