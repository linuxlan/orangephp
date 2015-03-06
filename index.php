<?php
error_reporting(E_ALL & ~E_NOTICE & ~8192);
/*
 * */


require("vendor/autoload.php");
require("config/system_config.php");

define("DEBUG",TRUE); 

if ( defined("DEBUG") ) {
    if ( DEBUG ) {
        ini_set("display_errors","1");
    }
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define("BASEROOT",str_replace(SELF, '', __FILE__));
define("APPROOT",BASEROOT."app");
define("SYSTEMROOT",BASEROOT."system");
define("BASE_CONTROLLER_NAME","defaulta");
define("BASE_FUNCTION_NAME","index");
#define("BASEURI","/index.php");
define("BASEURI","");
define("MODULE_ILLUMINATE",true);

require_once SYSTEMROOT."/run.php";

