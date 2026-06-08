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
	$per_page = 15;
	$page = $_GET["page"] ?? 1;
	$skip = ($page - 1) * $per_page;

// There is no way to guarantee the field ids on the production site will be the same
// as in the development site. The is have to be edited on the live site after it is 
// installed. 
//
	$fname = '1.3';
	$lname = '1.6';
	$phone = '17';
	$pgcstatus= '16';
	$address1 = '5.1';
	$address2 = '5.2';
	$address3 = '5.3';
	$address4 = '5.4';
	$address5 = '5.5';
	$sfname = '3.3';
	$slname = '3.6';
	$fcitizenship = '6';
	$byear = '7';
	$fssan = '11';
	$foccupation = '12';
	$fexpenance = '13';
	$faccident = '15';
	$femail = '4';	

	$dc_atts = shortcode_atts(array('form' => '0', 'id'=>'0'), $atts, 'display_candidates');

	$form_id = esc_html($dc_atts['form']);
	$entry_id = esc_html($dc_atts['id']);
	
    	// Get entries from specific form
    	if (isset($_POST['id'])){

    		$entry = GFAPI::get_entry($_POST['id']);
    		
    		$datetime =  preg_split('/\s+/', $entry['date_created']);
    			
    		$output = '<div class="Table"><div class="table-heading"><div class="Cell3">Name</div><div class="Cell2">Status</div><div class="Cell2">Date</div></div>';   		    		
    		$output .= '<div class="table-row "><div class="Cell3"> ' . $entry[$fname] . ' ' .  $entry[$lname] . '</div><div><div class="Cell2">' .  select_status($entry[$pgcstatus], $entry[$pgcstatus]) .  '</div><div class="Cell2">'. $datetime['0'].'</div></div></div>';
     	    $output .= '<div class="table-row "><div class="Cell3"> Email: </div><div class="Cell4">' .  $entry[$femail] .  '</div></div>';
    	    $output .= '<div class="table-row "><div class="Cell3"> Phone: </div><div class="Cell4">' .  $entry[$phone] .  '</div></div>';
   	        $output .= '<div class="table-row "><div class="Cell3"> Address: </div><div class="Cell4">' .  $entry[$address1] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$address2] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$address3] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$address4] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">  </div><div class="Cell4">' .  $entry[$address5] .  '</div></div>';
     		$output .= '<div class="table-row "><div class="Cell3">Spouse:</div><div class="Cell4">' . $entry[$sfname] . ' ' .  $entry[$slname]  .  '</div></div>';
     		$output .= '<div class="table-row "><div class="Cell3">Citizenship:</div><div class="Cell4">' . $entry[$fcitizenship] . '</div></div>';
     		$output .= '<div class="table-row "><div class="Cell3">Age:</div><div class="Cell4">' . date("Y")-$entry[$byear] .  '</div></div>';
     		$output .= '<div class="table-row "><div class="Cell3">SSA Number:</div><div class="Cell4">' . $entry[$fssan] .  '</div></div>';
     		$output .= '<div class="table-row "><div class="Cell3">Occupation:</div><div class="Cell4">' . $entry[$foccupation] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">Experance:</div><div class="textbox">' . $entry[$fexpenance ] .  '</div></div>';
    		$output .= '<div class="table-row "><div class="Cell3">Incident/Accident:</div><div class="textbox">' . $entry[$faccident ] .  '</div></div>';
//  $output .=  select_status( $entry[$pgcstatus]);
    		$output .= '</div>'; 
    		
			return $output;    	
    	}
    	$search_criteria = array(
    		'field_filters' => array(
        	'mode' => 'any',
        	array(
            	'key'   => $pgcstatus,
            	'value' => '',
        		),
        	array(
            	'key'   => $pgcstatus,
            	'value' => 'new'
        		)
    		)
		);
		$paging = array( 'offset' => $skip, 'page_size' => $per_page );
		$sorting = array( 'key' => 'date_created', 'direction' => 'DEC' );
		$total_count     = 0;
		
		// Determine if there are more pages
		$has_more_pages = ($page * $per_page) < $total_count;
		 

		// Calculate the total number of pages required
		$total_pages = ceil($total_count / $per_page);
 
		// Initialize previous and next page variables
		$previous_page = $next_page = null;
		 
		// Determine the previous page number
		if ($page > 1)
		{
		    $previous_page = $page - 1;
		}
		 
		// Determine the next page number
		if ($page < $total_pages)
		{
		    $next_page = $page + 1;
		}
		
    	$entries = GFAPI::get_entries($form_id,  $search_criteria,  $paging,  $sorting, $total_count );
    	
		if($entry_id != 0){
			$output  = "form data";
		
			return $output;
		} else {
    	
    	if (empty($entries)) {
    	    return 'No entries found.';
    	}
    	
//   		$output =  '<div class="Table"><div class="Row"><div class="Cell">Filter by:</div><div class="Cell">new</div><div class="Cell">invited</div><div class="Cell">defered</div><div class="Cell">member</div><div class="Cell">deleted</div><div  class="Cell">count=' .  $total_count . '</div></div></div>';
    	$output .= '<div class="Table"><form method="post" action="'. site_url() .'/display-candidates/">'; 
    	$output .= '<div class="Heading"><div class="Cell0">Select</div><div class="Cell2">Last Name</div><div class="Cell2">First Name</div><div class="Cell">   Status </div></div> ';
 
     		foreach ($entries as $entry) { $output .= '<div class="Row">
     				<div class="Cell0"> <input type="radio" id="' .$entry['id'].'" name="id" value="' .$entry['id'].'"> </div>
					<div class="Cell2">' .  $entry[$lname] . '</div>
					<div class="Cell2">' .  $entry[$fname] . '</div>
					<div class="Cell">  '.  $entry[$pgcstatus] .'</div>
				</div>';

			} $output .= '<input type="submit" value="details"></form></div>';
		//  Pagination controls 
		if ($next_page > 0){
				$output .=  '<div class="Cell0"><a href="?page=' .  $next_page .' ">Previous</a> </div>
							<div class="Cell2">' . ' ' . '</div>
							<div class="Cell2">' . ' ' . '</div>';
		 }
		 
		 if ($previous_page > 0){
		     $output .=  ' <div class="Cell"> <a href="?page=' . $previous_page . '">Next</a></div>';
		 }



 
 
 
    	
   		return $output;
   		}
	} 
	function select_status($id, $s){
		$status_options = ['new', 'invited', 'defered', 'member', 'deleted' ] ;
		
		$o = '<form method="post" name="select-candidate" action="'. site_url() .'/display-candidates/"><input type="hidden" id="eid" name="eid" value=' . $id . '>';
		$o .= '<select name="pgcstatus" id="pgcstatus">';
		foreach ($status_options as $option){
			if( $option == $s ) {
				$o .= '<option value=' . $option . 'selected>' . $option . '</option>';
			} else {
				$o .= '<option value=' . $option . '>' . $option . '</option>';
 			}
 		}
 		$o .= '</select><input type="submit" value="Change"></form>';
		return $o;
	}
?>

