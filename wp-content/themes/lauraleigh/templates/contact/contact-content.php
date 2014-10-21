<?php
	/* Grid Page Template */
?>

<section class="pageSection">
	<div class="container">

		<?php

			// The Loop
			if ( have_posts() ) { ?>
				
				<?php while ( have_posts() ) { the_post();?>
					<div class="pageContainer">
						<h1 class="sectionTitle pageTitle"><?php the_title(); ?></h1>
						<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
						<div class="pageContent">
							<div class="row">
								<div class="col-xs-6">
									<?php the_content(); ?>
								</div>
								<div class="col-xs-6">
									<?php gravity_form(1, false, false, false, '', false); ?>
								</div>
							</div>
						</div>
					</div>
				<?php }
			} else {
				// no posts found
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		?>
		
	</div>
</section>