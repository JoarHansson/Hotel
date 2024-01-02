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

// get all extra items from db
$statementGetExtras = $db->prepare("SELECT * FROM extras");
$statementGetExtras->execute();

$extras = $statementGetExtras->fetchAll(PDO::FETCH_ASSOC);

?>


<h1 class="mb-16 text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950">Our rooms</h1>

<form action="index.php" method="post" class="mb-16 mx-auto grid max-w-md grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">

  <!-- economy: -->
  <button name="roomType" type="submit" value="1" class=" bg-orange-950 rounded-3xl p-8 shadow-orange-950/25 shadow-xl">
    <div class="flex items-center justify-between gap-x-4 bg-orange-400 -m-8 mb-8 p-8 rounded-t-2xl">
      <h3 class="text-xl font-extrabold leading-8 text-orange-950 <?= ($roomChosen === 1) ? "underline underline-offset-8 decoration-4" : "" ?>">Economy</h3>
      <p class="rounded-full bg-orange-950 px-6 py-2 text-sm font-bold leading-5 text-orange-400 shadow-orange-950/25 shadow-xl">$<?= getRoomPrice("economy") /* see functions.php */ ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extras as $extraItem) :
        if ($counter < 0) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-emerald-600 stroke-orange-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="M 9 12.75 L 11.25 15 M 11.25 15 L 15 9.75 M 21 12 A 9 9 0 1 1 3 12 A 9 9 0 0 1 21 12 Z" />
            </svg>
            <p class="text-orange-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-rose-600 stroke-orange-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="text-orange-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>

  <!-- standard: -->
  <button name="roomType" type="submit" value="2" class=" bg-purple-950 rounded-3xl p-8 shadow-purple-950/25 shadow-xl">
    <div class="flex items-center justify-between gap-x-4 bg-purple-400 -m-8 mb-8 p-8 rounded-t-2xl">
      <h3 class="text-xl font-extrabold leading-8 text-purple-950 <?= ($roomChosen === 2) ? "underline underline-offset-8 decoration-4" : "" ?>">Standard</h3>
      <p class="rounded-full bg-purple-950 px-4 py-2 text-sm font-bold leading-5 text-purple-400 shadow-purple-950/25 shadow-xl">$ <?= getRoomPrice("standard") ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extras as $extraItem) :
        if ($counter < 3) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-emerald-600 stroke-purple-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="M 9 12.75 L 11.25 15 M 11.25 15 L 15 9.75 M 21 12 A 9 9 0 1 1 3 12 A 9 9 0 0 1 21 12 Z" />
            </svg>
            <p class="text-purple-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-rose-600 stroke-purple-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="text-purple-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>

  <!-- deluxe: -->
  <button name="roomType" type="submit" value="3" class=" bg-yellow-950 rounded-3xl p-8 shadow-yellow-950/50 shadow-xl">
    <div class="flex items-center justify-between gap-x-4 bg-yellow-400 -m-8 mb-8 p-8 rounded-t-2xl">
      <h3 class="text-xl font-extrabold leading-8 text-yellow-950 <?= ($roomChosen === 3) ? "underline underline-offset-8 decoration-4" : "" ?>">Deluxe</h3>
      <p class="rounded-full bg-yellow-950 px-6 py-2 text-sm font-bold leading-5 text-yellow-400 shadow-yellow-950/25 shadow-xl">$ <?= getRoomPrice("deluxe") ?> / day</p>
    </div>

    <ul role="list" class="mt-6 space-y-3 text-sm leading-6 xl:mt-8">

      <?php $counter = 0;
      foreach ($extras as $extraItem) :
        if ($counter < 5) : ?>
          <li class="flex gap-x-3 items-center">
            <!-- included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-emerald-600 stroke-yellow-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="M 9 12.75 L 11.25 15 M 11.25 15 L 15 9.75 M 21 12 A 9 9 0 1 1 3 12 A 9 9 0 0 1 21 12 Z" />
            </svg>
            <p class="text-yellow-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php else : ?>
          <li class="flex gap-x-3 items-center">
            <!-- not included: -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 fill-rose-600 stroke-yellow-950">
              <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="text-yellow-100"><?php echo $extraItem["name"]; ?></p>
          </li>
        <?php endif; ?>
      <?php $counter++;
      endforeach; ?>

    </ul>
  </button>
</form>


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
