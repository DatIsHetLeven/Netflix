<?php

// laad de model en de view op

class Autoloader {
    public function model($model){
        $modelName = $model . 'Model';

        require_once '../app/models/' . $modelName . '.php';
        return new $modelName();
    }

    public function view($view, $data = []){
        if(file_exists('../app/views/' . $view . '.php')){
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}
