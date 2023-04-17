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
	
	 return rest_ensure_response( 'Hello World, this is the Cloud Based Squawk REST API' . $request['id']);
		/* 
			Process your GET request here.		
		*/
	}	
	public function cloud_base_squawks_post_callback( \WP_REST_Request $request) {

		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_squawks_put_edit_callback( \WP_REST_Request $request) {

		global $wpdb;
		$table_aircraft = $wpdb->prefix . "cloud_base_aircraft";	
		$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
		$table_squawk = $wpdb->prefix . 'cloud_base_squawk';
// 		$user = wp_get_current_user();
// 		$user_meta = get_userdata( $user->ID );
// 		$display_name = $user_meta->first_name .' '.  $user_meta->last_name;
 
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
		/* 
			Process your PUT request here.
		*/
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
			return new \WP_Error( 'not found', esc_html__( 'Record Not found.', 'my-text-domain' ), array( 'status' => 404 ) );
		}
	}
}

