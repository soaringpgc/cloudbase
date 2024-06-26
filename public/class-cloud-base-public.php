<?php

/**
 * The public-facing functionality of the plugin.
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
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public
 * @author     Your Name <email@example.com>
 */
class Cloud_Base_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $cloud_base       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $cloud_base, $version ) {

		$this->cloud_base = $cloud_base;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cloud_Base_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cloud_Base_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
// // datepicker needed for display_flights shortcode.		 
//  		wp_register_style( 'datepicker',  plugins_url('/cloudbase/includes/datepicker.css'));
 		wp_register_style( 'cloudbase_public_css',  plugins_url('/cloudbase/public/css/cloud-base-public.css'));
 		wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css' );
//  		wp_enqueue_style( 'datepicker');
		wp_enqueue_style( 'cloudbase_public_css');
    	wp_enqueue_style( 'jquery-ui' );  
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cloud_Base_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cloud_Base_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	    wp_register_script( 'workflow',  plugins_url('/cloudbase/includes/workflow.js'));
	    wp_register_script( 'validation',  plugins_url('/cloudbase/includes/backbone-validation-min.js'));	    
     	wp_register_script( 'templates',  plugins_url('/cloudbase/public/js/templates.js'));
     	wp_register_script( 'squawk_scripts',  plugins_url('/cloudbase/public/js/squawk_scripts.js'));
// // needed for display_flights shortcode. 
//  		$dateToBePassed = array(
//  			'root' => esc_url_raw( rest_url() ),
//  			'nonce' => wp_create_nonce( 'wp_rest' ),
//  			'success' => __( 'Data Has been updated!', 'your-text-domain' ),
//  			'failure' => __( 'Your submission could not be processed.', 'your-text-domain' ),
//  			'current_user_id' => get_current_user_id()    	    	
//  		);   	
//  		wp_add_inline_script(  $this->cloud_base, 'const cloud_base_public_vars = ' . json_encode ( $dateToBePassed  ), 'before'); 
	}
	public function register_shortcodes() 
	{
// 		add_shortcode( 'display_flights', array( $this, 'display_flights' ) );
		add_shortcode( 'display_status', array( $this, 'display_status' ) );
		add_shortcode( 'no_fly', array( $this, 'cb_no_fly' ) );
		add_shortcode( 'display_signoffs', array( $this, 'display_signoffs' ) );
		add_shortcode( 'signoff_summary', array( $this, 'signoff_summary' ) );
		add_shortcode( 'update_signoffs', array( $this, 'display_update_signoffs' ) );
		add_shortcode( 'batch_signoffs', array( $this, 'batch_signoffs' ) );
		add_shortcode( 'squawk_sheet', array( $this, 'squawk_sheet' ) );
	} // register_shortcodes()		
// // needed for display_flights shortcode.
// 	public function display_flights($atts = array() ){
// 		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
// 		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-public.js', array( 'wp-api',  'backbone', 'underscore',
// 		 'jquery-ui-datepicker', 'templates', 'workflow',  'validation'), $this->version, false );
// 	
// 		include_once 'partials/cloud-base-public-display.php';
// //		return display_flights();
// 	}
	public function squawk_sheet($atts = array() ){
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/squawk_scripts.js', array( 'wp-api',  'backbone', 'underscore',
		), $this->version, false );
    		$dateToBePassed = array(
 				'restURL' => esc_url_raw( rest_url() ),
 				'nonce' => wp_create_nonce( 'wp_rest' ),
 				'current_user_id' => get_current_user_id(),
 				'user_can' => $this->user_can()    	    	
     		);   	
     		wp_add_inline_script(  $this->cloud_base, 'const signoff_public_vars = ' . json_encode ( $dateToBePassed  ), 'before'
     		);
		ob_start();
		include_once 'partials/cloud-base-squawk_sheet.php';
 			process_squawk_sheet();
  			display_squawk_sheet();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	public function display_status($atts = array() ){
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		ob_start();
	    	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	    	$status_atts = shortcode_atts(array( 'details'=>"false"), $atts, 'display_status');
			include ('partials/cloud-base-aircraft_status.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
    }
    public function cb_no_fly($atts = array() ){
    
		include_once 'partials/cloud-base-no_fly.php';
	}
	public function display_signoffs($atts = array(), $content= null, $tag = '' ){

		include_once 'partials/html-cb-signoffs-display.php';
		return  display_member_signoffs($atts);
	}
	public function signoff_summary($atts = array(), $content= null, $tag = '' ){

		include_once 'partials/html-cb-signoff_summary.php';
		return  signoff_summery($atts);
	}
	public function display_update_signoffs($atts = array(),  $content= null, $tag = '' ){
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	    wp_register_script( 'update_signoffs',  plugins_url('/cloudbase/public/js/update_signoffs.js'));
		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/update_signoffs.js', array( 'wp-api', 'jquery-ui-datepicker' ), $this->version, false );

    		$dateToBePassed = array(
 				'restURL' => esc_url_raw( rest_url() ),
 				'nonce' => wp_create_nonce( 'wp_rest' ),
 				'current_user_id' => get_current_user_id(),
 				'user_can' => $this->user_can()    	    	
     		);   	
     		wp_add_inline_script(  $this->cloud_base, 'const signoff_public_vars = ' . json_encode ( $dateToBePassed  ), 'before'
     		);
		include_once 'partials/html-update-signoff.php';
		return update_signoff_display($atts);
	}
	public function batch_signoffs($atts = array(),  $content= null, $tag = '' ){
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	    wp_register_script( 'update_signoffs',  plugins_url('/cloudbase/public/js/update_signoffs.js'));
		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/update_signoffs.js', array( 'wp-api', 'jquery-ui-datepicker' ), $this->version, false );

    		$dateToBePassed = array(
 				'restURL' => esc_url_raw( rest_url() ),
 				'nonce' => wp_create_nonce( 'wp_rest' ),
 				'current_user_id' => get_current_user_id(),
 				'user_can' => $this->user_can()    	    	
     		);   	
     		wp_add_inline_script(  $this->cloud_base, 'const signoff_public_vars = ' . json_encode ( $dateToBePassed  ), 'before'
     		);
		include_once 'partials/html-cb-batch-signoffs.php';
		return batch_signoff($atts);
	}
	
	 public function user_can()
     {
     	$capabiliteis = array( 'manage_options', 'chief_flight', 'chief_tow', 
 			'edit_gc_operations',  'cb_edit_instruction', 'edit_gc_tow', 'field_manager', 
 			'assistant_field_manager', 'read' );		
 		if( is_user_logged_in() ) { // check if there is a logged in user 	 						
 			forEach($capabiliteis as $c ){
 				if (current_user_can( $c ) ){
 					return($c);
 				}
 		 	}
 	 	} else {		 
			return('read');  	 
	 	}
	 }
	/**
	 * This function updates aircraft details. This is where glider, pilot
	 * instructor, tow pilot and tug are selected. Also corrections can be make to 
	 * take off/landing time and tow alitude. 
	 */
     public function update_aircraft(){ 
//     exit(var_dump($_POST));
//     	global $wpdb;
//	 	$table_name = $wpdb->prefix . "cloud_base_aircraft";	   
	// NTFS - Note to Future Self  we are accessing the REST interface here.  
     	$retrieved_nonce = $_POST['_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'update_aircraft' ) ) die( 'Failed security check' );
 		if (!current_user_can('cb_edit_maintenance') ) die( 'Failed acccess check' );    
 		   $request = new WP_REST_Request( 'PUT', '/cloud_base/v1/aircraft');
 		   $request->set_param( 'aircraft_id', $_POST['key'] );
 		   $request->set_param( 'annual_due_date', $_POST['annual_due_date'] );
 		   $request->set_param( 'registration_due_date', $_POST['registration_due_date'] );
 		   $request->set_param( 'transponder_due', $_POST['transponder_due'] );
 		   $request->set_param( 'comment', $_POST['comment'] );
 		   $request->set_param( 'status', $_POST['status'] );
 		   $response = rest_do_request( $request);
//  		   var_dump($response);
 		
     	wp_redirect($_POST['source_page']);
     } //updateAircraft()    
     // add_action('template_redirect');
	public function custom_redirect() 
	{
// 		$user = sp_get_current_user();
		if(
			!is_user_logged_in() &&
			!is_home()&&
			!is_login()		
		) {
			$url = home_url();
			wp_redirect( $url . '/login');
			exit;
		}
	} // custom_redirect()   
}
