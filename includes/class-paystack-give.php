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

        function give_paystack_settings($settings)
        {

            $check_settings = array(
                array(
                    'name' => __('Paystack', 'paystack-give'),
                    'desc' => '',
                    'type' => 'give_title',
                    'id' => 'give_title_paystack',
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
                    'name' => __('Billing Details', 'paystack-give'),
                    'desc' => __('This will enable you to collect donor details. This is not required by Paystack (except email) but you might need to collect all information for record purposes', 'paystack-give'),
                    'id' => 'paystack_billing_details',
                    'type' => 'radio_inline',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled' => __('Enabled', 'paystack-give'),
                        'disabled' => __('Disabled', 'paystack-give'),
                    ),
                ),
            );

            return array_merge($settings, $check_settings);
        }

        add_filter('give_settings_gateways', 'give_paystack_settings');

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
            $payment_data = array(
                'price' => $purchase_data['price'],
                'give_form_title' => $purchase_data['post_data']['give-form-title'],
                'give_form_id' => intval($purchase_data['post_data']['give-form-id']),
                'give_price_id' => isset($purchase_data['post_data']['give-price-id']) ? $purchase_data['post_data']['give-price-id'] : '',
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
                give_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['give-gateway']);
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

                echo "
                    <script src='https://js.paystack.co/v1/inline.js'></script>
                    <script
                        src='https://code.jquery.com/jquery-3.3.1.min.js'
                        integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8='
                        crossorigin='anonymous'>
                    </script>
                    <script>
                    function payWithPaystack(){
                        var handler = PaystackPop.setup({
                        key: '$public_key',
                        email: '${payment_data['user_email']}',
                        amount: ${payment_data['price']} * 100,
                        ref: '$ref',
                        firstname: '{$payment_data['user_info']['first_name']}',
                        lastname: '{$payment_data['user_info']['last_name']}',
                        currency: '$currency',
                        metadata: {
                            custom_fields: [
                                {
                                    display_name: 'Form Title',
                                    variable_name: 'form_title',
                                    value: '${payment_data['give_form_title']}'
                                }
                            ]
                        },
                        callback: function(response){
                            if(response.reference!=='$ref'){return;}
                            $(document.body).addClass('show-loader');
                            $.ajax({
                                url: '$verify_url',
                                method: 'post',
                                success: function (data) {
                                    dx = JSON.parse(data);
                                    window.location.replace(dx.url);
                                },
                                error: function(err){
                                    console.log(err);
                                    window.history.back();
                                }
                            })
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
                    <div class='loader'>
                        <div class='loader__spinner'>
                            <div></div><div></div><div></div><div></div><div></div><div></div><div>
                            </div><div></div><div></div><div></div><div></div><div></div>
                        </div>
                    </div>
                    <style>
                        .show-loader .loader {
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                            -webkit-box-pack: center;
                            -ms-flex-pack: center;
                                justify-content: center;
                            -webkit-box-align: center;
                            -ms-flex-align: center;
                                align-items: center;
                            height: 100%;
                            width: 100%;
                        }
                        .loader {
                            display: none;
                            text-align: center;
                            // background-color: #404040;
                        }
                        @keyframes loader__spinner {
                            0% {
                                opacity: 1;
                            }
                            100% {
                                opacity: 0;
                            }
                        }
                        @-webkit-keyframes loader__spinner {
                            0% {
                                opacity: 1;
                            }
                            100% {
                                opacity: 0;
                            }
                        }
                        .loader__spinner {
                            position: relative;
                            display: inline-block;
                        }
                        .loader__spinner div {
                            left: 95px;
                            top: 35px;
                            position: absolute;
                            -webkit-animation: loader__spinner linear 1s infinite;
                            animation: loader__spinner linear 1s infinite;
                            background: #393939;
                            width: 10px;
                            height: 30px;
                            border-radius: 40%;
                            -webkit-transform-origin: 5px 65px;
                            transform-origin: 5px 65px;
                        }
                        .loader__spinner div:nth-child(1) {
                            -webkit-transform: rotate(0deg);
                            transform: rotate(0deg);
                            -webkit-animation-delay: -0.91667s;
                            animation-delay: -0.91667s;
                        }
                        .loader__spinner div:nth-child(2) {
                            -webkit-transform: rotate(30deg);
                            transform: rotate(30deg);
                            -webkit-animation-delay: -0.83333s;
                            animation-delay: -0.83333s;
                        }
                        .loader__spinner div:nth-child(3) {
                            -webkit-transform: rotate(60deg);
                            transform: rotate(60deg);
                            -webkit-animation-delay: -0.75s;
                            animation-delay: -0.75s;
                        }
                        .loader__spinner div:nth-child(4) {
                            -webkit-transform: rotate(90deg);
                            transform: rotate(90deg);
                            -webkit-animation-delay: -0.66667s;
                            animation-delay: -0.66667s;
                        }
                        .loader__spinner div:nth-child(5) {
                            -webkit-transform: rotate(120deg);
                            transform: rotate(120deg);
                            -webkit-animation-delay: -0.58333s;
                            animation-delay: -0.58333s;
                        }
                        .loader__spinner div:nth-child(6) {
                            -webkit-transform: rotate(150deg);
                            transform: rotate(150deg);
                            -webkit-animation-delay: -0.5s;
                            animation-delay: -0.5s;
                        }
                        .loader__spinner div:nth-child(7) {
                            -webkit-transform: rotate(180deg);
                            transform: rotate(180deg);
                            -webkit-animation-delay: -0.41667s;
                            animation-delay: -0.41667s;
                        }
                            .loader__spinner div:nth-child(8) {
                            -webkit-transform: rotate(210deg);
                            transform: rotate(210deg);
                            -webkit-animation-delay: -0.33333s;
                            animation-delay: -0.33333s;
                        }
                        .loader__spinner div:nth-child(9) {
                            -webkit-transform: rotate(240deg);
                            transform: rotate(240deg);
                            -webkit-animation-delay: -0.25s;
                            animation-delay: -0.25s;
                        }
                        .loader__spinner div:nth-child(10) {
                            -webkit-transform: rotate(270deg);
                            transform: rotate(270deg);
                            -webkit-animation-delay: -0.16667s;
                            animation-delay: -0.16667s;
                        }
                        .loader__spinner div:nth-child(11) {
                            -webkit-transform: rotate(300deg);
                            transform: rotate(300deg);
                            -webkit-animation-delay: -0.08333s;
                            animation-delay: -0.08333s;
                        }
                        .loader__spinner div:nth-child(12) {
                            -webkit-transform: rotate(330deg);
                            transform: rotate(330deg);
                            -webkit-animation-delay: 0s;
                            animation-delay: 0s;
                        }
                        .loader__spinner {
                            width: 40px;
                            height: 40px;
                            margin: auto;
                            -webkit-transform: translate(-20px, -20px) scale(0.2) translate(20px, 20px);
                            transform: translate(-20px, -20px) scale(0.2) translate(20px, 20px);
                        }
                    </style>
                ";
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
            $public_key = give_get_option('paystack_live_public_key');
        }

        $curl = curl_init();

        curl_setopt_array(
            $curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $ref,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $secret_key",
                    "Cache-Control: no-cache",
                ),
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $result = json_decode($response, true);

            if ($result) {
                if (isset($result['data'])) {
                    //something came in
                    if ($result['data']['status'] == 'success') {
                        // the transaction was successful, you can deliver value
                        give_update_payment_status($payment->ID, 'complete');
                        echo json_encode(
                            [
                                'url' => give_get_success_page_uri(),
                                'status' => 'given',
                            ]
                        );
                    } else {
                        // the transaction was not successful, do not deliver value'
                        give_update_payment_status($payment->ID, 'failed');
                        give_insert_payment_note($payment, 'ERROR: ' . $result['data']['message']);
                        echo json_encode(
                            [
                                'status' => 'not-given',
                                'message' => "Transaction was not successful: Last gateway response was: " . $result['data']['gateway_response'],
                            ]
                        );
                    }

                } else {
                    echo isset($result['message']) ? $result['message'] : 'An unexpected error occurred';
                }
            }
        }
    }

}
