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
		$table_name = $wpdb->prefix . "pgc_signoffs_types";
// NTFS: this array has the new "cb_" capabilities and the "PGC" capabilities from  
// and earily version of sign offs. eventualy all shoudl be over written with new. 		
		$value_label_authority = array("read"=>"Self", "cb_edit_dues"=>"Treasurer", "cb_edit_operations"=>"Operations", "cb_edit_instruction"=>"CFI-G", 
			  	"cb_edit_cfig"=>"Chief CFI-G", "chief_tow"=>"Chief Tow Pilot", 
			  	"edit_gc_dues"=>"Treasurer", "edit_gc_operations"=>"Operations", "edit_gc_instruction"=>"CFI-G", 
			  	"chief_flight"=>"Chief CFI-G", "chief_tow"=>"Chief Tow Pilot", "edit_gc_tow"=>"Tow Pilot", "manage_options"=>"god");

		$sql = "SELECT * FROM {$table_name}";
 		$items = $wpdb->get_results( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			foreach($items as $k=> $v){
//	NTFS: The CAPABILITY is stored in the database, however it does not look pretty
// use the above array to reverse lookup the primary AUTHORITY that can signoff an item. 			
			$items[$k]->authority=  $value_label_authority[$v->authority];
			}
				
			return new \WP_REST_Response ($items);
 		 } else {
			return new \WP_Error( 'no_types', esc_html__( 'no Types avaliable.', 'my-text-domain' ), array( 'status' => 204 ) );
		}
		return new \WP_Error( 'rest_api_sad', esc_html__( 'Something went horribly wrong.', 'my-text-domain' ), array( 'status' => 500 ) );
	}	
	public function cloud_base_signoffs_post_callback( \WP_REST_Request $request) {
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_signoffs_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_signoffs_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
			
}

