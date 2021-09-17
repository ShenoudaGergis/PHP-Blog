<?php
    function getCategorySection() {
        global $connection;
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

<?php
    }
?>
