<?php

class Pages extends Autoloader {

    public function __construct()
    {
      $this->activities = $this->model("DanceTicket");
    }

    public function index(){

        $this->view('pages/index');
    }

    public function food(){
        $this->view('pages/food');
    }
    public function historic(){
        $this->view('pages/historic');
    }

    public function dance(){
        // $this->DanceModel = $this->models->('DanceTickets');
        // $tickets = $this->models->getDanceTickets();
        //$data = ['danceTickets' => []];
        $danceTickets = $this->activities->getDanceTickets();

        $data['danceTickets'] = $danceTickets;


        //print_r(query('SELECT * FROM ticketnormaal'));
        $this->view('pages/dance', $data);
    }

    public function jazz(){
        $this->view('pages/jazz');
    }

    public function cms(){
        $this->view('pages/cms');
    }

}
