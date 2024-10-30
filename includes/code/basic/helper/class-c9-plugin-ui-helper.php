<?php

/**
 * Class to provide the helper functions for plugin UI.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/helper
 * @author     CloudNineApps
 */
if (!class_exists('C9_Plugin_UI_Helper')) {
    class C9_Plugin_UI_Helper {
        
        ////////////////////////////////////////////////////////////////////////////////
        // Constants
        ////////////////////////////////////////////////////////////////////////////////
        
        /** The message type. */
        public static $MESSAGE_TYPE = "type";
        
        /** Whether the message is dismissible. */
        public static $IS_DISMISSIBLE = "is_dismissible";
        
        /** The message. */
        public static $MESSAGE = "message";
        
        
        ////////////////////////////////////////////////////////////////////////////////
        // Code
        ////////////////////////////////////////////////////////////////////////////////
        
        /**
         * Shows an admin message using the supplied data, if any. If no data specified, it will not show a message.
         * 
         * @param data: The message data, if any.
         */
        public static function show_admin_message($data) {
            if (array_key_exists(C9_Plugin_UI_Helper::$MESSAGE_TYPE, $data)) {
                $type = $data[C9_Plugin_UI_Helper::$MESSAGE_TYPE];
                $is_dismissible_str = ($data[C9_Plugin_UI_Helper::$IS_DISMISSIBLE] == 'Y') ? "is-dismissible" : "";
                $msg = $data[C9_Plugin_UI_Helper::$MESSAGE];
?>
<div class="notice notice-<?php echo $type; ?> <?php echo $is_dismissible_str; ?>">
  <p><?php echo $msg; ?></p>
</div>
<?php
            }
        }
        
        /**
         * Shows other plugins' based on the supplied data.
         * 
         * @param plugins: The list of other plugins' data, if any. Each element provides following information.
         * <ul>
         *   <li><em>title</em>: The plugin title.</li>
         *   <li><em>url</em>: The plugin URL.</li>
         *   <li><em>logo_url</em>: The plugin logo URL.</li>
         *   <li><em>is_pad</em>: Whether the plugin is a paid plugin.</li>
         *   <li><em>show_ratings</em>: Whether to show the plugin ratings.</li>
         *   <li><em>ratings</em>: The plugin ratings.</li>
         *   <li><em>num_ratings</em>: The number of ratings.</li>
         * </ul>
         */
        public static function show_other_plugins($plugins) {
            global $pagenow;
            if (!function_exists('plugins_api') && 'plugin-install.php' != $pagenow) {
                include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
            }
?>
<form id="plugin-filter" method="post">
  <div class="wp-list-table widefat plugin-install">
<?php
            foreach ($plugins as $plugin) {
                C9_Logger::debug(sprintf("C9_Plugin_UI_Helper::show_other_plugins(): plugin: %s", json_encode($plugin)));
                $plugin_id = $plugin['item_id'];
                $is_paid = ($plugin['is_paid'] == "Y") ? true : false;
                $rating = floatval($plugin['rating']);
                $num_ratings = $plugin['num_ratings'];
                $show_ratings = ($plugin['show_ratings'] == "Y" && $rating > 0 && $num_ratings > 0) ? true : false;
                $paid_str = $is_paid ? " (Paid)" : "";
                $stars_str = '';
                if ($show_ratings) {
                    $stars = [];
                    $star_index = 1;
                    for ($star_index; $star_index <= $rating; $star_index++) {
                        // Star within rating, give it a complete star
                        $stars[$star_index] = sprintf("<div class='star star-full' aria-hidden='true'></div>");
                    }
                    if ($star_index <= 5) {
                        // Non 5-star rating
                        // If rating is > (star_index - 1), it's a half rating
                        $partial_empty_star = ($rating > ($star_index - 1)) ? 'half' : 'empty';
                        $stars[$star_index++] = sprintf("<div class='star star-%s' aria-hidden='true'></div>", $partial_empty_star);
                        // Fill in with empty stars for the remaining ones
                        for ($star_index; $star_index <= 5; $star_index++) {
                            $stars[$star_index] = sprintf("<div class='star star-empty' aria-hidden='true'></div>");
                        }
                    }
                    $stars_str = implode("\n", $stars);
                }
                
                $plugin_value = $plugin_id;
                $action_file = '';
                $action = '';
                $action_link = '';
                $action_css_class = '';
                $action_txt = '';
                $nonce_input = '';
                $plugin['slug'] = $plugin_id;
                $install_status = install_plugin_install_status($plugin);
                switch($install_status['status']) {
                    case 'install':
                        $action_file = 'update.php';
                        $action = 'install-plugin';
                        $action_css_class = 'install';
                        $action_txt = 'Install Now';
                        $nonce_input = sprintf('%s_%s', $action, $plugin_value);
                        break;
                    case 'update_available':
                        $action_file = 'update.php';
                        $action = 'update-plugin';
                        $action_css_class = 'update';
                        $action_txt = 'Update Now';
                        $nonce_input = sprintf('%s_%s', $action, $plugin_value);
                        break;
                    case 'latest_installed':
                    case 'newer_installed':
                        $plugin_value = sprintf('%s/%s.php', $plugin_id, $plugin_id);
                        $action_file = 'plugins.php';
                        $action = 'activate';
                        $action_css_class = 'active';
                        $action_txt = 'Activate Now';
                        $nonce_input = sprintf('activate-plugin_%s', $plugin_value);
                        break;
                }
                $action_link = sprintf('%s%s?action=%s&plugin=%s&_wpnonce=%s', get_admin_url(), $action_file, $action, $plugin_value, wp_create_nonce($nonce_input));
?>
<?php 
    $plugin_details_url_txt = sprintf('class="thickbox open-plugin-details-modal" href="%s?TB_iframe=true&width=772&height=900"', $plugin['url']);
?>
<?php add_thickbox(); ?>
    <div class="plugin-card plugin-card-<?php echo $plugin_id; ?>">
      <div class="plugin-card-top">
        <div class="name column-name">
          <h3>
            <a <?php echo $plugin_details_url_txt; ?>><?php echo $plugin['title']; ?><?php echo $paid_str; ?>
              <img src="<?php echo $plugin['logo_url']; ?>" class="plugin-icon"/>
            </a>
          </h3>
        </div>
<?php
                if ($is_paid) {
                    // Paid plugin
?>
        <div class="action-links">
          <ul>
            <li><a class="button" href="<?php echo $plugin['url']; ?>" target="_blank">Buy Now</a></li>
            <li><a <?php echo $plugin_details_url_txt; ?>>More Details</a></li>
          </ul>
        </div>
<?php
                } // if (paid plugin)
                else {
                    // Free plugin
?>
        <div class="action-links">
          <ul class="plugin-action-buttons">
            <li><a class="<?php echo $action_css_class; ?> button" data-slug="<?php echo $plugin_id; ?>" href="<?php echo $action_link; ?>" aria-label="Install <?php echo $plugin['title']; ?>" data-name="<?php echo $plugin['title']; ?>"><?php echo $action_txt; ?></a></li>
            <li><a <?php echo $plugin_details_url_txt; ?>>More Details</a></li>
          </ul>
        </div>
<?php
                } // if (free plugin)
?>
        <div class="desc column-description">
          <p><?php echo $plugin['description']; ?></p>
<?php
                if ($show_ratings) {
?>
          <div class="star-rating">
            <?php echo $stars_str; ?>
            <span class="num-ratings" aria-hidden="true">(<?php echo $num_ratings; ?>)</span>
          </div>
<?php
                } // if (show ratings)
?>
        </div>
      </div>
    </div>
<?php
            } // For each plugin
?>
  </div>
</form>
<?php
        } // show_other_plugins() ENDS

        /**
         * Shows the plugin's about content using the supplied data.
         * 
         * @param plugin_data: The plugin data.
         * @param slug: The plugin slug.
         * @param logo_url: The logo URL.
         */
        public static function show_plugin_about_content($plugin_data, $slug, $logo_url) {
            $plugin_details_url_txt = sprintf('class="thickbox open-plugin-details-modal" href="%s?TB_iframe=true&width=772&height=900"', $plugin_data['PluginURI']);
?>            
<?php add_thickbox(); ?>
<div class="wrap">
<h1>About <?php echo $plugin_data['Name']; ?></h1>
  <p/>
  <div class="plugin-card plugin-card-<?php echo $slug; ?>">
    <div class="plugin-card-top">
      <div class="name column-name">
        <h3>
          <a <?php echo $plugin_details_url_txt; ?>><?php echo $plugin_data['Name']; ?>
            <img src="<?php echo $logo_url; ?>" class="plugin-icon"/>
          </a>
        </h3>
        <table>
          <tr>
            <th align="right">Version</th>
            <td><?php echo $plugin_data['Version']; ?></td>
          </tr>
        </table>
        <p/>
        By <a href="https://cloudnineapps.com" target="_blank">Cloud Nine Apps</a>
      </div>
    </div>
  </div>
</div>
<?php            
        }
    }
}