<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://paystack.com
 * @since 1.0.0
 *
 * @package    Paystack_Give
 * @subpackage Paystack_Give/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Paystack_Give
 * @subpackage Paystack_Give/public
 * @author     Paystack <support@paystack.com>
 */
class Paystack_Give_Public
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
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) 
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/paystack-give-public.css', array(), $this->version, 'all');

    }

    public static function fetchPublicKey(){
        if (give_is_test_mode()) {
            $public_key = give_get_option('paystack_test_public_key');
        } else {
            $public_key = give_get_option('paystack_live_public_key');
        }
        return $public_key
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/paystack-give-public.js', array( 'jquery' ), $this->version, false);
        wp_register_script('Paystack', 'https://js.paystack.co/v1/inline.js', false, '1');
        wp_enqueue_script('Paystack');
        wp_enqueue_script('paystack_frontend', plugin_dir_url(__FILE__) . 'js/paystack-forms-public.js', false, $this->version);
        wp_localize_script('paystack_frontend', 'give_paystack_settings', array('key'=> Paystack_Give_Public::fetchPublicKey()), $this->version, true, true);

    }

}
