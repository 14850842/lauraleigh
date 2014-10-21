<?php get_header(); ?>
      
    
    <?php get_template_part( 'templates/posts/feature','image' ); ?>

    <div class="divider">  
    	<div class="container">
			<div id="content" class="clearfix row">

				<div id="main" class="col-md-12 clearfix" role="main">

        		

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

							<?php get_template_part('templates/posts/single-post','header'); ?>

							<section class="entry-content single-content clearfix" itemprop="articleBody">
								<?php the_content(); ?>
								<?php wp_link_pages(
                                	array(

                                        'before' => '<div class="page-link"><span>' . __( 'Pages:', 'brew' ) . '</span>',
                                        'after' => '</div>'
                                	) 
                                ); ?>
							</section> <?php // end article section ?>


						</article> <?php // end article ?>

					<?php //get_template_part( 'author-info' ); ?>

					<?php if ( is_single() ) {?>
					  <div class="singlePostNav">
					    <div class="row">

					      <?php $trunc_limit = 30; ?>
					      <?php $prev = get_previous_post(); ?>
					      <div class="previous col-md-2">
					      	<?php if ($prev) : ?>
					      		<nav class="nav-roundslide nav-buttons">
									<a class="prev link" href="<?php echo get_permalink($prev); ?>">
										<span class="icon-wrap">
											<svg class="svg-icon shape-largearrow">
											  	<use xlink:href="#shape-largearrow"></use>
											</svg>
										</span>
										<h3><?php echo get_the_title($prev); ?></h3>
									</a>
								</nav>
							<?php endif; ?>
						     
					      </div>
					      <div class="col-md-offset-1 col-md-6 social text-center">
					      	<?php woo_story_sharing(); ?>
					      </div>
					      <div class="col-md-offset-1 col-md-2 next">
					      	<?php $next = get_next_post(); ?>
					      	<?php if ($next) : ?>
						      	<nav class="nav-roundslide nav-buttons">
									<a class="next link" href="<?php echo get_permalink($next); ?>">
										<span class="icon-wrap">
											<svg class="svg-icon shape-largearrow">
											  	<use xlink:href="#shape-largearrow"></use>
											</svg>
										</span>
										<h3><?php echo get_the_title($next); ?></h3>
									</a>
								</nav>
							<?php endif; ?>
					      </div>

					    </div>
					  </div><!-- /#single-post-nav -->
					<?php } ?>

          <?php comments_template(); ?>

					<?php endwhile; ?>

					<?php else : ?>

						<article id="post-not-found" class="hentry clearfix">
								<header class="article-header">
									<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
								</header>
								<section class="entry-content">
									<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
								</section>
								<footer class="article-footer">
										<p><?php _e( 'This is the error message in the single.php template.', 'bonestheme' ); ?></p>
								</footer>
						</article>

					<?php endif; ?>

				</div> <?php // end #main ?>

			</div> <?php // end #content ?>
		</div>

    </div> <?php // end ./container ?>

<?php get_footer(); ?>
