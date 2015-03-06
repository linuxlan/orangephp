<?php

NameSpace app\main\controllers;
use system\controller as controller;

class error extends controller {


    public function __construct() {
        parent::__construct();
    }


    public function page404() {
        print "404_page";
    }

}
