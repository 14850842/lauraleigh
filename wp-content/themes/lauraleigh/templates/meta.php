<?php

	/* Post Meta Information */

global $post;

?>

<div class="metaContainer">
	<time class="updated" datetime="<?php get_the_time('Y-m-j') ?>">
		<svg class="svg-icon shape-calendar">
		  	<use xlink:href="#shape-calendar"></use>
		</svg>		
		<?php echo get_the_time(get_option('date_format')) ?>
	</time>
			
	<span class="location">
		<?php 
		$location = wp_get_post_terms($post->ID, 'shoot_location');
		if(!empty($location)){ ?>
		<svg class="svg-icon shape-mappin">
		  	<use xlink:href="#shape-mappin"></use>
		</svg>
		<?php 
		$term_link = get_term_link( $location[0] );
   
		    // If there was an error, continue to the next term.
		    if ( is_wp_error( $term_link ) ) {
		        continue;
		    }
		    // We successfully got a link. Print it out.
		    echo '<a href="' . esc_url( $term_link ) . '">' . $location[0]->name . '</a>';

		}?>
	</span>

	<span class="category">
		<svg class="svg-icon shape-category">
		  	<use xlink:href="#shape-category"></use>
		</svg>
		<?php 
		$category = get_the_category(); 
		if($category[0]){
		echo '<a href="'.get_category_link($category[0]->term_id ).'">'.$category[0]->cat_name.'</a>';
		}
		?>
	</span>
</div>
