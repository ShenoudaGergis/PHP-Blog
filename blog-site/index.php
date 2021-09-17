<?php
    require_once "./initializer.php";
    require      "./templates/header.php";
    require_once "./templates/banner.php";
    require_once "./templates/post-card.php";
    require_once "./templates/search-section.php";
    require_once "./templates/your-recent-posts-section.php";
    require_once "./templates/categories-section.php";
    require_once "./templates/tags-section.php";
    require_once "./utils/validate.php";

?>
    
    <?php
        getBanner("RECENT POST" , "OUR RECENT BLOG ENTRIES");
    ?>

<section class="blog-posts grid-system">
    <div class="container">
        <div class="row">
            
            <div class="col-md-8">
                <?php 
                    getPostsSection(null , "index.php");
                ?>
            </div>

            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="row">
                        
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
    </div>
</section>

    
<?php 
    require "./templates/footer.php";
?>

<!--                 <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-outline-primary btn-block">Create Post</button>                
                    </div>
                </div> -->