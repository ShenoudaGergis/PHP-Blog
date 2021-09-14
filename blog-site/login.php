<?php 
    require_once "./initializer.php";
    require_once "./validate.php";
    require "./templates/header.php";


    $inputs = [];
    if(isset($_REQUEST["email"]))    $inputs["email"]    = $_REQUEST["email"]; 
    if(isset($_REQUEST["password"])) $inputs["password"] = $_REQUEST["password"];

    if(count($inputs) !== 0) {
        $errors = [];
        foreach ($inputs as $key => $value) {
            if(!\validation\validate($key , $value)) array_push($errors , $key);
        }

        if(count($errors) === 0) {
            $out = $connection->userLogin($inputs["email"] , $inputs["password"]);
            if($out["state"] === -1) {

            }
            if(count($out["data"]) === 0) {
                $noUser = true;
            }
            else {
                $data = $out["data"];
                setcookie("blog" , $data["id"] , time() + 100 , "/blog-site");
                $_SESSION["user"] = $data;
                header("Location: ./index.php");
                die();
            }
        }
    } 

?>

<section class="contact-us" style="padding: 100px;">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="down-contact">
          <div class="row">
            <div class="col-lg-12">
              <div class="sidebar-item contact-form">
                <div class="sidebar-heading">
                  <h2>Login</h2>
                </div>
                <div class="content">
                    <form id="contact" action="./login.php" method="post">
                        <div class="row">
                            <div class="col-md-9">
                                <fieldset>
                                    <input name="email" type="email" placeholder="Your Email" required=""
                                        value = <?php
                                            if(isset($inputs["email"])) {
                                                echo sprintf('"%s"' , $inputs["email"]);
                                            }
                                        ?> 
                                    >
                                </fieldset>
                            </div>
                            <?php 
                                if(isset($errors) && in_array("email", $errors)) {
                                    echo '
                                    <div class="col-md-3 alert alert-danger" role="alert">
                                        Invalid Email
                                    </div>
                                    ';
                                }
                            ?>
                            <div class="col-md-9">
                                <fieldset>
                                    <input name="password" type="password" placeholder="Your Password" required="">
                                </fieldset>
                            </div>
                            <?php
                                if(isset($errors) && in_array("password", $errors)) {
                                echo '
                                    <div class="col-md-3 alert alert-danger" role="alert">
                                        Invalid password
                                    </div>
                                    ';
                                }
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <button class="btn btn-success">Login</button>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <br />
                                    <?php
                                        if(isset($noUser) && $noUser) {
                                            echo '
                                                <div class="alert alert-danger" role="alert">
                                                    No user found
                                                </div>
                                            ';
                                        }
                                    ?>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>      
    </div>
  </div>
</section>


<?php 
    require "./templates/footer.php";
?>