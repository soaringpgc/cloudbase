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
class Cloud_Base_Aircraft_Events extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/aircraft_events' . '(?:/(?P<id>[\d]+))?';  
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_events_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_events_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_events_put_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_aircraft_events_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	              
    }
/* 
NOTE have onoly word out GET so far! 18 March 2024 dsj

*/
	public function cloud_base_aircraft_events_get_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_event_types";	
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
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
 		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_aircraft_events_post_callback( \WP_REST_Request $request) {
	    global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_event_types";	

		if (!empty($request['title'])){
			$title = $request['title'];
		} else {
			return new \WP_Error( 'Title missing', esc_html__( 'Title/type is missing.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		if (!empty($request['aircraft_type'])){
			$aircraft_type = $request['aircraft_type'];
		} else {
			return new \WP_Error( 'Aircraft type missing', esc_html__( 'Aircraft type is missing.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		if (!empty($request['description'])){
			$description = $request['description'];
		} else {
			$description = null;		
		}
		isset($request['description']) 	? $flightyear=$request['description'] 	: $description=null;
		isset($request['active']) 	? $flightyear=$request['active'] 	: $active=true;
 
 		if (isset($request['interval'])) {
 			$interval=$request['interval'] 	;
 			if(isset($request['interval_units']) ){
 				$interval_unitsr=$request['interval_units'] ;
 			} else {
				return new \WP_Error( 'units missing', esc_html__( 'Interval Units is missing.', 'my-text-domain' ), array( 'status' => 204 ) );
			
 			}
 		}				
        $data = array( 'title'=>$title, 'aircraft_type'=>$aircraft_type, 'interval'=>$interval, 'interval_units'=> $interval_units, 'aircraft_type'=>$aircraft_type) ;        	
		
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s AND `aircraft_type` " , $title, $aircraft_type  );			
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {
		// if an inactive type of the same name exists, make it active. 
			$wpdb-update($table_name, array( 'active'=>true), array('id'=>$id));	
			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	 
		    $items = $wpdb->get_row( $sql, OBJECT);				
		    return new \WP_REST_Response ($items);			 	 			
 		 } else {
        	$result = $wpdb->insert($table_name, $data); 							
			   return new \WP_REST_Response ($result);			 	 	
	    }	    
	}
	public function cloud_base_aircraft_events_put_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_event_types";	
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
	public function cloud_base_aircraft_events_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "cloud_base_aircraft_event_types";	
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

