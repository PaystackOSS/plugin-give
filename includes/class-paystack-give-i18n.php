<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link  https://paystack.com
 * @since 1.0.0
 *
 * @package    Paystack_Give
 * @subpackage Paystack_Give/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Paystack_Give
 * @subpackage Paystack_Give/includes
 * @author     Paystack <support@paystack.com>
 */
class Paystack_Give_i18n
{


    /**
     * Load the plugin text domain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() 
    {

        load_plugin_textdomain(
            'paystack-give',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

    }



}
