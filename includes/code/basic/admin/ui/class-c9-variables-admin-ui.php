<?php

/**
 * Class to provide the admin UI functionality.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/admin/ui
 * @author     CloudNineApps
 */
class C9_Variables_Admin_UI {
    
    /** The delegate. */
    private $delegate;
    
    
    public function __construct($delegate) {
        $this->delegate = $delegate;
    }
    
    /** Returns the delegate. */
    public function get_delegate() {
        return $this->delegate;
    }
    
    /** Registers the menu. */
    public function register_menu() {
        C9_Logger::debug('C9_Variables_Admin_UI::register_menu(): Invoked');
        add_action('admin_notices', [$this, 'show_admin_message']);
        $this->register_custom_ui_actions();
        $menu_slug = $this->add_menu_page();
        add_submenu_page($menu_slug, __('Variables Settings', 'c9-variables'), __('Settings', 'c9-variables'), 'manage_options', C9_Variables_Constants::$VARS_SETTINGS_PAGE, [$this, 'settings_page']);
        add_submenu_page($menu_slug, __('Other Plugins', 'c9-variables'), __('Other Plugins', 'c9-variables'), 'manage_options', C9_Variables_Constants::$VARS_OTHER_PLUGINS_PAGE, [$this, 'other_plugins_page']);
        add_submenu_page($menu_slug, __('About Variables', 'c9-variables'), __('About Variables', 'c9-variables'), 'manage_options', C9_Variables_Constants::$VARS_ABOUT_PAGE, [$this, 'about_page']);
    }
    
    /** Shows the admin messages. */
    public function show_admin_message() {
        $data = $this->delegate->get_admin_message_data();
        C9_Plugin_UI_Helper::show_admin_message($data);
    }
    
    /** Prepares the settings page. */
    public function settings_page() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $this->delegate->refresh_subscription_status();
        $plugin_data = $this->delegate->get_plugin_data();
?>
<div class="wrap">
  <h1><?php echo $plugin_data['Name']; _e(' Settings', 'c9-variables'); ?></h1>
  <?php settings_errors(); ?>
  <h2 class="nav-tab-wrapper">
    <a href="#" class="nav-tab nav-tab-active"><?php _e('General', 'c9-variables'); ?></a>
  </h2>
  <div class="c9-settings">
    <form method="post" action="options.php">
<?php
        settings_fields(C9_Variables_Constants::$VARS_SETTINGS);
        do_settings_sections(C9_Variables_Constants::$VARS_SETTINGS);
?>
       <table class="form-table">
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <?php $this->add_settings_fields(); ?>
        <tr>
          <td colspan="2" align="center"><?php submit_button(); ?></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php
    }
    
    /** Prepares the other plugins page. */
    public function other_plugins_page() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $plugins = $this->delegate->get_other_plugins();
?>
<div class="wrap">
  <h1><?php _e('Cloud Nine Apps Other Plugins ', 'c9-variables'); ?></h1>
  <p/>
<?php
        if ($plugins != null && count($plugins) > 0) {
            // Other plugins data avaiable
            $plugins = json_decode($plugins, true);
            C9_Plugin_UI_Helper::show_other_plugins($plugins);
        } // if (plugins data available)
        else {
            _e('Plugin data is not available currently. Please try again after some time.', 'c9-variables');
        }
?>
</div>
<?php
    }
    
    /** Prepares the about page. */
    public function about_page() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $plugin_data = $this->delegate->get_plugin_data();
        $slug = $this->delegate->get_plugin_slug();
        $logo_url = $this->delegate->get_plugin_url() . "/assets/images/icon-256x256.png";
        C9_Plugin_UI_Helper::show_plugin_about_content($plugin_data, $slug, $logo_url);
?>
<?php
    }
    
    /** Gets the enhanced footer message when within plugin settings. Otherwise, returns the passed in footer message. */
    public function get_footer_message($txt) {
        if ($this->is_plugin_admin_page()) {
            $txt = $this->prepare_footer_message();
        }
        return $txt;
    }
    
    /** Adds the TinyMCE external plugin. */
    public function add_tinymce_plugin($plugin_array) {
        C9_Logger::debug('C9_Variables_Admin_UI::add_tinymce_plugin(): Invoked');
        $plugin_array['c9_vars_variables_button'] = $this->get_js_url();
        return $plugin_array;
    }
    
    /** Adds the variables button. */
    public function add_tinymce_plugin_variables_button($buttons) {
        C9_Logger::debug('C9_Variables_Admin_UI::add_tinymce_plugin_variables_button(): Invoked');
        array_push($buttons, 'c9_vars_variables_button');
        #unset($buttons[0]);
        #unset($buttons[1]);
        return $buttons;
    }
    
    /** Returns true if the current page is a plugin admin page. */
    public function is_plugin_admin_page() {
        $plugin_pages = [C9_Variables_Constants::$VARS_SETTINGS_PAGE, C9_Variables_Constants::$VARS_OTHER_PLUGINS_PAGE, C9_Variables_Constants::$VARS_ABOUT_PAGE];
        $page = (isset($_GET['page'])) ? $_GET['page'] : '';
        C9_Logger::debug(sprintf("C9_Variables_Admin_UI::is_plugin_admin_page(): Current page: '%s'", $page));
        return in_array($page, $plugin_pages);
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // Methods to be overriden to provide more enhanced functionality
    ////////////////////////////////////////////////////////////////////////////////
    
    /** Registers any custom UI actions. */
    protected function register_custom_ui_actions() {
    }
    
    /** Returns the javascript URL. */
    protected function get_js_url() {
        return plugin_dir_url( __FILE__ ) . '../../../../../admin/js/c9-variables-admin.js';
    }
    
    /** Adds the menu page and returns the menu slug. */
    protected function add_menu_page() {
        $menu_slug = C9_Variables_Constants::$VARS_MENU_SLUG;
        add_menu_page(__('Variables', 'c9-variables'), __('Variables', 'c9-variables'), 'edit_variables', $menu_slug, '', $this->get_delegate()->get_plugin_url() . '/assets/images/icon-bw-20x20.png');
        return $menu_slug;
    }
    
    /**
     * Adds the settings fields.
     * 
     * Note: There's no need to add the table, initial space and submit button. Only add settings rows.
     */
    protected function add_settings_fields() {
?>        
      <tr>
        <th><?php _e('Enable Debug Mode?', 'c9-variables'); ?></th>
        <td><input type="checkbox" name="<?php echo C9_Variables_Constants::$DEBUG_MODE_SETTING; ?>" value="true" <?php echo esc_attr(get_option(C9_Variables_Constants::$DEBUG_MODE_SETTING)) == 'true' ? 'checked="checked"' : ''; ?>/></td>
      </tr>
      <tr>
        <th><?php _e('Allow Anonymous Usage Tracking?', 'c9-variables'); ?></th>
        <td><input type="checkbox" name="<?php echo C9_Variables_Constants::$USAGE_TRACKING_SETTING; ?>" value="true" <?php echo esc_attr(get_option(C9_Variables_Constants::$USAGE_TRACKING_SETTING)) == 'true' ? 'checked="checked"' : ''; ?>/></td>
      </tr>
<?php        
    }
    
    /** Prepares and returns the footer message.*/
    protected function prepare_footer_message() {
        $txt = "If you like <strong>C9 Variables</strong> please leave us a <a href='https://wordpress.org/support/plugin/c9-variables/reviews?rate=5#new-post' target='_blank' class='wc-rating-link' data-rated='Thank you!'>&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thanks in advance!";
        return $txt;
    }
}
