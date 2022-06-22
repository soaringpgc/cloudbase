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
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),),   
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
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
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	

// fields to return. 
//  	  $valid_fields = array('id'=>'s.id', 'aircraft_id'=>'s.aircraft_id' , 'registration'=>'s.registration', 'captian_id'=>'s.captian_id', 'captian'=>'a.display_name',
//  	  'make'=>'s.make', 'model'=>'s.model',	'compitition_id'=>'s.compitition_id', 'annual_due_date'=>'s.annual_due_date', 'registration_due_date'=>'s.registration_due_date',
//  	  'status'=>'s.status', 'aircraft_type'=>'s.aircraft_type', 't.title'=>'t.title  AS type' );

	  $valid_fields = array('id'=>'s.id', 'aircraft_id'=>'s.aircraft_id' , 'registration'=>'s.registration', 'comment'=>'s.comment',
 	  'make'=>'s.make', 'model'=>'s.model',	'compitition_id'=>'s.compitition_id', 'annual_due_date'=>'s.annual_due_date', 'registration_due_date'=>'s.registration_due_date', 'transponder_due'=>'s.transponder_due',
 	  'status'=>'s.status', 'aircraft_type'=>'s.aircraft_type', 'title'=>'t.title  AS type', 'status_t'=>'u.title AS status_t' );

 	  $select_string = $this->select_fields($request, $valid_fields); 
// process filters.  	  
 	  $valid_filters = array('aircraft_id'=>'aircraft_id' , 'type'=>'t.title', 'captian_id'=>'captian_id', 'compitition_id'=>'compitition_id' );
	  $filter_string = $this->select_filters($request, $valid_filters);

// 	  $sql = "SELECT {$select_string}  FROM {$table_name} s inner join 
// 			{$table_type} t on s.aircraft_type=t.id inner join wp_users a on a.id = s.captian_id WHERE {$filter_string} " ;

	  $sql = "SELECT {$select_string}  FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id WHERE {$filter_string}  ORDER BY s.aircraft_type DESC, s.registration ASC" ;				
//return new \WP_REST_Response ($sql);
	  $items = $wpdb->get_results( $sql, OBJECT);
	  if( $wpdb->num_rows > 0 ) {	
	  	 return new \WP_REST_Response ($items);
 	   } else {
    	  return new \WP_Error( 'No Aircraft', esc_html__( 'no Aircraft avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
	   }
	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_aircraft_post_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	
	  if (!empty($request['aircraft_type'])){
	  	$sql = $wpdb->prepare("SELECT id FROM {$table_type} WHERE `id`  =%d " , $request['aircraft_type']);
	  	$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  	if( $wpdb->num_rows > 0 ) {	
			$aircraft_type = $sqlreturn->id;
	 	 } else {
    	   return new \WP_Error( 'No aircraft type', esc_html__( 'That type does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	  	}
	  } else{
	     	return new \WP_Error( 'type_required', esc_html__( 'Type Required.', 'my-text-domain' ), array( 'status' => 400 ) );
	  }	  	  
 	  $captian_id = null;
	  if (!empty($request['captian_id'])){
	  	$the_user = get_user_by( 'id', $request['captian_id'] ); 
		if (!$the_user) {
		    return new \WP_Error( 'Not Found', esc_html__( 'Member Not found.', 'my-text-domain' ), array( 'status' => 400 ) );
	  	} else {
			$captian_id = $the_user->id;
		}
	  } 	
	  if (!empty($request['registration'])){
	  	$registration  = $request['registration'];
	  } elseif ($aircraft_type < 3 ){
		return new \WP_Error( 'registration required', esc_html__( 'Registration Required.', 'my-text-domain' ), array( 'status' => 400 ) );  
      } else {
      	$registration  = null; 
      }
	  if (!empty($request['make'])){
	  	$make  = $request['make'];
	  } elseif ($aircraft_type < 3 ){
 		return new \WP_Error( 'make required', esc_html__( 'Aircraft Manufacture Required.', 'my-text-domain' ), array( 'status' => 400 ) );  
      } else {
      	$make  = null; 
      }	    
  	  if (!empty($request['model'])){
	  	$model  = $request['model'];
	  } elseif ($aircraft_type < 3 ){
		return new \WP_Error( 'model required', esc_html__( 'Aircraft Model Required.', 'my-text-domain' ), array( 'status' => 400 ) );  
      } else {
      	$model  = null; 
      }	  	  
	  $compitition_id = '';
	  if (!empty($request['compitition_id'])){
	  	$compitition_id  = $request['compitition_id'];
	  }  	  
	  // registration_due_date 
	  $registration_due_date = '';
	  if (!empty($request['registration_due_date'])){
		$registration_due_date =$request['registration_due_date'];
	  } 
	// annual_due_date
	  $annual_due_date = '';
	  if (!empty($request['annual_due_date'])){
		$annual_due_date =$request['annual_due_date'];
	  }  
	  // transponder_due 
	  $transponder_due = '';
	  if (!empty($request['transponder_due'])){
		$registration_due_date =$request['transponder_due'];
	  } 	    	  
	  $comment = '';
	  if (!empty($request['comment'])){
	  	$comment  = $request['comment'];
	  }   	   
	  if (!empty($request['status'])){
		$sql = $wpdb->prepare("SELECT id FROM {$table_status} WHERE `id` = %d " , $request['status']);
		$sqlreturn = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {	
			$status = $sqlreturn->id;
 		} else {
   			return new \WP_Error( 'invalid status', esc_html__( 'That status does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
		}
	  } else{
  	    	$status = 1;
   	  }
// generate new aircraft id number	  
	  $sql = "SELECT MAX(aircraft_id)  FROM {$table_name} " ;
	  $sqlreturn =  $wpdb->get_var( $sql);
	  $aircraft_id = $sqlreturn + 1;
	  $wpdb->insert($table_name, array('aircraft_id' => $aircraft_id , 'registration' => $registration, 'aircraft_type' => $aircraft_type, 
			'status' => $status, 'captian_id' => $captian_id, 'date_updated' => current_time('mysql', 1), 'make' => $make, 'model' => $model, 'annual_due_date'=>$annual_due_date, 
				'registration_due_date'=>$registration_due_date, 'transponder_due'=>$transponder_due, 
			'compitition_id' => $compitition_id, 'comment'=>$comment,  'valid_until' => null  ), 
			array( '%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) ); 
//   // read it back to get id and send
	  $valid_fields = array('id'=>'s.id', 'aircraft_id'=>'s.aircraft_id' , 'registration'=>'s.registration', 
 	  'make'=>'s.make', 'model'=>'s.model',	'compitition_id'=>'s.compitition_id', 'annual_due_date'=>'s.annual_due_date', 'registration_due_date'=>'s.registration_due_date', 'transponder_due'=>'s.transponder_due',
 	  'status'=>'s.status', 'aircraft_type'=>'s.aircraft_type', 't.title'=>'t.title  AS type' );

 	  $select_string = $this->select_fields($request, $valid_fields);   
	  $sql =  $wpdb->prepare("SELECT *, t.title  AS type  FROM {$table_name} s inner join 
			{$table_type} t on s.aircraft_type=t.id  WHERE `registration` = %s AND s.valid_until IS NULL" , $registration  );	
	  
	  $items = $wpdb->get_row( $sql, OBJECT);		 		 
			 		 
 	  return new \WP_REST_Response ($items); 
	}
	public function cloud_base_aircraft_edit_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	
	  $table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	  $table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
	  
	  $item =  $request['aircraft_id'];
	  if ($item  != null){	
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `aircraft_id` = %d AND valid_until IS NULL " ,  $item );	
		$item = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {					
        	if (!empty($request['aircraft_type'])){
        	 	$sql = $wpdb->prepare("SELECT id FROM {$table_type} WHERE `id` =%d " , $request['aircraft_type']);
        	 	$sqlreturn = $wpdb->get_row( $sql, OBJECT);
        	 	if( $wpdb->num_rows > 0 ) {	
        			$aircraft_type = $sqlreturn->id;
        		 } else {
            return new \WP_Error( 'No aircraft type', esc_html__( 'That type does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
        	 	}
        	} else{
        	    	$aircraft_type = $item->aircraft_type;
         	}	
         	// aircraft id  
	    	if (!empty($request['aircraft_id'])){
				$aircraft_id =$request['aircraft_id'];
	  		} else {
				$aircraft_id = $item->aircraft_id;
			}
			// registration 
	    	if (!empty($request['registration'])){
				$registration =$request['registration'];
	  		} else {
				$registration = $item->registration;
			}
			// compitition_id 
	    	if (!empty($request['compitition_id'])){
				$compitition_id =$request['compitition_id'];
	  		} else {
				$compitition_id = $item->compitition_id;
			}
			// registration_due_date 
	    	if (!empty($request['registration_due_date'])){
				$registration_due_date =$request['registration_due_date'];
	  		} else {
				$registration_due_date = $item->registration_due_date;
			}
			// annual_due_date 
	    	if (!empty($request['annual_due_date'])){
				$annual_due_date =$request['annual_due_date'];
	  		} else {
				$annual_due_date = $item->annual_due_date;
			}
			// transponder_due 
	    	if (!empty($request['transponder_due'])){
				$transponder_due =$request['transponder_due'];
	  		} else {
				$transponder_due = $item->transponder_due;
			}
			// make 
	    	if (!empty($request['make'])){
				$make =$request['make'];
	  		} else {
				$make = $item->make;
			}
			// comment 
	    	if (!empty($request['comment'])){
				$comment =$request['comment'];
	  		} else {
				$comment = $item->comment;
			}
			// model 
	    	if (!empty($request['model'])){
				$model =$request['model'];
	  		} else {
				$model = $item->model;
			}
	      	
	    	if (!empty($request['captian'])){
	  			$the_user = get_user_by( 'id', $request['captian_id'] ); 
				if (!$the_user) {
//					wp_send_json_error(array('message'=>'Member Not found.'), 400 );		
	  	  		 	return new \WP_Error( 'invalid user', esc_html__( 'Member Not found.', 'my-text-domain' ), array( 'status' => 400 ) );		
	  			} else {
					$captian_id = $the_user->id;
				}
	  		} else{
        	    	$captian_id = $item->captian_id;
         	} 		
	  		if (!empty($request['status'])){
	  			$sql = $wpdb->prepare("SELECT id FROM {$table_status} WHERE `id` = %d " , $request['status']);
	  			$sqlreturn = $wpdb->get_row( $sql, OBJECT);
	  			if( $wpdb->num_rows > 0 ) {	
					$status = $sqlreturn->id;
	 	 		} else {
	  	   			return new \WP_Error( 'invalid status', esc_html__( 'That status does not exist.', 'my-text-domain' ), array( 'status' => 400 ) );
	  			}
	  		} else{
        	    	$status = $item->status;
         	}
	  		
		    // create new record with valid_until = null. 
		    $update_result = $wpdb->insert($table_name, array('aircraft_id' => $aircraft_id , 'registration' => $registration, 'aircraft_type' => $aircraft_type, 
				'status' => $status, 'captian_id' => $captian_id, 'date_updated' => current_time('mysql', 1), 'make' => $make, 'model' => $model, 'annual_due_date'=>$annual_due_date, 
				'registration_due_date'=>$registration_due_date, 'transponder_due'=>$transponder_due, 
				'compitition_id' => $compitition_id, 'comment'=>$comment, 'valid_until' => null  ), 
				array( '%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s',' %s', '%s', '%s' )); 
								
			if ( $update_result == 1 ) {
 	  		    // mark existing recored as nolonger valid by setting the valin_until to now.
  	       		$wpdb->update($table_name, array('valid_until' => current_time( 'mysql' )), array( 'id' =>  $item->id) );
//   				// read it back to get id and send
  	  			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `registration` = %s AND valid_until IS NULL " , $registration  );	
 	  			$items = $wpdb->get_row( $sql, OBJECT);
 	  			return new \WP_REST_Response ($items);											
 			}	else {
				return new \WP_Error( 'update_failed', esc_html__( 'Unable to update record.', 'my-text-domain' ), array( 'status' => 400 ) );
 			
 			}												
		} else{
			return new \WP_Error( 'not_found', esc_html__( 'That aircraft Id was not found', 'my-text-domain' ), array( 'status' => 400 ) );
		}
	  } else{
		    return new \WP_Error( 'missing paramater', esc_html__( 'Parameter missing', 'my-text-domain' ), array( 'status' => 400 ) );
	  }
	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}
	public function cloud_base_aircraft_delete_callback( \WP_REST_Request $request) {
	  global $wpdb;
	  $table_name = $wpdb->prefix . "cloud_base_aircraft";	

	  $item =  $request['aircraft_id'];
		
	  if ($item  != null){	
		$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d AND valid_until IS NULL " ,  $item );	

		$items = $wpdb->get_row( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			$sql =  $wpdb->prepare("UPDATE {$table_name} SET `valid_until`= now() WHERE `id` = %d " , $items->id );
			$wpdb->query($sql);
			wp_send_json(array('message'=>'Deleted', 'id'=>$items->id), 202 );
		} else{
			return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 400 ) );
	  	}	
	  } else{
//	  	  wp_send_json_error(array('message'=>'Parameter missing.'), 404 );		
   	      return new \WP_Error( 'missing paramater', esc_html__( 'Parameter missing', 'my-text-domain' ), array( 'status' => 400 ) );
	  }
//	   wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );		
  	   return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
}

	
	

