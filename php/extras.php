<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

$roomChosen = $_SESSION["roomType"];

// get all extra items from db
$statementGetExtras = $db->prepare("SELECT * FROM extras");
$statementGetExtras->execute();

$extras = $statementGetExtras->fetchAll(PDO::FETCH_ASSOC);

if ($roomChosen === 3) {
  // deluxe room: 5 features free
  for ($i = 0; $i < 5; $i++) {
    $extras[$i]["price"] = 0;
  }
} else if ($roomChosen === 2) {
  // standard room: 3 features free
  for ($i = 0; $i < 3; $i++) {
    $extras[$i]["price"] = 0;
  }
}

?>

<p class="mb-4">Choose extras</p>

<p class="mb-4">
  total price:
  <span id="total-price">
    <?= $_SESSION["pricePerDay"] * $_SESSION["numberOfDays"]; ?>
  </span>
</p>

<form action="index.php" method="post">
  <ul>
    <?php foreach ($extras as $key => $extraItem) : ?>
      <?php if ($extraItem["price"] === 0) : ?>
        <li>
          <input checked type="checkbox" name="extra-<?php echo $key ?>" value="<?php echo $extraItem["name"] . "_$" . $extraItem["price"] ?>" class="extra-items">
          <label for="extra-<?php echo $key ?>"><?php echo $extraItem["name"] . ": $" . $extraItem["price"] ?></label>
        </li>
      <?php else : ?>
        <li>
          <input type="checkbox" name="extra-<?php echo $key ?>" value="<?php echo $extraItem["name"] . "_$" . $extraItem["price"] ?>" class="extra-items">
          <label for="extra-<?php echo $key ?>"><?php echo $extraItem["name"] . ": $" . $extraItem["price"] ?></label>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>

  <input name="pageState" type="text" value="confirm" hidden>
  <button type="submit" class="bg-emerald-500 px-4 py-2  hover:bg-emerald-400">Continue</button>
</form>

<form action="index.php" method="post" id="form-cancel-booking" class="m-4">
  <input name="pageState" type="text" value="home" hidden>
  <button type="submit" class="bg-rose-500 px-4 py-2  hover:bg-rose-400">Cancel</button>
</form>

<p>complete your reservation in 5 minutes.</p>
<p id="count-down">5:00</p>

<script src="/js/extras.js"></script>
<script src="/js/countdown.js"></script>
