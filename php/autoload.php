<?php

declare(strict_types=1);

session_start();

$db = new PDO("sqlite:" . __DIR__ . "/../database/hotel.db");
