<?php

// Hier wordt de url geontorleerd en bepaald
class Url {
    // deze wordt uatomatisch opgeladen als de contoller wordt niet bepaald
    protected $currentController = "PagesController";
    protected $currentMethod = "index";
    protected $parameters = [];

    public function __construct() {
        $url = $this->getUrl();

        if ($url === null) $url = ["pages", "index"];
        if (count($url) >= 2) $methodName = $this->popLast($url);

        $className = ucwords($this->popLast($url)) . "Controller";

        if (isset($url)) {
            // gaan in de controllers map kijken of de gevraggde contoller bestaat. De contoller moet altijd met hooftletter beginnen
            $sectionsUppercase = array_map(function ($section) { return ucwords($section); }, $url);
            $controllerName = join("/", $sectionsUppercase) . "/" . $className;

            if (file_exists('../app/controllers/' . $controllerName . '.php')) {
                // als de controller bestaat, wordt de huidige controller veranderd
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // een object van de gevraagde controller maken
        require_once '../app/controllers/' . $this->currentController . '.php';
        $this->currentController = new $className;

        // Gaan kijken of de method bestaat en run het anders is altijd de index pagina
        if (isset($methodName)) {
            if (method_exists($this->currentController, $methodName)) {
                $this->currentMethod = $methodName;
                unset($url[1]);
            }
        }

        // Nu gaan we kijken of er parameters zijn en geven die terug anders een lege array
        $this->parameters = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], $this->parameters);
    }

    // omdat url uit controller index[0], method index[1] en parameters(optioneel) index[2] bestaat, moeten we een array van de url maken
    public function getUrl() {
        // super golbal GET[''] gaat kijken wat url is als het niet bepaald is dan gaat hij naar pages/index

        if (isset($_GET['url'])) {
            // Verwijder de forward slash
            $url = rtrim($_GET['url'], '/');

            // Verwijder speciale characaters van de url
            $url = filter_var($url, FILTER_SANITIZE_URL);

            // een array van url maken
            $url = explode('/', $url);
            return $url;
        }
    }

    private function popLast(&$array) {
        $lastIndex = count($array) - 1;
        $value = $array[$lastIndex];
        unset($array[$lastIndex]);

        return $value;
    }
}
