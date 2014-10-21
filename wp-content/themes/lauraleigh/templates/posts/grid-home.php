<?php
global $exclude_post;
	/* Grid Post Template */
?>

<section class="postSection">
	<div class="container">
		<?php
			$args = array('post_type'=>'post','posts_per_page'=>6,'post__not_in'=>array($exclude_post));
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>
				<h2 class="sectionTitle text-center">Some of My Favourite Shoots</h2>
				<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
				
				<?php while ( $query->have_posts() ) { $query->the_post(); ?>
					<div class="postContainer">
						<?php get_template_part('templates/posts/post','layout'); ?>
						<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
					</div>
				<?php }
			} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
	</div>
</section>