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
class Cloud_Base_Flight_Types extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/flight_types' . '(?:/(?P<id>[\d]+))?';
    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_members_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_types_put_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
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
		$table_name = $wpdb->prefix . "cloud_base_flight_type";	
		if (!empty($request['id'])){
			$item_id = $request['id'];
		}
		if (empty($request['active'])){
			$active = 'true';
		} else {
			$active = 'false'; 
		}

		if (!empty($request['id'])){
			$item_id = $request['id'];	
		// return the current item for item requested
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );		
		} else {
		// return all current items. 
	        $sql = "SELECT * FROM ". $table_name . " WHERE `active` =" . $active .  " ORDER BY `title` ASC ";	
		}
		$items = $wpdb->get_results( $sql, OBJECT);

		if( $wpdb->num_rows > 0 ) {
			return new \WP_REST_Response ($items);
 		 } else {
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		return new \WP_Error( 'server_error', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_types_post_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_flight_type";	

		if (!empty($request['title'])){
			$title = $request['title'];
		} else {
			return new \WP_Error( 'Title missing', esc_html__( 'Title/type is missing.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		if (!empty($request['description'])){
			$description = $request['description'];
		} else {
			$description = null;		
		}

 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );			
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {
		// if an inactive type of the same name exists, make it active. 
			$wpdb-update($table_name, array( 'active'=>true), array('id'=>$id));	
			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	 
		    $items = $wpdb->get_row( $sql, OBJECT);				
		    return new \WP_REST_Response ($items);			 	 			
//			return rest_ensure_response( 'Sign off already exists id= '. $items->id );	
 		 } else {
		 	if ($wpdb->insert($table_name, array( 'title'=>$title, 'description'=> $description , 'active'=>'1')) != false){
 			   $sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	 
			   $items = $wpdb->get_row( $sql, OBJECT);				
			   return new \WP_REST_Response ($items);			 	 	
		 	} else {
//		 			return new \WP_REST_Response ($wpdb->last_error);	
		 	 	return rest_ensure_response( 'Insert failed = '. $title );	
		 	}
	    }	    
	    
//		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
	}
	public function cloud_base_types_put_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_flight_type";	
		if (!empty($request['id'])){
			$id = $request['id'];
			$sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d  " ,  $id) ;	
			$items = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
        		if (!empty($request['title'])){
        			$title = $request['title'];
        		} else {
        			$title =$items['title'];
        		}
        		if (!empty($request['description'])){
        			$description = $request['description'];
        		} else {
        			$description = $items['description'];	
        		}						
			$wpdb->update($table_name, array( 'title'=>$title, 'description'=> $description , 'active'=>1), array('id'=>$id));				
			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " , $id  );	
			$items = $wpdb->get_row( $sql, OBJECT);				
   			    return new \WP_REST_Response ($items);		
			} else {		
			  return new \WP_Error( 'Not Found', esc_html__( 'Not Found', 'my-text-domain' ), array( 'status' => 404 ) );
			}
		} else {
			return new \WP_Error( 'Id missing', esc_html__( 'id missing', 'my-text-domain' ), array( 'status' => 400 ) );
		}
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong .', 'my-text-domain' ), array( 'status' => 500 ) );
	}
	public function cloud_base_types_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_flight_type";	
		if (!empty($request['id'])){
		  $item_id =  $request['id'];		
		  $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE `id` = %d " ,  $item_id );	
			$items = $wpdb->get_row( $sql, OBJECT);
			if( $wpdb->num_rows > 0 ) {
			    $sql =  $wpdb->prepare("UPDATE  {$table_name} SET active = 0 WHERE `id` = %d " , $items->id );
				$wpdb->query($sql);
				return new \WP_REST_Response ($items);
			} else{
			 	return new \WP_Error( 'not_found', esc_html__( 'Not found. ', 'my-text-domain' ), array( 'status' => 400 ) );
			}	
		} 
	}	

}	

