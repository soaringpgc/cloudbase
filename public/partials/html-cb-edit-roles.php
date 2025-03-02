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

 	 if ($_SERVER["REQUEST_METHOD"] == "POST") {      
//  	 	// verify it is really from our form. 
 	 	if (isset($_POST['registerpgc_nonce']) && wp_verify_nonce($_POST[ 'registerpgc_nonce'], 'registerpgc_action')) {
//     	// Process form data
	 	    $member_id = sanitize_text_field($_POST["member_id"]);
 	 	    $user =  get_user_by('id' , $member_id); 
    		if( isset($_POST['member_status']) && (current_user_can('cb_edit_dues') )){
    		 	$status_role = sanitize_text_field($_POST["member_status"]);    		 	
     		 	switch ($status_role) {
       		 		case "inactive":
       		 			foreach ($user->roles as $role  ) {  // to prevent accidental remove of admins! 
       		 				if($role != "admin"){
       		 					$user->remove_role($role );
       		 				}
       		 			}
       		 		    if($role != "admin"){            // can't be admin and inactive. 
       		 				$user->add_role("inactive"); 
       		 			}
      		 			break;
    		 		case "subscriber":
    		 			$user->remove_role("candidate" );
    		 			$user->remove_role("inactive" );
    		 			$user->add_role("subscriber");   
    		 			break;
 					case "candidate":   // probably never used, but cover all bases. 
 					    $user->remove_role("subscriber" );
    		 			$user->remove_role("inactive" );
    		 			$user->add_role("candidate");   
     		 			break;  
     		 		default :     // for those that don't even want to be on the inactive list. 
     		 			$user->remove_role("candidate" );
    		 			$user->remove_role("inactive" );
    		 			$user->remove_role("subscriber");   
				}  		
    		}
    		if( isset($_POST['duty']) && (current_user_can('cb_edit_operations') )){
    		 	$field_role  = sanitize_text_field($_POST["duty"]); 
    		 	switch($field_role  ){
    		 		case "exempt": 
    		 			$user->remove_role("assistant_field_manager");   
    		 			$user->remove_role("field_manager");   	    		 		
    		 			break;
    		 		case "fm":
    		 			$user->remove_role("assistant_field_manager");   
    		 			$user->add_role("field_manager");   
    		 			break;
    		 		case "afm":
    		 			$user->add_role("assistant_field_manager"); 
   		 				$user->remove_role("field_manager");   
    		 			break; 
    		 	}  		
    		}
    		if( current_user_can('cb_edit_instruction' )){
    			if(isset($_POST['cfi_g']) ) { // if isset then it must be yes, not set no. 
    				$user->add_role("cfi_g");   
    			} else {
    				$user->remove_role("cfi_g");  
     			}
    		}
    		if( current_user_can('cb_edit_towpilot' )){    			
    			if(isset($_POST['towpilot'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("cfi_g");   
    			} else {
    				$user->remove_role("cfi_g");  
    			}
    		}
    		if( current_user_can('cb_edit_dues' )){    			
    			if(isset($_POST['board'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("board_member");   
    			} else {
    				$user->remove_role("board_member");  
    			}
    		}
    		if( current_user_can('update_core' )){    			
    			if(isset($_POST['chief_flight'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("chief_flight");   
    			} else {
    				$user->remove_role("chief_flight");   
    			}
    			if(isset($_POST['chief_tow'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("chief_tow");   
    			} else {
    				$user->remove_role("chief_tow");   
    			}
    			if(isset($_POST['cfig_scheduler'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("cfig_scheduler");   
    			} else {
    				$user->remove_role("cfig_scheduler");    
    			}	
    			if(isset($_POST['tow_scheduler'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("tow_scheduler");   
    			} else {
    				$user->remove_role("tow_scheduler");   
    			}
    			if(isset($_POST['schedule_assist'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("schedule_assist");   
    			} else {
    				$user->remove_role("schedule_assist");    
    			}	
    			if(isset($_POST['treasurer'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("treasurer");   
    			} else {
    				$user->remove_role("treasurer");    
    			}	
    			if(isset($_POST['operations'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("operations");   
    			} else {
    				$user->remove_role("operations");   
    			}
    			if(isset($_POST['chief_of_ops'])) {    // if isset then it must be yes, not set no. 
    				$user->add_role("chief_of_ops");   
    			} else {
    				$user->remove_role("chief_of_ops");    
    			}	
    		}
 		} else {
 			echo ("nonce failure");
 			die();
 		}
 	}
?>

<?php

	global $wpdb;
	$args = array('role' => 'subscriber', 'orderby'=>'meta_value', 'meta_key'=>'last_name', 'order' => 'ASC') ; 
    $pilots = get_users($args);	   	
	?>

   	 <form  action="?"  method="post" id="update_signoffs">
   	 	<input type="hidden" name=action value="update_roles"/>
   	 	<input type = "hidden"
              id = "record_id"
              size = "5"
              value = ""
              name = "record_id"/>    
     	<label for="pilot_to_update" >Pilot: </label>
	 	<select name ="pilotid"  id ="pilotid" >
     	<?php
      		echo  '<option selected="selected"  value="0">Choose Member</option>';	
      		foreach ($pilots as $pilot ){
      				echo '<option value=' .$pilot->ID .'>'. $pilot->last_name . ", " . $pilot->first_name . '</option>';		    		
  	   		} 
 		echo '</select><br>';	
 		if (current_user_can('cb_edit_dues')){
 			$args = array('role__not_in' => ['subscriber'], 'orderby'=>'meta_value', 'meta_key'=>'last_name', 'order' => 'ASC') ; 
    		$pilots = get_users($args);	   	
 			?>
   	 		<input type = "hidden"
        	      id = "record_id"
        	      size = "5"
        	      value = ""
        	      name = "record_id"/>    
        	<label for="pilot_to_update" >Inactive Member: </label>
	    	<select name ="ipilotid"  id ="ipilotid" >
        	<?php
    			echo  '<option selected="selected"  value="0">Choose Inactive</option>';	
    			foreach ($pilots as $pilot ){
    					echo '<option value=' .$pilot->ID .'>'. $pilot->last_name . ", " . $pilot->first_name . '</option>';		    		
  	   			} 
 			echo '</select>';
 		 }	
 		 echo('</form><br><br>');
?>
 
<form id=”registerpgc"  name=”registerpgc" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
<!-- 
    <p>
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required>
    </p>
 -->
<div id="role_collections">
	<form id="role_update">
		<input type="hidden" id="member_id" name="member_id"> 		
		<?php 
		if (current_user_can('update_core')){
		 	echo( '<fieldset>
		 		<legend> Managers :</legend>
 				<div class="some-class">		 	
				<div class="some-class">
					<label for="treasurer"><input type="checkbox" id="treasurer" name="treasurer" value="yes" />Treasurer</label>
					<label for="chief_of_ops"><input type="checkbox" id="chief_of_ops" name="chief_of_ops" value="yes" />Chief ofOps</label>
					<label for="operations"><input type="checkbox" id="operations" name="operations" value="yes" />Operations</label>
					<label for="schedule_assist"><input type="checkbox" id="schedule_assist" name="schedule_assist" value="yes" />Schedule Asst.</label>
					<br>
					<label for="chief_flight"><input type="checkbox" id="chief_flight" name="chief_flight" value="yes" />Chief_CFI-g.</label>
					<label for="chief_tow"><input type="checkbox" id="chief_tow" name="chief_tow" value="yes" />Chief Tow</label>
					<label for="cfig_scheduler"><input type="checkbox" id="cfig_scheduler" name="cfig_scheduler" value="yes" />CFI Scheduler</label>
					<label for="tow_scheduler"><input type="checkbox" id="tow_scheduler" name="tow_scheduler" value="yes" />Tow Scheduler</label>
				</div>
			</fieldset><hr>');
		}
		if (current_user_can('cb_edit_dues')){
		 	echo( '<fieldset>
		 		<legend> Member status:</legend>
		 		<div class="some-class">
				<label for="candidate" ><input type="radio" id="member_status" name="member_status" value="candidate" size="10"/>Candidate</label>
				<label for="member"><input type="radio" id="member_status" name="member_status" value="subscriber"/>Active</label> 
				<label for="inactive"><input type="radio" id="member_status" name="member_status" value="inactive"/>Inactive</label> 
				<label for="none"><input type="radio" id="member_status" name="member_status" value="none"/>None</label>		
			</div>
			</fieldset><hr>');
		}
		if(current_user_can( 'cb_edit_operations')){
			echo( '<fieldset><legend> Field Role:</legend>
				<div class="some-class">
					<label for="mf" ><input type="radio" id="duty" name="duty" value="fm" size="10"/>FM</label>
					<label for="afm"><input type="radio" id="duty" name="duty" value="afm"/>AFM</label> 
					<label for="exempt"><input type="radio" id="duty" name="duty" value="exempt"/>Exempt</label>		
				</div>
				</fieldset><hr>');		
		}
		if(current_user_can( 'cb_edit_instruction')){
			echo( '<fieldset><legend>CFI-G:</legend>
				<div class="some-class">
					<label for="cfi_g"><input type="checkbox" id="cfi_g" name="cfi_g" value="yes"/>CFI-G</label> 
				</div>
				</fieldset><hr>');		
		}		
		if(current_user_can( 'cb_edit_towpilot')){
			echo( '<fieldset><legend>Tow Pilot:</legend>
				<div class="some-class">
					<label for="towpilot"><input type="checkbox" id="towpilot" name="towpilot" value="yes" />Tow Pilot</label>
				</div>
				</fieldset><hr>');		
		}
		if(current_user_can( 'cb_edit_dues')){
			echo( '<fieldset><legend>Board Member:</legend>
				<div class="some-class">
					<label for="board"><input type="checkbox" id="board" name="board" value="yes" />Board</label>
				</div>
				</fieldset><hr>');		
		}				
		?>	
	<br>
    <p>
        <input type="submit" name="submit" value="Submit">
    </p>
    	<?php wp_nonce_field('registerpgc_action', 'registerpgc_nonce'); ?>

	</form>



<?php
        
        
?>