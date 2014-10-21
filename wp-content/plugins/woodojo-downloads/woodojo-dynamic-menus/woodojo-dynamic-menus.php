<?php
/**
 * Module Name: WooDojo - Dynamic Menus
 * Module Description: Dynamically select a custom menu for the available Menu Locations in your posts, pages and custom post types.
 * Module Version: 1.0.5
 *
 * @package WooDojo
 * @subpackage Downloadable
 * @author Tiago
 * @since 1.0.0
*/

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 /* Instantiate WooDojo Dynamic Menus */
 if ( class_exists( 'WooDojo' ) ) {
 	require_once( 'classes/class-woodojo-dynamic-menus.php' );
 	$woodojo_dynamic_menus = new WooDojo_Dynamic_Menu;
 }