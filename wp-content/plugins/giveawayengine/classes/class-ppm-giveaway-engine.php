<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * PPM Giveaway Engine Class
 *
 *  *
 * @package WordPress
 * @subpackage PPM_Giveaway_Engine
 * @category Plugin
 * @author Sergio Pellegrini
 * @since 1.0.0
 */
class PPM_Giveaway_Engine {
	private $dir;
	private $assets_dir;
	private $assets_url;
	private $token;
	public $version;
	private $file;

	/**
	 * Constructor function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->token = 'giveaway';

		$this->load_plugin_textdomain();

		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );

		

		//add_filter( 'plugin_action_links_our-team-by-woothemes/woothemes-our-team.php', array( $this, 'our_team_action_links' ) );

		add_action( 'init', array( $this, 'register_post_type' ) ); // Register the giveaway post type
		add_action( 'init', array($this,'ppm_giveaway_engine_entry_table'), 1 );
		add_action( 'switch_blog', array($this,'ppm_giveaway_engine_entry_table') );

		add_action( 'wp_enqueue_scripts', array($this,'ppm_giveaway_engine_scripts'), 999 );
		add_action( 'wp_ajax_ppm_giveaway_entry', array( $this, 'ppm_add_entry_callback' ) );
		add_action( 'wp_ajax_nopriv_ppm_giveaway_entry', array( $this, 'ppm_add_entry_callback' ) );

		add_action( 'wp_ajax_add_social', array( $this, 'ppm_social_entry_callback' ) );
		add_action( 'wp_ajax_nopriv_add_social', array( $this, 'ppm_social_entry_callback' ) );
		
		


		//add_action( 'init', array( $this, 'register_taxonomy' ) ); // No need for taxonomy at this stage
    	//add_action( 'load-post-new.php', array( $this, 'our_team_help_tab' ) ); // to do 
    	//add_action( 'load-post.php', array( $this, 'our_team_help_tab' ) ); // to do

		if ( is_admin() ) {
			global $pagenow;

			add_action( 'admin_menu', array( $this, 'meta_box_setup' ), 20 );

			add_action( 'admin_menu', array( $this, 'add_admin_menu' ),20 );
			
			add_action( 'save_post', array( $this, 'meta_box_save' ) ); 

			add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) ); //changed for giveaways

			add_action( 'admin_print_styles', array( $this, 'enqueue_admin_styles' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );
			

			if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && esc_attr( $_GET['post_type'] ) == $this->token ) {
				add_filter( 'manage_edit-' . $this->token . '_columns', array( $this, 'register_custom_column_headings' ), 10, 1 );
				add_action( 'manage_posts_custom_column', array( $this, 'register_custom_columns' ), 10, 2 );
			}

			//Ajax Entry Options 


			// Get users ajax callback
			add_action( 'wp_ajax_select_winner', array( $this, 'get_select_winner' ) );
			add_action( 'admin_footer',  array( $this, 'get_select_winner_javascript' ) );

		}

		add_action( 'after_setup_theme', array( $this, 'ensure_post_thumbnails_support' ) );
	} // End __construct()


	/**
	 * Register the admin entry screen.
	 *
	 * @access public
	 * @param string $token
	 * @param string 'Entry'
	 * @return to function callback
	 */
	function add_admin_menu() {

		add_submenu_page( 'edit.php?post_type='.$this->token, 'Giveaway Entries', 'Entries','manage_options','ppm-edit-entries',array(&$this, 'entry_interface'));

	}

	function entry_interface() {
		global $post;
		?>
			<div class="wrap">
				<h2>Giveaway Entries</h2>
				<?php if ( empty( $_POST['giveaway-entry-id'] ) ) : ?>
					<p><?php printf( __( "Select giveaway to view the submitted entries.", 'ppm-giveaway-engine' ) ); ?></p>
					<form method="post" action="">
						<?php wp_nonce_field('giveaway-entries') ?>
						<select id="ppm_giveaway_entry" name="giveaway-entry-id">
							<?php
								$args = array( 'posts_per_page' => -1,'post_type'=>$this->token);
								
								$giveaways = get_posts( $args );

								foreach ( $giveaways as $post ) : setup_postdata( $post ); ?>
									<option value="<?php echo $post->ID;?>"><?php echo get_the_title(); ?></option>
								<?php endforeach; 
								wp_reset_postdata();?>
						</select>
						<input type="submit" class="button" name="giveaway-entry" id="giveaway-entry" value="<?php _e( 'View Giveaways', 'ppm-giveaway-engine' ) ?>" />
					</form>

				<?php else : ?>
					<?php
					   global $wp_entry_table;
					   $wp_entry_table = new Entry_List_Table();
					   $wp_entry_table->prepare_items();
					  
					   $wp_entry_table->display();

					?>

				<?php endif; ?>	

			</div>
		<?php
	}

	/**
	 * Register the post type.
	 *
	 * @access public
	 * @param string $token
	 * @param string 'Team Member'
	 * @param string 'Our Team'
	 * @param array $supports
	 * @return void
	 */
	public function register_post_type () {
		$labels = array(
			'name' 					=> _x( 'Giveaways', 'post type general name', 'ppm-giveaway-engine' ),
			'singular_name' 		=> _x( 'Giveaway', 'post type singular name', 'ppm-giveaway-engine' ),
			'add_new' 				=> _x( 'Add New', 'giveaway', 'ppm-giveaway-engine' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'ppm-giveaway-engine' ), __( 'Giveaway', 'ppm-giveaway-engine' ) ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'ppm-giveaway-engine' ), __( 'Giveaway', 'ppm-giveaway-engine' ) ),
			'new_item' 				=> sprintf( __( 'New %s', 'ppm-giveaway-engine' ), __( 'Giveaway', 'ppm-giveaway-engine' ) ),
			'all_items' 			=> sprintf( __( 'All %s', 'ppm-giveaway-engine' ), __( 'Giveaways', 'ppm-giveaway-engine' ) ),
			'view_item' 			=> sprintf( __( 'View %s', 'ppm-giveaway-engine' ), __( 'Giveaway', 'ppm-giveaway-engine' ) ),
			'search_items' 			=> sprintf( __( 'Search %a', 'ppm-giveaway-engine' ), __( 'Giveaways', 'ppm-giveaway-engine' ) ),
			'not_found' 			=> sprintf( __( 'No %s Found', 'ppm-giveaway-engine' ), __( 'Giveaways', 'ppm-giveaway-engine' ) ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s Found In Trash', 'ppm-giveaway-engine' ), __( 'Giveaways', 'ppm-giveaway-engine' ) ),
			'parent_item_colon' 	=> '',
			'menu_name' 			=> __( 'Giveaways', 'ppm-giveaway-engine' )

		);

		$single_slug = apply_filters( 'ppm_giveaway_engine_single_slug', _x( 'giveaway', 'single post url slug', 'ppm-giveaway-engine' ) );
		$archive_slug = apply_filters( 'ppm_giveaway_engine_archive_slug', _x( 'giveaways', 'post archive url slug', 'ppm-giveaway-engine' ) );

		$args = array(
			'labels' 				=> $labels,
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui'			 	=> true,
			'show_in_menu' 			=> true,
			'query_var' 			=> true,
			'rewrite' 				=> array(
										'slug' 			=> $single_slug,
										'with_front' 	=> false
										),
			'capability_type' 		=> 'post',
			'has_archive' 			=> $archive_slug,
			'hierarchical' 			=> false,
			'supports' 				=> array(
										'title',
										'author',
										'editor',
										'thumbnail',
										'page-attributes'
										),
			'menu_position' 		=> 5,
			'menu_icon' 			=> 'dashicons-awards'
		);
		$args = apply_filters( 'ppm_giveaway_engine_post_type_args', $args );
		register_post_type( $this->token, (array) $args );
	} // End register_post_type()

	/**
	 * Register the "our-team-category" taxonomy.
	 * @access public
	 * @since  1.3.0
	 * @return void
	 */
	public function register_taxonomy () {
		$this->taxonomy_category = new Woothemes_Our_Team_Taxonomy(); // Leave arguments empty, to use the default arguments.
		$this->taxonomy_category->register();
	} // End register_taxonomy()

	/**
	 * Add custom columns for the "manage" screen of this post type.
	 *
	 * @access public
	 * @param string $column_name
	 * @param int $id
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_columns ( $column_name, $id ) {
		global $wpdb, $post;

		$meta = get_post_custom( $id );

		switch ( $column_name ) {

			case 'image':
				$value = '';

				$value = $this->get_image( $id, 40 );

				echo $value;
			break;

			default:
			break;

		}
	} // End register_custom_columns()

	/**
	 * Add custom column headings for the "manage" screen of this post type.
	 *
	 * @access public
	 * @param array $defaults
	 * @since  1.0.0
	 * @return void
	 */
	public function register_custom_column_headings ( $defaults ) {
		$new_columns 	= array( 'image' => __( 'Image', 'our-team-by-woothemes' ) );
		$last_item 		= '';

		if ( isset( $defaults['date'] ) ) { unset( $defaults['date'] ); }

		if ( count( $defaults ) > 2 ) {
			$last_item = array_slice( $defaults, -1 );

			array_pop( $defaults );
		}
		$defaults = array_merge( $defaults, $new_columns );

		if ( $last_item != '' ) {
			foreach ( $last_item as $k => $v ) {
				$defaults[$k] = $v;
				break;
			}
		}

		return $defaults;
	} // End register_custom_column_headings()

	/**
	 * Update messages for the post type admin.
	 * @since  1.0.0
	 * @param  array $messages Array of messages for all post types.
	 * @return array           Modified array.
	 */
	public function updated_messages ( $messages ) {
	  global $post, $post_ID;

	  $messages[$this->token] = array(
	    0 => '', // Unused. Messages start at index 1.
	    1 => sprintf( __( 'Giveaway updated. %sView Giveaway%s', 'ppm-giveaway-engine' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    2 => __( 'Custom field updated.', 'ppm-giveaway-engine' ),
	    3 => __( 'Custom field deleted.', 'ppm-giveaway-engine' ),
	    4 => __( 'Giveaway updated.', 'ppm-giveaway-engine' ),
	    /* translators: %s: date and time of the revision */
	    5 => isset($_GET['revision']) ? sprintf( __( 'Giveaway restored to revision from %s', 'ppm-giveaway-engine' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	    6 => sprintf( __( 'Giveaway published. %sView team member%s', 'ppm-giveaway-engine' ), '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', '</a>' ),
	    7 => __('Giveaway saved.'),
	    8 => sprintf( __( 'Giveaway submitted. %sPreview team member%s', 'ppm-giveaway-engine' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	    9 => sprintf( __( 'Giveaway scheduled for: %1$s. %2$sPreview giveway%3$s', 'ppm-giveaway-engine' ),
	      // translators: Publish box date format, see http://php.net/date
	      '<strong>' . date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink($post_ID) ) . '">', '</a>' ),
	    10 => sprintf( __( 'Giveaway draft updated. %sPreview giveaway%s', 'ppm-giveaway-engine' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', '</a>' ),
	  );

	  return $messages;
	} // End updated_messages()

	/**
	 * Setup the meta box.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function meta_box_setup () {
		add_meta_box( 'giveaway-data', __( 'Giveaway Details', 'ppm-giveaway-engine' ), array( $this, 'meta_box_content' ), $this->token, 'normal', 'high' );
	} // End meta_box_setup()

	/**
	 * The contents of our meta box.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function meta_box_content () {
		global $post_id;
		$fields = get_post_custom( $post_id ); // Get All Meta
		$field_data = $this->get_custom_fields_settings(); 

		$html = '';

		$html .= '<input type="hidden" name="ppm_' . $this->token . '_noonce" id="ppm_' . $this->token . '_noonce" value="' . wp_create_nonce( plugin_basename( $this->dir ) ) . '" />';

		if ( 0 < count( $field_data ) ) {
			$html .= '<table class="form-table">' . "\n";
			$html .= '<tbody>' . "\n";

			foreach ( $field_data as $k => $v ) {
				$data = $v['default'];
				if ( isset( $fields['_' . $k] ) && isset( $fields['_' . $k][0] ) ) {
					$data = $fields['_' . $k][0];
				}

				switch ( $v['type'] ) { // Make select option
					case 'hidden':
						$field = '<input name="' . esc_attr( $k ) . '" type="hidden" id="' . esc_attr( $k ) . '" value="' . esc_attr( $data ) . '" />';
						$html .= '<tr valign="top">' . $field . "\n";
						$html .= '<tr/>' . "\n";
						break;
					case 'checkbox':
						if ($data == true) {
							$checked = 'checked=checked';

						} else {
							$checked = '';
						}
						$field = '<input  '.esc_attr($checked).'  name="' . esc_attr( $k ) . '" type="checkbox" id="' . esc_attr( $k ) . '"  value="true" />';
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
					default:
						$field = '<input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />';
						$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td>' . $field . "\n";
						$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
						$html .= '</td><tr/>' . "\n";
						break;
				}

			}

			$html .= '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}

		echo $html;
	} // End meta_box_content()

	/**
	 * Save meta box fields.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param int $post_id
	 * @return void
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;

		// Verify
		if ( ( get_post_type() != $this->token ) || ! wp_verify_nonce( $_POST['ppm_' . $this->token . '_noonce'], plugin_basename( $this->dir ) ) ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$field_data = $this->get_custom_fields_settings();
		$fields = array_keys( $field_data );

		foreach ( $fields as $f ) {

			${$f} = strip_tags(trim($_POST[$f]));

			// Escape the URLs.
			if ( 'url' == $field_data[$f]['type'] ) {
				${$f} = esc_url( ${$f} );
			}

			if ( get_post_meta( $post_id, '_' . $f ) == '' ) {
				add_post_meta( $post_id, '_' . $f, ${$f}, true );
			} elseif( ${$f} != get_post_meta( $post_id, '_' . $f, true ) ) {
				update_post_meta( $post_id, '_' . $f, ${$f} );
			} elseif ( ${$f} == '' ) {
				delete_post_meta( $post_id, '_' . $f, get_post_meta( $post_id, '_' . $f, true ) );
			}
		}
	} // End meta_box_save()

	/**
	 * Customise the "Enter title here" text.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param string $title
	 * @return void
	 */
	public function enter_title_here ( $title ) {
		if ( get_post_type() == $this->token ) {
			$title = __( 'Enter the giveaway title here', 'ppm-giveaway-engine' );
		}
		return $title;
	} // End enter_title_here()

	/**
	 * Enqueue post type admin CSS.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function enqueue_admin_styles () {
		wp_register_style( 'ppm-admin-styles', $this->assets_url . 'css/admin.css', array(), '1.0.1' );
		wp_enqueue_style( 'ppm-admin-styles' );
	} // End enqueue_admin_styles()

	/**
	 * Enqueue front end javascript.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function ppm_giveaway_engine_scripts() {
	    if (!is_admin()) {

	    	wp_register_style( 'giveaway-styles', $this->assets_url . 'css/giveaway.css', array(), '1.0.1' );
			wp_enqueue_style( 'giveaway-styles' );

	    	wp_register_script( 'validation','//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js', array('jquery'), '1.0.0',true);
	        wp_register_script( 'ppm-giveaway-engine', $this->assets_url . 'js/giveawayengine.js', array('jquery'), '1.0.13',true);
	        wp_register_script( 'ppm-jsrender', $this->assets_url . 'js/jsrender.min.js', array('jquery'), '1.0.0',true);
	        	
	        wp_localize_script( 'ppm-giveaway-engine', 'giveawayOptions', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	       	wp_enqueue_script( 'validation');
	        wp_enqueue_script('ppm-jsrender');
	        wp_enqueue_script('ppm-giveaway-engine');
	        
	    }
	}

	/**
	 * Enqueue post type admin JavaScript.
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 */
	public function enqueue_admin_scripts () {
		// Might use select 2. This will be more for the stats engine but for now it is not used.
		//wp_enqueue_script('jquery-ui-autocomplete', null, array('jquery'), null, false);
	} // End enqueue_admin_styles()

	/**
	 * Get the settings for the custom fields.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_custom_fields_settings () {
		$fields = array();

		if ( apply_filters( 'ppm_giveaway_engine_close_giveaway', true ) ) {
			$fields['option_close_giveaway'] = array(
			    'name' 				=> __( 'Close Giveaway', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Check to close giveaway', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'checkbox',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_sponsor_name', true ) ) {
			$fields['sponsor_name'] = array(
			    'name' 				=> __( 'Sponsor Name', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter the sponsors name', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}


		if ( apply_filters( 'ppm_giveaway_engine_facebook_url', true ) ) {
			$fields['facebook_url'] = array(
			    'name' 				=> __( 'Facebook Page URL', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter a Facebook Page URL.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_twitter_url', true ) ) {
			$fields['twitter_url'] = array(
			    'name' 				=> __( 'Twitter URL', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter a Facebook Page URL.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_share_url', true ) ) {
			$fields['share_url'] = array(
			    'name' 				=> __( 'Share URL', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter the url you would like to entrants to share on social media. Default (if blank) giveaway url.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_twitter_text', true ) ) {
			$fields['twitter_text'] = array(
			    'name' 				=> __( 'Twitter Text', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter the tweet you would like to entrants to share on social media..', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_entrant_names', true ) ) {
			$fields['option_entrant_names'] = array(
			    'name' 				=> __( 'Collect Names', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Select whether you would like to collect entrants names.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_entrant_email', true ) ) {
			$fields['option_entrant_emails'] = array(
			    'name' 				=> __( 'Collect Emails', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Select whether you would like to collect entrants email addresses.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_start_date', true ) ) {
			$fields['option_start_date'] = array(
			    'name' 				=> __( 'Giveaway Start Date', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Select the starting date of the giveaway.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_end_date', true ) ) {
			$fields['option_end_date'] = array(
			    'name' 				=> __( 'Giveaway End Date', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Select the end date of the giveaway.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		if ( apply_filters( 'ppm_giveaway_engine_tnc_url', true ) ) {
			$fields['option_tnc_url'] = array(
			    'name' 				=> __( 'Terms and Conditions URL', 'ppm-giveaway-engine' ),
			    'description' 		=> sprintf( __( 'Enter the terms & conditions url.', 'ppm-giveaway-engine' ) ),
			    'type' 				=> 'text',
			    'default' 			=> '',
			    'section' 			=> 'info'
			);
		}

		return apply_filters( 'ppm_giveaway_engine_fields', $fields );
	} // End get_custom_fields_settings()

	/**
	 * Ajax callback to add entry.
	 * @param  data array from entry form submit.
	 * @since  1.0.0
	 * @return json Entry Details.
	 */
	public function ppm_add_entry_callback() {

		//check_ajax_referer( 'ppm_giveaway_entry', 'ppm_giveaway_entry_nonce' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$entry = array();
		$entry['user_name'] = isset($_POST['giveaway_entrant_name']) ? $_POST['giveaway_entrant_name'] : '';
		$entry['user_email'] = isset($_POST['giveaway_entrant_email']) ? $_POST['giveaway_entrant_email'] : '';
		$entry['referal_url'] = isset($_POST['_wp_http_referer']) ? $_POST['_wp_http_referer'] : '';
		$entry['giveaway_id'] = isset($_POST['giveaway_id']) ? $_POST['giveaway_id'] : '';
		$entry['facebook_url'] =  esc_attr( get_post_meta( $entry['giveaway_id'], '_facebook_url', true ) );
		$entry['twitter_url'] =  esc_attr( get_post_meta( $entry['giveaway_id'], '_twitter_url', true ) );
		$entry['sponsor'] =  esc_attr( get_post_meta( $entry['giveaway_id'], '_sponsor_name', true ) );
		$entry['title'] =  esc_attr( get_the_title( $entry['giveaway_id']));
		$entry['url'] =  get_permalink( $entry['giveaway_id'] );
		$entry['twitter_text'] = esc_attr( get_post_meta( $entry['giveaway_id'], '_twitter_text', true ) );
		$entry['twitter_handle'] = 'theprettyblog';

		$result = $this->add_entry($entry);


		wp_send_json_success($result);


		die();

	}

	public function ppm_social_entry_callback() {
		global $wpdb;

		header( 'Content-Type: application/json; charset=utf-8' );

        if (empty($entry_id))
            $entry_id = $_POST['entry_id'];

        if (empty($entry_id))
            return new WP_Error("missing_entry_id", __("Missing entry id", "ppm_giveaway_engine"));

        if (empty($type))
        	$type = $_POST['type'];

        $current_entry = $original_entry = self::get_entry($entry_id);

        $social_array = unserialize($current_entry[3]);

        switch ($type) {
        	case 'twitter_sponsor_url':
        		$social_array['entries']['twitter_sponsor_url'] = 1;
        		break;
        	case 'facebook_sponsor_url':
        		$social_array['entries']['facebook_sponsor_url'] = 1;
        		break;
        	case 'facebook_url':
        		$social_array['entries']['facebook_url'] = 1;
        		break;
        	case 'follow':
        		$social_array['entries']['twitter_url'] = 1;
        		break;
        	case 'tweet':
        		$social_array['entries']['twitter_share'] = 1;
        		break;
        	case 'facebook_like':
        		$social_array['entries']['facebook_like'] = 1;
        		break;
        	default:
        		$social_array['entries'][$type] = 1;
        		break;
        }

        $current_entry[3] = serialize($social_array);

        $result = self::update_entry($current_entry);

        wp_send_json_success($result);
		
		die();
	}



	/**
     * Gets a single Entry object.
     *
     * Intended to get the giveaway entry
     * Checks and returns entry.
     *
     * @since  1.8
     * @access public
     * @static
     *
     * @param array $entry The Entry object
     *
     * @return mixed Either the new Entry ID or a WP_Error instance
     */
    public static function get_entry($entry_id) {
    	global $wpdb;
    	$entry_table = $wpdb->giveaway_entry_table;
    	$entry = $wpdb->get_row("SELECT * FROM $entry_table WHERE entry_id = {$entry_id}", ARRAY_N);

		if (false === $entry)
            return new WP_Error("insert_entry_properties_failed", __("There was a problem while inserting the entry properties", "ppm_giveaway_engine"), $wpdb->last_error);
        // reading newly created lead id

		if ( $entry ) {
			return $entry;
		} else return 0;
    }

    /**
     * Updates a single Entry object.
     *
     * Intended to update the giveaway entry
     * Checks and updates entry.
     *
     * @since  1.0.0
     * @access public
     * @static
     *
     * @param array $entry The Entry object
     *
     * @return mixed Either the result or a WP_Error instance
     */
    public static function update_entry($entry) {
    	global $wpdb;
    	$entry_table = $wpdb->giveaway_entry_table;
        $result  = $wpdb->query($wpdb->prepare("
                UPDATE $entry_table
                SET
                user_meta = %s
                WHERE
                entry_id = %d
                ", $entry[3],$entry[0]));

        if (false === $result)
            return new WP_Error("update_entry_properties_failed", __("There was a problem while updating the entry properties", "gravityforms"), $wpdb->last_error);

        return true;
    }



	/**
     * Adds a single Entry object.
     *
     * Intended to add the giveaway entry
     * Checks that the giveaway exists & Entry details exist form id.
     *
     * @since  1.8
     * @access public
     * @static
     *
     * @param array $entry The Entry object
     *
     * @return mixed Either the new Entry ID or a WP_Error instance
     */
    public static function add_entry($entry) {
        global $wpdb;

        if(!is_array($entry)){
            return new WP_Error("invalid_entry_object", __("The entry object must be an array", "ppm_giveaway_engine"));
        }

        // make sure the form id exists
        $giveaway_id = $entry["giveaway_id"];
        if (empty($giveaway_id)){
            return new WP_Error("empty_giveaway_id", __("The giveaway id must be specified", "ppm_giveaway_engine"));
        }

        
        // get values from the $entry object

        $user_name = isset($entry["user_name"]) ? $entry["user_name"] : "";
        $user_email = isset($entry["user_email"]) ? $entry["user_email"] : "";
        $timestamp = time()+date("Z");
        $entry_date   = gmdate("Y-m-d H:i:s",$timestamp);;

       	// Setup Future Metrics Array

       	$entry_array = array(
	   						'entries'=>array(
	   									'facebook_url'=>0,
	   									'twitter_url'=>0,
	   									'twitter_share'=>0,
	   									'facebook_like'=>0,
	   									'referal'=>0),
	   						'user_info'=>array(
	   									'ip'=>'',
	   									'referal_url'=>$entry['referal_url'],
	   									'entry_date'=>$entry_date,
	   									),
   						);
       	$entry_serial = serialize($entry_array);

       	$entry_table = $wpdb->giveaway_entry_table;


        $result     = $wpdb->query($wpdb->prepare("
                INSERT INTO $entry_table
                (entry_id, user_name, user_email, user_meta, giveaway_id, entry_date)
                VALUES
                (NULL, %s, %s, %s,  %d, '{$entry_date}')
                ", $user_name, $user_email, $entry_serial, $giveaway_id ));
        if (false === $result)
            return new WP_Error("insert_entry_properties_failed", __("There was a problem while inserting the entry properties", "ppm_giveaway_engine"), $wpdb->last_error);
        // reading newly created lead id


        $entry_id    = $wpdb->insert_id;
        $entry["id"] = $entry_id;

        return $entry;

    }

    /**
     * Gets all Entries associated with object.
     *
     * Intended to get the giveaway entries
     * Checks and returns entries
     *
     * @since  1.0.0
     * @access public
     * @static
     *
     * @param array $entry_idThe Entry object id
     *
     * @return mixed Either the new Entry ID or a WP_Error instance
     */
    public static function get_entries($entry_id) {
    	global $wpdb;
    	$entry_table = $wpdb->giveaway_entry_table;
    	$query = "SELECT * FROM $entry_table WHERE giveaway_id = {$entry_id}";

	    $items = $wpdb->get_results($query);

	    return $items;

	}

	/**
	 * Get the image for the given ID. If no featured image, check for Gravatar e-mail.
	 * @param  int 				$id   Post ID.
	 * @param  string/array/int $size Image dimension.
	 * @since  1.0.0
	 * @return string       	<img> tag.
	 */
	protected function get_image ( $id, $size ) {
		$response = '';

		if ( has_post_thumbnail( $id ) ) {
			// If not a string or an array, and not an integer, default to 150x9999.
			if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 50, 50 );
			}
			$response = get_the_post_thumbnail( intval( $id ), $size, array( 'class' => 'avatar' ) );
		} else {
			$gravatar_email = get_post_meta( $id, '_gravatar_email', true );
			if ( '' != $gravatar_email && is_email( $gravatar_email ) ) {
				$response = get_avatar( $gravatar_email, $size );
			}
		}

		return $response;
	} // End get_image()

	/**
	 * Get team members.
	 * @param  string/array $args Arguments to be passed to the query.
	 * @since  1.0.0
	 * @return array/boolean      Array if true, boolean if false.
	 */
	public function get_our_team ( $args = '' ) {
		$defaults = array(
			'query_id'		=> 'our_team',
			'limit' 		=> 12,
			'orderby' 		=> 'menu_order',
			'order' 		=> 'DESC',
			'id' 			=> 0,
			'slug'			=> null,
			'category' 		=> 0,
			'meta_key'		=> null,
			'meta_value'	=> null
		);

		$args = wp_parse_args( $args, $defaults );

		// Allow child themes/plugins to filter here.
		$args = apply_filters( 'woothemes_get_our_team_args', $args );

		// The Query Arguments.
		$query_args 						= array();
		$query_args['query_id']				= $args['query_id'];
		$query_args['post_type'] 			= 'team-member';
		$query_args['numberposts'] 			= $args['limit'];
		$query_args['orderby'] 				= $args['orderby'];
		$query_args['order'] 				= $args['order'];
		$query_args['suppress_filters'] 	= false;

		$ids = explode( ',', $args['id'] );
		if ( 0 < intval( $args['id'] ) && 0 < count( $ids ) ) {
			$ids = array_map( 'intval', $ids );
			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {
				$query_args['p'] = intval( $args['id'] );
			} else {
				$query_args['ignore_sticky_posts'] = 1;
				$query_args['post__in'] = $ids;
			}
		}

		if ( $args['slug'] ) {
			$query_args['name'] = esc_html( $args['slug'] );
		}

		// Whitelist checks.
		if ( ! in_array( $query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$query_args['orderby'] = 'date';
		}

		if ( ! in_array( $query_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$query_args['order'] = 'DESC';
		}

		if ( ! in_array( $query_args['post_type'], get_post_types() ) ) {
			$query_args['post_type'] = 'team-member';
		}

		$tax_field_type = '';

		// If the category ID is specified.
		if ( is_numeric( $args['category'] ) && 0 < intval( $args['category'] ) ) {
			$tax_field_type = 'id';
		}

		// If the category slug is specified.
		if ( ! is_numeric( $args['category'] ) && is_string( $args['category'] ) ) {
			$tax_field_type = 'slug';
		}

		// If a meta query is specified
		if ( is_string( $args['meta_key'] ) ) {
			$query_args['meta_key'] = esc_html( $args['meta_key'] );
		}

		if ( is_string( $args['meta_value'] ) ) {
			$query_args['meta_value'] = esc_html( $args['meta_value'] );
		}

		// Setup the taxonomy query.
		if ( '' != $tax_field_type ) {
			$term = $args['category'];
			if ( is_string( $term ) ) { $term = esc_html( $term ); } else { $term = intval( $term ); }
			$query_args['tax_query'] = array( array( 'taxonomy' => 'team-member-category', 'field' => $tax_field_type, 'terms' => array( $term ) ) );
		}

		// The Query.
		$query = get_posts( $query_args );

		// The Display.
		if ( ! is_wp_error( $query ) && is_array( $query ) && count( $query ) > 0 ) {
			foreach ( $query as $k => $v ) {
				$meta = get_post_custom( $v->ID );

				// Get the image.
				$query[$k]->image = $this->get_image( $v->ID, $args['size'] );

				foreach ( (array)$this->get_custom_fields_settings() as $i => $j ) {
					if ( isset( $meta['_' . $i] ) && ( '' != $meta['_' . $i][0] ) ) {
						$query[$k]->$i = $meta['_' . $i][0];
					} else {
						$query[$k]->$i = $j['default'];
					}
				}
			}
		} else {
			$query = false;
		}

		return $query;
	} // End get_our_team()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'ppm-giveaway-engine', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'ppm-giveaway-engines';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
		$this->flush_rewrite_rules();
		$this->giveaway_setup_database();

	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'ppm-giveaway-engine' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * Flush the rewrite rules
	 * @access public
	 * @since 1.4.0
	 * @return void
	 */
	private function flush_rewrite_rules () {
		$this->register_post_type();
		flush_rewrite_rules();
	} // End flush_rewrite_rules()

	/**
	 * Add Entry Table Prefix
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function ppm_giveaway_engine_entry_table() {
	    global $wpdb;
	    $wpdb->giveaway_entry_table = "{$wpdb->prefix}giveaway_entry_table";
	}

	/**
	 * Create Entry Table
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function giveaway_setup_database() {
	    // Code for creating a table goes here
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		global $charset_collate;
		// Call this manually as we may have missed the init hook
		$this->ppm_giveaway_engine_entry_table();

		$sql_create_table = "CREATE TABLE {$wpdb->giveaway_entry_table} (
		          entry_id bigint(20)  NOT NULL auto_increment,
		          user_name varchar(400)  NOT NULL default '',
		          user_email varchar(400) NOT NULL default '',
		          user_meta varchar(2000) NOT NULL default '',
		          giveaway_id varchar(20) NOT NULL default '',
		          entry_date datetime NOT NULL default '0000-00-00 00:00:00',
		          PRIMARY KEY  (entry_id),
		          KEY user_email (user_email)
		     ) $charset_collate; ";
		 
		dbDelta( $sql_create_table );
	}

	/**
	 * Ajax callback to search for users.
	 * @param  string $query Search Query.
	 * @since  1.1.0
	 * @return json       	Search Results.
	 */
	public function get_select_winner() {

		global $current_user;
      	get_currentuserinfo();

		check_ajax_referer( 'ppm-giveaway-winner-select', 'security' );

		$entry_id = urldecode( stripslashes( strip_tags( $_REQUEST['entry_id'] ) ) );

		if ( !empty( $entry_id  ) ) {

			header( 'Content-Type: application/json; charset=utf-8' );

			$entries = self::get_entries($entry_id);

			$entries_array = array();

			foreach ($entries as $entry) {
				$entry_meta = unserialize($entry->user_meta);
				$entries_array[] = $entry->entry_id;
				foreach ($entry_meta['entries'] as $key => $value) {
					if ($value !== 0) {
						$entries_array[] = $entry->entry_id;
					}
				}
			}

			//This must be an action

			self::ppmfisherYatesShuffle($entries_array);

			$entries_array = array_slice($entries_array, 0, 5);

			foreach ($entries_array as $entry) {
				$winners[] = self::get_entry($entry);
			}

			update_post_meta($entry_id,'_ppm_giveaway_winners',$winners);
			$giveaway_info = array('Username'=>$current_user->user_firstname,'Email'=>$current_user->user_email,'Date'=>date('Y-m-d H:i:s'));	
			update_post_meta($entry_id,'_ppm_giveaway_selection_info',$giveaway_info);

			//Hook for podio

			echo json_encode( $winners );

		}

		die();

	}

	private function ppmfisherYatesShuffle(array &$items) {
	    for($i = count($items) - 1; $i > 0; $i --) {
	        $j = @mt_rand(0, $i);
	        $tmp = $items[$i];
	        $items[$i] = $items[$j];
	        $items[$j] = $tmp;
	    }
	}


	/**
	 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
	 * @since  1.0.1
	 * @return  void
	 */
	public function ensure_post_thumbnails_support () {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) { add_theme_support( 'post-thumbnails' ); }
	} // End ensure_post_thumbnails_support()

	/**
	 * Output admin javascript
	 * @since  1.1.0
	 * @return  void
	 */
	public function get_select_winner_javascript() {

		global $pagenow, $post_type;

			$ajax_nonce = wp_create_nonce( 'our_team_ajax_get_users' );

	?>
			<script type="text/javascript" >
				jQuery(function() {
					jQuery( "#select-winner" ).submit(function(e){
						e.preventDefault();
						jQuery.ajax({
							url: ajaxurl,
							dataType: 'json',
							data: jQuery( this ).serialize(),
							success: function( data ) {
								console.log(data);
							}
						});
					});
				});
			</script>
	<?php
		
	} //End get_users_javascript

	/**
	 * Add the Our Team action links
	 * @param  array $links current action links
	 * @return array current action links merged with new action links
	 */
	public function our_team_action_links( $links ) {
		$our_team_links = array(
			'<a href="http://docs.woothemes.com/documentation/plugins/our-team/" target="_blank">' . __( 'Documentation', 'our-team-by-woothemes' ) . '</a>',
		);

		return array_merge( $links, $our_team_links );
	}

	/**
	 * Our Team Help Tab
	 * Gives users quick access to shortcode examples via the dashboard
	 * @return array content for the help tab
	 */
	public function our_team_help_tab () {
    $screen = get_current_screen();

    $screen->add_help_tab( array(
        'id'		=> 'woo_our_team_help_tab',
        'title'		=> __( 'Our Team', 'our-team-by-woothemes' ),
        'callback'	=> 'our_team_help_tab_content',
    ) );

    /**
     * Our Team help tab content
     * @return void
     */
    function our_team_help_tab_content() {
    	$odd 	= 'width: 46%; float: left; clear: both;';
    	$even 	= 'width: 46%; float: right;';
    	?>
    		<h3><?php _e( 'Displaying team members in posts and pages', 'our-team-by-woothemes' ); ?></h3>
    		<p>
    			<?php echo sprintf( __( 'The easiest way to display team members is to use the %s[woothemes_our_team]%s shortcode.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?>
    		</p>
    		<p>
    			<?php _e( 'The shortcode accepts various arguments as described below:', 'our-team-by-woothemes' ); ?>
    		</p>
    		<ul style="overflow: hidden;">
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%slimit%s - The maximum number of team members to display.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%sorderby%s - How to order the team members. (Accepts all default WordPress ordering options).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%sorder%s - The order direction. (eg. ASC or DESC).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%sid%s - Display a specific team member by ID.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%sdisplay_avatar%s - Display the team members gravatar. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%ssize%s - The size to display the team members gravatar.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%sdisplay_additional%s - Global toggle for displaying all additional information such as twitter, email and telephone number. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%sdisplay_url%s - Display the team members URL. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%sdisplay_role%s - Display the team members role. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%sdisplay_twitter%s - Display the team members twitter follow button. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%sdisplay_author_archive%s - Display the team members author archive link if specified. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%scontact_email%s - Display the team members contact email. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%stel%s - Display the team members telephone number. (true or false).', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $even; ?>"><?php echo sprintf( __( '%sslug%s - Display a specific team member by post slug.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
	    		<li style="<?php echo $odd; ?>"><?php echo sprintf( __( '%scategory%s - Display team members from within a specified category. Use the category slug.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?></li>
    		</ul>
    		<p>
    			<?php echo sprintf( __( 'For example, to display 6 team members while hiding gravatars you would use this shortcode: %s[woothemes_our_team limit="6" display_avatar="false"]%s.', 'our-team-by-woothemes' ), '<code>', '</code>' ); ?>
    		</p>
    		<p>
    			<p><?php echo sprintf( __( 'Read more about how to use Our Team in the %sdocumentation%s.', 'our-team-by-woothemes' ), '<a href="http://docs.woothemes.com/document/our-team-plugin/">', '</a>' ); ?></p>
    		</p>
    	<?php
    }
}

} // End Class
