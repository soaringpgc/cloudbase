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
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
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
		$valid_roles = array ('cfi_g', 'tow_pilot', 'subscriber', 'CFI_G', 'TOW_PILOT', 'SUBSCRIBER');		
		$pilot =  new stdClass();	
		$pilots = []; 
		$users =[];
		
		if (!empty($request['id']) or (!empty($request['pilot_id']) ))	{
			if(!empty($request['pilot_id'])){
				$users[] = get_user_by('ID', $request['pilot_id'] );
			} else {
				$users[] = get_user_by('ID', $request['id'] );
			}	
		} else {		
  		  $role = 'subscriber';		
		  if (!empty($request['role'])){
	        if (in_array($request['role'], $valid_roles, false)){
			  $role = $request['role'];	
		    }
		  }

	      $users = get_users(['role__in' => [$role]] );	
	    }
		usort($users, function($a, $b) {
    		return strnatcmp($a->last_name . ', ' . $a->first_name, $b->last_name . ', ' . $b->first_name);
		});		
     	$table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";
     	$table_types = $wpdb->prefix . "cloud_base_signoffs_types";
//NTFS: selecting user id's who have a no fly signoff that is expired. 
// 		
  	 	$sql =  "SELECT a.id  FROM {$table_signoffs } s inner join {$table_types } t 
  	 			on s.Signoff_id = t.id inner join wp_users a on a.id = s.member_id  
  	 			WHERE t.no_fly  = 1 AND s.date_expire < current_date() ORDER BY a.user_nicename ";
		 			  	 			
		$raw_no_fly_list= $wpdb->get_results($sql, ARRAY_A);
		if( $wpdb->num_rows > 0 ){
// 		var_dump($raw_no_fly_list);
// 		die;
		
		foreach($users as $key => $user){
			$pilot->pilot_id = $user->ID;
			$pilot->ID = $user->ID;
			$pilot->last_name = $user->last_name;
			$pilot->first_name = $user->first_name;
			$pilots[]=$pilot;
					$pilot =  new stdClass();	
		}		
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
	

