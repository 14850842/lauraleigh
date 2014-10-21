<?php
global $post;

/* About Section Home Page */

?>
<section class="aboutSection">
	<div class="container">
		<div class="textContainer">
			<?php
				echo wpautop(get_post_meta( $post->ID, '_ppm_about_text', true ));
			?>
			<?php $page = get_page_by_title('About Me'); ?>
			<a class="readmore" href="<?php echo get_permalink($page->ID);?>">More About Me <i class="fa fa-caret-right"></i></a>
		</div>
	</div>
</section>