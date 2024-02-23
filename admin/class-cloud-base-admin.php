<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/admin
 * @author     Your Name <email@example.com>
 */
class Cloud_Base_Admin {

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
	 * @param      string    $cloud_base       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $cloud_base, $version ) {

		$this->cloud_base = $cloud_base;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
	
 		wp_register_style( 'cloudbase_css',  plugins_url('/cloudbase/admin/css/cloud-base-admin.css'));
//  		wp_register_style( 'datepicker',  plugins_url('/cloudbase/includes/datepicker.css'));
//  		wp_enqueue_style( 'datepicker');
 		wp_enqueue_style( 'cloudbase_css');
	}

	/**
	 * Register the JavaScript for the admin area.
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
        wp_register_script( 'cloudbase_admin_templates',  plugins_url('/cloudbase/admin/js/templates.js'));
//
// Moved below, so scripts now only load when the setting page is loaded. 

// 		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-admin.js', 
// 		array( 'wp-api', 'jquery' ,  'backbone', 'underscore',
// 		'cloudbase_admin_templates'), $this->version, false );


	//localize data for script
// 		wp_localize_script( $this->cloud_base, 'POST_SUBMITTER', array(
// 			'root' => esc_url_raw( rest_url() ),
// 			)
//		);
	}
    public function add_settings_page() {
		$this->plugin_screen_hook_suffix = add_options_page(	
			'Cloud Base Settings', 'Cloud Base','read', 'cloud_base',
			array( $this, 'display_settings_page') );	
		add_action('admin_enqueue_scripts', function($hook) {
			if($hook !== $this->plugin_screen_hook_suffix){
				return;
			}
			wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-admin.js', 
				array( 'wp-api', 'jquery' ,  'backbone', 'underscore', 'cloudbase_admin_templates'), $this->version, false );
// 			wp_enqueue_style( 'datepicker');
//  			wp_enqueue_style( 'cloudbase_css');
				//localize data for script			
		
     		$dateToBePassed = array(
 				'root' => esc_url_raw( rest_url() ),
 				'nonce' => wp_create_nonce( 'wp_rest' ),
 				'success' => __( 'Data Has been updated!', 'your-text-domain' ),
 				'failure' => __( 'Your submission could not be processed.', 'your-text-domain' ),
 				'current_user_id' => get_current_user_id()    	    	
     		);   	
     		wp_add_inline_script(  $this->cloud_base, 'const cloud_base_admin_vars = ' . json_encode ( $dateToBePassed  ), 'before'
     		);
      	});			
	}
	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	  
	public function display_settings_page() {
		include_once 'partials/cloud-base-admin-display.php';
	}
	public function the_config_page_response(){
    	check_admin_referer('config_page');
     	$glider_club_long_name = sanitize_text_field($_POST['long_name']);
    	$glider_club_short_name = sanitize_text_field($_POST['short_name']);
    	$glider_club_unit = sanitize_text_field($_POST['units']);
    	$cloud_base_fy_month = sanitize_text_field($_POST['fyStartMonth']);
    	$cloud_base_fy_day = sanitize_text_field($_POST['fyStartDay']);
    	$cloud_base_se_month = sanitize_text_field($_POST['sessionStartMonth']);
    	$cloud_base_se_day = sanitize_text_field($_POST['sessionStartDay']);
    	update_option('glider_club_long_name', $glider_club_long_name  );
    	update_option('glider_club_short_name', $glider_club_short_name  ); 	
    	update_option('glider_club_tow_units', $glider_club_unit  ); 
    	
    	update_option('cloud_base_fy_month', $cloud_base_fy_month ); 
    	update_option('cloud_base_fy_day', $cloud_base_fy_day ); 
    	update_option('cloud_base_session_month', $cloud_base_se_month ); 
    	update_option('cloud_base_session_day', $cloud_base_se_day); 
	
    	wp_redirect('options-general.php?page=cloud_base&tab=config_page');
    	exit();    		
    }  
    
	public function cloud_base_new_signoffs($user_id){
	// this function adds required sign off the new members automatically. 
//	exit(var_dump($user_id));
		  global $wpdb;
		  $table_types = $wpdb->prefix . "cloud_base_signoffs_types";
		  $signoffs = $wpdb->get_results(" SELECT * From " . $table_types . " WHERE applytoall = 1" );
		  $table_member = $wpdb->prefix . "cloud_base_member_signoffs";
		  $today = date('Y-m-d');
		  echo $today;
//		  $user = get_user_by('ID', $user_id);
		  $expire_date = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " - 1460 day"));
		  foreach ($signoffs as $signoff) {
		  // if the uses alredady has signoff do not add again. 
		  	$results = $wpdb->get_results( 
                    $wpdb->prepare("SELECT * FROM {$table_member} WHERE member_id = %d AND Signoff_id=%d", $user_id, $signoff->id) );
		  	if ($results == NULL ){		  
		  		$result = $wpdb->insert($table_name, array('date_entered'=> $today, 'date_effective'=> $today, 'date_expire'=>$expire_date, 
		  			'member_id'=> $user_id, 'authority_id'=>get_current_user_id(), 'Signoff_id'=>$signoff->id ));  
		  	}
		  }
	}    
 	public function cloud_base_inactive_signoffs($user_id, $role, $old_roles) {
		if(in_array('inactive', $old_roles ) && $role = 'subscriber'){
			$result = $this->cloud_base_new_signoffs($user_id);
		}
 	}
 	// add required signoff for new members. 
	public function cloud_base_add_new_user_signoffs($user_id, $notify) {
		global $wpdb;  
	    $table_types = $wpdb->prefix . "cloud_base_signoffs_types";   
	    $table_signoffs = $wpdb->prefix . "cloud_base_member_signoffs";  
	    $authority = get_current_user_id();    
		$sql = "SELECT * FROM {$table_types} WHERE `active` = 1 AND `applytoall` = 1 ";
		$items = $wpdb->get_results( $sql, OBJECT);
		if( $wpdb->num_rows > 0 ) {
			foreach($items as $v){			
				$wpdb->insert( $table_signoffs, array ('member_id' => $user_id, 'signoff_id' => $v->id, 'date_entered' =>  date('Y-m-d'), 
					'date_effective' => '1970-01-01', 'date_expire' => '1970-01-01', 'authority_id' => $authority));					
			}
		}		
 	} 
 	/**
	 * add address and phone number to admin page
	 *
	 * @since  1.0.0
	 */
	  
	public function cloudbase_profile_additional_info( $user ) {
		include_once 'partials/profile_additions.php';
	}	
	function cloudbae_save_profile_additional_info( $user_id ) {
	  $saved = false;
	  if ( current_user_can( 'edit_user', $user_id ) ) {
	  	update_user_meta( $user_id, 'address1', $_POST['address1'] );
	    update_user_meta( $user_id, 'address2', $_POST['address2'] );
	    update_user_meta( $user_id, 'city', $_POST['city'] );
	    update_user_meta( $user_id, 'state', $_POST['state'] );
	    update_user_meta( $user_id, 'zip', $_POST['zip'] );

	    update_user_meta( $user_id, 'cel', $_POST['cel'] );
	    update_user_meta( $user_id, 'tel', $_POST['tel'] );
	    update_user_meta( $user_id, 'wrk', $_POST['wrk'] );
	    $saved = true;
	  }
	  return true;
	}
}
