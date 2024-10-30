<?php

/**
 * Class to provide constants.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/common
 * @author     CloudNineApps
 */
class C9_Variables_Constants {
    
    /** Prefix for plugin shortcodes. */
    //public static $SHORTCUTS_PREFIX = "c9-vars-";

    /** The variable name pattern. */
    public static $VARIABLE_NAME_PATTERN = "/[A-Za-z0-9_- ]/";

    /** The variable name sanitizing pattern (used to identify invalid characters). */
    public static $VARIABLE_NAME_SANITIZE_PATTERN = "/[^A-Za-z0-9-_ ]/";
    
    /** The variables slug. */
    public static $VARS_SLUG = "c9-variables";
    
    /** The insert variable shortcode. */
    public static $INSERT_VARIABLE_SHORTCODE = "c9-vars-insert";

    /** The variable post type. */
    public static $VARIABLES_POST_TYPE = "c9_vars_variable";
    
    /** The name attribute. */
    public static $NAME_ATTRIB = "name";
    
    /** The variable display name. */
    public static $DISPLAY_NAME = "display_name";
    
    /** The number of variables limit. */
    public static $NUM_VARS_LIMIT = 10;

    
    ////////////////////////////////////////////////////////////////////////////////
    // Request related
    ////////////////////////////////////////////////////////////////////////////////
    
    /** The get variables action. */
    public static $GET_VARIABLES_ACTION = "c9_vars_get_variables";
    
    /** The update variable action. */
    public static $UPDATE_VARIABLE_ACTION = "c9_vars_update_variable";
    
    /** The update variable last used action. */
    public static $UPDATE_VARIABLE_LAST_USED_ACTION = "c9_vars_update_variable_last_used";
    
    /** The nonce param. */
    public static $NONCE_PARAM = "c9_vars_security";
    
    /** The name param. */
    public static $NAME_PARAM = "name";
    
    /** The keywords param. */
    public static $KEYWORDS_PARAM = "keywords";
    
    /** The page param. */
    public static $PAGE_PARAM = "paged";
    
    /** The REST API base URL. */
    public static $REST_API_BASE_URL = "https://cloudnineapps.com";
    
    /** The item messages URI (do not begin with '/'). */
    public static $ITEM_MESSAGES_URI = "wp-json/c9-ec-ext/v1/items/{item_id}/message";
    
    /** The item ID param. */
    public static $ITEM_ID_PARAM = "item_id";
    
    /** The other plugins URI (do not begin with '/'). */
    public static $OTHER_PLUGINS_URI = "wp-json/c9-ec-ext/v1/items";
    
    /** The item type param. */
    public static $ITEM_TYPE_PARAM = "type";
    
    /** The filter id param. */
    public static $FILTER_ID_PARAM = "filter_id";
    
    /** The status field in response. */
    public static $STATUS = "status";
    
    /** The data field in response. */
    public static $DATA = "data";
    
    
    ////////////////////////////////////////////////////////////////////////////////
    // Fields
    ////////////////////////////////////////////////////////////////////////////////
    
    /** The ID field. */
    public static $ID_FIELD = "ID";
    
    /** The post type field. */
    public static $POST_TYPE_FIELD = "post_type";
    
    /** The post title field. */
    public static $POST_TITLE_FIELD = "post_title";
    
    /** The post content field. */
    public static $POST_CONTENT_FIELD = "post_content";
    
    /** The last used timestamp metadata field. */
    public static $LAST_USED_FIELD = "c9_vars_last_used";
    
    
    ////////////////////////////////////////////////////////////////////////////////
    // Settings
    ////////////////////////////////////////////////////////////////////////////////
    
    /** The variables settings group. */
    public static $VARS_SETTINGS = "c9-vars-settings";
    
    /** The variables settings page. */
    public static $VARS_SETTINGS_PAGE = "c9_vars_settings";
    
    /** The other plugins page. */
    public static $VARS_OTHER_PLUGINS_PAGE = "c9_vars_other_plugins";
    
    /** The about page. */
    public static $VARS_ABOUT_PAGE = "c9_vars_about";
    
    /** The debug mode setting. */
    public static $DEBUG_MODE_SETTING = "c9_vars_debug_mode";
    
    /** The debug mode setting default. */
    public static $DEBUG_MODE_SETTING_DEFAULT = "false";
    
    /** The usage tracking setting. */
    public static $USAGE_TRACKING_SETTING = "c9_vars_usage_tracking";
    
    /** The usage tracking setting default. */
    public static $USAGE_TRACKING_SETTING_DEFAULT = "false";
    
    /** The number of results per page setting default. */
    public static $NUM_RESULTS_PER_PAGE_SETTING_DEFAULT = "10";
    
    
    ////////////////////////////////////////////////////////////////////////////////
    // UI Constants
    ////////////////////////////////////////////////////////////////////////////////
    
    /** The variables menu slug. */
    public static $VARS_MENU_SLUG = "c9_vars_menu";
}
