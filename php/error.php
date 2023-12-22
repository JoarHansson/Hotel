<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

?>

<h1>error:</h1>
<p><?php echo $_SESSION["message"]; ?></p>

<?php
unset($_SESSION["message"]);
unset($_SESSION["reservation"]);
?>