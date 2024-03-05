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
	function update_signoff_display($atts ) {
		global $wpdb;
		$args = array('role' => 'subscriber', 'orderby'=>'meta_value', 'meta_key'=>'last_name', 'order' => 'ASC', 'role__not_in'=>'inactive') ; 
    	$pilots = get_users($args);	   	
 		ob_start();?>

   	 	<form  method="post" id="update_signoffs">
   	 		<input type="hidden" name=action value="update_signoffs"/>
   	 		<input type = "hidden"
                 id = "record_id"
                 size = "5"
                 value = ""
                 name = "record_id"/>    
        	<label for="pilot_to_update" >Pilot: </label>
	    	<select name ="pilot_to_update"  id ="pilot_to_update" >
        	<?php
    	  		echo  '<option selected="selected"  value="0">Choose Member</option>';	
    	  		foreach ($pilots as $pilot ){
    	  		// no one can sign of for themselves... update March 2024 one can sign off "self" signoffs. 
    	  		// also update the rest interface at this time. 
//     	  			if( $pilot->id != get_current_user_id()){
    	  				echo '<option value=' .$pilot->ID .'>'. $pilot->last_name . ", " . $pilot->first_name . '</option>';	
//     	  			}	    		
  	   	  		} 
 		  	echo '</select><br>';	
 		  	$table_name = $wpdb->prefix . "cloud_base_signoffs_types";
 		  	$signoffs = $wpdb->get_results("Select * from " . $table_name) ; 
		  	echo ('<div id="signoff_form" class="signoff_state" ><label for="signoff_type" >Signoff:</label>
    			<select name ="signoff_type" id="signoff_type" >');
    	  	echo  '<option selected="selected"  value="0">Choose sign off</option>';	
		  	foreach ($signoffs  as $signoff ){
		  		if( current_user_can( $signoff->authority) ) {	
			 		echo ('<option value="' . $signoff->id . '">' . $signoff->signoff_type . '</option>');
			 	}
		   	}
		    	echo '<input type="hidden" name="signatory" id="signatory" value=" ' . get_current_user_id() .'">';	
		    	echo '<input type="hidden" name="selfsignoff" id="selfsignoff" value="0">';	
			?>
    		</select>
     		<div display:inline-block
    			 visibility: hidden
    			 id = "update_signoff">
    		</div>

    		<div style="visibility: hidden; 
    			 class = "show_delete ";>
     			 <label class = "show_delete"; >Delete</label>
     			 <input class = "show_delete"; type="checkbox" id="delete_signoff" value="true" />
            </div>
            <label for="effective_date">Effective Date: </label>
           	<input type = "text"
           		 class = "calendar"
                 id = "effective_date"
                 size = "10"
                 width = "100px"
                 value = "<?php echo date('Y-m-d') ?>"
                 title = "When signoff becomes effective" 
                 name = "effective_date"/>  <br>                  		
        	<?php   
        		wp_nonce_field('update_signoffs' );
         		echo '<input type="submit" value="Add Signoff" id="submit">';  	
         		echo '<input type="button" name="cancel" id="cancel" value="cancel" />         ';  	
         	?>
    	</form>
		</div>
		<?php		
		// list all of the current signoff a member has. 
       	echo     '   <div id="select_header" class="signoff_state"> Select to update</div>
       				<div id="responsecontainer" ></div></div> ';    
	
  		$output = ob_get_contents();
  		ob_end_clean();
  		return $output;
	}
?>

