<?php
	/* Child Page Layout */
?>

<?php global $post; ?>
<?php global $more; $more = 0; ?>
<div class="pageContent">
	<div class="row">
		<div class="col-md-12">
			<h2 class="pageTitle"><?php the_title(); ?></h2>
			<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
		</div>
		<div class="col-sm-6">
			<div class="row">
				<?php grid_page_images($post->ID); ?>
			</div>
		</div>
		<div class="col-sm-6">
			<?php the_content(); ?>
			<?php get_page_link_info(); ?>
		</div>
	</div>
</div>