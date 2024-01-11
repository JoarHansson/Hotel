<?php

declare(strict_types=1);

require __DIR__ . "/php/autoload.php";
require __DIR__ . "/php/header.php";

// the page state logic is used by index.php to know which file to require
unset($_SESSION["pageState"]);
unset($_SESSION["loggedIn"]);

?>

<div class="container mx-auto pb-16 px-4 sm:px-6 lg:px-8">

  <form id="form-login" action="admin.php" method="post" class="flex flex-col text-base font-bold leading-loose bg-cyan-50 px-4 py-8 lg:p-8 mx-auto max-w-md rounded-3xl shadow-cyan-50/25 shadow-xl">
    <label for="admin-name">Admin name:</label>
    <input type="text" name="admin-name" class="mb-4 w-full border-0 ring-1 ring-inset ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-cyan-950 placeholder:text-gray-300">
    <label for="admin-password">Admin password:</label>
    <input type="password" name="admin-password" class="mb-8 w-full border-0 ring-1 ring-inset ring-gray-600 focus:ring-2 focus:ring-inset focus:ring-cyan-950 placeholder:text-gray-300">
    <button type="submit" class="button-cyan">Login</button>
    <?php if (isset($_SESSION["loginMessage"])) : ?>
      <div class="mt-8 text-center"><?php echo $_SESSION["loginMessage"]; ?></div>
      <?php unset($_SESSION["loginMessage"]); ?>
    <?php endif; ?>
  </form>

</div>
</body>

</html>
