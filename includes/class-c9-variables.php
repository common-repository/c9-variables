<?php

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
 * @package    C9_Variables
 * @subpackage C9_Variables/includes
 * @author     CloudNineApps
 */
class C9_Variables {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      C9_Variables_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

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
	    
		if ( defined( 'C9_VARIABLES_VERSION' ) ) {
		    $this->version = C9_VARIABLES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'c9-variables';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - C9_Variables_Loader. Orchestrates the hooks of the plugin.
	 * - C9_Variables_i18n. Defines internationalization functionality.
	 * - C9_Variables_Admin. Defines all hooks for the admin area.
	 * - C9_Variables_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
	    
	    // Load utils classes
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/utils/class-c9-logger.php';
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/utils/class-c9-text-utils.php';
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/utils/class-c9-http-utils.php';
	    
	    // Load helper classes
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/helper/class-c9-plugin-ui-helper.php';
	    
	    // Load basic classes
	    if (!class_exists('C9_Variables_Constants')) {
        	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/common/class-c9-variables-constants.php';
        	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/admin/core/class-c9-variables-admin-delegate.php';
        	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/admin/ui/class-c9-variables-admin-ui.php';
        	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/code/basic/public/core/class-c9-variables-public-delegate.php';
	    }
	    
	    // Set log level
	    $debug_mode = get_option(C9_Variables_Constants::$DEBUG_MODE_SETTING);
	    if ($debug_mode === 'true') {
	        C9_Logger::enable_debug(true);
	    }
	    else {
	        C9_Logger::enable_debug(false);
	    }
	    
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-c9-variables-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-c9-variables-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-c9-variables-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-c9-variables-public.php';

		$this->loader = new C9_Variables_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the C9_Variables_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new C9_Variables_i18n();

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
		$delegate = new C9_Variables_Admin_Delegate();
		$ui = new C9_Variables_Admin_UI($delegate);
	    $plugin_admin = new C9_Variables_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_loader(), $delegate, $ui );

		$this->loader->add_action('init', $plugin_admin, 'init');
		$this->loader->add_action('admin_init', $delegate, 'admin_init');
		$this->loader->add_action('save_post_c9_vars_variable', $delegate, 'post_variable_save_handler', 10, 1);
		$this->loader->add_action('admin_menu', $ui, 'register_menu');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('wp_ajax_nopriv_c9_vars_get_variables', $delegate, 'get_variables');
		$this->loader->add_action('wp_ajax_c9_vars_get_variables', $delegate, 'get_variables');
		$this->loader->add_action('wp_ajax_nopriv_c9_vars_update_variable_last_used', $delegate, 'update_variable_last_used');
		$this->loader->add_action('wp_ajax_c9_vars_update_variable_last_used', $delegate, 'update_variable_last_used');
		$this->loader->add_filter('admin_footer_text', $ui, 'get_footer_message');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	    $delegate = new C9_Variables_Public_Delegate();
	    $plugin_public = new C9_Variables_Public( $this->get_plugin_name(), $this->get_version(), $delegate );

		$this->loader->add_action( 'init', $plugin_public, 'init' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    C9_Variables_Loader    Orchestrates the hooks of the plugin.
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
