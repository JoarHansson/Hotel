<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

$_SESSION["reservation"]["features"] = []; // clear features just in case

if (isset($_POST)) {
  foreach ($_POST as $key => $extraItem) {
    $item = htmlspecialchars($extraItem);

    if (str_contains($key, "extra")) {
      $itemExploded = explode("_$", $item);
      $itemAssoc = ["name" => $itemExploded[0], "cost" => $itemExploded[1]];

      $_SESSION["reservation"]["features"][] = $itemAssoc;
    }
  }
}

$costChosenFeatures = 0;
foreach ($_SESSION["reservation"]["features"] as $feature) {
  $costChosenFeatures += $feature["cost"];
}

$_SESSION["reservation"]["total_cost"] = strval(($_SESSION["numberOfDays"] * $_SESSION["pricePerDay"]) + $costChosenFeatures);

?>

<h1 class="mb-8 text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950 underline underline-offset-8 decoration-8">Confirm your booking</h1>

<div id="confirm-booking-container" class="bg-cyan-50 px-4 py-8 lg:p-8 mx-auto grid max-w-md lg:max-w-3xl rounded-3xl shadow-cyan-50/25 shadow-xl">
  <div class="flex flex-col lg:flex-row gap-8 justify-between items-stretch">
    <!-- order details: -->
    <div class="mx-auto lg:mr-auto lg:ml-0 flex flex-col justify-between gap-4">
      <ul class="text-sm lg:text-base font-bold leading-loose">
        <li class="text-2xl font-extrabold mb-4">Order details:</li>
        <li>Arrival date: <?= $_SESSION["reservation"]["arrival_date"]; ?></li>
        <li>Departure date: <?= $_SESSION["reservation"]["departure_date"]; ?></li>
        <li class="mb-4">Number of days: <?= $_SESSION["numberOfDays"]; ?></li>
        <li>Type of room: <span class="capitalize"><?php echo $roomChosen === 3 ? "Deluxe" : ($roomChosen === 2 ? "Standard" : "Economy") ?></span></li>
        <li class="mb-4">Price per day: $<span id="price-per-day"><?php echo $_SESSION["pricePerDay"] ?></span></li>

        <li class="font-extrabold">Extra features:</li>
        <?php foreach ($_SESSION["reservation"]["features"] as $feature) : ?>
          <li><?= $feature["name"] ?>: $<?= $feature["cost"] ?></li>
        <?php endforeach; ?>

      </ul>
      <div class="font-extrabold">Complete your reservation in <span id="count-down">5:00</span></div>
    </div>

    <div class="flex flex-col justify-between h-full mx-auto lg:ml-auto lg:mr-0 gap-16">
      <div class="text-center font-bold w-full text-2xl bg-cyan-950 text-cyan-50 p-2">
        Your total: $<span class="text-cyan-50" id="total-price"><?= $_SESSION["reservation"]["total_cost"]; ?></span>
      </div>
      <!-- The form is submitted in confirm.js if #button-submit-confirm is clicked -->
      <form id="form-confirm" action="php/payment.php" method="post" class="flex flex-col text-base font-bold leading-loose">
        <label for="guest-name">Name of guest:</label>
        <input type="text" name="guest-name" placeholder="Enter your name" class="mb-4 w-full border-0 ring-1 ring-inset ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-cyan-950 placeholder:text-gray-300">
        <label for="transfer-code">Transfer code of $<?= $_SESSION["reservation"]["total_cost"]; ?>:</label>
        <input type="text" name="transfer-code" placeholder="Enter your transfer code" class="w-full border-0 ring-1 ring-inset ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-cyan-950 placeholder:text-gray-300">
      </form>
      <div class="flex flex-col justify-center mx-auto lg:ml-auto lg:mr-0 gap-4">
        <button id="button-submit-confirm" class="button-green">Confirm and pay</button>
        <button id="button-cancel-booking" class="button-red">Cancel booking</button>
      </div>
    </div>

  </div>
</div>


<!-- The hidden form is submitted in cancelBooking.js if the timer runs out,
or if #button-cancel-booking is pressed -->
<form action="index.php" method="post" id="form-cancel-booking" hidden>
  <input name="pageState" type="text" value="home" hidden>
</form>

<script src="/js/confirm.js"></script>
<script src="/js/cancelBooking.js"></script>
