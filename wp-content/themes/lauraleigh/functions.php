<?php

/* PPM Functions */

use Evansims\Socialworth;

add_action( 'wp_enqueue_scripts', 'ppm_scripts_and_styles', 999 );

function ppm_scripts_and_styles() {
    global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
    global $brew_options;
    if (!is_admin()) {

        wp_register_script( 'third-party', get_stylesheet_directory_uri() . '/library/js/third-party.js', array('jquery'), '3.0.0',true);
        wp_register_script( 'owl', get_stylesheet_directory_uri() . '/library/js/owl.min.js', array('jquery'), '3.0.0',true);
        wp_register_script( 'ppm', get_stylesheet_directory_uri() . '/library/js/ppm.js', array('third-party','owl','jquery'), '3.0.0',true);
        
        wp_enqueue_script('third-party');
        wp_enqueue_script('owl');
        wp_enqueue_script('ppm');


    }
}

register_nav_menus(
        array(
            'secondary-nav' => __( 'Secondary Navigation', 'bonestheme' ),   // main nav in header
            'clients-nav' => __( 'Clients Navigation', 'bonestheme' ),   // main nav in header
        )
    );

/*********************
MENUS & NAVIGATION
*********************/

// the main menu
function secondary_nav($nav = 'secondary-nav') {
    // display the wp3 menu if available
    wp_nav_menu(array(
        'container' => false,                                       // remove nav container
        'container_class' => 'menu clearfix',                       // class of container (should you choose to use it)
        'menu' => __( 'The Main Menu', 'bonestheme' ),              // nav name
        'menu_class' => 'nav navbar-nav',              // adding custom nav class
        'theme_location' => $nav,                             // where it's located in the theme
        'before' => '',                                             // before the menu
        'after' => '',                                            // after the menu
        'link_before' => '',                                      // before each link
        'link_after' => '',                                       // after each link
        'depth' => 2,                                             // limit the depth of the nav
        'fallback_cb' => 'wp_bootstrap_navwalker::fallback',  // fallback
        'walker' => new wp_bootstrap_navwalker()                    // for bootstrap nav
    ));
} /* end bones main nav */

add_filter( 'cmb_meta_boxes', 'cmb_sample_metaboxes' );

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb_sample_metaboxes( array $meta_boxes ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_ppm_';

    $meta_boxes['feature_metabox'] = array(
        'id'         => 'feature_metabox',
        'title'      => __( 'Feature Metabox', 'cmb' ),
        'pages'      => array( 'feature', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
            array(
                'name'       => __( 'Title Meta', 'cmb' ),
                'desc'       => __( 'title description (optional)', 'cmb' ),
                'id'         => $prefix . 'title_meta',
                'type'       => 'text',
            ),
        ),
    );


    $meta_boxes['image_metabox'] = array(
        'id'         => 'image_metabox',
        'title'      => __( 'Image Options', 'cmb' ),
        'description' => __(''),
        'pages'      => array( 'post','page'), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
            array(
                'id'          => $prefix . 'image_group',
                'type'        => 'group',
                'description' => __( 'Add Blog Featured Portraits', 'cmb' ),
                'options'     => array(
                    'group_title'   => __( 'Image {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'    => __( 'Add Another Image', 'cmb' ),
                    'remove_button' => __( 'Remove Image', 'cmb' ),
                    'sortable'      => true, // beta
                ),
                // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
                'fields'      => array(
                    array(
                        'name' => 'Entry Image',
                        'id'   => 'image',
                        'type' => 'file',
                    ),
                ),
            ),
        )
    );

    $meta_boxes['link_metabox'] = array(
        'id'         => 'link_metabox',
        'title'      => __( 'Link Options', 'cmb' ),
        'description' => __(''),
        'pages'      => array( 'page'), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
            array(
                'id'          => $prefix . 'link_group',
                'type'        => 'group',
                'description' => __( 'Add links to Content', 'cmb' ),
                'options'     => array(
                    'group_title'   => __( 'Link {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'    => __( 'Add Another Link', 'cmb' ),
                    'remove_button' => __( 'Remove Link', 'cmb' ),
                    'sortable'      => true, // beta
                ),
                // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
                'fields'      => array(
                    array(
                        'name' => 'Link Text',
                        'id'   => 'link_text',
                        'type' => 'text',
                    ),
                    array(
                        'name' => 'Link',
                        'id'   => 'link',
                        'type' => 'text',
                    ),
                ),
            ),
        )
    );

    $meta_boxes['faq_metabox'] = array(
        'id' => 'faq-information',
        'title' => 'FAQ Information',
        'pages' => array('page'), // post type
        'show_on' => array( 'key' => 'page-template', 'value' => 'templates/template-faq.php' ),
        'context' => 'normal', //  'normal', 'advanced', or 'side'
        'priority' => 'high',  //  'high', 'core', 'default' or 'low'
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'id'          => $prefix . 'faq_group',
                'type'        => 'group',
                'description' => __( 'Add FAQ', 'cmb' ),
                'options'     => array(
                    'group_title'   => __( 'FAQ {#}', 'cmb' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'    => __( 'Add Entry', 'cmb' ),
                    'remove_button' => __( 'Remove Entry', 'cmb' ),
                    'sortable'      => true, // beta
                ),
                // Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
                'fields'      => array(
                    array(
                        'name' => 'Entry Title',
                        'id'   => 'title',
                        'type' => 'text',
                        // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
                    ),
                    array(
                        'name' => 'Description',
                        'description' => 'Write a short description for this entry',
                        'id'   => 'description',
                        'type' => 'textarea_small',
                    ),
                ),
            )
        ),
    );

    $meta_boxes['about_metabox'] = array(
        'id'         => 'about_metabox',
        'title'      => __( 'Home Page Text', 'cmb' ),
        'show_on' => array( 'key' => 'page-template', 'value' => 'templates/template-home.php' ),
        'pages'      => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left

        // 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
        'fields'     => array(
            array(
                'name'       => __( 'About Excerpt Text', 'cmb' ),
                'desc'       => __( 'Text to be shown on the Home page for the About section', 'cmb' ),
                'id'         => $prefix . 'about_text',
                'type'       => 'textarea',
                'show_on_cb' => 'cmb_test_text_show_on_cb', // function should return a bool value
            ),
        ),
    );

    return $meta_boxes;
}

function get_page_link_info() {
    global $post;
    $links = get_post_meta( $post->ID, '_ppm_link_group', true );
    if ($links) {
        echo '';
            foreach ( (array) $links as $key => $link ) {
                echo '<a href="'.$link['link'].'" class="readmore">'.$link['link_text'].'<i class="fa fa-caret-right"></i> </a> ';
            }
        echo '';
    }
}

// Location Custom Taxonomy
register_taxonomy( 'shoot_location', 
    array('post'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
    array('hierarchical' => true,     /* if this is true, it acts like categories */             
        'labels' => array(
            'name' => __( 'Shoot Location', 'bonestheme' ), /* name of the custom taxonomy */
            'singular_name' => __( 'Shoot Location', 'bonestheme' ), /* single taxonomy name */
            'search_items' =>  __( 'Search Shoot Locations', 'bonestheme' ), /* search title for taxomony */
            'all_items' => __( 'All Shoot Locations', 'bonestheme' ), /* all title for taxonomies */
            'parent_item' => __( 'Parent Shoot Location', 'bonestheme' ), /* parent title for taxonomy */
            'parent_item_colon' => __( 'Parent Shoot Location:', 'bonestheme' ), /* parent taxonomy title */
            'edit_item' => __( 'Edit Shoot Location', 'bonestheme' ), /* edit custom taxonomy title */
            'update_item' => __( 'Update Shoot Location', 'bonestheme' ), /* update title for taxonomy */
            'add_new_item' => __( 'Add New Shoot Location', 'bonestheme' ), /* add new title for taxonomy */
            'new_item_name' => __( 'New Shoot Location Name', 'bonestheme' ) /* name title for taxonomy */
        ),
        'show_admin_column' => true, 
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'shoot-location' ),
    )
);   


function grid_post_images($id) {
    $entries = get_post_meta( $id, '_ppm_image_group', true );
    if ($entries) {
        foreach ( (array) $entries as $key => $entry ) {
            echo '<div class="col-xs-3">';
                echo wp_get_attachment_image( $entry['image_id'], 'full', null, array('class' => 'img-responsive') );
            echo '</div>';
        }
    }
}


function grid_page_images($id) {
    $entries = get_post_meta( $id, '_ppm_image_group', true );

    if ($entries) {
         $count = sizeof($entries);
         $num = 0;

        if ($count < 2) {
            $col = 12 / $count;
        } else {
            $col = 6;
        }


        foreach ( (array) $entries as $key => $entry ) { $num ++;
            echo '<div class="col-xs-'.$col.'">';
                echo wp_get_attachment_image( $entry['image_id'], 'full', null, array('class' => 'img-responsive') );
            echo '</div>';

            if ($num % 2 == 0) echo '<div class="clearfix"></div>';
        }
    }
}

add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    global $brew_options;

    if ($args->theme_location == 'main-nav') {
        $items .= '<li class="social-nav">';
        if (!empty($brew_options['instagram_url'])) $items .= '<a target="_blank" title="Follow on Instagram" href="'.$brew_options['instagram_url'].'"><span class="fa fa-instagram"></span></a>';
        if (!empty($brew_options['facebook_url'])) $items .= '<a target="_blank" title="Like on Facebook" href="'.$brew_options['facebook_url'].'"><span class="fa fa-facebook"></span></a>';
        if (!empty($brew_options['twitter_url'])) $items .= '<a target="_blank" title="Follow on Twitter" href="'.$brew_options['twitter_url'].'"><span class="fa fa-twitter"></span></a>';
        if (!empty($brew_options['pinterest_url'])) $items .= '<a target="_blank" title="Follow on Pinterest" href="'.$brew_options['pinterest_url'].'"><span class="fa fa-pinterest"></span></a>';

        $items .= '</li>';
    }
    return $items;
}

add_filter('redux/options/brew_options/sections', 'child_sections');
function child_sections($sections){
    //$sections = array();
    $sections[] = array(
        'icon'          => 'ok',
        'icon_class'    => 'fa fa-gears',
        'title'         => __('Theme Options', 'peadig-framework'),
        'desc'          => __('<p class="description">Theme modifications</p>', 'ppm'),
        'fields' => array(
                array(
                        'id'=>'site_logo',
                        'type' => 'media', 
                        'url'=> true,
                        'title' => __('Site Logo', 'ppm'),
                        'compiler' => 'true',
                        //'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'=> __('Select main logo from media gallery', 'ppm'),
                        'default'=>array('url'=>'http://s.wordpress.org/style/images/codeispoetry.png'),
                        ),
                array(
                        'id'=>'site_favicon',
                        'type' => 'media', 
                        'url'=> true,
                        'title' => __('Site Icon', 'ppm'),
                        'compiler' => 'true',
                        //'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'=> __('Add a website icon', 'ppm'),
                        'default'=>array('url'=>'http://s.wordpress.org/style/images/codeispoetry.png'),
                        ),  
        )
    );


     $sections[] = array(
        'icon'          => 'ok',
        'icon_class'    => 'fa fa-heart',
        'title'         => __('Social Profiles', 'ppm-framework'),
        'desc'          => __('<p class="description">Social Network URLS</p>', 'ppm'),
        'fields' => array(
           
            array(
                        'id'=>'twitter_url',
                        'type' => 'text',
                        'title' => __('Twitter', 'redux-framework-demo'),
                        'desc' => __('Enter your twitter url', 'redux-framework-demo'),
                        ),  
            array(
                        'id'=>'facebook_url',
                        'type' => 'text',
                        'title' => __('Facebook', 'redux-framework-demo'),
                        'desc' => __('Enter your Facebook URL', 'redux-framework-demo'),
                        ),  
            array(
                        'id'=>'pinterest_url',
                        'type' => 'text',
                        'title' => __('pinterest', 'redux-framework-demo'),
                        'desc' => __('Enter your pinterest URL', 'redux-framework-demo'),
                        ),  
            array(
                        'id'=>'instagram_url',
                        'type' => 'text',
                        'title' => __('Instagram', 'redux-framework-demo'),
                        'desc' => __('Enter your Instagram URL', 'redux-framework-demo'),
                        ),  
        )
    );

    return $sections;
}

function woo_story_sharing($title='Share:')
{
    include_once(get_stylesheet_directory().'/library/socialshare.php');
    $url = get_permalink();
    $title = get_the_title();
    $summary = get_the_excerpt();   
    $socialworth = new Socialworth($url);
    //$response = $socialworth->all();

    global $post;

    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');?>
    <ul class="social list-inline">  
        <li>
            <div class="nav-buttons nav-share">
                <span class="link">
                    <span class="icon-wrap"><span class="share">0</span></span>
                    <h3>Shares</h3>
                </span>
            </div>
        </li>
        <li>
            <div class="nav-buttons">
                <a href="#" class="link" onclick="window.open('http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo urlencode($title); ?>&p[summary]=<?php echo urlencode($summary); ?>&p[url]=<?php echo urlencode($url); ?>&p[images][0]=<?php echo urlencode($thumb[0]); ?>', 'sharer', 'toolbar=0,status=0,width=626,height=436');return false;">
                    <span class="icon-wrap"><svg class="svg-icon shape-facebook">
                                                <use xlink:href="#shape-facebook"></use>
                                            </svg></span>
                    <h3>Facebook</h3>
                </a>
            </div>
        </li>
        <li>  
            <div class="nav-buttons">
                <a target="_blank" class="social link" href="https://twitter.com/share/?counturl=<?php the_permalink();?>&amp;url=<?php the_permalink();?>&amp;text=<?php the_title();?>">
                    <span class="icon-wrap"><svg class="svg-icon shape-twitter">
                                                <use xlink:href="#shape-twitter"></use>
                                            </svg></span>
                    <h3>Twitter</h3>
                </a>
            </div>  
        </li>
        <li>
            <div class="nav-buttons">
                <a class="social link" target="_blank" onclick="window.open('//pinterest.com/pin/create/button/?url=<?php the_permalink();?>&amp;media=<?php echo $thumb[0];?>', 'sharer', 'toolbar=0,status=0,width=626,height=436');return false;" href="#">
                    <span class="icon-wrap"><svg class="svg-icon shape-pinterest">
                                                <use xlink:href="#shape-pinterest"></use>
                                            </svg></span>
                    <h3>Pinterest</h3>
                </a>
            </div> 
        </li>
    </ul>
    <div class="clearfix"></div>
    <?php
}

function my_more_link( $link, $link_button ) {
            
    return str_replace( $link_button, '<p><a href="' . get_permalink() . '" class="readmore">' . __( $link_button, 'bonestheme' ) . ' <i class="fa fa-caret-right"></i></a> </p>', $link );
}

add_filter( 'the_content_more_link', 'my_more_link', 10, 2 );
?>