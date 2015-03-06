<?php
namespace app\main\models;
use system\models;

class userModel extends models {

    protected $table = 'users';
    public function __construct() {
        parent::__construct();
    }

    public function findByUserName($username) {
    }

}
