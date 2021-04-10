<?php


class TicketController extends Autoloader{

    public function __construct()
    {
        $this->activities = $this->model("Activity");
        $this->tickets = $this->model("Ticket");
        $this->events = $this->model("Event");

    }

    public function shoppingCart(){
        $data = [
            "tickets" => "",
            "events" => ""
        ];

        $data["tickets"] = $this->tickets->getTicketsCartInfo();
        $data["events"] = $this->events->getAllEvents();

        $this->view('tickets/shoppingCart', $data);
    }

    // get jazz ticket as json
    public function getJazzTicket() {
        $data = ['selectedTicket' => '', 'errorMess' => ''];
        if(isset($_GET['JazzTicketId'])) {
            $selectedId = $_GET['JazzTicketId'];
            $tempTicket = $this->activities->getJazzTicketById($selectedId);
            if(strpos($tempTicket->TicketName, 'All-Access pass for') !== false) {
                $data['selectedTicket'] = $tempTicket;
            } else {
                if(!empty($tempTicket)) {
                    $data['selectedTicket'] = $tempTicket;
                } elseif($tempTicket === 'empty') {
                    $data['errorMess'] = 'The ticket is sold out';
                } else {
                    $data['errorMess'] = 'Something went wrong, please try again';
                }
            }
        }
        echo json_encode($data);
    }
    public function addJazzToCart() {
        $getJazzTicket = $this->getJazzTicket();
        $jazzTicket = json_decode($getJazzTicket);

        $tempInt = $_GET['participantAmount'];
        $tempInput = filter_var(trim($tempInt), FILTER_SANITIZE_NUMBER_INT);
        if(filter_var($tempInput, FILTER_SANITIZE_NUMBER_INT)) {
            $ticketAmount = $tempInput;
            if ($ticketAmount >= 0) {
                $ticketId = $jazzTicket->TicketId;
                header("Location: " . URLROOT . "/ticket/addTicketToCart&ticketId="  .  $ticketId . "&participantAmount=" . $ticketAmount);

            } else {
                $data['ErrorMess'] = 'The amount was zero, please choose an amount above it.';
                $this->view('pages/jazz' ,$data);
            }
        } else {
            $data['ErrorMess'] = 'The amount is acceptable, please try again';
            $this->view('pages/jazz' ,$data);
        }
    }


    public function getTicket(){

        $data = [];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $activityId = $_GET["activityId"];

            $activity = $this->activities->getHistoricTourById($activityId);

            $data = [
                "activity" => $activity
            ];
        }
        $this->view('tickets/selectedTour' ,$data);
    }

    public function addTicketToCart(){


        if(isset($_GET["ticketId"]) && isset($_GET["participantAmount"])){


            //get the variables set in the URL link, comment is only set if it is given in the URL
            $ticketId = $_GET["ticketId"];
            $participantAmount = $_GET["participantAmount"];
            $price = 0;

            if(isset($_GET["comment"])) {
                $comment = $_GET["comment"];
            } else {
                $comment = null;
            }

            //initialize variables, load the ticket from the DB

            $localSelected = [];
            $currentTicket = $this->tickets->getTicketById($ticketId);

            $ticketsHistoric = $participantAmount;
            if ($currentTicket->EventId == 3) {
              
                if ($ticketsHistoric >= 4) {
                    $numberOfGroups = floor($ticketsHistoric / 4);
                    
                    $ticketsHistoric -= $numberOfGroups * 4;
                    $price += $numberOfGroups * 60;
                }

                if($ticketsHistoric > 0){
                    $price += $ticketsHistoric * $currentTicket->Price;
                }
            }else{
                $price = $currentTicket->Price * $ticketsHistoric;
            }


            if($currentTicket !== null && !(gettype($currentTicket) === "boolean")){
                //get the current existing cart
                if(isset($_SESSION["selectedTickets"])) {
                    $localSelected = $_SESSION["selectedTickets"];
                }

                //create the ticket based on info from the url and the found ticket
                $newSelectedTicket = [
                     "SelectedTicketId" => sizeof($localSelected)+ 1,
                     "Price" => $price,
                     "Participants" => $participantAmount,
                     "Comment" => $comment,
                     "InvoiceId" => null,
                     "TicketId" => $currentTicket->TicketId
                ];

                $selectedIndex = $newSelectedTicket["SelectedTicketId"];

                //update the local array
                $localSelected[$selectedIndex] = json_decode(json_encode($newSelectedTicket));

                //set the session array
                $_SESSION["selectedTickets"] = $localSelected;
                //print_r($_SESSION['selectedTickets']);
                $this->shoppingCart();

            } else {
                echo "Error 404 Ticket not Found :(";
            }
        }
    }

    public function addTourToCart(){

        $data = [
            'feedbackError' => '',
            'feedback' => '',
            'activity' => ''
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            if(isset($_GET['ticketId'])){

                $activityId = $_GET['ticketId'];
                $activity = $this->activities->getHistoricTourById($activityId);

                $participantAmount = 0;


                $familyTicket = 0;
                $singleTicket = 0;

                if(isset($_POST['family'])){
                $familyTicket = $_POST['family'];}

                if(isset($_POST['single'])){
                $singleTicket = $_POST['single'];}

                if($familyTicket == 0 && $singleTicket == 0) {

                    $data['feedbackError'] = "<span class='invalid'>Please select number and then press add to cart</span>";
                    $data['activity'] = $activity;

                    $this->view('tickets/selectedTour' ,$data);
                }else if($singleTicket + ($familyTicket * 4) > $activity->capacity - $activity->booked){

                    $data['feedbackError'] = "<span class='invalid'>Not enough places left, please change your selection</span>";
                    $data['activity'] = $activity;

                    $this->view('tickets/selectedTour' ,$data);
                }else{

                    $participantAmount = 0;

                    if($singleTicket != 0){
                        $participantAmount += $singleTicket;
                    }

                    if($familyTicket != 0){
                        $participantAmount += $familyTicket * 4;
                    }

                    header("Location: " . URLROOT . "/ticket/addTicketToCart&ticketId="  .$activityId  . "&participantAmount=" . $participantAmount);
                }

            }
        }
    }
    //Toevoegen aan cart voor dance
    public function addDanceToCart(){

      $artistInfo = $this->activities->getDanceTickets();
      $data['artistInfo'] = $artistInfo;

      $ticketId = $_GET['ticketId'];
      $aantalTickets = $_GET['aantalTickets'];

      $danceTickets = $this->activities->getDanceTickets();
      //als invoer 0 of negatief is, toon error
      if ($aantalTickets <=0) {
        $data['danceTickets'] = $danceTickets;
        $data['Error'] = "<span class='errorMessage'> ERROR OCCURRED : Please put a valid number (at least 1)</span>";
        $this->view('pages/dance' ,$data);
      }
      if ($aantalTickets!=0 && $ticketId!=null) {
        // aantal tickets moet positief zijn
        if ($aantalTickets >0) {
          //Als alles goed gaat.
            header("Location: " . URLROOT . "/ticket/addTicketToCart&ticketId="  .$ticketId  . "&participantAmount=" . $aantalTickets);
          }
        }
      }
}
