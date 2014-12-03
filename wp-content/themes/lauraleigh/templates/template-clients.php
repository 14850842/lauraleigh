<?php
/* 
	Template Name: Clients
*/

get_header();

	get_template_part('templates/clients/feature','image');

	get_template_part('templates/clients/grid','clients');

	if (!is_page( 'pricing' ) && !is_page( 'products' )){
		
		get_template_part('templates/clients/testimonials');

		get_template_part('templates/clients/publications');

		get_template_part('templates/clients/faq');

		get_template_part('templates/clients/suppliers');
	};
	
	get_template_part('templates/features');

get_footer();

?>