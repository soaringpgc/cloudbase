<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cloud_Base
 * @subpackage Cloud_Base/includes
 * @author     Your Name <email@example.com>
 */
class Cloud_Base {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cloud_Base_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $cloud_base    The string used to uniquely identify this plugin.
	 */
	protected $cloud_base;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CLOUD_BASE_VERSION' ) ) {
			$this->version = CLOUD_BASE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->cloud_base = 'cloud-base';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_rest_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cloud_Base_Loader. Orchestrates the hooks of the plugin.
	 * - Cloud_Base_i18n. Defines internationalization functionality.
	 * - Cloud_Base_Admin. Defines all hooks for the admin area.
	 * - Cloud_Base_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cloud-base-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cloud-base-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cloud-base-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cloud-base-public.php';

		/**
		 * The class responsible for defining all actions that occur in the rest api
		 * of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rest/class-cloud-base-rest.php';

		$this->loader = new Cloud_Base_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cloud_Base_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cloud_Base_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cloud_Base_Admin( $this->get_cloud_base(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		  //  add the config tab page 
		$this->loader->add_action( 'admin_post_config_page', $plugin_admin, 'the_config_page_response');
		// when a new user is added add required signoffs.
 		$this->loader->add_action( 'user_register', $plugin_admin, 'cloud_base_new_signoffs', 15);
// 'set_user_role' appears to be disabled by members plugin. 
// 		$this->loader->add_action( 'set_user_role', $plugin_admin, 'cloud_base_inactive_signoffs', 5, 3);
	 	$this->loader->add_action( 'profile_update', $plugin_admin, 'cloud_base_inactive_signoffs', 5, 3);
	 	$this->loader->add_action( 'edit_user_created_user', $plugin_admin, 'cloud_base_add_new_user_signoffs', 5, 3);	 	
	 	
	 	$this->loader->add_action( 'show_user_profile', $plugin_admin, 'cloudbase_profile_additional_info', 5, 3);
	 	$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'cloudbase_profile_additional_info', 5, 3);
	 	$this->loader->add_action( 'personal_options_update', $plugin_admin, 'cloudbae_save_profile_additional_info', 5, 3);	 	
	 	$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'cloudbae_save_profile_additional_info', 5, 3);	 
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cloud_Base_Public( $this->get_cloud_base(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );		
		$this->loader->add_action( 'admin_post_update_aircraft', $plugin_public, 'update_aircraft' );
	}

	/**
	 * Register all of the hooks related to the REST functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_rest_hooks() {

		$plugin_rest = new Cloud_Base_Aircraft( $this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Fees($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Types($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Squawks($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Flights($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Pilots($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Status($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');
		
		$plugin_rest = new Cloud_Base_Sign_offs($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_Sign_off_types($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');

		$plugin_rest = new Cloud_Base_flight_types($this->get_cloud_base(), $this->get_version());
		$this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes');
	    /**
	     * Register the /wp-json/cloudbase/v1/posts endpoint so it will be cached.
	     * using wp-rest-cache 
	     */
// 	    if ( is_plugin_active('wp-rest-cache/wp-rest-cache.php') ) {
// 	    	  $this->loader->add_filter( 'wp_rest_cache/allowed_endpoints', $plugin_rest, 'cb_add_cloudbase_posts_endpoint');
// 	    	}
 	    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_cloud_base() {
		return $this->cloud_base;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cloud_Base_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
