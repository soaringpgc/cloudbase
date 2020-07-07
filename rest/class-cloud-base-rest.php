<?php
/**
 * The rest functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 */

/**
 * The REST functionality of the plugin.
 *
 * Defines the plugin name, version, and examples to create your REST access
 * methods. Don't forget to validate and sanatize incoming data!
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/rest
 * @author     Dave Johnson <johnson.s.david@pm.me>
 */
class Cloud_Base_Rest extends WP_REST_Controller {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cloud_base    The ID of this plugin.
	 */
	private $cloud_base;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param       string $cloud_base        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 * @param       string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $cloud_base, $version) {

		$this->plugin_name = 'cloud_base';
		if ( defined ( 'CLOUD_BASE_REST_VERSION')){
			$this->rest_version = CLOUD_BASE_REST_VERSION;
		} else {
			$this->rest_version = '1';
		}	
		// you may want base path name to be different from plugin name. 	

		$this->namespace = $this->plugin_name. '/v' .  $this->rest_version; 			
	}

// register routes must be overridden for each endpoint. 
	public function register_routes() {
	                         
             
    }
 
 	public function cloud_base_admin_access_check(){
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( !(current_user_can( 'edit_users' ))) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
	}
	public function cloud_base_instruction_access_check(){
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( !(current_user_can('edit_gc_instruction'))) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
	}   
	public function cloud_base_operatioins_access_check(){
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( !(current_user_can('edit_gc_operations'))) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
	} 
	 
	public function cloud_base_treasurer_access_check(){
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( !(current_user_can('edit_gc_dues'))) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
	}  
	
	public function cloud_base_members_access_check(){
	// put your access requirements here. You might have different requirements for each access method. 
	// can read, at least a subscriber. 
    	if (  current_user_can( 'read' )) {
    	    return true;
     	}
    	// This is a white-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.	
    	return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
	}   
	   
	public function cloud_base_private_access_check(){
	return true;
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( ! (current_user_can( 'edit_users' ) || current_user_can('edit_gc_operations') || current_user_can('edit_gc_dues') ||
    		current_user_can('edit_gc_instruction') || current_user_can('edit_gc_tow') || current_user_can('edit_gc_tow') || current_user_can('read') 
    	)) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
	}
	
}
include 'aircraft-rest.php';
include 'fees-rest.php';
include 'aircraft_types-rest.php';
include 'squawks-rest.php';
include 'flights-rest.php';
include 'flight_types-rest.php';
include 'pilots-rest.php';
include 'status-rest.php';
include 'sign_offs-rest.php';
include 'sign_offs_types-rest.php';


