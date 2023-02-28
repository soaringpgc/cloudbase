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
	 function signoff_summery($atts){
 	    global $wpdb;
     	$charset_collate = $wpdb->get_charset_collate();
     	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
     	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";
     	
     	$cb_atts = shortcode_atts(array('no_fly' => '0','signoff' => ''), $atts, 'signoff_summery');

	 	if( $cb_atts['signoff'] != '' ){ // display all signoffs 
 	 		$sql = $wpdb->prepare( "SELECT a.user_nicename, a.id, t.signoff_type,  s.date_expire  FROM {$table_signoffs } s inner join {$table_types } t 
  	 		on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id  WHERE  t.signoff_type  = %s AND s.date_expire < current_date() ORDER BY a.user_nicename ", esc_HTML__($cb_atts['signoff']));	 	
			$header = "<h3>" . get_option ('glider_club_short_name'). " Sign Off Summey for, " . $cb_atts['signoff'] . " as of ". date("Y/m/d"). "</h3>";

 	 	} else { 	 		
  	 		$sql = $wpdb->prepare( "SELECT a.user_nicename, a.id, t.signoff_type,  s.date_expire  FROM {$table_signoffs } s inner join {$table_types } t 
  	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id  WHERE t.no_fly  = %d AND s.date_expire < current_date() ORDER BY a.user_nicename ", $cb_atts['no_fly']);
			if(  $cb_atts['no_fly'] == '1' ){
				$header ="<h3 class='red'>" . get_option ('glider_club_short_name'). " NO Fly List as of : ". date("Y/m/d"). "</h3>";
			} else {
				$header = "<h3>" . get_option ('glider_club_short_name'). " Fly List as of : ". date("Y/m/d"). "</h3>";
			}
 	 	}
 	 	ob_start();
       
	/**
	 *
	 * @ntfs     (note to future self))
	 *       
	 * 	The sql statment is selecting a pilots signoffs from the _member_signoffs table(s). That table contains the pilot id, id of the sign off and 
	 *  the id of the authoritive person who signed it off. to get the text of the signoff id we just join with the _signoff_types table(t) to get the 
	 *  name of the person who signed off we join wiht the wp_users table(a). 
	 *
	 *
	 */	 		
				  	 			
		$raw_no_fly_list= $wpdb->get_results($sql);

		echo $header;
		
		echo '<div text-align:"center"> <div class="Table" >         
			<div class="Heading">
          	<div  class="Cell2" >Member</div>
       	   	<div  class="Cell2" >Sign-off</div>
        	<div  class="Cell2">Expire Date</div>
       		</div> ';
       	$previous = "";
		if( $wpdb->num_rows > 0 ) {
			foreach ($raw_no_fly_list as $no_fly){  
				$member_name = get_user_by('id', $no_fly->id);
				if ( !in_array('inactive', $member_name->roles, true )){
					echo '<div class="Row">';			
					if (	$previous != $no_fly->id)	{				
						echo '<div class="Cell"  >' . $member_name->last_name . ", " . $member_name->first_name .  '</div>';
						$previous = $no_fly->id;
					}	else {
						echo '<div class="Cell2"  ></div>';
					}					
					echo '<div class="Cell2"  >'. $no_fly->signoff_type .  '</div>';							
					echo '<div class="Cell1"  >'. date("Y-m-d", strtotime($no_fly->date_expire))  .  '</div> </div>';
				}
			}	
		} 
  		echo '</div>';
  		echo '<br>';
  		$output = ob_get_contents();
  		ob_end_clean();
 		return $output ;

 	}
?>



