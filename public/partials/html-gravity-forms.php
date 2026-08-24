<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://quovadimus.net
 * @since      1.0.0
 *
 * @author    Dave Johnson/Philadelphia Glider Council 
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php		
			
function display_canidates($atts) {

if( current_user_can( 'new_member_edit' ) || current_user_can( 'manage_options'  || current_user_can('email_multiple_users'))) {	

// There is no way to guarantee the field ids on the production site will be the same
// as in the development site. The is have to be edited on the live site after it is 
// installed. 
//

	if (str_contains($_SERVER["SERVER_NAME"], 'local')){		
		$gf_fields = array( "fname" => "1.3", "lname" => "1.6", "phone" => "17", "pgcstatus"=> "16", "address1" => "5.1", "address2" => "5.2",
	           "address3" => "5.3", "address4" => "5.4", "address5" => "5.5", "sfname" => "3.3", "slname" => "3.6", "fcitizenship" => "6", 
	           "byear" => "7", "fweight" => "8", "fheight" => "9", "fssan" => "11", "foccupation" => "12", "fexpenance" => "13", 
	           "faccident" => "15", "femail" => "4");	
	} else {
			$gf_fields = array( "fname" => "1.3", "lname" => "1.6", "phone" => "10", "pgcstatus"=> "44", "address1" => "21.1", "address2" => "21.2",
	           "address3" => "21.3", "address4" => "21.4", "address5" => "21.5", "sfname" => "34.3", "slname" => "34.6", "fcitizenship" => "24", 
	           "byear" => "25", "fweight" => "27", "fheight" => "26", "fssan" => "30", "foccupation" => "41", "fexpenance" => "31", 
	           "faccident" => "33", "femail" => "2");	
	}
	$per_page = 15;
	$this_page = $_POST["this_page"] ?? 1;
	$skip = ($this_page - 1) * $per_page;
	
 	if (isset($_POST["eid"]) && isset($_POST["pgcstatus"]) ){ 		 	
 		$result = GFAPI::update_entry_field($_POST["eid"] , '44', $_POST["pgcstatus"] );
 	}
  	$filter_list = array('');
 	if ( !empty($_POST['filter_list'] )){
 		foreach($_POST['filter_list'] as $fcheck ) {
	    	array_push($filter_list, $fcheck); 
 		}
 	} else { 	
 		$filter_list = array( 'member', 'new', 'deferred');
 	}
	
	$dc_atts = shortcode_atts(array('form' => '0', 'id'=>'0'), $atts, 'display_candidates');
	
		$form_id = esc_html($dc_atts['form']);
		
		    // Get entries from specific form
	if (isset($_POST['id'])){
	
		$entry = GFAPI::get_entry($_POST['id']);	
		$datetime =  preg_split('/\s+/', $entry['date_created']);
			
		$output = '<div class="Table"><div class="table-heading"><div class="Cell3">Name</div><div class="Cell2">Status</div><div class="Cell2">Date</div></div>';   		    		
		$output .= '<div class="table-row "><div class="Cell3"> ' . $entry[$gf_fields["fname"]] . ' ' .  $entry[$gf_fields["lname"]] . '</div>';
		$output .= '<div><div class="Cell2">' .  select_status($_POST['id'], $entry[$gf_fields["pgcstatus"]]) .  '</div><div class="Cell2">'. $datetime['0'].'</div></div></div>';
	    $output .= '<div class="table-row "><div class="Cell3"> Email: </div><div class="Cell4">' .  $entry[$gf_fields["femail"]] .  '</div></div>';
	    $output .= '<div class="table-row "><div class="Cell3"> Phone: </div><div class="Cell4">' .  $entry[$gf_fields["phone"]] .  '</div></div>';
	    $output .= '<div class="table-row "><div class="Cell3"> Address: </div><div class="Cell4">' .  $entry[$gf_fields["address1"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$gf_fields["address2"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$gf_fields["address3"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$gf_fields["address4"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$gf_fields["address5"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Spouse:</div><div class="Cell4">' . $entry[$gf_fields["sfname"]] . ' ' .  $entry[$gf_fields["slname"]]  .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Citizenship:</div><div class="Cell4">' . $entry[$gf_fields["fcitizenship"]] . '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Age:</div><div class="Cell4">' . date("Y")-$entry[$gf_fields["byear"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Height:</div><div class="Cell4">' . $entry[$gf_fields["fheight"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Weight:</div><div class="Cell4">' . $entry[$gf_fields["fweight"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">SSA Number:</div><div class="Cell4">' . $entry[$gf_fields["fssan"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Occupation:</div><div class="Cell4">' . $entry[$gf_fields["foccupation"]] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Experance:</div><div class="textbox">' . $entry[$gf_fields["fexpenance"] ] .  '</div></div>';
		$output .= '<div class="table-row "><div class="Cell3">Incident/Accident:</div><div class="textbox">' . $entry[$gf_fields["faccident"] ] .  '</div></div>';
		$output .= '</div>'; 
		
		return $output;    	
	}
		    
	 $search_criteria = array(
	 	'field_filters' => array(
	 	'mode' => 'all',
	 	array(
	     	'key'   => $pgcstatus,
	     	'operator' => 'in',
	     	'value' => $filter_list
	 		),
// 	 	array(
// 	     	'key'   => $pgcstatus,
// 	     	'operator' => 'not in',
// 	     	'value' => $filter_list
// 	 		)
	 	)
	);
	$paging = array( 'offset' => $skip, 'page_size' => $per_page );
	$sorting = array( 'key' => 'date_created', 'direction' => 'DESC' );
	$total_count     = 0;
			
	 $entries = GFAPI::get_entries($form_id,  $search_criteria,  $sorting, $paging,   $total_count );
	 
	// Determine if there are more pages
	$has_more_pages = ($this_page * $per_page) < $total_count;
		 	
	// Calculate the total number of pages required
	$total_pages = ceil($total_count / $per_page);
	
	// Initialize previous and next page variables
	$previous_page = $next_page = 0;
	 
	// Determine the previous page number
	if ($this_page > 1)
	{
	    $previous_page = $this_page - 1;
	}
	 
	// Determine the next page number
	if ($this_page < $total_pages)
	{
	    $next_page = $this_page + 1;
	} 
	    		    	
  	$output =  '<div class="Table">
  		<div class="Row"><form method="POST" >
  		Filter by:<br>
  		<input type="checkbox"  name="filter_list[]" value="new"><label for="new">New</label>
  		<input type="checkbox"  name="filter_list[]" value="invited"><label for="invited">Invited</label>
  		<input type="checkbox"  name="filter_list[]" value="deferred"><label for="defered">Deferred</label>
  		
  		<input type="checkbox"  name="filter_list[]" value="member"><label for="member">Member</label>
  		<input type="checkbox"  name="filter_list[]" value="deleted"><label for="deleted">Deleted</label>
  		<button type="submit" name="action" value="filter">Filter</button>
  		</form></div></div>';
  		
   $output .= '<div class="Table">'; // '<form method="post" action="">'; 
   $output .= '<div class="Heading"><div class="Cell0">Select</div><div class="Cell2">Last Name</div><div class="Cell2">First Name</div><div class="Cell">   Status </div></div> ';
   if (empty($entries)) {
 		$output .= 'No entries found.';
	    return $output ;
    }	
   foreach ($entries as $entry) { 
   		$output .= '<div class="Row">';
	 	$output .=  '<div class="Cell0"><form method="POST" >
	                  <input type="hidden" name="id" value =' .$entry['id']. '>	                         
	                    <button type="submit" name="action" value="details">Details</button></form></div>';

		$output .= '	<div class="Cell2">' .  $entry[$gf_fields["lname"]] . '</div>
			<div class="Cell2">' .  $entry[$gf_fields["fname"]] . '</div>
			<div class="Cell">  '.  $entry[$gf_fields["pgcstatus"]] .'</div>
		</div>';
	} 
	$output .= '</form></div>';

	//  Pagination controls 
	if ($previous_page > 0){
	     $output .=  '<div class="Cell1"><form method="POST" >
	                      <input type="hidden" name="this_page" value ="' . $previous_page . '">	                         
	                      <button type="submit" name="action" value="previous">Previous</button></form></div>';
	 }	else {
	  	$output .=  '<div class="Cell1"> </div>';
	 }
	  	
	 if ($next_page > 0){
	     $output .=  '<div class="Cell2"></div><div class="Cell2"></div>
	     				  <div class="Cell0"><form method="POST" >
	                      <input type="hidden" name="this_page" value ="' . $next_page . '">	                         
	                      <button type="submit" name="action" value="next">Next</button></form></div>';
	 }
	  	return $output;
	
	 }
} 
function select_status($id, $s){
	$status_options = ['new', 'invited', 'deferred', 'member', 'deleted' ] ;
	
	$o = '<div class="Cell"><form method="post">
			<input type="hidden" id="eid" name="eid" value=' . $id . '>';
	$o .= '<select name="pgcstatus" id="pgcstatus">';
	foreach ($status_options as $option){

		if( $option === $s ) {
			$o .= '<option value=' . $option . ' selected="selected">' . $option . '</option>';
		} else {
			$o .= '<option value=' . $option . '>' . $option . '</option>';
		}
	}
	$o .= '</select></div><div class="Cell"><button type="submit" name="action" value="update">Update</button></form></div>';
	return $o;
}

// hmmm, would need to give create user apliity... 
function pgc_make_new_user($id, $gf_fields){
	$entry = GFAPI::get_entry($id);	 
	
	$meta_data = array ( "address1" =>   $entry[$gf_fields["address1"]],      
						"city" =>  $entry[$gf_fields["address2"]], "state" =>   $entry[$gf_fields["address3"]],           
						"zip" =>   $entry[$gf_fields["address4"]],   "soaringsociety" =>   $entry[$gf_fields["fssan"]],         
						"cel" =>  $entry[$gf_fields["phone"]], "contact1name" =>    $entry[$gf_fields["sfname"]] . ' ' .  $entry[$gf_fields["slname"]]  );
	
	$user_id = wp_insert_user( array(
  			'user_login' => strtolower(str_replace(' ', '', $entry[$gf_fields["fname"]] . $entry[$gf_fields["lname"]]) ) ,
  			'user_pass' => 'passwordgoeshere',
  			'user_email' =>  $entry[$gf_fields["femail"]],
  			'first_name' => $entry[$gf_fields["fname"]],
  			'last_name' => $entry[$gf_fields["lname"]],
  			'display_name' =>  $entry[$gf_fields["fname"]] . ' ' . $entry[$gf_fields["lname"]],
  			'role' => 'canidate', 
  			'meta_input' => $meta_data
		));
	
	
	
	}
?>

