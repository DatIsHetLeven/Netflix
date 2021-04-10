<?php
require_once APPROOT . "/models/EventModel.php";

$eventModel = new EventModel();
$events = $eventModel->getAllEvents();
?>

<nav>
  <section class="wrapper">
    <ul>
      <li><a>test</li>
    </ul>
  </section>
</nav>
