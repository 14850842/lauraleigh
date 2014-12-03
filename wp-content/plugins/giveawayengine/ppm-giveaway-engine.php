<?php
/**
 * Plugin Name: Giveaway Engine
 * Plugin URI: http://www.thegiveawayengine.com
 * Description: IMPLE, SOCIAL, MEASURABLE GIVEAWAYS.
 * Author: PlusPlusMinus
 * Version: 1.0.0
 * Author URI: http://www.thegiveawayengine.com
 *
 * @package WordPress
 * @subpackage PPP_Giveaway_Engine
 * @author Sergio Pellegrini
 * @since 1.0.0
 */

require_once( 'classes/class-ppm-giveaway-engine.php' );

if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
   require_once( 'classes/admin/ppm-giveaway-engine-entry-table.php' );
}

//require_once( 'classes/class-woothemes-our-team-taxonomy.php' );
require_once( 'ppm-giveaway-template.php' );
//require_once( 'classes/class-woothemes-widget-our-team.php' );

global $ppm_giveaway_engine;
$ppm_giveaway_engine = new PPM_Giveaway_Engine( __FILE__ );
$ppm_giveaway_engine->version = '1.0.0';