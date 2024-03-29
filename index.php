<?php

declare(strict_types=1);

require __DIR__ . "/php/autoload.php";
require __DIR__ . "/php/header.php";

// check the page state in order to require the correct php file
if (isset($_POST["pageState"])) {
  if ($_POST["pageState"] === "home") {
    $_SESSION["pageState"] = "home";
  } else if ($_POST["pageState"] === "calender") {
    $_SESSION["pageState"] = "calender";
  } else if ($_POST["pageState"] === "confirm") {
    $_SESSION["pageState"] = "confirm";
  }
}

?>

<div class="container mx-auto pb-16 px-4 sm:px-6 lg:px-8">

  <?php

  if ($_SESSION["pageState"] === "home") {
    require __DIR__ . "/php/home.php";
  } else if ($_SESSION["pageState"] === "calender") {
    require __DIR__ . "/php/calender.php";
  } else if ($_SESSION["pageState"] === "extras") {
    require __DIR__ . "/php/extras.php";
  } else if ($_SESSION["pageState"] === "confirm") {
    require __DIR__ . "/php/confirm.php";
  } else if ($_SESSION["pageState"] === "success") {
    require __DIR__ . "/php/success.php";
  } else if ($_SESSION["pageState"] === "error") {
    require __DIR__ . "/php/error.php";
  } else {
    require __DIR__ . "/php/home.php";
  }

  // echo "<pre class='font-bold text-xl mx-auto bg-cyan-50 p-8 mt-8'>";
  // echo "session:\n";
  // print_r($_SESSION);
  // echo "post:\n";
  // print_r($_POST);
  // echo "</pre>";

  ?>

</div>
</body>

</html>
