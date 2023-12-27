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

$_SESSION["reservation"]["total_cost"] = ($_SESSION["numberOfDays"] * $_SESSION["pricePerDay"]) + $costChosenFeatures;

?>

<p>your order details:</p>
<p>arrival date: <?= $_SESSION["reservation"]["arrival_date"]; ?></p>
<p>departure date: <?= $_SESSION["reservation"]["departure_date"]; ?></p>
<br>
<p><?= $_SESSION["numberOfDays"]; ?> days á €<?= $_SESSION["pricePerDay"]; ?></p>
<br>
<p>extras:</p>
<?php foreach ($_SESSION["reservation"]["features"] as $feature) : ?>
  <p><?= $feature["name"] ?>: $<?= $feature["cost"] ?></p>
<?php endforeach; ?>
<br>
<p class="mb-4">
  total price:
  <span id="total-price">
    <?= $_SESSION["reservation"]["total_cost"]; ?>
  </span>
</p>

<form action="php/payment.php" method="post" class="flex flex-col bg-slate-900 p-4 w-96">
  <p class="mb-4">To complete the booking, enter your name and valid transfer code of $<?= $_SESSION["reservation"]["total_cost"]; ?> below</p>
  <label for="guest-name">Name:</label>
  <input type="text" name="guest-name" class="text-black">
  <label for="transfer-code">Transfer Code:</label>
  <input type="text" name="transfer-code" class="text-black mb-4">
  <button type="submit" class="bg-slate-400 text-black py-2">Confirm</button>
</form>

<form action="index.php" method="post">
  <input name="pageState" type="text" value="home" hidden>
  <button type="submit" class="bg-rose-500 px-4 py-2  hover:bg-rose-400">Cancel</button>
</form>