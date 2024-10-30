<?php

/**
 * Class to provide the helper functions for Item Subscription.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/helper
 * @author     CloudNineApps
 */
if (!class_exists('C9_Item_Subscription_Helper')) {
    class C9_Item_Subscription_Helper {
        
        ////////////////////////////////////////////////////////////////////////////////
        // Constants
        ////////////////////////////////////////////////////////////////////////////////
        
        /** The message type. */
        public static $SUBSCRIPTION_VALIDATION_URI = "wp-json/c9-ec-ext/v1/subscriptions/validate";
        
        /** The subscription status setting suffix. */
        public static $SUBSCRIPTION_STATUS_SUFFIX = "_subscription_status";
        
        /** The subscription check timestamp setting suffix. */
        public static $SUBSCRIPTION_CHECK_TIMESTAMP_SUFFIX = "_subscription_check_ts";
        
        
        ////////////////////////////////////////////////////////////////////////////////
        // Code
        ////////////////////////////////////////////////////////////////////////////////
        
        /**
         * Returns the subscription status for the specified item and license key.
         * 
         * This method uses database-based caching to optimize the number of REST calls to the subscription service. 
         * 
         * @param base_url: The base URL to use for subscription service.
         * @param item_id: The item whose subscription is to be checked.
         * @param license_key: The license key to use for the check.
         * @param refresh: (Optional) When set to true, it forces the refresh of subscriptions status (default: false).
         * @param cache_expiry: (Optional) Use the specified expiry period in seconds for cache (default: 3600).
         * 
         * @return Returns the subscription status from {valid|expiring|expired}. The default is valid.
         *         If the REST call for subscription status fails, it still returns 'valid' to avoid user disruption
         *         and logs the error.
         */
        public static function get_subscription_status($base_url, $item_id, $license_key, $refresh=false, $cache_expiry=3600) {
            if (!$license_key) {
                return 'expired';
            }
            
            $status = 'valid';
            $fetch_subscription_status = true;
            $subscription_status_key = $item_id . C9_Item_Subscription_Helper::$SUBSCRIPTION_STATUS_SUFFIX;
            $subscription_check_ts_key = $item_id . C9_Item_Subscription_Helper::$SUBSCRIPTION_CHECK_TIMESTAMP_SUFFIX;
            
            // If not a forced refresh, prefer using cached status
            if (!$refresh) {
                $subscription_status = get_option($subscription_status_key, true);
                $subscription_check_ts = get_option($subscription_check_ts_key, true);
                if ($subscription_status != null && $subscription_check_ts != null) {
                    $now = time();
                    $delta = $now - $subscription_check_ts;
                    if ($delta < $cache_expiry) {
                        $status = $subscription_status;
                        $fetch_subscription_status = false;
                        //C9_Logger::debug(sprintf("C9: Reusing subscription status: %s (delta: %d secs)", $status, $delta));
                    }
                }
            }
            
            // Fetch subscription status, if neeeded
            if ($fetch_subscription_status) {
                $params = [];
                $params['item_id'] = $item_id;
                $params['key'] = $license_key;
                $response = C9_HTTP_Utils::get_request($base_url, C9_Item_Subscription_Helper::$SUBSCRIPTION_VALIDATION_URI, null, $params);
                if (isset($response['status']) && $response['status'] == 200) {
                    $content = json_decode($response['data'], true);
                    $status = $content['status'];
                    C9_Logger::debug(sprintf("C9: item_id: %s, subscription status: %s", $item_id, $status));
                }
                else if (isset($response['status']) && $response['status'] == 400) {
                    // A bad request is possible, such as, when an invalid key was provided
                    $status = 'invalid';
                    C9_Logger::error("C9: Invalid subscription");
                }
                else {
                    C9_Logger::error(sprintf("C9: Error occurred while retrieving the subscription status, response: %s", json_encode($response)));
                }
                
                // Cache result till next fetch
                update_option($subscription_status_key, $status);
                update_option($subscription_check_ts_key, time());
            }
            
            return $status;
        }
    }
}