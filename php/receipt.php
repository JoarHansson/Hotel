<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";

header("Content-Type: application/json");

echo json_encode($_SESSION["reservation"]);
