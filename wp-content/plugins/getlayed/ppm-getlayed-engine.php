<?php
/**
 * Plugin Name: Get layed
 * Plugin URI: http://www.plusplusminus.co.za
 * Description: Woo.
 * Author: PlusPlusMinus
 * Version: 1.0.0
 * Author URI: http://www.plusplusminus.co.za
 *
 * @package WordPressVe
 * @subpackage PPM_Getlayed_Engine
 * @author Sergio Pellegrini
 * @since 1.0.0
 */

require_once( 'classes/class-ppm-getlayed-engine.php' );


//require_once( 'classes/class-woothemes-our-team-taxonomy.php' );
//require_once( 'ppm-giveaway-template.php' );
//require_once( 'classes/class-woothemes-widget-our-team.php' );

global $ppm_getlayed_engine;
$ppm_getlayed_engine = new PPM_Getlayed_Engine( __FILE__ );
$ppm_getlayed_engine->version = '1.0.0';