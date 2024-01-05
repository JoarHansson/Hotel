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

<h1 class="mb-16 text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950 underline underline-offset-8 decoration-8">Choose your extras</h1>

<div id="choose-extras-container" class="bg-cyan-50 px-4 py-8 lg:p-8 mx-auto grid max-w-md lg:max-w-3xl rounded-3xl shadow-cyan-50/25 shadow-xl">
  <!-- <h2 id="instruction-text" class="mb-8 text-center text-2xl font-extrabold leading-relaxed">Choose your extras</h2> -->
  <div class="flex flex-col lg:flex-row gap-8 justify-between items-end">
    <!-- The form is submitted in extras.js if #button-submit-extras is clicked -->
    <form id="form-extras" class="mx-auto lg:mr-auto lg:ml-0" action="index.php" method="post">
      <ul class="text-sm lg:text-base font-bold leading-loose">

        <?php foreach ($extras as $key => $extraItem) : ?>
          <?php if ($extraItem["price"] === 0) : ?>
            <li>
              <input checked type="checkbox" name="extra-<?php echo $key ?>" value="<?php echo $extraItem["name"] . "_$" . $extraItem["price"] ?>" class="extra-items rounded border-gray-300 text-cyan-950 focus:ring-cyan-950">
              <label for="extra-<?php echo $key ?>" class=" pl-1 "><?php echo $extraItem["name"] . ": $" . $extraItem["price"] ?></label>
            </li>
          <?php else : ?>
            <li>
              <input type="checkbox" name="extra-<?php echo $key ?>" value="<?php echo $extraItem["name"] . "_$" . $extraItem["price"] ?>" class="extra-items rounded border-gray-300 text-cyan-950 focus:ring-cyan-950">
              <label for="extra-<?php echo $key ?>" class=" pl-1 "><?php echo $extraItem["name"] . ": $" . $extraItem["price"] ?></label>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>

        <li class="h-1 w-full bg-cyan-950 my-4"></li> <!-- separator -->

        <li>Chosen room: <span class="capitalize"><?php echo $roomChosen === 3 ? "Deluxe" : ($roomChosen === 2 ? "Standard" : "Economy") ?></span></li>
        <li>Price per day: $<span id="price-per-day"><?php echo $_SESSION["pricePerDay"] ?></span></li>
        <li>Complete your reservation in <span id="count-down">5:00</span></li>
      </ul>
      <input name="pageState" type="text" value="confirm" hidden>
    </form>

    <div class="flex flex-col justify-between h-full mx-auto lg:ml-auto lg:mr-0 gap-8">
      <div class="text-center font-bold w-full text-2xl bg-cyan-950 text-cyan-50 p-2">
        Your total: $<span class="text-cyan-50" id="total-price"><?= $_SESSION["pricePerDay"] * $_SESSION["numberOfDays"]; ?></span>
      </div>
      <div class="flex flex-col justify-center mx-auto lg:ml-auto lg:mr-0 gap-4">
        <button id="button-submit-extras" class="button-green">Continue</button>
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

<script src="/js/extras.js"></script>
<script src="/js/cancelBooking.js"></script>
