<?php
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'the_content', 'ppm_giveaway_engine_content' );
/**
 * Display team member data on single / archive pages
 * @since 1.4.0
 * @return  $content the post content
 */
function ppm_giveaway_engine_content( $content ) {
	global $post;

	if ( 'giveaway' == get_post_type()) {
		$giveaway_start_date 	= esc_attr( get_post_meta( $post->ID, '_option_start_date', true ) );
		$giveaway_end_date  = esc_attr( get_post_meta( $post->ID, '_option_end_date', true ) );
		$giveaway_closed  = esc_attr( get_post_meta( $post->ID, '_option_close_giveaway', true ) );
		$giveaway_tnc = esc_attr( get_post_meta( $post->ID, '_option_tnc_url', true ) );
		$giveaway_entrant_name = esc_attr( get_post_meta( $post->ID, '_option_entrant_names', true ) );
		$giveaway_entrant_email = esc_attr( get_post_meta( $post->ID, '_option_entrant_emails', true ) );

		if (!($giveaway_closed == true)) {

			$giveaway_form 	= '';
			$giveaway_content		= '';
			$member_fields 			= '';
			$author 				= '';


			$giveaway_content = '<div class="giveaway-container">';
			$giveaway_content .= '<h2 class="giveaway-title title">Enter the Giveaway</h2>';
			$giveaway_content .= '<div id="giveaway-confirmation" class="giveaway-entry-confirmation"></div>';
			// To Do Short Desc, View More Info, TNC, Dates
			$giveaway_form .= '<form id="giveaway-form" action="'.get_permalink().'" method="POST">';
			$giveaway_form .= '<ul class="giveaway-fields list-unstyled">';
			
			if (isset( $giveaway_entrant_name ) && ( false != $giveaway_entrant_name )) {
				$giveaway_form .= '<li><label for="entrant_name">Name</label><input type="text" class="giveaway-input" name="giveaway_entrant_name" id="giveaway_entrant_name"/></li>';
			} 

			if (isset( $giveaway_entrant_email ) && ( false != $giveaway_entrant_email )) {
				$giveaway_form .= '<li><label for="entrant_name">Email</label><input type="text" class="giveaway-input" name="giveaway_entrant_email" id="giveaway_entrant_email"/></li>';
			} 
			
			$giveaway_form .= '</ul>';
			$giveaway_form .= wp_nonce_field('ppm_giveaway_entry','ppm_giveaway_entry_nonce',true,false);
			$giveaway_form .= '<input type="hidden" name="action" class="giveaway-submit button" value="ppm_giveaway_entry"/>';
			$giveaway_form .= '<input type="hidden" name="giveaway_id" class="giveaway-submit button" value="'.get_the_id().'"/>';
			$giveaway_form .= '<input type="submit" class="giveaway-submit button" value="Enter Giveaway"/>';

			$giveaway_form .= '</form>';

			$giveaway_content .= $giveaway_form;

			$giveaway_content .= '<p>To view the competition terms and conditions <a href="'.$giveaway_tnc.'">click here</a></p>';
			
			$giveaway_content .= '</div>';

			$giveaway_content .= '<script id="confirmationTmpl" type="text/x-jsrender">
									<div class="giveaway-entry-confirmation">
										<br>
										<h4 class="title">Increase your chance of winning by earning more entries:</h4>
										<br>
										<div class="social-media-entry entry-facebook-like">
											<h4><i class="fa fa-facebook"></i> Like Us on Facebook</h4>
											<div class="fb-like-box facebook_page" data-type="facebook_url" data-href="{{:data.facebook_url}}" data-width="292" data-show-faces="false" data-header="false" data-stream="false" data-show-border="false"></div>
										</div>
										<div class="social-media-entry entry-twitter-follow">
											<h4><i class="fa fa-twitter"></i> Follow Us on Twitter</h4>
											<a href="{{:data.twitter_url}}" data-type="twitter_url" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @{{:data.twitter_handle}}</a>
										</div>
										<div class="social-media-entry entry-twitter-tweet">
											<h4><i class="fa fa-facebook"></i> Like "Title Here" on Facebook</h4>
											<p>Like the giveaway on Facebook, to earn an extra free entry.</p>
											<div class="fb-like facebook_post" data-type="facebook_like" data-href="{{:data.url}}" data-width="450" data-layout="button_count" data-show-faces="false" data-send="false"></div>
										</div>
										<div class="social-media-entry entry-twitter-tweet">
											<h4><i class="fa fa-twitter"></i> Tweet About "Title Here"</h4>
											<p>If you Tweet about the competition, you are entitled to a extra free entry. You tweet here:</p>  
								            <a href="https://twitter.com/share" data-type="twitter_share" class="twitter-share-button" data-url="{{:data.url}}" data-text="{{:data.tweet_text}}" data-via="{{:data.twitter_handle}}">Tweet</a>    
								        </div>
								    </div>
								  </script>';



			return $content . $giveaway_content;
		}
		else {
			$giveaway_content = '<div class="giveaway-container giveaway-entered">';
			$giveaway_content .= '<h2 class="title giveaway-title">The giveaway has been closed.</h2>';
			$giveaway_content .= '</div>';
			return $content . $giveaway_content;
		}

	} else {

		return $content;

	}
}
