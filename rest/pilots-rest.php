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
class Cloud_Base_pilots extends Cloud_Base_Rest {

	public function register_routes() {
	   
     $this->resource_path = '/pilots' . '(?:/(?P<id>[\d]+))?';
    
      register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_dummy_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_pilots_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_pilots_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	
	}

// call back for pilots signoffs:	
	public function cloud_base_pilots_get_callback( \WP_REST_Request $request) {
		global $wpdb;
     	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
     	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";

		$valid_roles = array ('cfi_g', 'tow_pilot', 'subscriber', 'active','CFI_G', 'TOW_PILOT', 'SUBSCRIBER', 'ACTIVE');		
		$pilot =  new stdClass();	
		$pilots = []; 
		$users =[];
		$no_fly=[];
		
		if (isset($request['no_fly'])){
		// get the no fly list. 
			$no_flying = $wpdb->get_results( "SELECT DISTINCT a.id  
				FROM {$table_signoffs } s 
				INNER JOIN {$table_types } t ON s.Signoff_id = t.id 
				INNER JOIN wp_users a ON a.id = s.member_id 
				INNER JOIN wp_usermeta ON s.member_id  = wp_usermeta.user_id 										
				WHERE wp_usermeta.meta_key = 'wp_capabilities' 
				AND wp_usermeta.meta_value LIKE '%subscriber%'  
				AND	t.no_fly  = 1 AND s.date_expire < current_date() ", $output = OBJECT )	;	
			  foreach($no_flying as $item){
			  	$no_fly[] = $item->id;
			  }	
			  $args = array('include' => $no_fly)	;			
 			if($request['no_fly'] != 'true'){
// 			// If we are looking for the fly list get all subscribers 
 				$subscribers = get_users(['role__in' => 'subscriber', 'fields' => 'ID']);		
// 				// get the difference between the subscriber list and the no fly list. 			 
 				$fly_list = array_diff($subscribers, $no_fly )	;	
				$args = array('include' => $fly_list)	;				 
 			}
			$users = get_users($args);	
		} elseif (isset($request['role'])){
	        if (in_array($request['role'], $valid_roles, false)){
			  $role = $request['role'];	
			  $users = get_users(['role__in' => [$role]] );	
		    } else {
				return new \WP_Error( 'invalid_role', esc_html__( 'role not found.', 'my-text-domain' ), array( 'status' => 400 ) );        		    
		    }		
		} elseif (isset($request['id'])){
			$users[] = get_user_by('ID', $request['id'] );				
		} else {
			$users = get_users(['role__in' => 'subscriber'] );	
		}
		if (!empty($users)){
			foreach($users as $key => $user){
				$pilot->pilot_id = $user->ID;
				$pilot->ID = $user->ID;
				$pilot->last_name = $user->last_name;
				$pilot->first_name = $user->first_name;
				$pilot->name = $user->last_name . ", " . $user->first_name;
				$pilots[]=$pilot;
				$pilot =  new stdClass();	
			}	
			usort($users, function($a, $b) {
    			return strnatcmp($a->last_name . ', ' . $a->first_name, $b->last_name . ', ' . $b->first_name);
			});			
           return new \WP_REST_Response ($pilots);    			
		} else {
			return new \WP_Error( 'no_pilots', esc_html__( 'no pilots found.', 'my-text-domain' ), array( 'status' => 204 ) );        
        }         
	}
	
	public function cloud_base_pilots_data_put_callback( \WP_REST_Request $request) {
// unused 

	}
	
	public function cloud_base_pilots_data_edit_callback( \WP_REST_Request $request) {
	

	}

	public function cloud_base_pilots_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
	
}
	

