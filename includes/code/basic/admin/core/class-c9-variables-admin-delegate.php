<?php

/**
 * The delegate to provide core business logic for admin.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/admin/core
 * @author     CloudNineApps
 */
class C9_Variables_Admin_Delegate {
    
    /** Performs admin related initialization. */
    public function admin_init() {
        $this->setup_capabilities();
    }
    
    /** Returns the plugin home directory. */
    public function get_plugin_home_dir() {
        return WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->get_plugin_slug();
    }
    
    /** Returns the path to the plugin file. */
    public function get_plugin_file() {
        $plugin_home = $this->get_plugin_home_dir();
        $plugin_file = $plugin_home . DIRECTORY_SEPARATOR . $this->get_plugin_file_name();
        return $plugin_file;
    }
    
    /** Returns the plugin URL. */
    public function get_plugin_url() {
        $plugin_file = $this->get_plugin_file();
        $url = plugin_dir_url($plugin_file);
        return $url;
    }
    
    /** Returns the plugin data. */
    public function get_plugin_data() {
        $plugin_file = $this->get_plugin_file();
        $plugin_data = get_plugin_data($plugin_file);
        C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_plugin_data(): Plugin name: '%s', version: '%s'", $plugin_data['Name'], $plugin_data['Version']));
        return $plugin_data;
    }
    
    /** Registers the custom post type. */
    public function register_cpt() {
        $labels = array(
            'name'               => __('Variables', 'c9-variables'),
            'menu_name'          => __('Variables', 'c9-variables'),
            'singular_name'      => __('Variable', 'c9-variables'),
            'all_items'          => __('All Variables', 'c9-variables'),
            'add_new_item'       => __('Add New Variable', 'c9-variables'),
            'new_item'           => __('New Variable', 'c9-variables'),
            'search_items'       => __('Search Variables', 'c9-variables'),
            'edit_item'          => __('Edit Variable', 'c9-variables'),
            'not_found'          => __('No variables found.', 'c9-variables'),
            'not_found_in_trash' => __('No variables found in Trash.', 'c9-variables')
        );
        $attribs = [
            'labels'              => $labels,
            'description'         => __('A custom post type for variables to support content modularization.', 'c9-variables'),
            'public'              => false,
            'show_ui'             => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'supports'            => ['title', 'editor'],
            'show_in_menu'        => C9_Variables_Constants::$VARS_MENU_SLUG,
            'parent_item'         => null,
            'menu_icon'           => null,
            'rewrite'             => ['slug'  => C9_Variables_Constants::$VARS_SLUG],
            'capability_type'     => 'variable',
            'capabilities'        => [
                'publish_posts'          => 'publish_variables',
                'edit_posts'             => 'edit_variables',
                'edit_others_posts'      => 'edit_others_variables',
                'delete_posts'           => 'delete_variables',
                'delete_others_posts'    => 'delete_others_variables',
                'read_private_posts'     => 'read_private_variables',
                'edit_post'              => 'edit_variable',
                'delete_post'            => 'delete_variable',
                'read_post'              => 'read_variable',
                'edit_published_posts'   => 'edit_published_variables',
                'edit_private_posts'     => 'edit_private_variables',
                'delete_private_posts'   => 'delete_private_variables',
                'delete_published_posts' => 'delete_published_variables'
            ],
            'map_meta_cap'        => false
        ];
        C9_Logger::debug("C9_Variables_Admin_Delegate::register_cpt(): Registering custom post type...");
        register_post_type(C9_Variables_Constants::$VARIABLES_POST_TYPE, $attribs);
        $this->update_cpt_attribs($attribs);
        // Update cpt registration with any updates to the attributes
        register_post_type(C9_Variables_Constants::$VARIABLES_POST_TYPE, $attribs);
        
        // Uncomment the following only to support organizing variables using custom taxonomies (equivalent of tags)
        /*register_taxonomy('c9_vars_variables', array(C9_Variables_Constants::$VARIABLES_POST_TYPE),
             [
                 'labels'            => $labels,
                 'hierarchical'      => false,
                 'show_ui'           => true,
                 'show_admin_column' => true,
                 'rewrite'           => ['slug' => C9_Variables_Constants::$VARS_SLUG]
             ]
         );*/
    }
    
    /** Performs capabilities setup. */
    public function setup_capabilities() {
        C9_Logger::debug("C9_Variables_Admin_Delegate::setup_capabilities(): Setting up capabilities...");
        $admin = get_role('administrator');
        $editor = get_role('editor');
        $author = get_role('author');
        $contributor = get_role('contributor');
        $roles = [$admin, $editor, $author];
        
        // Add common capabilities
        foreach ($roles as $role) {
            $role->add_cap('publish_variables');
            $role->add_cap('edit_variables');
            $role->add_cap('edit_published_variables');
            $role->add_cap('delete_variables');
            $role->add_cap('read_variable');
            $role->add_cap('edit_variable');
            $role->add_cap('delete_variable');
        }
        
        // Additional privileged role specific capabilities
        $privileged_roles = [$admin, $editor];
        foreach ($privileged_roles as $role) {
            $role->add_cap('edit_published_variables');
            $role->add_cap('edit_others_variables');
            $role->add_cap('edit_private_variables');
            $role->add_cap('delete_published_variables');
            $role->add_cap('delete_others_variables');
            $role->add_cap('delete_private_variables');
            $role->add_cap('read_private_variables');
        }
        
        // Give only basic privileges to contributor
        $contributor->add_cap('edit_variables');
        $contributor->add_cap('delete_variables');
        $contributor->add_cap('read_variable');
        $contributor->add_cap('edit_variable');
        $contributor->add_cap('delete_variable');
    }
    
    /** Registers custom filters and actions. */
    public function register_custom_filter_and_actions() {
        add_action('save_post', [$this, 'save_variable_custom_fields'], 10, 2);
        add_action('post_updated_messages', [$this, 'variable_updated_messages'], 10, 1);
    }
    
    /** Provides updated messages for variables. */
    public function variable_updated_messages($messages) {
        $post = get_post();
        $post_type = get_post_type($post);
        $post_type_object = get_post_type_object($post_type);
        
        if ($post_type != C9_Variables_Constants::$VARIABLES_POST_TYPE) {
            return $messages;
        }
        
        $messages[$post_type] = [
            0  => '', // Unused, messages start at index 1
            1  => __('Variable updated.', 'c9-variables'),
            2  => __('Custom field updated.', 'c9-variables'),
            3  => __('Custom field deleted.', 'c9-variables'),
            4  => __('Variable updated.', 'c9-variables'),
            5  => isset($_GET['revision']) ? sprintf(__('Variable restored to revision from %s', 'c9-variables'), wp_post_revision_title( (int) $_GET['revision'], false)) : false,
            6  => __('Variable published.', 'c9-variables'),
            7  => __('Variable saved.', 'c9-variables'),
            8  => __('Variable submitted.', 'c9-variables'),
            9  => sprintf(__('Variable scheduled for: <strong>%1$s</strong>.', 'c9-variables'), date_i18n(__('M j, Y @ G:i', 'c9-variables'), strtotime($post->post_date))),
            10 => __('Variable draft updated.', 'c9-variables')
        ];
        
        return $messages;
    }
    
    /** Retrieves the variables. */
    public function get_variables() {
        // Validate request
        check_ajax_referer($this->get_variables_action(), C9_Variables_Constants::$NONCE_PARAM);
        C9_Logger::debug("C9_Variables_Admin_Delegate::get_variables(): Invoked");
        
        // Search
        $page = 1;
        $total = 0;
        $start = 0;
        $end = 0;
        $is_next_page_available = "false";
        $variables = [];
        $keywords = C9_Text_Utils::html_entity_decode($_POST[C9_Variables_Constants::$KEYWORDS_PARAM], C9_Variables_Constants::$VARIABLE_NAME_SANITIZE_PATTERN);
        $query = $this->prepare_get_variables_query($keywords);
        $results = new WP_Query($query);
        if ($results->have_posts()) {
            $posts = $results->posts;
            foreach($posts as $post) {
                $var_name = $post->post_name;
                $var_display_name = esc_html(C9_Text_Utils::prepare_shortened_text($post->post_title));
                $var = [C9_Variables_Constants::$NAME_ATTRIB => $var_name, C9_Variables_Constants::$DISPLAY_NAME => $var_display_name];
                array_push($variables, $var);
            }
            
            // Prepare pagination related values
            $page = isset($_POST[C9_Variables_Constants::$PAGE_PARAM]) ? $_POST[C9_Variables_Constants::$PAGE_PARAM] : 1;
            $total = $results->found_posts;
            $num_results_per_page = $this->get_number_of_results_per_page();
            $curr_page_num_results = $results->post_count;
            $start = ($page - 1) * $num_results_per_page + 1;
            $end = $start + $curr_page_num_results - 1;
            $is_next_page_available = ($end < $total) ? "true" : "false";
            
            // Clean up
            wp_reset_postdata();
        }
        $result = [
            'total'                  => $total,
            'start'                  => $start,
            'end'                    => $end,
            'paged'                  => $page,
            'is_next_page_available' => $is_next_page_available,
            'data'                   => $variables
        ];
        if (C9_Logger::is_debug_enabled()) {
            C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_variables(): query: %s", json_encode($query)));
            C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_variables(): result: %s", json_encode($result)));
        }
        wp_send_json($result);
        wp_die();
    }
    
    /** Updates the variable last used timestamp. */
    public function update_variable_last_used() {
        // Validate request
        check_ajax_referer($this->get_update_variable_last_used_action(), C9_Variables_Constants::$NONCE_PARAM);
        C9_Logger::debug("C9_Variables_Admin_Delegate::update_variable_last_used(): Invoked");
        
        // Lookup by post title and update last used timestamp
        $name = C9_Text_Utils::html_entity_decode($_POST[C9_Variables_Constants::$NAME_PARAM], C9_Variables_Constants::$VARIABLE_NAME_SANITIZE_PATTERN);
        $results = new WP_Query([C9_Variables_Constants::$POST_TYPE_FIELD => C9_Variables_Constants::$VARIABLES_POST_TYPE, 'name' => $name, 'posts_per_page' => 1]);
        while($results->have_posts()) {
            $results->the_post();
            $id = get_the_ID();
            $this->post_variable_save_handler($id);
        }
        wp_die();
    }
    
    /** Handles post variable save tasks (such as, last used timestamp update). */
    public function post_variable_save_handler($id) {
        $ts = time();
        update_post_meta($id, C9_Variables_Constants::$LAST_USED_FIELD, $ts);
        C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::post_variable_save_handler(): Updated variable id: %s, timestamp: %s", $id, $ts));
    }
    
    /** Returns the product message data, if any, as a map with keys (status, is_dismissible, message). */
    public function get_product_message_data() {
        $path_vars = [];
        $plugin_id = $this->get_plugin_slug();
        $path_vars[C9_Variables_Constants::$ITEM_ID_PARAM] = $plugin_id;
        $response = C9_HTTP_Utils::get_request(C9_Variables_Constants::$REST_API_BASE_URL, C9_Variables_Constants::$ITEM_MESSAGES_URI, $path_vars);
        $data = [];
        if (isset($response['status']) && $response['status'] == 200) {
            $status = $response[C9_Variables_Constants::$STATUS];
            $data = $response[C9_Variables_Constants::$DATA];
            if ($data) {
                $data = json_decode($data, true);
            }
        }
        else {
            C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_product_message_data(): Error ocurred while retrieving product message data, response: %s", json_encode($response)));
        }
        C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_product_message_data(): Product message: %s", json_encode($data)));
        
        return $data;
    }
    
    /** Returns data for other plugins. */
    public function get_other_plugins() {
        $data = [];
        $plugin_id = $this->get_plugin_slug();
        $data[C9_Variables_Constants::$ITEM_TYPE_PARAM] = 'WPPlugin';
        $data[C9_Variables_Constants::$FILTER_ID_PARAM] = $plugin_id;
        $response = C9_HTTP_Utils::get_request(C9_Variables_Constants::$REST_API_BASE_URL, C9_Variables_Constants::$OTHER_PLUGINS_URI, null, $data);
        $data = [];
        if (isset($response['status']) && $response['status'] == 200) {
            $status = $response[C9_Variables_Constants::$STATUS];
            $data = $response[C9_Variables_Constants::$DATA];
        }
        else {
            C9_Logger::error(sprintf("C9_Variables_Admin_Delegate::get_other_plugins(): Error while retrieving other plugins from pluginID: '%s', response: %s", $plugin_id, json_encode($response)));
        }
        C9_Logger::debug(sprintf("C9_Variables_Admin_Delegate::get_other_plugins(): pluginID: '%s', other plugins: %s", $plugin_id, json_encode($data)));

        return $data;
    }
    
    /** Updates the CPT attributes. */
    public function update_cpt_attribs(&$attribs) {
        if (!$this->is_subscription_valid()) {
            $attribs['capabilities'] = ['create_posts' => 'do_not_allow'];
            $attribs['map_meta_cap'] = true;
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // Methods to be overriden to provide more enhanced functionality
    ////////////////////////////////////////////////////////////////////////////////
    
    /** Returns the plugin slug. */
    public function get_plugin_slug() {
        return "c9-variables";
    }
    
    /** Returns the path to the plugin file name. */
    public function get_plugin_file_name() {
        return "c9-variables.php";
    }
    
    /** Returns true if the subscription is valid. */
    public function is_subscription_valid() {
        $count_posts = wp_count_posts(C9_Variables_Constants::$VARIABLES_POST_TYPE);
        if (isset($count_posts->publish)) {
            C9_Logger::debug(sprintf("C9_Variables: Number of variables: %d", $count_posts->publish));
        }
        $count = isset($count_posts->publish) ? $count_posts->publish : 0;
        $count += isset($count_posts->draft) ? $count_posts->draft : 0;
        $count += isset($count_posts->trash) ? $count_posts->trash : 0;
        $is_valid = ($count < C9_Variables_Constants::$NUM_VARS_LIMIT);
        if (!$is_valid) {
            C9_Logger::error("C9_Variables: Variables limit exceeded!");
        }
        return $is_valid;
    }
    
    /** Refreshes and returns the subscription status. */
    public function refresh_subscription_status() {
        $status = $this->is_subscription_valid() ? 'valid' : 'expired';
        return $status;
    }
    
    /** Returns admin message data as a map with keys (status, is_dismissible, message). */
    public function get_admin_message_data() {
        $data = [];
        if (!$this->is_subscription_valid()) {
            $data[C9_Plugin_UI_Helper::$MESSAGE_TYPE] = "error";
            $data[C9_Plugin_UI_Helper::$IS_DISMISSIBLE] = 'Y';
            $data[C9_Plugin_UI_Helper::$MESSAGE] = __("You have reached the maximum number of variables supported by the <strong>C9 Variables</strong> basic plugin. You will be able to use existing variables (including editing these), but not add a new one. Please consider upgrading to the <a href='https://cloudnineapps.com/products/wordpress-plugins/c9-variables-pro' target='_blank'><strong>C9 Variables Pro</strong></a> plugin that supports unlimited variables and many more productivity enhancements.", 'c9-variables');
        }
        else {
            $data = $this->get_product_message_data();
        }
        
        return $data;
    }
    
    /** Registers the settings. */
    public function register_settings() {
        C9_Logger::debug("C9_Variables_Admin_Delegate::register_settings(): Registering settings...");
        register_setting(C9_Variables_Constants::$VARS_SETTINGS, C9_Variables_Constants::$DEBUG_MODE_SETTING);
        register_setting(C9_Variables_Constants::$VARS_SETTINGS, C9_Variables_Constants::$USAGE_TRACKING_SETTING);
    }
    
    /** Saves variable custom fields. */
    public function save_variable_custom_fields($post_id, $post) {
    }
    
    /** Returns the get variables action. */
    protected function get_variables_action() {
        return C9_Variables_Constants::$GET_VARIABLES_ACTION;
    }
    
    /** Returns the update variable last used action. */
    protected function get_update_variable_last_used_action() {
        return C9_Variables_Constants::$UPDATE_VARIABLE_LAST_USED_ACTION;
    }
    
    /** Prepares the get variables query. */
    protected function prepare_get_variables_query($keywords) {
        $page = isset($_POST[C9_Variables_Constants::$PAGE_PARAM]) ? $_POST[C9_Variables_Constants::$PAGE_PARAM] : 1;
        $query = [
            C9_Variables_Constants::$POST_TYPE_FIELD => C9_Variables_Constants::$VARIABLES_POST_TYPE,
            's'                                      => $keywords,
            'orderby'                                => C9_Variables_Constants::$POST_TITLE_FIELD,
            'order'                                  => 'ASC',
            C9_Variables_Constants::$PAGE_PARAM      => $page,
            'posts_per_page'                         => $this->get_number_of_results_per_page()
        ];
        return $query;
    }
    
    /** Returns the number of results per page. */
    protected function get_number_of_results_per_page() {
        return C9_Variables_Constants::$NUM_RESULTS_PER_PAGE_SETTING_DEFAULT;
    }
}
