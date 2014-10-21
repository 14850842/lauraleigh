<?php
	/* Grid Page Template */
?>

<section class="pageSection">
	<div class="container">
		<?php
			
			if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<div class="pageContainer">

					<?php get_template_part('templates/2col/page','layout'); ?>
					
				</div>

            <?php endwhile; ?>    
            
            <?php else : ?>
            
            <?php endif; ?>
	</div>
</section>