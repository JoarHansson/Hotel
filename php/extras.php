<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

// get all extra items from db
$statementGetExtras = $db->prepare("SELECT * FROM extras");
$statementGetExtras->execute();

$extras = $statementGetExtras->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="mb-4">Choose extras</h1>

<form action="index.php" method="post">
  <ul>
    <?php foreach ($extras as $key => $extraItem) : ?>
      <li>
        <input type="checkbox" name="extra-<?php echo $key ?>" value="<?php echo $extraItem["name"] ?>">
        <label for="extra-<?php echo $key ?>"><?php echo $extraItem["name"] . ": $" . $extraItem["price"] ?></label>
      </li>
    <?php endforeach; ?>
  </ul>

  <input name="pageState" type="text" value="confirm" hidden>
  <button type="submit" class="bg-emerald-500 px-4 py-2  hover:bg-emerald-400">OK</button>
</form>