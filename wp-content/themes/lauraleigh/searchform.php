<?php
/**
 * Search Form Template
**/

?>
<form action="<?php echo home_url( '/' ); ?>" method="get" class="form-inline">
    <fieldset>
    <div class="input-group">
      <input type="text" name="s" id="search" placeholder="<?php _e("Search","bonestheme"); ?>" value="<?php the_search_query(); ?>" class="form-control" />
      <input type="hidden" name="post_type" value="post"/>
      <span class="input-group-btn">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
      </span>
    </div>
    </fieldset>
</form>

