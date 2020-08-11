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
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_flights_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
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
		
	//	SELECT * FROM wp_cloud_base_flight_sheet WHERE date(start_time) LIKE DATE(now()) ORDER BY start_time DESC
		if (empty($request['flight_id'])){
			$sql = "SELECT * FROM ". $flights_table . " WHERE DATE(date_entered) = DATE(now()) AND valid_until = 0 ORDER BY end_time DESC ";		
			$items = $wpdb->get_results( $sql, OBJECT);		
			if( $wpdb->num_rows > 0 ) {		
				wp_send_json($items, 200 );
			} else {
	 			return rest_ensure_response( 'No flights today' );
			}
		} else {
			$sql = $wpdb->prepare( "SELECT * FROM {$flights_table} WHERE  id =  %d ",  $request['flight_id']);
			$items = $wpdb->get_row( $sql, OBJECT);		
			if( $wpdb->num_rows > 0 ) {		
				wp_send_json($items, 200 );
			} else {
	 			return rest_ensure_response( 'No such flight' );
			}
		}	
	}	
		
	public function cloud_base_flights_post_callback( \WP_REST_Request $request) {
	
		global $wpdb;
		$fee_table = $wpdb->prefix . "cloud_base_tow_fees";	
		$aircraft_table = $wpdb->prefix . "cloud_base_aircraft";	
		$aircraft_type_table = $wpdb->prefix . "cloud_base_aircraft_type";	
		$flight_types_table = $wpdb->prefix . "cloud_base_flight_type";	
		$flight_numbers = $wpdb->prefix . "cloud_base_flight_numbers";			
		$flights_table = $wpdb->prefix . "cloud_base_flight_sheet";	
		
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
			$tow_pilot = null;  
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
			$tug_id = null;  
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
		$sql = "SELECT * FROM {$flight_numbers} WHERE flight_number = (SELECT MAX(flight_number) FROM {$flight_numbers}  WHERE year = YEAR(now()))";
	
		$items = $wpdb->get_row( $sql, OBJECT);		
		if( $wpdb->num_rows > 0 ) { 
			$flight_number = $items->flight_number + 1; 
		} else {
			$flight_number = 1; 
		}
		// add new flight number
		$wpdb->insert($flight_numbers, array('year'=>date("Y"), 'flight_number'=>$flight_number));
		 // add flight. 
		$wpdb->insert($flights_table, array('flight_number'=> $flight_number, 'flight_type'=>$flight_type, 'aircraft_id'=>$aircraft_id,
			'pilot_id'=>$pilot , 'flight_fee_id'=>$altitude ,'total_charge'=>$charge ,'instructor_id'=> $instructor_id ,'tow_plane_id'=>$tug_id,
			'tow_pilot_id'=> $tow_pilot ,'start_time'=>$start_time,'end_time'=>$end_time ,'ip'=>$ip_address ,'notes'=>$flight_notes ,'valid_until'=>'' ));

		wp_send_json(array('flight_number'=>$flight_number), 201 );
	
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_flights_put_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_flights_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
			
}

