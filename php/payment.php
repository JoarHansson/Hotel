<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

require __DIR__ . "/../vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

$client = new Client();
$baseUri = "https://www.yrgopelag.se/centralbank/";

$roomChosen = $_SESSION["roomType"];

if (isset($_POST["guest-name"], $_POST["transfer-code"])) {
  $guestName = htmlspecialchars($_POST["guest-name"]);
  $transferCode = htmlspecialchars($_POST["transfer-code"]);

  $reservedDateFrom = $_SESSION["reservedDateFrom"];
  $reservedDateTo = $_SESSION["reservedDateTo"];
  $totalCost = $_SESSION["reservation"]["total_cost"];

  try {
    $responseCheckTransferCode = $client->request("POST", $baseUri . "transferCode", [
      "form_params" => [
        "transferCode" => $transferCode,
        "totalcost" => $totalCost
      ]
    ]);
  } catch (ClientException $e) {
    echo $e->getMessage();
  }

  $transferCodeStatus = json_decode($responseCheckTransferCode->getBody()->getContents());

  // If transfer code is valid...
  // if (1 === 1) { // used for testing instead of line below
  if ($transferCodeStatus->transferCode === $transferCode) {

    // save the booking info (insert into bookings table):
    $statementSaveBookingInfo = $db->prepare(
      "INSERT INTO bookings (guest_name, checkin_date, checkout_date, room_id)
      VALUES (:guestName, :reservedDateFrom, :reservedDateTo, :room_id)"
    );

    $statementSaveBookingInfo->bindParam(":reservedDateFrom", $reservedDateFrom, PDO::PARAM_INT);
    $statementSaveBookingInfo->bindParam(":reservedDateTo", $reservedDateTo, PDO::PARAM_INT);
    $statementSaveBookingInfo->bindParam(":guestName", $guestName, PDO::PARAM_STR);
    $statementSaveBookingInfo->bindParam(":room_id", $roomChosen, PDO::PARAM_INT);
    $statementSaveBookingInfo->execute();

    // get booking id:
    $statementGetBookingId = $db->prepare(
      "SELECT id FROM bookings
      WHERE checkin_date = :reservedDateFrom AND
      checkout_date = :reservedDateTo AND
      guest_name = :guestName"
    );
    $statementGetBookingId->bindParam(":reservedDateFrom", $reservedDateFrom, PDO::PARAM_INT);
    $statementGetBookingId->bindParam(":reservedDateTo", $reservedDateTo, PDO::PARAM_INT);
    $statementGetBookingId->bindParam(":guestName", $guestName, PDO::PARAM_STR);
    $statementGetBookingId->execute();

    $bookingId = $statementGetBookingId->fetch(PDO::FETCH_ASSOC);

    // save the extras info for the booking (insert into bookings_extras table):
    foreach ($_SESSION["reservation"]["features"] as $feature) {
      $statementSaveExtrasInfo = $db->prepare(
        "INSERT INTO bookings_extras (bookings_id, extras_id)
        VALUES (:bookingId, :extrasId)"
      );

      $statementSaveExtrasInfo->bindParam(":bookingId", $bookingId, PDO::PARAM_INT);
      $statementSaveExtrasInfo->bindParam(":extrasId", $feature["id"], PDO::PARAM_INT);
      $statementSaveExtrasInfo->execute();
    }

    // deposit the tc to my account
    try {
      $responseDepositTransferCode = $client->request("POST", $baseUri . "deposit", [
        "form_params" => [
          "user" => "Joar",
          "transferCode" => $transferCode
        ]
      ]);
    } catch (ClientException $e) {
      echo $e->getMessage();
    }

    $_SESSION["message"] = "Payment succeeded";
    $_SESSION["pageState"] = "success";
  } else {

    // if transfer code isn't valid, clear the reservation from db

    $statementDeleteReservation = $db->prepare(
      "DELETE FROM reservations
          WHERE room_id = :roomId
          AND checkin_date = :reservedDateFrom
          AND checkout_date = :reservedDateTo"
    );
    $statementDeleteReservation->bindParam(":roomId", $roomChosen, PDO::PARAM_INT);
    $statementDeleteReservation->bindParam(":reservedDateFrom", $reservedDateFrom, PDO::PARAM_INT);
    $statementDeleteReservation->bindParam(":reservedDateTo", $reservedDateTo, PDO::PARAM_INT);
    $statementDeleteReservation->execute();


    $_SESSION["message"] = "Payment failed";
    $_SESSION["pageState"] = "error";
  }
}

header("Location: " . $baseUrl . "index.php");
