<?php global $brew_options ?>
<?php if ( $brew_options['breadcrumb'] == 1) { ?>
		<?php if ( function_exists('ppm_custom_breadcrumb') ) { ppm_custom_breadcrumb(); } ?>
<?php } ?>