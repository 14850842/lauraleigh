<?php
	/* Publications */
?>

<section id="features" class="publicationSection">
	<div class="container">
		<div class="publicationInfoSection">
			<?php get_template_part('templates/clients/publications','info' ); ?>
		</div>
		<div class="publicationGrid">
		<?php
			$args = array('post_type'=>'feature','feature-category'=>'publications','posts_per_page'=>6,'orderby'=>'menu_order');
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>

				<div class="row">

					<ul class="list-inline">
			
						<?php while ( $query->have_posts() ) { $query->the_post(); ?>
							
							<li>
								<div class="imageContainer">
									<?php the_post_thumbnail('medium',array('class'=>'img-responsive')); ?>
								</div>
								<div class="titleContainer">
									<h3><?php the_title(); ?></h3>
      								<small><span class="meta"><?php echo get_post_meta($post->ID,'_ppm_title_meta',true);?></span></small>
      							</div>
    						</li>
		    					
						<?php } ?>

					</ul>

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