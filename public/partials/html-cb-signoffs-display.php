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
		
	 function display_member_signoffs($atts){
 	    global $wpdb;
     	$charset_collate = $wpdb->get_charset_collate();
     	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
     	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";
     	$no_fly= false; 

 	 	$cb_atts = shortcode_atts(array('all' => false, 'no_fly' => '0'), $atts, 'display_signoffs');

 	 	if( $cb_atts['all'] == true ){ // display all signoffs 
 	 	     $args = array('role' => 'subscriber',
     		'orderby' => 'user_nicename',
     		'order' => 'ASC');
     		$pilots = get_users($args);
 	 	} else {
 	 		$pilots = array ( wp_get_current_user()); 	// default just display current pilot
 	 	}
 	 	if( $cb_atts['no_fly'] == true ){ // display only no_fly signoffs. 
 	 	    $no_fly= true; 
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

 	 	$value_label_authority = get_option('cloud_base_authoritys');
 	 	
	 	foreach ($pilots as $pilot ){		 
			if ($no_fly ){
	 			$sql = "SELECT s.id, t.signoff_type, a.display_name, s.date_effective, s.date_expire, t.authority, t.no_fly FROM " . $table_signoffs . " s inner join " . $table_types . " t 
  	 				on s.Signoff_id = t.id inner join wp_users a on a.id = s.authority_id WHERE s.date_expire <= CURDATE() and `member_id` =  " . $pilot->id ;
					
			} else {
	 			$sql = "SELECT s.id, t.signoff_type, a.display_name, s.date_effective, s.date_expire, t.authority, t.no_fly FROM " . $table_signoffs . " s inner join " . $table_types . " t 
  	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.authority_id WHERE `member_id` =  " . $pilot->id ;

			}
			$pilot_signoffs= $wpdb->get_results($sql);
			
			if($cb_atts['no_fly'] && count($pilot_signoffs) === 0 ){
				return;
			}
			
			if ( $cb_atts['all'] == false &&  $cb_atts['no_fly'] == true && count($pilot_signoffs)> 0 ){
 	 				echo'<h3 class="red" > YOU ARE ON THE NO FLY LIST!</h3>';
 	 		}
 	 				
			echo '<div text-align:"center" > 
				<div class="Table" >         
				<div class="Heading">
				<div  class="Cell2" >Pilot</div>
        	  	<div  class="Cell2" >Sign Off</div>
          		<div  class="Cell1">Effec Date</div>
          		<div  class="Cell1">Expire Date</div>
          		<div  class="Cell1">Authority</div>
       			</div> ';
			if( $wpdb->num_rows > 0 ) {
				foreach ($pilot_signoffs as $signoff){  
					if ( $signoff->date_expire <= date("Y-m-d H:i:s") ){
						if ($signoff->no_fly ){
							echo '<div class="Row-red">';
						} else {
							echo '<div class="Row-orange">';
						}
					} else {
						echo '<div class="Row-green">';
					}
					echo '<div class="Cell2"  >'.  $pilot->first_name . ' ' . $pilot->last_name  .  '</div>';
					echo '<div class="Cell2"  >'. $signoff->signoff_type  .  '</div>';					
					echo '<div class="Cell1" >'. date("Y-m-d", strtotime($signoff->date_effective)) .  '</div>';
					echo '<div class="Cell1"  >'. date("Y-m-d", strtotime($signoff->date_expire))  .  '</div>';
					echo '<div class="Cell1"  >'. $value_label_authority[$signoff->authority]  .  '</div> </div>';
				}	
			} else {
    	 		echo'<div>No current sign offs  '  ;   	
  			}
  			echo '</div>';
  			}
  			$output = ob_get_contents();
  			ob_end_clean();
 			return $output ;
//   		} 
 	}
?>



