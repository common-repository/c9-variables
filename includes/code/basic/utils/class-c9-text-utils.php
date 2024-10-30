<?php

/**
 * Class to provide the text utilities.
 * 
 * @since      1.0.0
 * @package    C9_Variables
 * @subpackage C9_Variables/includes/code/basic/utils
 * @author     CloudNineApps
 */
if (!class_exists('C9_Text_Utils')) {
    class C9_Text_Utils {
        
        /**
         * Decodes the specified text for HTML entities and any additional pattern supplied.
         * 
         * @param txt: The text to decode.
         * @param pattern: (Optional) Additional pattern to apply to keep only expected characters.
         */
        public static function html_entity_decode($txt, $pattern=null) {
            $txt = html_entity_decode($txt);
            if ($pattern) {
                $txt = preg_replace($pattern, '', $txt);
            }
            return $txt;
        }
        
        /**
         * Prepares shortened text based on the supplied text and length.
         * - If the text length <= specified length, returns the original text.
         * - Else it shortens the text by and appends elipsis to match the max length.
         * 
         * @param txt: The text to shorten.
         * @param len: The length after which to shorten the text. The shortened text cannot exceed this length. 
         */
        public static function prepare_shortened_text($txt, $len=80) {
            if ($txt && strlen($txt) > $len) {
                $txt = substr($txt, 0, $len - 3) . '...';
            }
            return $txt;
        }
        
        /**
         * Checks if the supplied text starts with the specified string.
         * 
         * @param txt: The input text.
         * @param str: The string whose existence needs to be checked.
         * @return Returns true if the text begins with the specified string. Otherwise, false.
         */
        public static function starts_with($txt, $str) {
            $len = strlen($str);
            return (substr($txt, 0, $len) === $str);
        }
        
        /**
         * Checks if the supplied text ends with the specified string.
         * 
         * @param txt: The input text.
         * @param str: The string whose existence needs to be checked.
         * @return Returns true if the text ends with the specified string. Otherwise, false.
         */
        public static function ends_with($txt, $str) {
            $len = strlen($str);
            return (substr($txt, -$len) === $str);
        }
    }
}