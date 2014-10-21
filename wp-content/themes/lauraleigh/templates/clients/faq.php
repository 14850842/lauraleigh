<?php

/* FAQ */

?>

<section class="faqSection">
	<div class="container">
		<?php
			$args = array('post_type'=>'page','pagename'=>'faqs');
			// The Query
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				
				while ( $the_query->have_posts() ) { $the_query->the_post(); ?>
					<?php $child_of = $post->ID; ?>
					<div class="textContainer">
						<h2><?php the_title();?></h2>
						<div class="titleDivider divider ss-style-roundedsplit"></div>
					</div>
					
				<?php }
			} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>

		<?php


		$args = array( 'posts_per_page' => -1,'post_type'=>'page','post_parent'=>$child_of);

		$myposts = get_posts( $args );
		$tab_header = $tab_content = '';

		$tab_header .= '<ul class="nav nav-tabs" role="tablist">';
		$tab_content .=  '<div class="tab-content">';
		foreach ( $myposts as $post ) : setup_postdata( $post );
			$tab_header .= '<li><a href="#'.$post->post_name.'" role="tab" data-toggle="tab">'.get_the_title().'</a></li>';

			$faqs = get_post_meta($post->ID,'_ppm_faq_group',true); 

			$tab_content .= '<div class="tab-pane in fade" id="'.$post->post_name.'"><div class="faq-group" id="accordion'.$post->ID.'">';

			if (!empty($faqs)) {
				foreach ($faqs as $key => $faq) {
					$tab_content .=	'<h3 class="faq-title">
		        						<a data-toggle="collapse" data-parent="#accordion'.$post->ID.'" href="#collapse'.$post->ID.'-'.$key.'">
		        							'.$faq['title'].'
		        						</a>
		        					</h3>

			        				<div id="collapse'.$post->ID.'-'.$key.'" class="panel-collapse collapse">
			      						<div class="panel-body">
			      							'.$faq['description'].'
			      						</div>
			      					</div>';
				}	
			}

			$tab_content .= '</div></div>';
			
			  
		
		endforeach; 
		wp_reset_postdata();
		$tab_content .=  '</div>';
		$tab_header .= '</ul>';

		echo $tab_header;

		echo $tab_content;
		
		?>


		</ul>
	</div>
</section>

<script>
  jQuery(function () {
    jQuery('.nav-tabs a:first').tab('show')
  })
</script>