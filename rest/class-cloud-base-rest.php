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

	public	$value_lable_period = array("yearly"=>"Yearly", "biennial"=>"Biennial", "yearly-eom"=>"Yearly-EOM", "biennial-eom"=>"Biennial-EOM", "no_expire"=>"No expire", 
				"monthly" => "Monthly", "quarterly" => "Quarterly", "fixed"=>"Fixed Date" );		

//	public  $cloud_base_authoritys = array("read"=>"Self", "cb_edit_dues"=>"Treasurer", "cb_edit_operations"=>"Operations", 
//				    "cb_edit_instruction"=>"CFI-G", "cb_edit_cfig"=>"Chief CFI-G", "cb_chief_tow"=>"Chief Tow Pilot");
				    
	public function cloud_base_roles($authority){
		return array_keys($this->$cloud_base_authoritys, $authority );
//		return =array_search($authority, $this->$cloud_base_authoritys)
	}

// register routes must be overridden for each endpoint. 
//	public function register_routes() {
//	                                     
//    }
 
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
//    	if ( !(current_user_can('edit_gc_instruction'))) {

    	if ( !(current_user_can(cloud_base_roles('CFI_G')))) {
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
    	// This is a white-listing approach. You could alternatively do this via black-listing, by returning false here and changing the permissions check.	
    	return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
	}   
	public function cloud_base_flight_editor_access_check(){
	// put your access requirements here. You might have different requirements for each access method. 
	// can read, at least a subscriber. 
    	if (  current_user_can( 'flight_edit' )) {
    	    return true;
     	}
    	// This is a white-listing approach. You could alternatively do this via black-listing, by returning false here and changing the permissions check.	
    	return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
	}   
	   
	public function cloud_base_private_access_check(){
	return true;
	// put your access requirements here. You might have different requirements for each
	// access method. I'm showing only one here. 
    	if ( (current_user_can( 'edit_users' ) || current_user_can('edit_gc_operations') || current_user_can('edit_gc_dues') ||
    		current_user_can('edit_gc_instruction') || current_user_can('edit_gc_tow') || current_user_can('edit_gc_tow') || current_user_can('read') 
    	)) {
     	   return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not authorized for that.', 'my-text-domain' ), array( 'status' => 401 ) );
    	}
    	// This is a white-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
    	return true;	
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
//
// 	 	public function does_user_exist( int $user_id ) : bool {
// 	 	  return (bool) get_users( [ 'include' => $user_id, 'fields' => 'ID' ] );
// 		}
		public function cb_expire($start_date, $signoff_id){
// do the database look up here. the call expire_date	
		global $wpdb;
	 	$table_signoffs = $wpdb->prefix . "pgc_signoffs_types";
	 	$sql = $wpdb->prepare("SELECT * FROM {$table_signoffs} WHERE `id` = %d", $signoff_id);	 	
  		$signoff_duration = $wpdb->get_row($sql);
		$date_expire = $this->gc_expire_date($start_date, $signoff_duration->period, $signoff_duration->fixed_date);
		return($date_expire);	
	}	
		
	public  function select_fields( $request, $valid_fields){
	// Associative aray of valid fields, Key is string passed in the "_fileds" varable
	// value is what need to be passed to mySQL. 	
	    $select_string = "";
	    if (!empty($request['audit'])){
		  $select_string =  "s.valid_until, " . $select_string;
	    }	
		$valid_keys = array_keys($valid_fields );		
		if(!empty($request['_fields'])){
			$field_array = explode(',' , $request['_fields']);			
			foreach(array_reverse($field_array) as $field){
			// get the last elemeint as we don't want a ',' after it. 
				$last = array_pop($field_array);
				if(in_array($last, $valid_keys)) {
					$last = $valid_fields[$field];
					break;
				}
			}						
			foreach($field_array as $field){
			// build up the SELECT list. 
				if(in_array($field, $valid_keys)) {
					$select_string = $select_string . $valid_fields[$field] . ', ';
				}
			}
		} else {
			$valid_values = array_values($valid_fields );
			$last = array_pop($valid_values);
			foreach($valid_values as $field){
				$select_string = $select_string . $field . ', ';
			}	
		} 
		return($select_string . $last);
	}

// request -- HTML  request string 
// $valid-filters associative array of valid filters, Key is varable sent in the html string and 
// value is the actual string that needs to be passed to sql 
// if the audit flag is set the valid until field is added to the output. 
//  note "s" must be a alias for table 'cloud_base_aircraft' in the SQL string. 

	public function select_filters($request, $valid_filters){
	  global $wpdb;
	  $valid_keys = array_keys($valid_filters );		  
	  if (!empty($request['audit'])){
		 $filter_string = "s.valid_until > -1 ";
	  } else {
	  	$filter_string = "(s.valid_until = 0  or s.valid_until IS NULL OR s.valid_until > NOW()) ";
	  } 
	  foreach($valid_keys as $key ){
	  	if(!empty($request[$key]) ){
	  		$filter_string = $filter_string . ' AND '. $valid_filters[$key] .'='.  $wpdb->prepare('%s' , $request[$key]);
	  	}
	  }
	return($filter_string);
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


