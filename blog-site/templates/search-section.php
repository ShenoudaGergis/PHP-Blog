<?php 
    function getSearchSection() {
        global $connection;
?>
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
                <div class="col-lg-12" style="padding-top: 20px;">
                    <button class="btn btn-block btn-md btn-outline-primary" type="submit">SEARCH</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
}
?>