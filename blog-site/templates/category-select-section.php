<?php
    function getCategorySelect($all , $selectedID) {
        global $connection;
?>
    <select name="category" class="form-control form-select-lg">
        <option value="*" disabled selected hidden>Select Category</option>
        <?php echo ($all) ? '<option value="*">All</option>' : "" ?>

    <?php
        $categories = $connection->getCategories();
        if($categories["state"] === 0) {
            foreach ($categories["data"] as $category) {
    ?>
        <option <?php echo (intval($category["id"]) === $selectedID) ? "selected" : ""; ?> value="<?php echo $category["id"]; ?>"><?php echo $category["name"]; ?></option>
    <?php
            }
        }
    ?>
    </select> 
<?php
    }
?>