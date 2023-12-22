<?php

declare(strict_types=1);

session_start();

if (empty($_SESSION["reservation"])) {
  $_SESSION["reservation"]["island"] = "The Island";
  $_SESSION["reservation"]["hotel"] = "The Hotel";
}

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");
