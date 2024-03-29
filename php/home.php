<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

$roomChosen = $_SESSION["roomType"];

// if the home button or cancel button is pressed throughout the booking process,
// the reservation is cleared and the user can start the booking process over.
if (isset($_SESSION["reservedDateFrom"], $_SESSION["reservedDateTo"])) {

  $reservedDateFrom = $_SESSION["reservedDateFrom"];
  $reservedDateTo = $_SESSION["reservedDateTo"];

  // get reservation with dates from the session
  $statementGetReservation = $db->prepare(
    "SELECT * FROM reservations
    WHERE room_id = :roomId
    AND checkin_date = :reservedDateFrom
    AND checkout_date = :reservedDateTo"
  );

  $statementGetReservation->bindParam(":roomId", $roomChosen, PDO::PARAM_INT);
  $statementGetReservation->bindParam(":reservedDateFrom", $reservedDateFrom, PDO::PARAM_INT);
  $statementGetReservation->bindParam(":reservedDateTo", $reservedDateTo, PDO::PARAM_INT);
  $statementGetReservation->execute();
  $reservation = $statementGetReservation->fetch(PDO::FETCH_ASSOC);


  // get booking (if there is one) with dates from the session
  $statementGetBooking = $db->prepare(
    "SELECT * FROM bookings
    WHERE room_id = :roomId
    AND checkin_date = :reservedDateFrom
    AND checkout_date = :reservedDateTo"
  );

  $statementGetBooking->bindParam(":roomId", $roomChosen, PDO::PARAM_INT);
  $statementGetBooking->bindParam(":reservedDateFrom", $reservedDateFrom, PDO::PARAM_INT);
  $statementGetBooking->bindParam(":reservedDateTo", $reservedDateTo, PDO::PARAM_INT);
  $statementGetBooking->execute();
  $booking = $statementGetBooking->fetch(PDO::FETCH_ASSOC);


  if (!isset($booking["checkin_date"], $booking["checkout_date"])) {
    // clear reservation from db
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
  }

  // clear the session values:
  unset($_SESSION["reservation"]);
  unset($_SESSION["pricePerDay"]);
  unset($_SESSION["numberOfDays"]);
  unset($_SESSION["reservedDateFrom"]);
  unset($_SESSION["reservedDateTo"]);
  unset($_SESSION["message"]);
}

?>

<h1 class="mb-8 mt-32 lg:mt-64  text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950 underline underline-offset-8 decoration-8">Welcome to The Ice Hotel</h1>

<form action="index.php" method="post" class="w-full text-center">
  <input name="pageState" type="text" value="calender" hidden>
  <button type="submit" class="button-cyan">Book a room</button>
</form>
