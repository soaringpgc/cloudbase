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
class Cloud_Base_Sign_offs extends Cloud_Base_Rest {

	public function register_routes() {	              
     $this->resource_path = '/sign_offs_types' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
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
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_signoffs_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),),
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
	
	 return rest_ensure_response( 'Hello World, this is the Cloud Based sign off REST API ' . $request['id']);
		/* 
			Process your GET request here.		
		*/
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
	public function cb_expire_date($start_date, $period, $fixed_date ){
	// function to calculate the expire date. 
			switch($period ){
			case "monthly":
				$start_date->modify('+1 month');
			break;
			case "quarterly":
				$start_date->modify('+3 month');
			break;
			case "yearly":
				$start_date->modify('+1 year');
			break;
			case "biennial":
				$start_date->modify('+2 year');
			break;
			case "fixed":
				$start_date = new \DateTime($fixed_date );
			break;
			case "no_expire":
				 $start_date = new \DateTime('2099-12-31');
			break;
			case "yearly-eom":
				 $start_date->modify('+1 year');
				 $start_date->modify('last day of this month');
			break;
			case "biennial-eom":
				$start_date->modify('+2 year');
				$start_date->modify('last day of this month');
			break;
			default:
		}	
		return($start_date);	
	}
	public function cb_expire($start_date, $signoff_id){
// do the database look up here. the call expire_date	
		global $wpdb;
	 	$table_signoffs = $wpdb->prefix . "cloud_base_signoffs_types";
	 	$sql = $wpdb->prepare("SELECT * FROM {$table_signoffs} WHERE `id` = %d", $signoff_id);	 	
  		$signoff_duration = $wpdb->get_row($sql);
		$date_expire = $this->gc_expire_date($start_date, $signoff_duration->period, $signoff_duration->fixed_date);
		return($date_expire);	
	}
			
}

