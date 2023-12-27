<?php

declare(strict_types=1);

// require __DIR__ . "/autoload.php";

// getDataFromDb returns all reserved or booked dates in a separate array.
// $table works with arguments "bookings" or "reservations".
// $roomId works with 1, 2 or 3.
function getDataFromDb(string $table, int $roomId): array
{
  $db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");

  $statement = $db->prepare(
    "SELECT * FROM $table
    WHERE room_id = :roomId"
  );

  $statement->bindParam(":roomId", $roomId, PDO::PARAM_INT);
  $statement->execute();
  $dataRows = $statement->fetchAll(PDO::FETCH_ASSOC);

  // return all data rows (bookings/reservations)
  return $dataRows;
}

// filterDatesFromData filters out dates and puts them in a one dimensional array.
// Argument passed as $dataRows must be in the format that getDataFromDb returns.
function filterDatesFromData(array $dataRows): array
{
  // get all booked dates in a separate array
  $dates = array_map(function ($dataRow) {
    return range($dataRow["checkin_date"], $dataRow["checkout_date"]);
  }, $dataRows);

  // returned flattened array $dates:
  return array_merge(...$dates);
}
