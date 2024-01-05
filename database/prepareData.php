<?php

declare(strict_types=1);

$db = new PDO("sqlite:" . __DIR__ . "/hotel.db");


/*

This file has been used to setup and reset the database during development.

This file won't be included when the site is deployed.

*/

// $statementsDropTable = $db->prepare("DROP TABLE occupancy");
// $statementsDropTable->execute();

// create all tables
$statementsCreateTables = [
  "CREATE TABLE IF NOT EXISTS rooms (
      id INTEGER PRIMARY KEY,
      name VARCHAR,
      base_price INTEGER
  )",
  "CREATE TABLE IF NOT EXISTS reservations (
    id INTEGER PRIMARY KEY,
    checkin_date INTEGER,
    checkout_date INTEGER,
    timestamp INTEGER,
    room_id INTEGER,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
  )",
  "CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY,
    guest_name VARCHAR,
    checkin_date INTEGER,
    checkout_date INTEGER,
    room_id INTEGER,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
  )",
  "CREATE TABLE IF NOT EXISTS extras (
    id INTEGER PRIMARY KEY,
    name VARCHAR,
    price INTEGER
  )",
  "CREATE TABLE IF NOT EXISTS bookings_extras (
    id INTEGER,
    bookings_id INTEGER,
    extras_id INTEGER,
    FOREIGN KEY (bookings_id) REFERENCES bookings(id),
    FOREIGN KEY (extras_id) REFERENCES extras(id)
  )"
];

foreach ($statementsCreateTables as $statementCreateTable) {
  $statement = $db->prepare($statementCreateTable);
  $statement->execute();
}

// delete everything in all tables:
$tables = ["rooms", "reservations", "bookings", "extras", "bookings_extras"];

foreach ($tables as $table) {
  $statementDeleteFromTables = $db->prepare("DELETE FROM {$table}");
  $statementDeleteFromTables->execute();
}

// Insert into rooms
$statementInsertIntoRooms = $db->prepare(
  "INSERT INTO rooms (name, base_price)
  VALUES ('economy', 3), ('standard', 5), ('deluxe', 7)"
);
$statementInsertIntoRooms->execute();

// Insert into extras
$extras = [
  "Sushi buffet",
  "Extra blanket",
  "Polar bear safari",
  "Penguin BBQ buffet",
  "Whisky on the rocks",
  "Ice sculpting course",
  "Ice fishing excursion",
  "Northern lights package",
  "Siberian husky companion",
  "Snowmobile (gas included)"
];

foreach ($extras as $item) {
  $statementInsertIntoExtras = $db->prepare(
    "INSERT INTO extras (name, price)
    VALUES (:item, :price)"
  );

  $price = rand(1, 2); // might change later..

  $statementInsertIntoExtras->bindParam(":item", $item, PDO::PARAM_STR);
  $statementInsertIntoExtras->bindParam(":price", $price, PDO::PARAM_INT);
  $statementInsertIntoExtras->execute();
}
