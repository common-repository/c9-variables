<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes
 * @author     CloudNineApps
 */
class C9_Variables_Activator {

	/**
	 * Performs plugin activation.
	 *
	 * @since    1.0.0
	 */
    public static function activate() {
        if (class_exists('C9_Variables_Pro')) {
            wp_die(__("The <strong>C9 Variables Pro</strong> plugin has already been installed and activated. Please consider using it as it provides more capabilities than the <strong>C9 Variables</strong> plugin. If you still prefer to use <strong>C9 Variables</strong>, please deactivate the <strong>C9 Variables Pro</strong> plugin first.", 'c9-variables'),
                __('Plugin activation error', 'c9-variables'), array('back_link' => true));
        }
    }
}
