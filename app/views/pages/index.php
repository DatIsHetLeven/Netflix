<?php
require APPROOT . '/views/includes/head.php';
require APPROOT . '/views/includes/navigation.php';

?>
<main id="index">
<section id="indexInfo">
  <section id="indexButtonNav">
    <button>Inloggen</button>
    <button>Nederlands</button>
  </section>
  <section id="content">
    <h1>Onbeperkt series, films en meer kijken.</h1>
    <h2>Kijk waar je wilt. Altijd opzegbaar. </h2>
    <p><h3>Klaar om te kijken? Voer je e-mailadres in om je lidmaatschap te starten of te hernieuwen.</h3>
    <input type="email" placeholder="E-mailadres"></input>
    <button>Aan de slag ></button>
  </section>
</section>

<section id="content2">
  <h1>Kijk op je tv.</h1>
  <h2>Kijk op smart-tv's, PlayStation, Xbox, Chromecast, Apple TV, blu-rayspelers en meer.</h2>
  <img src="<?php echo URLROOT ?>/public/img/kijkTv.jpg">
</section>

<section id="content2">
  <h1>Kijk overal</h1>
  <h2>Stream onbeperkt series en films op je telefoon, tablet, laptop en tv, zonder meer te betalen.</h2>
  <img alt="" src="https://assets.nflxext.com/ffe/siteui/acquisition/ourStory/fuji/desktop/device-pile.png">
</section>

<section id="content3">
  <span><h1>Veelgestelde vragen</h1>
  <button>Wat kan ik kijken op Netflix</button>
  <button>Wat is Netflix </button>
  <button>Hoeveel kost Netflix?</button>
  <button>Waar kan ik kijken?</button>
  <button>Hoe kan ik opzeggen?</button>
  <h3>Klaar om te kijken? Voer je e-mailadres in om je lidmaatschap te starten of te hernieuwen.</h3>
  <input type="email" placeholder="E-mailadres"></input>
  <button id="aanDeSlag">Aan de slag ></button>
</section>

<?php
 require APPROOT . '/views/includes/footer.php';
