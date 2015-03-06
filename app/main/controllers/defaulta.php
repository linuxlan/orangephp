<?php

NameSpace app\main\controllers;
use system\controller as controller;

use app\main\models\userModel as userModel;
use app\main\models\newsModel as newsModel;
use app\main\models\commentModel as commentModel;


class defaulta extends controller{

    private $user_model;
    private $news_model;
    private $comment_model;
    private $user_info;

    public function __construct() {
        parent::__construct();
        $this->user_model = new userModel;
        $this->view->assign("user_info",$this->user_info);
    }

    public function index() {
//        var_dump($this->user_model->create(array("username" => "user"))->save());
//       $this->user_model->username= "user_".time();
//       $this->user_model->save();
        //foreach(array("username"=>"111") as $key=>$val) {
        //    $this->user_model->$key = $val;
        //}
        //$this->user_model->save();

        var_dump($this->user_model->where("username","=","linuxlan")->where('id','=','1')->get()->toArray());
        var_dump($this->user_model->all()->toArray());
        var_dump($route);
        var_dump($this->route->get_segment(3));
        //$this->view->assign("list",$data);
        //$this->view->draw("index");
    }


    public function test() {
        var_dump($this->session->set_userdata("aaa","哈哈哈哈哈"));
        var_dump($this->session->userdata("aaa"));
        echo "test method";
    }

    public function search() {
        var_dump($this->user_info);
    }

    public function detail() {

        $is_commnet = isset($_GET['comment']) ? true : false;
        if ( $is_commnet ) {
            $data = $_POST;
            if ( $data['comment'] == "" ) {
                header("Location: ".BASEURI."/defaulta/detail/{$GLOBALS['segment_data']}");return;
            }
            $data['create_time'] = time();
            $data['status'] = 1;
            $this->comment_model->insert($data);

            header("Location: ".BASEURI."/defaulta/detail/{$GLOBALS['segment_data']}");return;
        }

        if ( !isset($GLOBALS['uri_segments'][4])  || $GLOBALS['uri_segments'][4] == "") {
            header("Location: /");return;
        }
        $new_news_list = $this->news_model->findAll("",0,5);

        $news_id = $GLOBALS['uri_segments'][4];
        $news_info = $this->news_model->findOneByID($news_id);
        $comment_list = $this->comment_model->findAll(array("news_id"=>$GLOBALS['segment_data']),0,999);

        if ( !$news_info ) {
            header("Location: /");return;
        }

        $comment_count = $this->comment_model->countByNewsID($news_id);
        $user_list = $this->user_model->findAll("",0,999);
        $this->view->assign("user_list",$user_list);
        $this->view->assign("info",$news_info);
        $this->view->assign("comment_list",$comment_list);
        $this->view->assign("new_news_list",$new_news_list);
        $this->view->assign("comment_count",$comment_count['count']);
        $this->view->draw("default_detail");
    }

    public function dologin() {
        $data = $_POST;

        $result = $this->user_model->login($data['username'],md5($data['passwd']));

        if ( !$result  ) {
            header("Location: ".BASEURI);return;
        }

        if ( $result['status']  ==2 ) {
            $result['status_lab'] = "管理员";
        }
        else {
            $result['status_lab'] = "用户";
        }
        unset($result['passwd']);
        $_SESSION['user_info'] = $result;

        header("Location: ".BASEURI);return;
    }

    public function doregister() {
        $data = $_POST;

        if ( $data['username'] == "") {
            header("Location: ".BASEURI);return;
        }
        if ( $data['nickname'] == "") {
            header("Location: ".BASEURI);return;
        }
        if ( $data['passwd'] == "") {
            header("Location: ".BASEURI);return;
        }
        if ( $data['passwd'] != $data['passwd_new']) {
            header("Location: ".BASEURI);return;
        }
        unset($data['passwd_new']);

        $data['create_time'] = time();
        $data['status'] = 1;
        $data['passwd'] = md5($data['passwd']);

        $this->user_model->insert($data);

        header("Location: ".BASEURI);return;
    }

    public function logout() {
        unset($_SESSION['user_info']);
        header("Location: ".BASEURI);return;
    }
}
