<?php

declare(strict_types=1);

$db = new PDO("sqlite:" . __DIR__ . "/hotel.db");


/*

This file has been used to setup and reset the database during development.

This file won't be included when the site is deployed.

The queries used to create the tables within hotel.db are pasted below.
(this was done in DBdesigner and TablePlus)


--create tables
CREATE TABLE IF NOT EXISTS rooms (
	id INTEGER PRIMARY KEY,
	name VARCHAR,
	base_price INTEGER
);

CREATE TABLE IF NOT EXISTS occupancy (
	id INTEGER PRIMARY KEY,
	room_id INTEGER,
	date INTEGER,
	occupied BOOLEAN,
	FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE IF NOT EXISTS bookings (
	id INTEGER PRIMARY KEY,
	guest_name VARCHAR,
	checkin_date INTEGER,
	checkout_date INTEGER,
	room_id INTEGER,
	FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE IF NOT EXISTS extras (
	id INTEGER PRIMARY KEY,
	name VARCHAR,
	price INTEGER
);

CREATE TABLE bookings_extras (
	id INTEGER,
	bookings_id INTEGER,
	extras_id INTEGER
	FOREIGN KEY (bookings_id) REFERENCES bookings(id)
	FOREIGN KEY (extras_id) REFERENCES extras(id)
);

*/

// delete everything in all tables:
$tables = ["rooms", "occupancy", "bookings", "extras", "bookings_extras"];

foreach ($tables as $table) {
  $statementDeleteFromTables = $db->prepare("DELETE FROM {$table}");
  $statementDeleteFromTables->execute();
}

// Insert into rooms
$statementInsertIntoRooms = $db->prepare(
  "INSERT INTO rooms (name, base_price)
  VALUES ('economy', 5), ('standard', 7), ('luxury', 10)"
);
$statementInsertIntoRooms->execute();


// Insert into occupancy
for ($roomType = 1; $roomType < 4; $roomType++) { // 3 room types

  for ($date = 1; $date < 32; $date++) { // 31 days
    $statementInsertIntoOccupancy = $db->prepare(
      "INSERT INTO occupancy (room_id, date, occupied)
      VALUES (:roomType, :date, false)"
    );

    $statementInsertIntoOccupancy->bindParam(":date", $date, PDO::PARAM_INT);
    $statementInsertIntoOccupancy->bindParam(":roomType", $roomType, PDO::PARAM_INT);
    $statementInsertIntoOccupancy->execute();
  }
}


// Insert into extras
$extras = [
  "stuff",
  "things",
  "extra stuff",
  "more things",
  "fun expensive thing",
  "extra everything package",
  "even more things"
];

foreach ($extras as $item) {
  $statementInsertIntoExtras = $db->prepare(
    "INSERT INTO extras (name, price)
    VALUES (:item, :price)"
  );

  $price = rand(1, 3); // might change later..

  $statementInsertIntoExtras->bindParam(":item", $item, PDO::PARAM_STR);
  $statementInsertIntoExtras->bindParam(":price", $price, PDO::PARAM_INT);
  $statementInsertIntoExtras->execute();
}
