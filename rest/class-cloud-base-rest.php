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
//		$this->resource_base_path = '/cloud_base';
		$this->namespace = $this->plugin_name. '/v' .  $this->rest_version; 			
	}

	public function register_routes() {
	
	  $this->resource_path = '/aircraft' . '(?:/(?P<id>[\d]+))?';

      register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),
         	'args' => array('id'=> array('type'=>'integer', 'required'=> false, 'sanitize_callback'=> 'absint'))), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_aircraft_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_aircraft_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	
      
     $this->resource_path = '/fees' . '(?:/(?P<id>[\d]+))?';
    
     register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_fees_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_fees_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	 
    $this->resource_path = '/squawks' . '(?:/(?P<id>[\d]+))?'; 
       
    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_squawks_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_squawks_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	              
     $this->resource_path = '/flights' . '(?:/(?P<id>[\d]+))?';    register_rest_route( $this->namespace, $this->resource_path, 
        array(	
      	  array(
      	    'methods'  => \WP_REST_Server::READABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_get_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),), 
          array(
      	    'methods'  => \WP_REST_Server::CREATABLE,
             // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_post_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),       	
      	  array(	
      	    'methods'  => \WP_REST_Server::EDITABLE,  
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => array( $this, 'cloud_base_flights_put_edit_callback' ),
            // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_private_access_check' ),),
          array (
         	 'methods'  => \WP_REST_Server::DELETABLE,
              // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
             'callback' => array( $this, 'cloud_base_flights_delete_callback' ),
             // Here we register our permissions callback. The callback is fired before the main callback to check if the current user can access the endpoint.
         	'permission_callback' => array($this, 'cloud_base_admin_access_check' ),        	 		      		
      	  )
      	)
      );	                
             
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
	
// call back for fees:	
	public function cloud_base_fees_get_callback( \WP_REST_Request $request) {
			 return rest_ensure_response( 'Hello World, this is the Cloud Based Fees REST API ' . $request['id']);

		/* 
			Process your GET request here.	
				
		*/
	}	
	public function cloud_base_fees_post_callback( \WP_REST_Request $request) {
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_fees_put_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_fees_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}
// call back for aircraft:	
	public function cloud_base_aircraft_get_callback( \WP_REST_Request $request) {
	  global $wpdb;
	    if ( isset($request['id']) ){
	       return rest_ensure_response( 'Hello World, this is the Cloud Based Aircraft REST API ' . $request['id']);
	  
	    } elseif (isset($request['type'])) {
	  		return rest_ensure_response( 'Read aircraft tabe and list of of type= ' . $request['type']);
	    }
		/* 
			Process your GET request here.		
		*/
	}	
	public function cloud_base_aircraft_post_callback( \WP_REST_Request $request) {
	
	  return rest_ensure_response( 'Hello World, this is the POST Cloud Based Aircraft REST API ' . $request['id']);
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_aircraft_put_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_aircraft_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
// call back for squawks:	
	public function cloud_base_squawks_get_callback( \WP_REST_Request $request) {
	
	 return rest_ensure_response( 'Hello World, this is the Cloud Based Squawk REST API ' . $request['id']);
		/* 
			Process your GET request here.		
		*/
	}	
	public function cloud_base_squawks_post_callback( \WP_REST_Request $request) {
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_squawks_put_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_squawks_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
// call back for flights:	
	public function cloud_base_flights_get_callback( \WP_REST_Request $request) {
	
	 return rest_ensure_response( 'Hello World, this is the Cloud Based flilght REST API ' . $request['id']);
		/* 
			Process your GET request here.		
		*/
	}	
	public function cloud_base_flights_post_callback( \WP_REST_Request $request) {
		/* 
			Process your POST request here.
		*/
	}
	public function cloud_base_flights_put_callback( \WP_REST_Request $request) {
		/* 
			Process your PUT request here.
		*/
	}
	public function cloud_base_flights_delete_callback( \WP_REST_Request $request) {
		/* 
			Process your DELETE request here.			
		*/
	}	
		
	
	
}

	
	

