<?php

declare(strict_types=1);

session_start();

require __DIR__ . "/php/header.php";

require __DIR__ . "/php/calender.php";

// display message on whether the booking was successful or not:
if (isset($_SESSION["message"])) : ?>

  <div class="mb-4">
    <?= $_SESSION["message"]; ?>
  </div>

<?php endif;

if ($_SESSION["message"] == "reservation succeeded, now you shall pay") {
  require __DIR__ . "/php/confirm.php";
}

unset($_SESSION["message"]);

?>

</body>

</html>