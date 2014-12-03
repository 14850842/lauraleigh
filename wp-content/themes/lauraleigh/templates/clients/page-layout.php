<?php
	/* Child Page Layout */
?>

<?php global $post; ?>
<?php global $more; $more = 0; ?>
<div id="<?php echo $post->post_name; ?>" class="pageContent">
	<div class="row">
		<div class="col-sm-6">
			<?php grid_page_images($post->ID); ?>
		</div>
		<div class="col-sm-6">
			<h2 class="pageTitle"><?php the_title(); ?></h2>
			<div class="titleDivider divider ss-style-roundedsplit">&nsbp;</div>
			<?php the_content(); ?>
			
			<?php get_page_link_info(); ?>

			<?php if (is_page( 'pricing' )){ ?>
				<button class="readmore btn btn-default" data-toggle="modal" data-package="<?php the_title();?>" data-target="#enquiryModal">Enquire Now <i class="fa fa-caret-right"></i> </button>
			<?php } ?>
			
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="enquiryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Shoot Enquiry</h4>
      </div>
      <div class="modal-body">
          	<?php gravity_form(2, false, false, false, '', true, 12); ?>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal_lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Booking Form</h4>
      </div>
      <div class="modal-body">
          	<?php gravity_form(2, false, false, false, '', true, 12); ?>
      </div>
    </div>
  </div>
</div>