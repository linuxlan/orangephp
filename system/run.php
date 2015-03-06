<?php
/*
 *@author:
 *
 */

$start_time = time();
session_start();

use system\controller as controller;
use system\database as db;
use system\route as route;
use system\sessionEx as session;
use Illuminate\Database\Capsule\Manager as Capsule;

$default = "main";
$approot = APPROOT;

//url parse
$base_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $base_uri);


$route = &new route;

$route->set_rewrite(BASEURI != "" ? false : true);
//
//init App Name
$app = $route->get_current_application();

// init database
//
if ( !MODULE_ILLUMINATE ) {
    $db = &new db();
}
else {
    $capsule = new Capsule;
    $capsule->addConnection(require APPROOT."/{$app}/config/database.php");
    $capsule->bootEloquent();
}

//init template dir 
//
$template_base = APPROOT."/{$app}/views/";


$session   = new session;
$session->set_userdata("aaa","bbbbb");

$route->register_autoLoader("session",$session);
$route->goto_segment();

$end_time = time();

if (class_exists('system\database') && !MODULE_ILLUMINATE)
{   
    $db->close();
}                                                                                                                             

