<?php
	function getTagsSelect($all , $selectedIDs) {
		global $connection;
?>
    <select name="tag[]" multiple size="4" class="form-control form-select-lg">
        <option value="*" disabled selected hidden>Select Tags</option>
        <?php echo ($all) ? '<option value="*">All</option>' : "" ?>

    <?php
        $tags = $connection->getTags();
        if($tags["state"] === 0) {
            foreach ($tags["data"] as $tag) {
    ?>

        <option <?php echo (in_array($tag["id"] , $selectedIDs)) ? "selected" : ""; ?> value="<?php echo $tag["id"]; ?>"><?php echo $tag["name"]; ?></option>
    
    <?php
            }
        }
    ?>

    </select>
<?php
}
?>