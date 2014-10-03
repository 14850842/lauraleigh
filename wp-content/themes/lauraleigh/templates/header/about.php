<?php

/* About Section Home Page */

?>
<section class="aboutSection">
	<div class="container">
		<?php
			$args = array('post_type'=>'page','pagename'=>'about');
			// The Query
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) { $the_query->the_post(); global $more; $more = 0; ?>
					<div class="textContainer">
						<?php the_content('More About Me'); //Add View More ?>
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