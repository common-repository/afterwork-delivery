<?php

    // Exit if accessed directly
    if ( !defined('ABSPATH') )
    {
        exit;
    }

    /**
     * Check if WooCommerce is active
     */
    if ( !in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins'))) )
    {
        exit;
    }

    /**
     * Is used to obtain information from generic site
     *
     * @var string
     */
    define( 'WTLV_URL_OF_GENERIC_SITE', "http://outofhours.fr/" );

    /**
     * ID for "Livraison soir et weekend classique" shipping method
     *
     * @var string
     */
    define( 'WTLV_CLASSIC_PLUGIN_ID', 'wtlv_shipping_classic' );

    /**
     * ID for "Livraison soir et weekend en 24h" shipping method
     *
     * @var string
     */
    define( 'WTLV_EXPRESS_PLUGIN_ID', 'wtlv_shipping_express' );

    /**
     * Add the plugin to the shipping methods list
     *
     * @since 1.0
     * @param array $methods
     * @return array
     */
    function wtlv_add_shipping_method( $methods )
    {

        $methods[] = 'WT_AfterWorkDelivery_Classic';
        $methods[] = 'WT_AfterWorkDelivery_Express';

        return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'wtlv_add_shipping_method' );

    /**
     *  Add a custom email to the list of emails WooCommerce should load
     *
     * @since 1.0
     * @param array $email_classes available email classes
     * @return array filtered available email classes
     */
    function add_woocommerce_order_afterwork_email( $email_classes )
    {
        include_once('classes/wtlv-afterwork-order-email.php');

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WTLV_Afterwork_Order_Email'] = new WTLV_Afterwork_Order_Email();

        return $email_classes;
    }
    add_filter( 'woocommerce_email_classes', 'add_woocommerce_order_afterwork_email' );

    include_once('core.php');

    $wt_core = new WT_Core();



