<?php
	/* Child Page Layout */
?>

<?php global $post; ?>
<?php global $more; $more = 0; ?>
<div class="pageContent">
	<div class="row">
		<div class="col-xs-6">
			<?php grid_page_images($post->ID); ?>
		</div>
		<div class="col-xs-6">
			<h2 class="pageTitle"><?php the_title(); ?></h2>
			<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
			<?php the_content(); ?>
			<?php get_page_link_info(); ?>
			
		</div>
	</div>
</div>