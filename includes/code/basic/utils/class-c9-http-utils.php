<?php

/**
 * Class to provide the HTTP utilities.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/utils
 * @author     CloudNineApps
 */
if (!class_exists('C9_HTTP_Utils')) {
    class C9_HTTP_Utils {
    
        /**
         * Prepares and returns the URL based on base URL dervied from the current HTTP request data and the supplied relative URI.
         * 
         * @param relativeURI: (Optional) The URI path relative to the base URL. If not specified, it returns the base URL. 
         */
        public static function prepare_current_url($relative_uri="") {
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
            $url = sprintf("%s://%s:%s/%s", $protocol, $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $relative_uri);
            return $url;
        }
        
        /**
         * Fires an HTTP GET request and returns the response as a map with keys (status, data).
         * 
         * @param base_url: The base URL to use.
         * @param uri: The URI under the base URL (without query parameters). Do NOT begin with a '/'. E.g., "wp-json/c9-ec-ext/v1/products/{product_id}/search".
         * @param path_vars: (Optional) The map of path variables, if any. Otherwise, specify null.
         * @param params: (Optional) The map of query paramters, if any. Otherwise, specify null.
         * @param timeout: (Optional) The request timeout.
         */
        public static function get_request($base_url, $uri, $path_vars=null, $params=null, $timeout=60) {
            $response = [];
            $url = C9_HTTP_Utils::prepare_url($base_url, $uri, $path_vars, $params);
            $resp = wp_remote_get($url, array('timeout' => $timeout));
            $response['status'] = wp_remote_retrieve_response_code($resp);
            $response['data'] = wp_remote_retrieve_body($resp);
            
            return $response;
        }
        
        /**
         * Prepares the URL based on the supplied data.
         * 
         * @param base_url: The base URL to use.
         * @param uri: The URI under the base URL. Do NOT begin with a '/'.
         * @param path_vars: (Optional) The path variables, if any.
         * @param params: (Optional) The query params, if any.
         */
        public static function prepare_url($base_url, $uri, $path_vars=null, $params=null) {
            $url = sprintf("%s/%s", $base_url, $uri);
            if ($path_vars) {
                foreach (array_keys($path_vars) as $var) {
                    $url = preg_replace('/{' . $var . '}/', $path_vars[$var], $url);
                }
            }
            if ($params != null && count($params) > 0) {
                $url = sprintf("%s?%s", $url, http_build_query($params));
            }
            
            return $url;
        }
    }
}