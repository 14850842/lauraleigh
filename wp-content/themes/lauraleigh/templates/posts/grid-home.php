<?php
global $exclude_post;
	/* Grid Post Template */
?>

<section class="postSection">
	<div class="container">
		<?php
			$paged = 1;
			if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
			if ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
			$paged = intval( $paged );

			// Exclude categories on the homepage.

			$query_args = array(
				  'post_type' => 'post', 
				  'paged' => $paged,
				  'posts_per_page'=>6,
				  'post__not_in'=>array($exclude_post)
				);

			query_posts( $query_args );

			// The Loop
			if ( have_posts() ) { ?>
				<h2 class="sectionTitle text-center">Some of My Favourite Shoots</h2>
				<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
				
				<?php while ( have_posts() ) { the_post(); ?>
					<div class="postContainer">
						<?php get_template_part('templates/posts/post','layout'); ?>
						<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
					</div>
				<?php } ?>

			<?php 


		} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
	</div>
</section>