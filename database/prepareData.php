<?php

declare(strict_types=1);

$db = new PDO('sqlite:hotel.db');


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

CREATE TABLE IF NOT EXISTS extras (
	id INTEGER PRIMARY KEY,
	name VARCHAR,
	price INTEGER,
	room_id INTEGER,
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

*/

// delete everything in all tables:
$statementDeleteFromRooms = $db->prepare("DELETE FROM rooms");
$statementDeleteFromRooms->execute();
$statementDeleteFromOccupancy = $db->prepare("DELETE FROM occupancy");
$statementDeleteFromOccupancy->execute();
$statementDeleteFromExtras = $db->prepare("DELETE FROM extras");
$statementDeleteFromExtras->execute();
$statementDeleteFromBookings = $db->prepare("DELETE FROM bookings");
$statementDeleteFromBookings->execute();

// Insert into rooms
$statementInsertIntoRooms = $db->prepare(
  "INSERT INTO rooms (name, base_price)
  VALUES ('economy', 5), ('standard', 7), ('luxury', 10)"
);
$statementInsertIntoRooms->execute();


// Insert into occupancy
for ($roomType = 1; $roomType < 4; $roomType++) {

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
