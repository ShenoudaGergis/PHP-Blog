<?php
	require_once "./utils/initializer.php";
	require_once "./utils/misc.php";
	require      "./templates/header.php";
	require_once "./templates/banner.php";
?>

<?php 
	if(isset($_SESSION["user"])) {
		header("Location: ./index.php");
		die();
	}
	getBanner("Register" , "CREATE NEW USER");
?>


<?php
	$errors = [];
	$phone  = true;

	if($_SERVER["REQUEST_METHOD"] === "POST") {
		$r = fetchParams([
			"name"      => ["string"   , null] ,
			"username"  => ["string"   , null] ,
			"password"  => ["password" , null] , 
			"cpassword" => ["password" , null] ,
			"email"     => ["email"    , null] ,
			"phone"     => ["number"   , null]

		]);

		if($r["name"] === null)      $errors[] = "name";
		if($r["username"] === null)  $errors[] = "username";
		if($r["password"] === null)  $errors[] = "password";
		if($r["cpassword"] === null) $errors[] = "cpassword";
		if($r["email"] === null)     $errors[] = "email";
		if($r["phone"] === null)     $phone    = false;


		if(count($errors) === 0) {
			if($r["password"] !== $r["cpassword"]) $errors[] = "confirm";
		}

		if(count($errors) === 0) {
			$result = $connection->userAdd($r["name"] , $r["username"] , $r["password"] , $r["email"] , ($phone) ? $r["phone"] : "NULL" , 0);
			if($result["state"] === -1) $errors["process"] = $result["error"];
			else {
				header("Location: ./login.php");
				die();
			}
		}
	}

?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<br />
			<h3 class="display-5">Create New User</h3>
		</div>
	</div>
	<br /><br />

	<div class="row">
		<form class="col-md-12" action="./register.php" method="post">
			<div class="row">
				<label class="col-md-1 form-label">Name</label>
				<div class="col-md-8">
					<input autofocus name="name" value="<?php echo (!empty($p = $_POST["name"]))? $p : ""; ?>" type="text" class="form-control" placeholder="Your Name...">
				</div>

	    		<?php
	    			if(in_array("name" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Name
		    	</div>
	    		<?php		
	    			}
	    		?>
			</div>

			<br />
			<div class="row">
				<label class="col-md-1 form-label">Username</label>
				<div class="col-md-8">
					<input name="username" value="<?php echo (!empty($p = $_POST["username"]))? $p : ""; ?>" type="text" class="form-control" placeholder="Your Username...">
				</div>

	    		<?php
	    			if(in_array("username" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Username
		    	</div>
	    		<?php		
	    			}
	    		?>
			</div>

			<br />
			<div class="row">
				<label class="col-md-1 form-label">Password</label>
				<div class="col-md-8">
					<input name="password" value="" type="password" class="form-control">
				</div>

	    		<?php
	    			if(in_array("password" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Password
		    	</div>
	    		<?php		
	    			}
	    		?>
			</div>

			<br />
			<div class="row">
				<label class="col-md-1 form-label">Confirm Password</label>
				<div class="col-md-8">
					<input name="cpassword" value="" type="password" class="form-control">
				</div>

	    		<?php
	    			if(in_array("cpassword" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Password
		    	</div>
	    		<?php		
	    			} else if(in_array("confirm" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Passwords don't match
		    	</div>
		    	<?php
		    		}
		    	?>
			</div>

			<br />
			<div class="row">
				<label class="col-md-1 form-label">Email</label>
				<div class="col-md-8">
					<input name="email" value="<?php echo (!empty($p = $_POST["email"]))? $p : ""; ?>" type="email" class="form-control" placeholder="Your Email...">
				</div>

	    		<?php
	    			if(in_array("email" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Email
		    	</div>
	    		<?php		
	    			}
	    		?>

			</div>

			<br />
			<div class="row">
				<label class="col-md-1 form-label">Phone</label>
				<div class="col-md-8">
					<input name="phone" value="<?php (!empty($p = $_POST["phone"]))? $p : ""; ?>" type="number" class="form-control" placeholder="Your Phone number...">
				</div>
<!-- 	    		<?php
	    			if(in_array("phone" , $errors)) {
	    		?>
				<div class="col-md-3 alert alert-danger " role="alert">
	    			Invalid Phone
		    	</div>
	    		<?php		
	    			}
	    		?> -->
			</div>

			<br />
			<div class="row">
				<button class="btn btn-primary btn-block">Sumbit</button>
			</div>
		</form>		
	</div>

	<?php
		if(array_key_exists("process" , $errors)) {
	?>
	<br />
	<div class="col-md-12 alert alert-danger " role="alert">
		Can't Add User: <?php echo $errors["process"];?>
	</div>
	<?php		
		}
	?>

</div>

<?php
	require "./templates/footer.php";
?>