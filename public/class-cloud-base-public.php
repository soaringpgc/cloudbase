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
		wp_register_style( 'datepicker',  plugins_url('/cloudbase/includes/datepicker.css'));
//		wp_enqueue_style( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'css/cloud-base-public.css', array(), $this->version, 'all' );
 		wp_register_style( 'cloudbase_public_css',  plugins_url('/cloudbase/public/css/cloud-base-public.css'));
		wp_enqueue_style( 'datepicker');
		wp_enqueue_style( 'cloudbase_public_css');
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
	    wp_register_script( 'zepto',  plugins_url('/cloudbase/includes/zepto.js'));
	    wp_register_script( 'workflow',  plugins_url('/cloudbase/includes/workflow.js'));
	    wp_register_script( 'validation',  plugins_url('/cloudbase/includes/backbone-validation-min.js'));
	    
//	    wp_register_script( 'backbone-relational',  plugins_url('/Cloud-Base/includes/backbone-relational.js'));
     	wp_register_script( 'templates',  plugins_url('/cloudbase/public/js/templates.js'));
//
		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-public.js', array( 'wp-api', 'zepto' ,  'backbone', 'underscore',
		 'jquery-ui-datepicker', 'templates', 'workflow',  'validation'), $this->version, false );

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
	public function register_shortcodes() {
		add_shortcode( 'display_flights', array( $this, 'display_flights' ) );
	} // register_shortcodes()
	public function display_flights(){
		include_once 'partials/cloud-base-public-display.php';
//		return display_flights();
	}

}
