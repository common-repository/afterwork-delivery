<?php

    switch( $this->subaction )
    {
        case "change_tab":

            if( !isset($_POST['menutype']) ) return false;

            $menutype = esc_sql($_POST['menutype']);

            $curl_args = array(
                '_handler' => 'wtlv_child',
            );

            switch ( $menutype )
            {
                case 'classic':

                    $curl_args = array_merge( $curl_args, array('_action' => 'common', '_subaction' => 'get_days') );
                    $curl_query = $this->core->curl_get_info($curl_args);
                    $days = ($curl_query['status'] == true)  ? $curl_query['days'] : '';

                    $curl_args = array_merge( $curl_args, array('_action' => 'classic', '_subaction' => 'get_timetable') );
                    $curl_query = $this->core->curl_get_info($curl_args);
                    $timetable_data = ($curl_query['status'] == true)  ? $curl_query['timetable'] : '';

                    $curl_args = array_merge( $curl_args, array('_action' => 'classic', '_subaction' => 'get_tarifs', 'shop_url' => home_url()) );
                    $curl_query = $this->core->curl_get_info($curl_args);
                    $tarifs_classic = ($curl_query['status'] == true)  ? $curl_query['tarifs_classic'] : '';

                    ob_start();
                        include($this->core->Attr_Get('plugin_templates_path') . 'tabs/classic-timetable.php');
                        include($this->core->Attr_Get('plugin_templates_path') . 'tabs/classic-tariftable.php');
                    $this->result['html'] = ob_get_contents();
                    ob_end_clean();

                    $this->result['status'] = true;

                break;

                case 'express':

                    $curl_args = array_merge( $curl_args, array('_action' => 'express', '_subaction' => 'get_tarifs', 'shop_url' => home_url()) );
                    $curl_query = $this->core->curl_get_info($curl_args);
                    $tarifs_express = ($curl_query['status'] == true)  ? $curl_query['tarifs_express'] : '';

                    ob_start();
                        include($this->core->Attr_Get('plugin_templates_path') . 'tabs/express-tarif.php');
                    $this->result['html'] = ob_get_contents();
                    ob_end_clean();

                    $this->result['status'] = true;
                break;

                case 'settings':

                    $curl_args = array_merge( $curl_args, array('_action' => 'settings', '_subaction' => 'get_weights') );
                    $curl_query = $this->core->curl_get_info($curl_args);
                    $weights = ($curl_query['status'] == true)  ? $curl_query['weights'] : '';

                    ob_start();
                        include($this->core->Attr_Get('plugin_templates_path') . 'tabs/settings-weights.php');
                    $this->result['html'] = ob_get_contents();
                    ob_end_clean();

                    $this->result['status'] = true;
                break;

                default:
                    $this->result['status'] = false;
                break;
            }
        break;
    }