<?php
	/* Grid Page Template */
?>

<section class="pageSection">
	<div class="container">
		<?php
			$args = array('post_type'=>'page','post_parent'=>$post->ID,'posts_per_page'=>-1,'orderby'=>'menu_order');
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>
				
				<?php while ( $query->have_posts() ) { $query->the_post(); ?>
					<div class="pageContainer">
						<?php get_template_part('templates/clients/page','layout'); ?>
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