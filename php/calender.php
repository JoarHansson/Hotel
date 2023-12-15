<?php

declare(strict_types=1);

$week = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday"
];


$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");

// get reservations (occupancy) from db to be able to show availability in the calender UI
$statementCheckReservations = $db->prepare(
  "SELECT * FROM occupancy
  WHERE occupied = true AND room_id = 3" /* room_id hard coded for now */
);
$statementCheckReservations->execute();
$reservations = $statementCheckReservations->fetchAll(PDO::FETCH_ASSOC);

$reservedDates = array_column($reservations, "date");

// also get bookings from db to be able to show that in the calender UI as well
$statementCheckBookings = $db->prepare(
  "SELECT * FROM bookings
  WHERE room_id = 3" /* room_id hard coded for now */
);
$statementCheckBookings->execute();
$bookings = $statementCheckBookings->fetchAll(PDO::FETCH_ASSOC);

$bookedDates = [];
foreach ($bookings as $booking) {
  $bookedDates[] = range($booking["checkin_date"], $booking["checkout_date"], 1);
}

// flatten array bookedDates:
$bookedDates = array_merge(...$bookedDates);

?>

<p id="instruction-text" class="mb-4">Choose a start date</p>

<div class="calender mb-4">

  <?php

  // Create month with 31 days and week starting on the 1st (Jan 2024)
  // The counter helps to assign the correct weekday to each date

  $counter = 0;

  for ($i = 1; $i < 32; $i++) :

    $isReserved = in_array($i, $reservedDates);
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

  <button id="button-submit-form" class="bg-emerald-600 col-span-2">OK</button>
  <button id="button-clear-selection" class="bg-rose-600 col-span-2">CLEAR</button>

</div>

<!-- The hidden form is submitted in calender.js -->
<form action="php/booking.php" method="post" id="form-make-booking" class="hidden">
  <input name="date-from" id="date-from" type="date" min="2024-01-01" max="2024-01-31">
  <input name="date-to" id="date-to" type="date" min="2024-01-01" max="2024-01-31">
</form>

<script src="/js/calender.js"></script>