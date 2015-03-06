<?php
namespace system;

use system\template as template;

class controller {


    private static $instance;
    protected $view ;

    public function __construct () {

        $this->view  = &new template();
        self::$instance =& $this;

    }

}
