<?php

/**
 * The delegate to provide core business logic for public.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/public/core
 * @author     CloudNineApps
 */
class C9_Variables_Public_Delegate {
    
    /** Registers the shortcodes. */
    public function register_shortcodes() {
        C9_Logger::debug('C9_Variables_Public_Delegate::register_shortcodes(): Invoked');
        
        // Shortcode handlers
        add_shortcode(C9_Variables_Constants::$INSERT_VARIABLE_SHORTCODE, [$this, 'get_variable']);
    }
    
    /** Gets the value of the specified variable. */
    public function get_variable($attribs=[], $content=null) {
        C9_Logger::debug("C9_Variables_Public_Delegate::get_variable(): Invoked");
        $name = "";
        $var = "";
        if ($attribs && $attribs[C9_Variables_Constants::$NAME_ATTRIB]) {
            $name = C9_Text_Utils::html_entity_decode($attribs[C9_Variables_Constants::$NAME_ATTRIB], C9_Variables_Constants::$VARIABLE_NAME_SANITIZE_PATTERN);
            $results = new WP_Query([C9_Variables_Constants::$POST_TYPE_FIELD => C9_Variables_Constants::$VARIABLES_POST_TYPE, 'name' => $name, 'posts_per_page' => 1]);
            if($results->have_posts()) {
                $results->the_post();
                $var = get_the_content();
                $var = do_shortcode($var);
            }
            
            // Clean up
            wp_reset_postdata();
        }
        if (C9_Logger::is_debug_enabled()) {
            C9_Logger::debug(sprintf("C9_Variables_Public_Delegate::get_variable(): name: '%s', content: %s", $name, $var));
        }
        
        return $var;
    }
}
