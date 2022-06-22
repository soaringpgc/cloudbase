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
class Cloud_Base_Status extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/aircraft_status' . '(?:/(?P<id>[\d]+))?';
    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_status_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_status_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_status_put_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_status_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	              
    }

// call back for status:	
	public function cloud_base_status_get_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_status";	
		$item_id =  $request['id'];

		if ($item_id  != null){	
		// return the current item for item requested
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );		
		} else {
		// return all current items. 
	        $sql = "SELECT * FROM ". $table_name . "  ORDER BY title ASC ";	
		}
		$items = $wpdb->get_results( $sql, OBJECT);

		if( $wpdb->num_rows > 0 ) {
			return new \WP_REST_Response ($items);
 		 } else {
		// should not get here normally but if it happens.
		    	return new \WP_Error( 'No Status', esc_html__( 'no Status avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}	
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_status_post_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_status";	

		if (!empty($request['title'])){
			$title = $request['title'];
		} else {
//			wp_send_json_error(array('message'=>'Missing Status..'), 400 );	
			return new \WP_Error( 'rest_api_sad', esc_html__( 'missing Status.', 'my-text-domain' ), array( 'status' => 400 ) );
		}
		if (!empty($request['color'])){
			$color = $request['color'];
		} else {
			$color = '000000';
		}
		
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {

			return rest_ensure_response( 'Already exists id= '. $items->id );
 		 } else {
			$wpdb->insert($table_name, array('title'=>$title, 'color'=>$color ), array ('%s', '%s' ));
 			// read it back to get id and send
 			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
			$items = $wpdb->get_row( $sql, OBJECT);				
			return new \WP_REST_Response ($items);						
	    }
	}
	public function cloud_base_status_put_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_status";	
		$item_id =  $request['id'];
 	
 		if ($item_id  != null ) {	
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d  " ,  $item_id) ;	
			$items = $wpdb->get_row( $sql, OBJECT);
				$item_id =  $request['id'];
				if (!empty($request['title'])){
					$title = $request['title'];
				} else {
					$title =$items->title;
				}
				if (!empty($request['color'])){
					$color = $request['color'];
				} else {
					$color =$items->color;
				}				
			if( $wpdb->num_rows > 0 ) {
				$sql =  $wpdb->prepare("UPDATE {$table_name} SET `title`= %s, `color`= %s WHERE `id` = %d " , $title, $color, $item_id  );
//				return new \WP_REST_Response($sql);
				$wpdb->query($sql);
				// read it back to get id and send
 				$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
				$items = $wpdb->get_row( $sql, OBJECT);				
				return new \WP_REST_Response ($items);		
			} else{
			 	return new \WP_Error( 'not_found', esc_html__( 'Not found. ', 'my-text-domain' ), array( 'status' => 400 ) );			
 			}
 		} else {
 			return new \WP_Error( 'nothing changed', esc_html__( 'id and/or status missing. ', 'my-text-domain' ), array( 'status' => 400 ) );
 		}
	}
	public function cloud_base_status_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_status";	
		$item_id =  $request['id'];
		
		if ($item_id  != null){	
			$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
			$sql =  $wpdb->prepare("SELECT * FROM  $table_aircraft  WHERE `aircraft_status` = %d " ,  $item_id );	
			$aircraft = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows == 0 ) {	
				$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );	
				$items = $wpdb->get_row( $sql, OBJECT);
				if( $wpdb->num_rows > 0 ) {
			        $sql =  $wpdb->prepare("DELETE from {$table_name}  WHERE `id` = %d " , $items->id );
					$wpdb->query($sql);
					return new \WP_REST_Response ($items);
//					wp_send_json(array('message'=>'Deleted', 'id'=>$item_id), 202 );
				} else{
// 					wp_send_json_error(array('message'=>'Record Not found.'), 404 );	
  				    return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );
				}	
			} else {
//			 	wp_send_json_error(array('message'=>'Record found but is inuse, cannot delete.'), 404 );	
				 return new \WP_Error( 'in Use', esc_html__( 'Record found but is inuse, cannot delete.', 'my-text-domain' ), array( 'status' => 404 ) );
			}	
		} else{
//			 wp_send_json_error(array('message'=>'Invalid request no id..'), 404 );	
			return new \WP_Error( 'invalid', esc_html__( 'invalid request no id.', 'my-text-domain' ), array( 'status' => 404 ) );
		}
	}	
}
	

