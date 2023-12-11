<?php

declare(strict_types=1);

session_start();

require __DIR__ . "/php/header.php";

require __DIR__ . "/php/calender.php";

// display message on whether the booking was successful or not:
if (isset($_SESSION["message"])) {
  echo $_SESSION["message"];
  unset($_SESSION["message"]);
}

?>

</body>

</html>