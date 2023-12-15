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

// get occupancy from db to be able to show availability in the calender UI
$statementCheckOccupancy = $db->prepare(
  "SELECT * FROM occupancy
  WHERE occupied = true AND room_id = 3" /* room_id hard coded for now */
);
$statementCheckOccupancy->execute();
$occupancy = $statementCheckOccupancy->fetchAll(PDO::FETCH_ASSOC);

$occupiedDates = array_column($occupancy, "date");

?>

<p id="instruction-text" class="mb-4">Choose a start date</p>

<div class="calender mb-4">

  <?php

  // Create month with 31 days and week starting on the 1st (Jan 2024)
  // The counter helps to assign the correct weekday to each date

  $counter = 0;

  for ($i = 1; $i < 32; $i++) : ?>

    <button value="<?= $i ?>" class="calender-day
    <?php

    if (in_array($i, $occupiedDates)) {
      echo "bg-slate-900 cursor-not-allowed ";
    } else {
      echo "bg-slate-600 ";
    }

    if (isset($week[$counter])) {
      echo $week[$counter];
    } else {
      $counter = 0;
      echo "Monday";
    }

    ?>" id="calender-day-<?= $i ?>">
      <?= $i ?>
    </button>

    <?php $counter++; ?>
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