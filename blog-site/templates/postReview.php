<?php

function getTemplate($post) {
	echo	sprintf('<div class="col-lg-6">
				<div class="blog-post">
					<div class="blog-thumb">
						<img src="./assets/images/banner-item-01.jpg" alt="">
					</div>
					<div class="down-content">
						<span> %s </span>
						<a href="post-details.php?id=%d">
							<h4> %s </h4>
						</a>
						<ul class="post-info">
							<li><a href="#"> %s </a></li>
							<li><a href="#"> %d comments </a></li>
							<li><a href="#"> %s </a></li>
						</ul>
						<p> %s </p>
						<div class="post-options">
							<div class="row">
								<div class="col-lg-12">
									<ul class="post-tags">
										<li><i class="fa fa-tags"></i></li>
										<li><a href="#">Best Templates</a>,</li>
										<li><a href="#">TemplateMo</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>' , 
			$post["category"] , 
			$post["id"] ,
			$post["post_title"] ,
			$post["user_name"] ,
			$post["comment_count"] ,
			$post["publish_date"] ,
			(strlen($s = $post["content"]) > 40) ? substr($s, 0, 37) . "..." : $s
		);
}