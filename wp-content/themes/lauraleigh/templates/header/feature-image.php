<?php
global $exclude_post;
/* Feature Image Home Page */

?>

<div class="featureImage">
		<?php
			$args = array('post_type'=>'post','tag'=>'home-feature','posts_per_page'=>1);
			// The Query
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) { $the_query->the_post(); ?>
					<?php $exclude_post = $post->ID; ?>
					<div class="imageContainer">
						<?php the_post_thumbnail('full',array('class'=>'img-responsive') ); ?>
					</div>
					
				<?php }
			} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
</div> 
