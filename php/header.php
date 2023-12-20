<!DOCTYPE html>
<html lang="en" class="h-screen">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="/public/style.css">
</head>

<body class="p-4 h-full bg-slate-800 flex flex-col items-center">

  <header class="w-full">
    <form action="index.php" method="post" class="flex justify-between">
      <button type="submit" class="button-green" name="pageState" value="home">home</button>
      <button type="submit" class="button-green" name="pageState" value="calender">calender</button>
    </form>
  </header>