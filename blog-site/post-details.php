<?php
    require_once "./initializer.php";
    require      "./templates/header.php";
    require_once "./templates/banner.php";
    require_once "./templates/search-section.php";
    require_once "./templates/your-recent-posts-section.php";
    require_once "./templates/categories-section.php";
    require_once "./templates/tags-section.php";
    require_once "./utils/validate.php";

    if(isset($_GET) && 
        array_key_exists("id" , $_GET) &&
        \validation\validate("number" , $_GET["id"])) {

        $result = $connection->getPost($_GET["id"]);

        if(($result["state"] === 0) && (count($result["data"]) >= 1)) {
            $post = $result["data"][0];
        } else {
            header("Location: ./index.php");
            die();            
        }

    } else {
        header("Location: ./index.php");
        die();
    }
?>

    <?php
        getBanner("POST DETAILS" , "SINGLE BLOG POST");
    ?>

<section class="blog-posts grid-system">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="all-blog-posts">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog-post">
                                <div class="blog-thumb">
                                    <img src="assets/images/blog-post-02.jpg" alt="">
                                </div>
                                <div class="down-content">
                                    <span><?php echo $post["category_name"]; ?></span>
                                    <h4><?php echo $post["post_title"]; ?></h4>
                                    <ul class="post-info">
                                        <li><i class="badge rounded-pill bg-primary text-light"><?php echo $post["user_name"]; ?></i></li>
                                        <li><i class="badge rounded-pill bg-secondary text-light"><?php echo count($post["comments"]); ?> comments</i></li>
                                        <li><i class="badge rounded-pill bg-warning text-dark"><?php echo date('M-j-Y',strtotime($post["publish_date"]));?></i></li>
                                    </ul>
                                    <p>
                                        <?php echo $post["content"]; ?>
                                    </p>
                                    <div class="post-options">
                                        <div class="row">
                                            <div class="col-6">
                                                <ul class="post-tags">
                                                    <li>
                                                        <i class="fa fa-tags"></i>
                                                    </li>
                                                    <?php
                                                        foreach ($post["tags"] as $tag) {
                                                    ?>
                                                        <li><?php echo $tag; ?></li>
                                                    <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="col-6">
                                                <ul class="post-share">
                                                    <li><i class="fa fa-share-alt"></i></li>
                                                    <li><a href="#">Facebook</a>,</li>
                                                    <li><a href="#"> Twitter</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="col-lg-12">
                            <div class="sidebar-item comments">
                                <div class="sidebar-heading">
                                    <h2><?php echo count($post["comments"]);?> Comments</h2>
                                </div>
                                <div class="content">
                                    <ul>    

                                    <?php 
                                        foreach ($post["comments"] as $comment) {
                                    ?>
                                
                                    <li>
                                        <div class="author-thumb">
                                            <img src="assets/images/comment-author-03.jpg" alt="">
                                        </div>
                                        <div class="right-content">
                                            <h4>
                                                <?php echo $comment["username"]; ?>
                                                <span>
                                                    <?php echo $comment["comment_date"] ?>
                                                </span>
                                            </h4>
                                            <p>
                                                <?php echo $comment["comment"]; ?>
                                            </p>
                                        </div>
                                    </li>
                                
                                <?php
                                    }
                                ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="sidebar-item submit-comment">
                                <div class="sidebar-heading">
                                    <h2>Your comment</h2>
                                </div>
                                <div class="content">
                                    <form id="comment" action="sendComment.php" method="post">
                                        <div class="row">
                                            <input type="hidden" name="id" value="<?php echo $post["id"]; ?>">
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <textarea name="message" rows="6" id="message" placeholder="Type your comment" required=""></textarea>
                                                </fieldset>
                                            </div>
                                            <div class="col-lg-12">
                                                <fieldset>
                                                    <button type="submit" id="form-submit" class="main-button">Submit</button>
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
            <div class="col-lg-4">
                <div class="sidebar">

                        <?php
                            getSearchSection();
                            if(isset($_SESSION["user"])) {
                                getRecentPostsSection();
                            }
                            getCategorySection();
                            getTagsSection();
                        ?> 

                </div>            
            </div>
        </div>
    </div>
</section>

    
<?php 
  require "./templates/footer.php";
?>