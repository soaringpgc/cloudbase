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
class Cloud_Base_Types extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/aircraft_types' . '(?:/(?P<id>[\d]+))?';
    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_put_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_types_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	              
    }

// call back for types:	
	public function cloud_base_types_get_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_type";	
		$item_id =  $request['id'];
// Associative aray of valid fields, Key is string passed in the _fileds varable
// value is what need to be passed to mySQL. 
 		$valid_fields = array('id'=>'id' , 'list'=>'title');
 		$select_string = $this->select_fields($request, $valid_fields);

		if ($item_id  != null){	
		// return the current item for item requested
			$sql = $wpdb->prepare("SELECT {$select_string} FROM {$table_name} WHERE `id` = %d " ,  $item_id );		
		} else {
		// return all current items. 
	        $sql = "SELECT {$select_string} FROM ". $table_name . "  ORDER BY title ASC ";	
		}

		$items = $wpdb->get_results( $sql, OBJECT);

		if( $wpdb->num_rows > 0 ) {
			return new \WP_REST_Response ($items);
//			wp_send_json($items);
 		 } else {
// 		 	wp_send_json(array('message'=>'no Types avaliable.'), 204 );
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		// should not get here normally but if it happens.
//			wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );		
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_types_post_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_type";	

		if (!empty($request['type'])){
			$title = $request['type'];
		} else {
//			wp_send_json_error(array('message'=>'missing Type.'), 400 );				
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing Type.', 'my-text-domain' ), array( 'status' => 400 ) );
		}
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {

			return rest_ensure_response( 'Already exists id= '. $items->id );
 		 } else {
		 	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (title ) VALUES ( %s) " , $title );	
			$wpdb->query($sql);	
 			// read it back to get id and send
 			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
			$items = $wpdb->get_row( $sql, OBJECT);
			return new \WP_REST_Response ($items);
//			wp_send_json($items);				
	    }
//			wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );		
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
	}
	public function cloud_base_types_put_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_type";	
		$item_id =  $request['id'];
		if (!empty($request['title'])){
			$title = $request['title'];
		} else {
			$title =null;
		}
 		if ($item_id != null && $title != null ) {	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d  " ,  $item_id) ;	
			$items = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `title`= %s WHERE `id` = %d " , $title, $item_id  );
				$wpdb->query($sql);
//
				$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d  " ,  $item_id) ;	

				$items = $wpdb->get_row( $sql, OBJECT);	
				if( $wpdb->num_rows > 0 ) {
					return new \WP_REST_Response ($items);
			//		wp_send_json($items);
			    } else {
			 		return new \WP_Error( 'update_error', esc_html__( 'Record was not updated.', 'my-text-domain' ), array( 'status' => 400 ) );			    
			    }
	//			wp_send_json(array('message'=>'Record Updated'), 201 );
			} else{
			 	return new \WP_Error( 'not_found', esc_html__( 'Record not found.', 'my-text-domain' ), array( 'status' => 400 ) );
//			 	wp_send_json_error(array('message'=>'Record not found.', 'id'=>$item_id), 400);
 			}
 		} else {
//          wp_send_json_error(array('message'=>'id and/or type missing.'), 400 );		
 			return new \WP_Error( 'missing_parameters', esc_html__( 'id and/or type missing. ', 'my-text-domain' ), array( 'status' => 400 ) );
 		}
// 		wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );		
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
	}
	public function cloud_base_types_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_type";	
		$item_id =  $request['id'];
		
		if ($item_id  != null){	
			$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
			$sql =  $wpdb->prepare("SELECT * FROM  $table_aircraft  WHERE `aircraft_type` = %d " ,  $item_id );	
			$aircraft = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows === 0 ) {	
				$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );	
				$items = $wpdb->get_row( $sql, OBJECT);
				if( $wpdb->num_rows > 0 ) {
			        $sql =  $wpdb->prepare("DELETE from {$table_name}  WHERE `id` = %d " , $items->id );
					$wpdb->query($sql);
					return rest_ensure_response( 'Deleted'. $items->id );
//					wp_send_json_success(array('message'=>'Deleted', 'id'=>$item_id), 202 );
				} else{
//					wp_send_json_error(array('message'=>'Record not found.', 'id'=>$item_id), 404);
				  return new \WP_Error( 'not found', esc_html__( 'Record Note found.', 'my-text-domain' ), array( 'status' => 404 ) );
				}	
			} else {
//			wp_send_json_error(array('message'=>'Record found but is inuse, cannot delete.', 'id'=>$item_id), 423 );
				 return new \WP_Error( 'in Use', esc_html__( 'Record found, but is inuse, cannot delete.', 'my-text-domain' ), array( 'status' => 423) );
			}	
		} else{
//			wp_send_json_error(array('message'=>'invalid request no id.'), 404 );
			return new \WP_Error( 'invalid', esc_html__( 'invalid request no id.', 'my-text-domain' ), array( 'status' => 404 ) );

		}
	}	


}