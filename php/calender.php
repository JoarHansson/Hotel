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

?>

<div class="calender">

  <?php

  // Create month with 31 days and week starting on the 1st (Jan 2024)
  // The counter helps to assign the correct weekday to each date

  $counter = 0;

  for ($i = 1; $i < 32; $i++) : ?>

    <div class="calender-day
    <?php if (isset($week[$counter])) {
      echo $week[$counter];
    } else {
      $counter = 0;
      echo "Monday";
    } ?>" id="calender-day-<?= $i ?>">
      <?= $i ?>
    </div>

    <?php $counter++; ?>
  <?php endfor; ?>

</div>

<form action="php/booking.php" method="post" class="flex flex-col max-w-md my-2">
  <label for="date-from">from:</label>
  <input name="date-from" type="date" min="2024-01-01" max="2024-01-31">
  <label for="date-to">to:</label>
  <input name="date-to" type="date" min="2024-01-01" max="2024-01-31">
  <button type="submit">OK</button>
</form>