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
		$user_meta = get_userdata( $user->ID );
		$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
		$requestType = $_SERVER['REQUEST_METHOD'];
		if($requestType == 'GET'){
			return;
		}
		if (isset($_POST['id']) && isset($_POST['status'])){
			if($_POST['status'] != ""){
				$wpdb->update($table_squawk, array('status'=>$_POST['status']), array('id' => $_POST['id']) );
			}
		}
		if( !isset($_POST['aircraft']) || ($_POST['aircraft'] == 0)){
			 	echo '<h3 class="red"> You must select a glider!</h3>';
			return;
		}		
		
		$equipment_id = $_POST['aircraft'];
		if( !isset($_POST['squawk_problem']) || strlen($_POST['squawk_problem']) == 0){
		   	echo '<h3 class="red"> You must enter an issue!</h3>';
   		    return;			
		}
		
		$squawk = $_POST['squawk_problem'];
		$squawk=$_POST['squawk_problem'];	
	   	if( !wp_verify_nonce($_POST['_wpnonce'], 'submit_field_duty') ) {
   		    echo 'Did not save because your form seemed to be invalid. Sorry';
   		    return;
   		}  		
   		$squawk_id = $wpdb->get_var("SELECT MAX(squawk_id) FROM " . $table_squawk  );
   		$sql = $wpdb->prepare("SELECT * FROM {$table_aircraft} WHERE aircraft_id=%d" , $equipment_id);   		
   		$equipment = $wpdb->get_results($sql, OBJECT);

   		$data = array( 'squawk_id'=>$squawk_id+1, 'equipment'=>$equipment_id, 'date_entered'=>current_time('mysql'), 'text'=> $squawk, 'user_id'=> $user->ID, 'status'=>'New');
 
    	if( $wpdb->insert($table_squawk, $data ) != 1 ){
    		echo 'An error occured, your squawk was not entered. See system Programmer...... ';
   		    return;		    	
    	} else {

			$subject = "PGC SQUAWK (V3)";    	
    		$msg = " Equipment: " .$equipment[0]->compitition_id  . "(".  $equipment[0]->registration . ")<br>\n Reported By: ". $display_name  . "<br>\n Date: " . date('Y-M-d') .  "<br>\n Problem Description: " . $squawk;
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
			echo('<p> Your squawk has been recorded</p> ');
    			
    	}
	}	

 function display_squawk_sheet(){	 
//     convert_from_pdp();      

	global $wpdb;
	$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
	$user = wp_get_current_user();
	$user_meta = get_userdata( $user->ID );
	$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
  	$sql = "Select DISTINCT(a.aircraft_id), a.registration, a.compitition_id FROM {$table_aircraft} a INNER JOIN {$table_type} t on a.aircraft_type=t.type_id WHERE a.valid_until is NULL AND t.title ='Glider'"; 
	$equipemnt = $wpdb->get_results($sql); 
	$limit  = 12;	
	isset($_GET['pages']) ? $page = ($_GET['pages']) : $page = 1 ;

	$offset = ($page - 1 ) * $limit; 

	echo('<div class="squawk_body"><h4 class="squawk_text">PGC EQUIPMENT / OPERATIONS SQUAWK (V3)</h4>');
	echo('<div>A Squawk e-mail notification will be sent to the PGC Maintenance Team.</div>');
	echo('<div>Your issue will be tracked  in the PGC maintenance  database  through resolution.</div>');
	echo('<div class="squawk_box">');
	echo (' <div class="squawk_user_name">Reported by: ' .$display_name . '</div>');
	echo ('<form id="squawk_sheet"  name="squawk_sheet" method="post">');
	echo ('<input type="hidden" id="member_id" name="member_id"  value="'. $user->ID . '"</input> ');
 
	echo ('<label for="aircraft" class="squawk_text" >Equipment: </label>');
 	echo('<select class="select_list" id="aircraft" name="aircraft" >');
 		echo('<option value="" selected>Select Equipment</option>');  
	foreach($equipemnt as $a){
 		echo('<option value =' .$a->aircraft_id. '>' .$a->compitition_id . ", " .$a->registration  .'</option>' );
	}
 	echo('</select><br>');
 	echo ('<div ><label for="squawk_comment" style="vertical-align:top" class="squawk_text" >Squawk: </label>');
 	echo('<textarea id="squawk_problem" name="squawk_problem" rows="6", cols="65"></textarea><div>');
	echo('<input type="submit" value="Submit Squawk" id="submit" name="submit" >'); 
	
    wp_nonce_field( 'submit_field_duty' ); 
	echo('</form></div> ');	
	echo('<div class="squawk_text">Please check the previous squawks, below, before submiting to prevent duplicates.</div>');	

// display/update				

  	$sql = "Select s.squawk_id, a.registration, a.compitition_id, s.date_entered, s.status, s.text, s.comment,  s.user_id  FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  		on a.aircraft_id=s.equipment  WHERE a.valid_until is NULL AND s.status != 'COMPLETED' ORDER BY s.date_entered DESC
  		LIMIT ". $limit ." OFFSET " . $offset; 

	$squawks = $wpdb->get_results($sql); 
	$open =  $wpdb->num_rows;
echo('<div class="centered"');
	echo('<div class="table-container"><div class="table-heading squawk_body">Recient Squawks</div>
	<div class="table-row " >
		<div class="table-col">ID</div>
		<div class="table-col">Equip.</div>
		<div class="table-col">Date</div> 
		<div class="table-col">Squawk</div> 
		<div class="table-col">Member</div>   
		<div class="table-col">Status</div>     		
		</div>');
	foreach($squawks as $squawk){
 		echo('<div class="table-row squawk_text" >');
		echo('<div class="table-col"  id="id">'.$squawk->squawk_id.'</div>');
		echo('<div class="table-col">'.$squawk->compitition_id.'</div>');
		$sdate = strtotime($squawk->date_entered);
		echo('<div class="table-col" style="white-space:nowrap">'.date("Y-m-d",$sdate).'</div>');
		echo('<div class="table-col">'.$squawk->text.'</div>');
			$user = get_user_by('ID',$squawk->user_id );
			$user_meta = get_userdata( $user->ID );
			$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
		echo('<div class="table-col" style="white-space:nowrap">'.$display_name.'</div>');
		if(current_user_can( 'cb_edit_maintenance')){
// 		  echo('<div class="table-col">
// 				<textarea id="squawk_comment" name="squawk_comment" rows="2" cols="20">' .$squawk->comment. '</textarea></div>');
		  echo('<div class="table-col">');
			echo('<select id="squawk_status" name="squawk_status" class="status_change">  ');
			echo('<option value="" selected>'.$squawk->status.'</option>');  
			echo('<option value="NEW" >NEW</option>');
			echo('<option value="OPEN" >OPEN</option>');
			echo('<option value="PENDING" >PENDING</option>');	
			echo('<option value="COMPLETED" >COMPLETED</option>');
		 echo('</select></div></div>');	
		} else {
// 			echo('<div class="table-col">'.$squawk->comment.'</div>');	
			echo('<div class="table-col">'.$squawk->status.'</div></div>');
		}		
	}
	$closed=$limit;
// 	echo('</div></div></form>');
// 	echo('<div id="archived_squawks" class="viewstop">'); 
	if ($open < $limit  ){	
	  	$sql = "Select count(s.squawk_id) FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  		on a.aircraft_id=s.equipment  WHERE a.valid_until is NULL AND s.status != 'COMPLETED' "; 
	 	$open_count = $wpdb->get_var($sql);
	 	
	 	$offset = ($page - 1 ) * $limit - $open_count; 
	 	$offset < 0 ? $offset=0: $offset=$offset;

	  	$sql = "Select s.squawk_id, a.registration, a.compitition_id, s.date_entered, s.status, s.text, s.comment,  s.user_id  FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
	  		on a.aircraft_id=s.equipment  WHERE a.valid_until is NULL AND s.status = 'COMPLETED' ORDER BY s.date_entered DESC
	  		LIMIT ". $limit ." OFFSET " . $offset; 
		$closed_squawks = $wpdb->get_results($sql); 
		$closed =  $wpdb->num_rows;		
		foreach($closed_squawks as $squawk){
 			echo('<div class="table-row squawk_text" >');
			echo('<div class="table-col"  id="id">'.$squawk->squawk_id.'</div>');
			echo('<div class="table-col">'.$squawk->compitition_id.'</div>');
			$sdate = strtotime($squawk->date_entered);
			echo('<div class="table-col" style="white-space:nowrap">'.date("Y-m-d",$sdate).'</div>');
			echo('<div class="table-col">'.$squawk->text.'</div>');
				$user = get_user_by('ID',$squawk->user_id );
				$user_meta = get_userdata( $user->ID );
				$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
			echo('<div class="table-col" style="white-space:nowrap">'.$display_name.'</div>');
			if(current_user_can( 'cb_edit_maintenance')){
// 			  echo('<div class="table-col">
// 					<textarea id="squawk_comment" name="squawk_comment" rows="2" cols="20">' .$squawk->comment. '</textarea></div>');
			  echo('<div class="table-col">');
				echo('<select id="squawk_status" name="squawk_status" class="status_change">  ');
				echo('<option value="" selected>'.$squawk->status.'</option>');  
				echo('<option value="NEW" >NEW</option>');
				echo('<option value="OPEN" >OPEN</option>');
				echo('<option value="PENDING" >PENDING</option>');	
				echo('<option value="COMPLETED" >COMPLETED</option>');
			 echo('</select></div></div>');	
			} else {
				echo('<div class="table-col">'.$squawk->status.'</div></div>');
			}		
		}
	}
	
 	echo('</div></div>');

	$next= $page+1;
	$previous = $page-1;
	
// 	echo('<div id=page_number> Page '.$page .' </div>');
	echo ('<div class="nextPreviousButtons" > '); 	
	if ( $previous > 0 ){
	 	echo('<a class="buttons previous round" href="?pages=' .$previous. '">&#8249;</a>');
	}
	if ( $limit <= $closed ){
	 	echo('<a class="buttons next round" href="?pages=' .$next. '">&#8250;</a>');
	}
	echo('</div></div>' );
	
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
				$user = get_user_by('email', $squawk['id_entered' ]);
   				if(!$user){
   					$user_id = 1;
   				} else {
   					$user_id = $user->ID;
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
 				
      					$wpdb->insert($table_squawk , $data );   		
					}		 				
   				} 			
   			}
   		}	
	}
 
?>

