<?php
/**
 * The rest functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 */

/**
 * The REST functionality of the plugin.
 *
 * Defines the plugin name, version, and examples to create your REST access
 * methods. Don't forget to validate and sanatize incoming data!
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 * @author     Dave Johnson <johnson.s.david@pm.me>
 */

class Cloud_Base_Aircraft extends Cloud_Base_Rest {

	public function register_routes() {
	
	  $this->resource_path = '/aircraft' . '(?:/(?P<aircraft_id>[\d]+))?';

      register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),
         	'args' => array('id'=> array('type'=>'integer', 'required'=> false, 'sanitize_callback'=> 'absint'))), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_aircraft_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	
	}
	
// call back for aircraft:	
	public function cloud_base_aircraft_get_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  
	  $no_audit = "WHERE s.valid_until = 0 ";
	  
	  if (!empty($request['audit'])){
		 $no_audit = "WHERE s.valid_until > -1";
	  } 

	  $aircraft_id = '';
	  if (!empty($request['aircraft_id'])){
		$aircraft_id = $wpdb->prepare(' AND aircraft_id = %s ' , $request['aircraft_id']);
	  } 

	  $type = '';
	  if (!empty($request['type'])){
		$type = $wpdb->prepare(' AND t.title = %s ' , $request['type']);
	  } 

	  $captian = '';
	  if (!empty($request['captian'])){
		$captian = $wpdb->prepare(' AND captian_id = %d ' , $request['captian']);
	  } 
	  $compitition = '';
	  if (!empty($request['compitition'])){
		$compitition = $wpdb->prepare('AND compitition_id = %s  ' , $request['compitition']);
	  } 
	  
	  $sql = "SELECT s.aircraft_id, s.registration, s.compitition_id, s.status, t.title AS type FROM {$table_name}  s inner join 
			{$table_type} t on s.aircraft_type=t.id " . $no_audit . $aircraft_id . $type . $captian .  $compitition ;

	//	echo $sql; 
				
	  $sqlreturn = $wpdb->get_results( $sql, OBJECT);
	  if( $wpdb->num_rows > 0 ) {	
	    wp_send_json($sqlreturn);

 	   } else {
		// should not get here normally but if it happens.
     	 	return rest_ensure_response( 'No Aircraft avaliable.' );
	   }
	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );

	}	
	public function cloud_base_aircraft_post_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	
	  if (!empty($request['type'])){
	  	$sql = $wpdb->prepare("SELECT id FROM {$table_type} where UPPER(`title`) LIKE UPPER(%s) " , $request['type']);
//	  	echo $sql;
	  	$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  	if( $wpdb->num_rows > 0 ) {	
			$type = $sqlreturn->id;
	 	 } else {
	  	   	return new \WP_Error( 'invalid type', esc_html__( 'That type does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  	}
	  } else{
		return new \WP_Error( 'type required', esc_html__( 'Type Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  }

	  $captian = null;
	  if (!empty($request['captian'])){
	  	$the_user = get_user_by( 'id', $request['captian'] ); 
		if (!$the_user) {
	  	   	return new \WP_Error( 'invalid user', esc_html__( 'Member Not found.', 'my-text-domain' ), array( 'status' => 500 ) );		
	  	} else {
			$captian = $the_user->id;
		}
	  } 
	
	  if (!empty($request['registration'])){
	  	$registration  = $wpdb->prepare("%s" , $request['registration']);
	  } else{
		return new \WP_Error( 'registration required', esc_html__( 'Registration Required.', 'my-text-domain' ), array( 'status' => 404 ) );  

	  if (!empty($request['make'])){
	  	$make  = $wpdb->prepare("%s" , $request['make']);
	  } else{
		return new \WP_Error( 'make required', esc_html__( 'Aircraft Manufacture Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  }	  
  	  if (!empty($request['model'])){
	  	$make  = $wpdb->prepare("%s" , $request['model']);
	  } else{
		return new \WP_Error( 'model required', esc_html__( 'Aircraft Model Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  }		  
	  
	  $compitition = '';
	  if (!empty($request['compitition'])){
	  	$compitition  = $wpdb->prepare("%s" , $request['compitition']);
	  }   	  

	  if (!empty($request['status'])){
	  	$sql = $wpdb->prepare("SELECT id FROM {$table_status} where UPPER(`title`) LIKE UPPER(%s) " , $request['status']);
	  	$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  	if( $wpdb->num_rows > 0 ) {	
			$status = $sqlreturn->id;
	 	 } else {
	  	   	return new \WP_Error( 'invalid status', esc_html__( 'That status does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  	}
	  } else{
		return new \WP_Error( 'status required', esc_html__( 'Status Required.', 'my-text-domain' ), array( 'status' => 404 ) );  
	  }

// generate new aircraft id number	  
	  $sql = "SELECT MAX(aircraft_id)  FROM {$table_name} " ;
	  $sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  if( $wpdb->num_rows > 0 ) {	
		$aircraft_id = $sqlreturn->aircraft_id + 1;
	  } else {
	    return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	  }

	  $sql =  $wpdb->prepare("INSERT INTO {$table_name} (aircraft_id, registration, aircraft_type, 
	  status, captian_id, date_updated, make, model, compitition_id, valid_until) VALUES ( %d, %s, %d, %s, %d, now(), %s, %s, %s, %d) " , 
	  $aircraft_id, $registration, $type, $status, $captian, $make, $model, $compitition, "0");
	  
	  $wpdb->query($sql);
	  wp_send_json(array('message'=>'Record Added'), 201 );
	}
	

	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );

	}
	public function cloud_base_aircraft_edit_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	  
	  $item =  $request['aircraft_id'];
	  if ($item  != null){	
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `aircraft_id` = %d AND valid_until = 0 " ,  $item );	
		$item = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {		
			$type = $item->type;	
			$status = $item->status;
			$captian = $item->captian_id;	
			
	  		if (!empty($request['type'])){
	  			$sql = $wpdb->prepare("SELECT id FROM {$table_type} where UPPER(`title`) LIKE UPPER(%s) " , $request['type']);
	  			$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  			if( $wpdb->num_rows > 0 ) {	
	 			$type = $sqlreturn->id;
	 			} else {
	  	   			return new \WP_Error( 'invalid type', esc_html__( 'That type does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  			}
	    	} 	
	    	if (!empty($request['captian'])){
	  			$the_user = get_user_by( 'id', $request['captian'] ); 
				if (!$the_user) {
	  	  		 	return new \WP_Error( 'invalid user', esc_html__( 'Member Not found.', 'my-text-domain' ), array( 'status' => 500 ) );		
	  			} else {
					$captian = $the_user->id;
				}
	  		} 		
	  		if (!empty($request['status'])){
	  			$sql = $wpdb->prepare("SELECT id FROM {$table_status} where UPPER(`title`) LIKE UPPER(%s) " , $request['status']);
	  			$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  			if( $wpdb->num_rows > 0 ) {	
					$status = $sqlreturn->id;
	 	 		} else {
	  	   			return new \WP_Error( 'invalid status', esc_html__( 'That status does not exist.', 'my-text-domain' ), array( 'status' => 500 ) );
	  			}
	  		}
	  		
	  		// mark existing recored as nolonger valid by setting the valin_until to now.
	       	$sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $item->id );
			$wpdb->query($sql);
		    // create new record with valid_until = 0. 
		    $sql =  $wpdb->prepare("INSERT INTO {$table_name} (aircraft_id, registration, aircraft_type, 
	 			status, captian_id, date_updated, make, model, compitition_id, valid_until) VALUES ( %d, %s, %d, %s, %d, now(), %s, %s, %s, %d) " , 
	  			$item->aircraft_id, $item->registration, $type, $status, $captian, $item->make, $item->model, $item->compitition, "0");

			$wpdb->query($sql);			
		 	wp_send_json(array('message'=>'Record Updated'), 201 );
				
		} else{
			return new \WP_Error( 'not_found', esc_html__( 'That aircraft Id was not found', 'my-text-domain' ), array( 'status' => 404 ) );
		}

	  } else{
		return new \WP_Error( 'missing paramater', esc_html__( 'Parameter missing', 'my-text-domain' ), array( 'status' => 404 ) );
	  }
	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );

	}
	public function cloud_base_aircraft_delete_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	

	  $item =  $request['aircraft_id'];
		
	  if ($item  != null){	
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `aircraft_id` = %d AND valid_until = 0 " ,  $item );	
		$expire_date =	date('Y-m-d H:i:s');
		$items = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			$sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $items->id );
			$wpdb->query($sql);
			wp_send_json(array('message'=>'Deleted', 'id'=>$fee_id), 202 );
		} else{
			return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );
	  	}	
	  } else{
		return new \WP_Error( 'missing paramater', esc_html__( 'Parameter missing', 'my-text-domain' ), array( 'status' => 404 ) );
	  }
	  return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
}

	
	

