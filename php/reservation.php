<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

$roomChosen = $_SESSION["roomType"];

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

  $requestedDates = range($dateFrom, $dateTo, 1);

  // Get data from table reservations
  $reservations = getDataFromDb("reservations", $roomChosen);

  $reservedDates = filterDatesFromData($reservations);


  // Check room availability
  if (array_intersect($requestedDates, $reservedDates)) {
    $_SESSION["message"] = "Some of the chosen dates are not available.";
    $_SESSION["pageState"] = "error";
  } else {

    // if the room and dates are available, make the reservation

    $_SESSION["pricePerDay"] = $_POST["pricePerDay"];
    $_SESSION["numberOfDays"] = count(range($dateFrom, $dateTo, 1));
    $_SESSION["pageState"] = "extras";

    $timeOfReservation = time();

    $statementMakeReservation = $db->prepare(
      "INSERT INTO reservations (checkin_date, checkout_date, timestamp, room_id)
      VALUES (:dateFrom, :dateTo, :timestamp, :room_id)"
    );

    $statementMakeReservation->bindParam(":dateFrom", $dateFrom, PDO::PARAM_INT);
    $statementMakeReservation->bindParam(":dateTo", $dateTo, PDO::PARAM_INT);
    $statementMakeReservation->bindParam(":timestamp", $timeOfReservation, PDO::PARAM_INT);
    $statementMakeReservation->bindParam(":room_id", $roomChosen, PDO::PARAM_INT);
    $statementMakeReservation->execute();
  }
}

header("Location: /index.php");
