<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

session_start();

date_default_timezone_set("Europe/Stockholm");

if (empty($_SESSION["reservation"])) {
  $_SESSION["reservation"]["island"] = "Mount Frost";
  $_SESSION["reservation"]["hotel"] = "The Ice Hotel";
  $_SESSION["reservation"]["stars"] = "5";
  // currently:
  // - graphical presentation of the availability
  // - features
  // - discounts
  // - admin page
}

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");
