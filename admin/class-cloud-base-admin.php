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

		wp_enqueue_style( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'css/cloud-base-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->cloud_base, plugin_dir_url( __FILE__ ) . 'js/cloud-base-admin.js', array( 'jquery' ), $this->version, false );

	}

}
