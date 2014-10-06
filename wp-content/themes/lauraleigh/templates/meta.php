<?php

	/* Post Meta Information */

global $post;

?>

<div class="metaContainer">
	<time class="updated" datetime="<?php get_the_time('Y-m-j') ?>">
		<svg class="svg-icon shape-calendar">
		  	<use xlink:href="#shape-calendar"></use>
		</svg>		
		<?php echo get_the_time(get_option('date_format')) ?>
	</time>
	<span class="category">
		<svg class="svg-icon shape-category">
		  	<use xlink:href="#shape-category"></use>
		</svg>	
		<?php the_category(' ');?>
	</span>
</div>
