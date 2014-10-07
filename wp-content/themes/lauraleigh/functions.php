<?php

/* PPM Functions */

use Evansims\Socialworth;

add_action( 'wp_enqueue_scripts', 'ppm_scripts_and_styles', 999 );

function ppm_scripts_and_styles() {
    global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
    global $brew_options;
    if (!is_admin()) {

        wp_register_script( 'third-party', get_stylesheet_directory_uri() . '/library/js/third-party.js', array('jquery'), '3.0.0',true);
        wp_register_script( 'ppm', get_stylesheet_directory_uri() . '/library/js/ppm.js', array('third-party','jquery'), '3.0.0',true);
        
        wp_enqueue_script('third-party');
        wp_enqueue_script('ppm');


    }
}

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
            'pages'      => array( 'post'), // Post type
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
                ),
            ),
        );

    

    return $meta_boxes;
}

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

        $col = 12 / $count;

        foreach ( (array) $entries as $key => $entry ) {
            echo '<div class="col-xs-'.$col.'">';
                echo wp_get_attachment_image( $entry['image_id'], 'full', null, array('class' => 'img-responsive') );
            echo '</div>';
        }
    }
}

add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {
    global $brew_options;

    if ($args->theme_location == 'main-nav') {
        $items .= '<li>';
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
    $socialworth = new Socialworth('http://www.theprettyblog.com/wedding/jack-carlas-intimate-outdoor-wedding/');
    $response = $socialworth->all();

    global $post;

    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');?>
    <ul class="social list-inline">  
        <li>
            <div class="nav-buttons nav-share">
                <a href="#" onclick="window.open('http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo urlencode($title); ?>&p[summary]=<?php echo urlencode($summary); ?>&p[url]=<?php echo urlencode($url); ?>&p[images][0]=<?php echo urlencode($thumb[0]); ?>', 'sharer', 'toolbar=0,status=0,width=626,height=436');return false;">
                    <span class="icon-wrap"><span class="share"><?php print_r($response->total); ?></span></span>
                    <h3>Shares</h3>
                </a>
            </div>
        </li>
        <li>
            <div class="nav-buttons">
                <a href="#" onclick="window.open('http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo urlencode($title); ?>&p[summary]=<?php echo urlencode($summary); ?>&p[url]=<?php echo urlencode($url); ?>&p[images][0]=<?php echo urlencode($thumb[0]); ?>', 'sharer', 'toolbar=0,status=0,width=626,height=436');return false;">
                    <span class="icon-wrap"><svg class="svg-icon shape-facebook">
                                                <use xlink:href="#shape-facebook"></use>
                                            </svg></span>
                    <h3>Facebook</h3>
                </a>
            </div>
        </li>
        <li>  
            <div class="nav-buttons">
                <a target="_blank" class="social" href="https://twitter.com/share/?counturl=<?php the_permalink();?>&amp;url=<?php the_permalink();?>&amp;text=<?php the_title();?>">
                    <span class="icon-wrap"><svg class="svg-icon shape-twitter">
                                                <use xlink:href="#shape-twitter"></use>
                                            </svg></span>
                    <h3>Twitter</h3>
                </a>
            </div>  
        </li>
        <li>
            <div class="nav-buttons">
                <a class="social" target="_blank" onclick="window.open('//pinterest.com/pin/create/button/?url=<?php the_permalink();?>&amp;media=<?php echo $thumb[0];?>', 'sharer', 'toolbar=0,status=0,width=626,height=436');return false;" href="#">
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