<?php
	/* Archive Post Layout */
?>

<?php global $post; ?>

<div class="postHeader">
	<div class="row">
		<div class="col-xs-6">
			<h2 class="postTitle"><?php the_title(); ?></h2>
		</div>
		<div class="col-xs-6">
			<?php get_template_part('templates/meta'); ?>
		</div>
	</div>
</div>

<figure class="imageContainer">
	<div class="fullSizeImage">
			<?php the_post_thumbnail('full',array('class'=>'img-responsive')); ?>
		</div>
		<div class="portraitSizeImages">
			<div class="row">
				<?php grid_post_images($post->ID); ?>
			</div>
		</div>
		<figcaption class="viewContainer">
		<p><?php _e('View The Full Shoot','ppm'); ?></p>
		</figcaption>		
		<a href="<?php the_permalink();?>" title="<?php the_title();?>">View more</a>
</figure>