<?php

declare(strict_types=1);

require __DIR__ . "/php/autoload.php";
require __DIR__ . "/php/header.php";
require(__DIR__ . '/vendor/autoload.php');

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (isset($_POST["admin-name"], $_POST["admin-password"])) {

  if ($_POST["admin-password"] === $_ENV["API_KEY"]) {
    $_SESSION["loggedIn"] = true;
  } else {
    $_SESSION["loginMessage"] = "Incorrect name or password";
  }
}

if (empty($_SESSION["loggedIn"])) {
  header("Location: /login.php");
}

?>

<div class="container mx-auto pb-16 px-4 sm:px-6 lg:px-8">

  <form id="form-logout" action="/login.php" method="post" class="flex flex-col text-base font-bold leading-loose bg-cyan-50 px-4 py-8 lg:p-8 mx-auto max-w-md rounded-3xl shadow-cyan-50/25 shadow-xl">
    <button type="submit" class="button-cyan">Logout</button>
  </form>

  <?php

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
