<?php

namespace system;

class template {

    public $var = array();

    static $ext = ".php";

    private $template_dir;

    public function __construct() {
        $this->template_base = $GLOBALS['template_base'];
    }

    public function draw($file) {

       extract($this->var); 
       include $this->template_base.$file.self::$ext;
       unset($this->var);

    }

    public function assign($key,$value) {

        if ( is_array($key) ) {
            $this->var = $key + $this->var;
        }
        else {
            $this->var[$key] = $value;
        }
    }

}
