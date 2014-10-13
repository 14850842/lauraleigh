<?php
	$args = array('post_type'=>'page','pagename'=>'suppliers-i-love');
	// The Query
	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		
		while ( $the_query->have_posts() ) { $the_query->the_post(); global $more; $more = 0; ?>
			<div class="textContainer">
				<h2 class="sectionTitle"><?php the_title(); ?></h2>
				<div class="titleDivider divider ss-style-roundedsplit"></div>
				<div class="supplierContent">
					<?php the_content(); ?>
				</div>
			</div>
		<?php }
	} else {
		// no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();
?>