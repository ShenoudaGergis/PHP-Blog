<?php
function getBanner($title , $subTitle) {
?>

<div class="heading-page header-text">
    <section class="page-heading">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-content">
                        <h4><?php echo $title; ?></h4>
                        <h2><?php echo $subTitle; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
}
?>