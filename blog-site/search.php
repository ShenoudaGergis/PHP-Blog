<?php
require_once "./initializer.php";
require_once "./validate.php";
require      "./templates/header.php";

$p = $_GET["page"];
$q = $_GET["query"];
$c = $_GET["category"];
$t = $_GET["tag"];

if(trim($q) === "") $q = null;

if(\validation\validate("number" , $c)) $c = intval($c);
else $c = null;

if(\validation\validate("number" , $p)) $p = intval($p);
else $p = 1;

if(\validation\validate("number" , $t)) $t = intval($t);
else $t = null;
?>

    <div class="heading-page header-text">
      <section class="page-heading">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="text-content">
                <h4>Search Posts</h4>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <section class="blog-posts grid-system">
      	<div class="container">
        	<div class="row">
          		<div class="col-lg-8">
            		<div class="all-blog-posts">
              			<div class="row">
              	 			<div class="col-lg-12">
                    			<ul class="page-numbers">


                    				
                    			</ul>
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
