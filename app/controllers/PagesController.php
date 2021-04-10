<?php

class PagesController extends Autoloader {

    public function __construct(){
    }

    public function index(){
        $this->view("pages/index");
    }


}
