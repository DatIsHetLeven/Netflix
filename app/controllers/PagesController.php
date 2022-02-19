<?php

class PagesController extends Autoloader {

    public function __construct(){
    }

    public function index(){
        $this->view("pages/index");
    }

    public function inloggen(){
        $this->view("pages/inloggen");
    }


}
