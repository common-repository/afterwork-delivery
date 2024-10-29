<?php

    class WT_AfterWorkDelivery_Express extends WT_AfterWorkDelivery
    {

        public function __construct()
        {
            parent::__construct();

            // Id for your shipping method. Should be uunique.
            $this->id = WTLV_EXPRESS_PLUGIN_ID;

            // Title shown in admin
            $this->method_title = __( 'Afterwork Delivery Express' );

            // Description shown in admin
            $this->method_description = __("
                Afterwork Delivery Express is a service offered by<a href='http://afterworkdelivery.com/index.php'>Afterwork Delivery</a>, allowing your customer to chose their preferred delivery method when ordering in your webshop.
                This method allows to ship during 6-24 hours after order confirmation.
                ");

            $this->Init();
        }

        private function Init()
        {

            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
            $this->title = $this->settings['title'];
            $this->enabled = $this->settings['enabled'];

            // Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            parent::init_form_fields();

            $fields = array(

                'title' => array(
                    'title' => __('Method Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                    'default' => __('Livraison Soirs et Week-ends', $this->id),

                ),

            );

            $this->form_fields = array_merge($this->form_fields, $fields);
        }

        public function calculate_shipping( $package = array())
        {
            if( empty($package['destination']['postcode']) )
            {
                // Hide Afterwork Delivery Method on Checkout page.
                // Reason: user didn't indicate his postcode
                return false;
            }

            $is_in_limits = $this->check_weight( $package );
            if( !$is_in_limits )
            {
                // Hide Afterwork Delivery Method on Checkout page.
                // Reason: weight at least one of the goods is out of range of allowed
                return false;
            }

            $is_correct_postcode = $this->is_correct_postcode( $package['destination']['postcode'] );
            if( !$is_correct_postcode )
            {
                // Hide Afterwork Delivery Method on Checkout page.
                // Reason: user's destination postcode is not in an array of valid values for Île-de-France
                return false;
            }

            if( empty($this->generic_info['tarifs_express']) )
            {
                // Hide Afterwork Delivery Method on Checkout page.
                // Reason: no information about express tarif value provided by generic site
                return false;
            }

            $tarifs = $this->generic_info['tarifs_express'];

            $cost = (float) $tarifs['cost'];

            $final_rate = $this->calculate_final_rate( $package, $cost);

            if( $final_rate === false )
            {
                // Hide Afterwork Delivery Method on Checkout page.
                // Reason: cost or quantity error
                return false;
            }

            $this->register_rate($this->id, $this->title, $final_rate);
        }

    }
