<?php

declare(strict_types=1);

session_start();

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");

require __DIR__ . "/../vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

$client = new Client();
$baseUri = "https://www.yrgopelag.se/centralbank/";


if (isset($_POST["guest-name"], $_POST["transfer-code"])) {
  $guestName = htmlspecialchars($_POST["guest-name"]);
  $transferCode = htmlspecialchars($_POST["transfer-code"]);

  $dateFrom = $_SESSION["reservedDateFrom"];
  $dateTo = $_SESSION["reservedDateTo"];

  try {
    $responseCheckTransferCode = $client->request("POST", $baseUri . "transferCode", [
      "form_params" => [
        "transferCode" => $transferCode,
        "totalcost" => "3" /* hard coded for now */
      ]
    ]);
  } catch (ClientException $e) {
    echo $e->getMessage();
  }

  $transferCodeStatus = json_decode($responseCheckTransferCode->getBody()->getContents());

  // If transfer code is valid...
  if ($transferCodeStatus->transferCode === $transferCode) {

    // save the booking info (insert into bookings table):
    $statementSaveBookingInfo = $db->prepare(
      "INSERT INTO bookings (guest_name, checkin_date, checkout_date, room_id)
      VALUES (:guestName, :dateFrom, :dateTo, 3)"  /* room hard coded for now */
    );

    $statementSaveBookingInfo->bindParam(":dateFrom", $dateFrom, PDO::PARAM_INT);
    $statementSaveBookingInfo->bindParam(":dateTo", $dateTo, PDO::PARAM_INT);
    $statementSaveBookingInfo->bindParam(":guestName", $guestName, PDO::PARAM_STR);
    $statementSaveBookingInfo->execute();

    $_SESSION["message"] = "payment succeeded";
    $_SESSION["pageState"] = "receipt";


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
  } else {

    // if transfer code isn't valid, release the reservation
    $statementMakeBooking = $db->prepare(
      "UPDATE occupancy
      SET occupied = 0
      WHERE date BETWEEN :dateFrom AND :dateTo
      AND room_id = 3" /* hard coded for now */
    );

    $statementMakeBooking->bindParam(":dateFrom", $dateFrom, PDO::PARAM_INT);
    $statementMakeBooking->bindParam(":dateTo", $dateTo, PDO::PARAM_INT);
    $statementMakeBooking->execute();

    $_SESSION["message"] = "payment failed";
    $_SESSION["pageState"] = "error";
  }
}

header("Location: /index.php");
