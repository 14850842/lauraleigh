<?php
	/* Testimonials */
?>

<section class="testimonialSection">
	<div class="container">
		<?php
			$args = array('post_type'=>'testimonial','testimonial-category'=>'clients','posts_per_page'=>-1,'orderby'=>'menu_order');
			// The Query
			$query = new WP_Query( $args );

			// The Loop
			if ( $query->have_posts() ) { ?>
				<div class="flexslider">
					<ul class="slides">
			
						<?php while ( $query->have_posts() ) { $query->the_post(); ?>
							
							<li>
      							<div id="quote-<?php echo $post->ID;?>" class="quoteContainer" itemprop="review" itemscope itemtype="http://schema.org/Review">
      								<blockquote class="testimonials-text" itemprop="reviewBody">
      									<?php the_content(); ?>
      								</blockquote>
      								<cite class="author" itemprop="author" itemscope itemtype="http://schema.org/Person">
      									<span itemprop="name">- <?php echo get_post_meta($post->ID,'_byline',true); ?></span>
      								</cite>
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
</section>