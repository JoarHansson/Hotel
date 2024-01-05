<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

session_start();

if (empty($_SESSION["reservation"])) {
  $_SESSION["reservation"]["island"] = "The Island";
  $_SESSION["reservation"]["hotel"] = "The Ice Hotel";
  $_SESSION["reservation"]["stars"] = "3";
  // currently:
  // - graphical presentation of the availability
  // - features
  // - discounts?
}

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");
