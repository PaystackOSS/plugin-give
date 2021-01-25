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

        add_filter( 'give_get_sections_gateways', [ $this, 'register_sections' ] );
        add_filter( 'give_get_settings_gateways', [ $this, 'register_settings' ] );
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
        '<a href="' . admin_url('edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=paystack') . '">' . __('Settings', $this->plugin_name) . '</a>',
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

    /**
     * Register Admin Section.
     *
     * @param array $sections List of sections.
     *
     * @since  1.2.1
     * @access public
     *
     * @return array
     */
    public function register_sections( $sections ) {
        $sections['paystack'] = esc_html__( 'Paystack', 'paystack-give' );

        return $sections;
    }

    /**
     * Register Admin Settings.
     *
     * @param array $settings List of settings.
     *
     * @since  1.0.0
     * @access public
     *
     * @return array
     */
    public function register_settings( $settings ) {
        $current_section = give_get_current_setting_section();

        switch ( $current_section ) {
            case 'paystack':
                $settings = [
                    [
                        'type' => 'title',
                        'id'   => 'give_title_gateway_settings_paystack',
                    ],
                    [
                        'name' => esc_html__( 'Paystack', 'paystack-give' ),
                        'desc' => '',
                        'type' => 'give_title',
                        'id'   => 'give_title_paystack',
                    ],
                    [
                        'name'        => esc_html__( 'Test Secret Key', 'paystack-give' ),
                        'desc'        => esc_html__( 'Enter your Paystack Test Secret Key', 'paystack-give' ),
                        'id'          => 'paystack_test_secret_key',
                        'type'        => 'text',
                        'row_classes' => 'give-paystack-test-secret-key',
                    ],
                    [
                        'name'        => esc_html__( 'Test Public Key', 'paystack-give' ),
                        'desc'        => esc_html__( 'Enter your Paystack Test Public Key', 'paystack-give' ),
                        'id'          => 'paystack_test_public_key',
                        'type'        => 'text',
                        'row_classes' => 'give-paystack-test-public-key',
                    ],
                    [
                        'name'        => esc_html__( 'Live Secret Key', 'paystack-give' ),
                        'desc'        => esc_html__( 'Enter your Paystack Live Secret Key', 'paystack-give' ),
                        'id'          => 'paystack_live_secret_key',
                        'type'        => 'text',
                        'row_classes' => 'give-paystack-live-secret-key',
                    ],
                    [
                        'name'        => esc_html__( 'Live Public Key', 'paystack-give' ),
                        'desc'        => esc_html__( 'Enter your Paystack Live Public Key', 'paystack-give' ),
                        'id'          => 'paystack_live_public_key',
                        'type'        => 'text',
                        'row_classes' => 'give-paystack-live-public-key',
                    ],
                    [
                        'name'    => esc_html__( 'Billing Details', 'paystack-give' ),
                        'desc'    => esc_html__( 'This will enable you to collect donor details. This is not required by Paystack (except email) but you might need to collect all information for record purposes', 'paystack-give' ),
                        'id'      => 'paystack_billing_details',
                        'type'    => 'radio_inline',
                        'default' => 'disabled',
                        'options' => [
                            'enabled'  => esc_html__( 'Enabled', 'paystack-give' ),
                            'disabled' => esc_html__( 'Disabled', 'paystack-give' ),
                        ],
                    ],
                    [
                        'type' => 'sectionend',
                        'id'   => 'give_title_gateway_settings_paystack',
                    ]
                ];
                break;
        }
        return $settings;
    }
}
