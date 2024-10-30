<?php

// Die if accessed directly
if ( !defined('ABSPATH') ) die( 'What are you doing here? You silly human!' );

if ( ! class_exists( 'lbkTOC_Admin' ) ) {
    /**
     * class lbkTOC_Admin
     */
    final class lbkTOC_Admin {
        /**
         * Setup plugin for admin use
         * 
         * @access public
         * @since 1.0
         */
        public function __construct() {
            $this->hooks();
        }

        /**
         * Add the core admin hooks
         * 
         * @access private
         * @since 1.0
         */
        private function hooks() {
            // add_action( 'admin_menu', array( $this, 'menu' ) );
            // add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );
        }

        /**
         * Callback to add plugin as a submenu page of the Options page.
         * 
         * This also adds the action to enqueue the scripts to be loaded on plugin's admin page only.
         * 
         * @access private
         * @since 1.0
         * @static
         */
        public function menu() {
            $page = add_submenu_page( 
                'options-general.php',
                esc_html__( 'LBK Table Of Content', 'lbk-toc' ),
                esc_html__( 'LBK Table Of Content', 'lbk-toc' ),
                'manage_options',
                'lbk-toc',
                array( $this, 'page' )
            );
        }

        /**
         * Callback used to render the admin options page.
         * 
         * @access private
         * @since 1.0
         * @static
         */
        public function page() {
            include_once LBK_TOC_PATH . 'includes/admin-options-page.php';
        }

        /**
         * Add a link to the settings page
         * 
         * @access public
         * @since 1.0
         * @static
         */
        public function add_settings_link( $links, $file ) {
            if (
                strrpos( $file, '/lbk-toc.php' ) === ( strlen( $file ) - 12 ) &&
                current_user_can( 'manage_options' )
            ) {
                $settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=lbk-toc' ), __( 'Settings', 'lbk-toc' ) );
                $links = (array) $links;
                $links['lbksvc_settings_link'] = $settings_link;
            }

            return $links;
        }
    }
    new lbkTOC_Admin();
}