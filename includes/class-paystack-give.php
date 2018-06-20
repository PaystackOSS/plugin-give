<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Give
 * @subpackage Gateways
 * @author     Stephen Amaza <steve@paystack.com>
 * @license    https://opensource.org/licenses/gpl-license GNU Public License
 * @link       https://paystack.com
 * @since      1.0.0
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Paystack_Give
 * @subpackage Paystack_Give/includes
 * @author     Paystack <support@paystack.com>
 */
class Paystack_Give
{
    
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    Paystack_Give_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since  1.0.0
     * @access protected
     * @var    string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function __construct() 
    {
        if (defined('PLUGIN_NAME_VERSION') ) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'paystack-give';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Paystack_Give_Loader. Orchestrates the hooks of the plugin.
     * - Paystack_Give_i18n. Defines internationalization functionality.
     * - Paystack_Give_Admin. Defines all hooks for the admin area.
     * - Paystack_Give_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     */
    private function load_dependencies() 
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paystack-give-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paystack-give-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-paystack-give-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        include_once plugin_dir_path(dirname(__FILE__)) . 'public/class-paystack-give-public.php';

        $this->loader = new Paystack_Give_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Paystack_Give_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     */
    private function set_locale() 
    {

        $plugin_i18n = new Paystack_Give_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since  1.0.0
     * @access private
     */
    private function define_admin_hooks() 
    {

        $plugin_admin = new Paystack_Give_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Add menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

        /**
         * Register gateway so it shows up as an option in the Give gateway settings
         * 
         * @param array $gateways
         * 
         * @return array
         */
        function give_paystack_register_gateway($gateways)
        {
            $gateways['paystack'] = array(
                'admin_label'    => esc_attr__('Paystack', 'paystack-give'),
                'checkout_label' => esc_attr__('Pay via Paystack', 'paystack-give')
            );
            return $gateways;
        }

        add_filter('give_payment_gateways', 'give_paystack_register_gateway', 1);

        function give_paystack_settings($settings)
        {

            $check_settings = array(
                array(
                    'name' => __('Paystack', 'paystack-give'),
                    'desc' => '',
                    'type' => 'give_title',
                    'id'   => 'give_title_paystack',
                ),
                array(
                    'name' => __('Test Secret Key', 'paystack-give'),
                    'desc' => __('Enter your Paystack Test Secret Key', 'paystack-give'),
                    'id' => 'paystack_test_secret_key',
                    'type' => 'text',
                    'row_classes' => 'give-paystack-test-secret-key',
                ),
                array(
                    'name' => __('Test Public Key', 'paystack-give'),
                    'desc' => __('Enter your Paystack Test Public Key', 'paystack-give'),
                    'id' => 'paystack_test_public_key',
                    'type' => 'text',
                    'row_classes' => 'give-paystack-test-public-key',
                ),
                array(
                    'name' => __('Live Secret Key', 'paystack-give'),
                    'desc' => __('Enter your Paystack Live Secret Key', 'paystack-give'),
                    'id' => 'paystack_live_secret_key',
                    'type' => 'text',
                    'row_classes' => 'give-paystack-live-secret-key',
                ),
                array(
                    'name' => __('Live Public Key', 'paystack-give'),
                    'desc' => __('Enter your Paystack Live Public Key', 'paystack-give'),
                    'id' => 'paystack_live_public_key',
                    'type' => 'text',
                    'row_classes' => 'give-paystack-live-public-key',
                ),
                array(
                    'name'    => __('Billing Details', 'paystack-give'),
                    'desc'    => __('This will enable you to collect donor details. This is not required by Paystack (except email) but you might need to collect all information for record purposes', 'paystack-give'),
                    'id'      => 'paystack_billing_details',
                    'type'    => 'radio_inline',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled'  => __('Enabled', 'paystack-give'),
                        'disabled' => __('Disabled', 'paystack-give'),
                    )
                )
            );

            return array_merge($settings, $check_settings);
        }

        add_filter('give_settings_gateways', 'give_paystack_settings');
        
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since  1.0.0
     * @access private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Paystack_Give_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');


        function give_paystack_credit_card_form($form_id, $echo = true)
        {
            $billing_fields_enabled = give_get_option('paystack_billing_details');

            if ($billing_fields_enabled == 'enabled') {
                do_action('give_after_cc_fields');
            } else {
                //Remove Address Fields if user has option enabled
                remove_action('give_after_cc_fields', 'give_default_cc_address_fields');
            }
            return $form_id;
        }
        add_action('give_paystack_cc_form', 'give_paystack_credit_card_form');

        /**
         * This action will run the function attached to it when it's time to process the donation 
         * submission.
         **/
        function give_process_paystack_purchase($purchase_data)
        {
            $payment_data = array(
                'price'           => $purchase_data['price'],
                'give_form_title' => $purchase_data['post_data']['give-form-title'],
                'give_form_id'    => intval($purchase_data['post_data']['give-form-id']),
                'give_price_id'   => isset($purchase_data['post_data']['give-price-id']) ? $purchase_data['post_data']['give-price-id'] : '',
                'date'            => $purchase_data['date'],
                'email'           => $purchase_data['user_email'],
                'purchase_key'    => $purchase_data['purchase_key'],
                'currency'        => give_get_currency(),
                'user_info'       => $purchase_data['user_info'],
                'status'          => 'pending', 
                'gateway'         => 'paystack'
            );

            if (give_is_test_mode()) {
                $secret_key = give_get_option('paystack_test_secret_key');
                $public_key = give_get_option('paystack_test_public_key');
            } else {
                $secret_key = give_get_option('paystack_live_secret_key');
                $public_key = give_get_option('paystack_live_public_key');
            }

            echo "
                <script src='https://js.paystack.co/v1/inline.js'></script>
                <script>
                function payWithPaystack(){
                    var handler = PaystackPop.setup({
                    key: '$public_key',
                    email: '${payment_data['email']}',
                    amount: ${payment_data['price']} * 100,
                    ref: ''+Math.floor((Math.random() * 1000000000) + 1),
                    firstname: '${payment_data['email']}',
                    lastname: '${payment_data['email']}',
                    currency: '${payment_data['currency']}',
                    metadata: {
                        custom_fields: [
                            {
                                display_name: 'Mobile Number',
                                variable_name: 'mobile_number',
                                value: '+2348012345678'
                            }
                        ]
                    },
                    callback: function(response){
                        
                    },
                    onClose: function(){
                        window.history.back();
                    }
                    });
                    handler.openIframe();
                }

                window.onload = function() {
                    payWithPaystack();
                }
                </script>
            ";

        }

        add_action('give_gateway_paystack', 'give_process_paystack_purchase');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 1.0.0
     */
    public function run() 
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since  1.0.0
     * @return string    The name of the plugin.
     */
    public function get_plugin_name() 
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since  1.0.0
     * @return Paystack_Give_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() 
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since  1.0.0
     * @return string    The version number of the plugin.
     */
    public function get_version() 
    {
        return $this->version;
    }

}
