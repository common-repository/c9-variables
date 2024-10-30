<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/public
 * @author     CloudNineApps
 */
class C9_Variables_Public {

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

	/** The delegate. */
	private $delegate;
	
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $delegate ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->delegate = $delegate;

	}
	
	/** Performs initialization. */
	public function init() {
	    C9_Logger::debug('C9_Variables_Public::init(): Invoked');
	    
	    // Register shortcodes
	    $this->delegate->register_shortcodes();
	    
	    C9_Logger::debug('C9_Variables_Public::init(): Initialization completed.');
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
		 * defined in C9_Variables_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The C9_Variables_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/c9-variables-public.css', array(), $this->version, 'all' );

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
		 * defined in C9_Variables_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The C9_Variables_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/c9-variables-public.js', array( 'jquery' ), $this->version, false );

	}
	
}
