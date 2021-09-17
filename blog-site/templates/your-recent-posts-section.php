<?php
function getRecentPostsSection() {
    global $connection;
?>
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
                            for($i = 0;$i < 3;$i++) {
                                $post = $yourPosts["data"][$i];
                    ?>
                    <li>
                        <a href=<?php echo "./post-details.php?id=" . $post["id"]; ?>>
                            <h5><?php echo $post["post_title"]; ?></h5>
                            <i class="badge rounded-pill bg-light text-dark"><?php echo date('M-j-Y',strtotime($post["publish_date"]));?></i>
                        </a>
                    </li>
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
