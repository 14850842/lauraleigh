<?php

/* Feature Image Clients Page */

?>

<div class="featureImage">
		<?php
			// The Loop
			if ( have_posts() ) {
				
				while ( have_posts() ) { the_post(); ?>
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
