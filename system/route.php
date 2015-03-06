<?php
namespace system;
//register_shutdown_function(route::error());
//:w
//set_error_handler(route::error());


class route {

    private static $instance;

    protected $segment_data = array();
    protected $is_rewrite = false;
    protected $application = "main";
    protected $object_load_list = array();

    public function __construct() {
        $base_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->segment_data = explode('/', $base_uri);
        if ( isset($this->segment_data[1]) && $this->segment_data[1] != ""){
            $this->application = $this->segment_data[1];
        }
        self::$instance =& $this;
    }

    public function get_current_application() {
        return $this->application;
    }

    public function register_autoLoader($obj_name,&$obj){
        if ( !isset($this->object_load_list[$obj_name])) {
           $this->object_load_list[$obj_name]  = $obj;
        }
    }

    public static function error() {
        exit;
    }

    public function get_segment($index) {

        if ( isset($this->segment_data[$index]) ) {
            return $this->segment_data[$index];
        }
        return false;
    }

    public function set_rewrite($bool) {
        $this->is_rewrite = $bool;
     
    }

    public function goto_segment() {

        if ( !$this->is_rewrite ) {
            
            $_CONTROLLER = !isset($this->segment_data[3]) ? BASE_CONTROLLER_NAME : $this->segment_data[3];
            $method = !isset($this->segment_data[4]) ? BASE_FUNCTION_NAME : $this->segment_data[4];
            $segment_data = $this->segment_data[5];
        }
        else {
            $_CONTROLLER = !isset($this->segment_data[2]) ? BASE_CONTROLLER_NAME : $this->segment_data[2];
            $method = !isset($this->segment_data[3]) ? BASE_FUNCTION_NAME : $this->segment_data[3];
            $segment_data = $this->segment_data[4];
        }
        // init controller
        $fun = "app\\{$this->application}\\controllers\\".$_CONTROLLER;
        try {

            if ( !class_exists($fun) ) {
                echo "Error _ class";return;
            }
            $my = new $fun;
            foreach ( $this->object_load_list as $key=>$val ) {
                $my->$key = $val;
            }
            $my->route = self::$instance;
            if ( !method_exists($my,$method) ) {
                echo "Error";exit;
                
            }
            $my->$method();
        }
        catch(Exception $e) {
           echo "404"; 
        }
    }

    public static function get_instance() {
        return self::$instance;
    }

}
