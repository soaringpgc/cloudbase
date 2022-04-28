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
 		wp_register_style( 'datepicker',  plugins_url('/cloudbase/includes/datepicker.css'));
 		wp_enqueue_style( 'datepicker');
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
     	wp_register_script( 'backforms',  plugins_url('/cloudbase/includes/backform.js'));
        wp_register_script( 'cloudbase_admin_templates',  plugins_url('/cloudbase/admin/js/templates.js'));

		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-admin.js', 
		array( 'wp-api', 'jquery' ,  'backbone', 'underscore',
		 'jquery-ui-datepicker', 'cloudbase_admin_templates', 'backforms'), $this->version, false );

	//localize data for script
		wp_localize_script( $this->cloud_base, 'POST_SUBMITTER', array(
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'success' => __( 'Data Has been updated!', 'your-text-domain' ),
			'failure' => __( 'Your submission could not be processed.', 'your-text-domain' ),
			'current_user_id' => get_current_user_id()
			)
		);
	}
    public function add_settings_page() {
		$this->plugin_screen_hook_suffix = add_options_page(	
			'Cloud Base Settings', 'Cloud Base','read', 'cloud_base',
			array( $this, 'display_settings_page') );		
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
    

    
}
