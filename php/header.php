<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&family=Lilita+One&family=Lora:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/public/style.css">
</head>

<body class="relative bg-cyan-50 min-h-screen">
  <div class="absolute top-0 bottom-0 left-0 right-0 bg-[url('/assets/ice.jpg')] bg-center bg-fixed bg-auto opacity-50 -z-10"></div>

  <header class="w-full bg-cyan-300 p-4 mb-32 ">
    <form action="index.php" method="post" class="flex justify-between items-center">
      <button type="submit" class="text-4xl font-display italic uppercase text-cyan-950" name="pageState" value="home">The Ice Hotel</button>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
        <path d="M3 4H21V6H3V4ZM3 11H21V13H3V11ZM3 18H21V20H3V18Z" fill="currentColor" class="stroke-cyan-950 fill-cyan-950"></path>
      </svg>
    </form>
  </header>
