<?php
	/* Suppliers */
?>

<section class="suppliersSection">
	<div class="container">
		<div class="publicationInfoSection">
			<?php get_template_part('templates/clients/suppliers','info' ); ?>
		</div>
		<div class="suppliersGrid">
		<?php
			$args = array('post_type'=>'feature','feature-category'=>'suppliers','posts_per_page'=>6,'orderby'=>'menu_order');
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>

				<div class="row">
			
						<?php while ( $query->have_posts() ) { $query->the_post(); ?>
						
							<div class="col-xs-6 col-md-2 supplier">
								<div class="imageContainer">
									<?php the_post_thumbnail('medium',array('class'=>'img-responsive')); ?>

	      							<a class="overlay-link" href="#"><h4 class="overlay-text"><span>Visit Website</span></h4></a>
								</div>
								<div class="titleContainer">
									<h3><?php the_title(); ?></h3>
      								<span class="meta"><?php echo get_post_meta($post->ID,'_ppm_title_meta',true);?></span>
      							</div>
    						</div>
		    					
						<?php } ?>

				</div>
			<?php } else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
		</div>
	</div>
</section>