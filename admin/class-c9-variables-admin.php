<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/admin
 * @author     CloudNineApps
 */
class C9_Variables_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/** The loader. */
	private $loader;
	
	/** The delegate. */
	private $delegate;
	
	/** The UI. */
	private $ui;
	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader, $delegate, $ui ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->loader = $loader;
		$this->delegate = $delegate;
		$this->ui = $ui;

	}
	
	/** Performs initialization. */
	public function init() {
	    C9_Logger::debug('C9_Variables_Admin::init(): Invoked');
	    
	    // Register custom post type
	    $this->delegate->register_cpt();
	    
	    // Register settings
	    $this->delegate->register_settings();
	    
	    // Register custom filter and actions
	    $this->delegate->register_custom_filter_and_actions();
	    
	    // MCE plugin extension setup
	    if ((current_user_can('edit_posts') || current_user_can('edit_pages'))
	        && get_user_option('rich_editing') === 'true') {
	            add_filter('mce_external_plugins', [$this->ui, 'add_tinymce_plugin']);
	            add_filter('mce_buttons', [$this->ui, 'add_tinymce_plugin_variables_button']);
	    }
	    C9_Logger::debug('C9_Variables_Admin::init(): Initialization completed.');
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
	    wp_enqueue_style( "c9-common", plugin_dir_url( __FILE__ ) . 'css/common/c9-common.css', array(), $this->version, 'all' );
	    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/c9-variables-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/c9-variables-admin.js', array( 'jquery' ), $this->version, false );
		
		// AJAX setup
		wp_localize_script($this->plugin_name, C9_Variables_Constants::$GET_VARIABLES_ACTION, array(
		    'ajax_url' => admin_url('admin-ajax.php'),
		    C9_Variables_Constants::$NONCE_PARAM => wp_create_nonce(C9_Variables_Constants::$GET_VARIABLES_ACTION)
		));
		wp_localize_script($this->plugin_name, C9_Variables_Constants::$UPDATE_VARIABLE_LAST_USED_ACTION, array(
		    'ajax_url' => admin_url('admin-ajax.php'),
		    C9_Variables_Constants::$NONCE_PARAM => wp_create_nonce(C9_Variables_Constants::$UPDATE_VARIABLE_LAST_USED_ACTION)
		));
	}
}
