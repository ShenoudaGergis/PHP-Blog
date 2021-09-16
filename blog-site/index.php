<?php
    require_once "./initializer.php";
    require "./templates/header.php";
    require "./templates/postReview.php";
    require_once "validate.php";

?>
    <!-- Page Content -->
    <!-- Banner Starts Here -->
    <div class="heading-page header-text">
      <section class="page-heading">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="text-content">
                <h4>Recent Posts</h4>
                <h2>Our Recent Blog Entries</h2>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    
    <!-- Banner Ends Here -->

    <section class="blog-posts grid-system">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="all-blog-posts">
              <div class="row">

                <?php

                    if( isset($_GET["page"]) && 
                        \validation\validate("number" , $_GET["page"])
                    ) {
                        $v = intval($_GET["page"]);
                        if($v === 0) $page = 1;
                        else $page = $v;
                    } else {
                        $page = 1;
                    }

                    $ppp    = 2;
                    $from   = ($page - 1) * $ppp;
                    $result = $connection->getPost($from , $ppp);
                    if($result["state"] === 0) $posts = $result["data"];
                    else $posts = [];

                    foreach ($posts as $key => $value) {
                        getTemplate($value);
                    }

                ?>

                <div class="col-lg-12">
                    <ul class="page-numbers">
                        <?php
                            if(count($posts) !== 0) {

                                if($page === 2) {
                                    echo '<li><a href="./index.php?page=1"><<</a></li>';
                                    echo '<li><a href="./index.php?page=1">1 </a></li>';
                                }
                                if($page > 2) {
                                    echo sprintf('<li><a href="./index.php?page=%d"><<</a></li>' , $page - 1);
                                    echo sprintf('<li><a href="./index.php?page=%d">%d</a></li>' , $page - 2 , $page - 2);
                                    echo sprintf('<li><a href="./index.php?page=%d">%d</a></li>' , $page - 1 , $page - 1);
                                }
                                echo sprintf('<li class="active"><a href="#">%d</a></li>' , $page);

                                $result = $connection->getPost($from + $ppp * 1 , $ppp);
                                if($result["state"] === 0) $posts = $result["data"];
                                else $posts = [];
                                if(count($posts) !== 0) {
                                    echo sprintf('<li><a href="./index.php?page=%d">%d</a></li>' , $page + 1 , $page + 1);
                                    $result = $connection->getPost($from + $ppp * 2 , $ppp);
                                    if($result["state"] === 0) $posts = $result["data"];
                                    else $posts = [];
                                    if(count($posts) !== 0) {
                                        echo sprintf('<li><a href="./index.php?page=%d">%d</a></li>' , $page + 2 , $page + 2);
                                        echo sprintf('<li><a href="./index.php?page=%d">>></a></li>' , $page + 1);
                                    } else {
                                        echo sprintf('<li><a href="./index.php?page=%d">>></a></li>' , $page + 1);
                                    }
                                }


                            } else if($page !== 1) {
                        ?>
                            <script type="text/javascript">
                                window.location.href = "./index.php?page=1";
                            </script>
                        <?php
                            }
                        ?>
                    </ul>
                </div>              
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="sidebar">

              <div class="row">
                <div class="col-lg-12">
                <div class="sidebar-item search">
                    <form id="search_form" name="gs" method="GET" action="./search.php">
                        <div class="row">
                            <div class="col-lg-12" style="padding-bottom: 20px">
                                <input type="text" name="query" class="searchText" placeholder="type to search..." autocomplete="on">                            
                            </div>

                            <div class="col-lg-4">
                                <label><h6>Category </h6></label>                            
                            </div>
                            <div class="col-lg-8" style="padding-bottom: 20px">                                
                                <select name="category" class="form-control form-control-sm">
                                    <option value="*">All</option>
                                    <?php
                                        $categories = $connection->getCategories();
                                        if($categories["state"] === 0) {
                                            foreach ($categories["data"] as $category) {
                                    ?>

                                    <option value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>

                                    <?php
                                            }
                                        }
                                    ?>

                                </select>                       
                            </div>

                            <div class="col-lg-4">
                                <label><h6>Tag </h6></label>                            
                            </div>
                            <div class="col-lg-8">                                
                                <select name="tag" class="form-control form-control-sm">
                                    <option value="*">All</option>
                                    <?php
                                        $tags = $connection->getTags();
                                        if($tags["state"] === 0) {
                                            foreach ($tags["data"] as $tag) {
                                    ?>

                                    <option value="<?php echo $tag["id"]; ?>"><?php echo $tag["name"]; ?></option>

                                    <?php
                                            }
                                        }
                                    ?>
                                </select>                       
                            </div>
                        </div>
                    </form>
                </div>
                
                
                <?php
                    if(isset($_SESSION["user"])) {
                ?>         

                <br />
                <hr />
                <br />

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-outline-primary btn-block">Create Post</button>                
                    </div>
                </div>
                </div>
                <div class="col-lg-12">
                  <div class="sidebar-item recent-posts">
                    <div class="sidebar-heading">
                      <h2>Your Recent Posts</h2>
                    </div>
                    <div class="content">
                      <ul>
                        <?php 
                            $yourPosts = $connection->getPost($_SESSION["user"]["id"] , 0 , 6);
                            if($yourPosts["state"] === 0) {
                                foreach ($yourPosts["data"] as $post) {
                        ?>
                                <li><a href=<?php echo "./post-details.php?id=" . $post["id"]; ?>>
                                  <h5><?php echo $post["post_title"]; ?></h5>
                                  <span><?php echo $post["publish_date"]; ?></span>
                                </a></li>
                        <?php 
                                }
                            }
                        ?>
                      </ul>
                    </div>
                  </div>
                </div>

                <?php
                    }
                ?>  

                <div class="col-lg-12">
                  <div class="sidebar-item categories">
                    <div class="sidebar-heading">
                      <h2>Categories</h2>
                    </div>
                    <div class="content">
                      <ul>
                        <?php 
                            $categories = $connection->getCategories();
                            print_r($categoryies);
                            if($categories["state"] == 0) {
                                foreach ($categories["data"] as $category) {
                        ?>

                        <li><a href="./search.php?category=<?php echo $category["id"]; ?>">- <?php echo $category["name"]; ?></a></li>
                        <?php
                                }
                            }
                        ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="sidebar-item tags">
                    <div class="sidebar-heading">
                      <h2>Tag Clouds</h2>
                    </div>
                    <div class="content">
                      <ul>
                        <?php 
                            $tags = $connection->getTags();
                            if($tags["state"] == 0) {
                                foreach ($tags["data"] as $tag) {
                        ?>

                        <li><a href="./search.php?tag=<?php echo $tag["id"]; ?>"><?php echo $tag["name"]; ?></a></li>
                        
                        <?php
                                }
                            }
                        ?>
                      </ul>
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