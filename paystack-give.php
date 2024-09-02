<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://paystack.com
 * @since   1.0.0
 * @package Paystack_Give
 *
 * @wordpress-plugin
 * Plugin Name:       Paystack Payments for Give
 * Plugin URI:        http://wordpress.org/plugins/paystack-give
 * Description:       Paystack integration for accepting payments via card, bank accounts, USSD and mobile money
 * Version:           2.0.3
 * Author:            Paystack
 * Author URI:        https://paystack.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       paystack-give
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '2.0.3');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paystack-give-activator.php
 */
function activate_paystack_give()
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-paystack-give-activator.php';
    Paystack_Give_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paystack-give-deactivator.php
 */
function deactivate_paystack_give()
{
    include_once plugin_dir_path(__FILE__) . 'includes/class-paystack-give-deactivator.php';
    Paystack_Give_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_paystack_give');
register_deactivation_hook(__FILE__, 'deactivate_paystack_give');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-paystack-give.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_paystack_give()
{

    $plugin = new Paystack_Give();
    $plugin->run();
}
run_paystack_give();
