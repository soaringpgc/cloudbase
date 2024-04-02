<?php
/**
 * The rest functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and examples to create your REST access
 * methods. Don't forget to validate and sanatize incoming data!
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public
 * @author     Your Name <email@example.com>
 */

class Cloud_Base_Squawks extends Cloud_Base_Rest {

	public function register_routes() {
	
    $this->resource_path = '/squawks' . '(?:/(?P<id>[\d]+))?';         
       
    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_squawks_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),        	 		      		
      	  )
      	)
      );	                            
             
    }
	
// call back for squawks:	
	public function cloud_base_squawks_get_callback( \WP_REST_Request $request) {
	// id - return specific squawk by id
	// squawk_id  - return specific squake by squawk_id
	// aircraft_id all squawks aginst specific aircraft
	// captian_id squawks a captian is responsible for
	// type_id squawks against equipment type
	// compleated -- compleated squawks -- all of the above do not return compleated squawks 
	// default all squawks not compleated. 
		global $wpdb;
		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
		$wp_users = $wpdb->prefix . "users";
		isset($request['pages']) ? $page = ($request['pages']) : $page = 1 ;
		isset($request['limit']) ? $limit = ($request['limit']) : $limit = 12 ;
		$offset = ($page - 1 ) * $limit; 


	  $valid_fields = array('id'=>'s.id', 'squawk_id'=>'s.squawk_id' , 'aircraft_id'=>'s.aircraft_id', 'captian_id'=>'s.captian_id',
 	  'type_id'=>'s.type_id', 'status'=>'s.status',	'user_id'=>'s.user_id', 'date_entered'=>'s.date_entered' );

	  $sql = "SELECT  s.id, s.flight_number, s.flight_type, s.aircraft_id, a.compitition_id as glider, y.title as f_type, s.pilot_id, s.instructor_id,  s.tow_pilot_id, 
	  p.display_name as pilot, i.display_name as instructor, t.display_name as tow_pilot FROM wp_cloud_base_flight_sheet s 
	  INNER JOIN  ". $wp_users ." p ON s.pilot_id = p.id LEFT JOIN  ". $wp_users ." i ON  s.instructor_id = i.id LEFT JOIN  ". $wp_users ." t ON  s.tow_pilot_id = t.id 
	  INNER JOIN wp_cloud_base_flight_type y ON s.flight_type=y.id INNER JOIN wp_cloud_base_aircraft a ON s.aircraft_id=a.aircraft_id" ;

		if(isset($request['id'])){  // by squawk record id 
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment INNER JOIN {$table_type } t on a.aircraft_type = t.type_id WHERE s.id = " . $request['id'] ; 					
		} elseif(isset($request['squawk_id'])){ // by squawk id 
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE s.squawk_id = " . $request['squawk_id'] ; 					
		} elseif(isset($request['aircraft_id'])){ // by_ equipment id 
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE a.aircraft_id = " . $request['aircraft_id'] ; 					
		} elseif(isset($request['captian_id'])){// by captian id 
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE a.captian_id = " . $request['captian_id'] ; 					
		} elseif(isset($request['type_id'])){// by type id
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE t.type_id = " . $request['type_id '] ; 	
		} elseif(isset($request['compleated'])){// by type id
			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE t.type_id = " . $request['type_id '] ; 	
		} else  {		
 			$sql = "Select DISTINCT s.id, s.squawk_id, a.registration, a.aircraft_id, a.compitition_id, a.captian_id, s.date_entered, s.status, s.text, s.comment,  s.user_id, t.type_id   FROM {$table_aircraft} a INNER JOIN {$table_squawk} s 
  			on a.aircraft_id=s.equipment  INNER JOIN {$table_type } t on a.aircraft_type = t.type_id  WHERE a.valid_until is NULL AND s.status != 'COMPLETED' ORDER BY s.date_entered DESC
  			LIMIT ". $limit ." OFFSET " . $offset; 	
		}
// 		return new \WP_REST_Response ( $sql );		
// merge in the captians of the aircraft. 
		$items = $wpdb->get_results($sql); 
		

			foreach( $items as $key =>  $item ){	
				$items[$key]->captian_name = $this->cb_member_info($item->captian_id)->name ;						
// 				if( $item->captian_id != null  ){
// 			    	$user_meta = get_userdata( $item->captian_id );
// 					$items[$key]->captian_name =  $user_meta->first_name .' '.  $user_meta->last_name;			
// 				} else {
// 					$items[$key]->captian_name = "";
// 				}
			}
			return new \WP_REST_Response ( $items );
	}	
	public function cloud_base_squawks_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
		$user = wp_get_current_user();
// 		$user_meta = get_userdata( $user->ID );
// 		$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
		
		$display_name = $this->cb_member_info($user->ID )->name ;	
// 		$requestType = $_SERVER['REQUEST_METHOD'];

		if( !isset($request['aircraft']) || ($request['aircraft'] == 0)){
			 return new \WP_Error( 'Missing data', esc_html__( 'Equipment id missing', 'my-text-domain' ), array( 'status' => 400 ) );  		
		}				
		$equipment_id = $request['aircraft'];
		if( !isset($request['squawk_problem']) || strlen($request['squawk_problem']) == 0){
			return new \WP_Error( 'Missing data', esc_html__( 'missing data - enter an issue.', 'my-text-domain' ), array( 'status' => 400 ) );  			
		}		
		$squawk = $request['squawk_problem'];	
   		$squawk_id = $wpdb->get_var("SELECT MAX(squawk_id) FROM " . $table_squawk  );
   		$sql = $wpdb->prepare("SELECT * FROM {$table_aircraft} WHERE aircraft_id=%d" , $equipment_id);   		
   		$equipment = $wpdb->get_results($sql, OBJECT);
 
 		$to = "";   		
//    		if ( $equipment[0]->captian_id != null) {   		
//    			$captian_id = $equipment[0]->captian_id;    		
//    			$captian = get_users_by('ID', $captian_id );	
// 			$captian_meta = get_userdata( $member->ID );
// 			$captian_name =  $captian_meta->first_name .' '.  $captian_meta->last_name ;
//  			$captian_email = $captian_meta->user_email;
//  			$to .= $captian_email;
// 		} 
		
		$to .= $this->cb_member_info($equipment[0]->captian_id )->email ;	
				
		if(!isset($request['notify']) || $equipment[0]->aircraft_type=2 ) {
			foreach ( $ops_emails as $m ){
				$to .= $m->user_email .', ';
			};
		}		
   		$data = array( 'squawk_id'=>$squawk_id+1, 'equipment'=>$equipment_id, 'date_entered'=>current_time('mysql'), 'text'=> $squawk, 'user_id'=> $user->ID, 'status'=>'New');
		$members = get_users(['role__in' => 'maintenance_editor'] );	
		$to = ""; 		
		foreach( $members as $member ){	
		    $user_meta = get_userdata( $member->ID );
// 		    $users[ $member->ID]=  $user_meta->first_name .' '.  $user_meta->last_name ;						
			$to .= $user_meta->user_email .', ';
		};
 
    	if( $wpdb->insert($table_squawk, $data ) != 1 ){
    		return new \WP_Error( 'Insert Failed ', esc_html__( 'Unable to create record', 'my-text-domain' ), array( 'status' => 400 ) );  		   	
    	} else {
    		$sql = "SELECT * FROM {$table_squawk} WHERE id=" . $wpdb->insert_id;   		
    		$wpdb->get_results($sql);

			$subject = "PGC SQUAWK (V3)";    	
    		$msg = " Equipment: " .$equipment[0]->compitition_id  . "(".  $equipment[0]->registration . ")<br>\n Reported By: ". $display_name  . "<br>\n Date: " . date('Y-M-d') .  "<br>\n Problem Description: " . $squawk;
// 
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <webmaster@pgcsoaring.com>' . "\r\n";
	
  			mail($to,$subject,$msg,$headers);			
			return new \WP_REST_Response ( $items );	
		}
	}
	public function cloud_base_squawks_put_edit_callback( \WP_REST_Request $request) {

		global $wpdb;
		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
 
		if (isset($request['id']) && isset($request['status'])){
			if($request['status'] != ""){
				$items = $wpdb->update($table_squawk, array('status'=>$request['status']), array('squawk_id' => $request['id']) );
			}
			if($items != 1 ){
				return new \WP_REST_Response ($wpdb->last_error);
				
			}
			return new \WP_REST_Response ($items);
		} else {
    		return new \WP_Error( 'Missing data', esc_html__( 'missing data.', 'my-text-domain' ), array( 'status' => 404 ) );  		
		}
	}
	public function cloud_base_squawks_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
		global $wpdb;
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
		$id =  $request['id'];
		if ($id  != null){	
			$sql = $wpdb->prepare("SELECT * FROM {$table_squawk} WHERE `squawk_id` = %d " ,  $id );	
			$items = $wpdb->get_results( $sql );
	
			if( $wpdb->num_rows > 0 ) {
				if ($wpdb->delete($table_squawk, array('squawk_id'=> $id )) ==1 ){
					return rest_ensure_response( 'Deleted');
				} else {
					return new \WP_Error( $wpdb->last_error, esc_html__( 'Error.', 'my-text-domain' ), array( 'status' => 404 ) );
				}
			}
		} else{
			return new \WP_Error( 'ID missing', esc_html__( 'Id missing.', 'my-text-domain' ), array( 'status' => 400 ) );
		}
	}
}

