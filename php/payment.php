<?php

declare(strict_types=1);

session_start();

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");

if (isset($_POST["guest-name"], $_POST["transfer-code"])) {
  $guestName = htmlspecialchars($_POST["guest-name"]);
  $transferCode = htmlspecialchars($_POST["transfer-code"]);

  $dateFrom = $_SESSION["reservedDateFrom"];
  $dateTo = $_SESSION["reservedDateTo"];

  if (1 == 1) {  // placeholder for: if transfer code is valid...

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
  }
}

header("Location: /index.php");
