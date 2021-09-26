<?php
    require_once "./utils/initializer.php";
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
                    getPostsSection([$_SESSION["user"]["id"] , true] , "index.php"); //old was null for userID
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