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

class give_paystack_plugin_tracker
{
    var $public_key;
    var $plugin_name;
    function __construct($plugin, $pk)
    {
        //configure plugin name
        //configure public key
        $this->plugin_name = $plugin;
        $this->public_key = $pk;
    }



    function log_transaction_success($trx_ref)
    {
        //send reference to logger along with plugin name and public key
        $url = "https://plugin-tracker.paystackintegrations.com/log/charge_success";

        $fields = [
            'plugin_name'  => $this->plugin_name,
            'transaction_reference' => $trx_ref,
            'public_key' => $this->public_key
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        //  echo $result;
    }
}
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

    const API_QUERY_VAR = 'paystack-give-api';

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
        if (defined('PLUGIN_NAME_VERSION')) {
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
         * The class responsible for defining all actions that occur in the admin
         * area.
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
                'admin_label' => esc_attr__('Paystack', 'paystack-give'),
                'checkout_label' => esc_attr__('Paystack', 'paystack-give'),
            );
            return $gateways;
        }

        add_filter('give_payment_gateways', 'give_paystack_register_gateway', 1);

        /**
         * Filter the currencies
         * Note: you can register new currency by using this filter
         *
         * @since 1.8.15
         *
         * @param array $currencies
         */
        function give_paystack_add_currencies($currencies)
        {
            $add_currencies = array(
                'NGN' => array(
                    'admin_label' => sprintf(__('Nigerian Naira (%1$s)', 'give'), '&#8358;'),
                    'symbol' => '&#8358;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => ',',
                        'decimal_separator' => '.',
                        'number_decimals' => 2,
                    ),
                ),
                'GHS' => array(
                    'admin_label' => sprintf(__('Ghana Cedis (%1$s)', 'give'), 'GHS'),
                    'symbol' => 'GHS;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                ),
                'ZAR' => array(
                    'admin_label' => sprintf(__('South African Rands (%1$s)', 'give'), 'ZAR'),
                    'symbol' => 'ZAR;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                ),
                'KES' => array(
                    'admin_label' => sprintf(__('Kenyan Shillings (%1$s)', 'give'), 'KES'),
                    'symbol' => 'KES;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                ),
                'XOF' => array(
                    'admin_label' => sprintf(__('West African CFA franc (%1$s)', 'give'), 'XOF'),
                    'symbol' => 'XOF;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                ),
                'EGP' => array(
                    'admin_label' => sprintf(__('Egyptian Pound (%1$s)', 'give'), 'EGP'),
                    'symbol' => '£;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                ),
                'USD' => array(
                    'admin_label' => sprintf(__('US Dollars (%1$s)', 'give'), 'USD'),
                    'symbol' => 'USD;',
                    'setting' => array(
                        'currency_position' => 'before',
                        'thousands_separator' => '.',
                        'decimal_separator' => ',',
                        'number_decimals' => 2,
                    ),
                )
            );
            return array_merge($add_currencies, $currencies);
        }

        add_filter('give_currencies', 'give_paystack_add_currencies');

        add_action('parse_request', array($this, 'handle_api_requests'), 0);
    }

    public function handle_api_requests()
    {

        global $wp;
        if (!empty($_GET[Paystack_Give::API_QUERY_VAR])) { // WPCS: input var okay, CSRF ok.
            $wp->query_vars[Paystack_Give::API_QUERY_VAR] = sanitize_key(wp_unslash($_GET[Paystack_Give::API_QUERY_VAR])); // WPCS: input var okay, CSRF ok.

            $key = $wp->query_vars[Paystack_Give::API_QUERY_VAR];
            if ($key && ($key === 'verify') && isset($_GET['reference'])) {
                // handle verification here
                $this->verify_transaction();
                die();
            }
        }
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



            // Make sure we don't have any left over errors present.
            give_clear_errors();

            // Any errors?
            $errors = give_get_errors();
            if (!$errors) {

                $form_id         = intval($purchase_data['post_data']['give-form-id']);
                $price_id        = !empty($purchase_data['post_data']['give-price-id']) ? $purchase_data['post_data']['give-price-id'] : 0;
                $donation_amount = !empty($purchase_data['price']) ? $purchase_data['price'] : 0;

                $payment_data = array(
                    'price' => $donation_amount,
                    'give_form_title' => $purchase_data['post_data']['give-form-title'],
                    'give_form_id' => $form_id,
                    'give_price_id' => $price_id,
                    'date' => $purchase_data['date'],
                    'user_email' => $purchase_data['user_email'],
                    'purchase_key' => $purchase_data['purchase_key'],
                    'currency' => give_get_currency(),
                    'user_info' => $purchase_data['user_info'],
                    'status' => 'pending',
                    'gateway' => 'paystack',
                );

                // Record the pending payment
                $payment = give_insert_payment($payment_data);

                if (!$payment) {
                    // Record the error

                    give_record_gateway_error(__('Payment Error', 'give'), sprintf(__('Payment creation failed before sending donor to Paystack. Payment data: %s', 'give'), json_encode($payment_data)), $payment);
                    // Problems? send back
                    give_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['give-gateway'] . "&message=-some weird error happened-&payment_id=" . json_encode($payment));
                } else {

                    //Begin processing payment

                    if (give_is_test_mode()) {
                        $public_key = give_get_option('paystack_test_public_key');
                        $secret_key = give_get_option('paystack_test_secret_key');
                    } else {
                        $public_key = give_get_option('paystack_live_public_key');
                        $secret_key = give_get_option('paystack_live_secret_key');
                    }

                    $ref = $purchase_data['purchase_key']; // . '-' . time() . '-' . preg_replace("/[^0-9a-z_]/i", "_", $purchase_data['user_email']);
                    $currency = give_get_currency();

                    $verify_url = home_url() . '?' . http_build_query(
                        [
                            Paystack_Give::API_QUERY_VAR => 'verify',
                            'reference' => $ref,
                        ]
                    );

                    //----------
                    $url = "https://api.paystack.co/transaction/initialize";
                    $fields = [
                        'email' => $payment_data['user_email'],
                        'amount' => $payment_data['price'] * 100,
                        'reference' => $ref,
                        'callback_url' => $verify_url,
                        'currency' => $currency,
                        'metadata' => [
                            'custom_fields' => [
                                [
                                    'display_name' => 'Form Title',
                                    'variable_name' => 'form_title',
                                    'value' => $payment_data['give_form_title']
                                ],
                                [
                                    'display_name' => 'Plugin',
                                    'variable_name' => 'plugin',
                                    'value' => 'give'
                                ]
                            ]
                        ]

                    ];
                    $fields_string = http_build_query($fields);
                    //open connection
                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Authorization: Bearer " . $secret_key,
                        "Cache-Control: no-cache",
                    ));

                    //So that curl_exec returns the contents of the cURL; rather than echoing it
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    //execute post
                    $result = curl_exec($ch);
                    $json_response = json_decode($result, true);
                    if ($json_response['status']) {
                        wp_redirect($json_response['data']['authorization_url']);
                        exit;
                    } else {
                        give_send_back_to_checkout('?payment-mode=paystack' . '&error=' . $json_response['message']);
                    }
                    //--------------


                }
            } else {
                give_send_back_to_checkout('?payment-mode=paystack' . '&errors=' . json_encode($errors));
            }
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

    public function Verify_transaction()
    {
        $ref = $_GET['reference'];
        $payment = give_get_payment_by('key', $ref);
        // die(json_encode($payment));

        if ($payment === false) {
            die('not a valid ref');
        }
        if (give_is_test_mode()) {
            $secret_key = give_get_option('paystack_test_secret_key');
        } else {
            $secret_key = give_get_option('paystack_live_secret_key');
        }

        $url = "https://api.paystack.co/transaction/verify/" . $ref;

        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
            ),
        );

        $request = wp_remote_get($url, $args);

        if (is_wp_error($request)) {
            return false; // Bail early
        }

        $body = wp_remote_retrieve_body($request);

        $result = json_decode($body);

        // var_dump($result);

        if ($result->data->status == 'success') {


            //PSTK Logger
            if (give_is_test_mode()) {
                $pk = give_get_option('paystack_test_public_key');
            } else {
                $pk = give_get_option('paystack_live_public_key');
            }
            $pstk_logger =  new give_paystack_plugin_tracker('give', $pk);
            $pstk_logger->log_transaction_success($ref);
            //


            // the transaction was successful, you can deliver value

            give_update_payment_status($payment->ID, 'complete');
            //             echo json_encode(
            //                 [
            //                     'url' => give_get_success_page_uri(),
            //                     'status' => 'given',
            //                 ]
            //             );
            wp_redirect(give_get_success_page_uri());
            exit;
        } else {
            // the transaction was not successful, do not deliver value'
            give_update_payment_status($payment->ID, 'failed');
            give_insert_payment_note($payment, 'ERROR: ' . $result->data->message);
            echo json_encode(
                [
                    'status' => 'not-given',
                    'message' => "Transaction was not successful: Last gateway response was: " . $result['data']['gateway_response'],
                ]
            );
        }
    }
}
