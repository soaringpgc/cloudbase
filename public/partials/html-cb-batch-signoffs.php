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
function batch_signoff($atts){
    global $wpdb;
  	$charset_collate = $wpdb->get_charset_collate();
  	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
  	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";
  	$wp_users = $wpdb->prefix . "users";
  	$roles = array( 'edit_gc_dues', 'chief_tow', 'chief_flight', 'edit_gc_operations'); 

  	ob_start();  
 	echo ('<form  method="post" id="batch_signoffs">
	 		<input type="hidden" name=action value="update_signoffs"/>
	 		<input id="signoff" type="hidden" name=signoff value=""/>
    	<select name ="signOffToUpdate"  id ="signOffToUpdate" >'); 
 		echo  '<option selected="selected"  value="0">Choose Signoff</option>';	
  	
  		foreach($roles as $role) {
  			if(current_user_can($role)){
	 				$sql = "SELECT * FROM " . $table_types . "  WHERE authority = '" . $role  ."'";
	 				$signoffs= $wpdb->get_results($sql);   	 			  	 			
	 				if($signoffs != NULL ){	
 		  			foreach ($signoffs as $signoff ){
 		  				echo '<option value=' .$signoff->id .'>'. $signoff->signoff_type . '</option>';	
 		  			}	    		
	   		  		}     		   
  			}
  		}
  	echo '</select><br>';	 
  	echo (' <label for="effective_date">Effective Date: </label>
        	<input type = "text"
        		 class = "calendar"
              id = "effective_date"
              size = "10"
              width = "100px"
              value =  '   );   
             echo date('Y-m-d');
         echo (' title = "When signoff becomes effective" 
              name = "effective_date"/>  <br>     '   );          		

     		wp_nonce_field('update_signoffs' );
     		echo '<input type="hidden" name="signatory" id="signatory" value=" ' . get_current_user_id() .'">';	

      		echo( '<input type="submit" value="Update" id="submit">');  	
      		echo ('<input type="button" name="cancel" id="cancel" value="Cancel" /> ');
  		     		
  	 echo(  '	</form>');
    	 echo   '<div id="select_header" class="signoff_state"> Select to update</div>
    			 <div id="batchcontainer" ></div>';    
  	      	 
  	 $output = ob_get_contents();
		 ob_end_clean();
	 return $output ;    	      	
}
?>



