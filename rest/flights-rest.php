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
class Cloud_Base_Flights extends Cloud_Base_Rest {

	public function register_routes() {
	              
     $this->resource_path = '/flights' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_put_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_flights_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),        	 		      		
      	  )
      	)
      );	                         
    }
 
// call back for flights:	
	public function cloud_base_flights_get_callback( \WP_REST_Request $request) {
		global $wpdb;
		$fee_table = $wpdb->prefix . "cloud_base_tow_fees";	
		$aircraft_table = $wpdb->prefix . "cloud_base_aircraft";	
		$aircraft_type_table = $wpdb->prefix . "cloud_base_aircraft_type";	
		$flight_types_table = $wpdb->prefix . "cloud_base_flight_type";	
		$flight_numbers = $wpdb->prefix . "cloud_base_flight_numbers";			
		$flights_table = $wpdb->prefix . "cloud_base_flight_sheet";	
		
// 	  $valid_fields = array('id'=>'s.id', 'flight_number'=>'s.flight_number' , 'flight_type'=>'s.flight_type', 'flightyear'=>'s.flightyear',
//  	  'aircraft_id'=>'s.aircraft_id', 'pilot_id'=>'s.pilot_id',	'instructor_id'=>'s.instructor_id', 'tow_plane_id'=>'s.tow_plane_id', 
//  	  'tow_pilot_id'=>'s.tow_pilot_id', 'launch'=>'s.start_time', 'landing'=>'s.end_time', 'compition_id'=>'t.compition_id', 'tow_plane'=>'t.registration', 
//  	   'pilot'=>'a.display_name', 'instructor'=>'a.display_name', 'tow_pilot'=>'a.display_name'  );
    	if (!empty($request['flightyear'])){
    		$flightyear = $request['flightyear'];
    	} else {
    		$flightyear =  date("Y");
    	}

	//	SELECT * FROM wp_cloud_base_flight_sheet WHERE date(start_time) LIKE DATE(now()) ORDER BY start_time DESC
		if (empty($request['flight_number'])){
			$sql = "SELECT * FROM ". $flights_table . " WHERE DATE(date_entered) = DATE(now()) AND valid_until is NULL ORDER BY end_time DESC ";		

			$sql = "SELECT * FROM ". $flights_table . " WHERE valid_until is NULL ORDER BY flight_number DESC ";		

			$items = $wpdb->get_results( $sql, OBJECT);		
			if( $wpdb->num_rows > 0 ) {		
				return new \WP_REST_Response ($items);
			} else {
				return new \WP_REST_Response ($items);
			}
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM {$flights_table} WHERE  id =  %d ",  $request['flight_number']);
			$items = $wpdb->get_row( $sql, OBJECT);		
			if( $wpdb->num_rows > 0 ) {		
			return new \WP_REST_Response ($items);;
			} else {
              return new \WP_REST_Response ('No such flight' );
			}
		}	
	}	
		
	public function cloud_base_flights_post_callback( \WP_REST_Request $request) {	
		global $wpdb;
		$fee_table = $wpdb->prefix . "cloud_base_tow_fees";	
		$aircraft_table = $wpdb->prefix . "cloud_base_aircraft";	
		$aircraft_type_table = $wpdb->prefix . "cloud_base_aircraft_type";	
		$flight_types_table = $wpdb->prefix . "cloud_base_flight_type";	
//		$flight_numbers = $wpdb->prefix . "cloud_base_flight_numbers";			
		$flights_table = $wpdb->prefix . "cloud_base_flight_sheet";	
		
		if(!isset($request['flight_year'])){
			$flightyear = date("Y");
		} else {
			$flightyear = $request['flight_year'];
		}
		
		$ip_address =  $_SERVER['REMOTE_ADDR'];	
			
		if (!empty($request['flight_type_id'])){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT id FROM {$flight_types_table} where id = %d " , $request['flight_type_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 		$flight_type = $sqlreturn->id;
	 		} else {
	  			return new \WP_Error( 'invalid type', esc_html__( 'That flight type does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  		}
	    } else{
			$flight_type = null;  
	  	}
		if (!empty($request['aircraft_id'])){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT * FROM {$aircraft_table} where aircraft_id = %d " , $request['aircraft_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 			$aircraft_id = $sqlreturn->aircraft_id;
	 		} else {
	  			return new \WP_Error( 'invalid type', esc_html__( 'That aircraft does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  		}
	    } else{
			return new \WP_Error( 'Aircraft required', esc_html__( 'Aircraft Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  	}	
		if (!empty($request['pilot_id'])){
		// validates and sanitizes 
			$pilot = $wpdb->prepare(" %d", $request['pilot_id']);
			$user_meta=get_userdata($pilot); 
			$user_roles=$user_meta->roles; 
			if (!in_array("subscriber", $user_roles)){
	  			return new \WP_Error( 'invalid pilot', esc_html__( 'That pilot does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );				
			} 
	    } else{
			return new \WP_Error( 'Pilot required', esc_html__( 'Pilot Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  	}	
		if (!empty($request['instructor_id'])){
		// validates and sanitizes 
			$instructor_id = $wpdb->prepare( "%d", $request['instructor_id']);
			$user_meta=get_userdata($pilot); 
			$user_roles=$user_meta->roles; 
			if (!in_array("cfi_g", $user_roles)){
	  			return new \WP_Error( 'invalid instructor', esc_html__( 'That pilot is not an instructor.', 'my-text-domain' ), array( 'status' => 500 ) );				
			} 
	    } else{
			$instructor_id = null;  
	  	}
		if (!empty($request['tow_pilot_id'])){
		// validates and sanitizes 
			$tow_pilot = $wpdb->prepare( "%d", $request['tow_pilot_id']);
			$user_meta=get_userdata($tow_pilot); 
			$user_roles=$user_meta->roles; 
			if (!in_array("tow_pilot", $user_roles)){
	  			return new \WP_Error( 'invalid tow pilot', esc_html__( 'That pilot is not an tow pilot.', 'my-text-domain' ), array( 'status' => 500 ) );				
			} 
	    } else{
			$tow_pilot = 0;  
	  	}	
		if (!empty($request['tug_id'])){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT * FROM {$aircraft_table} where id = %d " , $request['tug_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 			$tug_id = $sqlreturn->id;
	 		} else {
	  			return new \WP_Error( 'invalid type', esc_html__( 'That aircraft does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  		}
	    } else{
			$tug_id = 0;  
	  	}
		if (!empty($request['start_time'])){
		// validates and sanitizes 
	  		$start_time = $wpdb->prepare("%s" , $request['start_time']);
	    } else{
			$start_time = null;  
	  	}
		if (!empty($request['end_time'])){
		// validates and sanitizes 
	  		$end_time = $wpdb->prepare("%s" , $request['end_time']);
	    } else{
			$end_time = null;  
	  	}
		if (!empty($request['altitude_id'])){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT * FROM {$fee_table} where id = %d " , $request['altitude_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 			$altitude = $sqlreturn->id;
	 			$charge = $sqlreturn->charge + $sqlreturn->hook_up;
	 		} else {
	  			return new \WP_Error( 'invalid alitude', esc_html__( 'That alitude does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  		}
	    } else{
			$altitude = null;  
			$charge = null; 
	  	}
		if (!empty($request['flight_notes'])){
		// validates and sanitizes 
	  		$flight_notes = $wpdb->prepare("%s" , $request['flight_notes']);
		} else {
			$flight_notes = null;  
	  	}
	  		
	// this will select the maximum flight number for this year. If it returns nothing, HAPPY NEW YEAR! 
		$sql = "SELECT MAX(flight_number) FROM {$flights_table} WHERE flightyear = $flightyear";	
		$items = $wpdb->get_var( $sql);		
		if( $items > 0 ) { 
			$flight_number = $items + 1; 
		} else {
			$flight_number = 1; 
		}
		// add flight. 
        $wpdb->insert($flights_table, array( 'flight_number'=>$flight_number, 'flightyear' =>$flightyear, 'aircraft_id'=>$aircraft_id, 'pilot_id'=>$pilot,
         'flight_fee_id'=>$altitude, 'instructor_id'=>$instructor_id,   'tow_plane_id'=>$tug_id, 'tow_pilot_id'=>$tow_pilot, 
         'start_time'=>$start_time, 'end_time'=> $end_time, 'ip'=>$ip_address, 'notes'=>$flight_notes, 'valid_until'=>null  ), 
        	 array('%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s') );
//return new \WP_Error($wpdb->last_error , esc_html__( 'Create failed. ', 'my-text-domain' ), array( 'status' => 400 ) );
			
// read back 
		$sql =  $wpdb->prepare("SELECT * FROM {$flights_table} WHERE flight_number =  %d AND flightyear = %d AND valid_until IS NULL" , $flight_number, $flightyear  );	
		$items = $wpdb->get_row( $sql, OBJECT);	
		if( $wpdb->num_rows > 0 ) {
			return new \WP_REST_Response ($items);
		} else {
    		return new \WP_Error($wpdb->last_error , esc_html__( 'Create failed. ', 'my-text-domain' ), array( 'status' => 400 ) );
		}
	}
	public function cloud_base_flights_put_callback( \WP_REST_Request $request) {
		global $wpdb;
		$fee_table = $wpdb->prefix . "cloud_base_tow_fees";	
		$aircraft_table = $wpdb->prefix . "cloud_base_aircraft";	
		$aircraft_type_table = $wpdb->prefix . "cloud_base_aircraft_type";	
		$flight_types_table = $wpdb->prefix . "cloud_base_flight_type";			
		$flights_table = $wpdb->prefix . "cloud_base_flight_sheet";	
		$flightyear = date("Y");
		$flight_number = $request['flight_number'];		
		$ip_address =  $_SERVER['REMOTE_ADDR'];	
		$change = 0;
				
		if ($flight_number  != null){	
		  $sql = $wpdb->prepare("SELECT * FROM {$flights_table} WHERE `flight_number` = %s AND flightyear = %d AND valid_until IS NULL " ,  $flight_number, date("Y")) ;	
		  $flight = $wpdb->get_row( $sql, OBJECT);
		  if( $wpdb->num_rows > 0 ) {
// was a new value supplied and is it different than what we already have?			
			if (!empty($request['start_time']) && $request['start_time'] != $flight->start_time ){
				$start_time = $request['start_time'];
				$change = 1;
			} else {
				$start_time = $flight->start_time;
			}				
			if (!empty($request['end_time']) && $request['end_time'] != $flight->end_time ){
				$start_time = $request['end_time'];
				$change = 1;
			} else {
				$end_time = $flight->end_time;
			}							
			if (!empty($request['aircraft_id']) && $request['aircraft_id'] != $flight->aircraft_id ){
				$sql = $wpdb->prepare("SELECT * FROM {$aircraft_table} where aircraft_id = %d " , $request['aircraft_id']);
	  			$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  			if( $wpdb->num_rows > 0 ) {	
	 				$aircraft_id = $sqlreturn->aircraft_id;
	 				$change = 1;
	 			} else {
	  				return new \WP_Error( 'invalid_aircraft', esc_html__( 'That aircraft does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	  			}
			} else {
				$aircraft_id = $flight->aircraft_id;
			}				
			if (!empty($request['pilot_id']) && $request['pilot_id'] != $flight->pilot_id ){
				$pilot_id = $wpdb->prepare(" %d", $request['pilot_id']);
				$change = 1;
				$user_meta=get_userdata($pilot_id); 
				$user_roles=$user_meta->roles; 
				if (!in_array("subscriber", $user_roles)){
	  				return new \WP_Error( 'invalid pilot', esc_html__( 'That pilot does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );				
				} 
			} else {
				$pilot_id = $flight->pilot_id;
			}				
			if (!empty($request['instructor_id']) && $request['instructor_id'] != $flight->instructor_id ){
				$instructor_id = $wpdb->prepare(" %d", $request['instructor_id']);
				$change = 1;
				$user_meta=get_userdata($instructor_id); 
				$user_roles=$user_meta->roles; 
    			if (!in_array("cfi_g", $user_roles)){
    	  			return new \WP_Error( 'invalid instructor', esc_html__( 'That pilot is not an instructor.', 'my-text-domain' ), array( 'status' => 400 ) );				
    			} 			} else {
    			$instructor_id = $flight->instructor_id;
			}				

			if (!empty($request['tow_pilot_id']) && $request['tow_pilot_id'] != $flight->tow_pilot_id ){
		// validates and sanitizes 
			$tow_pilot = $wpdb->prepare( "%d", $request['tow_pilot_id']);
			$change = 1;
			$user_meta=get_userdata($tow_pilot); 
			$user_roles=$user_meta->roles; 
			if (!in_array("tow_pilot", $user_roles)){
	  			return new \WP_Error( 'invalid tow pilot', esc_html__( 'That pilot is not an tow pilot.', 'my-text-domain' ), array( 'status' => 400 ) );				
			} 
	    	} else{
				$tow_pilot_id = $flight->tow_pilot_id;  
	  		}	

			if (!empty($request['tug_id']) && $request['tug_id'] != $flight->tug_id ){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT * FROM {$aircraft_table} where id = %d " , $request['tug_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 			$tug_id = $sqlreturn->id;
	 			$change = 1;
	 		} else {
	  			return new \WP_Error( 'invalid aircraft', esc_html__( 'That aircraft does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	  		}
		    } else{
			    $tug_id = $flight->tug_id;  
		  	}
	
			if (!empty($request['flight_fee_id']) && $request['flight_fee_id'] != $flight->flight_fee_id ){
		// validates and sanitizes 
	  		$sql = $wpdb->prepare("SELECT * FROM {$fee_table} where id = %d " , $request['flight_fee_id']);
	  		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  		if( $wpdb->num_rows > 0 ) {	
	 			$flight_fee_id = $sqlreturn->id;
	 			$change = 1;

	 		} else {
	  			return new \WP_Error( 'invalid alitude', esc_html__( 'That alitude does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	  		}
	   	    } else{
				$flight_fee_id = $flight->flight_fee_id;   
	  		}
	  	
  			if (!empty($request['flight_notes']) && $request['flight_notes'] != $flight->flight_notes ){	
			// validates and sanitizes 
	  			$flight_notes = $wpdb->prepare("%s" , $request['flight_notes']);
	  		    $change = 1;
			} else {
				$flight_notes = null;  
		  	}
	
			if ($change != 0 ){
			// mark existing recored as nolonger valid by setting the valin_until to now.
	       		$sql =  $wpdb->prepare("UPDATE {$flights_table} SET `valid_until`= now() WHERE `id` = %d " , $flight->id );
			 	$wpdb->query($sql);
		    // create new record with valid_until = null. 
				$sql =  $wpdb->prepare("INSERT INTO {$flights_table} (flight_number, flightyear, aircraft_id, pilot_id, 
			        flight_fee_id, instructor_id, tow_plane_id, tow_pilot_id, start_time, end_time, ip, notes, valid_until) 
			        VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %s, null) " , 
					$flight_number, $flightyear, $aircraft_id, $pilot_id, $altitude, $instructor_id, $tug_id, $tow_pilot, 
					$start_time, $end_time, $ip_address, $flight_notes );	
				$wpdb->query($sql);			

			  	$sql = $wpdb->prepare("SELECT * FROM {$flights_table} WHERE `flight_number` = %s AND flightyear = %d AND valid_until IS NULL " ,  $flight_number, date("Y")) ;	
				$items = $wpdb->get_row( $sql, OBJECT);	
				if( $wpdb->num_rows > 0 ) {
					return new \WP_REST_Response ($items);
				} else {
		    		return new \WP_Error( 'update Failed', esc_html__( 'Update failed. ', 'my-text-domain' ), array( 'status' => 400 ) );
				}
			    return rest_ensure_response( 'Record Updated= '. $ITEMS->id );
		    } else {
		    	return new \WP_Error( 'nothing changed', esc_html__( 'Updates identical to existing record. ', 'my-text-domain' ), array( 'status' => 400 ) );
		    }	
		  }
         } else{
         	return new \WP_Error( 'missing', esc_html__( 'flight number missing.', 'my-text-domain' ), array( 'status' => 404 ) );		
         }
	}
	public function cloud_base_flights_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
			
}

