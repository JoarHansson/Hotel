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

if (isset($_POST["roomType"])) {
  $_SESSION["roomType"] = intval($_POST["roomType"]);
} else {
  $_SESSION["roomType"] = 3; // === deluxe, the default choice
}

$roomChosen = $_SESSION["roomType"];

// Get info on the chosen room:
$statementGetRoomInfo = $db->prepare(
  "SELECT name, base_price FROM rooms
  WHERE id = :id"
);
$statementGetRoomInfo->bindParam(":id", $roomChosen, PDO::PARAM_INT);
$statementGetRoomInfo->execute();
$roomInfo = $statementGetRoomInfo->fetch(PDO::FETCH_ASSOC);

$_SESSION["pricePerDay"] = $roomInfo["base_price"];

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
$tenMinutesAgo = $timeOfPageLoad - 600; // a reservation is hold for 10 minutes

// delete old reservations that don't have a matching booking
foreach ($filteredReservations as $filteredItem) {
  $statementDeleteOldReservations = $db->prepare(
    "DELETE FROM reservations WHERE timestamp < :tenMinutesAgo AND id=:id"
  );
  $statementDeleteOldReservations->bindParam(":tenMinutesAgo", $tenMinutesAgo, PDO::PARAM_INT);
  $statementDeleteOldReservations->bindParam(":id", $filteredItem["id"], PDO::PARAM_INT);
  $statementDeleteOldReservations->execute();
}

// select reservations again after clearing old ones
$reservationsUpdated = getDataFromDb("reservations", $roomChosen);

$reservedDatesUpdated = filterDatesFromData($reservationsUpdated);

// get all extra items from db
$statementGetExtras = $db->prepare("SELECT * FROM extras");
$statementGetExtras->execute();

$extras = $statementGetExtras->fetchAll(PDO::FETCH_ASSOC);

// include first five items for advertising:
$extrasForAdvertising = array_slice($extras, 0, 5);

?>


<h1 class="mb-8 text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950 underline underline-offset-8 decoration-8">The rooms</h1>

<form action="index.php" method="post" class="mb-16 mx-auto grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">

  <!-- economy: -->
  <button name="roomType" type="submit" value="1" class=" bg-blue-950 rounded-3xl p-8 shadow-blue-950/25 shadow-xl hover:bg-blue-900 <?= ($roomChosen === 1) ? "ring-blue-400 ring-8" : "" ?>">
    <div class="flex items-center justify-between gap-x-4 bg-blue-400 -m-8 mb-8 p-8 rounded-t-2xl">
      <h3 class="text-xl font-extrabold leading-8 text-blue-950 <?= ($roomChosen === 1) ? "underline underline-offset-8 decoration-4" : "" ?>">Economy</h3>
      <p class="rounded-full bg-blue-950 px-6 py-2 text-sm font-bold leading-5 text-blue-400 shadow-blue-950/25 shadow-xl">$<?= getRoomPrice("economy") /* see functions.php */ ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extrasForAdvertising as $extraItem) :
        if ($counter < 1) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-emerald-600 stroke-emerald-600">
              <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
            </svg>
            <p class="text-blue-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-rose-600 stroke-rose-600">
              <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
            <p class="text-blue-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>

  <!-- standard: -->
  <button name="roomType" type="submit" value="2" class=" bg-purple-950 rounded-3xl p-8 shadow-purple-950/25 shadow-xl hover:bg-purple-900 <?= ($roomChosen === 2) ? "ring-purple-400 ring-8" : "" ?>">
    <div class="flex items-center justify-between gap-x-4 bg-purple-400 -m-8 mb-8 p-8 rounded-t-2xl">
      <h3 class="text-xl font-extrabold leading-8 text-purple-950 <?= ($roomChosen === 2) ? "underline underline-offset-8 decoration-4" : "" ?>">Standard</h3>
      <p class="rounded-full bg-purple-950 px-4 py-2 text-sm font-bold leading-5 text-purple-400 shadow-purple-950/25 shadow-xl">$ <?= getRoomPrice("standard") ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extrasForAdvertising as $extraItem) :
        if ($counter < 3) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-emerald-600 stroke-emerald-600">
              <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
            </svg>
            <p class="text-purple-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-rose-600 stroke-rose-600">
              <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
            <p class="text-purple-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>

  <!-- deluxe: -->
  <button name="roomType" type="submit" value="3" class=" bg-yellow-950 rounded-3xl p-8 shadow-yellow-950/25 shadow-xl hover:bg-yellow-900 <?= ($roomChosen === 3) ? "ring-yellow-400 ring-8" : "" ?>">
    <div class="flex items-center justify-between gap-x-4 bg-yellow-400 -m-8 mb-8 p-8 rounded-t-2xl ">
      <h3 class="text-xl font-extrabold leading-8 text-yellow-950 <?= ($roomChosen === 3) ? "underline underline-offset-8 decoration-4" : "" ?>">Deluxe</h3>
      <p class="rounded-full bg-yellow-950 px-6 py-2 text-sm font-bold leading-5 text-yellow-400 shadow-yellow-950/25 shadow-xl">$ <?= getRoomPrice("deluxe") ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extrasForAdvertising as $extraItem) :
        if ($counter < 5) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-emerald-600 stroke-emerald-600">
              <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
            </svg>
            <p class="text-yellow-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 fill-rose-600 stroke-rose-600">
              <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
            <p class="text-yellow-400"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>
</form>


<!-- calender container: -->
<div id="calender" class="bg-cyan-50 p-4 lg:p-8 mx-auto flex flex-col lg:flex-row justify-between gap-10 lg:gap-20 max-w-md lg:max-w-5xl rounded-3xl shadow-cyan-50/25 shadow-xl">

  <div class="w-full max-w-xl">
    <h2 id="instruction-text" class="mb-8 text-center text-2xl font-extrabold leading-relaxed">Choose a start date</h2>
    <div class="mb-8 mx-auto grid gap-1 grid-cols-7 w-full">
      <div class="text-center text-base font-bold">Mon</div>
      <div class="text-center text-base font-bold">Tue</div>
      <div class="text-center text-base font-bold">Wed</div>
      <div class="text-center text-base font-bold">Thu</div>
      <div class="text-center text-base font-bold">Fri</div>
      <div class="text-center text-base font-bold">Sat</div>
      <div class="text-center text-base font-bold">Sun</div>

      <?php
      // Create month with 31 days and week starting on the 1st (Jan 2024)
      // The counter helps to assign the correct weekday to each date
      $counter = 0;
      for ($i = 1; $i < 32; $i++) :
        $isReserved = in_array($i, $reservedDatesUpdated);
        $isBooked = in_array($i, $bookedDates);
        if ($roomChosen === 1) {
          $buttonClass = $isBooked ? "bg-blue-950 cursor-not-allowed"
            : ($isReserved  ? "bg-blue-800 cursor-not-allowed" : "bg-blue-600");
        } else if ($roomChosen === 2) {
          $buttonClass = $isBooked ? "bg-purple-950 cursor-not-allowed"
            : ($isReserved  ? "bg-purple-800 cursor-not-allowed" : "bg-purple-600");
        } else if ($roomChosen === 3) {
          $buttonClass = $isBooked ? "bg-yellow-950 cursor-not-allowed"
            : ($isReserved  ? "bg-yellow-800 cursor-not-allowed" : "bg-yellow-600");
        }
        $disabledStatus = $isReserved ? "disabled" : "";

        $weekday;
        if (isset($week[$counter])) {
          $weekday = $week[$counter];
        } else {
          $weekday = "Monday";
          $counter = 0;
        }
        $counter++; ?>
        <button <?= $disabledStatus ?> value="<?= $i ?>" id="calender-day-<?= $i ?>" class="calender-day py-4 text-base font-bold rounded-sm text-cyan-50 <?= "{$weekday} {$buttonClass}"; ?>">
          <?= $i ?>
        </button>
      <?php endfor; ?>

    </div>
  </div>

  <!-- info about chosen room and price, etc -->
  <div class="flex flex-col gap-16 justify-between items-center mx-auto lg:items-end w-max">

    <ul class="text-sm lg:text-base font-bold leading-loose">
      <li class="text-2xl font-extrabold mb-4  w-max">Chosen room: <span class="capitalize"><?php echo $roomInfo["name"] ?></span></li>
      <li>Price per day: $<span id="price-per-day"><?php echo $roomInfo["base_price"] ?></span></li>
      <li>Arrival date: <span id="arrival-date"></span></li>
      <li>Departure date: <span id="departure-date"></span></li>
      <li class="text-2xl bg-cyan-950 text-cyan-50 p-2 my-4">Your total: $<span class="text-cyan-50" id="total-price">0</span></li>
      <li class="flex items-center gap-2">
        <span class="w-4 h-4 <?= $roomChosen === 1 ? "bg-blue-600" : ($roomChosen === 2 ? "bg-purple-600" : "bg-yellow-600") ?>"></span>
        <span>- Available</span>
      </li>
      <li class="flex items-center gap-2">
        <span class="w-4 h-4 <?= $roomChosen === 1 ? "bg-blue-800" : ($roomChosen === 2 ? "bg-purple-800" : "bg-yellow-800") ?>"></span>
        <span>- Reserved (check back soon)</span>
      </li>
      <li class="flex items-center gap-2">
        <span class="w-4 h-4 <?= $roomChosen === 1 ? "bg-blue-950" : ($roomChosen === 2 ? "bg-purple-950" : "bg-yellow-950") ?>">"></span>
        <span>- Already booked</span>
      </li>
    </ul>

    <div class="flex flex-col justify-center gap-4 mb-8 lg:mb-0 mx-auto">
      <button id="button-submit-form" class="button-green">Continue</button>
      <button id="button-clear-selection" class="button-red">Clear selection</button>
    </div>

  </div>

</div>

<!-- The hidden form is submitted in calender.js -->
<form action="php/reservation.php" method="post" id="form-make-reservation" class="hidden">
  <input name="date-from" id="date-from" type="date" min="2024-01-01" max="2024-01-31">
  <input name="date-to" id="date-to" type="date" min="2024-01-01" max="2024-01-31">
</form>

<script src="js/calender.js"></script>
