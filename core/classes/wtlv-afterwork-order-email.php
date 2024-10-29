<?php

class WTLV_Afterwork_Order_Email extends WC_Email
{

    /**
     * Set email defaults
     */
    public function __construct()
    {
        // set ID, this simply needs to be a unique name
        $this->id = 'wtlv_afterwork_order_email';

        // set title for Afterwork Delivery Service email method in WooCommerce
        $this->title = 'Afterwork Delivery Service';

        // these are the default heading and subject lines
        $this->heading = 'Shipping Order';
        $this->subject = 'Shipping Order from ' . home_url();

        $this->template_html  = 'emails/admin-new-order.php';
        $this->template_plain = 'emails/plain/admin-new-order.php';

        // trigger on new paid orders
        add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger_pending_to_processing' ) );
        add_action( 'woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger_failed_to_processing' ) );
        add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger_pending_to_completed' ) );
        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'trigger_pending_to_on_hold') );
        add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'trigger_failed_to_completed') );
        add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $this, 'trigger_failed_to_on_hold') );

        // trigger on new action: Completed order email
        add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger_completed') );

        // call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

        // set the recipient
        $curl_args = array(
            '_handler' => 'wtlv_child',
            '_action' => 'common',
            '_subaction' => 'admin_email',
        );
        $curl_query = $this->curl_get_info($curl_args);
        $this->recipient = ($curl_query['status'] == true)  ? $curl_query['admin_email'] : '';

        // if none was entered, just use the email for http://afterworkdelivery.com/
        if ( empty($this->recipient) )
        {
            $this->recipient = "adepeju.kehinde@gmail.com";
        }

        preg_match("/^(.*[\/\\\]wp-content[\/\\\]plugins[\/\\\])(.*?)[\/\\\].*$/uis", __FILE__, $ps);
        if ( is_array($ps) && count($ps) == 3 )
        {
            $this->tpl_path = $ps[1] . $ps[2] . '/data/templates/';
        }

    }

    public function trigger_pending_to_processing( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form pending to processing.');

            // send the email
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
    }

    public function trigger_failed_to_processing( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form failed to processing.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function trigger_pending_to_completed( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form pending to completed.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function trigger_pending_to_on_hold( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form pending to on-hold.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function trigger_failed_to_completed( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form failed to completed.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function trigger_failed_to_on_hold( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed form failed to on-hold.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function trigger_completed( $order_id )
    {
        if ( !$order_id )
        {
            return false;
        }

        $is_need_send = $this->create_email($order_id);

        if ( $is_need_send )
        {
            $this->heading = __('Shipping Order: status was changed to completed.');

            // send the email
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }


    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @param int $order_id
     */
    public function create_email( $order_id )
    {

        $wtlv_classic = new WT_AfterWorkDelivery_Classic();
        $wtlv_express = new WT_AfterWorkDelivery_Express();

        //var_dump($wtlv_express->id); die;

        // return if both methods of Afterwork Delivery are swiched off
        if ( $wtlv_classic->settings['enabled'] == "no" && !$wtlv_express->settings['enabled'] == "no" )
        {
            return false;
        }

        // setup order object
        $this->object = new WC_Order( $order_id );
        $items = $this->object->get_items( 'shipping' );
        $shipping_method_id = array_values($items)[0]['method_id'];

        // return if shipping method is not one of Afterwork Delivery
        if ( !in_array($shipping_method_id, array("wtlv_shipping_classic", "wtlv_shipping_express")) )
        {
            return false;
        }

        /*
            // you can replace some variables
            $this->find[] = '{order_date}';
            $this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

            $this->find[] = '{order_number}';
            $this->replace[] = $this->object->get_order_number();
        */
        add_action( 'woocommerce_email_after_order_table', array( $this, 'wtlv_email_after_order_table' ), 1, 1 );

        return true;
    }

    /**
     * get_content_html function.
     *
     * @return string
     */
    public function get_content_html()
    {
        ob_start();
        woocommerce_get_template
        (
            $this->template_html,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading()
            )
        );
        return ob_get_clean();
    }


    /**
     * get_content_plain function.
     *
     * @return string
     */
    public function get_content_plain()
    {
        ob_start();
        woocommerce_get_template
        (
            $this->template_plain,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading()
            )
        );
        return ob_get_clean();
    }

    /**
     * wtlv_email_after_order_table function.
     *
     * includes additional table in email for Afterword Delivery Service
     * @param mixed $order
     * @return void
     */
    public function wtlv_email_after_order_table( $order )
    {
        include($this->tpl_path . 'emails/after-order-table.php');
    }

    public function curl_get_info($args)
    {

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, WTLV_URL_OF_GENERIC_SITE);
        curl_setopt($c, CURLOPT_TIMEOUT, 10);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $args);

        $result = curl_exec($c);
        curl_close($c);

        return(json_decode($result, true));

    }

}