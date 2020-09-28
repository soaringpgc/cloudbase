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
class Cloud_Base_Sign_off_types extends Cloud_Base_Rest {

	public function register_routes() {
	              
     $this->resource_path = '/sign_off_types' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_signoffs_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	                        
    }
// call back for signoffs:	
	public function cloud_base_signoffs_get_callback( \WP_REST_Request $request) {		
		global $wpdb;
	    $table_name = $wpdb->prefix . "cloud_base_signoffs_types";    
	    // authority array is stored in WP options, It is created/updated on activation           
	    $value_label_authority = get_option('cloud_base_authoritys');
						
// delete this before next commit. 		
// 		if (get_option ('glider_club_short_name') == 'PGC'){
// 			$table_name = $wpdb->prefix . "pgc_signoffs_types";
// 			$value_label_authority = array("read"=>"Self", "edit_gc_dues"=>"Treasurer", "edit_gc_operations"=>"Operations", 
// 					"edit_gc_instruction"=>"CFI-G", "chief_flight"=>"Chief CFI-G", "chief_tow"=>"Chief Tow Pilot", "edit_gc_tow"=>"Tow Pilot", "manage_options"=>"god");		
// 			} else {
// 				$table_name = $wpdb->prefix . "cloud_base_signoffs_types";
// 				$value_label_authority = array("read"=>"Self", "cb_edit_dues"=>"Treasurer", "cb_edit_operations"=>"Operations", 
// 				    "cb_edit_instruction"=>"CFI-G", "cb_edit_cfig"=>"Chief CFI-G", "cb_chief_tow"=>"Chief Tow Pilot");				
// 			}
	
	
// NTFS: this array has the new "cb_" capabilities and the "PGC" capabilities from  
// and earily version of sign offs. eventualy all shoudl be over written with new. 
// forget all that on activation PGC stuff is copyied and updated. (I hope)
//
// authority array is stored in WP options, It is created/updated on activation 
		$value_lable_period = array("yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "no_expire"=>"No expire", 
				"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );		

		$sql = "SELECT * FROM {$table_name}";
 		$items = $wpdb->get_results( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			foreach($items as $k=> $v){
//	NTFS: The CAPABILITY is stored in the database, however it does not look pretty
// use the above array to reverse lookup the primary AUTHORITY that can signoff an item. 			
			$items[$k]->authority_label =  $value_label_authority[$v->authority];
// likewise for the period. sending both dthe period and period lable. 
// kinda a pain as these two arrays need to be in two different locations. 			
			
			$items[$k]->period_label =  $value_lable_period[$v->period];
			}				
			return new \WP_REST_Response ($items);
 		 } else {
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_signoffs_post_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pgc_signoffs_types";
		if (!empty($request['signoff_type'])){
			$title = $request['signoff_type'];
		} else {
			wp_send_json_error(array('message'=>'Missing Sign off.'), 400 );	
		}
 	// check it does not exist. 
 		$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
		$items = $wpdb->get_row( $sql, OBJECT);		 			
		if( $wpdb->num_rows > 0 ) {
			return rest_ensure_response( 'Sign off already exists id= '. $items->id );
 		 } else {
		 	$sql =  $wpdb->prepare("INSERT INTO {$table_name} (title ) VALUES ( %s) " , $title );	
			$wpdb->query($sql);	
 			// read it back to get id and send
 			$sql =  $wpdb->prepare("SELECT * FROM {$table_name} WHERE `title` = %s " , $title  );	
			$items = $wpdb->get_row( $sql, OBJECT);				
			wp_send_json($items);				
	    }
	    wp_send_json_error(array('message'=>'Something went horribly wrong.'), 500 );	
	
		
		
		
		
		
		
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_signoffs_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pgc_signoffs_types";
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_signoffs_delete_callback( \WP_REST_Request $request) {
		global $wpdb;
		$table_name = $wpdb->prefix . "pgc_signoffs_types";
		/* 
			Process your DELETE request here.			
		*/
	}	
			
}

