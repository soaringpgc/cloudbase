<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public/partials
 */
?>

<?php 
	function process_squawk_sheet(){
		global $wpdb;
		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
		$user = wp_get_current_user();
		$user_meta = get_userdata( $user->id );
		$display_name = $user_meta->first_name .' '.  $user_meta->last_name;

		if (isset($_POST['id']) && isset($_POST['status'])){
			if($_POST['status'] != ""){
				$wpdb->update($table_squawk, array('status'=>$_POST['status']), array('id' => $_POST['id']) );
			}
		}
		if( !isset($_POST['aircraft']) || ($_POST['aircraft'] == 0)){
			return;
		}
		$equipment_id = $_POST['aircraft'];
		if( !isset($_POST['squawk_problem']) || strlen($_POST['squawk_problem']) == 0){
		   	echo 'You must enter an issue!';
   		    return;			
		}
		$squawk = $_POST['squawk_problem'];
		$squawk=$_POST['squawk_problem'];	
	   	if( !wp_verify_nonce($_POST['_wpnonce'], 'submit_field_duty') ) {
   		    echo 'Did not save because your form seemed to be invalid. Sorry';
   		    return;
   		}  		
   		$squawk_id = $wpdb->get_var("SELECT MAX(squawk_id) FROM " . $table_squawk  );
   		$sql = $wpdb->prepare("SELECT compitition_id FROM { $table_aircraft} WHERE aircraft_id=%d" , $equipment_id);   		
   		$equipment_name = $wpdb->get_var($sql);
   		$data = array( 'squawk_id'=>$squawk_id+1, 'equipment'=>$equipment_id, 'date_entered'=>current_time('mysql'), 'text'=> $squawk, 'user_id'=> $user->id, 'status'=>'New');
 
    	if( $wpdb->insert($table_squawk, $data ) != 1 ){
    		echo 'An error occured, your squawk was not entered. See system Programmer...... ';
   		    return;		    	
    	} else {

			$subject = "PGC SQUAWK (V3)";    	
    		$msg = " Equipment: " .$equipment_name  . "\n\n Reported By: ". $display_name  . "\n\n Date: " . $_POST[sq_date] .  "\n\n Problem Description: " . $squawk;
	    		
    		$sql = "SELECT wp_users.user_email FROM wp_users INNER JOIN wp_usermeta ON wp_users.ID = wp_usermeta.user_id WHERE wp_usermeta.meta_value like '%maintenance_editor%' "; 
			$ops_emails = $wpdb->get_results($sql);
			$to = ""; 
			foreach ( $ops_emails as $m ){
				$to .= $m->user_email .', ';
			};
			$to .= $user_meta->user_email; 
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <webmaster@pgcsoaring.com>' . "\r\n";
	
  			mail($to,$subject,$msg,$headers);
			echo('<p> Yoursquawk has been recorded</p> ');
    			
    	}
	}	
	/*
	  this function should be run only once. It is designed to copy squawks from the old 
	  PGC PDP system to the new Wordpess base squawk system. -dsj
	*/
	function convert_from_pdp(){
		global $wpdb;
		global $PGCi;  // database handle for PDP external db

		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
//    		$old_squwks = $PGCi->get_results('SELECT * FROM pgc_squawk');
   		$old_squawk = mysqli_query($PGCi, 'SELECT * FROM pgc_squawk') or die('Query failed!');	    

  
   		foreach($old_squawk as $squawk){
   			if($squawk['id_entered'] != null){
   				if(!$user = get_user_by('email', $squawk['id_entered' ])){
   					$user_id = 1;
   				} else {
   					$user_id = $user->id;
   				}   				
   				if( preg_match( '/\(.*?\)/',$squawk['sq_equipment'], $registration )){
   				$reg = str_replace(array('(',')'), '', $registration[0]);

   					$sdate = strtotime($squawk['date_entered']);
   					$sql = $wpdb->prepare( "SELECT aircraft_id FROM  {$table_aircraft} WHERE registration = %s",  'N'.$reg );
   					$aircraft_id = $wpdb->get_var($sql);
 					
   					if($aircraft_id  != NULL ){
   						$data = array ( 'squawk_id'=>$squawk['sq_key']  , 'equipment'=> $aircraft_id, 'date_entered'=>date("Y-m-d",$sdate), 
   							'date_updated'=>$squawk['sq_date'], 'user_last_update_id'=>'1', 'text'=>$squawk['sq_issue'], 'comment'=>'' , 
   							'user_id'=>$user_id ,'status'=>$squawk['sq_status']); 
  var_dump($data);  				
//    					$wpdb->insert($table_squawk , $data );   		
					}		 				
   				} 			
   			}
   		}	
	}
		
  function display_squawk_sheet(){	        

	global $wpdb;
	$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
	$user = wp_get_current_user();
	$user_meta = get_userdata( $user->id );
	$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
  	$sql = "Select DISTINCT(a.aircraft_id), a.registration, a.compitition_id FROM {$table_aircraft} a INNER JOIN {$table_type} t on a.aircraft_type=t.type_id WHERE a.valid_until is NULL AND t.title ='Glider'"; 

	$equipemnt = $wpdb->get_results($sql); 

	echo('<h4>Squawk Sheet</h4>');
	echo (' <div>Reported by: ' .$display_name . '</div>');
	echo ('<form id="squawk_sheet"  name="squawk_sheet" method="post" >');
	echo ('<input type="hidden" id="member_id" name="member_id" value="'. $user->id . '"</input> ');
 
	echo ('<label for="aircraft" >Equipment: </label>');
 	echo('<select id="aircraft" name="aircraft" >');
 		echo('<option value="" selected>Select Equipment</option>');  
	foreach($equipemnt as $a){
 		echo('<option value =' .$a->aircraft_id. '>' .$a->compitition_id . ", " .$a->registration  .'</option>' );
	}
 	echo('</select><br>');
 	echo ('<div ><label for="squawk_comment" style="vertical-align:top";  >Squawk: </label>');
 	echo('<textarea id="squawk_problem" name="squawk_problem" rows="6", cols="55"></textarea><div>');
	echo('<input type="submit" value="Submit Squawk" id="submit" name="submit" >'); 
	echo('<div>Please check the previous squawks, below, before submiting to prevent duplicates.</div>');
    wp_nonce_field( 'submit_field_duty' ); 
	echo('</form> ');		
// display/update				
	echo('<h4>Recent Squawks</h4>');
	echo('<div id="squawks" "></div>'); 

  	$sql = "Select s.squawk_id, a.registration, a.compitition_id, s.date_entered, s.status, s.text, s.comment,  s.user_id  FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  		on a.aircraft_id=s.equipment WHERE a.valid_until is NULL AND( s.status != 'Complete' OR s.date_entered > CURRENT_DATE() - INTERVAL 6 MONTH )"; 
	$squawks = $wpdb->get_results($sql); 
	echo('<form><div class="table-container"><div class="table-heading ">Recient Squawks</div>
	<div class="table-row" style="font-size: 12px" >
		<div class="table-col">ID</div>
		<div class="table-col">Equip.</div>
		<div class="table-col">Date</div> 
		<div class="table-col">Squawk</div> 
		<div class="table-col">Member</div>   
		<div class="table-col">Status</div>     		
		</div>');
	foreach($squawks as $squawk){
 		echo('<div class="table-row" style="font-size: 12px" >');
		echo('<div class="table-col"  id="id">'.$squawk->squawk_id.'</div>');
		echo('<div class="table-col">'.$squawk->compitition_id.'</div>');
		$sdate = strtotime($squawk->date_entered);
		echo('<div class="table-col">'.date("Y-m-d",$sdate).'</div>');
		echo('<div class="table-col">'.$squawk->text.'</div>');
			$user = get_user_by('ID',$squawk->user_id );
			$user_meta = get_userdata( $user->id );
			$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
		echo('<div class="table-col">'.$display_name.'</div>');
		if(current_user_can( 'cb_edit_maintenance')){
// 		  echo('<div class="table-col">
// 				<textarea id="squawk_comment" name="squawk_comment" rows="2" cols="20">' .$squawk->comment. '</textarea></div>');
		  echo('<div class="table-col">');
			echo('<select id="squawk_status" name="squawk_status" class="status_change">  ');
			echo('<option value="" selected>'.$squawk->status.'</option>');  
			echo('<option value="New" >New</option>');
			echo('<option value="Open" >Open</option>');
			echo('<option value="Pending" >Pending</option>');	
			echo('<option value="Comlete" >Complete</option>');
		 echo('</select></div></div>');	
		} else {
// 			echo('<div class="table-col">'.$squawk->comment.'</div>');	
			echo('<div class="table-col">'.$squawk->status.'</div></div>');
		}	
	}
	echo('</div></div></form>');
	echo('<h4>Archived Squawks</h4>');
	echo('<div id="archived_squawks" class="viewstop">'); 

  	$sql = "Select s.squawk_id, a.registration, a.compitition_id, s.date_entered, s.status, s.text, s.comment,  s.user_id  FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  		on a.aircraft_id=s.equipment WHERE a.valid_until is NULL AND( s.status != 'Complete' OR s.date_entered > CURRENT_DATE() - INTERVAL 6 MONTH )"; 
	$squawks = $wpdb->get_results($sql); 
	echo('<form><div class="table-container"><div class="table-heading ">Archived Squawks</div>
	<div class="table-row" style="font-size: 12px" >
		<div class="table-col">ID</div>
		<div class="table-col">Equip.</div>
		<div class="table-col">Date</div> 
		<div class="table-col">Squawk</div> 
		<div class="table-col">Member</div>   
		<div class="table-col">Status</div>     		
		</div>');
	foreach($squawks as $squawk){
 		echo('<div class="table-row" style="font-size: 12px" >');
		echo('<div class="table-col">'.$squawk->squawk_id.'</div>');
		echo('<div class="table-col">'.$squawk->compitition_id.'</div>');
		$sdate = strtotime($squawk->date_entered);
		echo('<div class="table-col">'.date("Y-m-d",$sdate).'</div>');
		echo('<div class="table-col">'.$squawk->text.'</div>');
			$user = get_user_by('ID',$squawk->user_id );
			$user_meta = get_userdata( $user->id );
			$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
		echo('<div class="table-col">'.$display_name.'</div>');
		echo('<div class="table-col">'.$squawk->status.'</div></div></div>');
	}
	

}	
 
?>

