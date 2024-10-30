<?php

/**
 * Class to provide log methods.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/utils
 * @author     CloudNineApps
 */
if (!class_exists('C9_Logger')) {
    class C9_Logger {
    
        /** The debug log level. */
        public static $DEBUG = "DEBUG";
        
        /** The info log level. */
        public static $INFO = "INFO";
        
        /** The warn log level. */
        public static $WARN = "WARN";
        
        /** The error log level. */
        public static $ERROR = "ERROR";
        
        /** Flag to turn debug level on/off. */
        private static $debug = false;
        
        
        /**
         * Enables/disables debug level. If the debug mode is already enabled for the current request, it does not disable it.
         */
        public static function enable_debug($flag) {
            // Do not disable debug mode, if already enabled for the current request
            $flag = (C9_Logger::$debug) ? true : $flag;
            C9_Logger::$debug = $flag;
        }
        
        /**
         * Returns true if debug level is enabled. Otherwise, false.
         */
        public static function is_debug_enabled() {
            return C9_Logger::$debug;
        }
        
        /**
         * Logs the specified message as debug.
         * 
         * @param msg: The message to log.
         */
        public static function debug($msg) {
            if (C9_Logger::is_debug_enabled()) {
                C9_Logger::log(C9_Logger::$DEBUG, $msg);
            }
        }
        
        /**
         * Logs the specified message as info.
         * 
         * @param msg: The message to log.
         */
        public static function info($msg) {
            C9_Logger::log(C9_Logger::$INFO, $msg);
        }
        
        /**
         * Logs the specified message as warning.
         * 
         * @param msg: The message to log.
         */
        public static function warn($msg) {
            C9_Logger::log(C9_Logger::$WARN, $msg);
        }
        
        /**
         * Logs the specified message as error.
         * 
         * @param msg: The message to log.
         */
        public static function error($msg) {
            C9_Logger::log(C9_Logger::$ERROR, $msg);
        }
        
        /**
         * Logs the supplied message at the specified log level.
         * 
         * @param level: The log level.
         * @param msg: The message to log.
         */
        public static function log($level, $msg) {
            $txt = "[$level] $msg";
            error_log($txt);
        }
    }
}