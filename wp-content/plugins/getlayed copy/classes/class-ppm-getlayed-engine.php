<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * PPM Get Layed Engine Class
 *
 *  *
 * @package WordPress
 * @subpackage PPM_Getlayed_Engine
 * @category Plugin
 * @author Sergio Pellegrini
 * @since 1.0.0
 */
class PPM_Getlayed_Engine {
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
		$this->token = 'layout';

		$this->load_plugin_textdomain();

		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( $this, 'activation' ) );

		
		
		
		add_action( 'wp_ajax_ppm_save_post', array( $this, 'ppm_save_post' ) );
		add_action( 'init', array( $this, 'ppm_add_image_sizes' ) );

		if ( is_admin() ) {
			global $pagenow;

			// Add Layout Button with preview items
			add_action( 'add_meta_boxes', array($this,'ppm_meta_box_add' ));  

		}

		add_action( 'wp_enqueue_scripts', array($this,'ppm_getlayed_engine_scripts'), 999 );
			

		add_filter( 'the_content', array($this,'PPM_Getlayed_Engine_Content_Init'),20 );


		add_action('wp_footer',array($this,'PPM_Getlayed_Engine_Menu'),999);

		add_filter('body_class', array($this,'PPM_Getlayed_Engine_Body_Class'));

	} // End __construct()

	/**
	 * Add Layout Button Meta Box
	 *
	 * @access public
	 * @since   1.0.0
	 * @return   void
	 *
	 */

	function ppm_meta_box_add()  
	{  
		add_meta_box( 'layout-manager', 'Layout Manager', array($this,'ppm_meta_box_cb'), 'post', 'side', 'high' );
	} 

	/* Layout Manager Edit Post Screen */
	function ppm_meta_box_cb()  
	{  	
		global $post;
		$layout_link = set_url_scheme( get_permalink( $post->ID ) );
		$layout_link = esc_url( apply_filters( 'layout_post_link', add_query_arg( array('preview'=>'true','layout'=>'true'), $layout_link ), $post ) );
		$layout_button = __( 'Layout' );
		?>
		<a class="preview button" href="<?php echo $layout_link; ?>" target="wp-layout-<?php echo (int) $post->ID; ?>" id="post-layout"><?php echo $layout_button; ?></a>
		<div class="clear"></div>
		<?php
	}

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
	public function ppm_getlayed_engine_scripts() {
		global $post;

		wp_register_style( 'giveaway-styles', $this->assets_url . 'css/editor.css', array(), '1.0.1' );
		wp_enqueue_style( 'giveaway-styles' );

		if (!is_admin() && $_GET['layout'] == 'true' && current_user_can('edit_post')) {

		 	

			wp_register_script( 'ppm-oql',$this->assets_url . 'js/owl-min.js', array('jquery'), '1.0.0',true);
	    	wp_register_script( 'ppm-medium',$this->assets_url . 'js/medium-editor.js', array('jquery'), '1.0.0',true);
	    	wp_register_script( 'ppm-tmpl',$this->assets_url . 'js/tmpl.js', array('jquery'), '1.0.0',true);
	    	wp_register_script( 'ppm-sortable',$this->assets_url . 'js/sortable.js', array('jquery'), '1.0.0',true);
	    	wp_register_script( 'ppm-general',$this->assets_url . 'js/general.js', array('jquery'), '1.0.0',true);

	    	wp_localize_script( 'ppm-general', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'post_id'=>$post->ID));   

	    	wp_enqueue_script( 'ppm-owl');
	       	wp_enqueue_script( 'ppm-medium');
	       	wp_enqueue_script( 'ppm-tmpl');
	       	wp_enqueue_script( 'ppm-sortable');
	       	wp_enqueue_script( 'ppm-general');
	    }    
	    
	}

	public function ppm_add_image_sizes() {
		$image_sizes = array(	'ppm_l1' => array('width'=>1200,'height'=>800,'crop'=>true),
								'ppm_l2' => array('width'=>768,'height'=>511,'crop'=>true),
								'ppm_l3' => array('width'=>496,'height'=>330,'crop'=>true),
								'ppm_p1' => array('width'=>600,'height'=>800,'crop'=>true),
								'ppm_p2' => array('width'=>450,'height'=>674,'crop'=>true),
							);

		$image_sizes  = apply_filters('ppm_image_sizes',$image_sizes);

		foreach ($image_sizes as $key => $sizes) {
			add_image_size($key,$sizes['width'],$sizes['height'],$sizes['crop']);
		}

		add_filter('tiny_mce_before_init', 'vsl2014_filter_tiny_mce_before_init');
		function vsl2014_filter_tiny_mce_before_init( $options ) {

		    if ( ! isset( $options['extended_valid_elements'] ) ) {
		        $options['extended_valid_elements'] = 'picture,source[source|srcset|media|src]';
		    } else {
		        $options['extended_valid_elements'] .= ',picture,source[source|srcset|media|src],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]';
		    }

		    if ( ! isset( $options['valid_children'] ) ) {
		        $options['valid_children'] = '+body[source|srcset|media|src]';
		    } else {
		        $options['valid_children'] .= '+body[source|srcset|media|src],+img[srcset]';
		    }

		    if ( ! isset( $options['custom_elements'] ) ) {
		        $options['custom_elements'] = 'img[srcset]';
		    } else {
		        $options['custom_elements'] .= ',picture,source';
		    }

		    return $options;
		}
	}

	public function PPM_Getlayed_Engine_Content_Init($content) {
		if ( is_preview() && $_GET['layout'] == 'true' && current_user_can('edit_post')) {
			$html = '<div id="layout-builder" class="editor">';
			$html .= $content;
			$html .= '</div>';
			return $html;
		} else {
			return $content;
		}
		
		
	}
	public function PPM_Getlayed_Engine_Body_Class($classes) {
		if (is_preview() && $_GET['layout'] == 'true' && current_user_can('edit_post')) {
			$classes[] = 'layout';
		} 
		return $classes;
	}

	public function PPM_Getlayed_Engine_Menu($content) {
		global $post;
		if ( is_preview() && $_GET['layout'] == 'true' && current_user_can('edit_post')) {
		?>
		<div class="editorMenu">
			<ul>
				<li>
					<button class="btn itbtn"><i class="fa fa-camera"></i></button>
				</li>
				<li>
					<button class="btn saveBtn"><i class="fa fa-save"></i></button>
				</li>
				<li>
					<button class="btn gallerybtn"><i class="fa fa-picture-o"></i></button>
				</li>
			</ul>
		</div>
		<?php add_filter( 'get_attached_media_args',array($this,'ppm_filter_media'), 10, 3 ); ?>
		<?php $media = get_attached_media( 'image',$post->ID ); ?>
		<div class="thumbnailMenu">

                <div id="owl-demo" class="owl-carousel owl-theme">
                	<?php $count = 0; ?>
                	<?php $image_array = array(); ?>
                	<?php foreach ($media as $image) { $count ++;
                		$image_attributes = wp_get_attachment_image_src( $image->ID,'full'); 
                		$image_sizes = get_intermediate_image_sizes(); 

						foreach ($image_sizes as $size_name => $size_attrs):
							$image_array[$image->ID][$size_attrs] = wp_get_attachment_image_src( $image->ID,$size_attrs); 
						endforeach;

                		echo '<div class="item">';
						
						echo '<img id="image-'.$image->ID.'"class="img-responsive" draggable="true" data-id="'.$image->ID.'" src="'.$image_attributes[0].'"/>';

						echo '</div>';
					} ?>
					
				</div>

				<div class="customNavigation">
				  <a class="btn prev">Previous</a>
				  <a class="btn next">Next</a>
				</div>
             

		</div>
		<script type="text/javascript">
			var image_array = {};
			image_array = jQuery.parseJSON('<?php echo json_encode($image_array);?>');
		</script>
		<script type="text/x-tmpl" id="tmpl-demo">
		  <div id="row-{%=o.count%}" class="row-item" data-row="{%=o.count%}" data-disable-editing="true" contenteditable="false">
			
			<div class="layout-row" contenteditable="false">

			  <div id="canvas-{%=o.count%}" class="canvas row-images clearfix" contenteditable="false"></div>

			  <div contenteditable="false" id="drop-{%=o.count%}" data-canvas="canvas-{%=o.count%}" data-row="{%=o.count%}" class="init drop container-add open">
				<div class="text-center alert alert-info">
				  <span class="fa fa-file-image-o fa-2x"></span>
				  <h5>Add your images</h5>
				  <small>Drag one or more of the thumbnails from the top of the screen and plece them here</small><br>
				  <small><em>(max of 4 images)</em></small>
				</div>
			  </div>
			</div>
		  </div>
		</script>
		<script type="text/x-tmpl" id="tmpl-picture">
			<picture>
				<source srcset="{%=o.one%}" media="(min-width: 1200px)">
				<source srcset="{%=o.two%}" media="(min-width: 992px)">
				<source srcset="{%=o.three%}" media="(min-width: 768px)">
				<img data-id="{%=o.image%}" srcset="{%=o.zero%}">
			</picture>
		</script>
		<script type="text/x-tmpl" id="tmpl-img-options">
			<div id="img-options">
				<ul>
					<li>
						<button class="btn trash-img"><i class="fa fa-remove"></i></button>
					</li>
					<li>
						<button class="btn"><i class="fa fa-gear"></i></button>
					</li>
				</ul>
			</div>
		</script>
		<script type="text/x-tmpl" id="tmpl-row-options">
			<div id="row-options">
				<ul>
					<li>
						<button class="btn trash-row" data-row="{%=o.row%}"><i class="fa fa-remove"></i></button>
					</li>
					<li>
						<button class="btn swap-row-up" data-row="{%=o.row%}"><i class="fa fa-angle-up"></i></button>
					</li>
					<li>
						<button class="btn swap-row-down" data-row="{%=o.row%}"><i class="fa fa-angle-down"></i></button>
					</li>
				</ul>
			</div>
		</script>
		<?php
		}
	}

	public function ppm_filter_media( $args, $type, $post ) {
		$args['orderby'] = 'title';
		return $args;
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
	 * Ajax callback to add entry.
	 * @param  data array from entry form submit.
	 * @since  1.0.0
	 * @return json Entry Details.
	 */
	public function ppm_save_post() {

		//check_ajax_referer( 'ppm_giveaway_entry', 'ppm_giveaway_entry_nonce' );

		header( 'Content-Type: application/json; charset=utf-8' );

		$entry = array();

		// Update post 37
		$my_post = array(
		  'ID'           => $_POST['id'],
		  'post_content' => $_POST['layout-builder']['value']
		);

		// Update the post into the database
		$result = wp_update_post( $my_post );

		wp_send_json_success($result);


		die();

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
            return new WP_Error("invalid_entry_object", __("The entry object must be an array", "PPM_Giveaway_Engine"));
        }

        // make sure the form id exists
        $giveaway_id = $entry["giveaway_id"];
        if (empty($giveaway_id)){
            return new WP_Error("empty_giveaway_id", __("The giveaway id must be specified", "PPM_Giveaway_Engine"));
        }

    }

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
	    $domain = 'ppm-getlayed-engine';
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

	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'ppm-getlayed-engine' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * Flush the rewrite rules
	 * @access public
	 * @since 1.4.0
	 * @return void
	 */
	private function flush_rewrite_rules () {
		flush_rewrite_rules();
	} // End flush_rewrite_rules()

} // End Class
