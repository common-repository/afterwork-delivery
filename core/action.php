<?php

    class WT_Livraison_Action
    {
        public $core;
        public $name;
        public $subaction;
        private $prefix;
        private $data = null;

        private $result = array(
            'error'  => array(),
            'status' => false
        );

        public function __construct($core)
        {
            $this->core = $core;
            $this->prefix = $this->core->Attr_Get('prefix');
        }

        public function Action_Load($data = null)
        {
            if ( is_string($this->name) && is_string($this->subaction) )
            {
                $file_path = $this->core->Attr_Get('plugin_actions_path') . $this->name . '.php';

                if ($file_path)
                {
                    global $wpdb;

                    include_once($file_path);
                }
            }

            $this->Result();
        }

        private function Result()
        {
            if ( is_array($this->result) )
            {
                die(json_encode($this->result));
            }
        }

    }
