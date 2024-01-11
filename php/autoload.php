<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

session_start();

date_default_timezone_set("Europe/Stockholm");

if (empty($_SESSION["reservation"])) {
  $_SESSION["reservation"]["island"] = "Mount Frost";
  $_SESSION["reservation"]["hotel"] = "The Ice Hotel";
  $_SESSION["reservation"]["stars"] = "5";
}

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");

// The following value for $baseUrl is used when testing the app in a local environment.
$baseUrl = "/";

// When the app is deployed the following value is used instead, or otherwise filepaths won't work correctly.
// $baseUrl = "https://php-fanclub.se/the-ice-hotel/";
