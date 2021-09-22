<?php
    require_once "./templates/category-select-section.php";
    require_once "./templates/tags-select-section.php";
?>

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

                    <?php
                        getCategorySelect(true);
                    ?>                                
                              
                </div>

                <div class="col-lg-4">
                    <label><h6>Tag </h6></label>                            
                </div>
                <div class="col-lg-8">                                

                    <?php
                        getTagsSelect(true);
                    ?>                                


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