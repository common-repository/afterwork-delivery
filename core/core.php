<?php

    class WT_Core
    {
        private $prefix = 'wtlv';
        private $attr = array();

        public function __construct()
        {
            $this->Init();
        }

        private function Init()
        {
            $this->Attr_Set('prefix', $this->prefix);

            $this->Plugin_Paths_Init();
            $this->Plugin_Hooks_Init();
            $this->Plugin_Pages_Init();
            $this->Scripts_Init();
            $this->Shortcodes_Init();

            // called only after woocommerce has finished loading
            add_action( 'woocommerce_init', array( $this, 'Plugin_Classes_Init' ) );

            add_action( 'init', array($this, 'Actions_Init') );
        }

        protected function Attr_Set($key, $value)
        {
            $this->attr[$key] = $value;
        }

        public function Attr_Get($key)
        {
            return ( isset($this->attr[$key]) ) ? $this->attr[$key] : false;
        }

        private function Plugin_Paths_Init()
        {
            $ps = array();

            preg_match("/^(.*[\/\\\]wp-content[\/\\\]plugins[\/\\\])(.*?)[\/\\\].*$/uis", __FILE__, $ps);

            if (is_array($ps) && count($ps) == 3)
            {
                $this->Attr_Set('plugin_dir_name', $ps[2]);
                $this->Attr_Set('plugin_path', $ps[1] . $this->Attr_Get('plugin_dir_name') . '/');
                $this->Attr_Set('plugin_index_path', $this->Attr_Get('plugin_path') . 'index.php');
                $this->Attr_Set('plugin_core_path', $this->Attr_Get('plugin_path') . 'core/');
                $this->Attr_Set('plugin_actions_path', $this->Attr_Get('plugin_path') . 'core/actions/');
                $this->Attr_Set('plugin_data_path', $this->Attr_Get('plugin_path') . 'data/');
                $this->Attr_Set('plugin_classes_path', $this->Attr_Get('plugin_path') . 'core/classes/');
                $this->Attr_Set('plugin_templates_path', $this->Attr_Get('plugin_path') . 'data/templates/');

                $this->Attr_Set('plugin_url', plugins_url($this->Attr_Get('plugin_dir_name')) . '/');
                $this->Attr_Set('plugin_css_url', $this->Attr_Get('plugin_url') . 'data/css/');
                $this->Attr_Set('plugin_js_url', $this->Attr_Get('plugin_url') . 'data/js/');
                $this->Attr_Set('plugin_img_url', $this->Attr_Get('plugin_url') . 'data/images/');
            }
        }

        private function Plugin_Hooks_Init()
        {
            register_activation_hook( $this->Attr_Get('plugin_index_path'), array($this, 'Install') );

            register_deactivation_hook( $this->Attr_Get('plugin_index_path'), array($this, 'Uninstall') );

            add_filter('admin_body_class', function($classes){

                if(isset($_GET['page']) && $_GET['page'] == $this->prefix)
                {
                    $classes = $this->prefix;
                }

                return $classes;
            });
        }

        public function Install()
        {
            $curl_args = array(
                '_handler' => 'wtlv_child',
                '_action' => 'common',
                '_subaction' => 'new_child',
                'child_url' => home_url(),
            );
            $curl_query = $this->curl_get_info($curl_args);
        }

        public function Uninstall()
        {
            $curl_args = array(
                '_handler' => 'wtlv_child',
                '_action' => 'common',
                '_subaction' => 'deactivate_child',
                'child_url' => home_url(),
            );
            $curl_query = $this->curl_get_info($curl_args);
        }

        public function Template_Get($name, $path_dir = null, $data = null)
        {
            if ( is_null($path_dir) )
            {
                $path_dir = $this->Attr_Get('plugin_templates_path');
            }
            $path = $path_dir . $name . '.php';

            if ( file_exists($path) )
            {
                include($path);
            }
            else
            {
                return false;
            }
        }

        public function Page_Title_Build($title)
        {
            return  __('Livraison') . ' :: ' . $title;
        }

        private function Plugin_Pages_Init()
        {

            if ( is_admin() )
            {
                add_action('admin_menu', function(){
                    add_menu_page($this->Page_Title_Build(__('Main')), __('Livraison'), 'manage_options', $this->prefix, function(){
                        $this->Template_Get('pages/main');
                    }, $this->Attr_Get('plugin_img_url') . "logo_plugin.png" , 200);
                });
            }

        }

        public function Shortcodes_Init()
        {
            add_shortcode($this->prefix . '_livraison', function($atts){
                $atts = shortcode_atts(array(
                    'id' => 0
                ), $atts);

                $atts['id'] = (int)$atts['id'];
            });
        }

        public function Actions_Init()
        {

            if ( isset($_REQUEST['_handler']) && $_REQUEST['_handler'] == $this->prefix )
            {
                include_once($this->Attr_Get('plugin_core_path') . 'action.php');

                $action = new WT_Livraison_Action($this);
                $action->name = $_REQUEST['_action'];
                $action->subaction = $_REQUEST['_subaction'];
                $action->Action_Load($_REQUEST[$_REQUEST['_action']]);
            }

        }

        public function Scripts_Init()
        {

            add_action( 'wp_enqueue_scripts',
                    function()
                    {
                        wp_enqueue_script( 'wtlv-main', $this->Attr_Get('plugin_js_url') . 'main.js', array( 'jquery' ), '1.0.0', false );

                        wp_enqueue_style( 'wtlv-front-style', $this->Attr_Get('plugin_css_url') . 'front-style.css' );
                    }
                );

            if( is_admin() )
            {
                add_action( 'admin_enqueue_scripts',
                    function()
                    {
                        wp_enqueue_script('wtlv-main', $this->Attr_Get('plugin_js_url') . 'main.js', array('jquery'), '1.0.0', false);
                        wp_enqueue_style('wtlv-adm-style', $this->Attr_Get('plugin_css_url') . 'adm-style.css');
                    }
                );
            }

        }

        public function Plugin_Classes_Init()
        {
            include_once($this->Attr_Get('plugin_classes_path') . 'wtlv-afterworkdelivery.php');
            include_once($this->Attr_Get('plugin_classes_path') . 'wtlv-afterworkdelivery-classic.php');
            include_once($this->Attr_Get('plugin_classes_path') . 'wtlv-afterworkdelivery-express.php');
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