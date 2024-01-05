<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

?>
<div id="choose-extras-container" class="bg-cyan-50 px-4 py-8 lg:p-8 mx-auto max-w-md lg:max-w-3xl rounded-3xl shadow-cyan-50/25 shadow-xl">

  <h3 class="text-xl text-center font-extrabold leading-8 mb-8">ERROR: <?php echo $_SESSION["message"]; ?></h3>

  <form action="index.php" method="post" class="w-full text-center">
    <input name="pageState" type="text" value="home" hidden>
    <button type="submit" class="button-cyan">Back to homepage</button>
  </form>

</div>

<?php
unset($_SESSION["message"]);
unset($_SESSION["reservation"]);
?>
