<?php

class PagesController extends Autoloader {

    public function __construct()
    {
        $this->activities = $this->model("Activity");
        $this->EventModel = $this->model("Event");
        $this->ArtistModel = $this->model("Artist");
        $this->LocationModel = $this->model("Location");
    }

    public function index(){
        $events = $this->EventModel->getAllEvents();
        $data["events"] = $events;

        $this->view("pages/index",$data);
    }

    public function food(){
        $data = [
            "restaurants" => "",
            "reservations" => ""
        ];
        
        $data["restaurants"] = $this->LocationModel->getRestaurants();
        $data["reservations"] = $this->activities->getReservations();
        
        $this->view('pages/food', $data);
    }
    
    public function historic(){
        $data = [
            "tours" => ""
        ];

        $tours = $this->activities->getHistoricTours();
        $data["tours"] = $tours;

        $this->view('pages/historic',$data);
    }


    public function dance(){
      // krijg dance info
      $artistInfo = $this->ArtistModel->getDanceArtist();
      $data['artistInfo'] = $artistInfo;

      $danceTickets = $this->activities->getDanceTickets();
      $data['danceTickets'] = $danceTickets;


      $this->view('pages/dance', $data);
    }

    public function jazz(){
        $jazzTickets = $this->activities->getJazzTickets();
        $data['performances'] = $jazzTickets;
        $data['errorMess'] = '';
        $this->view('pages/jazz', $data);
    }

    public function cms(){
        $this->view('pages/cms');
    }

    public function infoPage() {
        $data = [
            "status" => "notFound",
        ];

        if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
            $pageId = intval($_GET["page"]);

            $pageModel = $this->model("Page");
            $page = $pageModel->getPageById($pageId);

            if ($page !== null) {
                $data["page"] = $page;
                $data["status"] = "success";
            }
        }

        $this->view("cms/infoPage/infoPageView", $data);
    }
}
