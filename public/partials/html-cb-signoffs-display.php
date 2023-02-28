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
	 function display_member_signoffs(){
 	    global $wpdb;
     	$charset_collate = $wpdb->get_charset_collate();
     	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
     	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";
 	 	$pilot = wp_get_current_user();
 	 
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
 		if ($pilot != false){
		  	$sql = "SELECT s.id, t.signoff_type, a.display_name, s.date_effective, s.date_expire, t.authority, t.no_fly FROM " . $table_signoffs . " s inner join " . $table_types . " t 
  	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.authority_id WHERE `member_id` =  " . $pilot->id ;
			$pilot_signoffs= $wpdb->get_results($sql);
			echo '<div text-align:"center" > 
				<div class="Table" >         
				<div class="Heading">
        	  	<div  class="Cell2" >Sign Off</div>
       		   	<div  class="Cell1" >Authority</div>
          		<div  class="Cell1">Effec Date</div>
          		<div  class="Cell1">Expire Date</div>
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
					echo '<div class="Cell2"  >'. $signoff->signoff_type  .  '</div>';
					echo '<div class="Cell1"  >'. $signoff->display_name .  '</div>';						
					echo '<div class="Cell1" >'. date("Y-m-d", strtotime($signoff->date_effective)) .  '</div>';
					echo '<div class="Cell1"  >'. date("Y-m-d", strtotime($signoff->date_expire))  .  '</div> </div>';
				}	
			} else {
    	 		echo'<div>No current sign offs  '  ;   	
  			}
  			echo '</div>';
  			$output = ob_get_contents();
  			ob_end_clean();
 			return $output ;
  		} 
 	}
?>



