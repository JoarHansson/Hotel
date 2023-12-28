<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

$week = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday"
];


$roomChosen = 3; // hard coded for now.

// Get info on the chosen room:
$statementGetRoomInfo = $db->prepare(
  "SELECT name, base_price FROM rooms
  WHERE id = :id"
);
$statementGetRoomInfo->bindParam(":id", $roomChosen, PDO::PARAM_INT);
$statementGetRoomInfo->execute();
$roomInfo = $statementGetRoomInfo->fetch(PDO::FETCH_ASSOC);


// Get data from table reservations
$reservations = getDataFromDb("reservations", $roomChosen);

$reservedDates = filterDatesFromData($reservations);

// Get data from table bookings
$bookings = getDataFromDb("bookings", $roomChosen);

$bookedDates = filterDatesFromData($bookings);


// filter out reservations that doesn't have a matching booking
$filteredReservations = array_filter($reservations, function ($r) use ($bookedDates) {

  $range = range($r["checkin_date"], $r["checkout_date"], 1);

  if (array_diff($range, $bookedDates)) {
    return true;
  }
});


$timeOfPageLoad = time();
$tenMinutesAgo =  $timeOfPageLoad - 600; // a reservation is hold for 10 minutes

// delete old reservations that don't have a matching booking
foreach ($filteredReservations as $filteredItem) {
  $statementDeleteOldReservations = $db->prepare(
    "DELETE FROM reservations WHERE timestamp < :tenMinutesAgo AND id = :id"
  );
  $statementDeleteOldReservations->bindParam(":tenMinutesAgo", $tenMinutesAgo, PDO::PARAM_INT);
  $statementDeleteOldReservations->bindParam(":id", $filteredItem["id"], PDO::PARAM_INT);
  $statementDeleteOldReservations->execute();
}

// select reservations again after clearing old ones
$reservationsUpdated = getDataFromDb("reservations", $roomChosen);

$reservedDatesUpdated = filterDatesFromData($reservationsUpdated);

?>

<p id="room-price-info" class="mb-4">
  chosen room: <?php echo $roomInfo["name"] ?>
  <br>
  price per day:
  <span id="price-per-day"><?php echo $roomInfo["base_price"] ?></span>
</p>

<p id="instruction-text" class="mb-4">Choose a start date</p>

<p class="mb-4">total price: <span id="total-price">0</span></p>

<div class="calender mb-4">

  <?php

  // Create month with 31 days and week starting on the 1st (Jan 2024)
  // The counter helps to assign the correct weekday to each date

  $counter = 0;

  for ($i = 1; $i < 32; $i++) :

    $isReserved = in_array($i, $reservedDatesUpdated);
    $isBooked = in_array($i, $bookedDates);

    $buttonClass = $isBooked ? "bg-slate-950 cursor-not-allowed"
      : ($isReserved  ? "bg-slate-900 cursor-not-allowed" : "bg-slate-600");

    $disabledStatus = $isReserved ? "disabled" : "";

    $weekday;

    if (isset($week[$counter])) {
      $weekday = $week[$counter];
    } else {
      $weekday = "Monday";
      $counter = 0;
    }

    $counter++; ?>

    <button <?= $disabledStatus ?> value="<?= $i ?>" id="calender-day-<?= $i ?>" class="calender-day bg-s <?= "{$weekday} {$buttonClass}"; ?>">
      <?= $i ?>
    </button>

  <?php endfor; ?>

  <button id="button-submit-form" class="bg-emerald-600 col-span-2">Continue</button>
  <button id="button-clear-selection" class="bg-rose-600 col-span-2">Clear selection</button>

</div>

<!-- The hidden form is submitted in calender.js -->
<form action="php/reservation.php" method="post" id="form-make-reservation" class="hidden">
  <input name="date-from" id="date-from" type="date" min="2024-01-01" max="2024-01-31">
  <input name="date-to" id="date-to" type="date" min="2024-01-01" max="2024-01-31">

  <input name="pricePerDay" type="text" value="<?php echo $roomInfo["base_price"]  ?>">
</form>

<script src="/js/calender.js"></script>