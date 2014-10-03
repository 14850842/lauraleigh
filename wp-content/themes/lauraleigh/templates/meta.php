<?php

	/* Post Meta Information */

global $post;

?>

<div class="metaContainer">
	<time class="updated" datetime="<?php get_the_time('Y-m-j') ?>"><span class="fa fa-calendar"></span>	<?php echo get_the_time(get_option('date_format')) ?></time>
	<span class="category"><span class="fa fa-list"></span>	<?php the_category(' ');?></span>
</div>
