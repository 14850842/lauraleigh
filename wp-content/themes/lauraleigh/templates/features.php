<?php
	/* Features */
?>

<section class="featuresection">
	<div class="container">
		<div class="publicationInfoSection">
			<div class="textContainer">
				<h2 class="sectionTitle">My Work has been Featured Here:</h2>
				<div class="titleDivider divider ss-style-roundedsplit"></div>
			</div>
		</div>
		<div class="publicationGrid">
		<?php
			$args = array('post_type'=>'feature','feature-category'=>'blog','posts_per_page'=>6,'orderby'=>'menu_order');
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>

				<ul class="list-inline">
			
						<?php while ( $query->have_posts() ) { $query->the_post(); ?>
							
							<li>
								<div class="imageContainer">
									<a href="<?php echo get_post_meta($post->ID,'_url',true);?>"><?php the_post_thumbnail('medium',array('class'=>'img-responsive')); ?></a>
								</div>
    						</li>
		    					
						<?php } ?>

				</ul>
			<?php } else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
		</div>
	</div>
</section>