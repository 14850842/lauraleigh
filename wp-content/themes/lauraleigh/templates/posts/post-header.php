<?php
/*
	SinglePost Header
*/
?>

<header class="articleHeader text-center">
	<div class="titlewrap clearfix">
		<h1 class="postTitle entryTitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
	</div>
	<?php get_template_part('templates/meta'); ?>
	<div class="titleDivider divider ss-style-roundedsplit"></div>
</header> <?php // end article header ?>