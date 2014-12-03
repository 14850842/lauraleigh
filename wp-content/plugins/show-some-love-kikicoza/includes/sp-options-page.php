	<?php
	$sp_target = get_option('sp-target');
	$sp_style = get_option('sp-style');
	$sp_custom = get_option('sp-custom');
	$sp_display = get_option('sp-display');
    ?>
    <style>.sp-desc{font-style: italic;font-size: 12px}</style>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Show Some Love Plugin Options</h2>

	<div id="poststuff" class="metabox-holder has-right-sidebar">

	<div class="inner-sidebar" id="side-info-column">
	</div>

	<div id="post-body-content">
			<form action="" method="post">
            <?php wp_nonce_field( 'sp_opt-rename_tag'); ?>
            <p>Show some love to the people who make it possible to do what you do. This plugin remembers the details of each person you have added so you don't have to retype all the details in each post and displays it below the relevant posts. We make it easy to show some love.</p>
			<table class="form-table">
			<tbody>
			<tr valign="top">
			<th scope="row"><label for="default_post_edit_rows">List Style:</label></th>
			<td>
			<ul>
			<li>
			<input type="radio" name="sp-style" <?php if ($sp_style == 'ordered') { echo 'checked="checked"'; } ?> value="ordered" />
			<label>List</label>
			</li>
			<li>
			<input type="radio" name="sp-style" <?php if ($sp_style == 'inline') { echo 'checked="checked"'; } ?> value="inline" />
			<label>Inline</label>
			</li>
			</ul>
			<p class="sp-desc">Choose whether the links should be displayed in one line or underneath each other.</p>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="default_post_edit_rows">Target:</label></th>
			<td>
			<input type="checkbox" name="sp-target" <?php if ($sp_target == 'true') { echo 'checked="checked"'; } ?> value="true"/>
			<label>Open Link in a new Window</label>
			<p class="sp-desc">Select this option to have the link open in a new window so the visitor will not leave your site when clicking on the link.</p>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="default_post_edit_rows">Custom CSS:</label></th>
			<td>
			<textarea class="" rows="5" cols="50" name="sp-custom" id=""><?php echo $sp_custom;?></textarea>
			<p class="sp-desc"> If you want to modify the way the vendors are displayed, add custom CSS here. (Only if you understand CSS and HTML)</p>
			</td>
			</tr>
			<th scope="row"><label for="default_post_edit_rows">Placement:</label></th>
			<td>
			<input type="checkbox" name="sp-display" <?php if ($sp_display == 'true') { echo 'checked="checked"'; } ?> value="true"/>
			<label>Display after content</label>
			<p class="sp-desc">Let the plugin automatically place the links below my content.</p>
			<p class="sp-desc">Or uncheck and use the [show-some-love] shortcode.</p>
			</td>
			</tr>
			
			</tbody>
			</table>
            <input type="hidden" name="sp_action" value="update" />
			<input type="submit" class='button-primary' name="update_yarpp" value="<?php _e( 'Save Changes' )?>" />
		</div>
</div><!--#poststuff-->
</form>
		<h3>Not only is this plugin free...</h3>
		<p><strong>But also, by donating you will become popular and everyone will like you!</strong><br />
			Okey, maybe not 'everyone'. Definitely the entire KIKI Team though.<br /> Plus we will be able to make some more cool plugins.
		</p>
		
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QSVVTZ35YP952">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>


</div>
