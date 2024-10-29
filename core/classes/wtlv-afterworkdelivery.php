<?php

    class WT_AfterWorkDelivery extends WC_Shipping_Method
    {
        protected $core;
        protected $arr_postcodes;
        protected $generic_info;

        public function __construct()
        {
            $this->core = new WT_Core();

            add_action('woocommerce_order_details_after_order_table', array($this, 'Send_Message'), 100, 1);

            $curl_args = array(
                '_handler' => 'wtlv_child',
                '_action' => 'common',
                '_subaction' => 'get_all_config',
                'shop_url' => home_url(),
            );
            $curl_query = $this->core->curl_get_info($curl_args);
            $this->generic_info = ($curl_query['status'] == true)  ? $curl_query['generic_info'] : null;

            $this->Init();

        }

        private function Init()
        {
            if( count($this->generic_info['postcodes']) )
            {
                foreach($this->generic_info['postcodes'] as $k => $v)
                {
                    $this->arr_postcodes[$v['district']] = explode(",", $v['allowed']);
                }
            }

            add_filter( 'woocommerce_cart_shipping_method_full_label', array(&$this,'change_shipping_label'), 10, 2 );
        }

        public function init_form_fields()
        {

            $fields = array(

                'enabled' => array(
                    'title' => __('Enable/Disable', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable this shipping method', 'woocommerce'),
                    'default' => 'yes',

                ),

            );

            $this->form_fields = $fields;

        }


        /**
         * check_weight function.
         *
         * @access public
         * @param mixed $package
         * @return bool
         */
        public function check_weight( $package )
        {

            if( empty($this->generic_info['weights']) || empty($package['contents']) )
            {
                return false;
            }

            $error_weight = array(
                'min' => 0,
                'max' => 0
            );

            $weights = $this->generic_info['weights'];

            foreach( $package['contents'] as $k => $v )
            {
                $prd = get_product($v['product_id']);

                switch( $v['data']->product_type )
                {
                    case 'simple':
                        $prd_weight = (float)$prd->get_weight();
                    break;

                    case 'variation':
                        $prd_variations_data = $prd->get_available_variations();
                        $variations_weights = wp_list_pluck( $prd_variations_data, 'weight', 'variation_id' );
                        $wieght_str = $variations_weights[ $v['variation_id'] ];
                        preg_match( "/[0-9.,]+/uis", $wieght_str, $matches );
                        $prd_weight = (float)$matches[0];

                    break;
                }
                switch( esc_attr(get_option('woocommerce_weight_unit')) )
                {
                    case "g":
                        $prd_weight /= 1000.0;
                    break;

                    case "lbs":
                        $prd_weight *= 0.45;
                    break;

                    case "oz":
                        $prd_weight *= 0.03;
                    break;
                }

                if( $prd_weight < $weights['from'] )
                {
                    $error_weight['min']++;
                }
                else if ( $prd_weight > $weights['to'] )
                {
                    $error_weight['max']++;
                }
            }
            if( $error_weight['min'] != 0 || $error_weight['max'] != 0 )
            {
                return false;
            }

            return true;
        }

        public function is_correct_postcode($postcode)
        {
            if( empty($this->arr_postcodes) )
            {
                return false;
            }

            $catched = false;

            foreach( $this->arr_postcodes as $district )
            {
                if( in_array($postcode, $district) )
                {
                    $catched = true;
                    break;
                }
            }

            return ($catched) ? true : false;
        }

        public function calculate_final_rate($package, $cost)
        {
            if( empty($cost) || empty($package['contents']) )
            {
                return false;
            }

            $count = 0;
            foreach( $package['contents'] as $k => $v )
            {
                $count += $v['quantity'];
            }

            if( $count > 0 )
            {
                if($count <= 3)
                {
                    $final_rate = $cost;
                }
                else
                {
                    $final_rate = $cost + $cost * ($count - 3);
                }
            }
            else
            {
                return false;
            }

            return $final_rate;
        }

        public function register_rate($id, $title, $final_rate)
        {
            /** Method Title For Afterwork Delivery Classic Method */
            $label = __($title, $id);

            $rate = array(
                'id' => $id,
                'label' => (trim($label) != '' ? $label : $title),
                'cost' => $final_rate,
                'calc_tax' => 'per_order'
            );

            // Register the rate
            $this->add_rate($rate);
        }

        /**
         * add logo to carrier display.
         *
         * @access public
         * @param $full_label, $method
         * @return full_label
         */
        public function change_shipping_label($full_label, $method){

            //var_dump($method);

            if (in_array($method->id, array("wtlv_shipping_classic", "wtlv_shipping_express")))
            {
                if (!strpos( $full_label, 'wtlv-logo'))
                {
                    $full_label = '<span class="wtlv-logo"></span>' . $full_label;
                }
            }

            return $full_label;
        }

    }