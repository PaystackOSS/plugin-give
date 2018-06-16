<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Give
 * @subpackage Gateways
 * @author     Stephen Amaza <steve@paystack.com>
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 * @link       https://paystack.com
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Paystack_Give
 * @subpackage Gateways
 * @author     Stephen <steve@paystack.com>
 */
class Paystack_Give_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) 
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Paystack_Give_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Paystack_Give_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/paystack-give-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() 
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Paystack_Give_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Paystack_Give_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/paystack-give-admin.js', array( 'jquery' ), $this->version, false);

    }

    public function add_plugin_admin_menu() 
    {

        /*
        * Add a settings page for this plugin to the Settings menu.
        *
        * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
        *
        *        Administration Menus: http://codex.wordpress.org/Administration_Menus
        *
        */
        add_submenu_page(
            'edit.php?post_type=give_forms', 
            __('Paystack Settings', 'textdomain'),
            __('Paystack', 'textdomain'), 
            'manage_options', $this->plugin_name, 
            array($this, 'display_plugin_setup_page')
        );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since 1.0.0
     */

    public function add_action_links( $links ) 
    {
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
        '<a href="' . admin_url('edit.php?post_type=give_forms&page=give-settings&tab=gateways') . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since 1.0.0
     */

    public function display_plugin_setup_page() 
    {
        include_once 'partials/paystack-give-admin-display.php';
    }


}
