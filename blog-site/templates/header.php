
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap" rel="stylesheet">

    <title>Sprints Blog Posts</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/vanillaSelectBox.css">
    <script src="assets/js/vanillaSelectBox.js"></script>


  </head>

  <body>

    <!-- ***** Preloader Start ***** -->
    
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- ***** Preloader End ***** -->

    <!-- Header -->
    <header class="">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index.php"><h2>Sprints Blog<em>.</em></h2></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home<?php if(isset($_SESSION["user"])) echo " ( " . $_SESSION["user"]["username"] . " ) "; ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>

                <?php 
                    if(!isset($_SESSION["user"])) {
                ?>

                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="register.php">Register</a>
                    </li>

                <?php
                    } else {
                ?>      
                    <?php
                        if($_SESSION["user"]["type"] == 1) { 
                    ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>            

                    <?php
                        }
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">My Posts</a>
                    </li>            

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                
                <?php
                    }
                ?>
                    
            </ul>
          </div>
        </div>
      </nav>
    </header>