<?php

class Entry_List_Table extends WP_List_Table {

   /**
	* Constructor, we override the parent to pass our own arguments
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	function __construct() {
	   parent::__construct( array(
	  'singular'=> 'wp_list_entry', //Singular label
	  'plural' => 'wp_list_entries', //plural label, also this well be one of the table css class
	  'ajax'   => false //We won't support Ajax for this table
	  ) );
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
	   if ( $which == "top" ){
			$entry_id = $_POST['giveaway-entry-id'];
			$giveaway_engine_winners = get_post_meta($entry_id,'_ppm_giveaway_winners',true);
			if (empty($giveaway_engine_winners)) {
			//The code that goes before the table is here
			?>
				<div class="ppm-giveaway-box">
					<form id="select-winner" method="POST" action="">
						<?php $ajax_nonce = wp_create_nonce( 'ppm-giveaway-winner-select' ); ?>
						<p><?php printf( __( "Click to select the winner of the giveaway.", 'ppm-giveaway-engine' )); ?></p>
						<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>"/>
						<input type="hidden" name="action" value="select_winner"/>
						<input type="hidden" name="security" value="<?php echo $ajax_nonce; ?>"/>
						<input type="submit" class="button hide-if-no-js" name="ppm-giveaway-winner-select" id="ppm-giveaway-winner-select" value="<?php _e( 'Select Winner', 'ppm-giveaway-engine' ) ?>" /></p>
						<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'ppm-giveaway-engine' ) ?></em></p></noscript>
					</form>
				</div>
			<?php
			} else {
				?>
				<div class="ppm-giveaway-box"> 
					<h4><?php printf( __( "Entrants Selected", 'ppm-giveaway-engine' )); ?></h4>
					<?php foreach ($giveaway_engine_winners as $entry) {
						printf( "<p>%s - %s</p>",$entry[1],$entry[2]);
					}
					$giveaway_engine_info = get_post_meta($entry_id,'_ppm_giveaway_selection_info',true);
					?>
					<small><?php printf( __( "Winners selected by %s,%s. <br> Date: %s", 'ppm-giveaway-engine' ),$giveaway_engine_info['Username'],$giveaway_engine_info['Email'],$giveaway_engine_info['Date']); ?></small>
						
				</div>
				<?php
					
			}
			echo '<br class="clear">';
	   }
	   if ( $which == "bottom" ){
		  //The code that goes after the table is there
		  echo "Social Stats here";
	   }
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
	   return $columns= array(
		  'col_entry_id'=>__('ID'),
		  'col_entry_name'=>__('Name'),
		  'col_entry_email'=>__('Email'),
	   );
	}

	public function get_sortable_columns() {
	   return $sortable = array(
	      'col_entry_id'=>'entry_id',
	   );
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
	   global $wpdb, $_wp_column_headers;
	   $screen = get_current_screen();

	   /* -- Preparing your query -- */

	   	global $wpdb;
    	$entry_table = $wpdb->giveaway_entry_table;

    	$entry_id = $_POST['giveaway-entry-id'];

    	$query = "SELECT * FROM $entry_table WHERE giveaway_id = {$entry_id}";

	   	/* -- Ordering parameters -- */
	    //Parameters that are going to be used to order the result
	    $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
	    $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
	    if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

	   	/* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows

        //How many to display per page?
        $perpage = 20;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
       	if(!empty($paged) && !empty($perpage)){
        	$offset=($paged-1)*$perpage;
         	$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
       	}

	   	/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
	      //The pagination links are automatically built according to those parameters

	   	/* -- Register the Columns -- */
	    $columns = $this->get_columns();
		 $hidden = array();
		 $sortable = $this->get_sortable_columns();
		 $this->_column_headers = array($columns, $hidden, $sortable);

	   	/* -- Fetch the items -- */
	    $this->items = $wpdb->get_results($query);

	    foreach ($this->items as &$item) {
	    	$total = 0;
	    	$entry_meta = unserialize($item->user_meta);
	    	
	    	foreach ($entry_meta['entries'] as $key => $value) {
	    		$item->{$key} = $value;
	    		$total += $value;
	    	}

	    	$item->total = $total;

	    }
	    
	}

	function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
	    case 'col_entry_id':
	    	return $item->entry_id;
	    case 'col_entry_name':
	      return $item->user_name;
	    case 'col_entry_email':
	      return $item->user_email;
	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}

}