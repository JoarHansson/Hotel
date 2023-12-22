<?php

declare(strict_types=1);

session_start();

if (isset($_POST["date-from"], $_POST["date-to"])) {

  $dateFrom = htmlspecialchars($_POST["date-from"]);
  $dateTo = htmlspecialchars($_POST["date-to"]);

  $_SESSION["reservation"]["arrival_date"] = $dateFrom; // format: YYYY-MM-DD
  $_SESSION["reservation"]["departure_date"] = $dateTo; // format: YYYY-MM-DD

  // Use only the day:
  $dateFrom = substr($dateFrom, -2);
  $dateTo = substr($dateTo, -2);

  // convert from str to int:
  $dateFrom = intval($dateFrom);
  $dateTo = intval($dateTo);

  $_SESSION["reservedDateFrom"] = $dateFrom; // format:  D / DD
  $_SESSION["reservedDateTo"] = $dateTo; // format: D / DD

  // initialize db and get the chosen room and dates
  $db = new PDO("sqlite:../database/hotel.db");

  $statementCheckAvailability = $db->prepare(
    "SELECT * FROM occupancy
    WHERE date BETWEEN :dateFrom AND :dateTo
    AND room_id = 3" /* hard coded for now */
  );

  $statementCheckAvailability->bindParam(":dateFrom", $dateFrom, PDO::PARAM_INT);
  $statementCheckAvailability->bindParam(":dateTo", $dateTo, PDO::PARAM_INT);
  $statementCheckAvailability->execute();

  $chosenDatesAndRoom = $statementCheckAvailability->fetchAll(PDO::FETCH_ASSOC);


  // Check room availability
  $roomOccupancy = [];
  foreach ($chosenDatesAndRoom as $date) {
    $roomOccupancy[] = $date["occupied"]; // 1 or 0
  }

  if (in_array(true, $roomOccupancy)) {
    $_SESSION["message"] = "one or more dates are already fully booked.";
    $_SESSION["pageState"] = "error";
  } else {

    // if the room is available, make the reservation (update occupancy table):
    $statementMakeReservation = $db->prepare(
      "UPDATE occupancy
      SET occupied = 1
      WHERE date BETWEEN :dateFrom AND :dateTo
      AND room_id = 3" /* hard coded for now */
    );

    $statementMakeReservation->bindParam(":dateFrom", $dateFrom, PDO::PARAM_INT);
    $statementMakeReservation->bindParam(":dateTo", $dateTo, PDO::PARAM_INT);
    $statementMakeReservation->execute();

    $_SESSION["message"] = "Choose extras";
    $_SESSION["pricePerDay"] = $_POST["pricePerDay"];
    $_SESSION["numberOfDays"] = count(range($dateFrom, $dateTo, 1));
    $_SESSION["pageState"] = "extras";
  }
}

header("Location: /index.php");
