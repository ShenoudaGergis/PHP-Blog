<?php
    function getTagsSection() {
        global $connection;
?>
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

<?php
    }
?>