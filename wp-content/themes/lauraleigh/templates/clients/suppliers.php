<?php
	/* Suppliers */
?>

<section id="suppliers" class="suppliersSection">
	<div class="container">
		<div class="suppliersInfoSection">
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
					
					<ul class="list-inline">
						
						<?php while ( $query->have_posts() ) { $query->the_post(); ?>
						
							<li class="supplier">
								<div class="imageContainer">
									<?php the_post_thumbnail('medium',array('class'=>'img-responsive')); ?>
	      							<a class="overlay-link" href="<?php echo get_post_meta($post->ID,'url',true);?>"><h4 class="overlay-text"><span>Visit Website</span></h4></a>
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