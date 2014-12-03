<?php
/* 
	Template Name: Home 
*/

get_header();

	get_template_part('templates/header/feature','image');

	get_template_part('templates/header/about');

	get_template_part('templates/posts/grid','home');

get_footer();

?>