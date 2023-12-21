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

?>

<form action="php/payment.php" method="post" class="flex flex-col bg-slate-900 p-4 w-96">
  <p class="mb-4">To complete the booking, enter your name and valid transfer code of $ ___ below</p>
  <label for="guest-name">Name:</label>
  <input type="text" name="guest-name" class="text-black">
  <label for="transfer-code">Transfer Code:</label>
  <input type="text" name="transfer-code" class="text-black mb-4">
  <button type="submit" class="bg-slate-400 text-black py-2">Confirm</button>
</form>