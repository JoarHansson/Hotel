<?php

declare(strict_types=1);

require __DIR__ . "/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

$client = new Client();

$dateTodayTimeNow = date("Y-m-d\TH:i");
$dateToday = date("Y-m-d");
$TimeNow = date("H:i");

$baseUri = "https://api.open-meteo.com/v1/metno?latitude=69.73&longitude=29.99&start_hour={$dateTodayTimeNow}&end_hour={$dateTodayTimeNow}";

// check current temperature
try {
  $responseCheckTemperature = $client->request("GET", "{$baseUri}&hourly=temperature_2m");
} catch (ClientException $e) {
  echo $e->getMessage();
}

$temperatureData = json_decode($responseCheckTemperature->getBody()->getContents());

$temperature = $temperatureData->hourly->temperature_2m[0] . " " . $temperatureData->hourly_units->temperature_2m;

// check current precipitation
try {
  $responseCheckPrecipitation = $client->request("GET", "{$baseUri}&hourly=precipitation");
} catch (ClientException $e) {
  echo $e->getMessage();
}

$precipitationData = json_decode($responseCheckPrecipitation->getBody()->getContents());

$precipitation = $precipitationData->hourly->precipitation[0] . " " . $precipitationData->hourly_units->precipitation;

// check current wind speed
try {
  $responseCheckWindSpeed = $client->request("GET", "{$baseUri}&hourly=wind_speed_10m");
} catch (ClientException $e) {
  echo $e->getMessage();
}

$windSpeedData = json_decode($responseCheckWindSpeed->getBody()->getContents());

$windSpeed = $windSpeedData->hourly->wind_speed_10m[0] . " " . $windSpeedData->hourly_units->wind_speed_10m;

// add all weather data to the receipt
$_SESSION["reservation"]["additional_info"]["date_for_measurement"] = $dateToday;
$_SESSION["reservation"]["additional_info"]["time_for_measurement"] = $TimeNow;
$_SESSION["reservation"]["additional_info"]["temperature"] = $temperature;
$_SESSION["reservation"]["additional_info"]["precipitation"] = $precipitation;
$_SESSION["reservation"]["additional_info"]["wind_speed"] = $windSpeed;
$_SESSION["reservation"]["additional_info"]["weather_api"] = "https://open-meteo.com/en/docs/metno-api/";

// remove id of features for the receipt
for ($i = 0; $i < count($_SESSION["reservation"]["features"]); $i++) {
  unset($_SESSION["reservation"]["features"][$i]["id"]);
}
?>

<h1 class="mb-8 mt-32 lg:mt-64 text-center text-5xl font-extrabold font-sans italic leading-relaxed text-cyan-950 underline underline-offset-8 decoration-8">Thank you for choosing The Ice Hotel</h1>
<div id="content-container" class="bg-cyan-50 p-4 lg:p-8 mx-auto max-w-md lg:max-w-3xl rounded-3xl shadow-cyan-50/25 shadow-xl flex flex-col lg:flex-row gap-8 justify-between items-center">

  <ul class="text-sm lg:text-base font-bold leading-loose">
    <li class="text-xl font-extrabold mb-2 text-center">The current weather at Mount Frost:</li>
    <li>Temperature: <?php echo $temperature ?></li>
    <li>Precipitation: <?php echo $precipitation ?></li>
    <li>Wind speed: <?php echo $windSpeed ?></li>
  </ul>

  <form action="php/receipt.php" method="get" target="_blank" class=" text-center">
    <button type="submit" class="button-cyan">Get receipt</button>
  </form>

</div>
