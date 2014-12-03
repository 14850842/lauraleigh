<?php
// Fix for the WordPress 3.0 "paged" bug.
$paged = 1;
if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
if ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
$paged = intval( $paged );

// Exclude categories on the homepage.

$query_args = array(
		  'post_type' => 'post', 
		  'paged' => $paged
		);

query_posts( $query_args );
?>

<div id="wrapper" class="container">
	<div id="content" class="clearfix row">

		<div id="main" class="col-md-12 clearfix" role="main">

				<h2 class="sectionTitle text-center"><?php the_title();?></h2>
				<div class="titleDivider divider ss-style-roundedsplit"></div>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('item clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						<div class="postContainer">
							<?php get_template_part('templates/posts/post','layout'); ?>
							<div class="titleDivider divider ss-style-roundedsplit"></div>
						</div>

					</article> <?php // end article ?>

				<?php //get_template_part( 'author-info' ); ?>

				<?php if ( is_single() ) {?>
				  <?php get_template_part('templates/post','nav' ); ?>
				<?php } ?>

				<?php endwhile; ?>

				<nav class="wp-prev-next hide">
					<ul class="clearfix">
						<li class="prev-link"><?php next_posts_link( __( '&laquo; Older Entries', 'bonestheme' )) ?></li>
					</ul>
				</nav>

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
</div> <?php // end #wrapper ?>




