<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

if (isset($_POST["newPrice"], $_POST["roomId"])) {
  $newPrice = htmlspecialchars($_POST["newPrice"]);
  $roomId = htmlspecialchars($_POST["roomId"]);

  $statementUpdateRoomPrice = $db->prepare(
    "UPDATE rooms
    SET base_price = :newPrice
    WHERE id = :roomId"
  );

  $statementUpdateRoomPrice->bindParam(":newPrice", $newPrice, PDO::PARAM_INT);
  $statementUpdateRoomPrice->bindParam(":roomId", $roomId, PDO::PARAM_INT);
  $statementUpdateRoomPrice->execute();
}

header("Location: /admin.php");
