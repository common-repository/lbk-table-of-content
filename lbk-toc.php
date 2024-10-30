<?php
/**
 * LBK Table Of Content
 * 
 * @package LBK Table Of Content
 * @author Briki - LBK
 * @copyright 2021 LBK
 * @license GPL-2.0-or-later
 * @category plugin
 * @version 1.0.2
 * 
 * @wordpress-plugin
 * Plugin Name:       LBK Table Of Content
 * Plugin URI:        https://lbk.vn/
 * Description:       LBK Table Of Content
 * Version:           1.0.2
 * Requires at least: 1.0.2
 * Requires PHP:      7.4
 * Author:            Briki - LBK
 * Author             URI: https://facebook.com/vuong.briki
 * Text Domain:       lbk-cv
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain path:       /languages/
 * 
 * LBK Table Of Content is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *  
 * LBK Table Of Content is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *  
 * You should have received a copy of the GNU General Public License
 * along with LBK Table Of Content. If not, see <http://www.gnu.org/licenses/>.
*/

// Die if accessed directly
if ( !defined( 'ABSPATH' ) ) die( 'Hey, what are you doing here? You are silly human!' );

if ( !class_exists('lbkTOC') ) {
    /**
     * Class lbkTOC
     */
    final class lbkTOC {
        /**
         * Current version
         * 
         * @since 1.0
         * @var string
         */
        const VERSION = '1.0.2';

        /**
         * Store the instance of this class
         * 
         * @access private
         * @since 1.0
         * @static
         * 
         * @var lbkTOC
         */
        private static $instance;

        /**
         * A dummny constructor to prevent the class from being loaded more than once
         * 
         * @access public 
         * @since 1.0
         */
        public function __construct() {
            /** Do nothing here */
        }

        /**
         * funtion instance
         * 
         * @access private
         * @since 1.0
         * @static
         * 
         * @return lbkTOC
         */
        public static function instance() {
            if ( !isset( self::$instance ) && !( self::$instance instanceof lbkTOC ) ) {
                self::$instance = new lbkTOC();

                self::defineConstants();
                self::includes();
                self::hooks();
            }

            return self::$instance;
        }

        /**
         * Define the plugin constants.
         * 
         * @access private
         * @since 1.0
         * @static
         */
        private static function defineConstants() {
            define( 'LBK_TOC_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
            define( 'LBK_TOC_BASE_NAME', plugin_basename( __FILE__ ) );
            define( 'LBK_TOC_PATH', plugin_dir_path( __FILE__ ) );
            define( 'LBK_TOC_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Includes the plugin dependency files.
         * 
         * @access private
         * @since 1.0
         * @static
         */
        private static function includes() {
            if ( is_admin() ) {
                require_once LBK_TOC_PATH . 'includes/class.admin.php';
            }
            require_once LBK_TOC_PATH . 'includes/lbk-toc-func.php';
        }

        /**
         * Add hooks
         * 
         * @access private
         * @since 1.0
         * @static
         */
        private static function hooks() {
            add_filter( 'the_content', array( __CLASS__, 'add_toc' ) );
            add_shortcode( 'lbk_toc', 'lbk_toc_shortcode' );
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'custom_enqueue') );
        }

        /**
         * Register the scripts used in the admin
         * 
         * @access private
         * @since 1.0
         * @static
         */
        public static function custom_enqueue() {
            wp_enqueue_script( 'lbk-toc', LBK_TOC_URL.'js/frontend.js', array(), '1.0.0', true );
            wp_enqueue_style( 'lbk-toc', LBK_TOC_URL.'css/frontend.css', array(), '1.0.0', 'all' ); 
        }

        /**
         * Add TOC to content
         * 
         * @access private
         * @since 1.0
         * @static
         */
        public static function add_toc($content) {
            if ( is_single() && ! empty( $content ) ) {
                $dom = new DOMDocument();
                $previous_value = libxml_use_internal_errors(TRUE);
                $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
                libxml_clear_errors();
                libxml_use_internal_errors($previous_value); 
                for($i = 1; $i < 7; $i++) {
                    $headings[$i] = $dom->getElementsByTagName('h'.$i);
                    foreach ($headings[$i] as $heading) {
                        $heading_value = $heading->nodeValue;
                        $id = lbk_slugify( lbk_utf8convert( $heading_value ) );
                        $heading->setAttribute('id', $id);
                    }
                }
                $new_content = $dom->saveHTML();
                
                $lbk_toc = lbk_print_toc( lbk_get_headlines( $new_content, 2 ) );

                return $lbk_toc . $new_content;
            }
            return $content;
        }

        /**
         * Shortcode lbk_toc
         * 
         * @access private
         * @since 1.0
         * @static
         */
        private static function lbk_toc_shortcode() {
            return print_toc( get_headlines( $new_content, 1 ) );
        }
    }

    /**
     * The main function reponsible for returning the LBK Table Of Content instance to function everywhere.
     * 
     * Use this function like you would a global variable, except without needing to declare the global.
     * 
     * Example: <?php $instance = lbkTOC(); ?>
     * 
     * @access public
     * @since 1.0
     * 
     * @return lbkTOC
     */
    function lbkTOC_Site() {
        return lbkTOC::instance();
    }

    // Start LBK Table Of Content
    add_action( 'plugins_loaded', 'lbkTOC_Site' );
}